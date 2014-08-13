<?php if (defined("RS_TPL")) {?>
	<?php $this->switchPortScripts ?>
	<?php $this->getH("PrintOpFormIntro", array('importDPData')); ?>
	<br><table cellspacing=0 cellpadding=5 align=center class=widetable>
	<tr><th colspan=2>local port</th><th></th><th>remote device</th><th colspan=2>remote port</th><th><input type="checkbox" checked id="cb-toggle"></th></tr>
	<?php while ($this->loop('AllNeighbors')) : ?>
		<tr class="<?php $this->tr_class ?>">	
		<?php if ($this->is("isInitialRow", true)) { ?>
			<?php $this->td_class ?>
			<td rowspan="<?php $this->count ?>" <?php $this->td_class ?> NOWRAP>
			<?php if ($this->is("id_port_link_local")) { ?>
				<?php $this->id_port_link_local ?>
			<?php } else {?>
			 	<a class='interactive-portname port-menu nolink'><?php $this->localport ?></a>
			<?php } ?>
			</td>
		<?php } ?>
		<td><?php $this->portIIFOIFLocal ?></td>
		<td><?php $this->ifTypeVariants ?></td>
		<td><?php $this->device ?></td>
		<?php if (!$this->is("id_port_link_remote")) { ?>
			<td><?php $this->id_port_link_remote ?></td>
		<?php } ?> 
		<td><?php $this->portIIFOIFRemote ?></td>
		<td><?php if ($this->is("inputno",null)) { ?>
			<input type=checkbox name=do_<?php $this->inputno ?> class='cb-makelink'>
		<?php } ?></td>
		<?php if ($this->is("error_message")) { ?>
			<td style="background-color: white; border-top: none"><?php $this->error_message ?></td>
		<?php } ?> 
		</tr>
	<?php endwhile ?>
	
	<?php if ($this->is("inputno")) { ?>
	 	<input type=hidden name=nports value=<?php $this->inputno ?>>
	 	<tr><td colspan=7 align=center><?php $this->getH("PrintImageHref", array('CREATE', 'import selected', TRUE)); ?></td></tr>
	<?php } ?>  
	</table></form>
	<?php $this->addRequirement("Header","HeaderJsInline",array("code"=>"<<<END
$(document).ready(function () {
	$('#cb-toggle').click(function (event) {
		var list = $('.cb-makelink');
		for (var i in list) {
			var cb = list[i];
			cb.checked = event.target.checked;
		}
	}).triggerHandler('click');
});
END")); ?>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>