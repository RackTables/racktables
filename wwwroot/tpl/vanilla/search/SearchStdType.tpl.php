<?php if (defined("RS_TPL")) {?>

	<div class=portlet><h2><a href='index.php?page=<?php $this->page ?>'><?php $this->title ?></a></h2>

		<table border=0 cellpadding=5 cellspacing=0 align=center class=cooltable>
		<?php $this->startLoop("searchLoopObjs"); ?>
			<tr class=row_<?php $this->rowOrder ?>><td class=tdleft>
			<?php $this->renderedCell ?>
			</td></tr>
		<?php $this->endLoop(); ?>
		</table>
	</div>


<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>