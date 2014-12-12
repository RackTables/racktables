<?php if (defined("RS_TPL")) {?>

<?php $js = <<<END
function checkAll(bx) {
	for (var tbls=document.getElementsByTagName("table"), i=tbls.length; i--;)
		if (tbls[i].id == "locationFilter") {
			var bxs=tbls[i].getElementsByTagName("input");
			var in_tree = false;
			for (var j=0; j<bxs.length; j++) {
				if(in_tree == false && bxs[j].value == bx.value)
					in_tree = true;
				else if(parseInt(bxs[j].className) <= parseInt(bx.className))
					in_tree = false;
				if (bxs[j].type=="checkbox" && in_tree == true)
					bxs[j].checked = bx.checked;
			}
		}
}

function collapseAll(bx) {
	for (var tbls=document.getElementsByTagName("table"), i=tbls.length; i--;)
		if (tbls[i].id == "locationFilter") {
			var bxs=tbls[i].getElementsByTagName("div");
			//loop through divs to hide unchecked
			for (var j=0; j<bxs.length; j++) {
				var is_checked = -1;
				var in_div=bxs[j].getElementsByTagName("input");
				//loop through input to find if any is checked
				for (var k=0; k<in_div.length; k++) {
					if(in_div[k].type="checkbox") {
						if (in_div[k].checked == true) {
							is_checked = true;
							break;
						}
						else
							is_checked = false;
					}
				}
				// nothing selected and element id is lfd, collapse it
				if (is_checked == false && !bxs[j].id.indexOf("lfd"))
					expand(bxs[j].id.substr(3));
			}
		}
}

function expand(id) {
	var divid = document.getElementById("lfd" + id);
	var iconid = document.getElementById("lfa" + id);
	if (divid.style.display == 'none') {
		divid.style.display = 'block';
		iconid.innerHTML = ' - ';
	} else {
		divid.style.display = 'none';
		iconid.innerHTML = ' + ';
	}
}
END;

	$this->addRequirement("Header","HeaderJsInline",array("code" => $js));
?>
	<div class=portlet>
		<h2>Location filter</h2>
		<table border=0 align=center cellspacing=0 class="tagtree" id="locationFilter">
    		<form method=post>
    			<input type=hidden name=page value=rackspace>
    			<input type=hidden name=tab value=default>
    			<input type=hidden name=changeLocationFilter value=true>
    			<?php if($this->is("LocationsExist",true)) { ?>
    				<tr><td class=tagbox style='padding-left: 0px'><label>
					<input type=checkbox name='location'  onClick=checkAll(this)> Toggle all
					<img src=?module=chrome&uri=pix/1x1t.gif onLoad=collapseAll(this)>
					</label></td></tr>
					<tr><td class=tagbox><hr>
						<?php $this->Locations; ?>
					<hr></td></tr>
					<tr><td>
						<input class="icon" type="image" border="0" title="set filter" src="?module=chrome&uri=pix/pgadmin3-viewfiltereddata.png" name="submit"></input>
					</td></tr>
    			<?php } ?>
    			<?php if ($this->is("LocationsExist",false)) { ?>
 					<tr><td class='tagbox sparenetwork'>(no locations exist)</td></tr>
					<tr><td>
					<img src="pix/pgadmin3-viewfiltereddata-grayscale.png" width=32 height=32 border=0>
					</td></tr>		
    			<?php } ?>
			</form>
		</table>
	</div>	
<?php } else { ?>
Don't use this page directly, it's supposed <br />
to get loaded within the main page. <br />
Return to the index. <br />
<?php }?>