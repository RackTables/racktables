<?php

# This file is a part of RackTables, a datacenter and server room management
# framework. See accompanying file "COPYING" for the full copyright and
# licensing information.

// Find direct sub-pages and dump as a list.
function renderConfigMainpage ()
{
	global $pageno, $page;
	echo '<ul>';
	foreach ($page as $cpageno => $cpage)
		if (isset ($cpage['parent']) && $cpage['parent'] == $pageno && permitted ($cpageno))
			echo '<li>' . mkA (getTitle ($cpageno), $cpageno) . "</li>\n";
	echo '</ul>';
}

function renderUserList ()
{
	renderCellList ('user', 'User accounts');
}

function renderUserListEditor ()
{
	function printNewItemTR ()
	{
		startPortlet ('Add new');
		printOpFormIntro ('createUser');
		echo '<table cellspacing=0 cellpadding=5 align=center>';
		echo '<tr><th>&nbsp;</th><th>&nbsp;</th><th>Tags</th></tr>';
		echo '<tr><th class=tdright>Username</th><td class=tdleft><input type=text size=64 name=username></td>';
		echo '<tr><th class=tdright>Real name</th><td class=tdleft><input type=text size=64 name=realname></td></tr>';
		echo '<tr><th class=tdright>Password</th><td class=tdleft><input type=password size=64 name=password></td></tr>';
		echo '<tr><th class=tdright>Tags</th><td class=tdleft>';
		printTagsPicker ();
		echo '</td></tr>';
		echo '<tr><td colspan=2>';
		printImageHREF ('CREATE', 'Add new account', TRUE);
		echo '</td></tr>';
		echo '</table></form>';
		finishPortlet();
	}
	if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
		printNewItemTR();
	$accounts = listCells ('user');
	startPortlet ('Manage existing (' . count ($accounts) . ')');
	echo '<table cellspacing=0 cellpadding=5 align=center class=widetable>';
	echo '<tr><th>Username</th><th>Real name</th><th>New password (use old if blank)</th><th>&nbsp;</th></tr>';
	foreach ($accounts as $account)
	{
		printOpFormIntro ('updateUser', array ('user_id' => $account['user_id']));
		echo "<tr><td><input type=text name=username value='${account['user_name']}' size=16></td>";
		echo "<td><input type=text name=realname value='${account['user_realname']}' size=24></td>";
		echo "<td><input type=password name=password size=40></td><td>";
		printImageHREF ('save', 'Save changes', TRUE);
		echo '</td></form></tr>';
	}
	echo '</table><br>';
	finishPortlet();
	if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
		printNewItemTR();
}

function renderUser ($user_id)
{
	$userinfo = spotEntity ('user', $user_id);

	$summary = array();
	$summary['Account name'] = $userinfo['user_name'];
	$summary['Real name'] = $userinfo['user_realname'];
	$summary['tags'] = '';
	renderEntitySummary ($userinfo, 'summary', $summary);

	renderFilesPortlet ('user', $user_id);
}

function renderUserProperties ($user_id)
{
	printOpFormIntro ('edit');
	echo '<table border=0 align=center>';
	echo "<tr><th class=tdright>Tags:</th><td class=tdleft>";
	printTagsPicker ();
	echo "</td></tr>\n";
	echo "<tr><th class=submit colspan=2>";
	printImageHREF ('SAVE', 'Save changes', TRUE);
	echo '</th></tr></table></form>';
}

function renderRackCodeViewer ()
{
	$text = loadScript ('RackCode');
	echo '<table width="100%" border=0>';
	$lineno = 1;
	foreach (explode ("\n", $text) as $line)
	{
		echo "<tr><td class=tdright><a name=line${lineno}>${lineno}</a></td>";
		echo "<td class=tdleft>${line}</td></tr>";
		$lineno++;
	}
}

function renderRackCodeEditor ()
{
	addJS ('js/codemirror/codemirror.js');
	addJS ('js/codemirror/rackcode.js');
	addCSS ('js/codemirror/codemirror.css');
	addJS (<<<ENDJAVASCRIPT
function verify()
{
	$.ajax({
		type: "POST",
		url: "index.php",
		data: {'module': 'ajax', 'ac': 'verifyCode', 'code': $("#RCTA").text()},
		success: function (data)
		{
			arr = data.split("\\n");
			if (arr[0] == "ACK")
			{
				$("#SaveChanges")[0].disabled = "";
				$("#ShowMessage")[0].innerHTML = "Code verification OK, don't forget to save the code";
				$("#ShowMessage")[0].className = "msg_success";
			}
			else
			{
				$("#SaveChanges")[0].disabled = "disabled";
				$("#ShowMessage")[0].innerHTML = arr[1];
				$("#ShowMessage")[0].className = "msg_warning";
			}
		}
	});
}

$(document).ready(function() {
	$("#SaveChanges")[0].disabled = "disabled";
	$("#ShowMessage")[0].innerHTML = "";
	$("#ShowMessage")[0].className = "";

	var rackCodeMirror = CodeMirror.fromTextArea(document.getElementById("RCTA"),{
		mode:'rackcode',
		lineNumbers:true });
	rackCodeMirror.on("change",function(cm,cmChangeObject){
		$("#RCTA").text(cm.getValue());
    });
});
ENDJAVASCRIPT
	, TRUE);

	$text = loadScript ('RackCode');
	printOpFormIntro ('saveRackCode');
	echo '<table style="width:100%;border:1px;" border=0 align=center>';
	echo "<tr><td><textarea rows=40 cols=100 name=rackcode id=RCTA class='codepress rackcode'>";
	echo $text . "</textarea></td></tr>\n";
	echo "<tr><td align=center>";
	echo '<div id="ShowMessage"></div>';
	echo "<input type='button' value='Verify' onclick='verify();'>";
	echo "<input type='submit' value='Save' disabled='disabled' id='SaveChanges' onclick='$(RCTA).toggleEditor();'>";
	echo "</td></tr>";
	echo '</table>';
	echo "</form>";
}

function renderObjectParentCompatViewer()
{
	global $nextorder;
	$order = 'odd';
	$last_left_parent_id = NULL;
	echo '<br><table class=cooltable border=0 cellpadding=5 cellspacing=0 align=center>';
	echo '<tr><th>Parent</th><th>Child</th></tr>';
	foreach (getObjectParentCompat() as $pair)
	{
		if ($last_left_parent_id != $pair['parent_objtype_id'])
		{
			$order = $nextorder[$order];
			$last_left_parent_id = $pair['parent_objtype_id'];
		}
		echo "<tr class=row_${order}><td>${pair['parent_name']}</td><td>${pair['child_name']}</td></tr>\n";
	}
	echo '</table>';
}

function renderObjectParentCompatEditor()
{
	function printNewitemTR()
	{
		printOpFormIntro ('add');
		echo '<tr><th class=tdleft>';
		printImageHREF ('add', 'add pair', TRUE);
		echo '</th><th class=tdleft>';
		$chapter = readChapter (CHAP_OBJTYPE);
		// remove rack, row, location
		unset ($chapter['1560'], $chapter['1561'], $chapter['1562']);
		printSelect ($chapter, array ('name' => 'parent_objtype_id'));
		echo '</th><th class=tdleft>';
		printSelect ($chapter, array ('name' => 'child_objtype_id'));
		echo "</th></tr></form>\n";
	}

	global $nextorder;
	$last_left_parent_id = NULL;
	$order = 'odd';
	echo '<br><table class=cooltable align=center border=0 cellpadding=5 cellspacing=0>';
	echo '<tr><th>&nbsp;</th><th>Parent</th><th>Child</th></tr>';
	if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
		printNewitemTR();
	foreach (getObjectParentCompat() as $pair)
	{
		if ($last_left_parent_id != $pair['parent_objtype_id'])
		{
			$order = $nextorder[$order];
			$last_left_parent_id = $pair['parent_objtype_id'];
		}
		echo "<tr class=row_${order}><td>";
		if ($pair['count'] > 0)
			printImageHREF ('nodelete', $pair['count'] . ' relationship(s) stored');
		else
			echo getOpLink (array ('op' => 'del', 'parent_objtype_id' => $pair['parent_objtype_id'], 'child_objtype_id' => $pair['child_objtype_id']), '', 'delete', 'remove pair');
		echo "</td><td class=tdleft>${pair['parent_name']}</td><td class=tdleft>${pair['child_name']}</td></tr>\n";
	}
	if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
		printNewitemTR();
	echo '</table>';
}

function renderOIFCompatViewer()
{
	global $nextorder;
	$order = 'odd';
	$last_left_oif_id = NULL;
	echo '<br><table class=cooltable border=0 cellpadding=5 cellspacing=0 align=center>';
	echo '<tr><th>From interface</th><th>To interface</th></tr>';
	foreach (getPortOIFCompat() as $pair)
	{
		if ($last_left_oif_id != $pair['type1'])
		{
			$order = $nextorder[$order];
			$last_left_oif_id = $pair['type1'];
		}
		echo "<tr class=row_${order}><td>${pair['type1name']}</td><td>${pair['type2name']}</td></tr>";
	}
	echo '</table>';
}

function renderOIFCompatEditor()
{
	function printNewitemTR()
	{
		printOpFormIntro ('add');
		echo '<tr><th class=tdleft>';
		printImageHREF ('add', 'add pair', TRUE);
		echo '</th><th class=tdleft>';
		printSelect (getPortOIFOptions(), array ('name' => 'type1'));
		echo '</th><th class=tdleft>';
		printSelect (getPortOIFOptions(), array ('name' => 'type2'));
		echo '</th></tr></form>';
	}

	global $nextorder, $wdm_packs;

	startPortlet ('WDM wideband receivers');
	echo '<table border=0 align=center cellspacing=0 cellpadding=5 class=zebra>';
	echo '<tr><th>&nbsp;</th><th>enable</th><th>disable</th></tr>';
	foreach ($wdm_packs as $codename => $packinfo)
	{
		echo '<tr><td class=tdleft>' . $packinfo['title'] . '</td><td>';
		echo getOpLink (array ('op' => 'addPack', 'standard' => $codename), '', 'add');
		echo '</td><td>';
		echo getOpLink (array ('op' => 'delPack', 'standard' => $codename), '', 'delete');
		echo '</td></tr>';
	}
	echo '</table>';
	finishPortlet();

	startPortlet ('interface by interface');
	$last_left_oif_id = NULL;
	echo '<br><table class=cooltable align=center border=0 cellpadding=5 cellspacing=0>';
	echo '<tr><th>&nbsp;</th><th>From Interface</th><th>To Interface</th></tr>';
	if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
		printNewitemTR();
	$order = 'odd';
	foreach (getPortOIFCompat() as $pair)
	{
		if ($last_left_oif_id != $pair['type1'])
		{
			$order = $nextorder[$order];
			$last_left_oif_id = $pair['type1'];
		}
		echo "<tr class=row_${order}><td>";
		echo getOpLink (array ('op' => 'del', 'type1' => $pair['type1'], 'type2' => $pair['type2']), '', 'delete', 'remove pair');
		echo "</td><td class=tdleft>${pair['type1name']}</td><td class=tdleft>${pair['type2name']}</td></tr>";
	}
	if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
		printNewitemTR();
	echo '</table>';
	finishPortlet();
}

function renderIIFOIFCompat()
{
	echo '<br>';
	renderTwoColumnCompatTableViewer
	(
		getPortInterfaceCompat(),
		array
		(
			'header' => 'Inner interface',
			'key' => 'iif_id',
			'value' => 'iif_name',
			'width' => 16,
		),
		array
		(
			'header' => 'Outer interface',
			'key' => 'oif_id',
			'value' => 'oif_name',
			'width' => 48,
		)
	);
	echo '<br>';
}

function renderIIFOIFCompatEditor()
{
	startPortlet ('WDM standard by interface');
	$iif = getPortIIFOptions();
	global $nextorder, $wdm_packs;
	$order = 'odd';
	echo '<table border=0 align=center cellspacing=0 cellpadding=5>';
	foreach ($wdm_packs as $codename => $packinfo)
	{
		echo "<tr><th>&nbsp;</th><th colspan=2>${packinfo['title']}</th></tr>";
		foreach ($packinfo['iif_ids'] as $iif_id)
		{
			echo "<tr class=row_${order}><th class=tdleft>" . $iif[$iif_id] . '</th><td>';
			echo getOpLink (array ('op' => 'addPack', 'standard' => $codename, 'iif_id' => $iif_id), '', 'add');
			echo '</td><td>';
			echo getOpLink (array ('op' => 'delPack', 'standard' => $codename, 'iif_id' => $iif_id), '', 'delete');
			echo '</td></tr>';
			$order = $nextorder[$order];
		}
	}
	echo '</table>';
	finishPortlet();

	startPortlet ('interface by interface');
	renderTwoColumnCompatTableEditor
	(
		getPortInterfaceCompat(),
		array
		(
			'header' => 'inner interface',
			'key' => 'iif_id',
			'value' => 'iif_name',
			'width' => 16,
			'options' => getPortIIFOptions(),
		),
		array
		(
			'header' => 'outer interface',
			'key' => 'oif_id',
			'value' => 'oif_name',
			'width' => 48,
			'options' => getPortOIFOptions()
		)
	);
	finishPortlet();
}

function renderPortOIFViewer()
{
	echo '<br><table class="cooltable zebra" align=center border=0 cellpadding=5 cellspacing=0>';
	echo '<tr><th>Origin</th><th>Key</th><th>Refcnt</th><th>Outer Interface</th></tr>';
	$refcnt = getPortOIFRefc();
	foreach (getPortOIFOptions() as $oif_id => $oif_name)
	{
		echo '<tr>';
		echo '<td class=tdleft>' . getImageHREF ($oif_id < 2000 ? 'computer' : 'favorite') . '</td>';
		echo "<td class=tdright>${oif_id}</td>";
		echo '<td class=tdright>' . ($refcnt[$oif_id] ? $refcnt[$oif_id] : '&nbsp;') . '</td>';
		echo '<td class=tdleft>' . stringForTD ($oif_name, 48) . '</td>';
		echo '</tr>';
	}
	echo '</table>';
}

function renderPortOIFEditor()
{
	function printNewitemTR()
	{
		printOpFormIntro ('add');
		echo '<tr>';
		echo '<td>&nbsp;</td>';
		echo '<td>&nbsp;</td>';
		echo '<td>&nbsp;</td>';
		echo '<td class=tdleft>' . getImageHREF ('create', 'create new', TRUE) . '</td>';
		echo '<td class=tdleft><input type=text size=48 name=oif_name></td>';
		echo '<td class=tdleft>' . getImageHREF ('create', 'create new', TRUE) . '</td>';
		echo '</tr></form>';
	}
	echo '<table class=widetable border=0 cellpadding=5 cellspacing=0 align=center>';
	echo '<tr><th class=tdleft>Origin</th><th>Key</th><th>Refcnt</th><th>&nbsp;</th><th>Outer Interface</th><th>&nbsp;</th></tr>';
	if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
		printNewitemTR();
	$refcnt = getPortOIFRefc();
	foreach (getPortOIFOptions() as $oif_id => $oif_name)
	{
		echo '<tr>';
		if ($oif_id < 2000)
		{
			echo '<td class=tdleft>' . getImageHREF ('computer') . '</td>';
			echo "<td class=tdleft>${oif_id}</td>";
			echo '<td class=tdright>' . ($refcnt[$oif_id] ? $refcnt[$oif_id] : '&nbsp;') . '</td>';
			echo '<td>&nbsp;</td>';
			echo '<td class=tdleft>' . stringForTD ($oif_name, 48) . '</td>';
			echo '<td>&nbsp;</td>';
		}
		else
		{
			printOpFormIntro ('upd', array ('id' => $oif_id));
			echo '<td class=tdleft>' . getImageHREF ('favorite') . '</td>';
			echo "<td class=tdleft>${oif_id}</td>";
			if ($refcnt[$oif_id])
			{
				echo "<td class=tdright>${refcnt[$oif_id]}</td>";
				echo '<td class=tdleft>' . getImageHREF ('nodestroy', 'cannot remove') . '</td>';
			}
			else
			{
				echo '<td>&nbsp;</td>';
				echo '<td class=tdleft>';
				echo getOpLink (array ('op' => 'del', 'id' => $oif_id), '', 'destroy', 'remove');
				echo '</td>';
			}
			echo '<td class=tdleft><input type=text size=48 name=oif_name value="' . stringForTextInputValue ($oif_name, 48) . '"></td>';
			echo '<td>' . getImageHREF ('save', 'Save changes', TRUE) . '</td>';
			echo '</form>';
		}
		echo '</tr>';
	}
	if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
		printNewitemTR();
	echo '</table>';
}

function renderAttributes ()
{
	global $attrtypes;
	startPortlet ('Optional attributes');
	echo '<table class="cooltable zebra" border=0 cellpadding=5 cellspacing=0 align=center>';
	echo "<tr><th class=tdleft>Attribute name</th><th class=tdleft>Attribute type</th><th class=tdleft>Applies to</th></tr>";
	foreach (getAttrMap() as $attr)
	{
		echo '<tr>';
		echo "<td class=tdleft>${attr['name']}</td>";
		echo "<td class=tdleft>" . $attrtypes[$attr['type']] . "</td>";
		echo '<td class=tdleft>';
		if (count ($attr['application']) == 0)
			echo '&nbsp;';
		else
			foreach ($attr['application'] as $app)
				if ($attr['type'] == 'dict')
					echo decodeObjectType ($app['objtype_id']) . " (values from '${app['chapter_name']}')<br>";
				else
					echo decodeObjectType ($app['objtype_id']) . '<br>';
		echo '</td></tr>';
	}
	echo "</table><br>\n";
	finishPortlet();
}

function renderEditAttributesForm ()
{
	function printNewItemTR ()
	{
		printOpFormIntro ('add');
		echo '<tr><td>';
		printImageHREF ('create', 'Create attribute', TRUE);
		echo "</td><td><input type=text name=attr_name></td><td>";
		global $attrtypes;
		printSelect ($attrtypes, array ('name' => 'attr_type'));
		echo '</td><td>';
		printImageHREF ('create', 'Create attribute', TRUE);
		echo '</td></tr></form>';
	}
	startPortlet ('Optional attributes');
	echo "<table cellspacing=0 cellpadding=5 align=center class=widetable>\n";
	echo '<tr><th>&nbsp;</th><th>Name</th><th>Type</th><th>&nbsp;</th></tr>';
	if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
		printNewItemTR();
	foreach (getAttrMap() as $attr)
	{
		printOpFormIntro ('upd', array ('attr_id' => $attr['id']));
		echo '<tr><td>';
		if ($attr['id'] < 10000)
			printImageHREF ('nodestroy', 'system attribute');
		elseif (count ($attr['application']))
			printImageHREF ('nodestroy', count ($attr['application']) . ' reference(s) in attribute map');
		else
			echo getOpLink (array('op'=>'del', 'attr_id'=>$attr['id']), '', 'destroy', 'Remove attribute');
		echo "</td><td><input type=text name=attr_name value='${attr['name']}'></td>";
		echo "<td class=tdleft>${attr['type']}</td><td>";
		printImageHREF ('save', 'Save changes', TRUE);
		echo '</td></tr>';
		echo '</form>';
	}
	if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
		printNewItemTR();
	echo "</table>\n";
	finishPortlet();
}

function getAttributeOptions ($attrMap)
{
	$ret = array();
	$shortType = array
	(
		'uint' => 'U',
		'float' => 'F',
		'string' => 'S',
		'dict' => 'D',
		'date' => 'T',
	);
	foreach ($attrMap as $attr)
		$ret[$attr['id']] = sprintf ('[%s] %s', $shortType[$attr['type']], $attr['name']);
	return $ret;
}

function renderEditAttrMapForm ()
{
	function printNewItemTR ($aselect)
	{
		printOpFormIntro ('add');
		echo '<tr>';
		echo "<td colspan=2 class=tdleft>${aselect}</td>";
		echo '<td class=tdleft>';
		printImageHREF ('add', '', TRUE);
		echo ' ';
		$objtypes = readChapter (CHAP_OBJTYPE, 'o');
		printNiftySelect (cookOptgroups ($objtypes), array ('name' => 'objtype_id'));
		$choptions = array (0 => '-- dictionary chapter for [D] attributes --');
		foreach (getChapterList() as $chapter)
			if ($chapter['sticky'] != 'yes')
				$choptions[$chapter['id']] = $chapter['name'];
		echo ' ' . getSelect ($choptions, array ('name' => 'chapter_no'));
		echo '</td></tr></form>';
	}
	global $attrtypes, $nextorder;
	$order = 'odd';
	$attrMap = getAttrMap();
	$aselect = getSelect (getAttributeOptions ($attrMap), array ('name' => 'attr_id'));
	startPortlet ('Attribute map');
	echo "<table class=cooltable border=0 cellpadding=5 cellspacing=0 align=center>";
	echo '<tr><th class=tdleft>Attribute name</th><th class=tdleft>Attribute type</th><th class=tdleft>Applies to</th></tr>';
	if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
		printNewItemTR ($aselect);
	foreach ($attrMap as $attr)
	{
		if (!count ($attr['application']))
			continue;
		echo "<tr class=row_${order}><td class=tdleft>${attr['name']}</td>";
		echo "<td class=tdleft>" . $attrtypes[$attr['type']] . "</td><td colspan=2 class=tdleft>";
		foreach ($attr['application'] as $app)
		{
			if ($app['sticky'] == 'yes')
				printImageHREF ('nodelete', 'system mapping');
			elseif ($app['refcnt'])
				printImageHREF ('nodelete', $app['refcnt'] . ' value(s) stored for objects');
			else
				echo getOpLink (array('op'=>'del', 'attr_id'=>$attr['id'], 'objtype_id'=>$app['objtype_id']), '', 'delete', 'Remove mapping');
			echo ' ';
			if ($attr['type'] == 'dict')
				echo decodeObjectType ($app['objtype_id']) . " (values from '${app['chapter_name']}')<br>";
			else
				echo decodeObjectType ($app['objtype_id']) . '<br>';
		}
		echo "</td></tr>";
		$order = $nextorder[$order];
	}
	if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
		printNewItemTR ($aselect);
	echo "</table>\n";
	finishPortlet();
}

function renderDictionary ()
{
	echo '<ul>';
	foreach (getChapterList() as $chapter_no => $chapter)
		echo '<li>' . mkA ($chapter['name'], 'chapter', $chapter_no) . " (${chapter['wordc']} records)</li>";
	echo '</ul>';
}

// We don't allow to rename/delete a sticky chapter and we don't allow
// to delete a non-empty chapter.
function renderChaptersEditor ()
{
	function printNewItemTR ()
	{
		printOpFormIntro ('add');
		echo '<tr><td>';
		printImageHREF ('create', 'Add new', TRUE);
		echo "</td><td><input type=text name=chapter_name></td><td>&nbsp;</td><td>";
		printImageHREF ('create', 'Add new', TRUE);
		echo '</td></tr></form>';
	}
	$dict = getChapterList();
	foreach (array_keys ($dict) as $chapter_no)
		$dict[$chapter_no]['mapped'] = FALSE;
	foreach (getAttrMap() as $attrinfo)
		if ($attrinfo['type'] == 'dict')
			foreach ($attrinfo['application'] as $app)
				$dict[$app['chapter_no']]['mapped'] = TRUE;
	echo "<table cellspacing=0 cellpadding=5 align=center class=widetable>\n";
	echo '<tr><th>&nbsp;</th><th>Chapter name</th><th>Words</th><th>&nbsp;</th></tr>';
	if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
		printNewItemTR();
	foreach ($dict as $chapter_id => $chapter)
	{
		$wordcount = $chapter['wordc'];
		$sticky = $chapter['sticky'] == 'yes';
		printOpFormIntro ('upd', array ('chapter_no' => $chapter_id));
		echo '<tr>';
		echo '<td>';
		if ($sticky)
			printImageHREF ('nodestroy', 'system chapter');
		elseif ($wordcount > 0)
			printImageHREF ('nodestroy', 'contains ' . $wordcount . ' word(s)');
		elseif ($chapter['mapped'])
			printImageHREF ('nodestroy', 'used in attribute map');
		else
			echo getOpLink (array('op'=>'del', 'chapter_no'=>$chapter_id), '', 'destroy', 'Remove chapter');
		echo '</td>';
		echo "<td><input type=text name=chapter_name value='${chapter['name']}'" . ($sticky ? ' disabled' : '') . "></td>";
		echo "<td class=tdleft>${wordcount}</td><td>";
		if ($sticky)
			echo '&nbsp;';
		else
			printImageHREF ('save', 'Save changes', TRUE);
		echo '</td></tr>';
		echo '</form>';
	}
	if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
		printNewItemTR();
	echo "</table>\n";
}

function renderChapter ($tgt_chapter_no)
{
	global $nextorder;
	$words = readChapter ($tgt_chapter_no, 'a');
	$wc = count ($words);
	if (!$wc)
	{
		echo "<center><h2>(no records)</h2></center>";
		return;
	}
	$refcnt = getChapterRefc ($tgt_chapter_no, array_keys ($words));
	$attrs = getChapterAttributes($tgt_chapter_no);
	echo "<br><table class=cooltable border=0 cellpadding=5 cellspacing=0 align=center>\n";
	echo "<tr><th colspan=4>${wc} record(s)</th></tr>\n";
	echo "<tr><th>Origin</th><th>Key</th><th>Refcnt</th><th>Word</th></tr>\n";
	$order = 'odd';
	foreach ($words as $key => $value)
	{
		echo "<tr class=row_${order}><td>";
		printImageHREF ($key < 50000 ? 'computer' : 'favorite');
		echo "</td><td class=tdright>${key}</td><td class=tdright>";
		if ($refcnt[$key])
		{
			// For the ObjectType chapter the extra filter is as simple as "{\$typeid_${key}}" but
			// the reference counter also includes the relations with AttributeMap.objtype_id hence
			// it often is not the same as the amount of objects that match the expression. With
			// this in mind don't display the counter as a link for this specific chapter.
			if ($tgt_chapter_no == CHAP_OBJTYPE)
				$cfe = '';
			else
			{
				$tmp = array();
				foreach ($attrs as $attr_id)
					$tmp[] = "{\$attr_${attr_id}_${key}}";
				$cfe = implode (' or ', $tmp);
			}

			if (! empty($cfe))
			{
				$href = makeHref
				(
					array
					(
						'page'=>'depot',
						'tab'=>'default',
						'andor' => 'and',
						'cfe' => $cfe
					)
				);
				echo '<a href="' . $href . '">' . $refcnt[$key] . '</a>';
			}
			else
				echo $refcnt[$key];
		}
		echo "</td><td>${value}</td></tr>\n";
		$order = $nextorder[$order];
	}
	echo "</table>\n<br>";
}

function renderChapterEditor ($tgt_chapter_no)
{
	global $nextorder;
	function printNewItemTR ()
	{
		printOpFormIntro ('add');
		echo '<tr><td>&nbsp;</td><td>&nbsp;</td><td>';
		printImageHREF ('add', 'Add new', TRUE);
		echo "</td>";
		echo "<td class=tdleft><input type=text name=dict_value size=64></td><td>";
		printImageHREF ('add', 'Add new', TRUE);
		echo '</td></tr></form>';
	}
	echo "<br><table class=cooltable border=0 cellpadding=5 cellspacing=0 align=center>\n";
	$words = readChapter ($tgt_chapter_no, 'r');
	$refcnt = getChapterRefc ($tgt_chapter_no, array_keys ($words));
	$order = 'odd';
	echo "<tr><th>Origin</th><th>Key</th><th>&nbsp;</th><th>Word</th><th>&nbsp;</th></tr>\n";
	if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
		printNewItemTR();
	foreach ($words as $key => $value)
	{
		echo "<tr class=row_${order}><td>";
		$order = $nextorder[$order];
		// Show plain row for stock records, render a form for user's ones.
		if ($key < 50000)
		{
			printImageHREF ('computer');
			echo "</td><td class=tdright>${key}</td><td>&nbsp;</td><td>${value}</td><td>&nbsp;</td></tr>";
			continue;
		}
		printOpFormIntro ('upd', array ('dict_key' => $key));
		printImageHREF ('favorite');
		echo "</td><td class=tdright>${key}</td><td>";
		// Prevent deleting words currently used somewhere.
		if ($refcnt[$key])
			printImageHREF ('nodelete', 'referenced ' . $refcnt[$key] . ' time(s)');
		else
			echo getOpLink (array('op'=>'del', 'dict_key'=>$key), '', 'delete', 'Delete word');
		echo '</td>';
		echo "<td class=tdleft><input type=text name=dict_value size=64 value='${value}'></td><td>";
		printImageHREF ('save', 'Save changes', TRUE);
		echo "</td></tr></form>";
	}
	if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
		printNewItemTR();
	echo "</table>\n<br>";
}

// $v is a $configCache item
// prints HTML-formatted varname and description
function renderConfigVarName ($v)
{
	echo '<span class="varname">' . $v['varname'] . '</span>';
	echo '<p class="vardescr">' . $v['description'] . ($v['is_userdefined'] == 'yes' ? '' : ' (system-wide)') . '</p>';
}

function renderUIConfig ()
{
	startPortlet ('Current configuration');
	echo '<table class="cooltable zebra" border=0 cellpadding=5 cellspacing=0 align=center width="70%">';
	echo '<tr><th class=tdleft>Option</th><th class=tdleft>Value</th></tr>';
	foreach (loadConfigCache() as $v)
	{
		if ($v['is_hidden'] != 'no')
			continue;
		echo '<tr>';
		echo "<td nowrap valign=top class=tdright>";
		renderConfigVarName ($v);
		echo '</td>';
		echo "<td valign=top class=tdleft>${v['varvalue']}</td></tr>";
	}
	echo "</table>\n";
	finishPortlet();
}

function renderConfigEditor ()
{
	global $pageno;
	$per_user = ($pageno == 'myaccount');
	global $configCache;
	startPortlet ('Current configuration');
	echo "<table cellspacing=0 cellpadding=5 align=center class=widetable width='50%'>\n";
	echo "<tr><th class=tdleft>Option</th>";
	echo "<th class=tdleft>Value</th></tr>";
	printOpFormIntro ('upd');

	$i = 0;
	foreach ($per_user ? $configCache : loadConfigCache() as $v)
	{
		if ($v['is_hidden'] != 'no')
			continue;
		if ($per_user && $v['is_userdefined'] != 'yes')
			continue;
		echo "<input type=hidden name=${i}_varname value='${v['varname']}'>";
		echo '<tr><td class="tdright">';
		renderConfigVarName ($v);
		echo '</td>';
		echo "<td class=\"tdleft\"><input type=text name=${i}_varvalue value='" . htmlspecialchars ($v['varvalue'], ENT_QUOTES) . "' size=24></td>";
		echo '<td class="tdleft">';
		if ($per_user && $v['is_altered'] == 'yes')
			echo getOpLink (array('op'=>'reset', 'varname'=>$v['varname']), 'reset');
		echo '</td>';
		echo "</tr>\n";
		$i++;
	}
	echo "<input type=hidden name=num_vars value=${i}>\n";
	echo "<tr><td colspan=3>";
	printImageHREF ('SAVE', 'Save changes', TRUE);
	echo "</td></tr>";
	echo "</form>";
	finishPortlet();
}

function renderUIResetForm()
{
	printOpFormIntro ('go');
	echo "This button will reset user interface configuration to its defaults (except organization name): ";
	echo "<input type=submit value='proceed'>";
	echo "</form>";
}

function serializeTagStats ($taginfo)
{
	$statsdecoder = array
	(
		'total' => ' total record(s) linked',
		'object' => ' object(s)',
		'rack' => ' rack(s)',
		'file' => ' file(s)',
		'user' => ' user account(s)',
		'ipv6net' => ' IPv6 network(s)',
		'ipv4net' => ' IPv4 network(s)',
		'ipv4vs' => ' IPv4 virtual service(s)',
		'ipv4rspool' => ' IPv4 real server pool(s)',
		'vst' => ' VLAN switch template(s)',
		'ipvs' => ' VS group(s)',
	);
	$stats = array ("tag ID = ${taginfo['id']}");
	if ($taginfo['kidc'])
		$stats[] = "${taginfo['kidc']} sub-tag(s)";
	if ($taginfo['refcnt']['total'])
		foreach ($taginfo['refcnt'] as $article => $count)
			if (array_key_exists ($article, $statsdecoder))
				$stats[] = $count . $statsdecoder[$article];
	return implode (', ', $stats);
}

function renderTagRowForViewer ($taginfo, $level = 0)
{
	$self = __FUNCTION__;
	$trclass = '';
	if ($level == 0)
		$trclass .= ' separator';
	$trclass .= $taginfo['is_assignable'] == 'yes' ? '' : ($taginfo['kidc'] ? ' trnull' : ' trwarning');
	if (!count ($taginfo['kids']))
		$level++; // Shift instead of placing a spacer. This won't impact any nested nodes.
	$refc = $taginfo['refcnt']['total'];
	echo "<tr class='${trclass}'><td align=left style='padding-left: " . ($level * 16) . "px;'>";
	if (count ($taginfo['kids']))
		printImageHREF ('node-expanded-static');
	echo '<span title="' . serializeTagStats ($taginfo) . '" class="' . getTagClassName ($taginfo['id']) . '">' . $taginfo['tag'];
	echo ($refc ? " <i>(${refc})</i>" : '') . '</span></td></tr>';
	foreach ($taginfo['kids'] as $kid)
		$self ($kid, $level + 1);
}

function renderTagTree ()
{
	echo '<center><table class=tagtree>';
	foreach (getTagTree() as $taginfo)
		renderTagRowForViewer ($taginfo);
	echo '</table></center>';
}

function renderTagRowForEditor ($taginfo, $parent_name = NULL, $level = 0)
{
	$self = __FUNCTION__;
	if (!count ($taginfo['kids']))
		$level++; // Idem
	$trclass = $taginfo['is_assignable'] == 'yes' ? '' : ($taginfo['kidc'] ? ' class=trnull' : ' class=trwarning');
	echo "<tr${trclass}><td align=left style='padding-left: " . ($level * 16) . "px;'>";
	if ($taginfo['kidc'])
		printImageHREF ('node-expanded-static');
	if ($taginfo['refcnt']['total'] > 0 || $taginfo['kidc'])
		printImageHREF ('nodestroy', serializeTagStats ($taginfo));
	else
		echo getOpLink (array ('op' => 'destroyTag', 'tag_id' => $taginfo['id']), '', 'destroy', 'Delete tag');
	echo '</td><td>';
	printOpFormIntro ('updateTag', array ('tag_id' => $taginfo['id']));
	echo "<input type=text size=48 name=tag_name ";
	echo "value='${taginfo['tag']}'></td><td class=tdleft>";
	if ($taginfo['refcnt']['total'])
		printSelect (array ('yes' => 'yes'), array ('name' => 'is_assignable')); # locked
	else
		printSelect (array ('yes' => 'yes', 'no' => 'no'), array ('name' => 'is_assignable'), $taginfo['is_assignable']);
	echo '</td><td class=tdleft>';

	$poptions = $parent_name === NULL ?
		array (0 => '-- NONE --') :
		array ($taginfo['parent_id'] => $parent_name);
	$sparams = array ('name' => 'parent_id', 'id' => 'nodeid_' . $taginfo['id'], 'class' => 'nodelist-popup');
	echo getSelect ($poptions, $sparams, $taginfo['parent_id'], FALSE);

	echo '</td><td>' . getImageHREF ('save', 'Save changes', TRUE) . '</form></td></tr>';
	foreach ($taginfo['kids'] as $kid)
		$self ($kid, $taginfo['tag'], $level + 1);
}

function addParentNodeOptionsJS ($prefix, $nodetype)
{
	addJS
	(
<<<END
function ${prefix}_showselectbox(e) {
	$(this).load('index.php', {module: 'ajax', ac: 'get-parent-node-options', node_type: '${nodetype}', node_id: this.id});
	$(this).unbind('mousedown', ${prefix}_showselectbox);
}
$(document).ready(function () {
	$('select.nodelist-popup').bind('mousedown', ${prefix}_showselectbox);
});
END
		, TRUE
	);
}

function renderTagTreeEditor ()
{
	function printNewItemTR ($options)
	{
		printOpFormIntro ('createTag');
		echo '<tr>';
		echo '<td align=left style="padding-left: 16px;">' . getImageHREF ('create', 'Create tag', TRUE) . '</td>';
		echo '<td><input type=text size=48 name=tag_name></td>';
		echo '<td class=tdleft>' . getSelect (array ('yes' => 'yes', 'no' => 'no'), array ('name' => 'is_assignable'), 'yes') . '</td>';
		echo '<td>' . getSelect ($options, array ('name' => 'parent_id')) . '</td>';
		echo '<td>' . getImageHREF ('create', 'Create tag', TRUE, 120) . '</td>';
		echo '</tr></form>';
	}
	global $taglist;
	addParentNodeOptionsJS ('tageditor', 'existing tag');
	$options = getParentNodeOptionsNew ($taglist, 'tag');
	echo '<br><table cellspacing=0 cellpadding=5 align=center class=widetable>';
	echo '<tr><th>&nbsp;</th><th>tag name</th><th>assignable</th><th>parent tag</th><th>&nbsp;</th></tr>';
	if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
		printNewItemTR ($options);
	foreach (getTagTree() as $taginfo)
		renderTagRowForEditor ($taginfo);
	if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
		printNewItemTR ($options);
	echo '</table>';
}

function renderGraphCycleResolver()
{
	global $pageno;
	// $fieldmap below does not contain 'parent_id' as it is done by the SELECT.
	switch ($pageno)
	{
		case 'tagtree';
			global $taglist;
			$nodelist = $taglist;
			$textfield = 'tag';
			$opcode = 'updateTag';
			$fieldmap = array
			(
				'tag_id' => 'id',
				'tag_name' => 'tag',
				'is_assignable' => 'is_assignable',
			);
			break;
		default:
			throw new RackTablesError ('unexpected call to tabhandler function', RackTablesError::INTERNAL);
	}
	$invalids = getInvalidNodes ($nodelist);
	$options = getParentNodeOptionsNew ($nodelist, $textfield);
	echo '<br><table cellspacing=0 cellpadding=5 align=center class=widetable>';
	echo '<tr><th>node</th><th>current parent node</th><th>new parent node</th><th>&nbsp;</th></tr>';
	foreach ($invalids as $node)
	{
		$formvalues = array();
		foreach ($fieldmap as $form_param => $nodefield)
			$formvalues[$form_param] = $node[$nodefield];
		printOpFormIntro ($opcode, $formvalues);
		echo '<tr>';
		echo '<td class=tdleft>' . stringForLabel ($node[$textfield]) . '</td>';
		echo '<td class="tdleft trerror">' . stringForLabel ($invalids[$node['parent_id']][$textfield]) . '</td>';
		echo '<td>' . getSelect ($options, array ('name' => 'parent_id'), $node['parent_id']) . '</td>';
		echo '<td>' . getImageHREF ('save', 'Save changes', TRUE) . '</td>';
		echo '</tr></form>';
	}
	echo '</table>';
}

function renderMyAccount ()
{
	global $remote_username, $remote_displayname, $expl_tags, $impl_tags, $auto_tags;

	startPortlet ('Current user info');
	echo '<div style="text-align: left; display: inline-block;">';
	echo "<table>";
	echo "<tr><th>Login:</th><td>${remote_username}</td></tr>\n";
	echo "<tr><th>Name:</th><td>${remote_displayname}</td></tr>\n";
	echo "<tr><th>Explicit tags:</th><td>" . serializeTags (getExplicitTagsOnly ($expl_tags)) . "</td></tr>\n";
	echo "<tr><th>Implicit tags:</th><td>" . serializeTags ($impl_tags) . "</td></tr>\n";
	echo "<tr><th>Automatic tags:</th><td>" . serializeTags ($auto_tags) . "</td></tr>\n";
	echo '</table></div>';
}

function renderMyPasswordEditor ()
{
	printOpFormIntro ('changeMyPassword');
	echo '<table border=0 align=center>';
	echo "<tr><th class=tdright>Current password (*):</th><td><input type=password name=oldpassword></td></tr>";
	echo "<tr><th class=tdright>New password (*):</th><td><input type=password name=newpassword1></td></tr>";
	echo "<tr><th class=tdright>New password again (*):</th><td><input type=password name=newpassword2></td></tr>";
	echo "<tr><td colspan=2 align=center><input type=submit value='Change'></td></tr>";
	echo '</table></form>';
}

function renderMyQuickLinks ()
{
	global $indexlayout, $page;
	startPortlet ('Items to display in page header');
	echo '<div style="text-align: left; display: inline-block;">';
	printOpFormIntro ('save');
	echo '<ul class="qlinks-form">';
	$active_items = explode (',', getConfigVar ('QUICK_LINK_PAGES'));
	$items = array();
	foreach ($indexlayout as $row)
		foreach ($row as $ypageno)
		{
			$items[$ypageno] = getPageName ($ypageno);
			if ($ypageno == 'config') // expand
				foreach ($page as $subpageno => $subpage)
					if (array_fetch ($subpage, 'parent', NULL) == $ypageno)
						$items[$subpageno] = $items[$ypageno] . ': ' . getPageName ($subpageno);
		}
	foreach ($items as $ypageno => $pagename)
	{
		$checked_state = in_array ($ypageno, $active_items) ? 'checked' : '';
		echo "<li><label><input type='checkbox' name='page_list[]' value='$ypageno' $checked_state>" . $pagename . "</label></li>\n";
	}
	echo '</ul>';
	printImageHREF ('SAVE', 'Save changes', TRUE);
	echo '</form></div>';
	finishPortlet();
}

function renderCactiConfig()
{
	$columns = array
	(
		array ('th_text' => 'base URL', 'row_key' => 'base_url'),
		array ('th_text' => 'username', 'row_key' => 'username'),
		array ('th_text' => 'graph(s)', 'row_key' => 'num_graphs', 'td_class' => 'tdright'),
	);
	$servers = getCactiServers();
	startPortlet ('Cacti servers (' . count ($servers) . ')');
	renderTableViewer ($columns, $servers);
	finishPortlet();
}

function renderCactiServersEditor()
{
	function printNewItemTR ()
	{
		printOpFormIntro ('add');
		echo '<tr>' .
			'<td>' . getImageHREF ('create', 'add a new server', TRUE) . '</td>' .
			'<td><input type=text size=48 name=base_url></td>' .
			'<td><input type=text size=24 name=username></td>' .
			'<td><input type=password size=24 name=password></td>' .
			'<td>&nbsp;</td>' .
			'<td>' . getImageHREF ('create', 'add a new server', TRUE) . '</td>' .
			'</tr></form>';
	}
	echo '<table cellspacing=0 cellpadding=5 align=center class=widetable>';
	echo '<tr>' .
		'<th>&nbsp;</th>' .
		'<th>base URL</th>' .
		'<th>username</th>' .
		'<th>password</th>' .
		'<th>graph(s)</th>' .
		'<th>&nbsp;</th>' .
		'</tr>';
	if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
		printNewItemTR();
	foreach (getCactiServers() as $server)
	{
		printOpFormIntro ('upd', array ('id' => $server['id']));
		echo '<tr><td>';
		if ($server['num_graphs'])
			printImageHREF ('nodestroy', 'cannot delete, graphs exist');
		else
			echo getOpLink (array ('op' => 'del', 'id' => $server['id']), '', 'destroy', 'delete this server');
		echo '</td>';
		echo '<td><input type=text size=48 name=base_url value="' . htmlspecialchars ($server['base_url'], ENT_QUOTES, 'UTF-8') . '"></td>';
		echo '<td><input type=text size=24 name=username value="' . htmlspecialchars ($server['username'], ENT_QUOTES, 'UTF-8') . '"></td>';
		echo '<td><input type=password size=24 name=password value="' . htmlspecialchars ($server['password'], ENT_QUOTES, 'UTF-8') . '"></td>';
		echo "<td class=tdright>${server['num_graphs']}</td>";
		echo '<td>' . getImageHREF ('save', 'update this server', TRUE) . '</td>';
		echo '</tr></form>';
	}
	if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
		printNewItemTR();
	echo '</table>';
}

function renderMuninConfig()
{
	$columns = array
	(
		array ('th_text' => 'base URL', 'row_key' => 'base_url', 'td_maxlen' => 150),
		array ('th_text' => 'graph(s)', 'row_key' => 'num_graphs', 'td_class' => 'tdright'),
	);
	$servers = getMuninServers();
	startPortlet ('Munin servers (' . count ($servers) . ')');
	renderTableViewer ($columns, $servers);
	finishPortlet();
}

function renderMuninServersEditor()
{
	function printNewItemTR()
	{
		printOpFormIntro ('add');
		echo '<tr>' .
			'<td>' . getImageHREF ('create', 'add a new server', TRUE) . '</td>' .
			'<td><input type=text size=48 name=base_url></td>' .
			'<td>&nbsp;</td>' .
			'<td>' . getImageHREF ('create', 'add a new server', TRUE) . '</td>' .
			'</tr></form>';
	}
	echo '<table cellspacing=0 cellpadding=5 align=center class=widetable>';
	echo '<tr>' .
		'<th>&nbsp;</th>' .
		'<th>base URL</th>' .
		'<th>graph(s)</th>' .
		'<th>&nbsp;</th>' .
		'</tr>';
	if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
		printNewItemTR();
	foreach (getMuninServers() as $server)
	{
		printOpFormIntro ('upd', array ('id' => $server['id']));
		echo '<tr><td>';
		if ($server['num_graphs'])
			printImageHREF ('nodestroy', 'cannot delete, graphs exist');
		else
			echo getOpLink (array ('op' => 'del', 'id' => $server['id']), '', 'destroy', 'delete this server');
		echo '</td>';
		echo '<td><input type=text size=48 name=base_url value="' . htmlspecialchars ($server['base_url'], ENT_QUOTES, 'UTF-8') . '"></td>';
		echo "<td class=tdright>${server['num_graphs']}</td>";
		echo '<td>' . getImageHREF ('save', 'update this server', TRUE) . '</td>';
		echo '</tr></form>';
	}
	if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
		printNewItemTR();
	echo '</table>';
}

?>
