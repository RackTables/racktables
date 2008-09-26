<?php
	require 'inc/init.php';
	// This is our context.
	$pageno = 'objects';
	$tabno = 'default';
	fixContext();
	if (!permitted())
	{
		renderAccessDenied();
		die;
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" style="height: 100%;">
<head>
 <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?php
echo '<title>' . getTitle ($pageno, $tabno) . "</title>\n";
echo "<link rel=stylesheet type='text/css' href=pi.css />\n";
echo "<link rel=icon href='" . getFaviconURL() . "' type='image/x-icon' />";
?>
</head>
<body style="height: 100%;">
<form action="javascript:;">
<div style="background-color: #f0f0f0; border: 1px solid #3c78b5; padding: 10px; height: 100%; text-align: center; margin: 5px;">
<h2>Pick address:</h2><br><br>
<input type=hidden id='ip'>
<select size=<?php echo getConfigVar ('MAXSELSIZE'); ?> id="addresses">
<?php renderAllIPv4Allocations(); ?>
</select><br><br>
<input type='submit' value='Proceed' onclick='if (getElementById("ip")!="") { opener.document.getElementById("remoteip").value=getElementById("ip").value; window.close();}'>
</div>
</form>
</body>
</html>
