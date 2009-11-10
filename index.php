<?php
ob_start();
try {
require 'inc/interface.php';
require 'inc/init.php';
$pageno = (isset ($_REQUEST['page'])) ? $_REQUEST['page'] : 'index';
// Special handling of tab number to substitute the "last" index where applicable.
// Always show explicitly requested tab, substitute the last used name in case
// it is awailable, fall back to the default one.

if (isset ($_REQUEST['tab'])) {
	$tabno = $_REQUEST['tab'];
	// check if we accidentaly got on a dynamic tab that shouldn't be shown for this object
	if ( isset($trigger[$pageno][$tabno]) and !strlen($trigger[$pageno][$tabno] ()) ) {
		$tabno = 'default';
		$url = "index.php?page=$pageno&tab=$tabno&".urlizeGetParameters(array('page', 'tab'));
		header('Location: '.$url);
		exit();
	}
} elseif (basename($_SERVER['PHP_SELF']) == 'index.php' and getConfigVar ('SHOW_LAST_TAB') == 'yes' and isset ($_SESSION['RTLT'][$pageno]))
{
	$tabno = $_SESSION['RTLT'][$pageno];
	$url = "index.php?page=$pageno&tab=$tabno&".urlizeGetParameters(array('page', 'tab'));
	header('Location: '.$url);
	exit();
}
else
	$tabno = 'default';

// no ctx override is necessary
redirectIfNecessary();
fixContext();
if (!permitted())
	renderAccessDenied();
// Only store the tab name after clearance is got. Any failure is unhandleable.
$_SESSION['RTLT'][$pageno] = $tabno;

echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'."\n";
echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">'."\n";
echo '<head><title>' . getTitle ($pageno) . "</title>\n";
echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
echo "<link rel=stylesheet type='text/css' href=pi.css />\n";
echo "<link rel=icon href='" . getFaviconURL() . "' type='image/x-icon' />";
printStyle();
?>
	<script language='javascript' type='text/javascript' src='js/racktables.js'></script>
	<script language='javascript' type='text/javascript' src='js/jquery-1.3.1.min.js'></script>
	<script language='javascript' type='text/javascript' src='js/live_validation.js'></script>
	<script language='javascript' type='text/javascript' src='js/codepress/codepress.js'></script>
	</head>
<body>
 <table border=0 cellpadding=0 cellspacing=0 width='100%' height='100%' class=maintable>
 <tr class=mainheader>
  <td colspan=2>
   <table width='100%' cellspacing=0 cellpadding=2 border=0>
   <tr>
    <td valign=top><a href='http://racktables.org/'><?php printImageHREF ('logo'); ?></a></td>
    <td valign=top><div class=greeting><?php printGreeting(); ?></div></td>
   </tr>
   </table>
  </td>
 </tr>

 <tr>
  <td class="menubar" colspan=2>
   <table border="0" width="100%" cellpadding="3" cellspacing="0">
   <tr>
<?php showPathAndSearch ($pageno); ?>
   </tr>
   </table>
  </td>
 </tr>

	<tr>
<?php
	showTabs ($pageno, $tabno);
?>
	</tr>

 <tr>
  <td colspan=2>
<?php
if (isset ($tabhandler[$pageno][$tabno]))
{
	if (isset ($page[$pageno]['bypass']) && isset ($page[$pageno]['bypass_type']))
	{
		switch ($page[$pageno]['bypass_type'])
		{
			case 'uint':
				assertUIntArg ($page[$pageno]['bypass'], 'index');
				break;
			case 'uint0':
				assertUIntArg ($page[$pageno]['bypass'], 'index', TRUE);
				break;
			case 'inet4':
				assertIPv4Arg ($page[$pageno]['bypass'], 'index');
				break;
			case 'string':
				assertStringArg ($page[$pageno]['bypass'], 'index');
				break;
			default:
				throw new RuntimeException ('Dispatching error for bypass parameter');
		}
		showMessageOrError();
		$tabhandler[$pageno][$tabno] ($_REQUEST[$page[$pageno]['bypass']]);
	}
	else
	{
		showMessageOrError();
		$tabhandler[$pageno][$tabno] ();
	}
}
elseif (isset ($page[$pageno]['handler']))
{
	showMessageOrError();
	$page[$pageno]['handler'] ($tabno);
}
else
	throw new RuntimeException ("Failed to find handler for page '${pageno}', tab '${tabno}'");
?>
	</td>
	</tr>
	</table>
</body>
</html>
<?php
	ob_end_flush();
} catch (Exception $e) {
	ob_end_clean();
	printException($e);
}
