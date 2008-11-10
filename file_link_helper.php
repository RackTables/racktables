<?php
	require 'inc/init.php';
	// This is our context.
	$pageno = 'files';
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
<h2>Choose a file:</h2><br><br>
<input type=hidden id='file_name'>
<select size=<?php echo getConfigVar ('MAXSELSIZE'); ?> id="file_id">
<?php
	$entity_type = $_REQUEST['entity_type'];
	$entity_id = $_REQUEST['entity_id'];

	// Append different param to URL depending on entity_type
	switch ($entity_type)
	{
		case 'ipv4net':
			$entity_param = 'id';
			break;
		case 'ipv4rspool':
			$entity_param = 'pool_id';
			break;
		case 'ipv4vs':
			$entity_param = 'vs_id';
			break;
		case 'object':
			$entity_param = 'object_id';
			break;
		case 'rack':
			$entity_param = 'rack_id';
			break;
		case 'user':
			$entity_param = 'user_id';
			break;
	}


	$files = getAllUnlinkedFiles($entity_type, $entity_id);
	foreach ($files as $file)
	{
		echo "<option value='${file['id']}' onclick='getElementById(\"file_name\").value=\"${file['name']}\";'>${file['name']}</option>\n";
	}
?>
</select><br><br>
<?php
	echo "<input type='submit' value='Proceed' onclick='" .
	"if (getElementById(\"file_id\").value != \"\") {\n" .
	"	opener.location=\"${root}process.php?page=$entity_type&tab=files&op=linkFile&entity_type=$entity_type&entity_id=$entity_id&$entity_param=$entity_id&file_id=\"+getElementById(\"file_id\").value+\"&file_name=\"+getElementById(\"file_name\").value; \n" .
	"	window.close();" .
	"}" .
	"'>";
?>
</div>
</form>
</body>
</html>
