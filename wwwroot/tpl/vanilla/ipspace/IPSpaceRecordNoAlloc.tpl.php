<?php if (defined("RS_TPL")) {?>
	<tr valign=top>
		<?php $this->IPNetInfo; ?>
		<td>
			<?php $this->IPNetCapacity; ?>
		</td>
		<?php if($this->is('hasRouterCell')) { ?><td></td><?php } ?>
	</tr>
	
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>