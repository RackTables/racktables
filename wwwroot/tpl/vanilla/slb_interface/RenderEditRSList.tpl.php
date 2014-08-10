<?php if (defined("RS_TPL")) {?>
	<table cellspacing=0 cellpadding=5 align=center class=cooltable>
	<tr><th>&nbsp;</th><th>Address</th><th>Port</th><th>Comment</th><th>in service</th><th>configuration</th><th>&nbsp;</th></tr>
	<?php $this->getH("PrintOpFormIntro", array( 'addRS')); ?> 
	<tr class=row_odd valign=top><td>
	<?php $this->getH("PrintImageHREF", array('add', 'Add new real server')); ?> 	
	</td><td><input type=text name=rsip></td>

	<td><input type=text name=rsport size=5 value='<?php $this->default_port ?> '></td>
	<td><input type=text name=comment size=15></td>

	<td><input type=checkbox name=inservice <?php $this->checked ?> ></td>
	<td><textarea name=rsconfig></textarea></td><td>
	<?php $this->getH("PrintImageHREF", array('ADD', 'Add new real server', TRUE)); ?> 
	</td></tr></form>

	<?php $this->startLoop("rs_outTable"); ?>	
		<?php $this->OpFormIntro ?>
		<tr valign=top class=row_<?php $this->order ?> ><td>
		<?php $this->OpLink ?>
		</td><td><input type=text name=rsip value='<?php $this->rs_rsip ?>'></td>
		<td><input type=text name=rsport size=5 value='<?php $this->rs_rsport ?>'></td>
		<td><input type=text name=comment size=15 value='<?php $this->rs_comment ?>'></td>
		<td><input type=checkbox name=inservice <?php $this->checked ?> ></td>
		<td><textarea name=rsconfig><?php $this->rs_rsconfig ?></textarea></td><td>
		<?php $this->ImgHref ?>
		</td></tr></form>
	<?php $this->endLoop(); ?> 

	</table>	
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>