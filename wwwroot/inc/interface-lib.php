<?php

# This file is a part of RackTables, a datacenter and server room management
# framework. See accompanying file "COPYING" for the full copyright and
# licensing information.

define ('TAGNAME_REGEXP', '/^[\p{L}0-9]([. _~-]?[\p{L}0-9])*$/u');
define ('AUTOTAGNAME_REGEXP', '/^\$[\p{L}0-9]([. _~-]?[\p{L}0-9])*$/u');

// Let's have it here, so extensions can add their own images.
$image = array();
$image['rackspace']['path'] = 'pix/racks.png';
$image['rackspace']['width'] = 218;
$image['rackspace']['height'] = 200;
$image['objects']['path'] = 'pix/server.png';
$image['objects']['width'] = 218;
$image['objects']['height'] = 200;
$image['depot']['path'] = 'pix/server.png';
$image['depot']['width'] = 218;
$image['depot']['height'] = 200;
$image['files']['path'] = 'pix/files.png';
$image['files']['width'] = 218;
$image['files']['height'] = 200;
$image['ipv4space']['path'] = 'pix/addressspace.png';
$image['ipv4space']['width'] = 218;
$image['ipv4space']['height'] = 200;
$image['ipv6space']['path'] = 'pix/addressspacev6.png';
$image['ipv6space']['width'] = 218;
$image['ipv6space']['height'] = 200;
$image['ipv4slb']['path'] = 'pix/slb.png';
$image['ipv4slb']['width'] = 218;
$image['ipv4slb']['height'] = 200;
$image['config']['path'] = 'pix/configuration.png';
$image['config']['width'] = 218;
$image['config']['height'] = 200;
$image['reports']['path'] = 'pix/report.png';
$image['reports']['width'] = 218;
$image['reports']['height'] = 200;
$image['8021q']['path'] = 'pix/8021q.png';
$image['8021q']['width'] = 218;
$image['8021q']['height'] = 200;
$image['objectlog']['path'] = 'pix/crystal-mimetypes-shellscript-218x200.png';
$image['objectlog']['width'] = 218;
$image['objectlog']['height'] = 200;
$image['virtual']['path'] = 'pix/virtualresources.png';
$image['virtual']['width'] = 218;
$image['virtual']['height'] = 200;
$image['download']['path'] = 'pix/download.png';
$image['download']['width'] = 16;
$image['download']['height'] = 16;
$image['DOWNLOAD']['path'] = 'pix/download-big.png';
$image['DOWNLOAD']['width'] = 32;
$image['DOWNLOAD']['height'] = 32;
$image['plug']['path'] = 'pix/tango-network-wired.png';
$image['plug']['width'] = 16;
$image['plug']['height'] = 16;
$image['cut']['path'] = 'pix/tango-edit-cut-16x16.png';
$image['cut']['width'] = 16;
$image['cut']['height'] = 16;
$image['Cut']['path'] = 'pix/tango-edit-cut-22x22.png';
$image['Cut']['width'] = 22;
$image['Cut']['height'] = 22;
$image['Cut gray']['path'] = 'pix/tango-edit-cut-22x22-gray.png';
$image['Cut gray']['width'] = 22;
$image['Cut gray']['height'] = 22;
$image['CUT']['path'] = 'pix/tango-edit-cut-32x32.png';
$image['CUT']['width'] = 32;
$image['CUT']['height'] = 32;
$image['add']['path'] = 'pix/tango-list-add.png';
$image['add']['width'] = 16;
$image['add']['height'] = 16;
$image['ADD']['path'] = 'pix/tango-list-add-big.png';
$image['ADD']['width'] = 32;
$image['ADD']['height'] = 32;
$image['delete']['path'] = 'pix/tango-list-remove.png';
$image['delete']['width'] = 16;
$image['delete']['height'] = 16;
$image['DELETE']['path'] = 'pix/tango-list-remove-32x32.png';
$image['DELETE']['width'] = 32;
$image['DELETE']['height'] = 32;
$image['destroy']['path'] = 'pix/tango-user-trash-16x16.png';
$image['destroy']['width'] = 16;
$image['destroy']['height'] = 16;
$image['nodestroy']['path'] = 'pix/tango-user-trash-16x16-gray.png';
$image['nodestroy']['width'] = 16;
$image['nodestroy']['height'] = 16;
$image['NODESTROY']['path'] = 'pix/tango-user-trash-32x32-gray.png';
$image['NODESTROY']['width'] = 32;
$image['NODESTROY']['height'] = 32;
$image['DESTROY']['path'] = 'pix/tango-user-trash-32x32.png';
$image['DESTROY']['width'] = 32;
$image['DESTROY']['height'] = 32;
$image['nodelete']['path'] = 'pix/tango-list-remove-shadow.png';
$image['nodelete']['width'] = 16;
$image['nodelete']['height'] = 16;
$image['inservice']['path'] = 'pix/tango-emblem-system.png';
$image['inservice']['width'] = 16;
$image['inservice']['height'] = 16;
$image['notinservice']['path'] = 'pix/tango-dialog-error.png';
$image['notinservice']['width'] = 16;
$image['notinservice']['height'] = 16;
$image['find']['path'] = 'pix/tango-system-search.png';
$image['find']['width'] = 16;
$image['find']['height'] = 16;
$image['next']['path'] = 'pix/tango-go-next.png';
$image['next']['width'] = 32;
$image['next']['height'] = 32;
$image['prev']['path'] = 'pix/tango-go-previous.png';
$image['prev']['width'] = 32;
$image['prev']['height'] = 32;
$image['COMMIT']['path'] = 'pix/tango-go-prev-next-32x32.png';
$image['COMMIT']['width'] = 32;
$image['COMMIT']['height'] = 32;
$image['COMMIT gray']['path'] = 'pix/tango-go-prev-next-gray-32x32.png';
$image['COMMIT gray']['width'] = 32;
$image['COMMIT gray']['height'] = 32;
$image['RECALC']['path'] = 'pix/tango-view-refresh-32x32.png';
$image['RECALC']['width'] = 32;
$image['RECALC']['height'] = 32;
$image['clear']['path'] = 'pix/tango-edit-clear.png';
$image['clear']['width'] = 16;
$image['clear']['height'] = 16;
$image['CLEAR']['path'] = 'pix/tango-edit-clear-big.png';
$image['CLEAR']['width'] = 32;
$image['CLEAR']['height'] = 32;
$image['CLEAR gray']['path'] = 'pix/tango-edit-clear-gray-32x32.png';
$image['CLEAR gray']['width'] = 32;
$image['CLEAR gray']['height'] = 32;
$image['save']['path'] = 'pix/tango-document-save-16x16.png';
$image['save']['width'] = 16;
$image['save']['height'] = 16;
$image['SAVE']['path'] = 'pix/tango-document-save-32x32.png';
$image['SAVE']['width'] = 32;
$image['SAVE']['height'] = 32;
$image['NOSAVE']['path'] = 'pix/tango-document-save-32x32-gray.png';
$image['NOSAVE']['width'] = 32;
$image['NOSAVE']['height'] = 32;
$image['create']['path'] = 'pix/tango-document-new.png';
$image['create']['width'] = 16;
$image['create']['height'] = 16;
$image['CREATE']['path'] = 'pix/tango-document-new-big.png';
$image['CREATE']['width'] = 32;
$image['CREATE']['height'] = 32;
$image['DENIED']['path'] = 'pix/tango-dialog-error-big.png';
$image['DENIED']['width'] = 32;
$image['DENIED']['height'] = 32;
$image['node-collapsed']['path'] = 'pix/node-collapsed.png';
$image['node-collapsed']['width'] = 16;
$image['node-collapsed']['height'] = 16;
$image['node-expanded']['path'] = 'pix/node-expanded.png';
$image['node-expanded']['width'] = 16;
$image['node-expanded']['height'] = 16;
$image['node-expanded-static']['path'] = 'pix/node-expanded-static.png';
$image['node-expanded-static']['width'] = 16;
$image['node-expanded-static']['height'] = 16;
$image['dragons']['path'] = 'pix/mitsudragon.png';
$image['dragons']['width'] = 195;
$image['dragons']['height'] = 33;
$image['LB']['path'] = 'pix/loadbalancer.png';
$image['LB']['width'] = 32;
$image['LB']['height'] = 32;
$image['RS pool']['path'] = 'pix/serverpool.png';
$image['RS pool']['width'] = 48;
$image['RS pool']['height'] = 16;
$image['VS']['path'] = 'pix/servicesign.png';
$image['VS']['width'] = 39;
$image['VS']['height'] = 62;
$image['router']['path'] = 'pix/router.png';
$image['router']['width'] = 32;
$image['router']['height'] = 32;
$image['object']['path'] = 'pix/bracket-16x16.png';
$image['object']['width'] = 16;
$image['object']['height'] = 16;
$image['OBJECT']['path'] = 'pix/bracket-32x32.png';
$image['OBJECT']['width'] = 32;
$image['OBJECT']['height'] = 32;
$image['attach']['path'] = 'pix/tango-mail-attachment-16x16.png';
$image['attach']['width'] = 16;
$image['attach']['height'] = 16;
$image['Attach']['path'] = 'pix/tango-mail-attachment-22x22.png';
$image['Attach']['width'] = 22;
$image['Attach']['height'] = 22;
$image['ATTACH']['path'] = 'pix/tango-mail-attachment-32x32.png';
$image['ATTACH']['width'] = 32;
$image['ATTACH']['height'] = 32;
$image['favorite']['path'] = 'pix/tango-emblem-favorite.png';
$image['favorite']['width'] = 16;
$image['favorite']['height'] = 16;
$image['computer']['path'] = 'pix/tango-computer.png';
$image['computer']['width'] = 16;
$image['computer']['height'] = 16;
$image['empty file']['path'] = 'pix/crystal-file-empty-32x32.png';
$image['empty file']['width'] = 32;
$image['empty file']['height'] = 32;
$image['text file']['path'] = 'pix/crystal-file-text-32x32.png';
$image['text file']['width'] = 32;
$image['text file']['height'] = 32;
$image['image file']['path'] = 'pix/crystal-file-image-32x32.png';
$image['image file']['width'] = 32;
$image['image file']['height'] = 32;
$image['text']['path'] = 'pix/tango-text-x-generic-16x16.png';
$image['text']['width'] = 16;
$image['text']['height'] = 16;
$image['NET']['path'] = 'pix/crystal-network_local-32x32.png';
$image['NET']['width'] = 32;
$image['NET']['height'] = 32;
$image['net']['path'] = 'pix/crystal-network_local-16x16.png';
$image['net']['width'] = 16;
$image['net']['height'] = 16;
$image['USER']['path'] = 'pix/crystal-edit-user-32x32.png';
$image['USER']['width'] = 32;
$image['USER']['height'] = 32;
$image['setfilter']['path'] = 'pix/pgadmin3-viewfiltereddata.png';
$image['setfilter']['width'] = 32;
$image['setfilter']['height'] = 32;
$image['setfilter gray']['path'] = 'pix/pgadmin3-viewfiltereddata-grayscale.png';
$image['setfilter gray']['width'] = 32;
$image['setfilter gray']['height'] = 32;
$image['resetfilter']['path'] = 'pix/pgadmin3-viewdata.png';
$image['resetfilter']['width'] = 32;
$image['resetfilter']['height'] = 32;
$image['resetfilter gray']['path'] = 'pix/pgadmin3-viewdata-grayscale.png';
$image['resetfilter gray']['width'] = 32;
$image['resetfilter gray']['height'] = 32;
$image['knight']['path'] = 'pix/smiley_knight.png';
$image['knight']['width'] = 72;
$image['knight']['height'] = 33;
$image['Zoom']['path'] = 'pix/tango-system-search-22x22.png';
$image['Zoom']['width'] = 22;
$image['Zoom']['height'] = 22;
$image['Zooming']['path'] = 'pix/tango-view-fullscreen-22x22.png';
$image['Zooming']['width'] = 22;
$image['Zooming']['height'] = 22;
$image['UNLOCK']['path'] = 'pix/crystal-actions-unlock-32x32.png';
$image['UNLOCK']['width'] = 32;
$image['UNLOCK']['height'] = 32;
$image['CLOCK']['path'] = 'pix/tango-appointment-32x32.png';
$image['CLOCK']['width'] = 32;
$image['CLOCK']['height'] = 32;
$image['DQUEUE done']['path'] = 'pix/crystal-ok-32x32.png';
$image['DQUEUE done']['width'] = 32;
$image['DQUEUE done']['height'] = 32;
$image['DQUEUE sync_aging']['path'] = 'pix/tango-appointment-32x32.png';
$image['DQUEUE sync_aging']['width'] = 32;
$image['DQUEUE sync_aging']['height'] = 32;
$image['DQUEUE resync_aging']['path'] = 'pix/tango-appointment-32x32.png';
$image['DQUEUE resync_aging']['width'] = 32;
$image['DQUEUE resync_aging']['height'] = 32;
$image['DQUEUE sync_ready']['path'] = 'pix/tango-emblem-system-32x32.png';
$image['DQUEUE sync_ready']['width'] = 32;
$image['DQUEUE sync_ready']['height'] = 32;
$image['DQUEUE resync_ready']['path'] = 'pix/tango-emblem-important-32x32.png';
$image['DQUEUE resync_ready']['width'] = 32;
$image['DQUEUE resync_ready']['height'] = 32;
$image['DQUEUE failed']['path'] = 'pix/tango-emblem-unreadable-32x32.png';
$image['DQUEUE failed']['width'] = 32;
$image['DQUEUE failed']['height'] = 32;
$image['DQUEUE disabled']['path'] = 'pix/tango-emblem-readonly-32x32.png';
$image['DQUEUE disabled']['width'] = 32;
$image['DQUEUE disabled']['height'] = 32;
$image['copy']['path'] = 'pix/tango-edit-copy-16x16.png';
$image['copy']['width'] = 16;
$image['copy']['height'] = 16;
$image['COPY']['path'] = 'pix/tango-edit-copy-32x32.png';
$image['COPY']['width'] = 32;
$image['COPY']['height'] = 32;
$image['html']['path'] = 'pix/tango-text-html.png';
$image['html']['width'] = 16;
$image['html']['height'] = 16;
$image['pencil']['path'] = 'pix/pencil-icon.png';
$image['pencil']['width'] = 12;
$image['pencil']['height'] = 12;

$page_by_realm = array();
$page_by_realm['object'] = 'depot';
$page_by_realm['rack'] = 'rackspace';
$page_by_realm['ipv4net'] = 'ipv4space';
$page_by_realm['ipv6net'] = 'ipv6space';
$page_by_realm['ipv4vs'] = 'ipv4slb';
$page_by_realm['ipv4rspool'] = 'ipv4slb';
$page_by_realm['file'] = 'files';
$page_by_realm['user'] = 'userlist';

function printSelect ($optionList, $select_attrs = array(), $selected_id = NULL)
{
	echo getSelect ($optionList, $select_attrs, $selected_id);
}

// Input array keys are OPTION VALUEs and input array values are OPTION text.
function getSelect ($optionList, $select_attrs = array(), $selected_id = NULL, $treat_single_special = TRUE)
{
	$ret = '';
	if (!array_key_exists ('name', $select_attrs))
		return '';
	// handle two corner cases in a specific way
	if (count ($optionList) == 0)
		return '(none)';
	if (count ($optionList) == 1 && $treat_single_special)
	{
		foreach ($optionList as $key => $value) { break; }
		return "<input type=hidden name=${select_attrs['name']} id=${select_attrs['name']} value=${key}>" . $value;
	}
	if (!array_key_exists ('id', $select_attrs))
		$select_attrs['id'] = $select_attrs['name'];
	$ret .= '<select';
	foreach ($select_attrs as $attr_name => $attr_value)
		$ret .= " ${attr_name}=${attr_value}";
	$ret .= '>';
	foreach ($optionList as $dict_key => $dict_value)
		$ret .= "<option value='${dict_key}'" . ($dict_key == $selected_id ? ' selected' : '') . ">${dict_value}</option>";
	$ret .= '</select>';
	return $ret;
}

function printNiftySelect ($groupList, $select_attrs = array(), $selected_id = NULL, $autocomplete = false)
{
	echo getNiftySelect ($groupList, $select_attrs, $selected_id);
}

// Input is a cooked list of OPTGROUPs, each with own sub-list of OPTIONs in the same
// format as printSelect() expects.
// If tree is true, hierarchical drop-boxes are used, otherwise optgroups are used.
function getNiftySelect ($groupList, $select_attrs, $selected_id = NULL, $tree = false)
{
	// special treatment for ungrouped data
	if (count ($groupList) == 1 and isset ($groupList['other']))
		return getSelect ($groupList['other'], $select_attrs, $selected_id);
	if (!array_key_exists ('name', $select_attrs))
		return '';
	if (!array_key_exists ('id', $select_attrs))
		$select_attrs['id'] = $select_attrs['name'];
	if ($tree)
	{
		# it is safe to call many times for the same file
		addJS ('js/jquery.optionTree.js');
		$ret  = "<input type=hidden name=${select_attrs['name']}>\n";
		$ret .= "<script type='text/javascript'>\n";
		$ret .= "\$(function() {\n";
		$ret .= "    var option_tree = {\n";
		foreach ($groupList as $groupname => $groupdata)
		{
			$ret .= "        '${groupname}': {";
			foreach ($groupdata as $dict_key => $dict_value)
				$ret .= "\"${dict_value}\":'${dict_key}', ";
			$ret .= "},\n";
		}
		$ret .= "    };\n";
		$ret .= "    var options = {empty_value: '', choose: 'select...'};\n";
		$ret .= "    \$('input[name=${select_attrs['name']}]').optionTree(option_tree, options);\n";
		$ret .= "});\n";
		$ret .= "</script>\n";
	}
	else
	{
		$ret = '<select';
		foreach ($select_attrs as $attr_name => $attr_value)
			$ret .= " ${attr_name}=${attr_value}";
		$ret .= ">\n";
		foreach ($groupList as $groupname => $groupdata)
		{
			$ret .= "<optgroup label='${groupname}'>\n";
			foreach ($groupdata as $dict_key => $dict_value)
				$ret .= "<option value='${dict_key}'" . ($dict_key == $selected_id ? ' selected' : '') . ">${dict_value}</option>\n";
			$ret .= "</optgroup>\n";
		}
		$ret .= "</select>\n";
	}
	return $ret;
}

function getOptionTree ($tree_name, $tree_options, $tree_config = array())
{
	function serializeJSArray ($options)
	{
		$tmp = array();
		foreach ($options as $key => $value)
			$tmp[] = "'${key}': \"${value}\"";
		return '{' . implode (', ', $tmp) . "}\n";
	}
	function serializeJSTree ($tree_options)
	{
		$self = __FUNCTION__;
		$tmp = array();
		# Leaves on the PHP tree are stored "value => label" way,
		# non-leaves are stored "label => array" way, and the JS
		# tree is always built "label => value" or "label => array"
		# way, hence a structure transform is required.
		foreach ($tree_options as $key => $value)
			$tmp[] = is_array ($value) ?
				'"' . str_replace ('"', '\"', $key) . '": ' . $self ($value) :
				'"' . str_replace ('"', '\"', $value) . '": "' . str_replace ('"', '\"', $key) . '"';
		return '{' . implode (', ', $tmp) . "}\n";
	}

	$default_config = array
	(
		'choose' => 'select...',
		'empty_value' => '',
	);
	foreach ($tree_config as $cfgoption_name => $cfgoption_value)
		$default_config[$cfgoption_name] = $cfgoption_value;
	# it is safe to call many times for the same file
	addJS ('js/jquery.optionTree.js');
	$ret  = "<input type=hidden name=${tree_name}>\n";
	$ret .= "<script type='text/javascript'>\n";
	$ret .= "\$(function() {\n";
	$ret .= "    var option_tree = " . serializeJSTree ($tree_options) . ";\n";
	$ret .= "    var options = " . serializeJSArray ($default_config) . ";\n";
	$ret .= "    \$('input[name=${tree_name}]').optionTree(option_tree, options);\n";
	$ret .= "});\n";
	$ret .= "</script>\n";
	return $ret;
}

function printImageHREF ($tag, $title = '', $do_input = FALSE, $tabindex = 0)
{
	echo getImageHREF ($tag, $title, $do_input, $tabindex);
}

// this would be better called mkIMG(), make "IMG" HTML element
function getImageHREF ($tag, $title = '', $do_input = FALSE, $tabindex = 0)
{
	global $image;
	if (!isset ($image[$tag]))
		$tag = 'error';
	$img = $image[$tag];
	$img['path'] = '?module=chrome&uri=' . $img['path'];
	if ($do_input == TRUE)
		return
			"<input type=image name=submit class=icon " .
			"src='${img['path']}' " .
			"border=0 " .
			($tabindex ? "tabindex=${tabindex}" : '') .
			(!strlen ($title) ? '' : " title='${title}'") . // JT: Add title to input hrefs too
			">";
	else
		return
			"<img " .
			"src='${img['path']}' " .
			"width=${img['width']} " .
			"height=${img['height']} " .
			"border=0 " .
			(!strlen ($title) ? '' : "title='${title}'") .
			">";
}

function dos2unix ($text)
{
	return str_replace ("\r\n", "\n", $text);
}

function escapeString ($value, $do_db_escape = FALSE)
{
	$ret = htmlspecialchars ($value, ENT_QUOTES, 'UTF-8');
	if ($do_db_escape)
	{
		global $dbxlink;
		$ret = substr ($dbxlink->quote ($ret), 1, -1);
	}
	return $ret;
}

function transformRequestData()
{
	global $sic;
	// Magic quotes feature is deprecated, but just in case the local system
	// still has it activated, reverse its effect.
	$do_magic_quotes = (function_exists ('get_magic_quotes_gpc') and get_magic_quotes_gpc());
	$seen_keys = array();

	// Escape any globals before we ever try to use them, but keep a copy of originals.
	$sic = array();
	// walk through merged GET and POST instead of REQUEST array because it
	// can contain cookies with data which could not be decoded from UTF-8
	foreach (array_merge($_GET, $_POST) as $key => $value)
	{
		if (is_array ($value))
			$_REQUEST[$key] = $value;
		else
		{
			$value = dos2unix ($value);
			if ($do_magic_quotes)
				$value = stripslashes ($value);
			$_REQUEST[$key] = escapeString ($value);
		}
		$sic[$key] = $value;
		$seen_keys[$key] = 1;
	}

	// delete cookie information from the $_REQUEST array
	foreach (array_keys ($_REQUEST) as $key)
		if (! isset ($seen_keys[$key]))
			unset ($_REQUEST[$key]);

	if (isset ($_SERVER['PHP_AUTH_USER']))
		$_SERVER['PHP_AUTH_USER'] = escapeString ($_SERVER['PHP_AUTH_USER']);
	if (isset ($_SERVER['REMOTE_USER']))
		$_SERVER['REMOTE_USER'] = escapeString ($_SERVER['REMOTE_USER']);
}

// JS scripts should be included through this function.
// They automatically appear in the <head> of your page.
// $data is a JS filename, or JS code w/o tags around, if $inline = TRUE
// Scripts are included in the order of adding within the same group, and groups are sorted alphabetically.
function addJS ($data, $inline = FALSE, $group = 'default')
{
	static $javascript = array();
	static $seen_filenames = array();
	
	if (! isset ($data))
	{
		ksort ($javascript);
		return $javascript;
	}
	// Add jquery.js and racktables.js the first time a Javascript file is added.
	if (empty($javascript))
	{
		$javascript = array
		(
			'a_core' => array
			(
				array('type' => 'file', 'script' => 'js/jquery-1.4.4.min.js'),
				array('type' => 'file', 'script' => 'js/racktables.js'),
			),
		);

		// initialize core js filelist
		foreach ($javascript as $group_name => $group_array)
			foreach ($group_array as $item)
				if ($item['type'] == 'file')
					$seen_filenames[$item['script']] = 1;
	}

	if ($inline)
		$javascript[$group][] = array
		(
			'type' => 'inline',
			'script' => $data,
		);
	elseif (! isset ($seen_filenames[$data]))
	{
		$javascript[$group][] = array
		(
			'type' => 'file',
			'script' => $data,
		);
		$seen_filenames[$data] = 1;
	}
}

// CSS styles should be included through this function.
// They automatically appear in the <head> of your page.
// $data is a CSS filename, or CSS code w/o tags around, if $inline = TRUE
// Styles are included in the order of adding.
function addCSS ($data, $inline = FALSE)
{
	static $styles = array();
	static $seen_filenames = array();
	
	if (! isset ($data))
		return $styles;
	if ($inline)
		$styles[] = array
		(
			'type' => 'inline',
			'style' => $data,
		);
	elseif (! isset ($seen_filenames[$data]))
	{
		$styles[] = array
		(
			'type' => 'file',
			'style' => $data,
		);
		$seen_filenames[$data] = 1;
	}
}

function getRenderedIPNetCapacity ($range)
{
	switch (strlen ($range['ip_bin']))
	{
		case 4:  return getRenderedIPv4NetCapacity ($range);
		case 16: return getRenderedIPv6NetCapacity ($range);
		default: throw new InvalidArgException ('range["ip_bin"]', $range['ip_bin'], "Invalid binary IP");
	}
}

function getRenderedIPv4NetCapacity ($range)
{
	$class = 'net-usage';
	if (isset ($range['addrc']))
	{
		// full mode
		// $a is "aquamarine zone", $b is "gray zone"
		$total = ip4_range_size ($range);

		// compute $a_total: own range size, without subranges
		if (empty ($range['spare_ranges']))
			$a_total = $total;
		else
		{
			$a_total = 0;
			foreach ($range['spare_ranges'] as $mask => $spare_list)
				$a_total = bcadd ($a_total, bcmul (count ($spare_list), ip4_mask_size ($mask)), 0);
		}
		$a_used = $range['own_addrc'];
		$b_total = bcsub ($total, $a_total, 0);
		$b_used = $range['addrc'] - $a_used;

		// generate link to progress bar image
		$width = 100;
		if ($total != 0)
		{
			$px_a = round (bcdiv ($a_total, $total, 4) * $width);
			$px1 = round (bcdiv ($a_used, $total, 4) * $width);
			$px2 = $px_a - $px1;
			$px3 = round (bcdiv ($b_used, $total, 4) * $width);
			if ($px3 + $px1 + $px2 > $width)
				$px3 = $width - $px1 - $px2;
		}
		else
			$px1 = $px2 = $px3 = 0;

		$title_items = array();
		$title2_items = array();
		if ($a_total != 0)
		{
			$title_items[] = "$a_used / $a_total";
			$title2_items[] = sprintf ("%d%% used", bcdiv ($a_used, $a_total, 4) * 100);
		}
		if ($b_total != 0)
		{
			$title_items[] = ($b_used ? "$b_used / " : "") . $b_total;
			$title2_items[] = sprintf ("%d%% sub-allocated", bcdiv ($b_total, $total, 4) * 100);
		}
		$title = implode (', ', $title_items);
		$title2 = implode (', ', $title2_items);
		$text = "<img width='$width' height=10 border=0 title='$title2' src='?module=progressbar4&px1=$px1&px2=$px2&px3=$px3'>" .
			" <small class='title'>$title</small>";
	}
	else
	{
		// fast mode
		$class .= ' pending';
		addJS ('js/net-usage.js');

		$free_text = '';
		if (isset ($range['kidc']) and $range['kidc'] > 0)
		{
			$free_masks = array_keys ($range['spare_ranges']);
			sort ($free_masks, SORT_NUMERIC);
			if ($mask = array_shift ($free_masks))
			{
				$cnt = count ($range['spare_ranges'][$mask]);
				$free_text = ', ' . ($cnt > 1 ? "<small>${cnt}x</small>" : "") . "/$mask free";
			}
		}
		$text =  ip4_range_size ($range) . $free_text;
	}

	$div_id = $range['ip'] . '/' . $range['mask'];

	return "<div class=\"$class\" id=\"$div_id\">" . $text . "</div>";
}

function getRenderedIPv6NetCapacity ($range)
{
	$div_id = $range['ip'] . '/' . $range['mask'];
	$class = 'net-usage';
	if (isset ($range['addrc']))
		$used = $range['addrc'];
	else
	{
		$used = NULL;
		$class .= ' pending';
		addJS ('js/net-usage.js');
	}

	static $prefixes = array
	(
		0 =>  '',
		3 =>  'k',
		6 =>  'M',
		9 =>  'G',
		12 => 'T',
		15 => 'P',
		18 => 'E',
		21 => 'Z',
		24 => 'Y',
	);

	if ($range['mask'] <= 64)
	{
		$what = 'net';
		$preposition = 'in';
		$range['mask'] += 64;
	}
	else
	{
		$what = 'IP';
		$preposition = 'of';
	}
	$what .= (0 == $range['mask'] % 64 ? '' : 's');
	$addrc = isset ($used) ? "$used $preposition " : '';

	$dec_order = intval ((128 - $range['mask']) / 10) * 3;
	$mult = isset ($prefixes[$dec_order]) ? $prefixes[$dec_order] : '??';
	
	$cnt = 1 << ((128 - $range['mask']) % 10);
	if ($cnt == 1 && $mult == '')
		$cnt = '1';

	return "<div class=\"$class\" id=\"$div_id\">" . "{$addrc}${cnt}${mult} ${what}" . "</div>";
}

// print part of HTML HEAD block
function printPageHeaders ()
{
	global $pageheaders;
	ksort ($pageheaders);
	foreach ($pageheaders as $s)
		echo $s . "\n";

	// add CSS styles
	foreach (addCSS (NULL) as $item)
		if ($item['type'] == 'inline')
			echo '<style type="text/css">' . "\n" . trim ($item['style'], "\r\n") . "\n</style>\n";
		elseif ($item['type'] == 'file')
			echo "<link rel=stylesheet type='text/css' href='?module=chrome&uri=${item['style']}' />\n";

	// add JS scripts
	foreach (addJS (NULL) as $group_name => $js_list)
		foreach ($js_list as $item)
			if ($item['type'] == 'inline')
				echo '<script type="text/javascript">' . "\n" . trim ($item['script'], "\r\n") . "\n</script>\n";
			elseif ($item['type'] == 'file')
				echo "<script type='text/javascript' src='?module=chrome&uri=${item['script']}'></script>\n";
}

function validTagName ($s, $allow_autotag = FALSE)
{
	if (1 == preg_match (TAGNAME_REGEXP, $s))
		return TRUE;
	if ($allow_autotag and 1 == preg_match (AUTOTAGNAME_REGEXP, $s))
		return TRUE;
	return FALSE;
}

function cmpTags ($a, $b)
{
	global $taglist;
	if (isset ($a['id']) && isset ($b['id']))
	{
		$a_root = array_first ($taglist[$a['id']]['trace']);
		$b_root = array_first ($taglist[$b['id']]['trace']);
		if ($a_root < $b_root)
			return -1;
		elseif ($a_root > $b_root)
			return 1;
	}
	elseif (isset ($a['id']))
		return -1;
	elseif (isset ($b['id']))
		return 1;

	return strcmp ($a['tag'], $b['tag']);
}

function serializeTags ($chain, $baseurl = '')
{
	$tmp = array();
	usort ($chain, 'cmpTags');
	foreach ($chain as $taginfo)
	{
		if ($baseurl == '')
			$tmp[] = $taginfo['tag'];
		else
		{
			$title = '';
			if (isset ($taginfo['user']) and isset ($taginfo['time']))
				$title = 'title="' . htmlspecialchars ($taginfo['user'] . ', ' . formatAge ($taginfo['time']), ENT_QUOTES) . '"';
			$tmp[] = "<a $title href='${baseurl}cft[]=${taginfo['id']}'>" . $taginfo['tag'] . "</a>";
		}
	}
	return implode (', ', $tmp);
}

function startPortlet ($title = '')
{
	echo "<div class=portlet><h2>${title}</h2>";
}

function finishPortlet ()
{
	echo "</div>\n";
}

function getPageName ($page_code)
{
	global $page;
	$title = isset ($page[$page_code]['title']) ? $page[$page_code]['title'] : callHook ('dynamic_title_decoder' ,$page_code);
	if (is_array ($title))
		$title = $title['name'];
	return $title;
}

function printTagTRs ($cell, $baseurl = '')
{
	if (getConfigVar ('SHOW_EXPLICIT_TAGS') == 'yes' and count ($cell['etags']))
	{
		echo "<tr><th width='50%' class=tagchain>Explicit tags:</th><td class=tagchain>";
		echo serializeTags ($cell['etags'], $baseurl) . "</td></tr>\n";
	}
	if (getConfigVar ('SHOW_IMPLICIT_TAGS') == 'yes' and count ($cell['itags']))
	{
		echo "<tr><th width='50%' class=tagchain>Implicit tags:</th><td class=tagchain>";
		echo serializeTags ($cell['itags'], $baseurl) . "</td></tr>\n";
	}
	if (getConfigVar ('SHOW_AUTOMATIC_TAGS') == 'yes' and count ($cell['atags']))
	{
		echo "<tr><th width='50%' class=tagchain>Automatic tags:</th><td class=tagchain>";
		echo serializeTags ($cell['atags']) . "</td></tr>\n";
	}
}

// stub function to override it by chain-connected hooks
function modifyEntitySummary ($cell, $summary)
{
	return $summary;
}

// renders 'summary' portlet, which persist on default tab of every realm page.
// $values is a tricky array.
// if its value is a string, it is treated as right td inner html, and the key is treated as left th text, colon appends there automatically.
// 'tags' key has a special meaning: instead of value, the result of printTagTRs call is appended to output
// if the value is a single-element array, its value rendered as-is instead of <tr> tag and all its contents.
// if the value is an array, its first 2 items are treated as left and right contents of row, no colon is appended. Used to enable non-unique titles
function renderEntitySummary ($cell, $title, $values = array())
{
	global $page_by_realm;
	// allow plugins to override summary table
	$values = callHook ('modifyEntitySummary', $cell, $values);

	startPortlet ($title);
	echo "<table border=0 cellspacing=0 cellpadding=3 width='100%'>\n";
	foreach ($values as $name => $value)
	{
		if (is_array ($value) and count ($value) == 1)
		{
			$value = array_shift ($value);
			echo $value;
			continue;
		}
		if (is_array ($value))
		{
			$name = array_shift ($value);
			$value = array_shift ($value);
		}
		elseif (! is_array ($value))
			$name .= ':';
		$class = 'tdright';
		$m = array();
		if (preg_match('/^\{(.*?)\}(.*)/', $name, $m))
		{
			$class .= ' ' . $m[1];
			$name = $m[2];
		}
		if ($name == 'tags:') 
		{
			$baseurl = '';
			if (isset ($page_by_realm[$cell['realm']]))
				$baseurl =  makeHref(array('page'=>$page_by_realm[$cell['realm']], 'tab'=>'default'))."&";
			printTagTRs ($cell, $baseurl);
		}
		else
			echo "<tr><th width='50%' class='$class'>$name</th><td class=tdleft>$value</td></tr>";
	}
	echo "</table>\n";
	finishPortlet();
}

function getOpLink ($params, $title,  $img_name = '', $comment = '', $class = '')
{
	if (isset ($params))
		$ret = '<a href="' . makeHrefProcess ($params) . '"';
	else
	{
		$ret = '<a href="#" onclick="return false;"';
		$class .= ' noclick';
	}
	if (! empty ($comment))
		$ret .= ' title="' . htmlspecialchars ($comment, ENT_QUOTES) . '"';
	$class = trim ($class);
	if (! empty ($class))
		$ret .= ' class="' . htmlspecialchars ($class, ENT_QUOTES) . '"';
	if (! empty ($comment))
		$ret .= 'title="' . htmlspecialchars($comment, ENT_QUOTES) . '"';
	$ret .= '>';
	if (! empty ($img_name))
	{
		$ret .= getImageHREF ($img_name, $comment);
		if (! empty ($title))
			$ret .= ' ';
	}
	$ret .= $title . '</a>';
	return $ret;
}

function renderProgressBar ($percentage = 0, $theme = '', $inline = FALSE)
{
	echo getProgressBar ($percentage, $theme, $inline);
}

function getProgressBar ($percentage = 0, $theme = '', $inline = FALSE)
{
	$done = ((int) ($percentage * 100));
	if (! $inline)
		$src = "?module=progressbar&done=$done" . (empty ($theme) ? '' : "&theme=${theme}");
	else
	{
		$bk_request = $_REQUEST;
		$_REQUEST['theme'] = $theme;
		$src = 'data:image/png;base64,' . chunk_split (base64_encode (getOutputOf ('renderProgressBarImage', $done)));
		$_REQUEST = $bk_request;
		header ('Content-type: text/html');
	}
	$ret = "<img width=100 height=10 border=0 title='${done}%' src='$src'>";
	return $ret;
}

function renderNetVLAN ($cell)
{
	if (! empty ($cell['8021q']))
	{
		$seen = array();
		foreach ($cell['8021q'] as $vlan_info)
			$seen[$vlan_info['vlan_id']] = $vlan_info['domain_id'] . '-' . $vlan_info['vlan_id'];
		echo '<div class="vlan"><strong><small>VLAN' . (count ($seen) > 1 ? 'S' : '') . '</small> ';
		$links = array();
		foreach ($seen as $vlan_id => $vlan_ck)
			$links[] = '<a href="' . makeHref (array ('page' => 'vlan', 'vlan_ck' => $vlan_ck)) . '">' . $vlan_id . '</a>';
		echo implode (', ', $links);
		echo '</strong></div>';
	}
}

function includeJQueryUI ($do_css = TRUE)
{
	addJS ('js/jquery-ui-1.8.21.min.js');
	if ($do_css)
		addCSS ('css/jquery-ui-1.8.22.redmond.css');
}

function getRenderedIPPortPair ($ip, $port = NULL)
{
	return "<a href=\"" .
		makeHref (array ('page' => 'ipaddress',  'tab'=>'default', 'ip' => $ip)) .
		"\">" . $ip . "</a>" .
		(isset ($port) ? ":" . $port : "");
}

?>
