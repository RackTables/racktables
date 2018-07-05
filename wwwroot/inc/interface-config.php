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
	echo '<table width="100%" border=0>';
	addJS ('js/codemirror/codemirror.js');
	addJS ('js/codemirror/rackcode.js');
	addCSS ('css/codemirror/codemirror.css');
	addCSS ('css/codemirror/rackcode.css');
	if (! array_key_exists ('line', $_REQUEST))
		$scrollcode = '';
	else
	{
		// Line numbers start from 0 in CodeMirror API and from 1 elsewhere.
		$lineno = genericAssertion ('line', 'natural') - 1;
		$scrollcode = "rackCodeMirror.addLineClass (${lineno}, 'wrap', 'border_highlight');\n" .
			"rackCodeMirror.scrollIntoView ({line: ${lineno}, ch: 0}, 50);\n";
	}
	// Heredoc, not nowdoc!
	addJS (<<<"ENDJAVASCRIPT"
$(document).ready(function() {
	var rackCodeMirror = CodeMirror.fromTextArea(document.getElementById("RCTA"),{
		mode:'rackcode',
		theme:'rackcode',
		readOnly:'nocursor',
		lineNumbers:true });
	${scrollcode}
});
ENDJAVASCRIPT
	, TRUE);
	echo "<tr><td><textarea rows=40 cols=100 id=RCTA>";
	echo loadScript ('RackCode') . "</textarea></td></tr>\n";
	echo '</table>';
}

function renderRackCodeEditor ()
{
	addJS ('js/codemirror/codemirror.js');
	addJS ('js/codemirror/rackcode.js');
	addCSS ('css/codemirror/codemirror.css');
	addCSS ('css/codemirror/rackcode.css');
	addJS (<<<'ENDJAVASCRIPT'
function verify()
{
	$.ajax({
		type: "POST",
		url: "index.php",
		data: {'module': 'ajax', 'ac': 'verifyCode', 'code': $("#RCTA").text()},
		success: function (data)
		{
			arr = data.split("\n");
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
		theme:'rackcode',
		autofocus:true,
		lineNumbers:true });
	rackCodeMirror.on("change",function(cm,cmChangeObject){
		$("#RCTA").text(cm.getValue());
    });
});
ENDJAVASCRIPT
	, TRUE);

	printOpFormIntro ('saveRackCode');
	echo '<table width="100%" border=0>';
	echo "<tr><td><textarea rows=40 cols=100 name=rackcode id=RCTA>";
	echo loadScript ('RackCode') . "</textarea></td></tr>\n";
	echo "<tr><td class=submit>";
	echo '<div id="ShowMessage"></div>';
	echo "<input type='button' value='Verify' onclick='verify();'>";
	echo "<input type='submit' value='Save' disabled='disabled' id='SaveChanges'>";
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
	$rows = array();
	$refcnt = getPortOIFRefc();
	foreach (getPortOIFOptions() as $oif_id => $oif_name)
		$rows[] = array
		(
			'origin' => $oif_id < 2000 ? getImageHREF ('computer', 'default') : getImageHREF ('favorite', 'custom'),
			'oif_id' => $oif_id,
			'refc' => $refcnt[$oif_id] ? $refcnt[$oif_id] : '',
			'oif_name' => $oif_name,
		);
	$columns = array
	(
		array ('th_text' => 'Origin', 'row_key' => 'origin', 'td_escape' => FALSE),
		array ('th_text' => 'Key', 'row_key' => 'oif_id', 'td_class' => 'tdright'),
		array ('th_text' => 'Refcnt', 'row_key' => 'refc', 'td_class' => 'tdright'),
		array ('th_text' => 'Outer Interface', 'row_key' => 'oif_name', 'td_maxlen' => 48),
	);
	renderTableViewer ($columns, $rows);
	echo '<br>';
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
	$words = readChapter ($tgt_chapter_no, 'a');
	$wc = count ($words);
	echo "<center><h2>${wc} record(s)</h2></center>";
	if ($wc == 0)
		return;
	$refcnt = getChapterRefc ($tgt_chapter_no, array_keys ($words));
	$attrs = getChapterAttributes ($tgt_chapter_no);
	$columns = array
	(
		array ('th_text' => 'Origin', 'row_key' => 'origin', 'td_escape' => FALSE),
		array ('th_text' => 'Key', 'row_key' => 'dict_key', 'td_class' => 'tdright'),
		array ('th_text' => 'Refcnt', 'row_key' => 'refc', 'td_class' => 'tdright', 'td_escape' => FALSE),
		array ('th_text' => 'Word', 'row_key' => 'word'),
	);
	$rows = array();
	foreach ($words as $key => $value)
	{
		if ($refcnt[$key] == 0)
			$refc = '';
		else
		{
			// For the ObjectType chapter the extra filter is as simple as "{\$typeid_${key}}" but
			// the reference counter also includes the relations with AttributeMap.objtype_id hence
			// it often is not the same as the amount of objects that match the expression. With
			// this in mind don't display the counter as a link for this specific chapter.
			if ($tgt_chapter_no == CHAP_OBJTYPE || ! count ($attrs))
				$refc = $refcnt[$key];
			else
			{
				$tmp = array();
				foreach ($attrs as $attr_id)
					$tmp[] = "{\$attr_${attr_id}_${key}}";
				$href = makeHref
				(
					array
					(
						'page'=>'depot',
						'tab'=>'default',
						'andor' => 'and',
						'cfe' => implode (' or ', $tmp),
					)
				);
				$refc = '<a href="' . $href . '">' . $refcnt[$key] . '</a>';
			}
		} // else
		$rows[] = array
		(
			'origin' => $key < 50000 ? getImageHREF ('computer', 'default') : getImageHREF ('favorite', 'custom'),
			'dict_key' => $key,
			'refc' => $refc,
			'word' => $value,
		);
	} // foreach
	renderTableViewer ($columns, $rows);
	echo '<br>';
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
	echo '</span>' . ($refc ? " <i>(${refc})</i>" : '') . '</td>';
	echo '<td>' . stringForTD ($taginfo['description'], 64) . '</td>';
	echo "</tr>\n";
	foreach ($taginfo['kids'] as $kid)
		$self ($kid, $level + 1);
}

function renderTagTree ()
{
	echo '<center><table class=tagtree>';
	echo '<tr><th>name</th><th>description</th></tr>';
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

	if ($taginfo['is_assignable'] == 'yes')
	{
		$class = getTagClass ($taginfo);
		echo "</td><td class='${class}'>" . getColorSelect ('colorid_'.$taginfo['id'], $taginfo['color']) . '</td>';
	}
	else
		echo '<td><input type="hidden" name="color" id="colorid_' . $taginfo['id'] . '" value=""></input></td>';

	echo '</td><td>' . getImageHREF ('save', 'Save changes', TRUE) . '</form></td></tr>';
	foreach ($taginfo['kids'] as $kid)
		$self ($kid, $taginfo['tag'], $level + 1);
}

function addParentNodeOptionsJS ($prefix, $nodetype)
{
	addJS
	(
// Heredoc, not nowdoc!
<<<"END"
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

function getColorSelect($id = 'color', $selected = NULL)
{

		if ($selected)
			$class = ' class=' . getTagClass (array ('id' => $selected, 'color' => $selected));
		else
			$class = '';

		$ret = "<select tabindex='1' name='color' id='${id}' onchange='this.className=this.options[this.selectedIndex].className;'${class}>";
		$ret .= '<option value=""option>';

		$colors = array(
				'FFFFFF',
				'C0C0C0',
				'808080',
				'000000',
				'FF0000',
				'800000',
				'FF8000',
				'FFFF00',
				'808000',
				'00FF00',
				'008000',
				'00FFFF',
				'008080',
				'0000FF',
				'000080',
				'FF00FF',
				'800080'
		);

		if ($selected != NULL && !in_array ($selected, $colors))
			$colors[] = $selected;

		foreach ($colors as $color)
		{
			$class = getTagClass (array ('id' => $color, 'color' => $color));
			$ret .= "<option class='${class}' value='$color'" . ($color == $selected ? " selected" : "" ) . ">#$color</option>";
		}

		$ret .= '</select>';
		return $ret;
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
		echo '<td>' . getColorSelect () . '</td>';
		echo '<td>' . getImageHREF ('create', 'Create tag', TRUE, 120) . '</td>';
		echo '</tr></form>';
	}
	global $taglist;
	addParentNodeOptionsJS ('tageditor', 'existing tag');
	$options = getParentNodeOptionsNew ($taglist, 'tag');
	echo '<br><table cellspacing=0 cellpadding=5 align=center class=widetable>';
	echo '<tr><th>&nbsp;</th><th>tag name</th><th>assignable</th><th>parent tag</th><th>color</th><th>&nbsp;</th></tr>';
	if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
		printNewItemTR ($options);
	foreach (getTagTree() as $taginfo)
		renderTagRowForEditor ($taginfo);
	if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
		printNewItemTR ($options);
	echo '</table>';
}

function renderTagRowForDescriptions ($taginfo, $level = 0)
{
	$self = __FUNCTION__;

	$trclass = $taginfo['is_assignable'] == 'yes' ? '' : ($taginfo['kidc'] ? ' trnull' : ' trwarning');
	if (!count ($taginfo['kids']))
		$level++; // Shift instead of placing a spacer. This won't impact any nested nodes.
	$refc = $taginfo['refcnt']['total'];
	echo "<tr class='${trclass}'>";

	echo '<td align=left style="padding-left: ' . ($level * 16) . 'px;">';
	printOpFormIntro ('updTagDescr', array ('id' => $taginfo['id']));
	if (count ($taginfo['kids']))
		printImageHREF ('node-expanded-static');
	echo '<span title="' . serializeTagStats ($taginfo) . '" class="' . getTagClassName ($taginfo['id']) . '">' . $taginfo['tag'];
	echo '</span>' . ($refc ? " <i>(${refc})</i>" : '') . "</td>";

	echo '<td>';
	if ($taginfo['description'] === NULL)
		echo '&nbsp;';
	else
		echo getOpLink
		(
			array ('op' => 'updTagDescr', 'id' => $taginfo['id'], 'description' => ''),
			'',
			'clear',
			'Clear value'
		);
	echo '</td>';

	echo '<td><input type=text size=64 name=description value="';
	echo stringForTextInputValue ($taginfo['description'], 0) . '"></td>';

	echo '<td>' . getImageHREF ('save', 'Save changes', TRUE) . '</td>';
	echo '</form>';
	echo "</tr>\n";

	foreach ($taginfo['kids'] as $kid)
		$self ($kid, $level + 1);
}

function renderTagDescriptionsEditor()
{
	echo '<br><table cellspacing=0 cellpadding=5 align=center class=widetable>';
	echo '<tr><th>tag name</th><th>&nbsp;</th><th>tag description</th></tr>';
	foreach (getTagTree() as $taginfo)
		renderTagRowForDescriptions ($taginfo);
	echo '</table></center><br>';
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

function renderPluginConfig ()
{
	$plugins = getPlugins ();
	if (empty ($plugins))
	{
		echo '<b>No plugins exist</b>';
		return;
	}

	foreach (array_keys ($plugins) as $name)
		$plugins[$name]['x_state'] = formatPluginState ($plugins[$name]['state']);
	$columns = array
	(
		array ('th_text' => 'Plugin', 'row_key' => 'longname'),
		array ('th_text' => 'Code Version', 'row_key' => 'code_version'),
		array ('th_text' => 'DB Version', 'row_key' => 'db_version'),
		array ('th_text' => 'Home page', 'row_key' => 'home_url'),
		array ('th_text' => 'State', 'row_key' => 'x_state'),
	);
	renderTableViewer ($columns, $plugins);
}

function renderPluginEditor()
{
	function considerOpLink ($oplinks, $opcode)
	{
		return permitted (NULL, NULL, $opcode) ? $oplinks[$opcode] : '';
	}

	$plugins = getPlugins ();
	if (empty ($plugins))
	{
		echo '<b>No plugins exist</b>';
		return;
	}

	foreach (array_keys ($plugins) as $name)
	{
		$links = '&nbsp;';
		$oplinks = array
		(
			'enable' => getOpLink (array ('op' => 'enable', 'name' => $name), '', 'enable', 'Enable'),
			'disable' => getOpLink (array ('op' => 'disable', 'name' => $name), '', 'disable', 'Disable'),
			'add' => getOpLink (array ('op' => 'install', 'name' => $name), '', 'add', 'Install'),
			'delete' => getOpLink (array ('op' => 'uninstall', 'name' => $name), '', 'delete', 'Uninstall', 'need-confirmation'),
			'upgrade' => getOpLink (array ('op' => 'upgrade', 'name' => $name), '', 'upgrade', 'Upgrade'),
		);
		switch ($plugins[$name]['state'])
		{
		case 'disabled':
			$links .= considerOpLink ($oplinks, 'enable');
			$links .= considerOpLink ($oplinks, 'delete');
			break;
		case 'enabled':
			$links .= considerOpLink ($oplinks, 'disable');
			$links .= considerOpLink ($oplinks, 'delete');
			break;
		case 'not_installed':
			$links .= considerOpLink ($oplinks, 'add');
			break;
		default:
			throw new RackTablesError ('invalid plugin state', RackTablesError::INTERNAL);
		}
		if
		(
			$plugins[$name]['code_version'] != 'N/A' &&
			$plugins[$name]['db_version'] != 'N/A' &&
			$plugins[$name]['code_version'] != $plugins[$name]['db_version']
		)
			$links .= considerOpLink ($oplinks, 'upgrade');
		$plugins[$name]['links'] = $links;
		$plugins[$name]['x_state'] = formatPluginState ($plugins[$name]['state']);
	}
	$columns = array
	(
		array ('th_text' => 'Plugin', 'row_key' => 'longname'),
		array ('th_text' => 'Code Version', 'row_key' => 'code_version'),
		array ('th_text' => 'DB Version', 'row_key' => 'db_version'),
		array ('th_text' => 'Home page', 'row_key' => 'home_url'),
		array ('th_text' => 'State', 'row_key' => 'x_state'),
		array ('row_key' => 'links', 'td_escape' => FALSE),
	);
	if (permitted (NULL, NULL, 'delete'))
		echo "<br><div class=msg_error>Warning: Uninstalling a plugin permanently deletes all related data.</div>\n";
	renderTableViewer ($columns, $plugins);
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
