<?php if (defined("RS_TPL")) {?>

	<div class=portlet><h2>Current configuration</h2>
	<table cellspacing=0 cellpadding=5 align=center class=widetable width='50%'>
	<tr><th class=tdleft>Option</th>
	<th class=tdleft>Value</th></tr>
	<?php $this->getH('PrintOpFormIntro', 'upd'); ?> 

	<?php $this->startLoop('LoopArray'); ?>
		<input type=hidden name=<?php $this->Index; ?>_varname value='<?php $this->VarName; ?>'>
		<tr><td class="tdright">
		<?php $this->RenderConfig; ?>
		</td>
		<td class="tdleft"><input type=text name=<?php $this->Index; ?>_varvalue value='<?php $this->HtmlSpecialChars; ?>' size=24></td>
		<td class="tdleft">
		<?php $this->OpLink ?> 
		</td></tr>
	<?php $this->endLoop(); ?>
	<input type=hidden name=num_vars value=<?php $this->Index; ?>>
	<tr><td colspan=3>
	<?php $this->getH('PrintImageHref', array('SAVE', 'Save changes', TRUE)); ?>
	</td></tr>
	</form>
	</div>

<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>