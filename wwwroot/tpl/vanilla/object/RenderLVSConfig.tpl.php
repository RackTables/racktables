<?php if (defined("RS_TPL")) {?>
	<?php $this->getH("PrintOpFormIntro", array('submitSLBConfig')); ?>
	<center><input type=submit value='Submit for activation'></center>
	</form>
	<pre><?php $this->lvsConfig ?></pre>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>