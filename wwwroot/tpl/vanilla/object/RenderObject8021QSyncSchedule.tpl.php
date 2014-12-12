<?php if (defined("RS_TPL")) {?>

	<table border=0 cellspacing=0 cellpadding=3 align=center>

	<?php 
		$this->startLoop('Looparray');
	?>
	<tr><th width='50%' class=tdright><?php $this->Th; ?>:</th><td class=tdleft colspan=2><?php $this->Td; ?></td></tr>
	<?php $this->endLoop(); ?>
	<tr><th class=tdright>run now:</th><td class=tdcenter>
	<?php $this->getH('PrintOpFormIntro', 'exec8021QPull'); 
		$this->getH('PrintImageHref', array('prev', 'pull remote changes in', TRUE, 101));
	?>
	</form></td><td class=tdcenter>
	<?php 
		if($this->is('Maxdecisions', TRUE)){
			$this->getH('PrintImageHref', array('COMMIT gray', 'cannot push due to version conflict(s)'));
		}
		else{
			$this->getH('PrintOpFormIntro', 'exec8021Qpush');
			$this->getH('PrintImageHref', array('COMMIT', 'push local changes out', TRUE, 102));
			echo '</form>';
		}
	?>
	</td></tr></table>


<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>