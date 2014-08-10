<?php if (defined("RS_TPL")) {?>

	<table cellspacing="0" align="center" width="50%">
	<tr><td>&nbsp;</td><th>Server</th><th>Graph ID</th><th>Caption</th><td>&nbsp;</td></tr>
	<?php 
		$this->getH('PrintOpFormIntro', 'add');
	?>
	<tr><td>
	<?php 
		$this->getH('PrintImageHref', array('Attach', 'Link new graph', TRUE));
	?>
	</td><td>
	<?php 
		$this->Getselect;
	?>
	</td><td><input type=text name=graph_id tabindex=100></td><td><input type=text name=caption tabindex=101></td><td>
	<?php 
		$this->getH('PrintImageHref', array('Attach', 'Link new graph', TRUE, 101));
	?>
	</td></tr></form>
	</table>
	<br/><br/>


<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>