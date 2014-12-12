<?php if (defined("RS_TPL")) {?>

	<div class=portlet><h2>UCS Actions</h2>
	<?php
		$this->getH('PrintOpFormIntro', 'AutoPopulateUCS');
	 ?>
	<table cellspacing=0 cellpadding=5 align=center class=widetable>
		<tr><th class=tdright><label for=ucs_login>Login:</label></th>
			<td class=tdleft colspan=2><input type=text name=ucs_login id=ucs_login></td></tr>
				<tr><th class=tdright><label for=ucs_password>Password:</label></th>
			<td class=tdleft colspan=2><input type=password name=ucs_password id=ucs_password></td></tr>
			<tr><th colspan=3><input type=checkbox name=use_terminal_settings id=use_terminal_settings>
			<label for=use_terminal_settings>Use Credentials from terminal_settings()</label></th></tr>
		<tr><th class=tdright>Actions:</th><td class=tdleft> 
		<?php
			$this->getH('PrintImagreHref', array('DQUEUE sync_ready', 'Auto-populate UCS', TRUE));
		 ?>
		 </td><td class=tdright>
		 <?php
		 	$this->getH('GetOpLink', array(array ('op' => 'cleanupUCS'), '', 'CLEAR', 'Clean-up UCS domain', 'need-confirmation'));
		  ?>
		</td></tr></table></form></div>

<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>