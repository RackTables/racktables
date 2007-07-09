<?

require 'inc/init.php';
authorize();

echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'."\n";
echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">'."\n";
echo '<head><title>' . getTitle ($pageno, $tabno) . "</title>\n";
echo "<link rel=stylesheet type='text/css' href=pi.css />\n";
echo "<link rel=icon href='" . getFaviconURL() . "' type='image/x-icon' />";
echo "<style type='text/css'>\n";
// Print style information
foreach ($color as $statecode => $colorcode)
{
	echo "td.state_${statecode} {\n";
	echo "text-align: center;\n";
	echo "background-color: #${colorcode};\n";
	echo "font: bold 10px Verdana, sans-serif;\n";
	echo "}\n\n";
}
?>
	</style>
	</head>
<body>
 <table border=0 cellpadding=0 cellspacing=0 width='100%' height='100%' class=maintable>
 <tr class=mainheader>
  <td>
   <table width='100%' cellspacing=0 cellpadding=2 border=0>
   <tr>
    <td valign=top><? printImageHREF ('logo'); ?></td>
    <td valign=top><div class=greeting><? printGreeting(); ?></div></td>
   </tr>
   </table>
  </td>
 </tr>

 <tr>
  <td class="menubar">
   <table border="0" width="100%" cellpadding="3" cellspacing="0">
   <tr>
<? showPathAndSearch ($pageno); ?>
   </tr>
   </table>
  </td>
 </tr>

	<tr>
<?
	showTabs ($pageno, $tabno);
?>
	</tr>

 <tr>
  <td>
<?
if (isset ($page[$pageno]['handler']))
	$page[$pageno]['handler'] ($tabno);
else
	showError ("Failed to find handler for page '${pageno}'");
?>
	</td>
	</tr>
	</table>
</body>
</html>
