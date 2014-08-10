<?php if (defined("RS_TPL")) {?>
	<div class=portlet>
		<h2>Manage existing (<?php $this->countAddrspaceList ?>)</h2>
		<?php if ($this->is("hasAddrspaceList", true)) { ?>
			<table class='widetable' border=0 cellpadding=5 cellspacing=0 align='center'>
			<tr><th>&nbsp;</th><th>prefix</th><th>name</th><th>capacity</th></tr>
			<?php $this->startLoop("allNetinfo"); ?>	
				<tr valign=top><td>
				<?php $this->destroyItem ?>
				</td><td class=tdleft><?php $this->mkAIpmask ?></td>
				<td class=tdleft><?php $this->name ?>
				<?php $this->RendTags ?>
				</td><td>
				<?php $this->ipnetCap ?>
				</tr>
			<?php $this->endLoop(); ?> 
			</table>
		<?php } ?> 
	</div>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>