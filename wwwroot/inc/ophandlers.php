<?php

# This file is a part of RackTables, a datacenter and server room management
# framework. See accompanying file "COPYING" for the full copyright and
# licensing information.

/*

"Ophandler" in RackTables stands for "operation handler", or a function
that handles execution of "operation" (in the meaning explained in
navigation.php). Most of the ophandlers are meant to perform one specific
action, for example, to set a name of an object. Each such action often
requires a set of parameters (e. g. ID of the object and the new name),
and it is responsibility of each ophandler function to verify, that all
necessary parameters are provided by the user and have proper values. There
is a number of helper functions to make such verification simpler.

Errors occuring in ophandlers are typically indicated with exceptions of
assorted classes. Namely, an "InvalidRequestArgException" class means, that
at least one of the parameters provided by the user is not acceptable. This
is a "soft" error, which gets displayed in the standard message area of
otherwise usual interface. A different case is "InvalidArgException", which
means that one of the internal functions detected its argument(s) invalid
or corrupted, and that argument(s) did not come from user's input (and thus
cannot be fixed without fixing a bug in the code). Such "hard" errors don't
get special early handling and end up in the default catching block. The
latter may print a detailed stack trace instead of the interface HTML to
help a developer debug the issue.

As long as an ophandler makes through its request (extracting arguments,
performing validation and actually updating records in the database), it
may queue up messages (often referred to as "green" and "red" bars) by
means of showError() and showSuccess() functions. The messages are not
displayed immediately, because successfull ophandlers are expected to
return only the new URL, where the user will be immediately redirected to
(it is also possible to return an empty string to mean, that the current
logical location remains the same). The page at the "next" location is
supposed to translate message buffer into the standard message area.

A very special case of an ophandler is tableHandler(). This generic
function handles the most trivial actions, which map to a single INSERT,
UPDATE or DELETE SQL statement with a fixed number of arguments. The rules
of argument validation and mapping are listed in $opspec_list (operation
specifications list) array.

*/

// This array is deprecated. Please do not add new message constants to it.
// use the new showError, showWarning, showSuccess functions instead
global $msgcode;
$msgcode = array();

global $opspec_list;
$opspec_list = array();

$opspec_list['object-edit-unlinkObjects'] = array
(
	'table' => 'EntityLink',
	'action' => 'DELETE',
	'arglist' => array
	(
		array ('url_argname' => 'link_id', 'table_colname' => 'id', 'assertion' => 'uint'),
	),
);
$opspec_list['object-ports-useup'] = array
(
	'table' => 'Port',
	'action' => 'UPDATE',
	'set_arglist' => array
	(
		array ('fix_argname' => 'reservation_comment', 'fix_argvalue' => NULL),
	),
	'where_arglist' => array
	(
		array ('url_argname' => 'port_id', 'table_colname' => 'id', 'assertion' => 'uint'),
		array ('url_argname' => 'object_id', 'assertion' => 'uint'), # preserve context
	),
);
$opspec_list['object-ports-delPort'] = array
(
	'table' => 'Port',
	'action' => 'DELETE',
	'arglist' => array
	(
		array ('url_argname' => 'port_id', 'table_colname' => 'id', 'assertion' => 'uint'),
		array ('url_argname' => 'object_id', 'assertion' => 'uint'),
	),
);
$opspec_list['object-ports-deleteAll'] = array
(
	'table' => 'Port',
	'action' => 'DELETE',
	'arglist' => array
	(
		array ('url_argname' => 'object_id', 'assertion' => 'uint'),
	),
);
$opspec_list['location-log-del'] = array
(
	'table' => 'ObjectLog',
	'action' => 'DELETE',
	'arglist' => array
	(
		array ('url_argname' => 'log_id', 'table_colname' => 'id', 'assertion' => 'uint'),
		array ('url_argname' => 'location_id', 'table_colname' => 'object_id', 'assertion' => 'uint'),
	),
);
$opspec_list['object-log-del'] = array
(
	'table' => 'ObjectLog',
	'action' => 'DELETE',
	'arglist' => array
	(
		array ('url_argname' => 'log_id', 'table_colname' => 'id', 'assertion' => 'uint'),
		array ('url_argname' => 'object_id', 'assertion' => 'uint'),
	),
);
$opspec_list['rack-log-del'] = array
(
	'table' => 'ObjectLog',
	'action' => 'DELETE',
	'arglist' => array
	(
		array ('url_argname' => 'log_id', 'table_colname' => 'id', 'assertion' => 'uint'),
		array ('url_argname' => 'rack_id', 'table_colname' => 'object_id', 'assertion' => 'uint'),
	),
);
$opspec_list['row-log-del'] = array
(
	'table' => 'ObjectLog',
	'action' => 'DELETE',
	'arglist' => array
	(
		array ('url_argname' => 'log_id', 'table_colname' => 'id', 'assertion' => 'uint'),
		array ('url_argname' => 'row_id', 'table_colname' => 'object_id', 'assertion' => 'uint'),
	),
);
$opspec_list['ipv4vs-editlblist-delLB'] =
$opspec_list['ipv4rspool-editlblist-delLB'] =
$opspec_list['object-editrspvs-delLB'] = array
(
	'table' => 'IPv4LB',
	'action' => 'DELETE',
	'arglist' => array
	(
		array ('url_argname' => 'object_id', 'assertion' => 'uint'),
		array ('url_argname' => 'pool_id', 'table_colname' => 'rspool_id', 'assertion' => 'uint'),
		array ('url_argname' => 'vs_id', 'assertion' => 'uint'),
	),
);
$opspec_list['ipv4vs-editlblist-updLB'] =
$opspec_list['ipv4rspool-editlblist-updLB'] =
$opspec_list['object-editrspvs-updLB'] = array
(
	'table' => 'IPv4LB',
	'action' => 'UPDATE',
	'set_arglist' => array
	(
		array ('url_argname' => 'vsconfig', 'assertion' => 'string0', 'translator' => 'nullIfEmptyStr'),
		array ('url_argname' => 'rsconfig', 'assertion' => 'string0', 'translator' => 'nullIfEmptyStr'),
		array ('url_argname' => 'prio', 'assertion' => 'string0', 'translator' => 'nullIfEmptyStr'),
	),
	'where_arglist' => array
	(
		array ('url_argname' => 'object_id', 'assertion' => 'uint'),
		array ('url_argname' => 'pool_id', 'table_colname' => 'rspool_id', 'assertion' => 'uint'),
		array ('url_argname' => 'vs_id', 'assertion' => 'uint'),
	),
);
$opspec_list['object-cacti-add'] = array
(
	'table' => 'CactiGraph',
	'action' => 'INSERT',
	'arglist' => array
	(
		array ('url_argname' => 'object_id', 'assertion' => 'uint'),
		array ('url_argname' => 'server_id', 'assertion' => 'uint'),
		array ('url_argname' => 'graph_id', 'assertion' => 'uint'),
		array ('url_argname' => 'caption', 'assertion' => 'string0'),
	),
);
$opspec_list['object-cacti-del'] = array
(
	'table' => 'CactiGraph',
	'action' => 'DELETE',
	'arglist' => array
	(
		array ('url_argname' => 'object_id', 'assertion' => 'uint'),
		array ('url_argname' => 'server_id', 'assertion' => 'uint'),
		array ('url_argname' => 'graph_id', 'assertion' => 'uint'),
	),
);
$opspec_list['object-munin-add'] = array
(
	'table' => 'MuninGraph',
	'action' => 'INSERT',
	'arglist' => array
	(
		array ('url_argname' => 'object_id', 'assertion' => 'uint'),
		array ('url_argname' => 'server_id', 'assertion' => 'uint'),
		array ('url_argname' => 'graph', 'assertion' => 'string'),
		array ('url_argname' => 'caption', 'assertion' => 'string0'),
	),
);
$opspec_list['object-munin-del'] = array
(
	'table' => 'MuninGraph',
	'action' => 'DELETE',
	'arglist' => array
	(
		array ('url_argname' => 'object_id', 'assertion' => 'uint'),
		array ('url_argname' => 'server_id', 'assertion' => 'uint'),
		array ('url_argname' => 'graph', 'assertion' => 'string'),
	),
);
$opspec_list['ipv4rspool-editrslist-delRS'] = array
(
	'table' => 'IPv4RS',
	'action' => 'DELETE',
	'arglist' => array
	(
		array ('url_argname' => 'id', 'assertion' => 'uint'),
	),
);
$opspec_list['parentmap-edit-add'] = array
(
	'table' => 'ObjectParentCompat',
	'action' => 'INSERT',
	'arglist' => array
	(
		array ('url_argname' => 'parent_objtype_id', 'assertion' => 'uint'),
		array ('url_argname' => 'child_objtype_id', 'assertion' => 'uint'),
	),
);
$opspec_list['parentmap-edit-del'] = array
(
	'table' => 'ObjectParentCompat',
	'action' => 'DELETE',
	'arglist' => array
	(
		array ('url_argname' => 'parent_objtype_id', 'assertion' => 'uint'),
		array ('url_argname' => 'child_objtype_id', 'assertion' => 'uint'),
	),
);
$opspec_list['portifcompat-edit-add'] = array
(
	'table' => 'PortInterfaceCompat',
	'action' => 'INSERT',
	'arglist' => array
	(
		array ('url_argname' => 'iif_id', 'assertion' => 'uint'),
		array ('url_argname' => 'oif_id', 'assertion' => 'uint'),
	),
);
$opspec_list['portifcompat-edit-del'] = array
(
	'table' => 'PortInterfaceCompat',
	'action' => 'DELETE',
	'arglist' => array
	(
		array ('url_argname' => 'iif_id', 'assertion' => 'uint'),
		array ('url_argname' => 'oif_id', 'assertion' => 'uint'),
	),
);
$opspec_list['portoifs-edit-add'] = array
(
	'table' => 'PortOuterInterface',
	'action' => 'INSERT',
	'arglist' => array
	(
		array ('url_argname' => 'oif_name', 'assertion' => 'string'),
	),
);
$opspec_list['portoifs-edit-del'] = array
(
	'table' => 'PortOuterInterface',
	'action' => 'DELETE',
	'arglist' => array
	(
		array ('url_argname' => 'id', 'assertion' => 'uint'),
	),
);
$opspec_list['portoifs-edit-upd'] = array
(
	'table' => 'PortOuterInterface',
	'action' => 'UPDATE',
	'set_arglist' => array
	(
		array ('url_argname' => 'oif_name', 'assertion' => 'string'),
	),
	'where_arglist' => array
	(
		array ('url_argname' => 'id', 'assertion' => 'uint'),
	),
);
$opspec_list['attrs-editmap-del'] = array
(
	'table' => 'AttributeMap',
	'action' => 'DELETE',
	'arglist' => array
	(
		array ('url_argname' => 'attr_id', 'assertion' => 'uint'),
		array ('url_argname' => 'objtype_id', 'assertion' => 'uint'),
	),
);
$opspec_list['attrs-editattrs-add'] = array
(
	'table' => 'Attribute',
	'action' => 'INSERT',
	'arglist' => array
	(
		array ('url_argname' => 'attr_type', 'table_colname' => 'type', 'assertion' => 'enum/attr_type'),
		array ('url_argname' => 'attr_name', 'table_colname' => 'name', 'assertion' => 'string'),
	),
);
$opspec_list['attrs-editattrs-del'] = array
(
	'table' => 'Attribute',
	'action' => 'DELETE',
	'arglist' => array
	(
		array ('url_argname' => 'attr_id', 'table_colname' => 'id', 'assertion' => 'uint'),
	),
);
$opspec_list['attrs-editattrs-upd'] = array
(
	'table' => 'Attribute',
	'action' => 'UPDATE',
	'set_arglist' => array
	(
		array ('url_argname' => 'attr_name', 'table_colname' => 'name', 'assertion' => 'string'),
	),
	'where_arglist' => array
	(
		array ('url_argname' => 'attr_id', 'table_colname' => 'id', 'assertion' => 'uint'),
	),
);
$opspec_list['dict-chapters-add'] = array
(
	'table' => 'Chapter',
	'action' => 'INSERT',
	'arglist' => array
	(
		array ('url_argname' => 'chapter_name', 'table_colname' => 'name', 'assertion' => 'string')
	),
);
$opspec_list['chapter-edit-add'] = array
(
	'table' => 'Dictionary',
	'action' => 'INSERT',
	'arglist' => array
	(
		array ('url_argname' => 'chapter_no', 'table_colname' => 'chapter_id', 'assertion' => 'uint'),
		array ('url_argname' => 'dict_value', 'assertion' => 'string'),
	),
);
$opspec_list['chapter-edit-del'] = array
(
	'table' => 'Dictionary',
	'action' => 'DELETE',
	'arglist' => array
	(
		// Technically dict_key is enough to delete, but including chapter_id into
		// WHERE clause makes sure, that the action actually happends for the same
		// chapter that authorization was granted for.
		array ('url_argname' => 'chapter_no', 'table_colname' => 'chapter_id', 'assertion' => 'uint'),
		array ('url_argname' => 'dict_key', 'assertion' => 'uint'),
		array ('fix_argname' => 'dict_sticky', 'fix_argvalue' => 'no'), # protect system rows
	),
);
$opspec_list['chapter-edit-upd'] = array
(
	'table' => 'Dictionary',
	'action' => 'UPDATE',
	'set_arglist' => array
	(
		array ('url_argname' => 'dict_value', 'assertion' => 'string'),
	),
	'where_arglist' => array
	(
		# same as above for listing chapter_no
		array ('url_argname' => 'chapter_no', 'table_colname' => 'chapter_id', 'assertion' => 'uint'),
		array ('url_argname' => 'dict_key', 'assertion' => 'uint'),
		array ('fix_argname' => 'dict_sticky', 'fix_argvalue' => 'no'), # protect system rows
	),
);
$opspec_list['tagtree-edit-createTag'] = array
(
	'table' => 'TagTree',
	'action' => 'INSERT',
	'arglist' => array
	(
		array ('url_argname' => 'tag_name', 'table_colname' => 'tag', 'assertion' => 'tag'),
		array ('url_argname' => 'parent_id', 'assertion' => 'uint0', 'translator' => 'nullIfZero'),
		array ('url_argname' => 'is_assignable', 'assertion' => 'enum/yesno'),
	),
);
$opspec_list['tagtree-edit-destroyTag'] = array
(
	'table' => 'TagTree',
	'action' => 'DELETE',
	'arglist' => array
	(
		array ('url_argname' => 'tag_id', 'table_colname' => 'id', 'assertion' => 'uint'),
	),
);
$opspec_list['8021q-vstlist-add'] = array
(
	'table' => 'VLANSwitchTemplate',
	'action' => 'INSERT',
	'arglist' => array
	(
		array ('url_argname' => 'vst_descr', 'table_colname' => 'description', 'assertion' => 'string'),
		// workaround SQL_STRICT
		array ('fix_argname' => 'mutex_rev', 'fix_argvalue' => 0),
		array ('fix_argname' => 'saved_by', 'fix_argvalue' => ""),
	),
);
$opspec_list['8021q-vstlist-del'] = array
(
	'table' => 'VLANSwitchTemplate',
	'action' => 'DELETE',
	'arglist' => array
	(
		array ('url_argname' => 'vst_id', 'table_colname' => 'id', 'assertion' => 'uint'),
	),
);
$opspec_list['8021q-vstlist-upd'] = array
(
	'table' => 'VLANSwitchTemplate',
	'action' => 'UPDATE',
	'set_arglist' => array
	(
		array ('url_argname' => 'vst_descr', 'table_colname' => 'description', 'assertion' => 'string'),
	),
	'where_arglist' => array
	(
		array ('url_argname' => 'vst_id', 'table_colname' => 'id', 'assertion' => 'uint'),
	),
);
$opspec_list['8021q-vdlist-del'] = array
(
	'table' => 'VLANDomain',
	'action' => 'DELETE',
	'arglist' => array
	(
		array ('url_argname' => 'vdom_id', 'table_colname' => 'id', 'assertion' => 'uint'),
	),
);
$opspec_list['vlandomain-vlanlist-add'] = array
(
	'table' => 'VLANDescription',
	'action' => 'INSERT',
	'arglist' => array
	(
		array ('url_argname' => 'vdom_id', 'table_colname' => 'domain_id', 'assertion' => 'uint'),
		array ('url_argname' => 'vlan_id', 'assertion' => 'vlan'),
		array ('url_argname' => 'vlan_type', 'assertion' => 'enum/vlan_type'),
		array ('url_argname' => 'vlan_descr', 'assertion' => 'string0', 'translator' => 'nullIfEmptyStr'),
	),
);
$opspec_list['vlandomain-vlanlist-del'] = array
(
	'table' => 'VLANDescription',
	'action' => 'DELETE',
	'arglist' => array
	(
		array ('url_argname' => 'vdom_id', 'table_colname' => 'domain_id', 'assertion' => 'uint'),
		array ('url_argname' => 'vlan_id', 'assertion' => 'vlan'),
	),
);
$opspec_list['vlan-edit-upd'] = // both locations are using the same tableHandler op
$opspec_list['vlandomain-vlanlist-upd'] = array
(
	'table' => 'VLANDescription',
	'action' => 'UPDATE',
	'set_arglist' => array
	(
		array ('url_argname' => 'vlan_type', 'assertion' => 'enum/vlan_type'),
		array ('url_argname' => 'vlan_descr', 'assertion' => 'string0', 'translator' => 'nullIfEmptyStr'),
	),
	'where_arglist' => array
	(
		array ('url_argname' => 'vdom_id', 'table_colname' => 'domain_id', 'assertion' => 'uint'),
		array ('url_argname' => 'vlan_id', 'assertion' => 'vlan'),
	),
);
$opspec_list['dict-chapters-upd'] = array
(
	'table' => 'Chapter',
	'action' => 'UPDATE',
	'set_arglist' => array
	(
		array ('url_argname' => 'chapter_name', 'table_colname' => 'name', 'assertion' => 'string'),
	),
	'where_arglist' => array
	(
		array ('url_argname' => 'chapter_no', 'table_colname' => 'id', 'assertion' => 'uint'),
		array ('fix_argname' => 'sticky', 'fix_argvalue' => 'no'), # protect system chapters
	),
);
$opspec_list['dict-chapters-del'] = array
(
	'table' => 'Chapter',
	'action' => 'DELETE',
	'arglist' => array
	(
		array ('url_argname' => 'chapter_no', 'table_colname' => 'id', 'assertion' => 'uint'),
		array ('fix_argname' => 'sticky', 'fix_argvalue' => 'no'), # protect system chapters
	),
);
$opspec_list['cacti-servers-add'] = array
(
	'table' => 'CactiServer',
	'action' => 'INSERT',
	'arglist' => array
	(
		array ('url_argname' => 'base_url', 'assertion' => 'string'),
		array ('url_argname' => 'username', 'assertion' => 'string0'),
		array ('url_argname' => 'password', 'assertion' => 'string0'),
	),
);
$opspec_list['cacti-servers-del'] = array
(
	'table' => 'CactiServer',
	'action' => 'DELETE',
	'arglist' => array
	(
		array ('url_argname' => 'id', 'assertion' => 'uint'),
	),
);
$opspec_list['cacti-servers-upd'] = array
(
	'table' => 'CactiServer',
	'action' => 'UPDATE',
	'set_arglist' => array
	(
		array ('url_argname' => 'base_url', 'assertion' => 'string'),
		array ('url_argname' => 'username', 'assertion' => 'string0'),
		array ('url_argname' => 'password', 'assertion' => 'string0'),
	),
	'where_arglist' => array
	(
		array ('url_argname' => 'id', 'assertion' => 'uint'),
	),
);
$opspec_list['munin-servers-add'] = array
(
	'table' => 'MuninServer',
	'action' => 'INSERT',
	'arglist' => array
	(
		array ('url_argname' => 'base_url', 'assertion' => 'string')
	),
);
$opspec_list['munin-servers-del'] = array
(
	'table' => 'MuninServer',
	'action' => 'DELETE',
	'arglist' => array
	(
		array ('url_argname' => 'id', 'assertion' => 'uint'),
	),
);
$opspec_list['munin-servers-upd'] = array
(
	'table' => 'MuninServer',
	'action' => 'UPDATE',
	'set_arglist' => array
	(
		array ('url_argname' => 'base_url', 'assertion' => 'string'),
	),
	'where_arglist' => array
	(
		array ('url_argname' => 'id', 'assertion' => 'uint'),
	),
);
$opspec_list['cables-heaps-add'] = array
(
	'table' => 'PatchCableHeap',
	'action' => 'INSERT',
	'arglist' => array
	(
		array ('url_argname' => 'end1_conn_id', 'assertion' => 'uint'),
		array ('url_argname' => 'pctype_id', 'assertion' => 'uint'),
		array ('url_argname' => 'end2_conn_id', 'assertion' => 'uint'),
		array ('fix_argname' => 'amount', 'fix_argvalue' => 0),
		array ('url_argname' => 'length', 'assertion' => 'decimal'),
		array ('url_argname' => 'description', 'assertion' => 'string0'),
	),
);
$opspec_list['cables-heaps-del'] = array
(
	'table' => 'PatchCableHeap',
	'action' => 'DELETE',
	'arglist' => array
	(
		array ('url_argname' => 'id', 'assertion' => 'uint'),
	),
);
$opspec_list['cables-heaps-upd'] = array
(
	'table' => 'PatchCableHeap',
	'action' => 'UPDATE',
	'set_arglist' => array
	(
		array ('url_argname' => 'end1_conn_id', 'assertion' => 'uint'),
		array ('url_argname' => 'pctype_id', 'assertion' => 'uint'),
		array ('url_argname' => 'end2_conn_id', 'assertion' => 'uint'),
		array ('url_argname' => 'length', 'assertion' => 'decimal'),
		array ('url_argname' => 'description', 'assertion' => 'string0'),
	),
	'where_arglist' => array
	(
		array ('url_argname' => 'id', 'assertion' => 'uint'),
	),
);
$opspec_list['cableconf-connectors-add'] = array
(
	'table' => 'PatchCableConnector',
	'action' => 'INSERT',
	'arglist' => array
	(
		array ('url_argname' => 'connector', 'assertion' => 'string'),
		array ('fix_argname' => 'origin', 'fix_argvalue' => 'custom'),
	),
);
$opspec_list['cableconf-connectors-del'] = array
(
	'table' => 'PatchCableConnector',
	'action' => 'DELETE',
	'arglist' => array
	(
		array ('url_argname' => 'id', 'assertion' => 'uint'),
		array ('fix_argname' => 'origin', 'fix_argvalue' => 'custom'),
	),
);
$opspec_list['cableconf-connectors-upd'] = array
(
	'table' => 'PatchCableConnector',
	'action' => 'UPDATE',
	'set_arglist' => array
	(
		array ('url_argname' => 'connector', 'assertion' => 'string'),
	),
	'where_arglist' => array
	(
		array ('url_argname' => 'id', 'assertion' => 'uint'),
		array ('fix_argname' => 'origin', 'fix_argvalue' => 'custom'),
	),
);
$opspec_list['cableconf-cabletypes-add'] = array
(
	'table' => 'PatchCableType',
	'action' => 'INSERT',
	'arglist' => array
	(
		array ('url_argname' => 'pctype', 'assertion' => 'string'),
		array ('fix_argname' => 'origin', 'fix_argvalue' => 'custom'),
	),
);
$opspec_list['cableconf-cabletypes-del'] = array
(
	'table' => 'PatchCableType',
	'action' => 'DELETE',
	'arglist' => array
	(
		array ('url_argname' => 'id', 'assertion' => 'uint'),
		array ('fix_argname' => 'origin', 'fix_argvalue' => 'custom'),
	),
);
$opspec_list['cableconf-cabletypes-upd'] = array
(
	'table' => 'PatchCableType',
	'action' => 'UPDATE',
	'set_arglist' => array
	(
		array ('url_argname' => 'pctype', 'assertion' => 'string'),
	),
	'where_arglist' => array
	(
		array ('url_argname' => 'id', 'assertion' => 'uint'),
		array ('fix_argname' => 'origin', 'fix_argvalue' => 'custom'),
	),
);
$opspec_list['cableconf-conncompat-add'] = array
(
	'table' => 'PatchCableConnectorCompat',
	'action' => 'INSERT',
	'arglist' => array
	(
		array ('url_argname' => 'pctype_id', 'assertion' => 'uint'),
		array ('url_argname' => 'connector_id', 'assertion' => 'uint'),
	),
);
$opspec_list['cableconf-conncompat-del'] = array
(
	'table' => 'PatchCableConnectorCompat',
	'action' => 'DELETE',
	'arglist' => array
	(
		array ('url_argname' => 'pctype_id', 'assertion' => 'uint'),
		array ('url_argname' => 'connector_id', 'assertion' => 'uint'),
	),
);
$opspec_list['cableconf-oifcompat-add'] = array
(
	'table' => 'PatchCableOIFCompat',
	'action' => 'INSERT',
	'arglist' => array
	(
		array ('url_argname' => 'pctype_id', 'assertion' => 'uint'),
		array ('url_argname' => 'oif_id', 'assertion' => 'uint'),
	),
);
$opspec_list['cableconf-oifcompat-del'] = array
(
	'table' => 'PatchCableOIFCompat',
	'action' => 'DELETE',
	'arglist' => array
	(
		array ('url_argname' => 'pctype_id', 'assertion' => 'uint'),
		array ('url_argname' => 'oif_id', 'assertion' => 'uint'),
	),
);

function setFuncMessages ($funcname, $messages)
{
	global $msgcode;
	foreach ($messages as $symbol => $code)
		$msgcode[$funcname][$symbol] = $code;
}

function addPortForwarding ()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 48));
	assertUIntArg ('object_id');
	$localip_bin = assertIPv4Arg ('localip');
	$remoteip_bin = assertIPv4Arg ('remoteip');
	if ($_REQUEST['proto'] != 'ALL')
	{
		assertUIntArg ('localport');
		assertUIntArg ('remoteport');
	}
	assertStringArg ('proto');
	assertStringArg ('description', TRUE);
	$remoteport = isset ($_REQUEST['remoteport']) ? $_REQUEST['remoteport'] : '';
	if (!strlen ($remoteport))
		$remoteport = $_REQUEST['localport'];

	newPortForwarding
	(
		$_REQUEST['object_id'],
		$localip_bin,
		$_REQUEST['localport'],
		$remoteip_bin,
		$remoteport,
		$_REQUEST['proto'],
		$_REQUEST['description']
	);

	showFuncMessage (__FUNCTION__, 'OK');
}

function delPortForwarding ()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 49));
	assertUIntArg ('object_id');
	$localip_bin = assertIPv4Arg ('localip');
	$remoteip_bin = assertIPv4Arg ('remoteip');
	if ($_REQUEST['proto'] != 'ALL')
	{
		assertUIntArg ('localport');
		assertUIntArg ('remoteport');
	}
	assertStringArg ('proto');

	deletePortForwarding
	(
		$_REQUEST['object_id'],
		$localip_bin,
		$_REQUEST['localport'],
		$remoteip_bin,
		$_REQUEST['remoteport'],
		$_REQUEST['proto']
	);
	showFuncMessage (__FUNCTION__, 'OK');
}

function updPortForwarding ()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 51));
	assertUIntArg ('object_id');
	$localip_bin = assertIPv4Arg ('localip');
	$remoteip_bin = assertIPv4Arg ('remoteip');
	if ($_REQUEST['proto'] != 'ALL')
	{
		assertUIntArg ('localport');
		assertUIntArg ('remoteport');
	}
	assertStringArg ('proto');
	assertStringArg ('description', TRUE);

	updatePortForwarding
	(
		$_REQUEST['object_id'],
		$localip_bin,
		$_REQUEST['localport'],
		$remoteip_bin,
		$_REQUEST['remoteport'],
		$_REQUEST['proto'],
		$_REQUEST['description']
	);
	showFuncMessage (__FUNCTION__, 'OK');
}

function addPortForObject ()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 48));
	assertStringArg ('port_name', TRUE);
	genericAssertion ('port_l2address', 'l2address0');
	genericAssertion ('port_name', 'string');
	commitAddPort
	(
		$_REQUEST['object_id'],
		trim ($_REQUEST['port_name']),
		$_REQUEST['port_type_id'],
		trim ($_REQUEST['port_label']),
		trim ($_REQUEST['port_l2address'])
	);
	showFuncMessage (__FUNCTION__, 'OK', array ($_REQUEST['port_name']));
}

function editPortForObject ()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 6));
	global $sic;
	$port_id = assertUIntArg ('port_id');
	commitUpdatePort
	(
		getBypassValue(),
		$port_id,
		genericAssertion ('name', 'string'),
		assertStringArg ('port_type_id'),
		genericAssertion ('label', 'string0'),
		genericAssertion ('l2address', 'l2address0'),
		assertStringArg ('reservation_comment', TRUE)
	);
	if (array_key_exists ('cable', $_REQUEST))
		commitUpdatePortLink ($port_id, $sic['cable']);
	showFuncMessage (__FUNCTION__, 'OK', array ($_REQUEST['name']));
}

function addMultiPorts ()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 10));
	assertStringArg ('format');
	assertStringArg ('input');
	assertStringArg ('port_type');
	$format = $_REQUEST['format'];
	$port_type = $_REQUEST['port_type'];
	$object_id = $_REQUEST['object_id'];
	// Input lines are escaped, so we have to explode and to chop by 2-char
	// \n and \r respectively.
	$lines1 = explode ("\n", $_REQUEST['input']);
	foreach ($lines1 as $line)
	{
		$parts = explode ('\r', $line);
		reset ($parts);
		if (!strlen ($parts[0]))
			continue;
		else
			$lines2[] = rtrim ($parts[0]);
	}
	$ports = array();
	foreach ($lines2 as $line)
	{
		switch ($format)
		{
			case 'fisxii':
				$words = explode (' ', preg_replace ('/[[:space:]]+/', ' ', $line));
				list ($slot, $port) = explode ('/', $words[0]);
				$ports[] = array
				(
					'name' => "e ${slot}/${port}",
					'l2address' => $words[8],
					'label' => "slot ${slot} port ${port}"
				);
				break;
			case 'c3600asy':
				$words = explode (' ', preg_replace ('/[[:space:]]+/', ' ', trim (substr ($line, 3))));
/*
How Async Lines are Numbered in Cisco 3600 Series Routers
http://www.cisco.com/en/US/products/hw/routers/ps274/products_tech_note09186a00801ca70b.shtml

Understanding 16- and 32-Port Async Network Modules
http://www.cisco.com/en/US/products/hw/routers/ps274/products_tech_note09186a00800a93f0.shtml
*/
				$async = $words[0];
				$slot = floor (($async - 1) / 32);
				$octalgroup = floor (($async - 1 - $slot * 32) / 8);
				$cable = $async - $slot * 32 - $octalgroup * 8;
				$og_label[0] = 'async 0-7';
				$og_label[1] = 'async 8-15';
				$og_label[2] = 'async 16-23';
				$og_label[3] = 'async 24-31';
				$ports[] = array
				(
					'name' => "async ${async}",
					'l2address' => '',
					'label' => "slot ${slot} " . $og_label[$octalgroup] . " cable ${cable}"
				);
				break;
			case 'fiwg':
				$words = explode (' ', preg_replace ('/[[:space:]]+/', ' ', $line));
				$ifnumber = $words[0] * 1;
				$ports[] = array
				(
					'name' => "e ${ifnumber}",
					'l2address' => "${words[8]}",
					'label' => "${ifnumber}"
				);
				break;
			case 'ssv1':
				$words = explode (' ', $line);
				if (!strlen ($words[0])) // empty L2 address is OK
					continue;
				$ports[] = array
				(
					'name' => $words[0],
					'l2address' => $words[1],
					'label' => ''
				);
				break;
			default:
				throw new InvalidRequestArgException ('format', $format);
				break;
		}
	}
	// Create ports, if they don't exist.
	$added_count = $updated_count = $error_count = 0;
	foreach ($ports as $port)
	{
		$port_ids = getPortIDs ($object_id, $port['name']);
		if (!count ($port_ids))
		{
			commitAddPort ($object_id, $port['name'], $port_type, $port['label'], $port['l2address']);
			$added_count++;
		}
		elseif (count ($port_ids) == 1) // update only single-socket ports
		{
			commitUpdatePort ($object_id, $port_ids[0], $port['name'], $port_type, $port['label'], $port['l2address']);
			$updated_count++;
		}
	}
	showFuncMessage (__FUNCTION__, 'OK', array ($added_count, $updated_count, $error_count));
}

function addBulkPorts ()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 82));
	assertStringArg ('port_type_id');
	assertStringArg ('port_name', TRUE);
	assertStringArg ('port_label', TRUE);
	assertUIntArg ('port_numbering_start', TRUE);
	assertUIntArg ('port_numbering_count');

	$object_id = $_REQUEST['object_id'];
	$port_name = $_REQUEST['port_name'];
	$port_type_id = $_REQUEST['port_type_id'];
	$port_label = $_REQUEST['port_label'];
	$port_numbering_start = $_REQUEST['port_numbering_start'];
	$port_numbering_count = $_REQUEST['port_numbering_count'];

	$added_count = $error_count = 0;
	if(strrpos($port_name, "%u") === false )
		$port_name .= '%u';
	for ($i=0,$c=$port_numbering_start; $i<$port_numbering_count; $i++,$c++)
	{
		commitAddPort ($object_id, @sprintf($port_name,$c), $port_type_id, @sprintf($port_label,$c), '');
		$added_count++;
	}
	showFuncMessage (__FUNCTION__, 'OK', array ($added_count, $error_count));
}

function updIPAllocation ()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 51));
	$ip_bin = assertIPArg ('ip');
	assertUIntArg ('object_id');
	assertStringArg ('bond_name', TRUE);
	genericAssertion ('bond_type', 'enum/alloc_type');
	updateIPBond ($ip_bin, $_REQUEST['object_id'], $_REQUEST['bond_name'], $_REQUEST['bond_type']);
	showFuncMessage (__FUNCTION__, 'OK');
	return buildRedirectURL (NULL, NULL, array ('hl_ip' => ip_format ($ip_bin)));
}

function delIPAllocation ()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 49));
	$ip_bin = assertIPArg ('ip');
	assertUIntArg ('object_id');

	unbindIPFromObject ($ip_bin, $_REQUEST['object_id']);
	showFuncMessage (__FUNCTION__, 'OK');
}

function addIPAllocation ()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 48, 'ERR1' => 170));
	$ip_bin = assertIPArg ('ip');
	assertUIntArg ('object_id');
	assertStringArg ('bond_name', TRUE);
	genericAssertion ('bond_type', 'enum/alloc_type');

	// check if address is alread allocated
	$address = getIPAddress($ip_bin);

	if(!empty($address['allocs']) && ( ($address['allocs'][0]['type'] != 'shared') || ($_REQUEST['bond_type'] != 'shared') ) )
		showWarning("IP ".ip_format($ip_bin)." already in use by ".$address['allocs'][0]['object_name']." - ".$address['allocs'][0]['name']);

	if  (getConfigVar ('IPV4_JAYWALK') != 'yes' and NULL === getIPAddressNetworkId ($ip_bin))
	{
		showFuncMessage (__FUNCTION__, 'ERR1', array (ip_format ($ip_bin)));
		return;
	}

	if($address['reserved'] && strlen ($address['name']))
	{
		showWarning("IP ".ip_format($ip_bin)." reservation \"".$address['name']."\" is removed");
		//TODO ask to take reserved IP or not !
	}

	bindIPToObject ($ip_bin, $_REQUEST['object_id'], $_REQUEST['bond_name'], $_REQUEST['bond_type']);

	showFuncMessage (__FUNCTION__, 'OK');
	return buildRedirectURL (NULL, NULL, array ('hl_ip' => ip_format ($ip_bin)));
}

function addIPv4Prefix ()
{
	assertStringArg ('range');
	assertStringArg ('name', TRUE);
	$taglist = genericAssertion ('taglist', 'array0');
	global $sic;
	$vlan_ck = empty ($sic['vlan_ck']) ? NULL : genericAssertion ('vlan_ck', 'uint-vlan1');
	$net_id = createIPv4Prefix ($_REQUEST['range'], $sic['name'], isCheckSet ('is_connected'), $taglist);
	$net_cell = spotEntity ('ipv4net', $net_id);
	if (isset ($vlan_ck))
	{
		if (considerConfiguredConstraint ($net_cell, 'VLANIPV4NET_LISTSRC'))
			commitSupplementVLANIPv4 ($vlan_ck, $net_id);
		else
			showError ("VLAN binding to network " . mkCellA ($net_cell) . " is restricted in config");
	}
	showSuccess ('IP network ' . mkA ($_REQUEST['range'], 'ipv4net', $net_id) . ' has been created');
}

function addIPv6Prefix ()
{
	assertStringArg ('range');
	assertStringArg ('name', TRUE);
	$taglist = genericAssertion ('taglist', 'array0');
	global $sic;
	$vlan_ck = empty ($sic['vlan_ck']) ? NULL : genericAssertion ('vlan_ck', 'uint-vlan1');
	$net_id = createIPv6Prefix ($_REQUEST['range'], $sic['name'], isCheckSet ('is_connected'), $taglist);
	$net_cell = spotEntity ('ipv6net', $net_id);
	if (isset ($vlan_ck))
	{
		if (considerConfiguredConstraint ($net_cell, 'VLANIPV4NET_LISTSRC'))
			commitSupplementVLANIPv6 ($vlan_ck, $net_id);
		else
			showError ("VLAN binding to network " . mkCellA ($net_cell) . " is restricted in config");
	}
	showSuccess ('IP network ' . mkA ($_REQUEST['range'], 'ipv6net', $net_id) . ' has been created');
}

function delIPv4Prefix ()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 49));
	assertUIntArg ('id');
	$netinfo = spotEntity ('ipv4net', $_REQUEST['id']);
	loadIPAddrList ($netinfo);
	if (! isIPNetworkEmpty ($netinfo))
	{
		showError ("There are allocations within prefix, delete forbidden");
		return;
	}
	if (array_key_exists ($netinfo['ip_bin'], $netinfo['addrlist']))
		updateV4Address ($netinfo['ip_bin'], '', 'no');
	$last_ip = ip_last ($netinfo);
	if (array_key_exists ($last_ip, $netinfo['addrlist']))
		updateV4Address ($last_ip, '', 'no');
	destroyIPv4Prefix ($_REQUEST['id']);
	showFuncMessage (__FUNCTION__, 'OK');
	global $pageno;
	if ($pageno == 'ipv4net')
		return buildRedirectURL ('index', 'default');
}

function delIPv6Prefix ()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 49));
	assertUIntArg ('id');
	$netinfo = spotEntity ('ipv6net', $_REQUEST['id']);
	loadIPAddrList ($netinfo);
	if (! isIPNetworkEmpty ($netinfo))
	{
		showError ("There are allocations within prefix, delete forbidden");
		return;
	}
	if (array_key_exists ($netinfo['ip_bin'], $netinfo['addrlist']))
		updateV6Address ($netinfo['ip_bin'], '', 'no');
	destroyIPv6Prefix ($_REQUEST['id']);
	showFuncMessage (__FUNCTION__, 'OK');
	global $pageno;
	if ($pageno == 'ipv6net')
		return buildRedirectURL ('index', 'default');
}

function editAddress ()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 51));
	assertStringArg ('name', TRUE);
	assertStringArg ('comment', TRUE);
	$ip_bin = assertIPArg ('ip');
	updateAddress ($ip_bin, $_REQUEST['name'], isCheckSet ('reserved', 'yesno'), $_REQUEST['comment']);
	showFuncMessage (__FUNCTION__, 'OK');
}

function createUser ()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 5));
	assertStringArg ('username');
	assertStringArg ('realname', TRUE);
	assertStringArg ('password');
	$username = $_REQUEST['username'];
	$password = sha1 ($_REQUEST['password']);
	$user_id = commitCreateUserAccount ($username, $_REQUEST['realname'], $password);
	if (isset ($_REQUEST['taglist']))
		produceTagsForNewRecord ('user', $_REQUEST['taglist'], $user_id);
	showFuncMessage (__FUNCTION__, 'OK', array ($username));
}

function updateUser ()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 6));
	genericAssertion ('user_id', 'uint');
	$username = assertStringArg ('username');
	assertStringArg ('realname', TRUE);
	$new_password = assertStringArg ('password', TRUE);
	$userinfo = spotEntity ('user', $_REQUEST['user_id']);
	// Set new password only if provided.
	$new_password = mb_strlen ($new_password) ? sha1 ($new_password) : $userinfo['user_password_hash'];
	commitUpdateUserAccount ($_REQUEST['user_id'], $username, $_REQUEST['realname'], $new_password);
	// if user account renaming is being performed, change key value in UserConfig table
	if ($userinfo['user_name'] !== $username)
		usePreparedUpdateBlade ('UserConfig', array ('user' => $username), array('user' => $userinfo['user_name']));
	showFuncMessage (__FUNCTION__, 'OK', array ($username));
}

function supplementAttrMap ()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 48, 'ERR1' => 154));
	$attr_id = assertUIntArg ('attr_id');
	assertUIntArg ('objtype_id');
	if (getAttrType ($attr_id) != 'dict')
		$chapter_id = NULL;
	else
	{
		try
		{
			assertUIntArg ('chapter_no');
		}
		catch (InvalidRequestArgException $e)
		{
			showFuncMessage (__FUNCTION__, 'ERR1', array ('chapter not selected'));
			return;
		}
		$chapter_id = $_REQUEST['chapter_no'];
	}
	commitSupplementAttrMap ($attr_id, $_REQUEST['objtype_id'], $chapter_id);
	showFuncMessage (__FUNCTION__, 'OK');
}

function clearSticker ()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 49));
	$attr_id = assertUIntArg ('attr_id');
	if (permitted (NULL, NULL, NULL, array (array ('tag' => '$attr_' . $attr_id))))
		commitUpdateAttrValue (getBypassValue(), $attr_id);
	else
	{
		$oldvalues = getAttrValues (getBypassValue());
		showError ('Permission denied, "' . $oldvalues[$attr_id]['name'] . '" left unchanged');
	}
}

function updateObjectAllocation ()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 63));
	global $remote_username, $sic;
	if (!isset ($_REQUEST['got_atoms']))
	{
		unset($_GET['page']);
		unset($_GET['tab']);
		unset($_GET['op']);
		unset($_POST['page']);
		unset($_POST['tab']);
		unset($_POST['op']);
		return buildRedirectURL (NULL, NULL, $_REQUEST);
	}
	$object_id = getBypassValue();
	$object = spotEntity ('object', $object_id);
	$changecnt = 0;
	// Get a list of rack ids which are parents of the object
	$parentRacks = reduceSubarraysToColumn (getParents ($object, 'rack'), 'id');
	$workingRacksData = array();
	foreach ($_REQUEST['rackmulti'] as $cand_id)
	{
		if (!isset ($workingRacksData[$cand_id]))
		{
			$rackData = spotEntity ('rack', $cand_id);
			amplifyCell ($rackData);
			$workingRacksData[$cand_id] = $rackData;
		}
		else
			$rackData = $workingRacksData[$cand_id];
		$is_ro = !rackModificationPermitted ($rackData, 'updateObjectAllocation', FALSE);
		// It's zero-U mounted to this rack on the form, but not in the DB.  Mount it.
		if (isset($_REQUEST["zerou_${cand_id}"]) && !in_array($cand_id, $parentRacks))
		{
			if ($is_ro)
				continue;
			$changecnt++;
			commitLinkEntities ('rack', $cand_id, 'object', $object_id);
		}
		// It's not zero-U mounted to this rack on the form, but it is in the DB.  Unmount it.
		if (!isset($_REQUEST["zerou_${cand_id}"]) && in_array($cand_id, $parentRacks))
		{
			if ($is_ro)
				continue;
			$changecnt++;
			commitUnlinkEntities ('rack', $cand_id, 'object', $object_id);
		}
	}

	foreach (array_keys ($workingRacksData) as $key)
		applyObjectMountMask ($workingRacksData[$key], $object_id);

	$oldMolecule = getMoleculeForObject ($object_id);
	foreach ($workingRacksData as $rack_id => $rackData)
	{
		$is_ro = !rackModificationPermitted ($rackData, 'updateObjectAllocation', FALSE);
		if ($is_ro || !processGridForm ($rackData, 'F', 'T', $object_id))
			continue;
		$changecnt++;
		// Reload our working copy after form processing.
		$rackData = spotEntity ('rack', $cand_id);
		amplifyCell ($rackData);
		applyObjectMountMask ($rackData, $object_id);
		$workingRacksData[$rack_id] = $rackData;
	}
	if ($changecnt)
	{
		// Log a record.
		$newMolecule = getMoleculeForObject ($object_id);
		usePreparedInsertBlade
		(
			'MountOperation',
			array
			(
				'object_id' => $object_id,
				'old_molecule_id' => count ($oldMolecule) ? createMolecule ($oldMolecule) : NULL,
				'new_molecule_id' => count ($newMolecule) ? createMolecule ($newMolecule) : NULL,
				'user_name' => $remote_username,
				'comment' => empty ($sic['comment']) ? NULL : $sic['comment'],
			)
		);
	}
	showFuncMessage (__FUNCTION__, 'OK', array ($changecnt));
}

function updateObject ()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 51));
	$taglist = genericAssertion ('taglist', 'array0');
	genericAssertion ('num_attrs', 'uint0');
	genericAssertion ('object_name', 'string0');
	genericAssertion ('object_label', 'string0');
	genericAssertion ('object_asset_no', 'string0');
	genericAssertion ('object_comment', 'string0');
	genericAssertion ('object_type_id', 'uint');
	$object_id = getBypassValue();

	global $dbxlink, $sic;
	$dbxlink->beginTransaction();
	commitUpdateObject
	(
		$object_id,
		$_REQUEST['object_name'],
		$_REQUEST['object_label'],
		isCheckSet ('object_has_problems', 'yesno'),
		$_REQUEST['object_asset_no'],
		$_REQUEST['object_comment']
	);
	updateObjectAttributes ($object_id);
	$object = spotEntity ('object', $object_id);
	if ($sic['object_type_id'] != $object['objtype_id'])
	{
		if (! array_key_exists ($sic['object_type_id'], getObjectTypeChangeOptions ($object_id)))
			throw new InvalidRequestArgException ('new type_id', $sic['object_type_id'], 'incompatible with requested attribute values');
		usePreparedUpdateBlade ('Object', array ('objtype_id' => $sic['object_type_id']), array ('id' => $object_id));
	}
	// Invalidate thumb cache of all racks objects could occupy.
	foreach (getResidentRacksData ($object_id, FALSE) as $rack_id)
		usePreparedDeleteBlade ('RackThumbnail', array ('rack_id' => $rack_id));
	$dbxlink->commit();
	rebuildTagChainForEntity ('object', $object_id, buildTagChainFromIds ($taglist), TRUE);
	showFuncMessage (__FUNCTION__, 'OK');
}

// Used when updating an object, location or rack
function updateObjectAttributes ($object_id)
{
	global $dbxlink;
	$type_id = getObjectType ($object_id);
	$oldvalues = getAttrValues ($object_id);
	$num_attrs = isset ($_REQUEST['num_attrs']) ? $_REQUEST['num_attrs'] : 0;
	for ($i = 0; $i < $num_attrs; $i++)
	{
		genericAssertion ("${i}_attr_id", 'uint');
		$attr_id = $_REQUEST["${i}_attr_id"];
		if (! array_key_exists ($attr_id, $oldvalues))
			throw new InvalidRequestArgException ('attr_id', $attr_id, 'malformed request');
		$value = $_REQUEST["${i}_value"];

		// If the object is a rack, skip certain attributes as they are handled elsewhere
		// (height, sort_order)
		if ($type_id == 1560 and ($attr_id == 27 or $attr_id == 29))
			continue;

		// Delete attribute and move on, when the field is empty or if the field
		// type is a dictionary and it is the "--NOT SET--" value of 0.
		if ($value == '' || ($oldvalues[$attr_id]['type'] == 'dict' && $value == 0))
		{
			if (permitted (NULL, NULL, NULL, array (array ('tag' => '$attr_' . $attr_id))))
				commitUpdateAttrValue ($object_id, $attr_id);
			else
				showError ('Permission denied, "' . $oldvalues[$attr_id]['name'] . '" left unchanged');
			continue;
		}

		// The value could be uint/float, but we don't know ATM. Let SQL
		// server check this and complain.
		if ('date' == $oldvalues[$attr_id]['type'])
			$value = assertDateArg ("${i}_value");
		else
			assertStringArg ("${i}_value");

		switch ($oldvalues[$attr_id]['type'])
		{
			case 'uint':
			case 'float':
			case 'string':
			case 'date':
				$oldvalue = $oldvalues[$attr_id]['value'];
				break;
			case 'dict':
				$oldvalue = $oldvalues[$attr_id]['key'];
				break;
			default:
		}
		if ($value === $oldvalue) // ('' == 0), but ('' !== 0)
			continue;
		if (permitted (NULL, NULL, NULL, array (array ('tag' => '$attr_' . $attr_id))))
			commitUpdateAttrValue ($object_id, $attr_id, $value);
		else
			showError ('Permission denied, "' . $oldvalues[$attr_id]['name'] . '" left unchanged');
	}
}

function addMultipleObjects()
{
	$taglist = genericAssertion ('taglist', 'array0');
	$max = getConfigVar ('MASSCOUNT');
	for ($i = 0; $i < $max; $i++)
	{
		if (!isset ($_REQUEST["${i}_object_type_id"]))
		{
			showError ('Submitted form is invalid at line ' . ($i + 1));
			return;
		}

		assertUIntArg ("${i}_object_type_id", TRUE);
		assertStringArg ("${i}_object_name", TRUE);
		assertStringArg ("${i}_object_label", TRUE);
		assertStringArg ("${i}_object_asset_no", TRUE);
		$name = $_REQUEST["${i}_object_name"];

		// It's better to skip silently, than to print a notice.
		if ($_REQUEST["${i}_object_type_id"] == 0)
			continue;
		try
		{
			$object_id = commitAddObject
			(
				$name,
				$_REQUEST["${i}_object_label"],
				$_REQUEST["${i}_object_type_id"],
				$_REQUEST["${i}_object_asset_no"],
				$taglist
			);
			$info = spotEntity ('object', $object_id);
			amplifyCell ($info);
			showSuccess ("added object " . formatPortLink ($info['id'], $info['dname'], NULL, NULL));
		}
		catch (RTDatabaseError $e)
		{
			showError ("Error creating object '$name': " . $e->getMessage());
			continue;
		}
	}
}

function addLotOfObjects()
{
	$taglist = genericAssertion ('taglist', 'array0');
	assertUIntArg ('global_type_id', TRUE);
	assertStringArg ('namelist', TRUE);
	$global_type_id = $_REQUEST['global_type_id'];
	if ($global_type_id == 0 or !strlen ($_REQUEST['namelist']))
	{
		showError ('Incomplete form has been ignored. Cheers.');
		return;
	}
	else
	{
		// The name extractor below was stolen from ophandlers.php:addMultiPorts()
		$names1 = explode ("\n", $_REQUEST['namelist']);
		$names2 = array();
		foreach ($names1 as $line)
		{
			$parts = explode ('\r', $line);
			reset ($parts);
			if (!strlen ($parts[0]))
				continue;
			else
				$names2[] = rtrim ($parts[0]);
		}
		foreach ($names2 as $name)
			try
			{
				$object_id = commitAddObject ($name, NULL, $global_type_id, '', $taglist);
				$info = spotEntity ('object', $object_id);
				amplifyCell ($info);
				showSuccess ("added object " . formatPortLink ($info['id'], $info['dname'], NULL, NULL));
			}
			catch (RackTablesError $e)
			{
				showError ("Error creating object '$name': " . $e->getMessage());
				continue;
			}
	}
}

function linkObjects ()
{
	assertStringArg ('parent_entity_type');
	assertUIntArg ('parent_entity_id');
	assertStringArg ('child_entity_type');
	assertUIntArg ('child_entity_id');

	commitLinkEntities
	(
		$_REQUEST['parent_entity_type'],
		$_REQUEST['parent_entity_id'],
		$_REQUEST['child_entity_type'],
		$_REQUEST['child_entity_id']
	);
	showSuccess ('Container set successfully');
}

function deleteObject ()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 7));
	assertUIntArg ('object_id');
	$oinfo = spotEntity ('object', $_REQUEST['object_id']);

	$racklist = getResidentRacksData ($_REQUEST['object_id'], FALSE);
	commitDeleteObject ($_REQUEST['object_id']);
	foreach ($racklist as $rack_id)
		usePreparedDeleteBlade ('RackThumbnail', array ('rack_id' => $rack_id));
	showFuncMessage (__FUNCTION__, 'OK', array ($oinfo['dname']));
}

function resetObject ()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 57));
	$racklist = getResidentRacksData (getBypassValue(), FALSE);
	commitResetObject (getBypassValue());
	foreach ($racklist as $rack_id)
		usePreparedDeleteBlade ('RackThumbnail', array ('rack_id' => $rack_id));
	showFuncMessage (__FUNCTION__, 'OK');
}

function updateUI ()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 51));
	assertUIntArg ('num_vars');

	for ($i = 0; $i < $_REQUEST['num_vars']; $i++)
	{
		assertStringArg ("${i}_varname");
		assertStringArg ("${i}_varvalue", TRUE);
		$varname = $_REQUEST["${i}_varname"];
		$varvalue = $_REQUEST["${i}_varvalue"];

		// If form value = value in DB, don't bother updating DB
		if (!isConfigVarChanged($varname, $varvalue))
			continue;
		// any exceptions will be handled by process.php
		setConfigVar ($varname, $varvalue, TRUE);
	}
	showFuncMessage (__FUNCTION__, 'OK');
}

function saveMyPreferences ()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 51));
	assertUIntArg ('num_vars');

	for ($i = 0; $i < $_REQUEST['num_vars']; $i++)
	{
		assertStringArg ("${i}_varname");
		assertStringArg ("${i}_varvalue", TRUE);
		$varname = $_REQUEST["${i}_varname"];
		$varvalue = $_REQUEST["${i}_varvalue"];

		// If form value = value in DB, don't bother updating DB
		if (!isConfigVarChanged($varname, $varvalue))
			continue;
		setUserConfigVar ($varname, $varvalue);
	}
	showFuncMessage (__FUNCTION__, 'OK');
}

function resetMyPreference ()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 51));
	assertStringArg ("varname");
	resetUserConfigVar ($_REQUEST["varname"]);
	showFuncMessage (__FUNCTION__, 'OK');
}

function resetUIConfig()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 57));
	$defaults = array
	(
		'MASSCOUNT' => '8',
		'MAXSELSIZE' => '30',
		'ROW_SCALE' => '2',
		'IPV4_ADDRS_PER_PAGE' => '256',
		'DEFAULT_RACK_HEIGHT' => '42',
		'DEFAULT_SLB_VS_PORT' => '',
		'DEFAULT_SLB_RS_PORT' => '',
		'DETECT_URLS' => 'no',
		'RACK_PRESELECT_THRESHOLD' => '1',
		'DEFAULT_IPV4_RS_INSERVICE' => 'no',
		'AUTOPORTS_CONFIG' => '4 = 1*33*kvm + 2*24*eth%u;15 = 1*446*kvm',
		'SHOW_EXPLICIT_TAGS' => 'yes',
		'SHOW_IMPLICIT_TAGS' => 'yes',
		'SHOW_AUTOMATIC_TAGS' => 'no',
		'DEFAULT_OBJECT_TYPE' => '4',
		'IPV4_AUTO_RELEASE' => '1',
		'SHOW_LAST_TAB' => 'yes',
		'EXT_IPV4_VIEW' => 'yes',
		'TREE_THRESHOLD' => '25',
		'IPV4_JAYWALK' => 'no',
		'ADDNEW_AT_TOP' => 'yes',
		'IPV4_TREE_SHOW_USAGE' => 'no',
		'PREVIEW_TEXT_MAXCHARS' => '10240',
		'PREVIEW_TEXT_ROWS' => '25',
		'PREVIEW_TEXT_COLS' => '80',
		'PREVIEW_IMAGE_MAXPXS' => '320',
		'VENDOR_SIEVE' => '',
		'IPV4LB_LISTSRC' => 'false',
		'IPV4OBJ_LISTSRC' => 'not ({$typeid_3} or {$typeid_9} or {$typeid_10} or {$typeid_11})',
		'IPV4NAT_LISTSRC' => '{$typeid_4} or {$typeid_7} or {$typeid_8} or {$typeid_798}',
		'ASSETWARN_LISTSRC' => '{$typeid_4} or {$typeid_7} or {$typeid_8}',
		'NAMEWARN_LISTSRC' => '{$typeid_4} or {$typeid_7} or {$typeid_8}',
		'RACKS_PER_ROW' => '12',
		'FILTER_PREDICATE_SIEVE' => '',
		'FILTER_DEFAULT_ANDOR' => 'and',
		'FILTER_SUGGEST_ANDOR' => 'yes',
		'FILTER_SUGGEST_TAGS' => 'yes',
		'FILTER_SUGGEST_PREDICATES' => 'yes',
		'FILTER_SUGGEST_EXTRA' => 'no',
		'DEFAULT_SNMP_COMMUNITY' => 'public',
		'IPV4_ENABLE_KNIGHT' => 'yes',
		'TAGS_TOPLIST_SIZE' => '50',
		'TAGS_QUICKLIST_SIZE' => '20',
		'TAGS_QUICKLIST_THRESHOLD' => '50',
		'ENABLE_MULTIPORT_FORM' => 'no',
		'DEFAULT_PORT_IIF_ID' => '1',
		'DEFAULT_PORT_OIF_IDS' => '1=24; 3=1078; 4=1077; 5=1079; 6=1080; 8=1082; 9=1084; 10=1588; 11=1668; 12=1589; 13=1590; 14=1591',
		'IPV4_TREE_RTR_AS_CELL' => 'no',
		'PROXIMITY_RANGE' => '0',
		'IPV4_TREE_SHOW_VLAN' => 'yes',
		'VLANSWITCH_LISTSRC' => '',
		'VLANIPV4NET_LISTSRC' => '',
		'DEFAULT_VDOM_ID' => '',
		'DEFAULT_VST_ID' => '',
		'STATIC_FILTER' => 'yes',
		'8021Q_DEPLOY_MINAGE' => '300',
		'8021Q_DEPLOY_MAXAGE' => '3600',
		'8021Q_DEPLOY_RETRY' => '10800',
		'8021Q_WRI_AFTER_CONFT_LISTSRC' => 'false',
		'8021Q_INSTANT_DEPLOY' => 'no',
		'CDP_RUNNERS_LISTSRC' => '',
		'LLDP_RUNNERS_LISTSRC' => '',
		'SHRINK_TAG_TREE_ON_CLICK' => 'yes',
		'MAX_UNFILTERED_ENTITIES' => '0',
		'SYNCDOMAIN_MAX_PROCESSES' => '0',
		'PORT_EXCLUSION_LISTSRC' => '{$typeid_3} or {$typeid_10} or {$typeid_11} or {$typeid_1505} or {$typeid_1506}',
		'FILTER_RACKLIST_BY_TAGS' => 'yes',
		'MGMT_PROTOS' => 'ssh: {$typeid_4}; telnet: {$typeid_8}',
		'SYNC_802Q_LISTSRC' => '',
		'QUICK_LINK_PAGES' => 'depot,ipv4space,rackspace',
		'CACTI_LISTSRC' => 'false',
		'CACTI_RRA_ID' => '1',
		'MUNIN_LISTSRC' => 'false',
		'VIRTUAL_OBJ_LISTSRC' => '1504,1505,1506,1507',
		'DATETIME_ZONE' => 'UTC',
		'DATETIME_FORMAT' => '%Y-%m-%d',
		'SEARCH_DOMAINS' => '',
		'8021Q_EXTSYNC_LISTSRC' => 'false',
		'8021Q_MULTILINK_LISTSRC' => 'false',
		'REVERSED_RACKS_LISTSRC' => 'false',
		'NEAREST_RACKS_CHECKBOX' => 'yes',
		'SHOW_OBJECTTYPE' => 'yes',
		'IPV4_TREE_SHOW_UNALLOCATED' => 'yes',
	);
	foreach ($defaults as $name => $value)
		setConfigVar ($name, $value);
	showFuncMessage (__FUNCTION__, 'OK');
}

// Add single record.
function addRealServer ()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 48));
	global $sic;
	$rsip_bin = assertIPArg ('rsip');
	assertStringArg ('rsport', TRUE);
	assertStringArg ('rsconfig', TRUE);
	assertStringArg ('comment', TRUE);
	addRStoRSPool
	(
		getBypassValue(),
		$rsip_bin,
		$_REQUEST['rsport'],
		isCheckSet ('inservice', 'yesno'),
		$sic['rsconfig'],
		$sic['comment']
	);
	showFuncMessage (__FUNCTION__, 'OK');
}

// Parse textarea submitted and try adding a real server for each line.
function addRealServers ()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 37, 'ERR1' => 131));
	global $sic;
	assertStringArg ('format');
	assertStringArg ('rawtext');
	$ngood = 0;
	// Keep in mind, that the text will have HTML entities (namely '>') escaped.
	foreach (explode ("\n", dos2unix ($sic['rawtext'])) as $line)
	{
		if (!strlen ($line))
			continue;
		$match = array ();
		switch ($_REQUEST['format'])
		{
			case 'ipvs_2': // address and port only
				if (!preg_match ('/^  -> ([0-9\.]+):([0-9]+) /', $line, $match))
					if (!preg_match ('/^  -> \[([0-9a-fA-F:]+)\]:([0-9]+) /', $line, $match))
						continue;
				addRStoRSPool (getBypassValue(), ip_parse ($match[1]), $match[2], getConfigVar ('DEFAULT_IPV4_RS_INSERVICE'), '');
				break;
			case 'ipvs_3': // address, port and weight
				if (!preg_match ('/^  -> ([0-9\.]+):([0-9]+) +[a-zA-Z]+ +([0-9]+) /', $line, $match))
					if (!preg_match ('/^  -> \[([0-9a-fA-F:]+)\]:([0-9]+) +[a-zA-Z]+ +([0-9]+) /', $line, $match))
						continue;
				addRStoRSPool (getBypassValue(), ip_parse ($match[1]), $match[2], getConfigVar ('DEFAULT_IPV4_RS_INSERVICE'), 'weight ' . $match[3]);
				break;
			case 'ssv_2': // IP address and port
				if (!preg_match ('/^([0-9\.a-fA-F:]+) ([0-9]+)$/', $line, $match))
					continue;
				addRStoRSPool (getBypassValue(), ip_parse ($match[1]), $match[2], getConfigVar ('DEFAULT_IPV4_RS_INSERVICE'), '');
				break;
			case 'ssv_1': // IP address
				if (! $ip_bin = ip_checkparse ($line))
					continue;
				addRStoRSPool (getBypassValue(), $ip_bin, 0, getConfigVar ('DEFAULT_IPV4_RS_INSERVICE'), '');
				break;
			default:
				showFuncMessage (__FUNCTION__, 'ERR1');
				return;
		}
		$ngood++;
	}
	showFuncMessage (__FUNCTION__, 'OK', array ($ngood));
}

function addVService ()
{
	global $sic;
	$vip_bin = assertIPArg ('vip');
	genericAssertion ('proto', 'enum/ipproto');
	assertStringArg ('name', TRUE);
	assertStringArg ('vsconfig', TRUE);
	assertStringArg ('rsconfig', TRUE);
	if ($_REQUEST['proto'] == 'MARK')
		$vport = NULL;
	else
	{
		assertUIntArg ('vport');
		$vport = $_REQUEST['vport'];
	}
	usePreparedInsertBlade
	(
		'IPv4VS',
		array
		(
			'vip' => $vip_bin,
			'vport' => $vport,
			'proto' => $_REQUEST['proto'],
			'name' => nullIfEmptyStr ($_REQUEST['name']),
			'vsconfig' => nullIfEmptyStr ($sic['vsconfig']),
			'rsconfig' => nullIfEmptyStr ($sic['rsconfig']),
		)
	);
	$vs_id = lastInsertID();
	lastCreated ('ipv4vs', $vs_id);
	if (isset ($_REQUEST['taglist']))
		produceTagsForNewRecord ('ipv4vs', $_REQUEST['taglist'], $vs_id);
	$vsinfo = spotEntity ('ipv4vs', $vs_id);
	showSuccess (mkCellA ($vsinfo) . ' created successfully');
}

function addVSG ()
{
	$name = assertStringArg ('name');
	usePreparedInsertBlade ('VS', array ('name' => $name));
	$vs_id = lastInsertID();
	lastCreated ('ipvs', $vs_id);
	if (isset ($_REQUEST['taglist']))
		produceTagsForNewRecord ('ipvs', $_REQUEST['taglist'], $vs_id);
	$vsinfo = spotEntity ('ipvs', $vs_id);
	showSuccess (mkCellA ($vsinfo) . ' created successfully');
}

function deleteVService ()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 49));
	assertUIntArg ('vs_id');
	$vsinfo = spotEntity ('ipv4vs', $_REQUEST['vs_id']);
	if ($vsinfo['refcnt'] != 0)
	{
		showError ("Could not delete linked virtual service");
		return;
	}
	commitDeleteVS ($vsinfo['id']);
	showFuncMessage (__FUNCTION__, 'OK');
	return buildRedirectURL ('ipv4slb', 'default');
}

function deleteVS()
{
	$vsinfo = spotEntity ('ipvs', assertUIntArg ('vs_id'));
	if (count (getTriplets ($vsinfo)) != 0)
	{
		showError ("Could not delete linked virtual service group");
		return;
	}
	commitDeleteVSG ($vsinfo['id']);
	showSuccess (formatEntityName ($vsinfo) . ' deleted');
	return buildRedirectURL ('ipv4slb', 'vs');
}

function updateSLBDefConfig ()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 43));
	global $sic;
	commitUpdateSLBDefConf
	(
		array
		(
			'vs' => $sic['vsconfig'],
			'rs' => $sic['rsconfig'],
		)
	);
	showFuncMessage (__FUNCTION__, 'OK');
}

function updateRealServer ()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 51));
	global $sic;
	assertUIntArg ('rs_id');
	$rsip_bin = assertIPArg ('rsip');
	assertStringArg ('rsport', TRUE);
	assertStringArg ('rsconfig', TRUE);
	assertStringArg ('comment', TRUE);
	commitUpdateRS (
		$_REQUEST['rs_id'],
		$rsip_bin,
		$_REQUEST['rsport'],
		isCheckSet ('inservice', 'yesno'),
		$sic['rsconfig'],
		$sic['comment']
	);
	showFuncMessage (__FUNCTION__, 'OK');
}

function updateVService ()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 51));
	global $sic;
	assertUIntArg ('vs_id');
	$taglist = genericAssertion ('taglist', 'array0');
	$vip_bin = assertIPArg ('vip');
	genericAssertion ('proto', 'enum/ipproto');
	if ($_REQUEST['proto'] == 'MARK')
		assertStringArg ('vport', TRUE);
	else
		assertUIntArg ('vport');
	assertStringArg ('name', TRUE);
	assertStringArg ('vsconfig', TRUE);
	assertStringArg ('rsconfig', TRUE);
	commitUpdateVS (
		$_REQUEST['vs_id'],
		$vip_bin,
		$_REQUEST['vport'],
		$_REQUEST['proto'],
		$_REQUEST['name'],
		$sic['vsconfig'],
		$sic['rsconfig']
	);
	rebuildTagChainForEntity ('ipvs', $_REQUEST['vs_id'], buildTagChainFromIds ($taglist), TRUE);
	showFuncMessage (__FUNCTION__, 'OK');
}

function updateVS ()
{
	$taglist = genericAssertion ('taglist', 'array0');
	$vs_id = assertUIntArg ('vs_id');
	$name = assertStringArg ('name');
	$vsconfig = nullIfEmptyStr (assertStringArg ('vsconfig', TRUE));
	$rsconfig = nullIfEmptyStr (assertStringArg ('rsconfig', TRUE));

	usePreparedUpdateBlade ('VS', array ('name' => $name, 'vsconfig' => $vsconfig, 'rsconfig' => $rsconfig), array ('id' => $vs_id));
	rebuildTagChainForEntity ('ipvs', $vs_id, buildTagChainFromIds ($taglist), TRUE);
	showSuccess ("Service updated successfully");
}

function addIPToVS()
{
	$ip_bin = assertIPArg ('ip');
	$vsinfo = spotEntity ('ipvs', assertUIntArg ('vs_id'));
	amplifyCell ($vsinfo);
	$row = array ('vs_id' => $vsinfo['id'], 'vip' => $ip_bin, 'vsconfig' => NULL, 'rsconfig' => NULL);
	if ($vip = isVIPEnabled ($row, $vsinfo['vips']))
	{
		showError ("Service already contains IP " . formatVSIP ($vip));
		return;
	}
	usePreparedInsertBlade ('VSIPs', $row);
	showSuccess ("IP addded");
}

function addPortToVS()
{
	global $vs_proto;
	$proto = assertStringArg ('proto');
	if (! in_array ($proto, $vs_proto))
		throw new InvalidRequestArgException ('proto', "Invalid VS protocol");
	$vport = assertUIntArg ('port', TRUE);
	if ($proto == 'MARK')
	{
		if ($vport > 0xFFFFFFFF)
		{
			showError ("fwmark value is too large");
			return;
		}
	}
	else
		if ($vport == 0 || $vport >= 0xFFFF)
		{
			showError ("Invalid $proto port value");
			return;
		}

	$vsinfo = spotEntity ('ipvs', assertUIntArg ('vs_id'));
	amplifyCell ($vsinfo);
	$row = array ('vs_id' => $vsinfo['id'], 'proto' => $proto, 'vport' => $vport, 'vsconfig' => NULL, 'rsconfig' => NULL);
	if ($port = isPortEnabled ($row, $vsinfo['ports']))
	{
		showError ("Service already contains port " . $port['proto'] . ' ' . $port['vport']);
		return;
	}
	usePreparedInsertBlade ('VSPorts', $row);
	showSuccess ("port addded");
}

function updateIPInVS()
{
	$vs_id = assertUIntArg ('vs_id');
	$ip_bin = assertIPArg ('ip');
	$vsconfig = nullIfEmptyStr (assertStringArg ('vsconfig', TRUE));
	$rsconfig = nullIfEmptyStr (assertStringArg ('rsconfig', TRUE));
	if (usePreparedUpdateBlade ('VSIPs', array ('vsconfig' => $vsconfig, 'rsconfig' => $rsconfig), array ('vs_id' => $vs_id, 'vip' => $ip_bin)))
		showSuccess ("IP configuration updated");
	else
		showNotice ("Nothing changed");
}

function updatePortInVS()
{
	$vs_id = assertUIntArg ('vs_id');
	$proto = assertStringArg ('proto');
	$vport = assertUIntArg ('port', TRUE);
	$vsconfig = nullIfEmptyStr (assertStringArg ('vsconfig', TRUE));
	$rsconfig = nullIfEmptyStr (assertStringArg ('rsconfig', TRUE));
	if (usePreparedUpdateBlade ('VSPorts', array ('vsconfig' => $vsconfig, 'rsconfig' => $rsconfig), array ('vs_id' => $vs_id, 'proto' => $proto, 'vport' => $vport)))
		showSuccess ("Port configuration updated");
	else
		showNotice ("Nothing changed");
}

function removeIPFromVS()
{
	$vip = array ('vip' => assertIPArg ('ip'));
	$vsinfo = spotEntity ('ipvs', assertUIntArg ('vs_id'));
	amplifyCell ($vsinfo);
	$used = 0;
	foreach (getTriplets ($vsinfo) as $triplet)
		if (isVIPEnabled ($vip, $triplet['vips']))
			$used++;
	if (usePreparedDeleteBlade ('VSIPs', array ('vs_id' => $vsinfo['id']) + $vip))
		showSuccess ("IP removed" . ($used ? ", it was binded with $used SLBs" : ''));
	else
		showNotice ("Nothing changed");
}

function removePortFromVS()
{
	$port = array ('proto' => assertStringArg ('proto'), 'vport' => assertUIntArg ('port', TRUE));
	$vsinfo = spotEntity ('ipvs', assertUIntArg ('vs_id'));
	amplifyCell ($vsinfo);
	$used = 0;
	foreach (getTriplets ($vsinfo) as $triplet)
		if (isPortEnabled ($port, $triplet['ports']))
			$used++;
	if (usePreparedDeleteBlade ('VSPorts', array ('vs_id' => $vsinfo['id']) + $port))
		showSuccess ("Port removed" . ($used ? ", it was binded with $used SLBs" : ''));
	else
		showNotice ("Nothing changed");
}

function updateTripletConfig()
{
	$key_fields = array
	(
		'object_id' => assertUIntArg ('object_id'),
		'vs_id' => assertUIntArg ('vs_id'),
		'rspool_id' => assertUIntArg ('rspool_id'),
	);
	$config_fields = array
	(
		'vsconfig' => nullIfEmptyStr (assertStringArg ('vsconfig', TRUE)),
		'rsconfig' => nullIfEmptyStr (assertStringArg ('rsconfig', TRUE)),
	);

	$vsinfo = spotEntity ('ipvs', $key_fields['vs_id']);
	amplifyCell ($vsinfo);
	$found = FALSE;

	if ($_REQUEST['op'] == 'updPort')
	{
		$table = 'VSEnabledPorts';
		$proto = assertStringArg ('proto');
		$vport = assertUIntArg ('port', TRUE);
		$key_fields['proto'] = $proto;
		$key_fields['vport'] = $vport;
		$key = "Port $proto-$vport";
		// check if such port exists in VS
		foreach ($vsinfo['ports'] as $vs_port)
			if ($vs_port['proto'] == $proto && $vs_port['vport'] == $vport)
			{
				$found = TRUE;
				break;
			}
	}
	else
	{
		$table = 'VSEnabledIPs';
		$vip = assertIPArg ('vip');
		$config_fields['prio'] = nullIfEmptyStr (assertStringArg ('prio', TRUE));
		$key_fields['vip'] = $vip;
		$key = "IP " . ip_format ($vip);
		// check if such VIP exists in VS
		foreach ($vsinfo['vips'] as $vs_vip)
			if ($vs_vip['vip'] === $vip)
			{
				$found = TRUE;
				break;
			}
	}
	if (! $found)
	{
		showError ("$key not found in VS");
		return;
	}

	$nchanged = 0;
	if (! isCheckSet ('enabled'))
	{
		if ($nchanged += usePreparedDeleteBlade ($table, $key_fields))
		{
			showSuccess ("$key disabled");
			return;
		}
	}
	else
	{
		global $dbxlink;
		$dbxlink->beginTransaction();
		$q = "SELECT * FROM $table WHERE";
		$sep = '';
		$params = array();
		foreach ($key_fields as $field => $value)
		{
			$q .= " $sep $field = ?";
			$params[] = $value;
			$sep = 'AND';
		}
		$result = usePreparedSelectBlade ("$q FOR UPDATE", $params);
		$row = $result->fetch (PDO::FETCH_ASSOC);
		unset ($result);
		if ($row)
		{
			if ($nchanged += usePreparedUpdateBlade ($table, $config_fields, $key_fields))
				showSuccess ("$key config updated");
		}
		else
		{
			if (
				$nchanged += ($table == 'VSEnabledIPs' ?
					addSLBIPLink ($key_fields + $config_fields) :
					addSLBPortLink ($key_fields + $config_fields)
				)
			)
				showSuccess ("$key enabled");
		}
		$dbxlink->commit();
	}
	if (! $nchanged)
		showNotice ("No changes made");
}

function removeTriplet()
{
	$key_fields = array
	(
		'object_id' => assertUIntArg ('object_id'),
		'vs_id' => assertUIntArg ('vs_id'),
		'rspool_id' => assertUIntArg ('rspool_id'),
	);

	global $dbxlink;
	$dbxlink->beginTransaction();
	usePreparedDeleteBlade ('VSEnabledIPs', $key_fields);
	usePreparedDeleteBlade ('VSEnabledPorts', $key_fields);
	$dbxlink->commit();
	showSuccess ('Triplet deleted');
}

function createTriplet()
{
	global $dbxlink;
	$object_id = assertUIntArg ('object_id');
	$vs_id = assertUIntArg ('vs_id');
	$rspool_id = assertUIntArg ('rspool_id');
	$vips = genericAssertion ('enabled_vips', 'array0');
	$ports = genericAssertion ('enabled_ports', 'array0');

	$vsinfo = spotEntity ('ipvs', $vs_id);
	amplifyCell ($vsinfo);
	try
	{
		$dbxlink->beginTransaction();
		foreach ($vsinfo['vips'] as $vip)
			if (in_array (ip_format ($vip['vip']), $vips))
				addSLBIPLink (array ('object_id' => $object_id, 'vs_id' => $vs_id, 'rspool_id' => $rspool_id, 'vip' => $vip['vip']));
		foreach ($vsinfo['ports'] as $port)
			if (in_array($port['proto'] . '-' . $port['vport'], $ports))
				addSLBPortLink (array ('object_id' => $object_id, 'vs_id' => $vs_id, 'rspool_id' => $rspool_id, 'proto' => $port['proto'], 'vport' => $port['vport']));
		$dbxlink->commit();
	}
	catch (RTDatabaseError $e)
	{
		$dbxlink->rollBack();
		throw $e;
	}
	showSuccess ("SLB triplet created");
}

function addLoadBalancer ()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 48));
	global $sic;
	assertUIntArg ('pool_id');
	assertUIntArg ('object_id');
	assertUIntArg ('vs_id');
	assertStringArg ('vsconfig', TRUE);
	assertStringArg ('rsconfig', TRUE);
	assertStringArg ('prio', TRUE);

	addLBtoRSPool (
		$_REQUEST['pool_id'],
		$_REQUEST['object_id'],
		$_REQUEST['vs_id'],
		$sic['vsconfig'],
		$sic['rsconfig'],
		$_REQUEST['prio']
	);
	showFuncMessage (__FUNCTION__, 'OK');
}

function addRSPool ()
{
	global $sic;
	assertStringArg ('name');
	assertStringArg ('vsconfig', TRUE);
	assertStringArg ('rsconfig', TRUE);
	$pool_id = commitCreateRSPool
	(
		$_REQUEST['name'],
		$sic['vsconfig'],
		$sic['rsconfig'],
		isset ($_REQUEST['taglist']) ? $_REQUEST['taglist'] : array()
	);
	showSuccess ('RS pool ' . mkA ($_REQUEST['name'], 'ipv4rspool', $pool_id) . ' created successfully');
}

function deleteRSPool ()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 49));
	assertUIntArg ('pool_id');
	$poolinfo = spotEntity ('ipv4rspool', $_REQUEST['pool_id']);
	if ($poolinfo['refcnt'] != 0)
	{
		showError ("Could not delete linked RS pool");
		return;
	}
	commitDeleteRSPool ($poolinfo['id']);
	showFuncMessage (__FUNCTION__, 'OK');
	return buildRedirectURL ('ipv4slb', 'rspools');
}

function importPTRData ()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 26, 'ERR' => 141));
	$net = spotEntity ('ipv4net', getBypassValue());
	assertUIntArg ('addrcount');
	$nbad = $ngood = 0;
	for ($i = 1; $i <= $_REQUEST['addrcount']; $i++)
	{
		$inputname = "import_${i}";
		if (! isCheckSet ($inputname))
			continue;
		$ip_bin = assertIPv4Arg ("addr_${i}");
		assertStringArg ("descr_${i}", TRUE);
		assertStringArg ("rsvd_${i}");
		// Non-existent addresses will not have this argument set in request.
		$rsvd = 'no';
		if ($_REQUEST["rsvd_${i}"] == 'yes')
			$rsvd = 'yes';
		try
		{
			if (! ip_in_range ($ip_bin, $net))
				throw new InvalidArgException ('ip_bin', $ip_bin);
			updateAddress ($ip_bin, $_REQUEST["descr_${i}"], $rsvd);
			$ngood++;
		}
		catch (RackTablesError $e)
		{
			$nbad++;
		}
	}
	if (!$nbad)
		showFuncMessage (__FUNCTION__, 'OK', array ($ngood));
	else
		showFuncMessage (__FUNCTION__, 'ERR', array ($nbad, $ngood));
}

function generateAutoPorts ()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 21));
	$object = spotEntity ('object', getBypassValue());
	executeAutoPorts ($object['id']);
	showFuncMessage (__FUNCTION__, 'OK');
	return buildRedirectURL (NULL, 'ports');
}

function updateTag ()
{
	try
	{
		commitUpdateTag
		(
			genericAssertion ('tag_id', 'uint'),
			genericAssertion ('tag_name', 'tag'),
			genericAssertion ('parent_id', 'uint0'),
			genericAssertion ('is_assignable', 'enum/yesno')
		);
	}
	catch (InvalidArgException $iae)
	{
		throw $iae->newIRAE ('parent_id');
	}
	showSuccess ('Tag updated successfully');
}

function saveEntityTags ()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 43));
	$realm = etypeByPageno();
	$entity_id = getBypassValue();
	$taglist = isset ($_REQUEST['taglist']) ? $_REQUEST['taglist'] : array();
	rebuildTagChainForEntity ($realm, $entity_id, buildTagChainFromIds ($taglist), TRUE);
	showFuncMessage (__FUNCTION__, 'OK');
}

function rollTags ()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 67, 'ERR' => 149));
	assertStringArg ('sum', TRUE);
	assertUIntArg ('realsum');
	if ($_REQUEST['sum'] != $_REQUEST['realsum'])
	{
		showFuncMessage (__FUNCTION__, 'ERR');
		return;
	}
	// Even if the user requested an empty tag list, don't bail out, but process existing
	// tag chains with "zero" extra. This will make sure, that the stuff processed will
	// have its chains refined to "normal" form.
	$extratags = isset ($_REQUEST['taglist']) ? $_REQUEST['taglist'] : array();
	$n_ok = 0;
	// Minimizing the extra chain early, so that tag rebuilder doesn't have to
	// filter out the same tag again and again. It will have own noise to cancel.
	$extrachain = getExplicitTagsOnly (buildTagChainFromIds ($extratags));
	foreach (listCells ('rack', getBypassValue()) as $rack)
	{
		if (rebuildTagChainForEntity ('rack', $rack['id'], $extrachain))
			$n_ok++;
		amplifyCell ($rack);
		foreach ($rack['mountedObjects'] as $object_id)
			if (rebuildTagChainForEntity ('object', $object_id, $extrachain))
				$n_ok++;
	}
	showFuncMessage (__FUNCTION__, 'OK', array ($n_ok));
}

function changeMyPassword ()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 51, 'ERR1' => 150, 'ERR2' => 151, 'ERR3' => 152));
	global $remote_username, $user_auth_src;
	if ($user_auth_src != 'database')
	{
		showFuncMessage (__FUNCTION__, 'ERR1');
		return;
	}
	assertStringArg ('oldpassword');
	assertStringArg ('newpassword1');
	assertStringArg ('newpassword2');
	$remote_userid = getUserIDByUsername ($remote_username);
	$userinfo = spotEntity ('user', $remote_userid);
	if ($userinfo['user_password_hash'] != sha1 ($_REQUEST['oldpassword']))
	{
		showFuncMessage (__FUNCTION__, 'ERR2');
		return;
	}
	if ($_REQUEST['newpassword1'] != $_REQUEST['newpassword2'])
	{
		showFuncMessage (__FUNCTION__, 'ERR3');
		return;
	}
	commitUpdateUserAccount ($remote_userid, $userinfo['user_name'], $userinfo['user_realname'], sha1 ($_REQUEST['newpassword1']));
	showFuncMessage (__FUNCTION__, 'OK');
}

function saveRackCode ()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 43, 'ERR1' => 154));
	assertStringArg ('rackcode');
	// For the test to succeed, unescape LFs, strip CRs.
	$newcode = dos2unix ($_REQUEST['rackcode']);
	$parseTree = getRackCode ($newcode);
	if ($parseTree['result'] != 'ACK')
	{
		showFuncMessage (__FUNCTION__, 'ERR1', array ($parseTree['load']));
		return;
	}
	saveScript ('RackCode', $newcode);
	saveScript ('RackCodeCache', base64_encode (serialize ($parseTree)));
	showFuncMessage (__FUNCTION__, 'OK');
}

function submitSLBConfig ()
{
	showNotice ("You should redefine submitSLBConfig ophandler in your local extension to install SLB config");
}

function addLocation ()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 5));
	assertUIntArg ('parent_id', TRUE);
	assertStringArg ('name');

	$location_id = commitAddObject ($_REQUEST['name'], NULL, 1562, NULL);
	if ($_REQUEST['parent_id'])
		commitLinkEntities ('location', $_REQUEST['parent_id'], 'location', $location_id);
	showSuccess ('added location ' . mkA ($_REQUEST['name'], 'location', $location_id));
}

// This function is used by two forms:
//  - renderEditLocationForm - all attributes may be modified
//  - renderRackspaceLocationEditor - only the name and parent may be modified
function updateLocation ()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 6));
	global $pageno;
	assertUIntArg ('location_id');
	assertUIntArg ('parent_id', TRUE);
	assertStringArg ('name');

	if ($pageno == 'location')
	{
		$taglist = genericAssertion ('taglist', 'array0');
		$has_problems = (isset ($_REQUEST['has_problems']) and $_REQUEST['has_problems'] == 'on') ? 'yes' : 'no';
		assertStringArg ('comment', TRUE);
		commitUpdateObject ($_REQUEST['location_id'], $_REQUEST['name'], NULL, $has_problems, NULL, $_REQUEST['comment']);
		updateObjectAttributes ($_REQUEST['location_id']);
		rebuildTagChainForEntity ('location', $_REQUEST['location_id'], buildTagChainFromIds ($taglist), TRUE);
	}
	else
		commitRenameObject ($_REQUEST['location_id'], $_REQUEST['name']);

	$locationData = spotEntity ('location', $_REQUEST['location_id']);

	// parent_id was submitted, but no link exists - create it
	if ($_REQUEST['parent_id'] > 0 && !$locationData['parent_id'])
		commitLinkEntities ('location', $_REQUEST['parent_id'], 'location', $_REQUEST['location_id']);

	// parent_id was submitted, but it doesn't match the existing link - update it
	if ($_REQUEST['parent_id'] > 0 && $_REQUEST['parent_id'] != $locationData['parent_id'])
		commitUpdateEntityLink
		(
			'location', $locationData['parent_id'], 'location', $_REQUEST['location_id'],
			'location', $_REQUEST['parent_id'], 'location', $_REQUEST['location_id']
		);

	// no parent_id was submitted, but a link exists - delete it
	if ($_REQUEST['parent_id'] == 0 && $locationData['parent_id'])
		commitUnlinkEntities ('location', $locationData['parent_id'], 'location', $_REQUEST['location_id']);

	showFuncMessage (__FUNCTION__, 'OK', array ($_REQUEST['name']));
}

function deleteLocation ()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 7, 'ERR1' => 206));
	assertUIntArg ('location_id');
	$locationData = spotEntity ('location', $_REQUEST['location_id']);
	amplifyCell ($locationData);
	if (count ($locationData['locations']) || count ($locationData['rows']))
	{
		showFuncMessage (__FUNCTION__, 'ERR1', array ($locationData['name']));
		return;
	}
	releaseFiles ('location', $_REQUEST['location_id']);
	destroyTagsForEntity ('location', $_REQUEST['location_id']);
	commitDeleteObject ($_REQUEST['location_id']);
	showFuncMessage (__FUNCTION__, 'OK', array ($locationData['name']));
	return buildRedirectURL ('rackspace', 'editlocations');
}

function addRow ()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 5));
	assertUIntArg ('location_id', TRUE);
	assertStringArg ('name');
	$row_id = commitAddObject ($_REQUEST['name'], NULL, 1561, NULL);
	if ($_REQUEST['location_id'])
		commitLinkEntities ('location', $_REQUEST['location_id'], 'row', $row_id);
	showSuccess ('added row ' . mkA ($_REQUEST['name'], 'row', $row_id));
}

// This function is used by two forms:
//  - renderEditRowForm - all attributes may be modified
//  - renderRackspaceRowEditor - only the name and location may be modified
function updateRow ()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 6));
	assertUIntArg ('row_id');
	assertUIntArg ('location_id', TRUE);
	assertStringArg ('name');

	commitUpdateObject ($_REQUEST['row_id'], $_REQUEST['name'], NULL, 'no', NULL, NULL);

	global $pageno;
	if ($pageno == 'row')
		updateObjectAttributes ($_REQUEST['row_id']);

	$rowData = spotEntity ('row', $_REQUEST['row_id']);

	// location_id was submitted, but no link exists - create it
	if ($_REQUEST['location_id'] > 0 && !$rowData['location_id'])
		commitLinkEntities ('location', $_REQUEST['location_id'], 'row', $_REQUEST['row_id']);

	// location_id was submitted, but it doesn't match the existing link - update it
	if ($_REQUEST['location_id'] > 0 && $_REQUEST['location_id'] != $rowData['location_id'])
		commitUpdateEntityLink
		(
			'location', $rowData['location_id'], 'row', $_REQUEST['row_id'],
			'location', $_REQUEST['location_id'], 'row', $_REQUEST['row_id']
		);

	// no parent_id was submitted, but a link exists - delete it
	if ($_REQUEST['location_id'] == 0 && $rowData['location_id'])
		commitUnlinkEntities ('location', $rowData['location_id'], 'row', $_REQUEST['row_id']);

	showFuncMessage (__FUNCTION__, 'OK', array ($_REQUEST['name']));
}

function deleteRow ()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 7, 'UMOUNT' => 58));
	$row_id = assertUIntArg ('row_id');
	$rowData = spotEntity ('row', $row_id);
	$unmounted = getRowMountsCount ($row_id);
	commitDeleteRow ($row_id);
	if ($unmounted)
		showFuncMessage (__FUNCTION__, 'UMOUNT', array ($unmounted));
	showFuncMessage (__FUNCTION__, 'OK', array ($rowData['name']));
	return buildRedirectURL ('rackspace', 'editrows');
}

function addRack ()
{
	setFuncMessages (__FUNCTION__, array ('ERR2' => 172));
	$taglist = genericAssertion ('taglist', 'array0');

	// The new rack(s) should be placed on the bottom of the list, sort-wise
	$rowInfo = getRowInfo($_REQUEST['row_id']);
	$sort_order = $rowInfo['count']+1;

	if (isset ($_REQUEST['got_data']))
	{
		assertStringArg ('name');
		assertUIntArg ('height1');
		assertStringArg ('asset_no', TRUE);
		$rack_id = commitAddObject ($_REQUEST['name'], NULL, 1560, $_REQUEST['asset_no'], $taglist);

		// Set the height and sort order
		commitUpdateAttrValue ($rack_id, 27, $_REQUEST['height1']);
		commitUpdateAttrValue ($rack_id, 29, $sort_order);

		// Link it to the row
		commitLinkEntities ('row', $_REQUEST['row_id'], 'rack', $rack_id);
		showSuccess ('added rack ' . mkA ($_REQUEST['name'], 'rack', $rack_id));
	}
	elseif (isset ($_REQUEST['got_mdata']))
	{
		assertUIntArg ('height2');
		assertStringArg ('names', TRUE);
		// copy-and-paste from renderAddMultipleObjectsForm()
		$names1 = explode ("\n", $_REQUEST['names']);
		$names2 = array();
		foreach ($names1 as $line)
		{
			$parts = explode ('\r', $line);
			reset ($parts);
			if (!strlen ($parts[0]))
				continue;
			else
				$names2[] = rtrim ($parts[0]);
		}
		foreach ($names2 as $cname)
		{
			$rack_id = commitAddObject ($cname, NULL, 1560, NULL, $taglist);

			// Set the height and sort order
			commitUpdateAttrValue ($rack_id, 27, $_REQUEST['height2']);
			commitUpdateAttrValue ($rack_id, 29, $sort_order);
			$sort_order++;

			// Link it to the row
			commitLinkEntities ('row', $_REQUEST['row_id'], 'rack', $rack_id);
			showSuccess ('added rack ' . mkA ($cname, 'rack', $rack_id));
		}
	}
	else
		showFuncMessage (__FUNCTION__, 'ERR2');
}

function updateRack ()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 6));
	assertUIntArg ('row_id');
	assertStringArg ('name');
	assertUIntArg ('height');
	assertStringArg ('asset_no', TRUE);
	assertStringArg ('comment', TRUE);
	$taglist = genericAssertion ('taglist', 'array0');
	$rack_id = getBypassValue();
	usePreparedDeleteBlade ('RackThumbnail', array ('rack_id' => $rack_id));
	commitUpdateRack
	(
		$rack_id,
		$_REQUEST['row_id'],
		$_REQUEST['name'],
		$_REQUEST['height'],
		isCheckSet ('has_problems', 'yesno'),
		$_REQUEST['asset_no'],
		$_REQUEST['comment']
	);
	updateObjectAttributes ($rack_id);
	rebuildTagChainForEntity ('rack', $rack_id, buildTagChainFromIds ($taglist), TRUE);
	showFuncMessage (__FUNCTION__, 'OK', array ($_REQUEST['name']));
}

function deleteRack ()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 7, 'ERR1' => 206));
	assertUIntArg ('rack_id');
	$rackData = spotEntity ('rack', $_REQUEST['rack_id']);
	amplifyCell ($rackData);
	if (!$rackData['isDeletable'])
	{
		showFuncMessage (__FUNCTION__, 'ERR1');
		return;
	}
	commitDeleteRack ($_REQUEST['rack_id']);
	showFuncMessage (__FUNCTION__, 'OK', array ($rackData['name']));
	return buildRedirectURL ('rackspace', 'default');
}

function cleanRack ()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 58));
	$rack_id = assertUIntArg ('rack_id');
	$unmounted = getRackMountsCount ($rack_id);
	commitCleanRack ($rack_id);
	showFuncMessage (__FUNCTION__, 'OK', array ($unmounted));
}

function updateRackDesign ()
{
	$rackData = spotEntity ('rack', getBypassValue());
	amplifyCell ($rackData);
	applyRackDesignMask($rackData);
	if (processGridForm ($rackData, 'A', 'F'))
		showSuccess ("Saved successfully");
	else
		showNotice ("Nothing saved");
}

function updateRackProblems ()
{
	$rackData = spotEntity ('rack', getBypassValue());
	amplifyCell ($rackData);
	applyRackProblemMask($rackData);
	if (processGridForm ($rackData, 'F', 'U'))
		showSuccess ("Saved successfully");
	else
		showNotice ("Nothing saved");
}

function querySNMPData ()
{
	genericAssertion ('ver', 'uint');
	$snmpsetup = array ();
	switch ($_REQUEST['ver'])
	{
	case 1:
	case 2:
		genericAssertion ('community', 'string');
		$snmpsetup['community'] = $_REQUEST['community'];
		break;
	case 3:
		assertStringArg ('sec_name');
		assertStringArg ('sec_level');
		assertStringArg ('auth_protocol');
		assertStringArg ('auth_passphrase', TRUE);
		assertStringArg ('priv_protocol');
		assertStringArg ('priv_passphrase', TRUE);

		$snmpsetup['sec_name'] = $_REQUEST['sec_name'];
		$snmpsetup['sec_level'] = $_REQUEST['sec_level'];
		$snmpsetup['auth_protocol'] = $_REQUEST['auth_protocol'];
		$snmpsetup['auth_passphrase'] = $_REQUEST['auth_passphrase'];
		$snmpsetup['priv_protocol'] = $_REQUEST['priv_protocol'];
		$snmpsetup['priv_passphrase'] = $_REQUEST['priv_passphrase'];
		break;
	default:
		throw new InvalidRequestArgException ('ver', $_REQUEST['ver']);
	}
	$snmpsetup['version'] = $_REQUEST['ver'];
	doSNMPmining (getBypassValue(), $snmpsetup); // shows message by itself
}

// File-related functions
function addFileWithoutLink ()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 5, 'ERR1' => 207));
	assertStringArg ('comment', TRUE);

	// Make sure the file can be uploaded
	if (get_cfg_var('file_uploads') != 1)
		throw new RackTablesError ('file uploads not allowed, change "file_uploads" parameter in php.ini', RackTablesError::MISCONFIGURED);

	// Exit if the upload failed
	if ($_FILES['file']['error'])
	{
		showFuncMessage (__FUNCTION__, 'ERR1', array ($_FILES['file']['error']));
		return;
	}
	if (FALSE === $fp = fopen($_FILES['file']['tmp_name'], 'rb'))
	{
		showFuncMessage (__FUNCTION__, 'ERR1', array ('failed to access the temporary file'));
		return;
	}

	global $sic;
	$file_id = commitAddFile ($_FILES['file']['name'], $_FILES['file']['type'], $fp, $sic['comment']);
	if (isset ($_REQUEST['taglist']))
		produceTagsForNewRecord ('file', $_REQUEST['taglist'], $file_id);
	showFuncMessage (__FUNCTION__, 'OK', array (htmlspecialchars ($_FILES['file']['name'])));
}

function addFileToEntity ()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 5, 'ERR1' => 207));
	$realm = etypeByPageno();
	assertStringArg ('comment', TRUE);

	// Make sure the file can be uploaded
	if (get_cfg_var('file_uploads') != 1)
		throw new RackTablesError ('file uploads not allowed, change "file_uploads" parameter in php.ini', RackTablesError::MISCONFIGURED);

	// Exit if the upload failed
	if ($_FILES['file']['error'])
	{
		showFuncMessage (__FUNCTION__, 'ERR1', array ($_FILES['file']['error']));
		return;
	}
	if (FALSE === $fp = fopen($_FILES['file']['tmp_name'], 'rb'))
	{
		showFuncMessage (__FUNCTION__, 'ERR1', array ('failed to access the temporary file'));
		return;
	}

	global $sic;
	commitAddFile ($_FILES['file']['name'], $_FILES['file']['type'], $fp, $sic['comment']);
	usePreparedInsertBlade
	(
		'FileLink',
		array
		(
			'file_id' => lastInsertID(),
			'entity_type' => $realm,
			'entity_id' => getBypassValue(),
		)
	);
	showFuncMessage (__FUNCTION__, 'OK', array (htmlspecialchars ($_FILES['file']['name'])));
}

function linkFileToEntity ()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 71));
	assertUIntArg ('file_id');
	global $sic;

	usePreparedInsertBlade
	(
		'FileLink',
		array
		(
			'file_id' => $sic['file_id'],
			'entity_type' => etypeByPageno(),
			'entity_id' => getBypassValue(),
		)
	);
	$fi = spotEntity ('file', $sic['file_id']);
	showFuncMessage (__FUNCTION__, 'OK', array (htmlspecialchars ($fi['name'])));
}

function replaceFile ()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 7, 'ERR2' => 201));
	// Make sure the file can be uploaded
	if (get_cfg_var('file_uploads') != 1)
		throw new RackTablesError ('file uploads not allowed, change "file_uploads" parameter in php.ini', RackTablesError::MISCONFIGURED);
	$shortInfo = spotEntity ('file', getBypassValue());

	if (FALSE === $fp = fopen ($_FILES['file']['tmp_name'], 'rb'))
	{
		showFuncMessage (__FUNCTION__, 'ERR2');
		return;
	}
	commitReplaceFile ($shortInfo['id'], $fp);

	showFuncMessage (__FUNCTION__, 'OK', array (htmlspecialchars ($shortInfo['name'])));
}

function unlinkFile ()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 72));
	assertUIntArg ('link_id');
	commitUnlinkFile ($_REQUEST['link_id']);
	showFuncMessage (__FUNCTION__, 'OK');
}

function deleteFile ()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 7));
	assertUIntArg ('file_id');
	$shortInfo = spotEntity ('file', $_REQUEST['file_id']);
	commitDeleteFile ($_REQUEST['file_id']);
	showFuncMessage (__FUNCTION__, 'OK', array (htmlspecialchars ($shortInfo['name'])));
}

function updateFileText ()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 6, 'ERR1' => 179, 'ERR2' => 155));
	assertStringArg ('mtime_copy');
	assertStringArg ('file_text', TRUE); // it's Ok to save empty
	$shortInfo = spotEntity ('file', getBypassValue());
	if ($shortInfo['mtime'] != $_REQUEST['mtime_copy'])
	{
		showFuncMessage (__FUNCTION__, 'ERR1');
		return;
	}
	global $sic;
	commitReplaceFile ($shortInfo['id'], $sic['file_text']);
	showFuncMessage (__FUNCTION__, 'OK', array (htmlspecialchars ($shortInfo['name'])));
}

function addIIFOIFCompatPack ()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 37));
	genericAssertion ('standard', 'enum/wdmstd');
	genericAssertion ('iif_id', 'iif');
	global $wdm_packs, $sic;
	$ngood = 0;
	foreach ($wdm_packs[$sic['standard']]['oif_ids'] as $oif_id)
	{
		commitSupplementPIC ($sic['iif_id'], $oif_id);
		$ngood++;
	}
	showFuncMessage (__FUNCTION__, 'OK', array ($ngood));
}

function addOIFCompat ()
{
	$type1 = assertUIntArg ('type1');
	$type2 = assertUIntArg ('type2');
	$n_changed = addPortOIFCompat ($type1, $type2);
	showSuccess ("$n_changed row(s) added");
}

function delOIFCompat ()
{
	$type1 = assertUIntArg ('type1');
	$type2 = assertUIntArg ('type2');
	$n_changed = deletePortOIFCompat ($type1, $type2);
	showSuccess ("$n_changed row(s) deleted");
}

function delIIFOIFCompatPack ()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 38));
	genericAssertion ('standard', 'enum/wdmstd');
	genericAssertion ('iif_id', 'iif');
	global $wdm_packs, $sic;
	$ngood = 0;
	foreach ($wdm_packs[$sic['standard']]['oif_ids'] as $oif_id)
	{
		usePreparedDeleteBlade ('PortInterfaceCompat', array ('iif_id' => $sic['iif_id'], 'oif_id' => $oif_id));
		$ngood++;
	}
	showFuncMessage (__FUNCTION__, 'OK', array ($ngood));
}

function addOIFCompatPack ()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 21));
	genericAssertion ('standard', 'enum/wdmstd');
	global $wdm_packs;
	$oifs = $wdm_packs[$_REQUEST['standard']]['oif_ids'];
	foreach ($oifs as $oif_id_1)
	{
		$args = $qmarks = array();
		$query = 'REPLACE INTO PortCompat (type1, type2) VALUES ';
		foreach ($oifs as $oif_id_2)
		{
			$qmarks[] = '(?, ?)';
			$args[] = $oif_id_1;
			$args[] = $oif_id_2;
		}
		$query .= implode (', ', $qmarks);
		usePreparedExecuteBlade ($query, $args);
	}
	showFuncMessage (__FUNCTION__, 'OK');
}

function delOIFCompatPack ()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 21));
	genericAssertion ('standard', 'enum/wdmstd');
	global $wdm_packs;
	$oifs = $wdm_packs[$_REQUEST['standard']]['oif_ids'];
	foreach ($oifs as $oif_id_1)
		foreach ($oifs as $oif_id_2)
			if ($oif_id_1 != $oif_id_2) # leave narrow-band mapping intact
				usePreparedDeleteBlade ('PortCompat', array ('type1' => $oif_id_1, 'type2' => $oif_id_2));
	showFuncMessage (__FUNCTION__, 'OK');
}

function add8021QOrder ()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 48));
	assertUIntArg ('vdom_id');
	assertUIntArg ('object_id');
	assertUIntArg ('vst_id');
	global $sic, $pageno;
	fixContext();
	if ($pageno != 'object')
		spreadContext (spotEntity ('object', $sic['object_id']));
	if ($pageno != 'vst')
		spreadContext (spotEntity ('vst', $sic['vst_id']));
	assertPermission();
	usePreparedExecuteBlade
	(
		'INSERT INTO VLANSwitch (domain_id, object_id, template_id, last_change, out_of_sync) ' .
		'VALUES (?, ?, ?, NOW(), "yes")',
		array ($sic['vdom_id'], $sic['object_id'], $sic['vst_id'])
	);
	showFuncMessage (__FUNCTION__, 'OK');
}

function del8021QOrder ()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 49));
	assertUIntArg ('object_id');
	assertUIntArg ('vdom_id');
	assertUIntArg ('vst_id');
	global $sic, $pageno;
	fixContext();
	if ($pageno != 'object')
		spreadContext (spotEntity ('object', $sic['object_id']));
	if ($pageno != 'vst')
		spreadContext (spotEntity ('vst', $sic['vst_id']));
	assertPermission();
	usePreparedDeleteBlade ('VLANSwitch', array ('object_id' => $sic['object_id']));
	$focus_hints = array
	(
		'prev_objid' => $_REQUEST['object_id'],
		'prev_vstid' => $_REQUEST['vst_id'],
		'prev_vdid' => $_REQUEST['vdom_id'],
	);
	showFuncMessage (__FUNCTION__, 'OK');
	return buildRedirectURL (NULL, NULL, $focus_hints);
}

function createVLANDomain ()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 48));
	assertStringArg ('vdom_descr');
	global $sic;
	usePreparedInsertBlade
	(
		'VLANDomain',
		array
		(
			'description' => $sic['vdom_descr'],
		)
	);
	$domain_id = lastInsertID();
	lastCreated ('vdom', $domain_id);
	usePreparedInsertBlade
	(
		'VLANDescription',
		array
		(
			'domain_id' => $domain_id,
			'vlan_id' => VLAN_DFL_ID,
			'vlan_type' => 'compulsory',
			'vlan_descr' => 'default',
		)
	);
	showFuncMessage (__FUNCTION__, 'OK');
}

function save8021QPorts ()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 21));
	global $sic;
	assertUIntArg ('mutex_rev', TRUE); // counts from 0
	assertStringArg ('form_mode');
	if ($sic['form_mode'] != 'save' and $sic['form_mode'] != 'duplicate')
		throw new InvalidRequestArgException ('form_mode', $sic['form_mode']);
	$extra = array();

	// prepare the $changes array
	$changes = array();
	switch ($sic['form_mode'])
	{
	case 'save':
		assertUIntArg ('nports');
		if ($sic['nports'] == 1)
		{
			assertStringArg ('pn_0');
			$extra = array ('port_name' => $sic['pn_0']);
		}
		for ($i = 0; $i < $sic['nports']; $i++)
		{
			assertStringArg ('pn_' . $i);
			assertStringArg ('pm_' . $i);
			// An access port only generates form input for its native VLAN,
			// which we derive allowed VLAN list from.
			$native = isset ($sic['pnv_' . $i]) ? $sic['pnv_' . $i] : 0;
			switch ($sic["pm_${i}"])
			{
			case 'trunk':
#				assertArrayArg ('pav_' . $i);
				$allowed = isset ($sic['pav_' . $i]) ? $sic['pav_' . $i] : array();
				break;
			case 'access':
				if ($native == 'same')
					continue 2;
				assertUIntArg ('pnv_' . $i);
				$allowed = array ($native);
				break;
			default:
				throw new InvalidRequestArgException ("pm_${i}", $_REQUEST["pm_${i}"], 'unknown port mode');
			}
			$changes[$sic['pn_' . $i]] = array
			(
				'mode' => $sic['pm_' . $i],
				'allowed' => $allowed,
				'native' => $native,
			);
		}
		break;
	case 'duplicate':
		assertStringArg ('from_port');
#			assertArrayArg ('to_ports');
		$before = getStored8021QConfig ($sic['object_id'], 'desired');
		if (!array_key_exists ($sic['from_port'], $before))
			throw new InvalidArgException ('from_port', $sic['from_port'], 'this port does not exist');
		foreach ($sic['to_ports'] as $tpn)
			if (!array_key_exists ($tpn, $before))
				throw new InvalidArgException ('to_ports[]', $tpn, 'this port does not exist');
			elseif ($tpn != $sic['from_port'])
				$changes[$tpn] = $before[$sic['from_port']];
		break;
	}
	apply8021qChangeRequest ($sic['object_id'], $changes, TRUE, $sic['mutex_rev']);
	return buildRedirectURL (NULL, NULL, $extra);
}

function bindVLANtoIPv4 ()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 48));
	genericAssertion ('id', 'uint');
	genericAssertion ('vlan_ck', 'uint-vlan1');
	global $sic;
	commitSupplementVLANIPv4 ($sic['vlan_ck'], $sic['id']);
	showFuncMessage (__FUNCTION__, 'OK');
}

function bindVLANtoIPv6 ()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 48));
	genericAssertion ('id', 'uint');
	genericAssertion ('vlan_ck', 'uint-vlan1');
	global $sic;
	commitSupplementVLANIPv6 ($sic['vlan_ck'], $_REQUEST['id']);
	showFuncMessage (__FUNCTION__, 'OK');
}

function unbindVLANfromIPv4 ()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 49));
	genericAssertion ('id', 'uint');
	genericAssertion ('vlan_ck', 'uint-vlan1');
	global $sic;
	commitReduceVLANIPv4 ($sic['vlan_ck'], $sic['id']);
	showFuncMessage (__FUNCTION__, 'OK');
}

function unbindVLANfromIPv6 ()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 49));
	genericAssertion ('id', 'uint');
	genericAssertion ('vlan_ck', 'uint-vlan1');
	global $sic;
	commitReduceVLANIPv6 ($sic['vlan_ck'], $sic['id']);
	showFuncMessage (__FUNCTION__, 'OK');
}

function process8021QSyncRequest ()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 63, 'ERR' => 191));
	// behave depending on current operation: exec8021QPull or exec8021QPush
	global $sic, $op;
	if (FALSE === $done = exec8021QDeploy ($sic['object_id'], $op == 'exec8021QPush'))
		showFuncMessage (__FUNCTION__, 'ERR');
	else
		showFuncMessage (__FUNCTION__, 'OK', array ($done));
}

function process8021QRecalcRequest ()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 87));
	assertPermission (NULL, NULL, NULL, array (array ('tag' => '$op_recalc8021Q')));
	$counters = recalc8021QPorts (getBypassValue());
	if ($counters['ports'])
		showFuncMessage (__FUNCTION__, 'OK', array ($counters['ports'], $counters['switches']));
	else
		showNotice ('No changes were made');
}

function resolve8021QConflicts ()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 63, 'ERR1' => 179, 'ERR2' => 109));
	global $sic, $dbxlink;
	assertUIntArg ('mutex_rev', TRUE); // counts from 0
	assertUIntArg ('nrows');
	// Divide submitted radio buttons into 3 groups:
	// left (saved version wins)
	// asis (ignore)
	// right (running version wins)
	$F = array();
	for ($i = 0; $i < $sic['nrows']; $i++)
	{
		if (!array_key_exists ("i_${i}", $sic))
			continue;
		// let's hope other inputs are in place
		switch ($sic["i_${i}"])
		{
		case 'left':
		case 'right':
			$F[$sic["pn_${i}"]] = array
			(
				'mode' => $sic["rm_${i}"],
				'allowed' => array_fetch ($sic, "ra_${i}", array()),
				'native' => $sic["rn_${i}"],
				'decision' => $sic["i_${i}"],
			);
			break;
		default:
			// don't care
		}
	}
	$dbxlink->beginTransaction();
	try
	{
		if (NULL === $vswitch = getVLANSwitchInfo ($sic['object_id'], 'FOR UPDATE'))
			throw new InvalidArgException ('object_id', $sic['object_id'], 'VLAN domain is not set for this object');
		if ($vswitch['mutex_rev'] != $sic['mutex_rev'])
			throw new InvalidRequestArgException ('mutex_rev', $sic['mutex_rev'], 'expired form (table data has changed)');
		$D = getStored8021QConfig ($vswitch['object_id'], 'desired');
		$C = getStored8021QConfig ($vswitch['object_id'], 'cached');
		$R = getRunning8021QConfig ($vswitch['object_id']);
		$plan = get8021QSyncOptions ($vswitch, $D, $C, $R['portdata']);
		$ndone = 0;
		foreach ($F as $port_name => $port)
		{
			if (!array_key_exists ($port_name, $plan))
				continue;
			elseif ($plan[$port_name]['status'] == 'merge_conflict')
			{
				// for R neither mutex nor revisions can be emulated, but revision change can be
				if (!same8021QConfigs ($port, $R['portdata'][$port_name]))
					throw new InvalidRequestArgException ("port ${port_name}", '(hidden)', 'expired form (switch data has changed)');
				if ($port['decision'] == 'right') // D wins, frame R by writing value of R to C
					$ndone += upd8021QPort ('cached', $vswitch['object_id'], $port_name, $port, $C[$port_name]);
				elseif ($port['decision'] == 'left') // R wins, cross D up
					$ndone += upd8021QPort ('cached', $vswitch['object_id'], $port_name, $D[$port_name], $C[$port_name]);
				// otherwise there was no decision made
			}
			elseif
			(
				$plan[$port_name]['status'] == 'delete_conflict' or
				$plan[$port_name]['status'] == 'martian_conflict'
			)
				if ($port['decision'] == 'left')
					// confirm deletion of local copy
					$ndone += del8021QPort ($vswitch['object_id'], $port_name);
				// otherwise ignore a decision that doesn't address a conflict
		}
	}
	catch (InvalidRequestArgException $e)
	{
		$dbxlink->rollBack();
		showFuncMessage (__FUNCTION__, 'ERR1');
		return;
	}
	catch (Exception $e)
	{
		$dbxlink->rollBack();
		showFuncMessage (__FUNCTION__, 'ERR2');
		return;
	}
	$dbxlink->commit();
	showFuncMessage (__FUNCTION__, 'OK', array ($ndone));
}

function update8021QPortList()
{
	genericAssertion ('ports', 'array');
	$enabled = $disabled = 0;
	global $sic;
	$default_port = array
	(
		'mode' => 'access',
		'allowed' => array (VLAN_DFL_ID),
		'native' => VLAN_DFL_ID,
	);
	foreach ($sic['ports'] as $line)
		if (preg_match ('/^enable (.+)$/', $line, $m))
			$enabled += add8021QPort (getBypassValue(), $m[1], $default_port);
		elseif (preg_match ('/^disable (.+)$/', $line, $m))
			$disabled += del8021QPort (getBypassValue(), $m[1]);
		else
			throw new InvalidRequestArgException ('ports[]', $line, 'malformed array item');
	# $enabled + $disabled > 0
	if ($enabled)
		showSuccess ("enabled 802.1Q for ${enabled} port(s)");
	if ($disabled)
		showSuccess ("disabled 802.1Q for ${disabled} port(s)");
}

function cloneVST()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 48));
	assertUIntArg ('mutex_rev', TRUE);
	assertUIntArg ('from_id');
	$src_vst = spotEntity ('vst', $_REQUEST['from_id']);
	amplifyCell ($src_vst);
	commitUpdateVSTRules (getBypassValue(), $_REQUEST['mutex_rev'], $src_vst['rules']);
	showFuncMessage (__FUNCTION__, 'OK');
}

function updVSTRule()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 43));
	// this is used for making throwing an invalid argument exception easier.
	function updVSTRule_get_named_param ($name, $haystack, &$last_used_name)
	{
		$last_used_name = $name;
		return isset ($haystack[$name]) ? $haystack[$name] : NULL;
	}

	global $port_role_options, $sic;
	$taglist = genericAssertion ('taglist', 'array0');
	assertUIntArg ('mutex_rev', TRUE);
	$data = genericAssertion ('template_json', 'json');
	$rule_no = 0;
	try
	{
		$last_field = '';
		foreach ($data as $rule)
		{
			$rule_no++;
			if
			(
				! isInteger (updVSTRule_get_named_param ('rule_no', $rule, $last_field))
				or ! isPCRE (updVSTRule_get_named_param ('port_pcre', $rule, $last_field))
				or NULL === updVSTRule_get_named_param ('port_role', $rule, $last_field)
				or ! array_key_exists (updVSTRule_get_named_param ('port_role', $rule, $last_field), $port_role_options)
				or NULL ===  updVSTRule_get_named_param ('wrt_vlans', $rule, $last_field)
				or ! preg_match ('/^[ 0-9\-,]*$/',  updVSTRule_get_named_param ('wrt_vlans', $rule, $last_field))
				or NULL ===  updVSTRule_get_named_param ('description', $rule, $last_field)
			)
				throw new InvalidRequestArgException ($last_field, $rule[$last_field], "rule #$rule_no");
		}
		commitUpdateVSTRules ($_REQUEST['vst_id'], $_REQUEST['mutex_rev'], $data);
	}
	catch (Exception $e)
	{
		// Every case that is soft-processed in process.php, will have the working copy available for a retry.
		if ($e instanceof InvalidRequestArgException or $e instanceof RTDatabaseError)
		{
			startSession();
			$_SESSION['vst_edited'] = $data;
			session_commit();
		}
		throw $e;
	}
	rebuildTagChainForEntity ('vst', $_REQUEST['vst_id'], buildTagChainFromIds ($taglist), TRUE);
	showFuncMessage (__FUNCTION__, 'OK');
}

function importDPData()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 44));
	global $sic, $dbxlink;
	assertUIntArg ('nports');
	$nignored = $ndone = 0;
	for ($i = 0; $i < $sic['nports']; $i++)
		if (array_key_exists ("do_${i}", $sic))
		{
			$params = array();
			assertStringArg ("ports_${i}");
			foreach (explode (',', $_REQUEST["ports_${i}"]) as $item)
			{
				$pair = explode (':', $item);
				if (count ($pair) != 2)
					continue;
				$params[$pair[0]] = $pair[1];
			}
			if (! isset ($params['a_id']) || ! isset ($params['b_id']) ||
				! intval ($params['a_id']) || ! intval ($params['b_id']))
				throw new InvalidArgException ("ports_${i}", $_REQUEST["ports_${i}"], "can not unpack port ids");

			$porta = getPortInfo ($params['a_id']);
			$portb = getPortInfo ($params['b_id']);
			if
			(
				$porta['linked'] or
				$portb['linked'] or
				($porta['object_id'] != $sic['object_id'] and $portb['object_id'] != $sic['object_id'])
			)
			{
				$nignored++;
				continue;
			}
			$oif_a = intval (@$params['a_oif']); // these parameters are optional
			$oif_b = intval (@$params['b_oif']);

			$dbxlink->beginTransaction();
			try
			{
				if ($oif_a)
				{
					commitUpdatePortOIF ($params['a_id'], $oif_a);
					$porta['oif_id'] = $oif_a;
				}
				if ($oif_b)
				{
					commitUpdatePortOIF ($params['b_id'], $oif_b);
					$portb['oif_id'] = $oif_b;
				}

				if (arePortsCompatible ($porta, $portb))
				{
					linkPorts ($params['a_id'], $params['b_id']);
					$ndone++;
					$dbxlink->commit();
				}
				else
				{
					$dbxlink->rollback();
					$nignored++;
				}
			}
			catch (RTDatabaseError $e)
			{
				$dbxlink->rollBack();
				$nignored++;
			}
		}
	showFuncMessage (__FUNCTION__, 'OK', array ($nignored, $ndone));
}

function addObjectlog ()
{
	assertStringArg ('logentry');
	global $remote_username, $sic;
	if (isset ($sic['rack_id']))
		$object_id = $sic['rack_id'];
	elseif (isset ($sic['row_id']))
		$object_id = $sic['row_id'];
	elseif (isset ($sic['location_id']))
		$object_id = $sic['location_id'];
	else
		$object_id = $sic['object_id'];

	usePreparedExecuteBlade ('INSERT INTO ObjectLog SET object_id=?, user=?, date=NOW(), content=?', array ($object_id, $remote_username, $sic['logentry']));
	showSuccess ('Log entry added');
}

function saveQuickLinks()
{
	genericAssertion ('page_list', 'array0');
	if (is_array ($_REQUEST['page_list']))
	{
		setUserConfigVar ('QUICK_LINK_PAGES', implode(',', $_REQUEST['page_list']));
		showSuccess ('Quick links list is saved');
	}
}

$ucsproductmap = array
(
	'N20-B6620-1' => 1736, # B200 M1
	'N20-B6625-1' => 1737, # B200 M2
	'N20-B6620-2' => 1741, # B250 M1
	'N20-B6625-2' => 1742, # B250 M2
	'B230-BASE-M2' => 1740, # B230 M2
	'N20-B6730-1' => 1739, # B230 M1
	'B440-BASE-M2' => 1743, # B440 M2
	'UCSB-B200-M3' => 1738, # B200 M3
	'N10-S6100' => 1755, # 6120 FI
	'UCS-FI-6248UP' => 1757, # 6248 FI
	'UCS-FI-6296UP' => 1758, # 6296 FI
	'N20-C6508' => 1735, # 5108 chassis
	'UCSB-5108-AC2' => 2220, # 5108-AC2 chassis
	'UCSB-5108-DC2' => 2221, # 5108-DC2 chassis
	'UCSB-5108-HVDC' => 2222, # 5108-HVDC chassis
	'UCSB-B420-M3' => 1744,   # B420 M3
	'UCSB-B22-M3'  => 1745,   # B22 M3
	'UCSB-B260-M4' => 2223,   # B260 M4
	'UCSB-B460-M4' => 2224,   # B460 M4
	'UCSB-B200-M4' => 2225,   # B200 M4
	'UCSC-C240-M3S' => 1754,  # C240 M3 Rackmount Server
);

function autoPopulateUCS()
{
	global $ucsproductmap;
	$ucsm_id = getBypassValue();
	$oinfo = spotEntity ('object', $ucsm_id);
	$chassis_id = array();
	$done = 0;
	# There are three request parameters (use_terminal_settings, ucs_login and
	# ucs_password) not processed here. These are asserted and used inside
	# queryTerminal().

	try
	{
		$contents = queryDevice ($ucsm_id, 'getinventory');
	}
	catch (RTGatewayError $e)
	{
		showError ($e->getMessage());
		return;
	}
	foreach ($contents as $item)
	{
		$mname = preg_replace ('#^sys/(.+)$#', $oinfo['name'] . '/\\1', $item['DN']);
		if ($item['type'] == 'NetworkElement')
		{
			$new_object_id = commitAddObject ($mname, NULL, 8, NULL);
			#    Set H/W Type for Network Switch
			if (array_key_exists ($item['model'], $ucsproductmap))
				commitUpdateAttrValue ($new_object_id, 2, $ucsproductmap[$item['model']]);
			#  	 Set Serial#
			commitUpdateAttrValue ($new_object_id, 1, $item['serial']);
			commitLinkEntities ('object', $ucsm_id, 'object', $new_object_id);
			bindIPToObject (ip_parse ($item['OOB']), $new_object_id, 'mgmt0', 'regular');
			$done++;
		}
		elseif ($item['type'] == 'EquipmentChassis')
		{
			$chassis_id[$item['DN']] = $new_object_id = commitAddObject ($mname, NULL, 1502, NULL);
			#    Set H/W Type for Server Chassis
			if (array_key_exists ($item['model'], $ucsproductmap))
				commitUpdateAttrValue ($new_object_id, 2, $ucsproductmap[$item['model']]);
			#  	 Set Serial#
			commitUpdateAttrValue ($new_object_id, 1, $item['serial']);
			commitLinkEntities ('object', $ucsm_id, 'object', $new_object_id);
			$done++;
		}
		elseif ($item['type'] == 'ComputeBlade')
		{
			if ($item['assigned'] == '')
				$new_object_id = commitAddObject ($mname, NULL, 4, NULL);
			else
			{
				$spname = preg_replace ('#.+/ls-(.+)#i', '${1}', $item['assigned']) . "(" . $oinfo['name'] . ")";
				$spname_id[$spname] = $new_object_id = commitAddObject ($spname, NULL, 4, NULL);
			}
			#    Set H/W Type for Blade Server
			if (array_key_exists ($item['model'], $ucsproductmap))
				commitUpdateAttrValue ($new_object_id, 2, $ucsproductmap[$item['model']]);
			#  	 Set Serial#
			commitUpdateAttrValue ($new_object_id, 1, $item['serial']);
			#  	 Set Slot#
			commitUpdateAttrValue ($new_object_id, 28, $item['slot']);
			$parent_name = preg_replace ('#^([^/]+)/([^/]+)/([^/]+)$#', '${1}/${2}', $item['DN']);
			if (array_key_exists ($parent_name, $chassis_id))
				commitLinkEntities ('object', $chassis_id[$parent_name], 'object', $new_object_id);
			$done++;
		}
		elseif ($item['type'] == 'VnicPort')
		{
			$spname = preg_replace ('#^([^/]+)/ls-([^/]+)/([^/]+)$#', '${2}', $item['DN']) . "(" . $oinfo['name'] . ")";
			$porttype = preg_replace ('#^([^/]+)/([^/]+)/([^-/]+)-.+$#', '${3}', $item['DN']);
			#        Add "virtual"(1469) ports for associated blades only
			if ($spid = $spname_id[$spname])
				commitAddPort ($spid, $item['name'], 1469, $porttype, $item['addr']);
		}
		elseif ($item['type'] == 'ComputeRackUnit')
		{
			if ($item['assigned'] == '')
				$new_object_id = commitAddObject ($mname, NULL, 4, NULL);
			else
			{
				$spname = preg_replace ('#.+/ls-(.+)#i', '${1}', $item['assigned']) . "(" . $oinfo['name'] . ")";
				$new_object_id = commitAddObject ($spname, NULL, 4, NULL);
			}
			# Set H/W Type for RackmountServer
			if (array_key_exists ($item['model'], $ucsproductmap))
				commitUpdateAttrValue ($new_object_id, 2, $ucsproductmap[$item['model']]);
			# Set Serial#
			commitUpdateAttrValue ($new_object_id, 1, $item['serial']);
			$parent_name = preg_replace ('#^([^/]+)/([^/]+)/([^/]+)$#', '${1}/${2}', $item['DN']);
			commitLinkEntities ('object', $ucsm_id, 'object', $new_object_id);
			$done++;
		}
	} # endfor
	showSuccess ("Auto-populated UCS Domain '${oinfo['name']}' with ${done} items");
}

function cleanupUCS()
{
	global $ucsproductmap;
	$oinfo = spotEntity ('object', getBypassValue());
	$contents = getObjectContentsList ($oinfo['id']);

	$clear = TRUE;
	foreach ($contents as $item_id)
	{
		$o = spotEntity ('object', $item_id);
		$attrs = getAttrValues ($item_id);
		# use HW type to decide if the object was produced by autoPopulateUCS()
		if (! array_key_exists (2, $attrs) or ! in_array ($attrs[2]['key'], $ucsproductmap))
		{
			showWarning ('Contained object ' . mkA ($o['dname'], 'object', $item_id) . ' is not an automatic UCS object');
			$clear = FALSE;
		}
	}
	if (! $clear)
	{
		showNotice ('nothing was deleted');
		return;
	}

	$done = 0;
	foreach ($contents as $item_id)
	{
		commitDeleteObject ($item_id);
		$done++;
	}
	showSuccess ("Removed ${done} items from UCS Domain '${oinfo['name']}'");
}

function getOpspec()
{
	global $pageno, $tabno, $op, $opspec_list;
	if (!array_key_exists ($pageno . '-' . $tabno . '-' . $op, $opspec_list))
		throw new RackTablesError ('key not found in opspec_list', RackTablesError::INTERNAL);
	$ret = $opspec_list[$pageno . '-' . $tabno . '-' . $op];
	if
	(
		!array_key_exists ('table', $ret)
		or !array_key_exists ('action', $ret)
		// add further checks here
	)
		throw new RackTablesError ('malformed array structure in opspec_list', RackTablesError::INTERNAL);
	return $ret;
}

function unlinkPort ()
{
	assertUIntArg ('port_id');
	commitUnlinkPort ($_REQUEST['port_id']);
	showSuccess ("Port unlinked successfully");
}

function clearVlan()
{
	genericAssertion ('vlan_ck', 'uint-vlan1');
	list ($vdom_id, $vlan_id) = decodeVLANCK ($_REQUEST['vlan_ck']);

	$n_cleared = pinpointDeleteVlan ($vdom_id, $vlan_id);
	if ($n_cleared > 0)
		showSuccess ("VLAN $vlan_id removed from $n_cleared ports");
}

function deleteVlan()
{
	genericAssertion ('vlan_ck', 'uint-vlan');
	list ($vdom_id, $vlan_id) = decodeVLANCK ($_REQUEST['vlan_ck']);
	$n_cleared = pinpointDeleteVlan ($vdom_id, $vlan_id);
	if ($n_cleared > 0)
		showSuccess ("VLAN $vlan_id removed from $n_cleared ports");
	// since there is no strict foreign keys refering VLANDescription, we can delete a row
	usePreparedDeleteBlade ('VLANDescription', array ('domain_id' => $vdom_id, 'vlan_id' => $vlan_id));
	showSuccess ("VLAN $vlan_id has been deleted");
	return buildRedirectURL ('vlandomain', 'default', array ('vdom_id' => $vdom_id));
}

function cloneRSPool()
{
	assertUIntArg ('pool_id');
	$pool = spotEntity ('ipv4rspool', $_REQUEST['pool_id']);
	$rs_list = getRSListInPool ($pool['id']);
	$tagidlist = array();
	foreach ($pool['etags'] as $taginfo)
		$tagidlist[] = $taginfo['id'];
	$new_id = commitCreateRSPool ($pool['name'] . ' (copy)', $pool['vsconfig'], $pool['rsconfig'], $tagidlist);
	foreach ($rs_list as $rs)
		addRStoRSPool ($new_id, $rs['rsip_bin'], $rs['rsport'], $rs['inservice'], $rs['rsconfig'], $rs['comment']);
	showSuccess ('Created a copy of pool  ' . mkA ($pool['name'], 'ipv4rspool', $pool['id']));
	return buildRedirectURL ('ipv4rspool', 'default', array ('pool_id' => $new_id));
}

function doVSMigrate()
{
	global $dbxlink;
	$vs_id = assertUIntArg ('vs_id');
	$vs_cell = spotEntity ('ipvs', $vs_id);
	amplifyCell ($vs_cell);
	$tag_ids = genericAssertion ('taglist', 'array0');
	$old_vs_list = genericAssertion ('vs_list', 'array');
	$plan = callHook ('buildVSMigratePlan', $vs_id, $old_vs_list);

	$dbxlink->beginTransaction();

	// remove all triplets
	usePreparedDeleteBlade ('VSEnabledIPs', array ('vs_id' => $vs_id));
	usePreparedDeleteBlade ('VSEnabledPorts', array ('vs_id' => $vs_id));

	// remove all VIPs and ports that are in $plan and create new ones
	foreach ($plan['vips'] as $vip)
	{
		usePreparedDeleteBlade ('VSIPs', array ('vs_id' => $vs_id, 'vip' => $vip['vip']));
		usePreparedInsertBlade ('VSIPs', array ('vs_id' => $vs_id) + $vip);
	}
	foreach ($plan['ports'] as $port)
	{
		usePreparedDeleteBlade ('VSPorts', array ('vs_id' => $vs_id, 'proto' => $port['proto'], 'vport' => $port['vport']));
		usePreparedInsertBlade ('VSPorts', array ('vs_id' => $vs_id) + $port);
	}

	// create triplets
	foreach ($plan['triplets'] as $triplet)
	{
		$tr_key = array
		(
			'vs_id' => $triplet['vs_id'],
			'object_id' => $triplet['object_id'],
			'rspool_id' => $triplet['rspool_id'],
		);

		foreach ($triplet['ports'] as $port)
			addSLBPortLink ($tr_key + $port);
		foreach ($triplet['vips'] as $vip)
			addSLBIPLink ($tr_key + $vip);
	}

	// update configs
	usePreparedUpdateBlade ('VS', $plan['properties'], array ('id' => $vs_id));

	// replace tags
	global $taglist;
	$chain = array();
	foreach ($tag_ids as $tid)
		if (! isset ($taglist[$tid]))
		{
			$dbxlink->rollback();
			showError ("Unknown tag id $tid");
		}
		else
			$chain[] = $taglist[$tid];
	rebuildTagChainForEntity ('ipvs', $vs_id, $chain, TRUE);

	$dbxlink->commit();
	showSuccess ("old VS configs were copied to VS group");
	return buildRedirectURL (NULL, 'default');
}

# validate user input and produce SQL columns per the opspec descriptor
function buildOpspecColumns ($opspec, $listname)
{
	global $sic;
	$columns = array();
	if (! array_key_exists ($listname, $opspec))
		throw new InvalidArgException ('opspec', '(malformed structure)', "missing '${listname}'");
	foreach ($opspec[$listname] as $argspec)
		switch (TRUE)
		{
		case array_key_exists ('url_argname', $argspec): # HTTP input
			genericAssertion ($argspec['url_argname'], $argspec['assertion']);
			// "table_colname" is normally used for an override, if it is not
			// set, use the URL argument name
			$table_colname = array_fetch ($argspec, 'table_colname', $argspec['url_argname']);
			$arg_value = $sic[$argspec['url_argname']];
			if (array_key_exists ('translator', $argspec))
			{
				if (! is_callable ($argspec['translator']))
					throw new RackTablesError ('opspec translator function is not callable', RackTablesError::INTERNAL);
				$arg_value = $argspec['translator'] ($arg_value);
			}
			elseif // FIXME: remove the old declaration style at a later point
			(
				($argspec['assertion'] == 'uint0' and $arg_value == 0)
				or ($argspec['assertion'] == 'string0' and $arg_value == '')
			)
				switch (TRUE)
				{
				case !array_key_exists ('if_empty', $argspec): // no action requested
					break;
				case $argspec['if_empty'] == 'NULL':
					$arg_value = NULL;
					break;
				default:
					throw new InvalidArgException ('opspec', '(malformed array structure)', '"if_empty" not recognized');
				}
			$columns[$table_colname] = $arg_value;
			break;
		case array_key_exists ('fix_argname', $argspec): # fixed column
			if (! array_key_exists ('fix_argvalue', $argspec))
				throw new InvalidArgException ('opspec', '(malformed structure)', 'missing "fix_argvalue"');
			$columns[$argspec['fix_argname']] = $argspec['fix_argvalue'];
			break;
		default:
			throw new InvalidArgException ('opspec', '(malformed structure)', 'unknown argument source');
		} // switch (TRUE)
	return $columns;
}

# execute a single SQL statement defined by an opspec descriptor
function tableHandler()
{
	$opspec = getOpspec();
	switch ($opspec['action'])
	{
	case 'INSERT':
		switch ($opspec['table'])
		{
			case 'Attribute':
				$realm = 'attr';
				break;
			case 'Chapter':
				$realm = 'chapter';
				break;
			case 'Dictionary':
				$realm = 'dict';
				break;
			case 'TagTree':
				$realm = 'tag';
				break;
			case 'VLANSwitchTemplate':
				$realm = 'vst';
				break;
			default:
				$realm = NULL;
		}
		usePreparedInsertBlade ($opspec['table'], buildOpspecColumns ($opspec, 'arglist'));
		if (isset ($realm))
			lastCreated ($realm, lastInsertID());

		$retcode = 48;
		break;
	case 'DELETE':
		usePreparedDeleteBlade
		(
			$opspec['table'],
			buildOpspecColumns ($opspec, 'arglist'),
			array_fetch ($opspec, 'conjunction', 'AND')
		);
		$retcode = 49;
		break;
	case 'UPDATE':
		usePreparedUpdateBlade
		(
			$opspec['table'],
			buildOpspecColumns ($opspec, 'set_arglist'),
			buildOpspecColumns ($opspec, 'where_arglist'),
			array_fetch ($opspec, 'conjunction', 'AND')
		);
		$retcode = 51;
		break;
	default:
		throw new InvalidArgException ('opspec/action', $opspec['action']);
	}
	showOneLiner ($retcode);
}

function updateFile ()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 6));
	$file_id = getBypassValue();
	$file_name = genericAssertion ('file_name', 'string');
	$file_type = genericAssertion ('file_type', 'string');
	$file_comment = genericAssertion ('file_comment', 'string0');
	$taglist = genericAssertion ('taglist', 'array0');
	usePreparedUpdateBlade
	(
		'File',
		array ('name' => $file_name, 'type' => $file_type, 'comment' => $file_comment),
		array ('id' => $file_id)
	);
	rebuildTagChainForEntity ('file', $file_id, buildTagChainFromIds ($taglist), TRUE);
	showFuncMessage (__FUNCTION__, 'OK', array ($file_name));
}

function editIPv4Net ()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 6));
	$net_id = getBypassValue();
	$name = genericAssertion ('name', 'string0');
	$comment = genericAssertion ('comment', 'string0');
	$taglist = genericAssertion ('taglist', 'array0');
	usePreparedUpdateBlade
	(
		'IPv4Network',
		array ('name' => $name, 'comment' => $comment),
		array ('id' => $net_id)
	);
	rebuildTagChainForEntity ('ipv4net', $net_id, buildTagChainFromIds ($taglist), TRUE);
	$netdata = spotEntity ('ipv4net', $net_id);
	showFuncMessage (__FUNCTION__, 'OK', array ("${netdata['ip']}/${netdata['mask']}"));
}

function editIPv6Net ()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 6));
	$net_id = getBypassValue();
	$name = genericAssertion ('name', 'string0');
	$comment = genericAssertion ('comment', 'string0');
	$taglist = genericAssertion ('taglist', 'array0');
	usePreparedUpdateBlade
	(
		'IPv6Network',
		array ('name' => $name, 'comment' => $comment),
		array ('id' => $net_id)
	);
	rebuildTagChainForEntity ('ipv6net', $net_id, buildTagChainFromIds ($taglist), TRUE);
	$netdata = spotEntity ('ipv6net', $net_id);
	showFuncMessage (__FUNCTION__, 'OK', array ("${netdata['ip']}/${netdata['mask']}"));
}

function updIPv4RSP ()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 6));
	$rspool_id = getBypassValue();
	$name = genericAssertion ('name', 'string0');
	$vsconfig = genericAssertion ('vsconfig', 'string0');
	$rsconfig = genericAssertion ('rsconfig', 'string0');
	$taglist = genericAssertion ('taglist', 'array0');
	usePreparedUpdateBlade
	(
		"IPv4RSPool",
		array ('name' => $name, 'vsconfig' => $vsconfig, 'rsconfig' => $rsconfig),
		array ('id' => $rspool_id)
	);
	rebuildTagChainForEntity ('ipv4rspool', $rspool_id, buildTagChainFromIds ($taglist), TRUE);
	showFuncMessage (__FUNCTION__, 'OK', array($_REQUEST['name']));
}

function editUserProperties ()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 6));
	$taglist = genericAssertion ('taglist', 'array0');
	$user_id = getBypassValue();
	rebuildTagChainForEntity ('user', $user_id, buildTagChainFromIds ($taglist), TRUE);
	$user = spotEntity ('user', $user_id);
	print_r($user);
	showFuncMessage (__FUNCTION__, 'OK', array($user['user_name']));
}

function renameObjectPorts()
{
	$object_id = getBypassValue();
	$n = 0;
	foreach (getObjectPortsAndLinks ($object_id, FALSE) as $port)
	{
		$canon_pn = shortenPortName ($port['name'], $port['object_id']);
		if ($canon_pn != $port['name'])
		{
			commitUpdatePort ($object_id, $port['id'], $canon_pn, $port['oif_id'], $port['label'], $port['l2address'], $port['reservation_comment']);
			$n++;
		}
	}
	if ($n)
		showSuccess ("Renamed $n ports");
	else
		showNotice ("Nothing renamed");
}

function consumePatchCable()
{
	if (commitModifyPatchCableAmount (genericAssertion ('id', 'uint'), -1))
		showSuccess ('consumed OK');
	else
		showError ('could not consume');
}

function replenishPatchCable()
{
	if (commitModifyPatchCableAmount (genericAssertion ('id', 'uint'), 1))
		showSuccess ('replenished OK');
	else
		showError ('could not replenish');
}

function setPatchCableAmount()
{
	setFuncMessages (__FUNCTION__, array ('OK' => 51));
	commitSetPatchCableAmount (genericAssertion ('id', 'uint'), genericAssertion ('amount', 'uint0'));
	showFuncMessage (__FUNCTION__, 'OK');
}

function updateVLANDomain()
{
	$domain_id = assertUIntArg ('vdom_id');
	$group_id = assertUIntArg ('group_id', TRUE);
	$description = assertStringArg ('vdom_descr');

	if (! $group_id)
		$group_id = NULL;
	else
	{
		$dominfo = getVLANDomain ($domain_id);
		$parent_dominfo = getVLANDomain ($group_id);
		if ($group_id == $domain_id)
			throw new InvalidRequestArgException ('group_id', $group_id, "domains should not be the same");
		if ($parent_dominfo['group_id'] || $dominfo['subdomc'])
			throw new InvalidRequestArgException ('group_id', $group_id, "Multi-level domain groups are not allowed");
	}

	usePreparedUpdateBlade ('VLANDomain', array ('group_id' => $group_id, 'description' => $description), array ('id' => $domain_id));
	showSuccess ("VLAN domain updated successfully");
}

?>
