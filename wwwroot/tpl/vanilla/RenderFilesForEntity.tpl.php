<?php if (defined("RS_TPL")) {?>
	<div class=portlet>
		<h2>Upload and link new</h2>
		<table border=0 cellspacing=0 cellpadding='5' align='center' class='widetable'>
		<tr><th>File</th><th>Comment</th><th></th></tr>
		<?php $this->getH("PrintOpFormIntro", array('addFile', array (), TRUE)); ?> 
		<tr>
		<td class=tdleft><input type='file' size='10' name='file' tabindex=100></td>
		<td class=tdleft><textarea tabindex=101 name=comment rows=10 cols=80></textarea></td><td>
		<?php $this->getH("PrintImageHref", array('CREATE', 'Upload file', TRUE, 102)); ?>
		</td></tr></form>
		</table><br>
	</div>

	<?php if ($this->is("ShowFiles",true)) { ?>
		<div class=portlet>
			<h2>Link existing (<?php $this->CountFiles ?>)</h2>
			<?php $this->getH("PrintOpFormIntro", array('linkFile')); ?> 
			<table border=0 cellspacing=0 cellpadding='5' align='center'>
			<tr><td class=tdleft>
			<?php $this->PrintedSelect ?> 
			</td><td class=tdleft>
			<?php $this->getH("PrintImageHref", array('ATTACH', 'Link file', TRUE)); ?> 
			</td></tr></table>
			</form>
		</div>
	<?php } ?>

	 <?php if ($this->is("ShowFileList",true)) { ?>
		<div class=portlet>
			<h2>Manage linked (<?php $this->CountFileList ?>)</h2>
			<table border=0 cellspacing=0 cellpadding='5' align='center' class='widetable'>
			<tr><th>File</th><th>Comment</th><th>Unlink</th></tr>
			<?php $this->startLoop("FilelistsOutput"); ?>	
				<tr valign=top><td class=tdleft>
				<?php $this->FileCell ?> 
				</td><td class=tdleft><?php $this->Comment ?></td><td class=tdcenter>
				<?php $this->OpLink ?></td></tr>
			<?php $this->endLoop(); ?> 
			</table>
		</div>
	<?php } ?>

<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>