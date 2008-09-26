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
<h2>Choose a port:</h2><br><br>
<input type=hidden id='remote_port_name'>
<input type=hidden id='remote_object_name'>
<select size=<?php echo getConfigVar ('MAXSELSIZE'); ?> id="ports">
<?php
	$type_id = $_REQUEST['type'];
	$port_id = $_REQUEST['port'];
	$object_id = $_REQUEST['object_id'];
	$port_name = $_REQUEST['port_name'];
	renderEmptyPortsSelect ($port_id, $type_id);
?>
</select><br><br>
<?php
	echo "<input type='submit' value='Proceed' onclick='".
	"if (getElementById(\"ports\").value != \"\") {".
	"	opener.location=\"$root/process.php?page=object&tab=ports&op=linkPort&object_id=$object_id&port_id=$port_id&port_name=$port_name&remote_port_name=\"+getElementById(\"remote_port_name\").value+\"&remote_object_name=\"+getElementById(\"remote_object_name\").value+\"&remote_port_id=\"+getElementById(\"ports\").value; ".
	"	window.close();".
	"}".
	"'>";
?>
</div>
</form>
</body>
</html>
