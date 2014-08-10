<?php if (defined("RS_TPL")) {?>

	<?php $this->getH('PrintOpFormIntro', 'changeMyPassword'); ?>
	<table border=0 align=center>
	<tr><th class=tdright>Current password (*):</th><td><input type=password name=oldpassword tabindex=1></td></tr>
	<tr><th class=tdright>New password (*):</th><td><input type=password name=newpassword1 tabindex=2></td></tr>
	<tr><th class=tdright>New password again (*):</th><td><input type=password name=newpassword2 tabindex=3></td></tr>
	<tr><td colspan=2 align=center><input type=submit value='Change' tabindex=4></td></tr>
	</table></form>


<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>