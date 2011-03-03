<?php
ob_start();
try {
require 'inc/init.php';
if (array_key_exists ('module', $_REQUEST))
{
	switch ($_REQUEST['module'])
	{
	case 'tsuri':
		genericAssertion ('uri', 'string');
		proxyStaticURI ($_REQUEST['uri']);
		break;
	default:
		throw new InvalidRequestArgException ('module', $_REQUEST['module']);
	}
	exit;
}

require 'inc/interface.php';
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
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head><title><?php echo getTitle ($pageno); ?></title>
<?php printPageHeaders(); ?>
</head>
<body>
<table border=0 cellpadding=0 cellspacing=0 width="100%" height="100%" class="maintable">
 <tr class="mainheader"><td>
   <table width="100%" cellspacing="0" cellpadding="2" border="0">
   <tr>
    <td valign=top><a href="http://racktables.org/"><?php printImageHREF ('logo'); ?></a></td>
    <td valign=top><div class=greeting><?php printGreeting(); ?></div></td>
   </tr>
   </table>
 </td></tr>
 <tr><td class="menubar">
  <table border="0" width="100%" cellpadding="3" cellspacing="0">
  <tr><?php showPathAndSearch ($pageno); ?></tr>
  </table>
 </td></tr>
 <tr><td><?php showTabs ($pageno, $tabno); ?></td></tr>
 <tr><td><?php showMessageOrError(); ?></td></tr>
 <tr><td><?php echo $content; ?></td></tr>
</table>
</body>
</html>
<?php
	ob_flush();
} catch (Exception $e) {
	ob_end_clean();
	printException($e);
}
clearMessages(); // prevent message appearing in foreign tab
?>
