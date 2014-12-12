<?php if (defined("RS_TPL")) {?>

	<?php if ($this->is('Sticky', 'yes')) { ?>
		<?php $this->getH("PrintImageHref", array('nodelete', 'system mapping')); ?>	
	<?php } elseif ($this->is('RefCnt', true) ){ ?>
		<?php $this->getH("PrintImageHref", array('nodelete', $this->_RefCnt . ' value(s) stored for objects')); ?> 
	<?php } else { ?>
		<?php $this->getH("GetOpLink", array(array('op'=>'del', 'attr_id'=>$this->_Id, 'objtype_id'=>$this->_ObjId), '', 'delete', 'Remove mapping')); ?>
	<?php } ?>
	<?php $this->DecObj ?>
	<?php if ($this->is("Type",'dict')) { ?>
		(values from '<?php $this->ChapterName ?>')
	<?php } ?><br>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>