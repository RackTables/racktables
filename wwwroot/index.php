<?php
ob_start();
try {
// Code block below is a module request dispatcher. Turning it into a
// function will break things because of the way require() works.
if (array_key_exists ('module', $_REQUEST))
{
	switch ($_REQUEST['module'])
	{
	case 'tsuri':
		require_once 'inc/init.php';
		genericAssertion ('uri', 'string');
		proxyStaticURI ($_REQUEST['uri']);
		break;
	case 'download':
		require_once 'inc/init.php';
		$pageno = 'file';
		$tabno = 'download';
		fixContext();
		if (!permitted())
		{
			require_once 'inc/interface.php';
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
	case 'image':
		require_once 'inc/render_image.php';
		// 'progressbar's never change, attempt an IMS chortcut before loading init.php
		checkIMSCondition();
		require_once 'inc/init.php';
		try
		{
			dispatchImageRequest();
		}
		catch (Exception $e)
		{
			ob_clean();
			renderError();
		}
		break;
	case 'ajax':
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
	case 'redirect':
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
		catch (InvalidRequestArgException2 $e)
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
	default:
		throw new InvalidRequestArgException ('module', $_REQUEST['module']);
	}
	ob_end_flush();
	exit;
}

require_once 'inc/interface.php';
# init.php has to be included after interface.php, otherwise the bits
# set by local.php get lost
require_once 'inc/init.php';
prepareNavigation();
// no ctx override is necessary
fixContext();
redirectIfNecessary();
if (!permitted())
	renderAccessDenied();
header ('Content-Type: text/html; charset=UTF-8');
// Only store the tab name after clearance is got. Any failure is unhandleable.
if (isset ($_REQUEST['tab']) and ! isset ($_SESSION['RTLT'][$pageno]['dont_remember']))
	$_SESSION['RTLT'][$pageno] = array ('tabname' => $tabno, 'time' => time());

// call the main handler - page or tab handler.
// catch exception and show its error message instead of page/tab content
try {
if (isset ($tabhandler[$pageno][$tabno]))
	call_user_func ($tabhandler[$pageno][$tabno], getBypassValue());
elseif (isset ($page[$pageno]['handler']))
	$page[$pageno]['handler'] ($tabno);
else
	showError ("Failed to find handler for page '${pageno}', tab '${tabno}'");
$content = ob_get_clean();
} catch (Exception $e) {
	ob_clean();
	$content = '';
	showError ("Unhandled exception: " . $e->getMessage());
}

ob_start();
renderInterfaceHTML ($pageno, $tabno, $content);
	ob_flush();
} catch (Exception $e) {
	ob_end_clean();
	printException($e);
}
clearMessages(); // prevent message appearing in foreign tab
?>
