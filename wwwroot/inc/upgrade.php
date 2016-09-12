<?php

# This file is a part of RackTables, a datacenter and server room management
# framework. See accompanying file "COPYING" for the full copyright and
# licensing information.

$relnotes = array
(
	'0.20.0' => <<<ENDOFTEXT
WARNING: This release have too many internal changes, some of them were waiting more than a year
to be released. So this release is considered "BETA" and is recommended only to curiuos users,
who agree to sacrifice the stability to the progress.

Racks and Rows are now stored in the database as Objects.  The RackObject table
was renamed to Object.  SQL views were created to ease the migration of custom
reports and scripts.

New plugins engine instead of local.php file. To make your own code stored in local.php work,
you must move the local.php file into the plugins/ directory. The name of this file does not
matter any more. You also can store multiple files in that dir, separate your plugins by features,
share them and try the plugins from other people just placing them into plugins/ dir, no more merging.
\$path_to_local_php variable has no special meaning any more.
\$racktables_confdir variable is now used only to search for secret.php file.
\$racktables_plugins_dir is a new overridable special variable pointing to plugins/ directory.

Beginning with this version it is possible to delete IP prefixes, VLANs, Virtual services
and RS pools from within theirs properties tab. So please inspect your permissions rules
to assure there are no undesired allows for deletion of these objects. To ensure this, you could
try this code in the beginning of permissions script:

	allow {userid_1} and {\$op_del}
	deny {\$op_del} and ({\$tab_edit} or {\$tab_properties})

Hardware gateways engine was rewritten in this version of RackTables. This means that
the file gateways/deviceconfig/switch.secrets.php is not used any more. To get information
about configuring connection properties and credentials in a new way please visit
http://wiki.racktables.org/index.php/Gateways

This also means that recently added features based on old API (D-Link switches and Linux
gateway support contributed by Ilya Evseev) are not working any more and waiting to be
forward-ported to new gateways API. Sorry for that.

Two new config variables appeared in this version:
  - SEARCH_DOMAINS. Comma-separated list of DNS domains which are considered "base" for your
    network. If RackTables search engine finds multiple objects based on your search input, but
    there is only one which FQDN consists of your input and one of these search domains, you will
    be redirected to this object and other results will be discarded. Such behavior was unconditional
    since 0.19.3, which caused many objections from users. So welcome this config var.
  - QUICK_LINK_PAGES. Comma-separated list of RackTables pages to display links to them on top.
    Each user could have his own list.

Also some of config variables have changed their default values in this version.
This means that upgrade script will change their values if you have them in previous default state.
This could be inconvenient, but it is the most effective way to encourage users to use new features.
If this behavior is not what you want, simply revert these variables' values:
  - SHOW_LAST_TAB               no => yes
  - IPV4_TREE_SHOW_USAGE        yes =>no (networks' usage is still available by click)
  - IPV4LB_LISTSRC              {\$typeid_4} => false
  - FILTER_DEFAULT_ANDOR        or => and (this implicitly enables the feature of dynamic tree shrinking)
  - FILTER_SUGGEST_EXTRA        no => yes (yes, we have extra logical filters!)
  - IPV4_TREE_RTR_AS_CELL       yes => no (display routers as simple text, not cell)

Also please note that variable IPV4_TREE_RTR_AS_CELL now has third special value
besides 'yes' and 'no': 'none'. Use 'none' value if you are experiencing low performance
on IP tree page. It will completely disable IP ranges scan for used/spare IPs and the
speed of IP tree will increase radically. The price is you will not see the routers in
IP tree at all.

ENDOFTEXT
,

	'0.20.1' => <<<ENDOFTEXT
The 0.20.0 release includes bug which breaks IP networks' capacity displaying on 32-bit architecture machines. To fix this, this release makes use of PHP's BC Math module. It is a new reqiurement. Most PHP distributions have this module already enabled, but if yours does not - you need yo recompile PHP.

Security context of 'ipaddress' page now includes tags from the network containing an IP address. This means that you should audit your permission rules to check there is no unintended allows of changing IPs based on network's tagset. Example:
	allow {client network} and {New York}
This rule now not only allows any operation on NY client networks, but also any operation with IP addresses included in those networks. To fix this, you should change the rule this way:
	allow {client network} and {New York} and not {\$page_ipaddress}

ENDOFTEXT
,

	'0.20.4' => <<<ENDOFTEXT
Please note that some dictionary items of Cisco Catalyst 2960 series switches
were renamed to meet official Cisco classification:
	2960-48TT   => 2960-48TT-L
	2960-24TC   => 2960-24TC-L
	2960-24TT   => 2960-24TT-L
	2960-8TC    => 2960-8TC-L
	2960G-48TC  => 2960G-48TC-L
	2960G-24TC  => 2960G-24TC-L
	2960G-8TC   => 2960G-8TC-L
	C2960-24    => C2960-24-S
	C2960G-24PC => C2960-24PC-L

The DATETIME_FORMAT configuration option used in setting date and time output
format now uses a different [1] syntax. During upgrade the option is reset to
the default value, which is now %Y-%m-%d (YYYY-MM-DD) per ISO 8601.

This release intoduces two new configuration options:
REVERSED_RACKS_LISTSRC and NEAREST_RACKS_CHECKBOX.

[1] http://php.net/manual/en/function.strftime.php
ENDOFTEXT
,

	'0.20.5' => <<<ENDOFTEXT
This release introduces the VS groups feature. VS groups is a new way to store
and display virtual services configuration. There is a new "ipvs" (VS group)
realm. All previously existing VS configuration remains functional and user
is free to convert it to the new format, which displays it in a more natural way
and allows to generate virtual_server_group keepalived configs. To convert a
virtual service to the new format, it is necessary to manually create a VS group
object and assign IP addresses to it. The VS group will display a "Migrate" tab
to convert the old-style VS objects, which can be removed after a successful
conversion.

The old-style VS configuration becomes DEPRECATED. Its support will be removed
in a future major release. So it is strongly recommended to convert it to the
new format.
ENDOFTEXT
,

	'0.20.6' => <<<ENDOFTEXT
New MGMT_PROTOS configuration option replaces the TELNET_OBJS_LISTSRC,
SSH_OBJS_LISTSRC and RDP_OBJS_LISTSRC options (converting existing settings as
necessary). MGMT_PROTOS allows to specify any management protocol for a
particular device list using a RackCode filter. The default value
("ssh: {\$typeid_4}, telnet: {\$typeid_8}") produces "ssh://server.fqdn" for
servers and "telnet://switch.fqdn" for network switches.
ENDOFTEXT
,

	'0.20.7' => <<<ENDOFTEXT
From now on the minimum (oldest) release of PHP that can run RackTables is
5.2.10. In particular, to continue running RackTables on CentOS 5 it is
necessary to replace its php* RPM packages with respective php53* packages
before the upgrade (except the JSON package, which PHP 5.3 provides internally).

Database triggers are used for some data consistency measures.  The database
user account must have the 'TRIGGER' privilege, which was introduced in
MySQL 5.1.7.

The IPV4OBJ_LISTSRC configuration option is reset to an expression which enables
the IP addressing feature for all object types except those listed.

Tags could now be assigned on the Edit/Properties tab using a text input with
auto-completion. Type a star '*' to view full tag tree in auto-complete menu.
It is worth to add the following line to the permissions script if the
old-fashioned 'Tags' tab is not needed any more:
  deny {\$tab_tags} # this hides 'Tags' tab

This release converts collation of all DB fields to the utf8_unicode_ci. This
procedure may take some time, and could fail if there are rows that differ only
by letter case. If this happen, you'll see the failed SQL query in upgrade report
with the "Duplicate entry" error message. Feel free to continue using your
installation. If desired so, you could eliminate the case-duplicating rows
and re-apply the failed query.
ENDOFTEXT
,
	'0.20.11' => <<<ENDOFTEXT
New IPV4_TREE_SHOW_UNALLOCATED configuration option introduced to disable
dsplaying unallocated networks in IPv4 space tree. Setting it also disables
the "knight" feature.
ENDOFTEXT
,
);

// At the moment we assume, that for any two releases we can
// sequentally execute all batches, that separate them, and
// nothing will break. If this changes one day, the function
// below will have to generate smarter upgrade paths, while
// the upper layer will remain the same.
// Returning an empty array means that no upgrade is necessary.
// Returning NULL indicates an error.
function getDBUpgradePath ($v1, $v2)
{
	$versionhistory = array
	(
		'0.20.0',
		'0.20.1',
		'0.20.2',
		'0.20.3',
		'0.20.4',
		'0.20.5',
		'0.20.6',
		'0.20.7',
		'0.20.8',
		'0.20.9',
		'0.20.10',
		'0.20.11',
		'0.20.12',
		'0.20.13',
		'0.20.14',
	);
	if (! in_array ($v1, $versionhistory) || ! in_array ($v2, $versionhistory))
		return NULL;
	$skip = TRUE;
	$path = NULL;
	// foreach() below cannot handle this specific case
	if ($v1 == $v2)
		return array();
	// Now collect all versions > $v1 and <= $v2
	foreach ($versionhistory as $v)
	{
		if ($skip && $v == $v1)
		{
			$skip = FALSE;
			$path = array();
			continue;
		}
		if ($skip)
			continue;
		$path[] = $v;
		if ($v == $v2)
			break;
	}
	return $path;
}

// Upgrade batches are named exactly as the release where they first appear.
// That is simple, but seems sufficient for beginning.
function getUpgradeBatch ($batchid)
{
	$query = array();
	global $dbver, $dbxlink;
	switch ($batchid)
	{
		case '0.20.0':
			$query[] = "
CREATE TABLE `PortLog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `port_id` int(10) unsigned NOT NULL,
  `date` datetime NOT NULL,
  `user` varchar(64) NOT NULL,
  `message` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `port_id-date` (`port_id`,`date`),
  CONSTRAINT `PortLog_ibfk_1` FOREIGN KEY (`port_id`) REFERENCES `Port` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;
";
			$query[] = "
CREATE TABLE `IPv4Log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `ip` int(10) unsigned NOT NULL,
  `date` datetime NOT NULL,
  `user` varchar(64) NOT NULL,
  `message` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ip-date` (`ip`,`date`)
) ENGINE=InnoDB;
";
			$query[] = "
CREATE TABLE `IPv6Log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `ip` binary(16) NOT NULL,
  `date` datetime NOT NULL,
  `user` varchar(64) NOT NULL,
  `message` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ip-date` (`ip`,`date`)
) ENGINE=InnoDB;
";
			$query[] = "ALTER TABLE `FileLink` MODIFY COLUMN `entity_type` ENUM('ipv4net','ipv4rspool','ipv4vs','ipv6net','location','object','rack','user') NOT NULL DEFAULT 'object'";
			$query[] = "ALTER TABLE `TagStorage` MODIFY COLUMN `entity_realm` ENUM('file','ipv4net','ipv4rspool','ipv4vs','ipv6net','location','object','rack','user','vst') NOT NULL default 'object'";
			$query[] = "ALTER TABLE `TagStorage` ADD COLUMN `user` char(64) DEFAULT NULL, ADD COLUMN `date` datetime DEFAULT NULL";

			// Rename object tables and keys, 'name' no longer needs to be unique
			$dbxlink->query ('ALTER TABLE `RackObject` RENAME TO `Object`');
			$dbxlink->query ('ALTER TABLE `RackObjectHistory` RENAME TO `ObjectHistory`');
			$dbxlink->query ('ALTER TABLE `Object` DROP KEY `name`');
			$query[] = 'ALTER TABLE `Object` DROP KEY `RackObject_asset_no`';
			$query[] = 'ALTER TABLE `Object` ADD UNIQUE KEY `asset_no` (`asset_no`)';
			$query[] = 'ALTER TABLE `Object` ADD KEY `type_id` (`objtype_id`,`id`)';
			$query[] = 'ALTER TABLE `ObjectHistory` DROP FOREIGN KEY `RackObjectHistory-FK-object_id`';
			$query[] = 'ALTER TABLE `ObjectHistory` ADD CONSTRAINT `ObjectHistory-FK-object_id` FOREIGN KEY (`id`) REFERENCES `Object` (`id`) ON DELETE CASCADE';
			$query[] = 'ALTER TABLE `RackSpace` DROP FOREIGN KEY `RackSpace-FK-rack_id`';

			// Rack height is now an attribute
			$query[] = "INSERT INTO `Attribute` (`id`,`type`,`name`) VALUES (27,'uint','Height, units')";
			$query[] = 'INSERT INTO `AttributeMap` (`objtype_id`,`attr_id`,`chapter_id`) VALUES (1560,27,NULL)';

			// Racks are now sorted using an attribute
			$query[] = "INSERT INTO `Attribute` (`id`,`type`,`name`) VALUES (29,'uint','Sort order')";
			$query[] = 'INSERT INTO `AttributeMap` (`objtype_id`,`attr_id`,`chapter_id`) VALUES (1560,29,NULL)';

			// Relate 'contact person' with locations
			$query[] = 'INSERT INTO `AttributeMap` (`objtype_id`,`attr_id`,`chapter_id`) VALUES (1562,14,NULL)';

			// Allow relationships between racks/rows/locations
			$query[] = "ALTER TABLE `EntityLink` MODIFY COLUMN `parent_entity_type` ENUM('ipv4net','ipv4rspool','ipv4vs','ipv6net','location','object','rack','row','user') NOT NULL";
			$query[] = "ALTER TABLE `EntityLink` MODIFY COLUMN `child_entity_type` ENUM('file','location','object','rack','row') NOT NULL";

			// Turn rows into objects
			$result = $dbxlink->query ('SELECT id, name FROM RackRow');
			$rows = $result->fetchAll (PDO::FETCH_ASSOC);
			unset ($result);
			foreach ($rows as $row)
			{
				$prepared = $dbxlink->prepare ('INSERT INTO `Object` (`name`,`objtype_id`) VALUES (?,?)');
				$prepared->execute (array($row['name'], 1561));
				$row_id = $dbxlink->lastInsertID();
				// Turn all racks in this row into objects
				$result = $dbxlink->query ("SELECT id, name, height, comment FROM Rack WHERE row_id=${row['id']} ORDER BY name");
				$racks = $result->fetchAll (PDO::FETCH_ASSOC);
				unset ($result);
				$sort_order = 1;
				foreach ($racks as $rack)
				{
					// Add the rack as an object, set the height and sort order as attributes, link the rack to the row,
					//   update rackspace, tags and files to reflect new rack_id, move history
					$prepared = $dbxlink->prepare ('INSERT INTO `Object` (`name`,`objtype_id`,`comment`) VALUES (?,?,?)');
					$prepared->execute (array($rack['name'], 1560, $rack['comment']));
					$rack_id = $dbxlink->lastInsertID();
					$query[] = "INSERT INTO `AttributeValue` (`object_id`,`object_tid`,`attr_id`,`uint_value`) VALUES (${rack_id},1560,27,${rack['height']})";
					$query[] = "INSERT INTO `AttributeValue` (`object_id`,`object_tid`,`attr_id`,`uint_value`) VALUES (${rack_id},1560,29,${sort_order})";
					$query[] = "INSERT INTO `EntityLink` (`parent_entity_type`,`parent_entity_id`,`child_entity_type`,`child_entity_id`) VALUES ('row',${row_id},'rack',${rack_id})";
					$query[] = "UPDATE `RackSpace` SET `rack_id`=${rack_id} WHERE `rack_id`=${rack['id']}";
					$query[] = "UPDATE `Atom` SET `rack_id`=${rack_id} WHERE `rack_id`=${rack['id']}";
					$query[] = "UPDATE `TagStorage` SET `entity_id`=${rack_id} WHERE `entity_realm`='rack' AND `entity_id`=${rack['id']}";
					$query[] = "UPDATE `FileLink` SET `entity_id`=${rack_id} WHERE `entity_type`='rack' AND `entity_id`=${rack['id']}";
					$query[] = "INSERT INTO `ObjectHistory` (`id`,`name`,`objtype_id`,`comment`,`ctime`,`user_name`) SELECT ${rack_id},`name`,1560,`comment`,`ctime`,`user_name` FROM `RackHistory` WHERE `id`=${rack['id']}";
					$sort_order++;
				}
			}
			$query[] = 'ALTER TABLE `RackSpace` ADD CONSTRAINT `RackSpace-FK-rack_id` FOREIGN KEY (`rack_id`) REFERENCES `Object` (`id`)';
			$query[] = 'DROP TABLE `Rack`';
			$query[] = 'DROP TABLE `RackRow`';
			$query[] = 'DROP TABLE `RackHistory`';
			$query[] = "
CREATE TABLE `RackThumbnail` (
  `rack_id` int(10) unsigned NOT NULL,
  `thumb_data` blob,
  UNIQUE KEY `rack_id` (`rack_id`),
  CONSTRAINT `RackThumbnail-FK-rack_id` FOREIGN KEY (`rack_id`) REFERENCES `Object` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB
";
			$query[] = "
CREATE VIEW `Location` AS SELECT O.id, O.name, O.has_problems, O.comment, P.id AS parent_id, P.name AS parent_name
FROM `Object` O
LEFT JOIN (
  `Object` P INNER JOIN `EntityLink` EL
  ON EL.parent_entity_id = P.id AND P.objtype_id = 1562 AND EL.parent_entity_type = 'location' AND EL.child_entity_type = 'location'
) ON EL.child_entity_id = O.id
WHERE O.objtype_id = 1562
";
			$query[] = "
CREATE VIEW `Row` AS SELECT O.id, O.name, L.id AS location_id, L.name AS location_name
  FROM `Object` O
  LEFT JOIN `EntityLink` EL ON O.id = EL.child_entity_id AND EL.parent_entity_type = 'location' AND EL.child_entity_type = 'row'
  LEFT JOIN `Object` L ON EL.parent_entity_id = L.id AND L.objtype_id = 1562
  WHERE O.objtype_id = 1561
";
			$query[] = "
CREATE VIEW `Rack` AS SELECT O.id, O.name AS name, O.asset_no, O.has_problems, O.comment,
  AV_H.uint_value AS height,
  AV_S.uint_value AS sort_order,
  RT.thumb_data,
  R.id AS row_id,
  R.name AS row_name
  FROM `Object` O
  LEFT JOIN `AttributeValue` AV_H ON O.id = AV_H.object_id AND AV_H.attr_id = 27
  LEFT JOIN `AttributeValue` AV_S ON O.id = AV_S.object_id AND AV_S.attr_id = 29
  LEFT JOIN `RackThumbnail` RT ON O.id = RT.rack_id
  LEFT JOIN `EntityLink` EL ON O.id = EL.child_entity_id  AND EL.parent_entity_type = 'row' AND EL.child_entity_type = 'rack'
  INNER JOIN `Object` R ON R.id = EL.parent_entity_id
  WHERE O.objtype_id = 1560
";
			$query[] = "
CREATE VIEW `RackObject` AS SELECT id, name, label, objtype_id, asset_no, has_problems, comment FROM `Object`
 WHERE `objtype_id` NOT IN (1560, 1561, 1562)
";
			$query[] = "UPDATE `Chapter` SET `name` = 'ObjectType' WHERE `id` = 1";
			$query[] = "DELETE FROM RackSpace WHERE object_id IS NULL AND state = 'T'";

			$query[] = "INSERT INTO `Config` (varname, varvalue, vartype, emptyok, is_hidden, is_userdefined, description) VALUES ('SYNC_802Q_LISTSRC','','string','yes','no','no','List of VLAN switches sync is enabled on')";
			$query[] = "UPDATE `Config` SET is_userdefined='yes' WHERE varname='PROXIMITY_RANGE'";
			$query[] = "INSERT INTO `Config` (varname, varvalue, vartype, emptyok, is_hidden, is_userdefined, description) VALUES ('QUICK_LINK_PAGES','depot,ipv4space,rackspace','string','yes','no','yes','List of pages to dislay in quick links')";
			$query[] = "ALTER TABLE `IPv4LB` MODIFY `prio` varchar(255) DEFAULT NULL";

			$query[] = "ALTER TABLE `IPv4Address` ADD COLUMN `comment` char(255) NOT NULL default '' AFTER `name`";
			$query[] = "ALTER TABLE `IPv6Address` ADD COLUMN `comment` char(255) NOT NULL default '' AFTER `name`";

			// change IP address format of IPv4VS and IPv4RS tables
			convertSLBTablesToBinIPs();

			// do not allow NULL allocation type
			$query[] = "ALTER TABLE `IPv4Allocation` MODIFY `type` enum('regular','shared','virtual','router') NOT NULL DEFAULT 'regular'";
			$query[] = "ALTER TABLE `IPv6Allocation` MODIFY `type` enum('regular','shared','virtual','router') NOT NULL DEFAULT 'regular'";

			$query[] = "INSERT INTO `Config` (varname, varvalue, vartype, emptyok, is_hidden, is_userdefined, description) VALUES ('SEARCH_DOMAINS','','string','yes','no','yes','DNS domain list (comma-separated) to search in FQDN attributes')";

			// update some config variables which changed their defaults in this verison
			replaceConfigVarValue ('SHOW_LAST_TAB', 'yes');
			replaceConfigVarValue ('IPV4_TREE_SHOW_USAGE','no');
			replaceConfigVarValue ('IPV4LB_LISTSRC', 'false', '{$typeid_4}');
			replaceConfigVarValue ('FILTER_DEFAULT_ANDOR', 'and');
			replaceConfigVarValue ('FILTER_SUGGEST_EXTRA', 'yes');
			replaceConfigVarValue ('IPV4_TREE_RTR_AS_CELL', 'no');
			replaceConfigVarValue ('SSH_OBJS_LISTSRC', 'false', 'none');
			replaceConfigVarValue ('TELNET_OBJS_LISTSRC', 'false', 'none');

			$query[] = "UPDATE Config SET varvalue = '0.20.0' WHERE varname = 'DB_VERSION'";
			break;
		case '0.20.1':
			// some HW types were moved from the 'Network switch' chapter to the 'Network chassis' chapter
			// change the type of affected objects to 'Network chassis'
			$query[] = "UPDATE `Object` SET objtype_id = 1503 WHERE id IN (SELECT object_id FROM `AttributeValue` WHERE attr_id = 2 and uint_value IN (888,889,890,891))";

			// new 'management interface' object type
			$query[] = "INSERT INTO `Chapter` (`id`,`sticky`,`name`) VALUES (38,'no','management interface type')";
			$query[] = "INSERT INTO `Attribute` (`id`,`type`,`name`) VALUES (30,'dict','Mgmt type')";
			$query[] = "INSERT INTO `AttributeMap` (`objtype_id`,`attr_id`,`chapter_id`) VALUES (1787,3,NULL),(1787,14,NULL),(1787,30,38)";
			$query[] = "UPDATE `Config` SET varvalue = CONCAT(varvalue, ' or {\$typeid_1787}') WHERE varname = 'IPV4OBJ_LISTSRC'";

			$query[] = "INSERT INTO Config VALUES ('8021Q_EXTSYNC_LISTSRC','false','string','yes','no','no','List source: objects with extended 802.1Q sync')";

			// constraints to prevent orphan records
			$query[] = "DELETE A FROM `Atom` A LEFT JOIN `Molecule` M ON A.molecule_id = M.id LEFT JOIN `Object` O ON A.rack_id = O.id WHERE M.id IS NULL OR O.id IS NULL";
			$query[] = "DELETE FROM `MountOperation` WHERE old_molecule_id NOT IN (SELECT id FROM `Molecule`) OR new_molecule_id NOT IN (SELECT id FROM `Molecule`)";
			$query[] = "ALTER TABLE `Atom` ADD CONSTRAINT `Atom-FK-molecule_id` FOREIGN KEY (`molecule_id`) REFERENCES `Molecule` (`id`) ON DELETE CASCADE";
			$query[] = "ALTER TABLE `Atom` ADD CONSTRAINT `Atom-FK-rack_id` FOREIGN KEY (`rack_id`) REFERENCES `Object` (`id`) ON DELETE CASCADE";
			$query[] = "ALTER TABLE `MountOperation` ADD CONSTRAINT `MountOperation-FK-old_molecule_id` FOREIGN KEY (`old_molecule_id`) REFERENCES `Molecule` (`id`) ON DELETE CASCADE";
			$query[] = "ALTER TABLE `MountOperation` ADD CONSTRAINT `MountOperation-FK-new_molecule_id` FOREIGN KEY (`new_molecule_id`) REFERENCES `Molecule` (`id`) ON DELETE CASCADE";
			# multiple Cacti servers
			$query[] = "
CREATE TABLE `CactiServer` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `base_url` char(255) DEFAULT NULL,
  `username` char(64) DEFAULT NULL,
  `password` char(64) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB
";
			$query[] = "ALTER TABLE CactiGraph ADD COLUMN server_id int(10) unsigned NOT NULL AFTER object_id";

			$result = $dbxlink->query ('SELECT COUNT(*) AS cnt FROM CactiGraph');
			$row = $result->fetch (PDO::FETCH_ASSOC);
			unset ($result);

			$result = $dbxlink->query ("SELECT varvalue FROM Config WHERE varname = 'CACTI_URL'");
			$cacti_url_row = $result->fetch (PDO::FETCH_ASSOC);
			unset ($result);

			if ($row['cnt'] != 0 || is_array ($cacti_url_row) && $cacti_url_row['varvalue'] != '')
			{
				$query[] = "INSERT INTO CactiServer (id) VALUES (1)";
				$query[] = "UPDATE CactiServer SET base_url = (SELECT varvalue FROM Config WHERE varname = 'CACTI_URL') WHERE id = 1";
				$query[] = "UPDATE CactiServer SET username = (SELECT varvalue FROM Config WHERE varname = 'CACTI_USERNAME') WHERE id = 1";
				$query[] = "UPDATE CactiServer SET password = (SELECT varvalue FROM Config WHERE varname = 'CACTI_USERPASS') WHERE id = 1";
				$query[] = "UPDATE CactiGraph SET server_id = 1";
			}
			$query[] = "ALTER TABLE CactiGraph DROP PRIMARY KEY";
			$query[] = "ALTER TABLE CactiGraph ADD PRIMARY KEY (server_id, graph_id)";
			$query[] = "ALTER TABLE CactiGraph ADD KEY (graph_id)";
			$query[] = "ALTER TABLE CactiGraph ADD CONSTRAINT `CactiGraph-FK-server_id` FOREIGN KEY (server_id) REFERENCES CactiServer (id)";
			$query[] = "DELETE FROM Config WHERE varname IN('CACTI_URL', 'CACTI_USERNAME', 'CACTI_USERPASS')";
			$query[] = "UPDATE Config SET varvalue = '0.20.1' WHERE varname = 'DB_VERSION'";
			break;
		case '0.20.2':
			$query[] = "ALTER TABLE TagStorage ADD COLUMN tag_is_assignable ENUM('yes', 'no') NOT NULL default 'yes' AFTER tag_id";
			$query[] = "ALTER TABLE TagStorage ADD KEY `tag_id-tag_is_assignable` (tag_id, tag_is_assignable)";
			$query[] = "ALTER TABLE TagTree ADD COLUMN is_assignable ENUM('yes', 'no') NOT NULL default 'yes' AFTER parent_id";
			$query[] = "ALTER TABLE TagTree ADD KEY `id-is_assignable` (id, is_assignable)";
			$query[] = "ALTER TABLE TagStorage DROP FOREIGN KEY `TagStorage-FK-tag_id`";
			$query[] = "ALTER TABLE TagStorage ADD CONSTRAINT `TagStorage-FK-TagTree` FOREIGN KEY (tag_id, tag_is_assignable) REFERENCES TagTree (id, is_assignable)";
			$query[] = "UPDATE UserAccount SET user_realname = NULL WHERE user_realname = ''";
			$query[] = "UPDATE Object SET comment = NULL WHERE comment = ''";
			$query[] = "
CREATE TABLE `MuninServer` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `base_url` char(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB
";
			$query[] = "
CREATE TABLE `MuninGraph` (
  `object_id` int(10) unsigned NOT NULL,
  `server_id` int(10) unsigned NOT NULL,
  `graph` char(255) NOT NULL,
  `caption`  char(255) DEFAULT NULL,
  PRIMARY KEY (`object_id`,`server_id`,`graph`),
  KEY `server_id` (`server_id`),
  KEY `graph` (`graph`),
  CONSTRAINT `MuninGraph-FK-server_id` FOREIGN KEY (`server_id`) REFERENCES `MuninServer` (`id`),
  CONSTRAINT `MuninGraph-FK-object_id` FOREIGN KEY (`object_id`) REFERENCES `Object` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB
";
			$query[] = "INSERT INTO `Config` (varname, varvalue, vartype, emptyok, is_hidden, is_userdefined, description) VALUES ('MUNIN_LISTSRC','false','string','yes','no','no','List of object with Munin graphs')";
			$query[] = "INSERT INTO `Config` (varname, varvalue, vartype, emptyok, is_hidden, is_userdefined, description) VALUES ('8021Q_MULTILINK_LISTSRC','false','string','yes','no','no','List source: IPv4/IPv6 networks allowing multiple VLANs from same domain')";
			$query[] = "ALTER TABLE VLANIPv4 ADD UNIQUE `network-domain-vlan` (ipv4net_id, domain_id, vlan_id)";
			$query[] = "ALTER TABLE VLANIPv4 DROP KEY `network-domain`";
			$query[] = "ALTER TABLE VLANIPv6 ADD UNIQUE `network-domain-vlan` (ipv6net_id, domain_id, vlan_id)";
			$query[] = "ALTER TABLE VLANIPv6 DROP KEY `network-domain`";
			$query[] = "UPDATE Config SET varvalue = '0.20.2' WHERE varname = 'DB_VERSION'";
			break;
		case '0.20.3':
			$query[] = "UPDATE Config SET varvalue = '0.20.3' WHERE varname = 'DB_VERSION'";
			break;
		case '0.20.4':
			$query[] = "ALTER TABLE `FileLink` MODIFY COLUMN `entity_type` ENUM('ipv4net','ipv4rspool','ipv4vs','ipv6net','location','object','rack','row','user') NOT NULL DEFAULT 'object'";
			$query[] = "ALTER TABLE `RackSpace` MODIFY COLUMN `state` ENUM('A','U','T') NOT NULL default 'A'";
			$query[] = "INSERT INTO `Config` (varname, varvalue, vartype, emptyok, is_hidden, is_userdefined, description) VALUES ('REVERSED_RACKS_LISTSRC', 'false', 'string', 'yes', 'no', 'no', 'List of racks with reversed (top to bottom) units order')";
			$query[] = "UPDATE `Config` SET varvalue = CONCAT(varvalue, ' or {\$typeid_965}') WHERE varname = 'IPV4OBJ_LISTSRC'";
			$query[] = "UPDATE AttributeValue INNER JOIN AttributeMap USING (attr_id) SET AttributeValue.uint_value = 1572 WHERE chapter_id = 12 AND uint_value = 162";
			$query[] = "UPDATE AttributeValue INNER JOIN AttributeMap USING (attr_id) SET AttributeValue.uint_value = 1710 WHERE chapter_id = 12 AND uint_value = 163";
			$query[] = "UPDATE Config SET varvalue = '%Y-%m-%d', description='PHP strftime() format to use for date output' WHERE varname = 'DATETIME_FORMAT'";
			$query[] = "INSERT INTO `Config` (varname, varvalue, vartype, emptyok, is_hidden, is_userdefined, description) VALUES ('NEAREST_RACKS_CHECKBOX', 'yes', 'string', 'yes', 'no', 'yes', 'Enable nearest racks in port list filter by default')";
			$query[] = "UPDATE Config SET varvalue = '0.20.4' WHERE varname = 'DB_VERSION'";
			break;
		case '0.20.5':
			$query[] = "
CREATE OR REPLACE VIEW `Rack` AS SELECT O.id, O.name AS name, O.asset_no, O.has_problems, O.comment,
  AV_H.uint_value AS height,
  AV_S.uint_value AS sort_order,
  RT.thumb_data,
  R.id AS row_id,
  R.name AS row_name,
  L.id AS location_id,
  L.name AS location_name
  FROM `Object` O
  LEFT JOIN `AttributeValue` AV_H ON O.id = AV_H.object_id AND AV_H.attr_id = 27
  LEFT JOIN `AttributeValue` AV_S ON O.id = AV_S.object_id AND AV_S.attr_id = 29
  LEFT JOIN `RackThumbnail` RT ON O.id = RT.rack_id
  LEFT JOIN `EntityLink` RL ON O.id = RL.child_entity_id  AND RL.parent_entity_type = 'row' AND RL.child_entity_type = 'rack'
  INNER JOIN `Object` R ON R.id = RL.parent_entity_id
  LEFT JOIN `EntityLink` LL ON R.id = LL.child_entity_id AND LL.parent_entity_type = 'location' AND LL.child_entity_type = 'row'
  LEFT JOIN `Object` L ON L.id = LL.parent_entity_id
  WHERE O.objtype_id = 1560
";

			// prevent some AttributeMap entries from being deleted
			$query[] = "ALTER TABLE AttributeMap ADD COLUMN sticky enum('yes','no') default 'no'";
			$query[] = "UPDATE AttributeMap SET sticky = 'yes' WHERE objtype_id = 4 AND attr_id IN (26,28)"; // Server -> Hypervisor, Slot number
			$query[] = "UPDATE AttributeMap SET sticky = 'yes' WHERE objtype_id = 8 AND attr_id IN (1,2,4,28)"; // Network switch -> OEM S/N 1, HW type, SW type, Slot number
			$query[] = "UPDATE AttributeMap SET sticky = 'yes' WHERE objtype_id = 798 AND attr_id = 28"; // Network security -> Slot number
			$query[] = "UPDATE AttributeMap SET sticky = 'yes' WHERE objtype_id = 1055 AND attr_id = 28"; // FC switch -> Slot number
			$query[] = "UPDATE AttributeMap SET sticky = 'yes' WHERE objtype_id = 1560 AND attr_id IN (27,29)"; // Rack -> Height, Sort order
			$query[] = "UPDATE AttributeMap SET sticky = 'yes' WHERE objtype_id = 1787 AND attr_id = 30"; // Management interface -> Mgmt type

			$query[] = "INSERT INTO `Config` (varname, varvalue, vartype, emptyok, is_hidden, is_userdefined, description) VALUES ('RDP_OBJS_LISTSRC','false','string','yes','no','yes','Rackcode filter for RDP-managed objects')";

			// SLB v2 tables
			$query[] = "
CREATE TABLE `VS` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(255) DEFAULT NULL,
  `vsconfig` text,
  `rsconfig` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB
";
			$query[] = "
CREATE TABLE `VSIPs` (
  `vs_id` int(10) unsigned NOT NULL,
  `vip` varbinary(16) NOT NULL,
  `vsconfig` text,
  `rsconfig` text,
  PRIMARY KEY (`vs_id`,`vip`),
  KEY `vip` (`vip`),
  CONSTRAINT `VSIPs-vs_id` FOREIGN KEY (`vs_id`) REFERENCES `VS` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB
";
			$query[] = "
CREATE TABLE `VSPorts` (
  `vs_id` int(10) unsigned NOT NULL,
  `proto` enum('TCP','UDP','MARK') NOT NULL,
  `vport` int(10) unsigned NOT NULL,
  `vsconfig` text,
  `rsconfig` text,
  PRIMARY KEY (`vs_id`,`proto`,`vport`),
  KEY `proto-vport` (`proto`,`vport`),
  CONSTRAINT `VS-vs_id` FOREIGN KEY (`vs_id`) REFERENCES `VS` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB
";
			$query[] = "
CREATE TABLE `VSEnabledIPs` (
  `object_id` int(10) unsigned NOT NULL,
  `vs_id` int(10) unsigned NOT NULL,
  `vip` varbinary(16) NOT NULL,
  `rspool_id` int(10) unsigned NOT NULL,
  `prio` varchar(255) DEFAULT NULL,
  `vsconfig` text,
  `rsconfig` text,
  PRIMARY KEY (`object_id`,`vs_id`,`vip`,`rspool_id`),
  KEY `vip` (`vip`),
  KEY `VSEnabledIPs-FK-vs_id-vip` (`vs_id`,`vip`),
  KEY `VSEnabledIPs-FK-rspool_id` (`rspool_id`),
  CONSTRAINT `VSEnabledIPs-FK-rspool_id` FOREIGN KEY (`rspool_id`) REFERENCES `IPv4RSPool` (`id`) ON DELETE CASCADE,
  CONSTRAINT `VSEnabledIPs-FK-vs_id-vip` FOREIGN KEY (`vs_id`, `vip`) REFERENCES `VSIPs` (`vs_id`, `vip`) ON DELETE CASCADE
) ENGINE=InnoDB
";
			$query[] = "
CREATE TABLE `VSEnabledPorts` (
  `object_id` int(10) unsigned NOT NULL,
  `vs_id` int(10) unsigned NOT NULL,
  `proto` enum('TCP','UDP','MARK') NOT NULL,
  `vport` int(10) unsigned NOT NULL,
  `rspool_id` int(10) unsigned NOT NULL,
  `vsconfig` text,
  `rsconfig` text,
  PRIMARY KEY (`object_id`,`vs_id`,`proto`,`vport`,`rspool_id`),
  KEY `VSEnabledPorts-FK-vs_id-proto-vport` (`vs_id`,`proto`,`vport`),
  KEY `VSEnabledPorts-FK-rspool_id` (`rspool_id`),
  CONSTRAINT `VSEnabledPorts-FK-object_id` FOREIGN KEY (`object_id`) REFERENCES `Object` (`id`) ON DELETE CASCADE,
  CONSTRAINT `VSEnabledPorts-FK-rspool_id` FOREIGN KEY (`rspool_id`) REFERENCES `IPv4RSPool` (`id`) ON DELETE CASCADE,
  CONSTRAINT `VSEnabledPorts-FK-vs_id-proto-vport` FOREIGN KEY (`vs_id`, `proto`, `vport`) REFERENCES `VSPorts` (`vs_id`, `proto`, `vport`) ON DELETE CASCADE
) ENGINE=InnoDB
";
			$query[] = "ALTER TABLE `EntityLink` MODIFY COLUMN `parent_entity_type` ENUM('ipv4net','ipv4rspool','ipv4vs','ipv6net','location','object','rack','row','user') NOT NULL";
			$query[] = "ALTER TABLE `FileLink` MODIFY COLUMN `entity_type` ENUM('ipv4net','ipv4rspool','ipv4vs','ipv6net','location','object','rack','row','user') NOT NULL DEFAULT 'object'";
			$query[] = "ALTER TABLE `TagStorage` MODIFY COLUMN `entity_realm` ENUM('file','ipv4net','ipv4rspool','ipv4vs','ipvs','ipv6net','location','object','rack','user','vst') NOT NULL DEFAULT 'object'";
			$query[] = "ALTER TABLE `UserConfig` DROP FOREIGN KEY `UserConfig-FK-user`";
			$query[] = "UPDATE Config SET varvalue = '0.20.5' WHERE varname = 'DB_VERSION'";
			break;
		case '0.20.6':
			// one HW type was moved from the 'Network switch' chapter to the 'Network chassis' chapter
			// change the type of affected objects to 'Network chassis'
			$query[] = "UPDATE `Object` SET objtype_id = 1503 WHERE id IN (SELECT object_id FROM `AttributeValue` WHERE attr_id = 2 and uint_value = 935)";

			// convert values of old 'TELNET_OBJS_LISTSRC' 'SSH_OBJS_LISTSRC', 'RDP_OBJS_LISTSRC' variables into 'MGMT_PROTOS'
			$query[] = "INSERT INTO `Config` (varname, varvalue, vartype, emptyok, is_hidden, is_userdefined, description) VALUES ('MGMT_PROTOS','ssh: {\$typeid_4}; telnet: {\$typeid_8}','string','yes','no','yes','Mapping of management protocol to devices')";
			if ('' !== $mgmt_converted_var = convertMgmtConfigVars())
				$query[] = "UPDATE `Config` SET varvalue = '" . mysql_escape_string ($mgmt_converted_var) . "' WHERE varname = 'MGMT_PROTOS'"; // TODO: call of deprecated function
			$query[] = "DELETE `Config`,`UserConfig` FROM `Config` LEFT JOIN `UserConfig` USING (`varname`) WHERE `Config`.`varname` IN ('TELNET_OBJS_LISTSRC', 'SSH_OBJS_LISTSRC', 'RDP_OBJS_LISTSRC')";

			$query[] = "ALTER TABLE `VSEnabledIPs` ADD CONSTRAINT `VSEnabledIPs-FK-object_id` FOREIGN KEY (`object_id`) REFERENCES `Object` (`id`) ON DELETE CASCADE";

			$query[] = "DELETE FROM Config WHERE varname = 'PORTS_PER_ROW'";
			$query[] = "UPDATE Config SET varvalue = '0.20.6' WHERE varname = 'DB_VERSION'";
			break;
		case '0.20.7':
			if (! isInnoDBSupported ())
			{
				showUpgradeError ('Cannot upgrade because triggers are not supported by your MySQL server.', __FUNCTION__);
				die;
			}

			// for the UNIQUE key to work, portb needs to be > porta
			$result = $dbxlink->query ('SELECT porta, portb FROM `Link` WHERE porta > portb');
			$links = $result->fetchAll (PDO::FETCH_ASSOC);
			unset ($result);
			foreach ($links as $link)
				$query[] = "UPDATE `Link` SET `porta`=${link['portb']}, `portb`=${link['porta']} WHERE `porta`=${link['porta']} AND `portb`=${link['portb']}";

			// add triggers
			$query[] = "
CREATE TRIGGER `EntityLink-before-insert` BEFORE INSERT ON `EntityLink` FOR EACH ROW
EntityLinkTrigger:BEGIN
  DECLARE parent_objtype, child_objtype, count INTEGER;

  # forbid linking an entity to itself
  IF NEW.parent_entity_type = NEW.child_entity_type AND NEW.parent_entity_id = NEW.child_entity_id THEN
    SET NEW.parent_entity_id = NULL;
    LEAVE EntityLinkTrigger;
  END IF;

  # in some scenarios, only one parent is allowed
  CASE CONCAT(NEW.parent_entity_type, '.', NEW.child_entity_type)
    WHEN 'location.location' THEN
      SELECT COUNT(*) INTO count FROM EntityLink WHERE parent_entity_type = 'location' AND child_entity_type = 'location' AND child_entity_id = NEW.child_entity_id;
    WHEN 'location.row' THEN
      SELECT COUNT(*) INTO count FROM EntityLink WHERE parent_entity_type = 'location' AND child_entity_type = 'row' AND child_entity_id = NEW.child_entity_id;
    WHEN 'row.rack' THEN
      SELECT COUNT(*) INTO count FROM EntityLink WHERE parent_entity_type = 'row' AND child_entity_type = 'rack' AND child_entity_id = NEW.child_entity_id;
    ELSE
      # some other scenario, assume it is valid
      SET count = 0;
  END CASE; 
  IF count > 0 THEN
    SET NEW.parent_entity_id = NULL;
    LEAVE EntityLinkTrigger;
  END IF;

  IF NEW.parent_entity_type = 'object' AND NEW.child_entity_type = 'object' THEN
    # lock objects to prevent concurrent link establishment
    SELECT objtype_id INTO parent_objtype FROM Object WHERE id = NEW.parent_entity_id FOR UPDATE;
    SELECT objtype_id INTO child_objtype FROM Object WHERE id = NEW.child_entity_id FOR UPDATE;

    # only permit the link if object types are compatibile
    SELECT COUNT(*) INTO count FROM ObjectParentCompat WHERE parent_objtype_id = parent_objtype AND child_objtype_id = child_objtype;
    IF count = 0 THEN
      SET NEW.parent_entity_id = NULL;
    END IF;
  END IF;
END;
";
			$query[] = "
CREATE TRIGGER `EntityLink-before-update` BEFORE UPDATE ON `EntityLink` FOR EACH ROW
EntityLinkTrigger:BEGIN
  DECLARE parent_objtype, child_objtype, count INTEGER;

  # forbid linking an entity to itself
  IF NEW.parent_entity_type = NEW.child_entity_type AND NEW.parent_entity_id = NEW.child_entity_id THEN
    SET NEW.parent_entity_id = NULL;
    LEAVE EntityLinkTrigger;
  END IF;

  # in some scenarios, only one parent is allowed
  CASE CONCAT(NEW.parent_entity_type, '.', NEW.child_entity_type)
    WHEN 'location.location' THEN
      SELECT COUNT(*) INTO count FROM EntityLink WHERE parent_entity_type = 'location' AND child_entity_type = 'location' AND child_entity_id = NEW.child_entity_id AND id != NEW.id;
    WHEN 'location.row' THEN
      SELECT COUNT(*) INTO count FROM EntityLink WHERE parent_entity_type = 'location' AND child_entity_type = 'row' AND child_entity_id = NEW.child_entity_id AND id != NEW.id;
    WHEN 'row.rack' THEN
      SELECT COUNT(*) INTO count FROM EntityLink WHERE parent_entity_type = 'row' AND child_entity_type = 'rack' AND child_entity_id = NEW.child_entity_id AND id != NEW.id;
    ELSE
      # some other scenario, assume it is valid
      SET count = 0;
  END CASE; 
  IF count > 0 THEN
    SET NEW.parent_entity_id = NULL;
    LEAVE EntityLinkTrigger;
  END IF;

  IF NEW.parent_entity_type = 'object' AND NEW.child_entity_type = 'object' THEN
    # lock objects to prevent concurrent link establishment
    SELECT objtype_id INTO parent_objtype FROM Object WHERE id = NEW.parent_entity_id FOR UPDATE;
    SELECT objtype_id INTO child_objtype FROM Object WHERE id = NEW.child_entity_id FOR UPDATE;

    # only permit the link if object types are compatibile
    SELECT COUNT(*) INTO count FROM ObjectParentCompat WHERE parent_objtype_id = parent_objtype AND child_objtype_id = child_objtype;
    IF count = 0 THEN
      SET NEW.parent_entity_id = NULL;
    END IF;
  END IF;
END;
";
			$link_trigger_body = <<<ENDOFTRIGGER
LinkTrigger:BEGIN
  DECLARE tmp, porta_type, portb_type, count INTEGER;

  IF NEW.porta = NEW.portb THEN
    # forbid connecting a port to itself
    SET NEW.porta = NULL;
    LEAVE LinkTrigger;
  ELSEIF NEW.porta > NEW.portb THEN
    # force porta < portb
    SET tmp = NEW.porta;
    SET NEW.porta = NEW.portb;
    SET NEW.portb = tmp;
  END IF; 

  # lock ports to prevent concurrent link establishment
  SELECT type INTO porta_type FROM Port WHERE id = NEW.porta FOR UPDATE;
  SELECT type INTO portb_type FROM Port WHERE id = NEW.portb FOR UPDATE;

  # only permit the link if ports are compatibile
  SELECT COUNT(*) INTO count FROM PortCompat WHERE (type1 = porta_type AND type2 = portb_type) OR (type1 = portb_type AND type2 = porta_type);
  IF count = 0 THEN
    SET NEW.porta = NULL;
  END IF;
END;
ENDOFTRIGGER;
			$query[] = "CREATE TRIGGER `Link-before-insert` BEFORE INSERT ON `Link` FOR EACH ROW $link_trigger_body";
			$query[] = "CREATE TRIGGER `Link-before-update` BEFORE UPDATE ON `Link` FOR EACH ROW $link_trigger_body";

			// enable IP addressing for all object types unless specifically excluded
			$query[] = "UPDATE `Config` SET varvalue = 'not ({\$typeid_3} or {\$typeid_9} or {\$typeid_10} or {\$typeid_11})' WHERE varname = 'IPV4OBJ_LISTSRC'";

			$query[] = "ALTER TABLE `EntityLink` MODIFY COLUMN `parent_entity_type` ENUM('location','object','rack','row') NOT NULL";
			$query[] = "ALTER TABLE `EntityLink` MODIFY COLUMN `child_entity_type` ENUM('location','object','rack','row') NOT NULL";

			$query[] = "UPDATE Config SET description = 'List source: objects for that asset tag should be set' WHERE varname = 'ASSETWARN_LISTSRC'";
			$query[] = "UPDATE Config SET description = 'List source: objects for that common name should be set' WHERE varname = 'NAMEWARN_LISTSRC'";
			$query[] = "ALTER TABLE `IPv4NAT` MODIFY COLUMN `proto` ENUM('TCP','UDP','ALL')";

			// add new 'point2point' alloc type
			$query[] = "ALTER TABLE `IPv4Allocation` MODIFY `type` enum('regular','shared','virtual','router','point2point') NOT NULL DEFAULT 'regular'";
			$query[] = "ALTER TABLE `IPv6Allocation` MODIFY `type` enum('regular','shared','virtual','router','point2point') NOT NULL DEFAULT 'regular'";

			// update to use utf8_unicode_ci collation
			// http://bugs.racktables.org/view.php?id=837
			$query[] = "ALTER DATABASE DEFAULT CHARACTER SET UTF8 COLLATE utf8_unicode_ci";

			$query[] = "SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0";
			$query[] = "ALTER TABLE `Atom` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci";
			$query[] = "ALTER TABLE `Attribute` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci";
			$query[] = "ALTER TABLE `AttributeMap` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci";
			$query[] = "ALTER TABLE `AttributeValue` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci";
			$query[] = "ALTER TABLE `CachedPAV` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci";
			$query[] = "ALTER TABLE `CachedPNV` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci";
			$query[] = "ALTER TABLE `CachedPVM` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci";
			$query[] = "ALTER TABLE `CactiGraph` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci";
			$query[] = "ALTER TABLE `CactiServer` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci";
			$query[] = "ALTER TABLE `Chapter` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci";
			$query[] = "ALTER TABLE `Config` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci";
			$query[] = "ALTER TABLE `Dictionary` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci";
			$query[] = "ALTER TABLE `EntityLink` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci";
			$query[] = "ALTER TABLE `File` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci";
			$query[] = "ALTER TABLE `FileLink` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci";
			$query[] = "ALTER TABLE `IPv4Address` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci";
			$query[] = "ALTER TABLE `IPv4Allocation` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci";
			$query[] = "ALTER TABLE `IPv4LB` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci";
			$query[] = "ALTER TABLE `IPv4Log` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci";
			$query[] = "ALTER TABLE `IPv6Log` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci";
			$query[] = "ALTER TABLE `IPv4NAT` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci";
			$query[] = "ALTER TABLE `IPv4Network` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci";
			$query[] = "ALTER TABLE `IPv4RS` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci";
			$query[] = "ALTER TABLE `IPv4RSPool` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci";
			$query[] = "ALTER TABLE `IPv4VS` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci";
			$query[] = "ALTER TABLE `IPv6Address` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci";
			$query[] = "ALTER TABLE `IPv6Allocation` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci";
			$query[] = "ALTER TABLE `IPv6Network` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci";
			$query[] = "ALTER TABLE `LDAPCache` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci";
			$query[] = "ALTER TABLE `Link` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci";
			$query[] = "ALTER TABLE `Molecule` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci";
			$query[] = "ALTER TABLE `MountOperation` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci";
			$query[] = "ALTER TABLE `MuninGraph` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci";
			$query[] = "ALTER TABLE `MuninServer` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci";
			$query[] = "ALTER TABLE `ObjectLog` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci";
			$query[] = "ALTER TABLE `ObjectParentCompat` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci";
			$query[] = "ALTER TABLE `Port` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci";
			$query[] = "ALTER TABLE `PortAllowedVLAN` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci";
			$query[] = "ALTER TABLE `PortCompat` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci";
			$query[] = "ALTER TABLE `PortInnerInterface` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci";
			$query[] = "ALTER TABLE `PortInterfaceCompat` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci";
			$query[] = "ALTER TABLE `PortLog` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci";
			$query[] = "ALTER TABLE `PortNativeVLAN` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci";
			$query[] = "ALTER TABLE `PortVLANMode` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci";
			$query[] = "ALTER TABLE `Object` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci";
			$query[] = "ALTER TABLE `ObjectHistory` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci";
			$query[] = "ALTER TABLE `RackSpace` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci";
			$query[] = "ALTER TABLE `RackThumbnail` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci";
			$query[] = "ALTER TABLE `Script` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci";
			$query[] = "ALTER TABLE `TagStorage` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci";
			$query[] = "ALTER TABLE `TagTree` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci";
			$query[] = "ALTER TABLE `UserAccount` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci";
			$query[] = "ALTER TABLE `UserConfig` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci";
			$query[] = "ALTER TABLE `VLANDescription` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci";
			$query[] = "ALTER TABLE `VLANDomain` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci";
			$query[] = "ALTER TABLE `VLANIPv4` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci";
			$query[] = "ALTER TABLE `VLANIPv6` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci";
			$query[] = "ALTER TABLE `VLANSTRule` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci";
			$query[] = "ALTER TABLE `VLANSwitch` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci";
			$query[] = "ALTER TABLE `VLANSwitchTemplate` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci";
			$query[] = "ALTER TABLE `VLANValidID` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci";
			$query[] = "ALTER TABLE `VS` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci";
			$query[] = "ALTER TABLE `VSIPs` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci";
			$query[] = "ALTER TABLE `VSPorts` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci";
			$query[] = "ALTER TABLE `VSEnabledIPs` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci";
			$query[] = "ALTER TABLE `VSEnabledPorts` CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci";
			$query[] = "SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS";

			$query[] = "UPDATE Config SET varvalue = '0.20.7' WHERE varname = 'DB_VERSION'";
			break;
		case '0.20.8':
			$query[] = "ALTER TABLE `VLANSTRule` CHANGE COLUMN `wrt_vlans` `wrt_vlans` text";

			$query[] = "
CREATE TABLE `PortOuterInterface` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `oif_name` char(48) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `oif_name` (`oif_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
";
			// if upgrading from certain older releases, inject some data to satisfy foreign keys
			if (version_compare ($dbver, '0.19.2', '<'))
				$query[] = "INSERT INTO Dictionary (chapter_id,dict_key,dict_value) VALUES (2,1399,'DC')";
			if (version_compare ($dbver, '0.19.5', '<'))
				$query[] = "INSERT INTO Dictionary (chapter_id,dict_key,dict_value) VALUES (2,1469,'virtual port')";
			if (version_compare ($dbver, '0.19.7', '<'))
				$query[] = "INSERT INTO Dictionary (chapter_id,dict_key,dict_value) VALUES (2,1588,'empty QSFP+')";
			if (version_compare ($dbver, '0.19.8', '<'))
				$query[] = "INSERT INTO Dictionary (chapter_id,dict_key,dict_value) VALUES
(2,1424,'1000Base-CX'),
(2,1425,'10GBase-ER-DWDM40-61.42 (ITU 20)'),
(2,1426,'10GBase-ER-DWDM40-60.61 (ITU 21)'),
(2,1427,'10GBase-ER-DWDM40-59.79 (ITU 22)'),
(2,1428,'10GBase-ER-DWDM40-58.98 (ITU 23)'),
(2,1429,'10GBase-ER-DWDM40-58.17 (ITU 24)'),
(2,1430,'10GBase-ER-DWDM40-57.36 (ITU 25)'),
(2,1431,'10GBase-ER-DWDM40-56.55 (ITU 26)'),
(2,1432,'10GBase-ER-DWDM40-55.75 (ITU 27)'),
(2,1433,'10GBase-ER-DWDM40-54.94 (ITU 28)'),
(2,1434,'10GBase-ER-DWDM40-54.13 (ITU 29)'),
(2,1435,'10GBase-ER-DWDM40-53.33 (ITU 30)'),
(2,1436,'10GBase-ER-DWDM40-52.52 (ITU 31)'),
(2,1437,'10GBase-ER-DWDM40-51.72 (ITU 32)'),
(2,1438,'10GBase-ER-DWDM40-50.92 (ITU 33)'),
(2,1439,'10GBase-ER-DWDM40-50.12 (ITU 34)'),
(2,1440,'10GBase-ER-DWDM40-49.32 (ITU 35)'),
(2,1441,'10GBase-ER-DWDM40-48.51 (ITU 36)'),
(2,1442,'10GBase-ER-DWDM40-47.72 (ITU 37)'),
(2,1443,'10GBase-ER-DWDM40-46.92 (ITU 38)'),
(2,1444,'10GBase-ER-DWDM40-46.12 (ITU 39)'),
(2,1445,'10GBase-ER-DWDM40-45.32 (ITU 40)'),
(2,1446,'10GBase-ER-DWDM40-44.53 (ITU 41)'),
(2,1447,'10GBase-ER-DWDM40-43.73 (ITU 42)'),
(2,1448,'10GBase-ER-DWDM40-42.94 (ITU 43)'),
(2,1449,'10GBase-ER-DWDM40-42.14 (ITU 44)'),
(2,1450,'10GBase-ER-DWDM40-41.35 (ITU 45)'),
(2,1451,'10GBase-ER-DWDM40-40.56 (ITU 46)'),
(2,1452,'10GBase-ER-DWDM40-39.77 (ITU 47)'),
(2,1453,'10GBase-ER-DWDM40-38.98 (ITU 48)'),
(2,1454,'10GBase-ER-DWDM40-38.19 (ITU 49)'),
(2,1455,'10GBase-ER-DWDM40-37.40 (ITU 50)'),
(2,1456,'10GBase-ER-DWDM40-36.61 (ITU 51)'),
(2,1457,'10GBase-ER-DWDM40-35.82 (ITU 52)'),
(2,1458,'10GBase-ER-DWDM40-35.04 (ITU 53)'),
(2,1459,'10GBase-ER-DWDM40-34.25 (ITU 54)'),
(2,1460,'10GBase-ER-DWDM40-33.47 (ITU 55)'),
(2,1461,'10GBase-ER-DWDM40-32.68 (ITU 56)'),
(2,1462,'10GBase-ER-DWDM40-31.90 (ITU 57)'),
(2,1463,'10GBase-ER-DWDM40-31.12 (ITU 58)'),
(2,1464,'10GBase-ER-DWDM40-30.33 (ITU 59)'),
(2,1465,'10GBase-ER-DWDM40-29.55 (ITU 60)'),
(2,1466,'10GBase-ER-DWDM40-28.77 (ITU 61)')";
			if (version_compare ($dbver, '0.19.10', '<'))
				$query[] = "INSERT INTO Dictionary (chapter_id,dict_key,dict_value) VALUES (2,1603,'1000Base-T (HP c-Class)')";
			if (version_compare ($dbver, '0.19.11', '<'))
				$query[] = "INSERT INTO Dictionary (chapter_id,dict_key,dict_value) VALUES (2,1642,'10GBase-T')";
			if (version_compare ($dbver, '0.19.12', '<'))
				$query[] = "INSERT INTO Dictionary (chapter_id,dict_key,dict_value) VALUES
(2,1661,'40GBase-KR4'),
(2,1663,'40GBase-SR4'),
(2,1664,'40GBase-LR4'),
(2,1668,'empty CFP'),
(2,1669,'100GBase-SR10'),
(2,1670,'100GBase-LR4'),
(2,1671,'100GBase-ER4')";
			$query[] = "INSERT INTO PortOuterInterface SELECT dict_key, dict_value FROM Dictionary WHERE chapter_id = 2";
			// Previously listed 10GBase-Kx actually means two standards: 10GBase-KX4
			// and 10GBase-KR. Make respective changes and make primary key auto
			// increment start at 2000.
			$query[] = "UPDATE PortOuterInterface SET oif_name = '10GBase-KX4' WHERE id = 41";
			$query[] = "INSERT INTO PortOuterInterface (id, oif_name) VALUES (1999, '10GBase-KR')";
			$query[] = "INSERT INTO PortCompat (type1, type2) VALUES (1999, 1999)";
			$query[] = "DELETE FROM Dictionary WHERE chapter_id = 2";
			$query[] = "DELETE FROM Chapter WHERE id = 2";
			$query[] = "ALTER TABLE PortInterfaceCompat ADD CONSTRAINT `PortInterfaceCompat-FK-oif_id` FOREIGN KEY (oif_id) REFERENCES PortOuterInterface (id)";
			$query[] = "ALTER TABLE PortCompat ADD CONSTRAINT `PortCompat-FK-oif_id1` FOREIGN KEY (type1) REFERENCES PortOuterInterface (id)";
			$query[] = "ALTER TABLE PortCompat ADD CONSTRAINT `PortCompat-FK-oif_id2` FOREIGN KEY (type2) REFERENCES PortOuterInterface (id)";
			// Add more 40G and 100G standards.
			$query[] = "INSERT INTO PortOuterInterface (id, oif_name) VALUES
(1660,'40GBase-FR'),
(1662,'40GBase-ER4'),
(1672,'100GBase-SR4'),
(1673,'100GBase-KR4'),
(1674,'100GBase-KP4')";
			$query[] = "INSERT INTO PortInterfaceCompat (iif_id, oif_id) VALUES
(10,1660),
(10,1662),
(11,1672),
(11,1673),
(11,1674)";
			$query[] = "INSERT INTO PortCompat (type1, type2) VALUES
(1660,1660),
(1662,1662),
(1672,1672),
(1673,1673),
(1674,1674)";
			// Refine 1G OIF list: fix spelling and add a new standard.
			$query[] = "UPDATE PortOuterInterface SET oif_name = '1000Base-LX10' WHERE id = 1205";
			$query[] = "INSERT INTO PortOuterInterface (id, oif_name) VALUES (42, '1000Base-EX')";
			$query[] = "INSERT INTO PortCompat (type1, type2) VALUES (42, 42)";
			$query[] = "INSERT INTO PortInterfaceCompat (iif_id, oif_id) VALUES (3, 42), (4,42)";
			// patch cables
		$query[] = "
CREATE TABLE `PatchCableConnector` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `origin` enum('default','custom') NOT NULL DEFAULT 'custom',
  `connector` char(32) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `connector_per_origin` (`connector`,`origin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
		$query[] = "
CREATE TABLE `PatchCableType` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `origin` enum('default','custom') NOT NULL DEFAULT 'custom',
  `pctype` char(64) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pctype_per_origin` (`pctype`,`origin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
		$query[] = "
CREATE TABLE `PatchCableConnectorCompat` (
  `pctype_id` int(10) unsigned NOT NULL,
  `connector_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`pctype_id`,`connector_id`),
  KEY `connector_id` (`connector_id`),
  CONSTRAINT `PatchCableConnectorCompat-FK-connector_id` FOREIGN KEY (`connector_id`) REFERENCES `PatchCableConnector` (`id`),
  CONSTRAINT `PatchCableConnectorCompat-FK-pctype_id` FOREIGN KEY (`pctype_id`) REFERENCES `PatchCableType` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
		$query[] = "
CREATE TABLE `PatchCableHeap` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pctype_id` int(10) unsigned NOT NULL,
  `end1_conn_id` int(10) unsigned NOT NULL,
  `end2_conn_id` int(10) unsigned NOT NULL,
  `amount` smallint(5) unsigned NOT NULL DEFAULT '0',
  `length` decimal(5,2) unsigned NOT NULL DEFAULT '1.00',
  `description` char(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `compat1` (`pctype_id`,`end1_conn_id`),
  KEY `compat2` (`pctype_id`,`end2_conn_id`),
  CONSTRAINT `PatchCableHeap-FK-compat1` FOREIGN KEY (`pctype_id`, `end1_conn_id`) REFERENCES `PatchCableConnectorCompat` (`pctype_id`, `connector_id`),
  CONSTRAINT `PatchCableHeap-FK-compat2` FOREIGN KEY (`pctype_id`, `end2_conn_id`) REFERENCES `PatchCableConnectorCompat` (`pctype_id`, `connector_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
		$query[] = "
CREATE TABLE `PatchCableHeapLog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `heap_id` int(10) unsigned NOT NULL,
  `date` datetime NOT NULL,
  `user` char(64) NOT NULL,
  `message` char(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `heap_id-date` (`heap_id`,`date`),
  CONSTRAINT `PatchCableHeapLog-FK-heap_id` FOREIGN KEY (`heap_id`) REFERENCES `PatchCableHeap` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
		$query[] = "
CREATE TABLE `PatchCableOIFCompat` (
  `pctype_id` int(10) unsigned NOT NULL,
  `oif_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`pctype_id`,`oif_id`),
  KEY `oif_id` (`oif_id`),
  CONSTRAINT `PatchCableOIFCompat-FK-oif_id` FOREIGN KEY (`oif_id`) REFERENCES `PortOuterInterface` (`id`),
  CONSTRAINT `PatchCableOIFCompat-FK-pctype_id` FOREIGN KEY (`pctype_id`) REFERENCES `PatchCableType` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
		$query[] = "INSERT INTO PatchCableConnector (id, origin, connector) VALUES
(1,'default','FC/PC'),(2,'default','FC/APC'),
(3,'default','LC/PC'),(4,'default','LC/APC'),
(5,'default','MPO-12/PC'),(6,'default','MPO-12/APC'),
(7,'default','MPO-24/PC'),(8,'default','MPO-24/APC'),
(9,'default','SC/PC'),(10,'default','SC/APC'),
(11,'default','ST/PC'),(12,'default','ST/APC'),
(13,'default','T568/8P8C/RJ45'),
(14,'default','SFP-1000'),
(15,'default','SFP+'),
(999,'default','CX4/SFF-8470')";
		$query[] = "INSERT INTO PatchCableType (id, origin, pctype) VALUES
(1,'default','duplex OM1'),
(2,'default','duplex OM2'),
(3,'default','duplex OM3'),
(4,'default','duplex OM4'),
(5,'default','duplex OS1'),
(6,'default','duplex OS2'),
(7,'default','simplex OM1'),
(8,'default','simplex OM2'),
(9,'default','simplex OM3'),
(10,'default','simplex OM4'),
(11,'default','simplex OS1'),
(12,'default','simplex OS2'),
(13,'default','Cat.5 TP'),
(14,'default','Cat.6 TP'),
(15,'default','Cat.6a TP'),
(16,'default','Cat.7 TP'),
(17,'default','Cat.7a TP'),
(18,'default','12-fiber OM3'),
(19,'default','12-fiber OM4'),
(20,'default','10Gb/s CX4 coax'),
(21,'default','24-fiber OM3'),
(22,'default','24-fiber OM4'),
(23,'default','1Gb/s 50cm shielded'),
(24,'default','10Gb/s 24AWG twinax'),
(25,'default','10Gb/s 26AWG twinax'),
(26,'default','10Gb/s 28AWG twinax'),
(27,'default','10Gb/s 30AWG twinax'),
(999,'default','Cat.3 TP')";
		$query[] = "INSERT INTO PatchCableOIFCompat (pctype_id, oif_id) VALUES
(13,18),(14,18),(15,18),(16,18),(17,18),(999,18), -- 10Base-T: Cat.3+ TP
(11,1198),(12,1198),(11,1199),(12,1199),          -- 100Base-BX10: 1xSMF
(5,1197),(6,1197),                                -- 100Base-LX10: 2xSMF
(5,1200),(6,1200),                                -- 100Base-EX: 2xSMF
(5,1201),(6,1201),                                -- 100Base-ZX: 2xSMF
(1,1195),(2,1195),(3,1195),(4,1195),              -- 100Base-FX: 2xMMF
(1,1196),(2,1196),(3,1196),(4,1196),              -- 100Base-SX: 2xMMF
(13,19),(14,19),(15,19),(16,19),(17,19),          -- 100Base-TX: Cat.5+ TP
(11,1206),(12,1206),(11,1207),(12,1207),          -- 1000Base-BX10: 1xSMF
(5,1204),(6,1204),                                -- 1000Base-LX: 2xSMF
(5,1205),(6,1205),                                -- 1000Base-LX10: 2xSMF
(1,1202),(2,1202),(3,1202),(4,1202),              -- 1000Base-SX: 2xMMF
(1,1203),(2,1203),(3,1203),(4,1203),              -- 1000Base-SX+: 2xMMF
(13,24),(14,24),(15,24),(16,24),(17,24),          -- 1000Base-T: Cat.5+ TP
(5,34),(6,34),                                    -- 1000Base-ZX: 2xSMF
(23,1077),                                        -- 1000Base direct attach: shielded
(1,30),(2,30),(3,30),(4,30),                      -- 10GBase-SR: 2xMMF
(5,36),(6,36),                                    -- 10GBase-LR: 2xSMF
(5,35),(6,35),                                    -- 10GBase-ER: 2xSMF
(5,38),(6,38),                                    -- 10GBase-ZR: 2xSMF
(1,39),(2,39),(3,39),(4,39),(5,39),(6,39),        -- 10GBase-LX4: 2xMMF/2xSMF
(1,37),(2,37),(3,37),(4,37),                      -- 10GBase-LRM: 2xMMF
(14,1642),(15,1642),(16,1642),(17,1642),          -- 10GBase-T: Cat.6+ TP
(20,40),                                          -- 10GBase-CX4: coax
(24,1084),(25,1084),(26,1084),(27,1084),          -- 10GBase direct attach: twinax
(18,1663),(19,1663),                              -- 40GBase-SR4: 8xMMF
(5,1664),(6,1664),                                -- 40GBase-LR4: 2xSMF
(5,1662),(6,1662),                                -- 40GBase-ER4: 2xSMF
(5,1660),(6,1660),                                -- 40GBase-FR: 2xSMF
(21,1669),(22,1669),                              -- 100GBase-SR10: 20xMMF
(18,1672),(19,1672),                              -- 100GBase-SR4: 8xMMF
(5,1670),(6,1670),                                -- 100GBase-LR4: 2xSMF
(5,1671),(6,1671)                                 -- 100GBase-ER4: 2xSMF";
		$query[] = "INSERT INTO PatchCableConnectorCompat (pctype_id, connector_id) VALUES
(1,1),(2,1),(3,1),(4,1),(5,1),(6,1),(7,1),(8,1),(9,1),(10,1),(11,1),(12,1), -- FC/PC
(1,2),(2,2),(3,2),(4,2),(5,2),(6,2),(7,2),(8,2),(9,2),(10,2),(11,2),(12,2), -- FC/APC
(1,3),(2,3),(3,3),(4,3),(5,3),(6,3),(7,3),(8,3),(9,3),(10,3),(11,3),(12,3), -- LC/PC
(1,4),(2,4),(3,4),(4,4),(5,4),(6,4),(7,4),(8,4),(9,4),(10,4),(11,4),(12,4), -- LC/APC
(1,9),(2,9),(3,9),(4,9),(5,9),(6,9),(7,9),(8,9),(9,9),(10,9),(11,9),(12,9), -- SC/PC
(1,10),(2,10),(3,10),(4,10),(5,10),(6,10),(7,10),(8,10),(9,10),(10,10),(11,10),(12,10), -- SC/APC
(1,11),(2,11),(3,11),(4,11),(5,11),(6,11),(7,11),(8,11),(9,11),(10,11),(11,11),(12,11), -- ST/PC
(1,12),(2,12),(3,12),(4,12),(5,12),(6,12),(7,12),(8,12),(9,12),(10,12),(11,12),(12,12), -- ST/APC
(13,13),(14,13),(15,13),(16,13),(17,13),(999,13), -- T568
(18,5),(19,5), -- MPO-12/PC
(18,6),(19,6), -- MPO-12/APC
(20,999), -- CX4
(21,7),(22,7), -- MPO-24/PC
(21,8),(22,8), -- MPO-24/APC
(23,14), -- SFP-1000
(24,15),(25,15),(26,15),(27,15) -- SFP+";
			// add rules for Cisco UCS objects
			$query[] = "INSERT INTO `ObjectParentCompat` (`parent_objtype_id`, `child_objtype_id`) VALUES (1787,8),(1787,1502)";
			$query[] = "UPDATE Config SET varvalue = '0.20.8' WHERE varname = 'DB_VERSION'";
			break;
		case '0.20.9':
			$query[] = "ALTER TABLE CactiGraph ADD KEY (server_id)";
			$query[] = "ALTER TABLE CactiGraph DROP PRIMARY KEY";
			$query[] = "ALTER TABLE CactiGraph ADD PRIMARY KEY (object_id, server_id, graph_id)";
			$query[] = "ALTER TABLE CactiGraph DROP KEY `object_id`";
			$query[] = "UPDATE Config SET description = 'List of pages to display in quick links' WHERE varname = 'QUICK_LINK_PAGES'";
			$query[] = "INSERT INTO `Config` (varname, varvalue, vartype, emptyok, is_hidden, is_userdefined, description) VALUES ('CACTI_RRA_ID','1','uint','no','no','yes','RRA ID for Cacti graphs displayed in RackTables')";
			$query[] = "INSERT INTO `Config` (`varname`,`varvalue`,`vartype`,`emptyok`,`is_hidden`,`is_userdefined`,`description`)
VALUES ('SHOW_OBJECTTYPE',  'no',  'string',  'no',  'no',  'yes',  'Show object type column on depot page.')";

			$query[] = "INSERT INTO PortInnerInterface (id, iif_name) VALUES (12, 'CFP2'),(13,'CPAK')";
			$query[] = "INSERT INTO PortOuterInterface (id, oif_name) VALUES (1589, 'empty CFP2'),(1590,'empty CPAK')";
			$query[] = "INSERT INTO PortInterfaceCompat (iif_id, oif_id) VALUES
				(12,1589),(12,1669),(12,1670),(12,1671),(12,1672),(12,1673),(12,1674),
				(13,1590),(13,1669),(13,1670),(13,1671),(13,1672),(13,1673),(13,1674)";
			$query[] = "INSERT INTO PortCompat (type1, type2) VALUES (1588,1589),(1588,1590),(1589,1589),(1589,1590),(1590,1590)";
			$query[] = "UPDATE Config SET varvalue = CONCAT(varvalue, '; 12=1589; 13=1590') WHERE varname = 'DEFAULT_PORT_OIF_IDS'";
			$query[] = extendPortCompatQuery();

			$query[] = "UPDATE Config SET varvalue = '0.20.9' WHERE varname = 'DB_VERSION'";
			break;
		case '0.20.10':
			$query[] = "UPDATE Config SET varvalue = '0.20.10' WHERE varname = 'DB_VERSION'";
			break;
		case '0.20.11':
			$query[] = "ALTER TABLE VLANDomain ADD COLUMN `group_id` int(10) UNSIGNED DEFAULT NULL AFTER `id`, " .
				"ADD CONSTRAINT `VLANDomain-FK-group_id` FOREIGN KEY (`group_id`) REFERENCES `VLANDomain` (`id`) ON DELETE SET NULL";

			// new 100GBase port types
			$query[] = "INSERT INTO `PortInnerInterface` (`id`, `iif_name`) VALUES (14,'CXP')";
			$query[] = "INSERT INTO `PortOuterInterface` (`id`, `oif_name`) VALUES
				(1591,'empty CXP'),
				(1675,'100GBase-LR10'),
				(1676,'100GBase-ER10'),
				(1677,'100GBase-CR4'),
				(1678,'100GBase-CR10')";
			$query[] = "INSERT INTO `PatchCableOIFCompat` (`pctype_id`, `oif_id`) VALUES
				(5,1675),(6,1675),  -- 100GBase-LR10: 2xSMF
				(5,1676),(6,1676)   -- 100GBase-ER10: 2xSMF";
			$query[] = "INSERT INTO `PortInterfaceCompat` (`iif_id`, `oif_id`) VALUES
				(11,1675),(11,1676),
				(12,1675),(12,1676),
				(13,1675),(13,1676),
				(14,1591),(14,1677),(14,1678)";
			$query[] = "INSERT INTO `PortCompat` (`type1`, `type2`) VALUES
				(1591,1591),
				(1675,1675),
				(1676,1676),
				(1677,1677),
				(1678,1678)";
			$query[] = "UPDATE Config SET varvalue = CONCAT(varvalue, '; 14=1591') WHERE varname = 'DEFAULT_PORT_OIF_IDS'";

			// ABI_ver = 2, invalidate RackCode cache
			$query[] = "DELETE FROM Script WHERE script_name='RackCodeCache'";

			$query[] = "INSERT INTO Config (varname, varvalue, is_hidden, is_userdefined, description) VALUES ('IPV4_TREE_SHOW_UNALLOCATED', 'yes', 'no', 'yes', 'Show unallocated networks in IPv4 tree'); ";
			$query[] = "UPDATE Config SET varvalue = '0.20.11' WHERE varname = 'DB_VERSION'";

			break;
		case '0.20.12':
			// NO_ZERO_DATE
			$query[] = "ALTER TABLE LDAPCache MODIFY COLUMN last_retry timestamp NULL DEFAULT NULL";
			$query[] = "DELETE FROM LDAPCache";

			$query[] = "INSERT INTO ObjectParentCompat (parent_objtype_id, child_objtype_id) VALUES (1787,4)";

			$port_trigger_body = <<<ENDOFTRIGGER
PortTrigger:BEGIN
  IF (NEW.`l2address` IS NOT NULL AND (SELECT COUNT(*) FROM `Port` WHERE `l2address` = NEW.`l2address` AND `object_id` != NEW.`object_id`) > 0) THEN
    CALL `Port-l2address-already-exists-on-another-object`;
  END IF;
END;
ENDOFTRIGGER;
			$query[] = "CREATE TRIGGER `Port-before-insert` BEFORE INSERT ON `Port` FOR EACH ROW $port_trigger_body";
			$query[] = "CREATE TRIGGER `Port-before-update` BEFORE UPDATE ON `Port` FOR EACH ROW $port_trigger_body";

			$query[] = "ALTER TABLE UserConfig DROP FOREIGN KEY `UserConfig-FK-varname`";
			$query[] = "ALTER TABLE UserConfig ADD CONSTRAINT `UserConfig-FK-varname` FOREIGN KEY (`varname`) REFERENCES `Config` (`varname`) ON DELETE CASCADE ON UPDATE CASCADE";
			$query[] = "UPDATE Config SET varname = 'SYNC_8021Q_LISTSRC' WHERE varname = 'SYNC_802Q_LISTSRC'";

			// new iif_type 'QSFP28'
			$query[] = "INSERT INTO `PortInnerInterface` (`id`, `iif_name`) VALUES (15,'QSFP28')";
			$query[] = "UPDATE Config SET varvalue = CONCAT(varvalue, '; 15=1588') WHERE varname = 'DEFAULT_PORT_OIF_IDS' AND 0 = INSTR(varvalue, '15=')";
			// rename 'empty QSFP+' to 'empty QSFP'
			$query[] = "UPDATE PortOuterInterface SET oif_name='empty QSFP' WHERE id=1588";

			$query[] = "INSERT INTO PortOuterInterface (id, oif_name) VALUES
				(1088,'1000Base-BX40-D'),
				(1089,'1000Base-BX40-U'),
				(1090,'1000Base-BX80-D'),
				(1091,'1000Base-BX80-U')";
			$query[] = "INSERT INTO PortCompat (type1, type2) VALUES
				(1088,1089),
				(1089,1088),
				(1090,1091),
				(1091,1090)";
			$query[] = "INSERT INTO PortInterfaceCompat (iif_id, oif_id) VALUES
				(4,1088), (4,1089), (4,1090), (4,1091),
				(15,1588),(15,1660),(15,1662),(15,1663),(15,1664),(15,1670),(15,1671),(15,1672),(15,1673),(15,1674)";
			$query[] = "INSERT INTO PatchCableOIFCompat (pctype_id, oif_id) VALUES
				(11,1088), (12,1088), (11,1089), (12,1089),
				(11,1090), (12,1090), (11,1091), (12,1091)";


			$query[] = "UPDATE Config SET varvalue = '0.20.12' WHERE varname = 'DB_VERSION'";
			break;
		case '0.20.13':
			$query[] = "DROP TRIGGER IF EXISTS `Port-before-insert`";
			$query[] = "DROP TRIGGER IF EXISTS `Port-before-update`";
			$query[] = "UPDATE Config SET varvalue = '0.20.13' WHERE varname = 'DB_VERSION'";
			break;
		case '0.20.14':
			$query[] = "UPDATE Object SET has_problems = 'no' WHERE objtype_id = 1561 AND has_problems = ''";
			$query[] = "UPDATE ObjectHistory SET ctime = ctime, has_problems = 'no' WHERE objtype_id = 1561 AND has_problems = ''";
			$query[] = "UPDATE Config SET varvalue = '0.20.14' WHERE varname = 'DB_VERSION'";
			break;
		case 'dictionary':
			$query = reloadDictionary();
			break;
		default:
			return NULL;
	}
	return $query;
}

function executeUpgradeBatch ($batchid)
{
	global $dbxlink;
	$query = getUpgradeBatch($batchid);
	if ($query === NULL)
	{
		showError ("unknown batch '${batchid}'", __FUNCTION__);
		die;
	}
	$failures = array();
	echo "<tr><th>Executing batch '${batchid}'</th><td>";
	foreach ($query as $q)
	{
		try
		{
			$result = $dbxlink->query ($q);
		}
		catch (PDOException $e)
		{
			$errorInfo = $dbxlink->errorInfo();
			$failures[] = array ($q, $errorInfo[2]);
		}
	}
	if (!count ($failures))
		echo "<strong><font color=green>done</font></strong>";
	else
	{
		echo "<strong><font color=red>The following queries failed:</font></strong><br><pre>";
		foreach ($failures as $f)
		{
			list ($q, $i) = $f;
			echo "${q} -- ${i}\n";
		}
		echo "</pre>";
	}
	echo '</td></tr>';
}

function authenticate_admin ($username, $password)
{
	global $dbxlink;
	$prepared = $dbxlink->prepare ('SELECT COUNT(*) FROM UserAccount WHERE user_id=1 AND user_name=? AND user_password_hash=?');
	if (!$prepared->execute (array ($username, sha1 ($password))))
		die ('SQL query failed in ' . __FUNCTION__);
	$rows = $prepared->fetchAll (PDO::FETCH_NUM);
	return $rows[0][0] == 1;
}

// Database version detector. Should behave corretly on any
// working dataset a user might have.
function getDatabaseVersion ()
{
	global $dbxlink;
	$prepared = $dbxlink->prepare ('SELECT varvalue FROM Config WHERE varname = "DB_VERSION" and vartype = "string"');
	if (! $prepared->execute())
	{
		$errorInfo = $dbxlink->errorInfo();
		die (__FUNCTION__ . ': SQL query failed with error ' . $errorInfo[2]);
	}
	$rows = $prepared->fetchAll (PDO::FETCH_NUM);
	if (count ($rows) != 1 || $rows[0][0] == '')
		die (__FUNCTION__ . ': Cannot guess database version. Config table is present, but DB_VERSION is missing or invalid. Giving up.');
	$ret = $rows[0][0];
	return $ret;
}

function showUpgradeError ($info = '', $location = 'N/A')
{
	if (preg_match ('/\.php$/', $location))
		$location = basename ($location);
	elseif ($location != 'N/A')
		$location = $location . '()';
	echo "<div class=msg_error>An error has occured in [${location}]. ";
	if ($info == '')
		echo 'No additional information is available.';
	else
		echo "Additional information:<br><p>\n<pre>\n${info}\n</pre></p>";
	echo "Go back or try starting from <a href='index.php'>index page</a>.<br></div>\n";
}

// changes the value of config variable. If $old_value_filter is set, value is changed only if current value equals to it.
function replaceConfigVarValue ($varname, $new_value, $old_value_filter = NULL)
{
	global $dbxlink;
	if (isset ($old_value_filter))
	{
		$result = $dbxlink->prepare ("SELECT varvalue FROM Config WHERE varname = ?");
		$result->execute (array ($varname));
		if ($row = $result->fetch (PDO::FETCH_ASSOC))
			if ($row['varvalue'] != $old_value_filter)
				return;
		unset ($result);
	}
	$result = $dbxlink->prepare ("UPDATE Config set varvalue = ? WHERE varname = ?");
	$result->execute (array ($new_value, $varname));
}

function renderUpgraderHTML()
{
	global $found_secret_file;
	if (! $found_secret_file)
		die ('<center>There is no working RackTables instance here, <a href="?module=installer">install</a>?</center>');

	try
	{
		connectDB();
	}
	catch (RackTablesError $e)
	{
		die ("Database connection failed:\n\n" . $e->getMessage());
	}

	if
	(
		! isset ($_SERVER['PHP_AUTH_USER']) ||
		$_SERVER['PHP_AUTH_USER'] == '' ||
		! isset ($_SERVER['PHP_AUTH_PW']) ||
		$_SERVER['PHP_AUTH_PW'] == '' ||
		!authenticate_admin ($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'])
	)
	{
		header ('WWW-Authenticate: Basic realm="RackTables upgrade"');
		header ('HTTP/1.0 401 Unauthorized');
?>
<h1>Trouble logging in?</h1>
You are trying to authenticate for the RackTables upgrade screen. This means that
you must authenticate with the username and password of the main RackTables
administrator. There is only one such account in each installation, its default
username is "admin". RackTables wiki provides more information on this topic.
<?php
		die;
	}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head><title>RackTables upgrade script</title>
<style type="text/css">
.tdleft {
	text-align: left;
}

.trok {
	background-color: #80FF80;
}

.trwarning {
	background-color: #FFFF80;
}

.trerror {
	background-color: #FF8080;
}
</style>
</head>
<body>
<h1>Platform check status</h1>
<?php

if (!platform_is_ok())
{
	echo '<h1>Please resolve the failed (red) item(s) above.</h1>';
	die ('</body></html>');
}

echo '<h1>Upgrade status</h1>';
global $dbver;
$dbver = getDatabaseVersion();
echo '<table border=1 cellpadding=5>';
echo "<tr><th>Current status</th><td>Data version: ${dbver}<br>Code version: " . CODE_VERSION . "</td></tr>\n";

$path = getDBUpgradePath ($dbver, CODE_VERSION);
if ($path === NULL)
{
	echo "<tr><th>Upgrade path</th><td><font color=red>not found</font></td></tr>\n";
	echo "<tr><th>Summary</th><td>Check README for more information. RackTables releases prior to 0.18.0 ";
	echo "must be upgraded to 0.18.0 first. RackTables releases prior to 0.20.0 must be upgraded to 0.20.0 first.</td></tr>\n";
}
else
{
	if (!count ($path))
		echo "<tr><th>Summary</th><td>Come back later.</td></tr>\n";
	else
	{
		echo "<tr><th>Upgrade path</th><td>${dbver} &rarr; " . implode (' &rarr; ', $path) . "</td></tr>\n";
		global $relnotes;
		foreach ($path as $batchid)
			if (isset ($relnotes[$batchid]))
				echo "<tr><th>Release notes for ${batchid}</th><td><pre>" . $relnotes[$batchid] . "</pre></td></tr>\n";
		if (array_key_exists ('reallyreally', $_REQUEST))
		{
			foreach ($path as $batchid)
				executeUpgradeBatch ($batchid);
			executeUpgradeBatch ('dictionary');
			echo "<tr><th>Summary</th><td>Upgrade complete, it is Ok to ";
			echo "<a href='index.php'>enter</a> the system.</td></tr>\n";
		}
		else
		{
			echo '<form method=post action="index.php?module=upgrade"><tr><th>Wait!</th>';
			echo '<td><p>RackTables database upgrades sometimes go wrong because of assorted reasons. ';
			echo 'It is <strong>highly recommended</strong> to make a database backup before ';
			echo 'proceeding any further. <tt>mysqldump</tt> and <tt>PHPMyAdmin</tt> are convenient ';
			echo 'tools for doing this.</p>';
			echo '<p><input type=checkbox name=reallyreally id=reallyreally><label for=reallyreally>';
			echo 'I am ready to bear all risks of this upgrade. I am ready to roll it back in case of ';
			echo 'a failure.</label> <input type=submit value="Yes, I am."></p></td></tr></form>';
		}
	}
}
echo '</table>';
echo '</body></html>';
}

// returns SQL query to make PortCompat symmetric (insert missing reversed-order pairs).
// It should be called each time after the PortCompat table pairs being added during upgrade.
function extendPortCompatQuery()
{
	return "INSERT INTO PortCompat SELECT pc1.type2, pc1.type1 FROM PortCompat pc1 LEFT JOIN PortCompat pc2 ON pc1.type1 = pc2.type2 AND pc1.type2 = pc2.type1 WHERE pc2.type1 IS NULL";
}

function convertSLBTablesToBinIPs()
{
	global $dbxlink;

	$dbxlink->query ("DROP TABLE IF EXISTS `IPv4VS_new`, `IPv4RS_new`, `IPv4VS_old`, `IPv4RS_old`");

	$dbxlink->query (<<<END
CREATE TABLE `IPv4VS_new` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `vip` varbinary(16) NOT NULL,
  `vport` smallint(5) unsigned default NULL,
  `proto` enum('TCP','UDP','MARK') NOT NULL default 'TCP',
  `name` char(255) default NULL,
  `vsconfig` text,
  `rsconfig` text,
  PRIMARY KEY  (`id`),
  KEY `vip` (`vip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
END
	);
	$result = $dbxlink->query ("SELECT id, vip, vport, proto, name, vsconfig, rsconfig FROM IPv4VS");
	$rows = $result->fetchAll (PDO::FETCH_ASSOC);
	unset ($result);
	foreach ($rows as $row)
	{
		$row['vip'] = ip4_int2bin ($row['vip']);
		usePreparedInsertBlade ('IPv4VS_new', $row);
	}

	$dbxlink->query (<<<END
CREATE TABLE `IPv4RS_new` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `inservice` enum('yes','no') NOT NULL default 'no',
  `rsip` varbinary(16) NOT NULL,
  `rsport` smallint(5) unsigned default NULL,
  `rspool_id` int(10) unsigned default NULL,
  `rsconfig` text,
  `comment` varchar(255) DEFAULT NULL,
  PRIMARY KEY  (`id`),
  KEY `rsip` (`rsip`),
  UNIQUE KEY `pool-endpoint` (`rspool_id`,`rsip`,`rsport`),
  CONSTRAINT `IPRS-FK` FOREIGN KEY (`rspool_id`) REFERENCES `IPv4RSPool` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8
END
	);
	$result = $dbxlink->query ("SELECT id, inservice, rsip, rsport, rspool_id, rsconfig FROM IPv4RS");
	$rows = $result->fetchAll (PDO::FETCH_ASSOC);
	unset ($result);
	foreach ($rows as $row)
	{
		$row['rsip'] = ip4_int2bin ($row['rsip']);
		usePreparedInsertBlade ('IPv4RS_new', $row);
	}

	$dbxlink->query (<<<END
RENAME TABLE
	`IPv4VS` TO `IPv4VS_old`,
	`IPv4VS_new` TO `IPv4VS`,
	`IPv4RS` TO `IPv4RS_old`,
	`IPv4RS_new` TO `IPv4RS`
END
	);
	// re-create foreign key in IPv4LB
	$dbxlink->query ("ALTER TABLE `IPv4LB` DROP FOREIGN KEY `IPv4LB-FK-vs_id`");
	$dbxlink->query ("ALTER TABLE `IPv4LB` ADD CONSTRAINT `IPv4LB-FK-vs_id` FOREIGN KEY (`vs_id`) REFERENCES `IPv4VS` (`id`)");

	$dbxlink->query ("DROP TABLE `IPv4VS_old`, `IPv4RS_old`");

	// re-create foreign key in IPv4RS
	$dbxlink->query ("ALTER TABLE `IPv4RS` DROP FOREIGN KEY `IPRS-FK`");
	$dbxlink->query ("ALTER TABLE `IPv4RS` ADD CONSTRAINT `IPv4RS-FK` FOREIGN KEY (`rspool_id`) REFERENCES `IPv4RSPool` (`id`) ON DELETE CASCADE");
}

// converts the values of old-style config vars TELNET_OBJS_LISTSRC, SSH_OBJS_LISTSRC, RDP_OBJS_LISTSRC
// to the format of MGMT_PROTOS (comma-separated list of "proto: rackcode" pairs)
function convertMgmtConfigVars()
{
	global $dbxlink;
	$ret = array();
	foreach (array ('telnet' => 'TELNET_OBJS_LISTSRC', 'ssh' => 'SSH_OBJS_LISTSRC', 'rdp' => 'RDP_OBJS_LISTSRC') as $proto => $varname)
	{
		$result = $dbxlink->prepare ("SELECT varvalue FROM Config WHERE varname = ?");
		$result->execute (array ($varname));
		if ($row = $result->fetch (PDO::FETCH_ASSOC))
			if ($row['varvalue'] != 'false' && $row['varvalue'] != '')
				$ret[] = "$proto: " . $row['varvalue'];
		unset ($result);
	}
	return implode (',', $ret);
}

?>
