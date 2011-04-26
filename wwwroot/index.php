<?php
ob_start();
require_once 'inc/pre-init.php';
try {
// Code block below is a module request dispatcher. Turning it into a
// function will break things because of the way require() works.
	switch (TRUE)
	{
	case ! array_key_exists ('module', $_REQUEST):
	case 'interface' == $_REQUEST['module']:
		require_once 'inc/interface.php';
		// init.php has to be included after interface.php, otherwise the bits
		// set by local.php get lost
		require_once 'inc/init.php';
		prepareNavigation();
		// Security context is built on the requested page/tab/bypass data,
		// do not override.
		fixContext();
		redirectIfNecessary();
		assertPermission();
		header ('Content-Type: text/html; charset=UTF-8');
		// Only store the tab name after clearance is got. Any failure is unhandleable.
		if (isset ($_REQUEST['tab']))
		{
			if (isset ($_SESSION['RTLT'][$pageno]['dont_remember']))
				unset ($_SESSION['RTLT'][$pageno]['dont_remember']);
			else
				$_SESSION['RTLT'][$pageno] = array ('tabname' => $tabno, 'time' => time());
		}
		// call the main handler - page or tab handler.
		if (isset ($tabhandler[$pageno][$tabno]))
		{
			if (! function_exists ($tabhandler[$pageno][$tabno]))
				throw new RackTablesError ("Missing handler function for node '${pageno}-${tabno}'", RackTablesError::INTERNAL);
			call_user_func ($tabhandler[$pageno][$tabno], getBypassValue());
		}
		elseif (isset ($page[$pageno]['handler']))
		{
			if (! function_exists ($page[$pageno]['handler']))
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
	case 'chrome' == $_REQUEST['module']:
		require_once 'inc/init.php';
		require_once 'inc/solutions.php';
		genericAssertion ('uri', 'string');
		proxyStaticURI ($_REQUEST['uri']);
		break;
	case 'download' == $_REQUEST['module']:
		require_once 'inc/init.php';
		$pageno = 'file';
		$tabno = 'download';
		fixContext();
		assertPermission();
		$file = getFile (getBypassValue());
		header("Content-Type: {$file['type']}");
		header("Content-Length: {$file['size']}");
		if (! array_key_exists ('asattach', $_REQUEST) or $_REQUEST['asattach'] != 'no')
			header("Content-Disposition: attachment; filename={$file['name']}");
		echo $file['contents'];
		break;
	case 'image' == $_REQUEST['module']:
		# The difference between "image" and "download" ways to serve the same
		# picture file is that the former is used in <IMG SRC=...> construct,
		# and the latter is accessed as a standalone URL and can reply with any
		# Content-type. Hence "image" module indicates failures with internally
		# built images, and "download" can return a full-fledged "permission
		# denied" or "exception" HTML page instead of the file requested.
		require_once 'inc/init.php'; // for authentication check
		require_once 'inc/solutions.php';
		try
		{
			dispatchImageRequest();
		}
		catch (RTPermissionDenied $e)
		{
			ob_clean();
			renderAccessDeniedImage();
		}
		catch (Exception $e)
		{
			ob_clean();
			renderErrorImage();
		}
		break;
	case 'progressbar' == $_REQUEST['module']:
		# Unlike images (and like static content), progress bars are processed
		# without a permission check, but only for authenticated users.
		require_once 'inc/init.php';
		require_once 'inc/solutions.php';
		try
		{
			genericAssertion ('done', 'uint0');
			// 'progressbar's never change, make browser cache the result
			if (checkCachedResponse (0, CACHE_DURATION))
				break;
			renderProgressBarImage ($_REQUEST['done']);
		}
		catch (Exception $e)
		{
			ob_clean();
			renderProgressBarError();
		}
		break;
	case 'ajax' == $_REQUEST['module']:
		require_once 'inc/ajax-interface.php';
		require_once 'inc/init.php';
		try
		{
			dispatchAJAXRequest();
		}
		catch (InvalidRequestArgException $e)
		{
			ob_clean();
			echo "NAK\nMalformed request";
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
	case 'redirect' == $_REQUEST['module']:
		// Include init after ophandlers/snmp, not before, so local.php can redefine things.
		require_once 'inc/ophandlers.php';
		// snmp.php is an exception, it is treated by a special hack
		if (isset ($_REQUEST['op']) and $_REQUEST['op'] == 'querySNMPData')
			require_once 'inc/snmp.php';
		require_once 'inc/init.php';
		try
		{
			genericAssertion ('op', 'string');
			$op = $_REQUEST['op'];
			prepareNavigation();
			$location = buildRedirectURL();
			// FIXME: find a better way to handle this error
			if ($op == 'addFile' && !isset($_FILES['file']['error']))
				throw new RackTablesError ('File upload error, check upload_max_filesize in php.ini', RackTablesError::MISCONFIGURED);
			fixContext();
			if
			(
				!isset ($ophandler[$pageno][$tabno][$op]) or
				!function_exists ($ophandler[$pageno][$tabno][$op])
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
			if (strlen ($redirect_to = call_user_func ($ophandler[$pageno][$tabno][$op])))
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
		header ('Location: ' . $location);
		// any other error requires no special handling and will be caught outside
		break;
	case 'popup' == $_REQUEST['module']:
		require_once 'inc/popup.php';
		require_once 'inc/init.php';
		renderPopupHTML();
		break;
	case 'upgrade' == $_REQUEST['module']:
		require_once 'inc/config.php'; // for CODE_VERSION
		require_once 'inc/dictionary.php';
		require_once 'inc/upgrade.php';
		renderUpgraderHTML();
		break;
	case 'installer' == $_REQUEST['module']:
		require_once 'inc/dictionary.php';
		require_once 'inc/install.php';
		renderInstallerHTML();
		break;
	default:
		throw new InvalidRequestArgException ('module', $_REQUEST['module']);
	}
	ob_end_flush();
}
catch (Exception $e)
{
	ob_end_clean();
	# prevent message appearing in foreign tab
	if (isset ($_SESSION['log']))
		unset ($_SESSION['log']);
	printException ($e);
}
?>
