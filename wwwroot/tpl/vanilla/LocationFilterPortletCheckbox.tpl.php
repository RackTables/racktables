<?php if (defined("RS_TPL")) {?>
	<div class=tagbox style='text-align:left; padding-left: <?php $this->LevelSpace; ?>px;'>
		<label>
			<input type=checkbox name='location_id[]' class=<?php $this->Level; ?> value='<?php $this->Id; ?>'<?php $this->Checked; ?> onClick=checkAll(this)>
			<?php $this->Name; ?>
		</label>
		<?php if ($this->is("Kidc")) { ?>
			<a id='lfa<?php $this->Id; ?>' onclick="expand('<?php $this->Id; ?>')\" href="#" > - </a>
			<div id='lfd<?php $this->Id; ?>'>
				<?php $this->Locations; ?>
			</div>
		<?php } ?>
	</div>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>