<?php if (defined("RS_TPL")) {?>
<br><table class=cooltable border=0 cellpadding=5 cellspacing=0 align=center>
<tr><th>Origin</th><th>Key</th><th>&nbsp;</th><th>Word</th><th>&nbsp;</th></tr>
<?php if ($this->is('NewTop')) { ?>
	<?php $this->getH('PrintOpFormIntro', 'add'); ?>
	<tr>
		<td>&nbsp;</td><td>&nbsp;</td>
		<td>
			<?php $this->getH('PrintImageHref', array('add', 'Add new', TRUE)); ?>
		</td>
		<td class=tdleft><input type=text name=dict_value size=64 tabindex=100></td>
		<td>
			<?php $this->getH('PrintImageHref',array('add', 'Add new', TRUE, 101)); ?>
		</td>
	</tr>
	</form>
<?php } ?>
<?php while($this->loop('ChapterList')) { ?>
	<tr class=row_<?php $this->order ?>>
	<td>
	<?php if($this->is('lowkey', true)) {
	    	$this->getH('PrintImageHref', 'computer'); ?>
		</td>
		<td class=tdright><?php $this->key ?></td>
		<td>&nbsp;</td><td><?php $this->value ?></td>
		<td>&nbsp;</td>
	<?php } else { ?>
		<?php $this->getH('OpFormIntro', array('upd', array ('dict_key' => $this->_key))); 
			  $this->getH('PrintImageHref', 'favorite');?>
		</td>
		<td class=tdright><?php $this->key ?></td>
		<td>
			<?php if($this->_refcnt) $this->getH('PrintImageHref', array('nodelete', 'referenced ' . $this->_refcnt . ' time(s)')); 
	    		  else $this->getH('GetOpLink', array(array('op'=>'del', 'dict_key'=>$this->_key), '', 'delete', 'Delete word'))//echo getOpLink (array('op'=>'del', 'dict_key'=>$key), '', 'delete', 'Delete word'); ?>
		</td>
		<td class=tdleft><input type=text name=dict_value size=64 value='<?php $this->value ?>'></td><td>
			<?php $this->getH('printImageHref', array('save', 'Save changes', TRUE)) ?>
		</td>	
		</form>
	<?php } ?>
	</tr>
<?php } ?>
<?php if (!$this->is('NewTop')) { ?>
	<?php $this->getH('PrintOpFormIntro', 'add'); ?>
	<tr>
		<td>&nbsp;</td><td>&nbsp;</td>
		<td>
			<?php $this->getH('PrintImageHref', array('add', 'Add new', TRUE)); ?>
		</td>
		<td class=tdleft><input type=text name=dict_value size=64 tabindex=100></td>
		<td>
			<?php $this->getH('PrintImageHref',array('add', 'Add new', TRUE, 101)); ?>
		</td>
	</tr>
	</form>
<?php } ?>
	  </table>



<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>