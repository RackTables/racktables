<?php if (defined("RS_TPL")) {?>
	<table border=0 class=objectview>
		<tr>
			<td class=pcleft>
				<?php $this->EmptyResults; ?>
				<?php if($this->is('hasResults')) { ?>
				<div class=portlet>
					<h2>networks (<?php $this->NetCount; ?>)</h2>
					<h4>
						<?php 	if($this->is('CollapseExpandOptions','allnone')) { ?>
							auto-collapsing at threshold <?php $this->TreeThreshold; ?> (<!-- All --><a href='<?php $this->ExpandAll; ?>'>expand&nbsp;all</a><!-- EndAll --> / 
								<!-- None --><a href='<?php $this->CollapseAll; ?>'>collapse&nbsp;all</a><!-- EndNone -->)
						<?php 	} 
						 		elseif($this->is('CollapseExpandOptions','all')) { ?>
							expanding all (<!-- Auto --><a href='<?php $this->CollapseAuto; ?>'>auto-collapse</a><!-- EndAuto --> / 
								<!-- None --><a href='<?php $this->CollapseAll; ?>'>collapse&nbsp;all</a><!-- EndNone -->)
						<?php 	} 
								elseif($this->is('CollapseExpandOptions','none')) { ?>
							collapsing all (
								<!-- All --><a href='<?php $this->ExpandAll; ?>'>expand&nbsp;all</a><!-- EndAll --> / 
								<!-- Auto --><a href='<?php $this->CollapseAuto; ?>'>auto-collapse</a><!-- EndAuto -->)
						<?php 	} else { ?>
							expanding <?php $this->ExpandIP; ?>/<?php $this->ExpandMask; ?> (<!-- Auto --><a href='<?php $this->CollapseAuto; ?>'>auto-collapse</a><!-- EndAuto --> / 
								<!-- All --><a href='<?php $this->ExpandAll; ?>'>expand&nbsp;all</a><!-- EndAll --> / 
								<!-- None --><a href='<?php $this->CollapseAll; ?>'>collapse&nbsp;all</a><!-- EndNone -->)
						<?php 	} ?>
					</h4>
					<table class='widetable' border=0 cellpadding=5 cellspacing=0 align='center'>
						<tr><th>prefix</th><th>name/tags</th><th>capacity</th>
						<?php if($this->is('AddRouted')) { ?>
							<th>routed by</th>
						<?php } ?>
						</tr>
						<?php $this->IPRecords; ?>
					</table>
				</div>
				<?php } ?>
			</td>
			<td class=pcright>
				<?php $this->CellFilter; ?>
			</td>
		</tr>
	</table>
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>