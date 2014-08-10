<?php if (defined("RS_TPL")) {?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" style="height: 100%;">
	<head>
		<link rel="stylesheet" type="text/css" href="?module=chrome&amp;uri=css/pi.css">
		<title>RackTables pop-up</title>
		<?php $this->Header; ?>
	</head>
	<body style="height: 100%;">
		<?php $this->Payload; ?>
	</body>
</html>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>