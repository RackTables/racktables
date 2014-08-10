<?php if (defined("RS_TPL")) {?>
	<?php $this->addRequirement("Header","HeaderJsInclude",array("path"=>"js/jquery.optionTree.js")); ?>
	<input type=hidden name=<?php $this->tree_name ?>>
	<script type='text/javascript'>
		$(function() {
			var option_tree = <?php $this->option_tree ?>;
			var options = <?php $this->options ?>;
		    $('input[name=<?php $this->tree_name ?>]').optionTree(option_tree, options);
			});
	</script>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>