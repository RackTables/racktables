<?php if (defined("RS_TPL")) {?>
	<?php $this->getH("PrintOpFormIntro", array('addNATv4Rule')); ?>
	<tr align='center'><td>
	<?php $this->getH("PrintImageHref", array('add', 'Add new NAT rule', TRUE)); ?>
	</td><td>
	<?php $this->printTcpUdpSel ?>
	<select name='localip' tabindex=1>
	<?php $this->startLoop("allAlloc"); ?>	
		<option value="<?php $this->ip ?>"><?php $this->osif ?><?php $this->ip ?><?php $this->name ?></option>
	<?php $this->endLoop(); ?> 
	</select>:<input type='text' name='localport' size='4' tabindex=2></td>
	<td><input type='text' name='remoteip' id='remoteip' size='10' tabindex=3>
	<a href='javascript:;' onclick='window.open("<?php $this->hrefForHelper ?>", "findobjectip", "height=700, width=400, location=no, menubar=no, resizable=yes, scrollbars=no, status=no, titlebar=no, toolbar=no");'>
	<?php $this->getH("PrintImageHref", array('find', 'Find object')); ?>
	</a>:<input type='text' name='remoteport' size='4' tabindex=4></td><td></td>
	<td colspan=1><input type='text' name='description' size='20' tabindex=5></td><td>
	<?php $this->getH("PrintImageHref", array('add', 'Add new NAT rule', TRUE, 6)); ?>
	</td></tr></form>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>