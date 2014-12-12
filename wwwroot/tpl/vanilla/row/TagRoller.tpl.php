<?php if (defined("RS_TPL")) {?>
	<?php $this->getH('PrintOpFormIntro',array('rollTags', array ('realsum' => $this->_sum))); ?>
	<table border=1 align=center>
	<tr>
		<td colspan=2>
			This special tool allows assigning tags to physical contents (racks <strong>and all contained objects</strong>) of the current ack row.<br>
			The tag(s) selected below will be appended to already assigned tag(s) of each particular entity. 
		</td>
	</tr>
	<tr>
		<th>Tags</th>
		<td><?php $this->Tags; ?></td>
	</tr>
	<tr>
		<th>Control question: the sum of <?php $this->a; ?> and <?php $this->b; ?></th>
		<td><input type=text name=sum></td>
	</tr>
	<tr>
		<td colspan=2 align=center><input type=submit value='Go!'></td>
	</tr>
	</table>
	</form>
	<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>