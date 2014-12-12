<?php if (defined("RS_TPL")) {?>
	<?php if ($this->is("ChildrenViolation", true)) { ?>
		<div class=portlet>
			<h2>EntityLink: Missing Children (<?php $this->ChildrenCount ?>)</h2>
			<table cellpadding=5 cellspacing=0 align=center class=cooltable>
			<tr><th>Parent</th><th>Child Type</th><th>Child ID</th></tr>
			<?php $this->startLoop("ChildrenOrphans"); ?>	
				<tr class=row_<?php $this->Order; ?>>
				<td><?php $this->RealmName; ?> : <?php $this->ElemName ?></td>
				<td><?php $this->EntityType ?></td>
				<td><?php $this->EntityId ?></td>
				</tr>
			<?php $this->endLoop(); ?> 
			</table>
		</div>
	<?php } ?> 
	<?php if ($this->is("ParentsViolation", true)) { ?>
		<div class=portlet>
			<h2>EntityLink: Missing Parents (<?php $this->ParentsCount ?>)</h2>
			<table cellpadding=5 cellspacing=0 align=center class=cooltable>
			<tr><th>Child</th><th>Parent Type</th><th>Parent ID</th></tr>
			<?php $this->startLoop("ParentsOrphans"); ?>	
				<tr class=row_<?php $this->order ?>>
				<td><?php $this->elemName ?> : <?php $this->parentName ?></td>
				<td><?php $this->entity_type ?></td>
				<td><?php $this->entity_id ?></td>
				</tr>
			<?php $this->endLoop(); ?> 
			</table>
		</div>
	<?php } ?> 
	<?php if ($this->is("AttrMapViolation", true)) { ?>
		<div class=portlet>
			<h2>AttributeMap: Invalid Mappings (<?php $this->AttrMapCount ?>)</h2>
			<table cellpadding=5 cellspacing=0 align=center class=cooltable>
			<tr><th>Attribute</th><th>Chapter</th><th>Object TypeID</th></tr>
			<?php $this->startLoop("AttrMapOrphans"); ?>	
				<tr class=row_<?php $this->Order ?>>
				<td><?php $this->AttrName ?></td>
				<td><?php $this->ChapterName ?></td>
				<td><?php $this->ObjtypeId ?></td>
				</tr>
			<?php $this->endLoop(); ?> 
			</table>
		</div>
	<?php } ?> 	
	<?php if ($this->is("ObjectViolation", true)) { ?>
		<div class=portlet>
			<h2>Object Container Compatibility rules: Invalid Parent or Child Type (<?php $this->ObjectCount ?>)</h2>
			<table cellpadding=5 cellspacing=0 align=center class=cooltable>
			<tr><th>ID</th><th>Name</th><th>Type ID</th></tr>
			<?php $this->startLoop("AllObjectsOrphans"); ?>	
				<tr class=row_<?php $this->Order ?>>
				<td><?php $this->Id ?></td>
				<td><?php $this->Name ?></td>
				<td><?php $this->ObjtypeId ?></td>
				</tr>
			<?php $this->endLoop(); ?> 
			</table>
		</div>
	<?php } ?> 	
	<?php if ($this->is("ObjectHistViolation", true)) { ?>
		<div class=portlet>
			<h2>ObjectHistory: Invalid Types (<?php $this->ObjectHistCount ?>)</h2>
			<table cellpadding=5 cellspacing=0 align=center class=cooltable>
			<tr><th>ID</th><th>Name</th><th>Type ID</th></tr>
			<?php $this->startLoop("AllObjectHistsOrphans"); ?>	
				<tr class=row_<?php $this->Order ?>>
				<td><?php $this->Id ?></td>
				<td><?php $this->Name ?></td>
				<td><?php $this->ObjtypeId ?></td>
				</tr>
			<?php $this->endLoop(); ?> 
			</table>
		</div>
	<?php } ?> 
	<?php if ($this->is("ObjectParViolation", true)) { ?>
		<div class=portlet>
			<h2>Port Compatibility rules: Invalid From or To Type (<?php $this->ObjectParCount ?>)</h2>
			<table cellpadding=5 cellspacing=0 align=center class=cooltable>
			<tr><th>From</th><th>From Type ID</th><th>To</th><th>To Type ID</th></tr>
			<?php $this->startLoop("AllObjectParsOrphans"); ?>	
				<tr class=row_<?php $this->Order ?>>
				<td><?php $this->ParentName ?></td>
				<td><?php $this->ParentObjtypeId ?></td>
				<td><?php $this->ChildName ?></td>
				<td><?php $this->ChildObjtypeId ?></td>
				</tr>
			<?php $this->endLoop(); ?> 
			</table>
		</div>
	<?php } ?>
	<?php if ($this->is("PortInterViolation", true)) { ?>
		<div class=portlet>
			<h2>Enabled Port Types: Invalid Outer Interface (<?php $this->PortInterCount ?>)</h2>
			<table cellpadding=5 cellspacing=0 align=center class=cooltable>
			<tr><th>Inner Interface</th><th>Outer Interface ID</th></tr>
			<?php $this->startLoop("AllPortIntersOrphans"); ?>	
				<tr class=row_<?php $this->Order ?>>
				<td><?php $this->IifName ?></td>
				<td><?php $this->OifId ?></td>
				</tr>
			<?php $this->endLoop(); ?> 
			</table>
		</div>
	<?php } ?>
	<?php if ($this->is("ObjectParRuleViolation", true)) { ?>
		<div class=portlet>
			<h2>Objects: Violate Object Container Compatibility rules (<?php $this->ObjectParRuleCount ?>)</h2>
			<table cellpadding=5 cellspacing=0 align=center class=cooltable>
			<tr><th>Contained Obj Name</th><th>Contained Obj Type</th><th>Container Obj Name</th><th>Container Obj Type</th></tr>
			<?php $this->startLoop("AllObjectParRulesOrphans"); ?>	
				<tr class=row_<?php $this->Order ?>>
				<td><?php $this->ChildName ?></td>
				<td><?php $this->ChildType ?></td>
				<td><?php $this->ParentName ?></td>
				<td><?php $this->ParentType ?></td>
				</tr>
			<?php $this->endLoop(); ?> 
			</table>
		</div>
	<?php } ?>
	<?php if ($this->is("TagStorageViolation", true)) { ?>
		<div class=portlet>
			<h2>TagStorage: Missing Parents (<?php $this->TagStorageCount ?>)</h2>
			<table cellpadding=5 cellspacing=0 align=center class=cooltable>
			<tr><th>Tag</th><th>Parent Type</th><th>Parent ID</th></tr>
			<?php $this->startLoop("AllTagStoragesOrphans"); ?>	
				<tr class=row_<?php $this->Order ?>>
				<td><?php $this->Tag ?></td>
				<td><?php $this->RealmName ?></td>
				<td><?php $this->EntityId ?></td>
				</tr>
			<?php $this->endLoop(); ?> 
			</table>
		</div>
	<?php } ?>
	<?php if ($this->is("FileLinkViolation", true)) { ?>
		<div class=portlet>
			<h2>FileLink: Missing Parents (<?php $this->FileLinkCount ?>)</h2>
			<table cellpadding=5 cellspacing=0 align=center class=cooltable>
			<tr><th>File</th><th>Parent Type</th><th>Parent ID</th></tr>
			<?php $this->startLoop("AllFileLinksOrphans"); ?>	
				<tr class=row_<?php $this->Order ?>>
				<td><?php $this->Name ?></td>
				<td><?php $this->RealmName ?></td>
				<td><?php $this->EntityId ?></td>
				</tr>
			<?php $this->endLoop(); ?> 
			</table>
		</div>
	<?php } ?>
	<?php if ($this->is("MissingTriggers", true)) { ?>
		<div class=portlet>
			<h2>Missing Triggers (<?php $this->MissingTriggers ?>)</h2>
			<table cellpadding=5 cellspacing=0 align=center class=cooltable>
			<tr><th>Table</th><th>Trigger</th></tr>
			<?php $this->startLoop("AllTriggers"); ?>	
				<tr class=row_<?php $this->Order ?>>
				<td><?php $this->Table ?></td>
				<td><?php $this->Trigger ?></td>
				</tr>
			<?php $this->endLoop(); ?> 
			</table>
		</div>
	<?php } ?>
	<?php if ($this->is("MissingKeys", true)) { ?>
		<div class=portlet>
			<h2>Missing Foreign Keys (<?php $this->MissingKeys ?>)</h2>
			<table cellpadding=5 cellspacing=0 align=center class=cooltable>
			<tr><th>Table</th><th>Key</th></tr>
			<?php $this->startLoop("AllKeys"); ?>	
				<tr class=row_<?php $this->Order ?>>
				<td><?php $this->Table ?></td>
				<td><?php $this->FKey ?></td>
				</tr>
			<?php $this->endLoop(); ?> 
			</table>
		</div>
	<?php } ?>
	<?php if ($this->is("Invalids", true)) { ?>
		<div class=portlet>
			<h2>Locations: Tree Contains Circular References (<?php $this->Invalids ?>)</h2>
			<table cellpadding=5 cellspacing=0 align=center class=cooltable>
			<tr><th>Child ID</th><th>Child Location</th><th>Parent ID</th><th>Parent Location</th></tr>
			<?php $this->startLoop("AllInvalids"); ?>	
				<tr class=row_<?php $this->Order ?>>
				<td><?php $this->Id ?></td>
				<td><?php $this->Name ?></td>
				<td><?php $this->Parent_Id ?></td>
				<td><?php $this->Parent_Name ?></td>
				</tr>
			<?php $this->endLoop(); ?> 
			</table>
		</div>
	<?php } ?>
	<?php if ($this->is("InvalidObjs", true)) { ?>
		<div class=portlet>
			<h2>Locations: Tree Contains Circular References (<?php $this->InvalidObjs ?>)</h2>
			<table cellpadding=5 cellspacing=0 align=center class=cooltable>
			<tr><th>Contained ID</th><th>Contained Object</th><th>Container ID</th><th>Container Object</th></tr>
			<?php $this->startLoop("AllInvalidObjs"); ?>	
				<tr class=row_<?php $this->Order ?>>
				<td><?php $this->Id ?></td>
				<td><?php $this->Name ?></td>
				<td><?php $this->Parent_Id ?></td>
				<td><?php $this->Parent_Name ?></td>
				</tr>
			<?php $this->endLoop(); ?> 
			</table>
		</div>
	<?php } ?>
	<?php if ($this->is("InvalidTags", true)) { ?>
		<div class=portlet>
			<h2>Locations: Tree Contains Circular References (<?php $this->InvalidTags ?>)</h2>
			<table cellpadding=5 cellspacing=0 align=center class=cooltable>
			<tr><th>Child ID</th><th>Child Tag</th><th>Parent ID</th><th>Parent Tag</th></tr>
			<?php $this->startLoop("AllInvalidTags"); ?>	
				<tr class=row_<?php $this->Order ?>>
				<td><?php $this->Id ?></td>
				<td><?php $this->Tag ?></td>
				<td><?php $this->Parent_Id ?></td>
				<td><?php $this->Parent_Tag ?></td>
				</tr>
			<?php $this->endLoop(); ?> 
			</table>
		</div>
	<?php } ?>
	
	<?php if ($this->is("NoViolations",true)) { ?>
		<h2>No integrity violations found</h2>
	<?php } ?> 

<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>