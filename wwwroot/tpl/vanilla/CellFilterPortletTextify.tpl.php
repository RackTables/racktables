<?php if (defined("RS_TPL")) {?>
	<?php $js = <<<END
	function textifyCellFilter(target, text)
	{
		var portlet = $(target).closest ('.portlet');
		portlet.find ('textarea[name="cfe"]').html (text);
		portlet.find ('input[type="checkbox"]').attr('checked', '');
		portlet.find ('input[type="radio"][value="and"]').attr('checked','true');
	}
END;
	$this->addRequirement("Header","HeaderJsInline",array("code"=>$js));
?>
	<a href="#" onclick="textifyCellFilter(this, '<?php $this->Text; ?>'); return false">
		<img src="pix/pix/pgadmin3-viewdata-grayscale.png" width=32 height=32 border=0 title="Make text expression from current filter">
	</a>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>