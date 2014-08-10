<?php if (defined("RS_TPL")) {?>
			<tr class='<?php $this->get("TrClass"); ?>'>
				<td class='<?php $this->get("TdClass"); ?>' style='padding-left: <?php $this->get("LevelPx"); ?>px;'>
					<label>
						<input type=checkbox class='<?php $this->get("InputClass"); ?>' name='<?php $this->get("InputName"); ?>[]' value='<?php $this->get("InputValue"); ?>'<?php $this->get("ExtraAttrs"); ?>>
						<span class="<?php $this->TagClass; ?>"><?php $this->get("TagName"); ?></span>
						<?php if ($this->is("RefCnt")) { ?>
							<i>(<?php $this->get("RefCnt"); ?>)</i>
						<?php } ?> 
					</label>
				</td>
			</tr>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>