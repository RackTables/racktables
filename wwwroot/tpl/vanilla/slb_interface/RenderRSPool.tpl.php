<?php if (defined("RS_TPL")) {?>
	<table border=0 class=objectview cellspacing=0 cellpadding=0>
		<?php if ($this->is("PoolInfo")) { ?>
			<tr><td colspan=2 align=center><h1><?php $this->PoolInfo ?></h1></td></tr>
		<?php } ?> 
		<tr><td class=pcleft>
		<?php $this->RenderedEntity ?> 
		<?php $this->RSPoolSrvPortlet ?>
		</td><td class=pcright>
		<?php $this->RenderedSLBTrip2 ?> 
		<?php $this->RenderedSLBTrip ?> 
		</td></tr><tr><td colspan=2>
		<?php $this->RenderedFiles ?> 
		</td></tr></table>
	</td></tr></table>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>