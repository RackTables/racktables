<?php

# This file is a part of RackTables, a datacenter and server room management
# framework. See accompanying file "COPYING" for the full copyright and
# licensing information.

ob_start();
require_once 'inc/pre-init.php';
try {
	// Switch block below is a module request dispatcher.
	// Dispatches based on module request.
	// The last string 'interface' is the default.
	$requestedModule = array_key_exists ('module', $_REQUEST) ? $_REQUEST['module'] : 'interface';

	switch ($requestedModule)
	{
	case 'interface':
		require_once 'inc/interface.php';
		// init.php has to be included after interface.php, otherwise the bits
		// set by local.php get lost
		require_once 'inc/init.php';
		prepareNavigation();
		requireExtraFiles ($interface_requires);
		// Security context is built on the requested page/tab/bypass data,
		// do not override.
		fixContext();
		redirectIfNecessary();
		assertPermission();
		header ('Content-Type: text/html; charset=UTF-8');
		// call the main handler - page or tab handler.
		if (isset ($tabhandler[$pageno][$tabno]))
		{
			if (! is_callable ($tabhandler[$pageno][$tabno]))
				throw new RackTablesError ("Missing handler function for node '${pageno}-${tabno}'", RackTablesError::INTERNAL);
			call_user_func ($tabhandler[$pageno][$tabno], getBypassValue());
		}
		elseif (isset ($page[$pageno]['handler']))
		{
			if (! is_callable ($page[$pageno]['handler']))
				throw new RackTablesError ("Missing handler function for node '${pageno}'", RackTablesError::INTERNAL);
			$page[$pageno]['handler'] ($tabno);
		}
		else
			throw new RackTablesError ("Failed to find handler for page '${pageno}', tab '${tabno}'", RackTablesError::INTERNAL);
		// Embed the current text in OB into interface layout (the latter also
		// empties color message buffer).
		$contents = ob_get_contents();
		ob_clean();
		renderInterfaceHTML ($pageno, $tabno, $contents);
		break;

	case 'chrome':
		require_once 'inc/init.php';
		require_once 'inc/solutions.php';
		proxyStaticURI (genericAssertion ('uri', 'string'));
		break;

	case 'download':
		require_once 'inc/init.php';
		$pageno = 'file';
		$tabno = 'download';
		fixContext();
		assertPermission();
		$file = getFile (getBypassValue());
		header("Content-Type: {$file['type']}");
		header("Content-Length: {$file['size']}");
		if (! array_key_exists ('asattach', $_REQUEST) || $_REQUEST['asattach'] != 'no')
			header("Content-Disposition: attachment; filename={$file['name']}");
		echo $file['contents'];
		break;

	case 'image':
		# The difference between "image" and "download" ways to serve the same
		# picture file is that the former is used in <IMG SRC=...> construct,
		# and the latter is accessed as a standalone URL and can reply with any
		# Content-Type. Hence "image" module indicates failures with internally
		# built images, and "download" can return a full-fledged "permission
		# denied" or "exception" HTML page instead of the file requested.
		require_once 'inc/init.php'; // for authentication check
		require_once 'inc/solutions.php';
		try
		{
			dispatchImageRequest();
		}
		catch (Exception $e)
		{
			ob_clean();
			throw ($e instanceof RTImageError) ? $e : new RTImageError;
		}
		break;

	case 'svg':
		require_once 'inc/init.php';
		require_once 'inc/solutions.php';
		header ('Content-Type: image/svg+xml');
		echo '<?xml version="1.0" encoding="iso-8859-1" standalone="no"?>' . "\n";
		echo '<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">' . "\n";
		try
		{
			$view = genericAssertion ('view', 'string');
			if (! array_key_exists ($view, $svghandler))
				throw new InvalidRequestArgException ('view', $view, 'undefined view');
			if (! is_callable ($svghandler[$view]))
				throw new RackTablesError ('missing handler function', RackTablesError::INTERNAL);
			call_user_func ($svghandler[$view]);
		}
		catch (RTPermissionDenied $e)
		{
			ob_clean();
			printSVGMessageBar ('permission denied', array ('fill' => 'white'), array ('fill' => 'black', 'stroke' => 'gray'));
		}
		catch (InvalidRequestArgException $e)
		{
			ob_clean();
			printSVGMessageBar ('malformed HTTP request', array(), array ('fill' => 'yellow', 'stroke' => 'black'));
		}
		catch (EntityNotFoundException $e)
		{
			ob_clean();
			printSVGMessageBar ('no such record', array(), array ('fill' => 'yellow', 'stroke' => 'black'));
		}
		catch (RackTablesError $e)
		{
			ob_clean();
			printSVGMessageBar ('RT error: ' . $e->getMessage(), array(), array ('fill' => 'red', 'stroke' => 'black'));
		}
		catch (Exception $e)
		{
			ob_clean();
			printSVGMessageBar ('unknown error', array(), array ('fill' => 'red', 'stroke' => 'black'));
		}
		break;

	case 'progressbar':
		# Unlike images (and like static content), progress bars are processed
		# without a permission check, but only for authenticated users.
		require_once 'inc/init.php';
		require_once 'inc/solutions.php';
		try
		{
			// 'progressbar's never change, make browser cache the result
			if (checkCachedResponse (0, CACHE_DURATION))
				break;
			renderProgressBarImage (genericAssertion ('done', 'uint0'));
		}
		catch (Exception $e)
		{
			ob_clean();
			throw ($e instanceof RTImageError) ? $e : new RTImageError ('pbar_error');
		}
		break;

	case 'progressbar4':
		# Unlike images (and like static content), progress bars are processed
		# without a permission check, but only for authenticated users.
		require_once 'inc/init.php';
		require_once 'inc/solutions.php';
		try
		{
			renderProgressBar4Image
			(
				genericAssertion ('px1', 'uint0'),
				genericAssertion ('px2', 'uint0'),
				genericAssertion ('px3', 'uint0')
			);
		}
		catch (Exception $e)
		{
			ob_clean();
			throw ($e instanceof RTImageError) ? $e : new RTImageError ('pbar_error');
		}
		break;

	case 'ajax':
		require_once 'inc/init.php';
		require_once 'inc/ajax-interface.php';
		require_once 'inc/solutions.php';
		try
		{
			$ac = genericAssertion ('ac', 'string');
			if (isset ($ajaxhandler[$ac]))
				$ajaxhandler[$ac]();
			else
			{
				ob_clean();
				echo "NAK\nMalformed request";
			}
		}
		catch (RTPermissionDenied $e)
		{
			ob_clean();
			# FIXME: the remote client could be expecting JSON data instead
			echo "NAK\nPermission denied";
		}
		catch (Exception $e)
		{
			ob_clean();
			echo "NAK\nRuntime exception: ". $e->getMessage();
		}
		break;

	case 'redirect':
		// Include init after ophandlers/snmp, not before, so local.php can redefine things.
		require_once 'inc/ophandlers.php';
		// snmp.php is an exception, it is treated by a special hack
		if (isset ($_REQUEST['op']) && $_REQUEST['op'] == 'querySNMPData')
			require_once 'inc/snmp.php';
		require_once 'inc/init.php';
		try
		{
			$op = genericAssertion ('op', 'string');
			prepareNavigation();
			$location = buildRedirectURL();
			// FIXME: find a better way to handle this error
			if ($op == 'addFile' && !isset($_FILES['file']['error']))
				throw new RackTablesError ('File upload error, check upload_max_filesize in php.ini', RackTablesError::MISCONFIGURED);
			fixContext();
			if
			(
				! isset ($ophandler[$pageno][$tabno][$op]) ||
				! is_callable ($ophandler[$pageno][$tabno][$op])
			)
				throw new RackTablesError ("Invalid navigation data for '${pageno}-${tabno}-${op}'", RackTablesError::INTERNAL);
			// We have a chance to handle an error before starting HTTP header.
			if (!isset ($delayauth["${pageno}-${tabno}-${op}"]))
				assertPermission();
			# Call below does the job of bypass argument assertion, if such is required,
			# so the ophandler function doesn't have to re-assert this portion of its
			# arguments. And it would be even better to pass returned value to ophandler,
			# so it is not necessary to remember the name of bypass in it.
			getBypassValue();
			if ('' != $redirect_to = call_user_func ($ophandler[$pageno][$tabno][$op]))
				$location = $redirect_to;
		}
		// known "soft" failures require a short error message
		catch (InvalidRequestArgException $e)
		{
			ob_clean();
			showError ($e->getMessage());
		}
		catch (RTDatabaseError $e)
		{
			ob_clean();
			showError ('Database error: ' . $e->getMessage());
		}
		catch (RTPermissionDenied $e)
		{
			ob_clean();
			showError ('Operation not permitted');
		}
		catch (Exception $e)
		{
			ob_clean();
			printException ($e);
			break;
		}
		redirectUser ($location);
		// any other error requires no special handling and will be caught outside
		break;

	case 'popup':
		require_once 'inc/popup.php';
		require_once 'inc/init.php';
		prepareNavigation();
		fixContext();
		assertPermission();
		$helper = assertStringArg ('helper');

		header ('Content-Type: text/html; charset=UTF-8');
		// call the main handler - page or tab handler.
		if (isset ($popuphandler[$helper]) && is_callable ($popuphandler[$helper]))
			call_user_func ($popuphandler[$helper], $helper);
		else
			throw new RackTablesError ("Missing handler function for node '${handler}'", RackTablesError::INTERNAL);
		$contents = ob_get_contents();
		ob_clean();
		renderPopupHTML ($contents);
		break;

	case 'upgrade':
		require_once 'inc/config.php'; // for CODE_VERSION
		require_once 'inc/database.php';
		require_once 'inc/dictionary.php';
		require_once 'inc/functions.php'; // for ip translation functions
		require_once 'inc/upgrade.php';
		renderUpgraderHTML();
		break;

	case 'installer':
		require_once 'inc/dictionary.php';
		require_once 'inc/config.php';
		require_once 'inc/install.php';
		renderInstallerHTML();
		break;

	default:
		throw new InvalidRequestArgException ('module', $requestedModule);
	}
	ob_end_flush();
}
catch (Exception $e)
{
	ob_end_clean();
	printException ($e);
}
?>
