<?php
ob_start();
# Neither "throw/catch" for custom exceptions nor printException() will
# work without first loading exceptions.php.
require_once 'inc/exceptions.php';
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
		if (! permitted())
		{
			renderAccessDenied (FALSE);
			break;
		}
		header ('Content-Type: text/html; charset=UTF-8');
		// Only store the tab name after clearance is got. Any failure is unhandleable.
		if (isset ($_REQUEST['tab']) and ! isset ($_SESSION['RTLT'][$pageno]['dont_remember']))
			$_SESSION['RTLT'][$pageno] = array ('tabname' => $tabno, 'time' => time());
		// call the main handler - page or tab handler.
		if (isset ($tabhandler[$pageno][$tabno]))
			call_user_func ($tabhandler[$pageno][$tabno], getBypassValue());
		elseif (isset ($page[$pageno]['handler']))
			$page[$pageno]['handler'] ($tabno);
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
		genericAssertion ('uri', 'string');
		proxyStaticURI ($_REQUEST['uri']);
		break;
	case 'download' == $_REQUEST['module']:
		require_once 'inc/init.php';
		$pageno = 'file';
		$tabno = 'download';
		fixContext();
		if (!permitted())
		{
			renderAccessDenied (FALSE);
			break;
		}

		$asattach = (isset ($_REQUEST['asattach']) and $_REQUEST['asattach'] == 'no') ? FALSE : TRUE;
		$file = getFile (getBypassValue());
		header("Content-Type: {$file['type']}");
		header("Content-Length: {$file['size']}");
		if ($asattach)
			header("Content-Disposition: attachment; filename={$file['name']}");
		echo $file['contents'];
		break;
	case 'image' == $_REQUEST['module']:
		require_once 'inc/caching.php';
		// 'progressbar's never change, attempt an IMS shortcut before loading init.php
		if (@$_REQUEST['img'] == 'progressbar')
			if (checkCachedResponse (0, 604800)) // 7 * 24 * 3600
				exit;
		require_once 'inc/init.php'; // for authentication check
		require_once 'inc/render_image.php';
		try
		{
			dispatchImageRequest();
		}
		catch (Exception $e)
		{
			ob_clean();
			renderErrorImage();
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
			$location = buildWideRedirectURL();
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
			if (!isset ($delayauth[$pageno][$tabno][$op]) and !permitted())
				showError ('Operation not permitted');
			else
			{
				// Call below does the job of bypass argument assertion, if such is required,
				// so the ophandler function doesn't have to re-assert this portion of its
				// arguments. And it would be even better to pass returned value to ophandler,
				// so it is not necessary to remember the name of bypass in it.
				getBypassValue();
				if (strlen ($redirect_to = call_user_func ($ophandler[$pageno][$tabno][$op])))
					$location = $redirect_to;
			}
			header ("Location: " . $location);
		}
		// known "soft" failures require a short error message
		catch (InvalidRequestArgException $e)
		{
			ob_clean();
			showError ($e->getMessage());
			header ('Location: ' . $location);
		}
		catch (RTDatabaseError $e)
		{
			ob_clean();
			showError ('Database error: ' . $e->getMessage());
			header ('Location: ' . $location);
		}
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
		// Enforce default value for now, releases prior to 0.17.0 didn't support 'httpd' auth source.
		$user_auth_src = 'database';
		if (FALSE === @include_once 'inc/secret.php')
			die ('<center>There is no working RackTables instance here, <a href="?module=installer">install</a>?</center>');
		try
		{
			$dbxlink = new PDO ($pdo_dsn, $db_username, $db_password);
		}
		catch (PDOException $e)
		{
			die ("Database connection failed:\n\n" . $e->getMessage());
		}
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
