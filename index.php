<?php

require 'inc/init.php';
// no ctx override is necessary
fixContext();
if (!permitted())
{
	renderAccessDenied();
	die;
}
// Only store the tab name after clearance is got. Any failure is unhandleable.
setcookie ('RTLT-' . $pageno, $tabno, time() + getConfigVar ('COOKIE_TTL'));

echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'."\n";
echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">'."\n";
echo '<head><title>' . getTitle ($pageno, $tabno) . "</title>\n";
echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
echo "<link rel=stylesheet type='text/css' href=pi.css />\n";
echo "<link rel=icon href='" . getFaviconURL() . "' type='image/x-icon' />";
echo "<style type='text/css'>\n";
// Print style information
foreach (array ('F', 'A', 'U', 'T', 'Th', 'Tw', 'Thw') as $statecode)
{
	echo "td.state_${statecode} {\n";
	echo "\ttext-align: center;\n";
	echo "\tbackground-color: #" . (getConfigVar ('color_' . $statecode)) . ";\n";
	echo "\tfont: bold 10px Verdana, sans-serif;\n";
	echo "}\n\n";
}
?>
.validation-error {
	border:1px solid red;
}

.validation-success {
	border:1px solid green;
}
	</style>
	<script language='javascript' type='text/javascript' src='js/live_validation.js'></script>
	<script language='javascript' type='text/javascript' src='js/codepress/codepress.js'></script>
	<script type="text/javascript">
	function init() {
		document.add_new_range.range.setAttribute('match', "^\\d\\d?\\d?\\.\\d\\d?\\d?\\.\\d\\d?\\d?\\.\\d\\d?\\d?\\/\\d\\d?$");

		Validate.init();
	}
	window.onload=init;
	</script>
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
			default:
				showError ('Dispatching error for bypass parameter', __FILE__);
				break;
		}
		$tabhandler[$pageno][$tabno] ($_REQUEST[$page[$pageno]['bypass']]);
	}
	else
		$tabhandler[$pageno][$tabno] ();
}
elseif (isset ($page[$pageno]['handler']))
	$page[$pageno]['handler'] ($tabno);
else
	showError ("Failed to find handler for page '${pageno}', tab '${tabno}'", __FILE__);
?>
	</td>
	</tr>
	</table>
</body>
</html>
