<?php if (defined("RS_TPL")) {
	$this->addJS('js/codepress/codepress.js');
	$this->getH('PrintOpFormIntro',array('updateFileText', array ('mtime_copy' => $this->_MTime))); ?>
	<table border=0 align=center>
		<tr>
			<td>
				<textarea rows=45 cols=180 id=file_text name=file_text tabindex=101 class='codepress <?php $this->Syntax; ?>'>
					<?php $this->Content; ?>
				</textarea>
			</td>
		</tr>
		<tr>
			<td class=submit><input type=submit value='Save' onclick='$(file_text).toggleEditor();'></td>
		</tr>
	</table>
	</form>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>