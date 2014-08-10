<?php if (defined("RS_TPL")) {?>
	<tr <?php if($this->is('Assignable',false)) { ?>
			<?php if ($this->is('HasChildren',true)) { ?>
				class='trnull'
			<?php } else { ?>
				class='trwarning'
			<?php } ?>
		<?php } ?>
	>
	<td align=left style='padding-left:<?php echo ($this->_Level * 16); ?>px;'>
	<?php if ($this->is('HasChildren',true)) { ?>
		<img width="16" border="0" height="16" src="?module=chrome&uri=pix/node-expanded-static.png"></img>
	<?php } ?>
	<span title="<?php $this->Stats; ?>" class="<?php $this->SpanClass; ?>">
		<?php $this->Tag; ?>
		<?php if (!$this->is('Refc', '')) { ?>
			<i>(<?php $this->Refc; ?>)</i>
		<?php } ?> 
	</span>
	</td>
	</tr>
	<?php $this->TagList; ?>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>