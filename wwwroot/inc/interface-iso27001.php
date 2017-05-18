<?php

function renderISO27001Configuration()
{
	function renderSimplePortlet ($title, $rows)
	{
		startPortlet ($title);
		if (! count ($rows))
			echo '(none)';
		else
		{
			echo '<div align=left><ul>';
			foreach ($rows as $row)
				echo "<li><span title='id=${row['id']}'>" . stringForLabel ($row['name'], 64) . '</span></li>';
			echo '</ul></div>';
		}
		finishPortlet();
	}

	echo '<table class=objview border=0 width="100%"><tr><td class=pcleft width="15%">';
	renderSimplePortlet ('asset groups', getISO27001AssetGroupList());
	echo '</td><td class=pcleft width="15%">';
	renderSimplePortlet ('asset owners', getISO27001AssetOwnerList());
	echo '</td><td class=pcleft width="15%">';
	renderSimplePortlet ('asset maintainers', getISO27001AssetMaintainerList());

	echo '</td><td class=pcleft width="*">';

	startPortlet ('criteria configuration');
	if (0 == count ($conf = getISO27001Configuration()))
		echo '(none)';
	else
	{
		echo '<table class=widetable border=0 cellpadding=5 cellspacing=0 align=center>';
		echo '<tr><th class=tdleft>Group</th><th>Criteria</th><th>Values</th></tr>';
		foreach ($conf as $cgroup)
		{
			echo '<tr valign=top><td class=tdleft>';
			echo "<span title='id=${cgroup['id']}'>" . stringForTD ($cgroup['name'], 64) . '</span>';
			echo '</td>';
			echo '<td class=tdleft>';
			if (0 == count ($cgroup['criteria']))
				echo '&empty;';
			else
			{
				echo '<ul>';
				foreach ($cgroup['criteria'] as $each)
				{
					echo "<li><span title='id=${each['id']}'>" . stringForLabel ($each['name'], 64);
					if ($each['weight'] != 1.0)
						printf (" (&times;%.2f)", $each['weight']);
					echo '</span></li>';
				}
				echo '</ul>';
			}
			echo '</td>';
			echo '<td class=tdleft>';
			if (0 == count ($cgroup['values']))
				echo '&empty;';
			else
			{
				echo '<ul>';
				foreach ($cgroup['values'] as $each)
					echo '<li>' . formatISO27001CVAsLabel ($each) . '</li>';
				echo '</ul>';
			}
			echo '</td>';
			echo '</tr>';
		}
		echo '</table>';
	}
	finishPortlet();
	echo '</td></tr></table>';
}

function renderISO27001AssetGroupEditor()
{
	echo '<br>';
	renderSimpleTableEditor
	(
		getISO27001AssetGroupList(),
		array
		(
			'header' => 'asset group',
			'key' => 'id',
			'value' => 'name',
			'width' => 64,
		)
	);
	echo '<br>';
}

function renderISO27001AssetOwnerEditor()
{
	echo '<br>';
	renderSimpleTableEditor
	(
		getISO27001AssetOwnerList(),
		array
		(
			'header' => 'owner',
			'key' => 'id',
			'value' => 'name',
			'width' => 64,
		)
	);
	echo '<br>';
}

function renderISO27001AssetMaintainerEditor()
{
	echo '<br>';
	renderSimpleTableEditor
	(
		getISO27001AssetMaintainerList(),
		array
		(
			'header' => 'maintainer',
			'key' => 'id',
			'value' => 'name',
			'width' => 64,
		)
	);
	echo '<br>';
}

function renderISO27001CGroupEditor()
{
	echo '<br>';
	renderSimpleTableEditor
	(
		getISO27001CriterionGroupList(),
		array
		(
			'header' => 'criterion group',
			'key' => 'id',
			'value' => 'name',
			'width' => 64,
		)
	);
	echo '<br>';
}

function renderISO27001CValueSetEditor()
{
	function printNewitemTR()
	{
		printOpFormIntro ('add');
		echo '<tr>';
		echo '<td class=tdleft>' . getImageHREF ('add', 'add new', TRUE) . '</td>';
		echo '<td>' . getSelect (getISO27001CriterionGroupOptions(), array ('name' => 'cgroup_id')) . '</td>';
		echo '<td><input type=text size=5 maxlength=5 name=value></td>';
		echo '<td><input type=text size=32 maxlength=32 name=label></td>';
		echo '<td class=tdleft>' . getImageHREF ('add', 'add new', TRUE) . '</td>';
		echo '</tr></form>';
	}

	global $nextorder;
	echo '<table class=widetable border=0 cellpadding=5 cellspacing=0 align=center>';
	echo '<tr><th>&nbsp;</th><th>Criterion group</th><th>Value</th><th>Label</th><th>&nbsp;</th></tr>';
	if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
		printNewitemTR();
	$cgroups = getISO27001CriterionGroupOptions();
	$last_id = NULL;
	$order = 'even';
	foreach (getISO27001CriterionGroupValueSets() as $row)
	{
		printOpFormIntro ('upd', array ('cgroup_id' => $row['cgroup_id'], 'value' => $row['value']));
		if ($last_id != $row['cgroup_id'])
		{
			$last_id = $row['cgroup_id'];
			$order = $nextorder[$order];
		}
		echo "<tr class=row_${order}>";
		echo '<td>';
		if ($row['refc'] > 0)
			printImageHREF ('nodelete', $row['refc'] . ' value(s) assigned');
		else
			echo getOpLink (array ('op' => 'del', 'cgroup_id' => $row['cgroup_id'], 'value' => $row['value']), '', 'delete', 'remove');
		echo '</td>';
		echo '<td>' . stringForTD ($cgroups[$row['cgroup_id']], 64) . '</td>';
		echo '<td class=tdright>' . $row['value'] . '</td>';
		echo '<td><input type=text name=label size=32 maxlength=32 value="' . stringForTextInputValue ($row['label'], 32) . '"></td>';
		echo '<td>' . getImageHREF ('save', 'save changes', TRUE) . '</td>';
		echo '</tr></form>';
	}
	if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
		printNewitemTR();
	echo '</table>';
}

function renderISO27001CriteriaEditor()
{
	function printNewitemTR()
	{
		printOpFormIntro ('add');
		echo '<tr>';
		echo '<td rowspan=2 class=tdleft>' . getImageHREF ('create', 'add new', TRUE) . '</td>';
		echo '<td>' . getSelect (getISO27001CriterionGroupOptions(), array ('name' => 'cgroup_id')) . '</td>';
		echo '<td><input type=text size=64 maxlength=64 name=name></td>';
		echo '<td rowspan=2><textarea name=comment rows=5 cols=40></textarea></td>';
		echo '<td rowspan=2 class=tdleft>' . getImageHREF ('create', 'add new', TRUE) . '</td>';
		echo '</tr><tr>';
		echo '<td>&nbsp;</td>';
		echo '<td><input type=text size=5 maxlength=5 name=weight value="1.0"></td>';
		echo '</tr></form>';
	}

	global $nextorder;
	echo '<table class=widetable border=0 cellpadding=5 cellspacing=0 align=center>';
	echo '<tr><th>&nbsp;</th><th>Group</th><th>Name/Weight</th><th>Comment</th><th>&nbsp;</th></tr>';
	if (getConfigVar ('ADDNEW_AT_TOP') == 'yes')
		printNewitemTR();
	$cgroups = getISO27001CriterionGroupOptions();
	$last_id = NULL;
	$order = 'even';
	foreach (getISO27001CriterionList() as $row)
	{
		printOpFormIntro ('upd', array ('id' => $row['id']));
		echo "<tr class=row_${order}>";
		echo '<td rowspan=2>';
		if ($row['refc'] > 0)
			printImageHREF ('nodestroy', $row['refc'] . ' value(s) assigned');
		else
			echo getOpLink (array ('op' => 'del', 'id' => $row['id']), '', 'destroy', 'remove');
		echo '</td>';
		echo '<td>' . stringForTD ($cgroups[$row['cgroup_id']], 64) . '</td>';
		echo '<td><input type=text size=64 maxlength=64 name=name value="' . stringForTextInputValue ($row['name'], 64) . '"></td>';
		echo '<td rowspan=2><textarea name=comment rows=5 cols=40>' . stringForTextarea ($row['comment']) . '</textarea></td>';
		echo '<td rowspan=2>' . getImageHREF ('save', 'save changes', TRUE) . '</td>';
		echo "</tr><tr class=row_${order}>";
		echo '<td>&nbsp;</td>';
		echo '<td><input type=text size=5 maxlength=5 name=weight value="' . $row['weight'] . '"></td>';
		echo '</tr></form>';
		$order = $nextorder[$order];
	}
	if (getConfigVar ('ADDNEW_AT_TOP') != 'yes')
		printNewitemTR();
	echo '</table>';
}

function renderObjectISO27001Asset()
{
	echo '<br><table border=0 cellspacing=0 cellpadding=5 align=center>';
	echo '<tr><th>&nbsp;</th><th>asset group</th><th>asset owner</th><th>asset maintainer</th><th>criticality</th><th>&nbsp;</th></tr>';

	if (NULL === $asset = getISO27001AssetInfo (getBypassValue()))
	{
		printOpFormIntro ('add');
		echo '<tr>';
		echo '<td rowspan=4>&nbsp;</td>';
		echo '<td rowspan=4>' . getSelect (getISO27001AssetGroupOptions(), array ('name' => 'agroup_id', 'size' => getConfigVar ('MAXSELSIZE')), getConfigVar ('ISO27001_DEFAULT_AGROUP')) . '</td>';
		echo '<td rowspan=4>' . getSelect (getISO27001AssetOwnerOptions(), array ('name' => 'aowner_id', 'size' => getConfigVar ('MAXSELSIZE')), getConfigVar ('ISO27001_DEFAULT_AOWNER')) . '</td>';
		echo '<td rowspan=4>' . getSelect (getISO27001AssetMaintainerOptions(), array ('name' => 'amaint_id', 'size' => getConfigVar ('MAXSELSIZE')), getConfigVar ('ISO27001_DEFAULT_AMAINT')) . '</td>';
		echo '<td valign=top><input type=text name=criticality width=5 value="' . getConfigVar ('ISO27001_DEFAULT_CRITICALITY') . '"></td>';
		echo '<td rowspan=4 valign=top>' . getImageHREF ('ATTACH', 'register as asset', TRUE) . '</td>';
		echo '</tr>';
		echo '<tr><td>&nbsp;</td></tr>';
		echo '<tr><th>comment</th></tr>';
		echo '<tr><td valign=top><textarea cols=20 rows=10 name=asset_comment></textarea></td></tr>';
		echo '</form>';
	}
	else
	{
		printOpFormIntro ('upd');
		echo '<tr>';
		echo '<td rowspan=4 valign=top>';
		if ($asset['refc'] == 0)
			echo getOpLink (array ('op' => 'del'), '', 'CUT', 'unregister asset', FALSE);
		else
			echo getImageHREF ('CUT gray', $asset['refc'] . ' value(s) assigned');
		echo '</td>';
		echo '<td rowspan=4>' . getSelect (getISO27001AssetGroupOptions(), array ('name' => 'agroup_id', 'size' => getConfigVar ('MAXSELSIZE')), $asset['agroup_id']) . '</td>';
		echo '<td rowspan=4>' . getSelect (getISO27001AssetOwnerOptions(), array ('name' => 'aowner_id', 'size' => getConfigVar ('MAXSELSIZE')), $asset['aowner_id']) . '</td>';
		echo '<td rowspan=4>' . getSelect (getISO27001AssetMaintainerOptions(), array ('name' => 'amaint_id', 'size' => getConfigVar ('MAXSELSIZE')), $asset['amaint_id']) . '</td>';
		echo '<td valign=top><input type=text name=criticality width=5 value="' . $asset['criticality'] . '"></td>';
		echo '<td rowspan=4 valign=top>' . getImageHREF ('SAVE', 'save changes', TRUE) . '</td>';
		echo '</tr>';
		echo '<tr><td>&nbsp;</td></tr>';
		echo '<tr><th>comment</th></tr>';
		echo '<tr><td valign=top><textarea cols=20 rows=10 name=asset_comment>' . stringForTextarea ($asset['asset_comment']) . '</textarea></td></tr>';
		echo '</form>';
	}
	echo '</table>';
}

function renderObjectISO27001CValues()
{
	function sameTypeCompleteAssetOptions ($realm, $object_id, $objtype_id)
	{
		$ret = array();
		foreach (getISO27001AssetList() as $a)
			if ($a['objtype_id'] == $objtype_id && $a['object_id'] != $object_id)
				switch ($a['objtype_id'])
				{
					case 1561: // row
					case 1562: // location
						throw new RackTablesError ("unexpected object type '${a['objtype_id']}'", RackTablesError::INTERNAL);
					default: // a rack or an object
						if ($a['incomplete'] == 0)
							$ret["${realm}-${a['object_id']}"] = formatEntityName (spotEntity ($realm, $a['object_id']));
				}
		natsort ($ret);
		return count ($ret) == 0 ? array() : array ('other' => $ret);
	} // sameTypeCompleteAssetOptions()

	function allAssetOptions ($realm, $object_id, $objtype_id)
	{
		$ret = array();
		$tmp = array
		(
			'same_c' => array(),
			'same_i' => array(),
			'different_c' => array(),
			'different_i' => array(),
		);
		foreach (getISO27001AssetList() as $a)
		{
			if ($a['object_id'] == $object_id)
				continue;
			switch ($a['objtype_id'])
			{
				case 1561: // row
				case 1562: // location
					throw new RackTablesError ("unexpected object type '${a['objtype_id']}'", RackTablesError::INTERNAL);
				case 1560: // rack
					$cell = spotEntity ('rack', $a['object_id']);
					$group = $realm == 'rack' ? 'same' : 'different';
					$dname = $cell['name'];
					break;
				default: // object
					$cell = spotEntity ('object', $a['object_id']);
					$group = ($realm == 'object' && $objtype_id == $a['objtype_id']) ? 'same' : 'different';
					$dname = $cell['dname'];
					break;
			}
			$group .= $cell['iso27001_incomplete'] == 0 ? '_c' : '_i';
			$tmp[$group]["${cell['realm']}-${a['object_id']}"] = stringForLabel ($dname);
		}
		$kmap = array
		(
			'same_c' => 'same type assets, complete',
			'same_i' => 'same type assets, incomplete',
			'different_c' => 'all other assets, complete',
			'different_i' => 'all other assets, incomplete',
		);
		foreach ($kmap as $oldkey => $newkey)
			if (count ($tmp[$oldkey]) > 0)
			{
				asort ($tmp[$oldkey], SORT_NATURAL);
				$ret[$newkey] = $tmp[$oldkey];
			}
		return $ret;
	} // allAssetOptions()

	function renderCopy()
	{
		startPortlet ('copy from');
		$asset = getISO27001AssetInfo (getBypassValue());
		$realm = etypeByPageno();
		// Note that in the two calls below object_id is used in the sense of ISO27001Asset or Object,
		// not just RackObject, i.e. it may refer to a rack.
		if (! isCheckSet ('all_sources'))
		{
			$href = makeHref (makePageParams (array ('all_sources' => 'on')));
			$header = "Currently listing only the same type complete assets (<a href='${href}'>show all assets</a>).";
			$options = sameTypeCompleteAssetOptions ($realm, $asset['object_id'], $asset['objtype_id']);
		}
		else
		{
			$href = makeHref (makePageParams (array ('all_sources' => 'off')));
			$header = "Currently listing all assets (<a href='${href}'>show only the same type complete assets</a>).";
			$options = allAssetOptions ($realm, $asset['object_id'], $asset['objtype_id']);
		}
		echo '<table border=0 align=center>';
		if (0 == count ($options))
			echo '<tr><td>(none)</td></tr>';
		else
		{
			printOpFormIntro ('save', array ('form_mode' => 'copy'));
			echo "<tr><td>${header}</td></tr>";
			echo '<tr><td>' . getNiftySelect ($options, array ('name' => 'copy_from', 'size' => getConfigVar ('MAXSELSIZE'))) . '</td></tr>';
			echo '<tr><td>' . getImageHREF ('next', 'copy all values from the asset above', TRUE) . '</td></tr>';
			echo '</form>';
		}
		echo '</table>';
		finishPortlet();
	} // renderCopy()

	function renderEdit()
	{
		function getValueTD ($input_attrs, $selected_class, $value, $curval)
		{
			$extra_class = $curval === $value ? " ${selected_class}" : '';
			$checked = $curval === $value ? ' checked' : '';
			$input = "<input type=radio value=${value}${checked}";
			foreach ($input_attrs as $attr => $value)
				$input .= " ${attr}=${value}";
			$input .= '>';
			return "<td class='tdleft criteriabox${extra_class}'>${input}</td>";
		}

		global $nextorder;
		addJS (<<<'ENDOFTEXT'
function checkColumnOfRadios (prefix, numRows, suffix)
{
	var elemId;
	for (var i=0; i < numRows; i++)
	{
		elemId = prefix + i + suffix;
		if (document.getElementById(elemId) == null || document.getElementById(elemId).disabled == true)
			continue;
		document.getElementById(elemId).checked = true;
	}
}
ENDOFTEXT
, TRUE);

		startPortlet ('edit');
		$conf = getISO27001Configuration();
		$object_id = getBypassValue();
		$vals = getObjectISO27001CValues ($object_id);
		$cgids = reduceSubarraysToColumn (reindexById (getISO27001CriterionList()), 'cgroup_id');
		printOpFormIntro ('save', array ('form_mode' => 'edit'));
		echo '<table border=0 cellspacing=0 cellpadding=3 align=center>';
		$i = 0;
		foreach ($conf as $cgroup_id => $cgroup)
		{
			// A group without any criteria does not get in the way of ISO 27001 valuation,
			// but a group with at least one criterion does, even if it has no values configured.
			if (0 == $nc = count ($cgroup['criteria']))
				continue;
			echo "<tr><td colspan=2><table border=0 cellpadding=5 cellspacing=0 width='100%' class='withcursor'>";
			echo '<tr>';
			echo '<th colspan=2 width="50%" class=tdleft valign=top>' . stringForLabel ($cgroup['name'], 64) . '</th>';
			echo '<th width="*" valign=top>';
			if ($nc < 2)
				echo "<label for=cgid_${cgroup['id']}_row_0_val_unset>unset";
			else
				echo "<label>unset<br><input type=radio name=column_radio_${cgroup['id']} value=unset " .
					"onclick=\"checkColumnOfRadios('cgid_${cgroup['id']}_row_', ${nc}, '_val_unset')\">";
			echo '</label></th>';
			foreach ($cgroup['values'] as $v)
			{
				echo '<th width="*" valign=top>';
				if ($nc < 2)
					echo "<label for=cgid_${cgroup['id']}_row_0_val_${v['value']}>" . formatISO27001CVAsLabel ($v);
				else
					echo "<label>" . formatISO27001CVAsLabel ($v) .
						"<br><input type=radio name=column_radio_${cgroup['id']} value=${v['value']} " .
						"onclick=\"checkColumnOfRadios('cgid_${cgroup['id']}_row_', ${nc}, '_val_${v['value']}')\">";
				echo '</label></th>';
			}
			echo '</tr>';
			$order = 'odd';
			$rownum = 0;
			foreach ($cgroup['criteria'] as $criterion_id => $criterion)
			{
				$input_attrs = array ('name' => "cval_${i}");
				$moreinfo = array
				(
					array ('tag' => '$iso27001_cid_' . $criterion_id),
					array ('tag' => '$iso27001_cgid_' . $cgids[$criterion_id]),
				);
				if (permitted (NULL, NULL, NULL, $moreinfo))
				{
					$tr_class = "row_${order}";
					echo "<input type=hidden name=cid_${i} value=${criterion_id}>";
				}
				else
				{
					$tr_class = "row_${order} disabled";
					$input_attrs['disabled'] = 1;
				}
				echo "<tr class='${tr_class}'>";
				$name = stringForLabel ($criterion['name'], 0);
				if ($criterion['comment'] != '')
					$name = '<span class="hover-history underline" title="' . stringForLabel ($criterion['comment'], 0) . '">' . $name . '</span>';
				echo '<td width="40%" class=tdleft>' . $name . '</td>';
				echo '<td width="10%">' . ($criterion['weight'] == 1.0 ? '&nbsp;' : sprintf (' (&times;%.2f)', $criterion['weight'])) . '</td>';
				$curval = $vals[$criterion_id] === NULL ? 'unset' : $vals[$criterion_id];
				$input_attrs['id'] = "cgid_${cgroup['id']}_row_${rownum}_val_unset";
				echo getValueTD ($input_attrs, 'unset', 'unset', $curval);
				foreach ($cgroup['values'] as $valinfo)
				{
					$input_attrs['id'] = "cgid_${cgroup['id']}_row_${rownum}_val_${valinfo['value']}";
					echo getValueTD ($input_attrs, 'selected', $valinfo['value'], $curval);
				}
				echo '</tr>';
				$order = $nextorder[$order];
				$i++;
				$rownum++;
			}
			echo '</table></td></tr>';
		}

		$asset = getISO27001AssetInfo ($object_id);
		echo '<tr><td colspan=2><textarea cols=10 name=cvalues_comment style="width: 100%;">';
		echo stringForTextarea ($asset['cvalues_comment']) . '</textarea></td></tr>';

		echo '<tr><td class=tdleft>';
		printImageHREF ('SAVE', 'save changes', TRUE);
		echo "<input type=hidden name=numcrit value=${i}></form>";
		echo '</td><td class=tdright>';
		$reset_options = array ('cvalues_comment' => '');
		$i = 0;
		foreach ($conf as $cgroup_id => $cgroup)
			foreach ($cgroup['criteria'] as $c)
				if ($vals[$c['id']] !== NULL) // To keep the URL shorter omit the criteria that are already unset.
				{
					$reset_options["cid_${i}"] = $c['id'];
					$reset_options["cval_${i}"] = 'unset';
					$i++;
				}
		if (count ($reset_options) == 0)
			printImageHREF ('CLEAR gray', 'nothing to reset');
		else
		{
			$reset_options['op'] = 'save';
			$reset_options['form_mode'] = 'edit';
			$reset_options['numcrit'] = $i;
			echo getOpLink ($reset_options, '' ,'CLEAR', 'reset all values', 'need-confirmation');
		}
		echo '</td></tr>';
		echo '</table>';
		finishPortlet();
	} // renderEdit()

	echo '<table class=objview border=0 width="100%"><tr><td class=pcleft width="30%">';
	renderCopy();
	echo '</td><td class=pcleft width="*">';
	renderEdit();
	echo '</td></tr></table>';
}

function renderISO27001ValuationReport()
{
	function printGrid4x4TD ($c1, $c2, $c3, $c4)
	{
		echo '<td class=zeropadding><table width="100%" height="100%" cellspacing=0 cellpadding=0>';
		echo "<tr><td class='grid${c1}'>&nbsp;</td><td class='grid${c2}'>&nbsp;</td></tr>";
		echo "<tr><td class='grid${c3}'>&nbsp;</td><td class='grid${c4}'>&nbsp;</td></tr>";
		echo '</table></td>';
	}

	function printGridRow ($strokes, $nverticals, $ncolumns)
	{
		for ($i = 0; $i + 0 + $nverticals < $ncolumns; $i++)
			$stroke = array_shift ($strokes);
		for ($i = 0; $i + 1 + $nverticals < $ncolumns; $i++)
			printGrid4x4TD ('', '', " top-${stroke}", " top-${stroke}"); // -
		if ($nverticals < $ncolumns)
			printGrid4x4TD ('', '', " top-${stroke} right-${stroke}", ''); // +
		for ($i = 0; $i < $nverticals; $i++)
		{
			$stroke = array_shift ($strokes);
			printGrid4x4TD (" right-${stroke}", '', " right-${stroke}", ''); // |
		}
	}

	global $nextorder, $pageno, $tabno;
	$conf = getISO27001Configuration();
	$fheaders = array ('group', 'name', 'tag', 'owner', 'location');
	$nfh = count ($fheaders);
	$minthr = array_key_exists ('min_threshold', $_REQUEST) ? genericAssertion ('min_threshold', 'decimal') : 0.0;

	$headers = array ("<th class=tdright colspan=${nfh}>criticality</th>");
	$strokes = array ('solid');
	foreach ($conf as $cgroup)
	{
		foreach ($cgroup['criteria'] as $c)
		{
			$cn = stringForTD ($c['name'], 64);
			if ($c['weight'] != 1.0)
				$cn .= sprintf (' (&times;%.2f)', $c['weight']);
			$headers[] = "<td class=tdright colspan=${nfh}>" . $cn . '</td>';
			$strokes[] = 'dotted';
		}
		$headers[] = "<th class=tdright colspan=${nfh}>subtotal: " . stringForLabel ($cgroup['name'], 64) . '</th>';
		$strokes[] = 'solid';
	}
	$headers[] = "<th class=tdright  colspan=${nfh}>valuation</th>";
	$strokes[] = 'solid';
	$nh = count ($headers);

	echo '<br><table cellpadding=5 cellspacing=0 border=0 align=center class=withcursor>';
	echo '<form method=get>';
	echo "<input type=hidden name=page value='${pageno}'>";
	echo "<input type=hidden name=tab value='${tabno}'>";
	echo '<tr><td colspan=' . ($nfh + $nh) . ' class=tdcenter>Minimum valuation: ';
	echo "<input type=text name=min_threshold value='${minthr}'> <input type=submit value='apply'></td></tr>";
	echo '<tr><td colspan=' . ($nfh + $nh) . ' ><hr></td></tr>';
	echo '</form>';
	$i = 0;
	foreach (array_reverse ($headers) as $header)
	{
		echo '<tr>' . $header;
		printGridRow ($strokes, $i++, $nh);
		echo '</tr>';
	}
	echo "<tr><td colspan=${nfh}>&nbsp;</td>";
	printGridRow ($strokes, $nh, $nh);
	echo '</tr>';

	echo '<tr>';
	foreach ($fheaders as $fh)
		echo "<th>${fh}</th>";
	printGridRow ($strokes, $nh, $nh);
	echo '</tr>';

	$order = 'odd';
	$total = 0;
	foreach (getISO27001AssetList() as $tmp)
	{
		if ($tmp['incomplete'] != 0)
			continue;
		switch ($tmp['objtype_id'])
		{
			case 1561: // row
			case 1562: // location
				throw new RackTablesError ('unexpected object type', RackTablesError::INTERNAL);
			case 1560: // rack
				$o = spotEntity ('rack', $tmp['object_id']);
				break;
			default:
				$o = spotEntity ('object', $tmp['object_id']);
		}
		$a = getISO27001AssetInfo ($o['id']);
		$vals = getObjectISO27001CValues ($o['id']);
		$valuation = $a['criticality'] * getISO27001AssetValuation ($conf, $vals);
		if ($valuation < (float)$minthr)
			continue;
		switch ($o['realm'])
		{
			case 'rack':
				$location = $o['row_id'] == NULL ? 'N/A' : $o['row_name'];
				break;
			case 'object':
				if ($o['rack_id'] == NULL)
					$location = 'N/A';
				else
				{
					$rack = spotEntity ('rack', $o['rack_id']);
					$location = mkCellA ($rack);
				}
				break;
		}
		echo "<tr class=row_${order}>";
		echo '<td>' . stringForTD ($a['agroup_name']) . '</td>';
		echo '<td>' . mkCellA ($o) . '</td>';
		echo '<td>' . stringForTD ($o['asset_no']) . '</td>';
		echo '<td>' . stringForTD ($a['aowner_name']) . '</td>';
		echo '<td>' . $location . '</td>';
		echo '<th class=tdright>' . sprintf ('%.2f', $a['criticality']) . '</th>';
		$asum = 0.0;
		foreach ($conf as $cgroup)
		{
			$gsum = 0.0;
			foreach ($cgroup['criteria'] as $criterion)
			{
				echo '<td>' . $vals[$criterion['id']] . '</td>';
				$gsum += $vals[$criterion['id']] * $criterion['weight'];
			}
			echo '<th class=tdright>' . sprintf ('%.2f', $gsum) . '</th>';
			$asum += $gsum;
		}
		echo '<th class=tdright>' . sprintf ('%.2f', $a['criticality'] * $asum) . '</th>';
		echo '</tr>';
		$order = $nextorder[$order];
		$total += 1;
	} // for each asset
	$colspan = $nh + $nfh;
	echo "<tr><td colspan=${colspan}><hr></td></tr>";
	echo "<tr><td colspan=${colspan}>Total assets displayed: ${total}</td></tr>";
	echo '</table><br>';
}

// FIXME: This function treats all references as they were to objects, thus
// the results may be inaccurate.
function renderISO27001StatisticsReport()
{
	function renderStatsBlock ($ptitle, $htitle, $tcode, $rows)
	{
		startPortlet ($ptitle);
		$params = array ('page' => 'depot', 'tab' => 'default');
		if (! count ($rows))
			echo '(none)';
		else
		{
			echo '<table cellspacing=0 cellpadding=5 align=center class=widetable>';
			echo "<tr><th>${htitle}</th><th>objects</th></tr>";
			$total = 0;
			foreach ($rows as $row)
				if ($row['refc'] > 0)
				{
					echo '<tr>';
					echo '<td class=tdleft>' . stringForTD ($row['name'], 32) . '</td>';
					$params['cfe'] = '{$iso27001_' . $tcode . '_' . $row['id'] . '}';
					$href = makeHref ($params);
					echo "<td class=tdright><a href='${href}'>" . $row['refc'] . '</a></td>';
					echo '</tr>';
					$total += $row['refc'];
				}
			echo "<tr><td class=tdleft>Total</td><td class=tdright>${total}</td></tr>";
			echo '</table>';
		}
		finishPortlet();
	}

	echo '<table class=objview border=0 width="100%"><tr><td class=pcleft width="33%">';
	renderStatsBlock ('asset groups', 'group', 'agroup', getISO27001AssetGroupList());
	echo '</td><td class=pcleft width="33%">';
	renderStatsBlock ('asset owners', 'owner', 'aowner', getISO27001AssetOwnerList());
	echo '</td><td class=pcleft width="33%">';
	renderStatsBlock ('asset maintainers', 'maintainer', 'amaint', getISO27001AssetMaintainerList());
	echo '</td></tr></table>';
}
