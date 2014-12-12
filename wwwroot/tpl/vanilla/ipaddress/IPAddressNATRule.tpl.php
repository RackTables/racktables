<?php if (defined("RS_TPL")) {?>
	<tr>
		<td><?php $this->Proto; ?></td>
		<td><a href="<?php $this->FromLink; ?>"><?php $this->FromIp; ?></a>:<?php $this->FromPort; ?></td>
		<td><a href="<?php $this->ToLink; ?>"><?php $this->ToIp; ?></a>:<?php $this->ToPort; ?></td>
		<td><?php $this->Description; ?></td>
	</tr>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>