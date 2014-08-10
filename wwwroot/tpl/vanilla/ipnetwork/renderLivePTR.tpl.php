<?php if (defined("RS_TPL")) {
	$this->addJS('js/racktables.js'); ?>
	<center><h1><?php $this->IP; ?>/<?php $this->Mask; ?></h1><h2><?php $this->Name; ?></h2></center>
	<table class=objview border=0 width='100%'><tr><td class=pcleft>
			<div class=portlet>
				<h2>current records</h2>
				<center>
					<?php if($this->is('Paged')) { ?>
						<h3><?php $this->StartIP; ?> ~ <?php $this->EndIP; ?></h3>
					<?php $this->startLoop('Pages'); ?>
						<?php $this->B; ?><a href='<?php $this->Link(); ?>'>$i</a><?php $this->BEnd; ?>
					<?php $this->endLoop(); ?>
					<?php } ?>
				</center>
				<?php if ($this->is('IsImport')) { ?>
					<?php $this->getH('PrintOpFormIntro',array('importPTRData', array ('addrcount' => $this->_AddrCount))); ?>
				<?php } ?>
				<table class='widetable' border=0 cellspacing=0 cellpadding=5 align='center'>
					<tr><th>address</th><th>current name</th><th>DNS data</th>
					<?php if ($this->is('IsImport')) { ?>
						<th>import</th>
					<?php } ?></tr>
					<?php while($this->loop('IPList')) { ?>
						<?php if ($this->is('IsImport')) { ?>
							<input type=hidden name=addr_<?php $this->IDx; ?> value=<?php $this->StrAddr; ?>>
							<input type=hidden name=descr_<?php $this->IDx; ?> value=<?php $this->PtrName; ?>>
							<input type=hidden name=rsvd_<?php $this->IDx; ?> value=<?php $this->Reserved; ?>>
						<?php } ?>
						<tr class='<?php $this->CSSClass; ?>'>
							<td class='tdleft <?php $this->CSSTDClass; ?>'><?php $this->Link; ?></td>
							<td class=tdleft><?php $this->Name; ?></td>
							<td class=tdleft><?php $this->PtrName; ?></td>
							<?php if ($this->is('IsImport')) { ?>
								<td>
									<?php if($this->is('BoxCounter')) { ?>
										<input type=checkbox name=import_<?php $this->IDx; ?> tabindex=<?php $this->IDx; ?> id=atom_1_<?php $this->BoxCounter; ?>_1>
									<?php } else { ?>&nbsp;<?php } ?>
								</td>
							<?php } ?>
						</tr>
					<?php } ?>
					<?php if ($this->is('IsImport') && $this->is('BoxCounter') > 1) { ?>
						<tr><td colspan=3 align=center><input type=submit value='Import selected records'></td>
						<td>
							<?php if ($this->is('BoxCounterJS')) { ?>
								<a href='javascript:;' onclick="toggleColumnOfAtoms(1, 1, <?php $this->BoxCounter; ?>)">(toggle selection)</a>
							<?php } else { ?>&nbsp;<?php } ?>
						</td></tr>
					<?php } ?>
				</table>
				<?php if ($this->is('IsImport')) { ?>
					</form>
				<?php } ?>
			</div>
		</td>
		<td class=pcright>
			<div class=portlet>
				<h2>stats</h2>
				<table border=0 width='100%' cellspacing=0 cellpadding=2>
					<tr class=trok><th class=tdright>Exact matches:</th><td class=tdleft><?php $this->Match; ?></td></tr>
					<tr class=trwarning><th class=tdright>Missing from DB/DNS:</th><td class=tdleft><?php $this->Missing; ?></td></tr>
					<?php if($this->is('Mismatch')) { ?>
						<tr class=trerror><th class=tdright>Mismatches:</th><td class=tdleft><?php $this->MisMatch; ?></td></tr>
					<?php } ?>
				</table>
			</div>
		</td>
	</tr>
	</table>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>