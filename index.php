<?php
ob_start();
try {
require 'inc/interface.php';
require 'inc/init.php';
prepareNavigation();
// no ctx override is necessary
fixContext();
redirectIfNecessary();
if (!permitted())
	renderAccessDenied();
header ('Content-Type: text/html; charset=UTF-8');
// Only store the tab name after clearance is got. Any failure is unhandleable.
$_SESSION['RTLT'][$pageno] = $tabno;

echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'."\n";
echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">'."\n";
echo '<head><title>' . getTitle ($pageno) . "</title>\n";
printPageHeaders();
?>
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
				assertUIntArg ($page[$pageno]['bypass']);
				break;
			case 'uint0':
				assertUIntArg ($page[$pageno]['bypass'], TRUE);
				break;
			case 'inet4':
				assertIPv4Arg ($page[$pageno]['bypass']);
				break;
			case 'string':
				assertStringArg ($page[$pageno]['bypass']);
				break;
			default:
				throw new RackTablesError ('Dispatching error for bypass parameter', RackTablesError::INTERNAL);
		}
		showMessageOrError();
		call_user_func ($tabhandler[$pageno][$tabno], $_REQUEST[$page[$pageno]['bypass']]);
	}
	else
	{
		showMessageOrError();
		call_user_func ($tabhandler[$pageno][$tabno]);
	}
}
elseif (isset ($page[$pageno]['handler']))
{
	showMessageOrError();
	$page[$pageno]['handler'] ($tabno);
}
else
	throw new RackTablesError ("Failed to find handler for page '${pageno}', tab '${tabno}'", RackTablesError::INTERNAL);
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
