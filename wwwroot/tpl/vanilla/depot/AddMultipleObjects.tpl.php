<?php if (defined("RS_TPL")) {?>
<div class=portlet>
	<h2>Distinct types, same tags</h2>
	<?php $this->FormIntro ?> 
	<table border=0 align=center>
	<tr><th>Object type</th><th>Common name</th><th>Visible label</th>
	<th>Asset tag</th><th>Tags</th></tr>
	<?php $this->startLoop("ObjectListData"); ?>	
		<tr><td>
		<?php $this->NiftySelect ?>
		</td>
		<td><input type=text size=30 name=<?php $this->i ?>_object_name tabindex=<?php $this->Tabindex ?> ></td>
		<td><input type=text size=30 name=<?php $this->i ?>_object_label tabindex=<?php $this->Tabindex ?> ></td>
		<td><input type=text size=20 name=<?php $this->i ?>_object_asset_no tabindex=<?php $this->Tabindex ?> ></td>
		<td valign=top rowspan=<?php $this->Max ?> >
		<?php $this->TagsPicker ?> 
		</td>
	<?php $this->endLoop(); ?> 
	<tr><td class=submit colspan=5><input type=submit name=got_fast_data value='Go!'></td></tr>
	</form></table>
</div>

<div class=portlet>
	<h2>Same type, same tags</h2>
	<?php $this->FormIntroLotOfObjects ?>
	<table border=0 align=center><tr><th>names</th><th>type</th></tr>
	<tr><td rowspan=3><textarea name=namelist cols=40 rows=25>
	</textarea></td><td valign=top>
	<?php $this->test ?> 
	<?php $this->get("sameTypeSameTagSelect"); ?> 
	</td></tr>
	<tr><th>Tags</th></tr>
	<tr><td valign=top>
		<?php $this->TagsPicker ?> 
	</td></tr>
	<tr><td colspan=2><input type=submit name=got_very_fast_data value='Go!'></td></tr></table>
	</form>
	</div>	
</div>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>