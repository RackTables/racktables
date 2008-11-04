<?php

// At the moment we assume, that for any two releases we can
// sequentally execute all batches, that separate them, and
// nothing will break. If this changes one day, the function
// below will have to generate smarter upgrade paths, while
// the upper layer will remain the same.
// Returning an empty array means that no upgrade is necessary.
function getDBUpgradePath ($v1, $v2)
{
	$versionhistory = array
	(
		'0.14.4',
		'0.14.5',
		'0.14.6',
		'0.14.7',
		'0.14.8',
		'0.14.9',
		'0.14.10',
		'0.14.11',
		'0.14.12',
		'0.15.0',
		'0.15.1',
		'0.16.0',
		'0.16.1',
		'0.16.2',
		'0.16.3',
		'0.16.4',
	);
	if (!in_array ($v1, $versionhistory) || !in_array ($v2, $versionhistory))
	{
		showError ("An upgrade path has been requested for versions '${v1}' and '${v2}', " .
		  "and at least one of those isn't known to me.", __FILE__);
		die;
	}
	$skip = TRUE;
	$path = array();
	// Now collect all versions > $v1 and <= $v2
	foreach ($versionhistory as $v)
	{
		if ($v == $v1)
		{
			$skip = FALSE;
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

function printReleaseNotes ($batchid)
{
	switch ($batchid)
	{
		case '0.16.0':
			echo "<font color=red><strong>Release notes for ${batchid}</strong></font><br>";
			echo 'The user permission records of this system have been automatically converted ';
			echo 'to switch to the new RackCode authorization system. To prevent possible data ';
			echo 'leak, the second line of the automatically created configuration bans everything ';
			echo '(and the first allows everything to you, the administrator). The whole config can ';
			echo "be reviewed on the Permissions page (under Configuration). Sorry for the inconvenience.<br><br>\n";
			break;
		case '0.16.3':
			echo "<font color=red><strong>Release notes for ${batchid}</strong></font><br>";
			echo 'This release fixes a missing UNIQUE KEY in a table. The upgrade script may find it necessary first to transform some records.<br>';
			echo 'Because of this it is normal to see several "update TagStorage ... Duplicate entry" failed queries during the upgrade.<br>';
			echo 'Additionally, it is normal to see " Can\'t DROP \'endpoint\'" message during upgrade<br>';
			break;
		default:
			break;
	}
}

// Upgrade batches are name exactly as the release where they first appear.
// That simple, but seems sufficient for beginning.
function executeUpgradeBatch ($batchid)
{
	$query = array();
	global $dbxlink;
	switch ($batchid)
	{
		case '0.14.5':
			// We can't realiably distinguish between 0.14.4 and 0.14.5, but
			// luckily the SQL statements below can be safely executed for both.


			// This has to be checked once more to be sure IPAddress allocation
			// conventions are correct.
			$query[] = "delete from IPAddress where name = '' and reserved = 'no'";

			// In the 0.14.4 release we had AUTO_INCREMENT low in the dictionary and auth
			// data tables, thus causing new user's data to take primary keys equal to
			// the values of shipped data in future releases. Let's shift user's data
			// up and keep DB consistent.
			$query[] = "alter table Attribute AUTO_INCREMENT = 10000";
			$query[] = "alter table Chapter AUTO_INCREMENT = 10000";
			$query[] = "alter table Dictionary AUTO_INCREMENT = 10000";
			$query[] = "alter table UserAccount AUTO_INCREMENT = 10000";
			$query[] = "update UserAccount set user_id = user_id + 10000 where user_id between 2 and 10000";
			$query[] = "update UserPermission set user_id = user_id + 10000 where user_id between 2 and 10000";
			$query[] = "update Attribute set attr_id = attr_id + 10000 where attr_id between 25 and 10000";
			$query[] = "update AttributeMap set attr_id = attr_id + 10000 where attr_id between 25 and 10000";
			$query[] = "update Chapter set chapter_no = chapter_no + 10000 where chapter_no between 21 and 10000";
			$query[] = "update AttributeMap set chapter_no = chapter_no + 10000 where chapter_no between 21 and 10000";
			break; // --------------------------------------------
		case '0.14.6':
			// This version features new dictionary entries, the correction above should allow us
			// inject them w/o a problem.
			$query[] = "INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (13,25,'FreeBSD 1.x')";
			$query[] = "INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (13,26,'FreeBSD 2.x')";
			$query[] = "INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (13,27,'FreeBSD 3.x')";
			$query[] = "INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (13,28,'FreeBSD 4.x')";
			$query[] = "INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (13,29,'FreeBSD 5.x')";
			$query[] = "INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (13,30,'FreeBSD 6.x')";
			$query[] = "INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (13,31,'RHFC8')";
			$query[] = "INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (13,32,'ALTLinux Master 4.0')";
			$query[] = "INSERT INTO `PortCompat` (`type1`, `type2`) VALUES (20,20)";
			$query[] = "INSERT INTO `PortCompat` (`type1`, `type2`) VALUES (21,21)";
			$query[] = "INSERT INTO `PortCompat` (`type1`, `type2`) VALUES (22,22)";
			$query[] = "INSERT INTO `PortCompat` (`type1`, `type2`) VALUES (23,23)";
			$query[] = "INSERT INTO `PortCompat` (`type1`, `type2`) VALUES (24,24)";
			$query[] = "INSERT INTO `PortCompat` (`type1`, `type2`) VALUES (25,25)";
			$query[] = "INSERT INTO `PortCompat` (`type1`, `type2`) VALUES (26,26)";
			$query[] = "INSERT INTO `PortCompat` (`type1`, `type2`) VALUES (27,27)";
			$query[] = "INSERT INTO `PortCompat` (`type1`, `type2`) VALUES (28,28)";
			$query[] = "INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (2,20,'KVM')";
			$query[] = "INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (2,21,'1000Base-ZX')";
			$query[] = "INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (2,22,'10GBase-ER')";
			$query[] = "INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (2,23,'10GBase-LR')";
			$query[] = "INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (2,24,'10GBase-LRM')";
			$query[] = "INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (2,25,'10GBase-ZR')";
			$query[] = "INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (2,26,'10GBase-LX4')";
			$query[] = "INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (2,27,'10GBase-CX4')";
			$query[] = "INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (2,28,'10GBase-Kx')";
			$query[] = "INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (12,114,'Cisco Catalyst 2970G-24T')";
			$query[] = "INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (12,115,'Cisco Catalyst 2970G-24TS')";
			$query[] = "INSERT INTO `UserPermission` (`user_id`, `page`, `tab`, `access`) VALUES (0,'help','%','yes')";
			// And 0.14.6 is the first release, which features Config table. Let's create
			// and fill it with default values.
			$query[] = "
CREATE TABLE `Config` (
  `varname` char(32) NOT NULL,
  `varvalue` char(64) NOT NULL,
  `vartype` enum('string','uint') NOT NULL default 'string',
  `emptyok` enum('yes','no') NOT NULL default 'no',
  `is_hidden` enum('yes','no') NOT NULL default 'yes',
  `description` text,
  PRIMARY KEY  (`varname`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1
			";
			$query[] = "INSERT INTO `Config` VALUES ('rtwidth_0','9','uint','no','yes','')";
			$query[] = "INSERT INTO `Config` VALUES ('rtwidth_1','21','uint','no','yes','')";
			$query[] = "INSERT INTO `Config` VALUES ('rtwidth_2','9','uint','no','yes','')";
			$query[] = "INSERT INTO `Config` VALUES ('color_F','8fbfbf','string','no','no','HSV: 180-25-75. Free atoms, they are available for allocation to objects.')";
			$query[] = "INSERT INTO `Config` VALUES ('color_A','bfbfbf','string','no','no','HSV: 0-0-75. Absent atoms.')";
			$query[] = "INSERT INTO `Config` VALUES ('color_U','bf8f8f','string','no','no','HSV: 0-25-75. Unusable atoms. Some problems keep them from being free.')";
			$query[] = "INSERT INTO `Config` VALUES ('color_T','408080','string','no','no','HSV: 180-50-50. Taken atoms, object_id should be set for such.')";
			$query[] = "INSERT INTO `Config` VALUES ('color_Th','80ffff','string','no','no','HSV: 180-50-100. Taken atoms with highlight. They are not stored in the database and are only used for highlighting.')";
			$query[] = "INSERT INTO `Config` VALUES ('color_Tw','804040','string','no','no','HSV: 0-50-50. Taken atoms with object problem. This is detected at runtime.')";
			$query[] = "INSERT INTO `Config` VALUES ('color_Thw','ff8080','string','no','no','HSV: 0-50-100. An object can be both current and problematic. We run highlightObject() first and markupObjectProblems() second.')";
			$query[] = "INSERT INTO `Config` VALUES ('default_port_type','11','uint','no','no','Default value for port type selects.')";
			$query[] = "INSERT INTO `Config` VALUES ('MASSCOUNT','15','uint','no','no','Number of lines in object mass-adding form.')";
			$query[] = "INSERT INTO `Config` VALUES ('MAXSELSIZE','30','uint','no','no','Maximum size of a SELECT HTML element.')";
			$query[] = "INSERT INTO `Config` VALUES ('enterprise','MyCompanyName','string','no','no','Fit to your needs.')";
			$query[] = "INSERT INTO `Config` VALUES ('NAMEFUL_OBJTYPES','4,7,8','string','yes','no','These are the object types, which assume a common name to be normally configured. If a name is absent for an object of one of such types, HTML output is corrected to accent this misconfiguration.')";
			$query[] = "INSERT INTO `Config` VALUES ('ROW_SCALE','2','uint','no','no','Row-scope picture scale factor.')";
			$query[] = "INSERT INTO `Config` VALUES ('PORTS_PER_ROW','12','uint','no','yes','Max switch port per one row on the switchvlans dynamic tab.')";
			$query[] = "INSERT INTO `Config` VALUES ('DB_VERSION','0.14.6','string','no','yes','Database version.')";
			break; // --------------------------------------------
		case '0.14.7':
			// IPAddress is hopefully fixed now finally.
			$query[] = "delete from IPAddress where name = '' and reserved != 'yes'";

			// Now rebuild the dictionary into a new table with the same data,
			// but proper indexing. We are going to convert compound index
			// into 1-field one to employ AUTO_INCREMENT properly. This means
			// renumbering lots of records in Dictionary and adjusting records
			// in related tables. After that we can safely swap the tables.
			$query[] = "
CREATE TABLE `Dictionary_0_14_7_new` (
  `chapter_no` int(10) unsigned NOT NULL,
  `dict_key` int(10) unsigned NOT NULL auto_increment,
  `dict_value` char(128) default NULL,
  PRIMARY KEY  (`dict_key`),
  UNIQUE KEY `chap_to_key` (`chapter_no`,`dict_key`),
  UNIQUE KEY `chap_to_val` (`chapter_no`,`dict_value`)
) TYPE=MyISAM AUTO_INCREMENT=50000
";

echo '<pre>';
			// Find all chapter numbers, which will require AttributeValue adjustment.
			$q2 = 'select distinct chapter_no from AttributeMap where chapter_no != 0';
			$r2 = $dbxlink->query ($q2);
			$chaplist = array();
			while ($row = $r2->fetch (PDO::FETCH_NUM))
				$chaplist[] = $row[0];
			$r2->closeCursor();
			unset ($r2);

			$stock = array();
			// Below I list the records, which are known to be the stock
			// dictionary records of 0.14.6 release.
			$stock[1] = array
			(
				1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 14, 15, 16
			);
			$stock[2] = array
			(
				3, 4, 5, 6, 7, 8, 9,
				10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20,
				20, 21, 22, 23, 24, 25, 26, 27, 28
			);
			$stock[11] = array
			(
				1, 3, 4, 5, 6, 7, 8, 9,
				10, 11, 12, 13, 14, 15, 16, 17, 18, 19,
				21, 22, 24, 25, 26, 27, 28, 29,
				30, 31, 32, 33, 34, 35, 36, 37, 38, 39,
				40, 41, 42, 43, 44, 45, 46, 47, 48, 49,
				50, 51, 52, 53, 54, 55, 56, 57, 58, 59,
				60, 61, 62, 63, 64, 65, 66, 67, 68, 69,
				70, 71, 72, 73, 74, 75, 76
			);
			$stock[12] = array
			(
				1, 11, 13, 14, 15, 16, 17, 18, 19, 20, 26, 29,
				31, 32, 33, 34, 35, 36, 37, 38, 39,
				40, 41, 42, 43, 44, 45, 46, 47, 48, 49,
				50, 51,	52, 53, 54, 55, 56, 57, 58, 59,
				60, 61, 62, 63, 64, 65, 66, 67, 68, 69,
				70, 71, 72, 73, 74, 75, 76, 77, 78, 79,
				80, 81, 82, 83, 84, 85, 86, 87, 88, 89,
				90, 91, 92, 93, 94, 95, 96, 97, 98, 99,
				100, 101, 102, 103, 104, 105, 106, 107, 108, 109,
				110, 111, 112, 113, 114, 115
			);
			$stock[13] = array
			(
				1, 2, 3, 4, 5, 6, 7, 8, 9,
				10, 11, 12, 13, 14, 15, 16, 17, 18, 19,
				20, 21, 22, 23, 24, 25, 26, 27, 28, 29,
				30, 31, 32
			);
			$stock[14] = array
			(
				1, 2, 9, 11, 13, 15, 19, 20, 21, 22
			);
			$stock[16] = array
			(
				1, 2, 3, 4, 5, 6, 7, 8
			);
			$stock[17] = array
			(
				1, 2, 3, 4, 5, 6, 7, 8, 9,
				10, 11, 12, 13, 14, 15, 16, 17, 18, 19,
				20, 21, 22, 23, 24, 25, 26, 27, 28, 29,
				30, 31, 32, 33, 34, 35, 36, 37, 38, 39,
				40, 41, 42, 43, 44, 45, 46, 47, 48, 49,
				50
			);
			$stock[18] = array
			(
				1, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14
			);
			$stock[19] = array
			(
				1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11
			);
			$stock[20] = array
			(
				1, 2
			);

			// Load dictionary and transform into two tree structures for
			// stock and user record sets.
			$dict = array();
			$q3 = 'select chapter_no, dict_key, dict_value from Dictionary order by chapter_no, dict_key';
			$r3 = $dbxlink->query ($q3);

			while ($row = $r3->fetch (PDO::FETCH_ASSOC))
			{
				$tree = 'user';
				$dict_key = $row['dict_key'];
				$chapter_no = $row['chapter_no'];
				switch ($chapter_no)
				{
					case 1: // RackObjectType
					case 2: // PortType
					case 11: // server models
					case 12: // network switch models
					case 13: // server OS type
					case 14: // network switch OS type
					case 16: // router OS type
					case 17: // router models
					case 18: // disk array models
					case 19: // tape library models
					case 20: // Protocols
						if (in_array ($dict_key, $stock[$chapter_no]))
							$tree = 'stock';
						break;
				}
				$dict[$tree][$chapter_no][$dict_key] = array ('value' => $row['dict_value']);
			}
			$r3->closeCursor();
			unset ($r3);


			// Now we store stock dataset first, bump up key value and store
			// user's data. After that we will know the new dict_key for all
			// records.
			// The result of both datasets processing is saved in $new_dict.
			// Save on calling LAST_ISERT_ID() each time by keeping own key.
			$newkey = 1;
			$new_dict = array();
			foreach ($dict['stock'] as $chapter_no => $words)
			{
				$new_dict[$chapter_no] = array();
				foreach ($words as $dict_key => $entry)
				{
					$query[] = "insert into Dictionary_0_14_7_new (chapter_no, dict_key, dict_value) " .
						"values (${chapter_no}, ${newkey}, '${entry['value']}')";
					$new_dict[$chapter_no][$dict_key] = $entry;
					$new_dict[$chapter_no][$dict_key]['newkey'] = $newkey;
					$newkey++;
				}
			}
			$newkey = 50000;
			foreach ($dict['user'] as $chapter_no => $words)
			{
				// Some chapters may appear on the user dataset only.
				if (!isset ($new_dict[$chapter_no]))
					$new_dict[$chapter_no] = array();
				foreach ($words as $dict_key => $entry)
				{
					$query[] = "insert into Dictionary_0_14_7_new " .
						"values (${chapter_no}, ${newkey}, '${entry['value']}')";
					$new_dict[$chapter_no][$dict_key] = $entry;
					$new_dict[$chapter_no][$dict_key]['newkey'] = $newkey;
					$newkey++;
				}
			}
			// The new table should now have adequate AUTO_INCREMENT w/o our care.
			// Install the new data.
			$query[] = 'drop table Dictionary';
			$query[] = 'alter table Dictionary_0_14_7_new rename to Dictionary';

			// Now we iterate over the joint dataset, picking some chapters and
			// performing additional processing:
			// 1 (RackObjectType) --- adjust RackObject and regenerate AttributeMap
			// 2 (PortType) --- adjust Port and regenerate PortCompat (at a latter point)
			// 3 (RackRow) --- adjust Rack
			// 20 (Protocols) --- adjust PortForwarding
			// All other chapters listed in $chaplist --- adjust AttributeValue
			
			$query[] = "delete from AttributeMap";
			foreach ($new_dict as $chapter_no => $words)
			{
				foreach ($words as $oldkey => $data)
				{
					$value = $data['value'];
					$newkey = $data['newkey'];
					// Even if the key doesn't change, go on to have
					// AttributeMap regenerated completely.
					if ($chapter_no == 1)
					{
						$q4 = "select id from RackObject where objtype_id = ${oldkey}";
						$r4 = $dbxlink->query ($q4);
						while ($row = $r4->fetch (PDO::FETCH_ASSOC))
							$query[] = "update RackObject set objtype_id = ${newkey} where id = ${row['id']} limit 1";
						$r4->closeCursor();
						unset ($r4);

						$q5 = "select attr_id, chapter_no from AttributeMap where objtype_id = ${oldkey}";
						$r5 = $dbxlink->query ($q5);
						while ($row = $r5->fetch (PDO::FETCH_ASSOC))
							$query[] = "insert into AttributeMap (objtype_id, attr_id, chapter_no) values (${newkey}, ${row['attr_id']}, ${row['chapter_no']})";
						$r5->closeCursor();
						unset ($r5);
					}
					elseif ($chapter_no == 2)
					{
						$q46 = "select id from Port where type = ${oldkey}";
						$r46 = $dbxlink->query ($q46);
						if ($r46 == NULL)
							echo 'ERROR';
						while ($row = $r46->fetch (PDO::FETCH_ASSOC))
							$query[] = "update Port set type = ${newkey} where id = ${row['id']} limit 1";
						$r46->closeCursor();
						unset ($r46);
					}
					elseif ($chapter_no == 3)
					{
						$q7 = "select id from Rack where row_id = ${oldkey}";
						$r7 = $dbxlink->query ($q7);
						while ($row = $r7->fetch (PDO::FETCH_ASSOC))
							$query[] = "update Rack set row_id = ${newkey} where id = ${row['id']} limit 1";
						$r7->closeCursor();
						unset ($r7);
					}
					elseif ($chapter_no == 20)
					{
						$q8 = "select object_id, localip, localport, remoteip, remoteport from PortForwarding where proto = ${oldkey}";
						$r8 = $dbxlink->query ($q8);
						while ($row = $r8->fetch (PDO::FETCH_ASSOC))
							$query[] = "update PortForwarding set proto = ${newkey} where " .
							"object_id = ${row['object_id']} and localip = ${row['localip']} and " .
							"localport = ${row['localport']} and remoteip = ${row['remoteip']} and " .
							"remoteport = ${row['remoteport']} and proto = ${oldkey} limit 1";
						$r8->closeCursor();
						unset ($r8);
					}
					elseif (in_array ($chapter_no, $chaplist))
					{
						$q81 = "select object_id, AttributeValue.attr_id from " .
						"AttributeValue natural join Attribute natural join AttributeMap " .
						"inner join RackObject on RackObject.id = object_id and RackObject.objtype_id = AttributeMap.objtype_id " .
						"where attr_type = 'dict' and chapter_no = ${chapter_no} and uint_value = ${oldkey}";
						$r81 = $dbxlink->query ($q81);
						while ($row = $r81->fetch (PDO::FETCH_ASSOC))
							$query[] = "update AttributeValue set uint_value = ${newkey} " .
							"where object_id = ${row['object_id']} and attr_id = ${row['attr_id']}";
						$r81->closeCursor();
						unset ($r81);
					}
				}
			}
			// Now it's possible to schedule PortCompat regeneration.
			// Convert the fields to unsigned on occasion.
			$query[] = 'drop table PortCompat';
			$query[] = 'create table PortCompat (type1 int(10) unsigned NOT NULL, type2 int(10) unsigned NOT NULL)';
			$q9 = "select type1, type2 from PortCompat";
			$r9 = $dbxlink->query ($q9);
			while ($row = $r9->fetch (PDO::FETCH_ASSOC))
			{
				$new_type1 = $new_dict[2][$row['type1']]['newkey'];
				$new_type2 = $new_dict[2][$row['type2']]['newkey'];
				$query[] = "insert into PortCompat (type1, type2) values (${new_type1}, ${new_type2})";
			}
			$r9->closeCursor();
			unset ($r9);
echo '</pre>';

			// Give the configuration some finish
			$query[] = "update Config set is_hidden = 'yes' where varname = 'color_F'";
			$query[] = "update Config set is_hidden = 'yes' where varname = 'color_A'";
			$query[] = "update Config set is_hidden = 'yes' where varname = 'color_U'";
			$query[] = "update Config set is_hidden = 'yes' where varname = 'color_T'";
			$query[] = "update Config set is_hidden = 'yes' where varname = 'color_Th'";
			$query[] = "update Config set is_hidden = 'yes' where varname = 'color_Tw'";
			$query[] = "update Config set is_hidden = 'yes' where varname = 'color_Thw'";
			$query[] = "update Config set description = 'Default port type' where varname = 'default_port_type'";
			$query[] = "update Config set description = 'Picture scale for rack row display' where varname = 'ROW_SCALE'";
			$query[] = "update Config set description = 'Organization name' where varname = 'enterprise'";
			$query[] = "update Config set description = 'Expect common name configured for the following object types' where varname = 'NAMEFUL_OBJTYPES'";
			$query[] = "update Config set description = '&lt;SELECT&gt; lists height' where varname = 'MAXSELSIZE'";
			$query[] = "update Config set description = '&quot;Fast&quot; form is this many records tall' where varname = 'MASSCOUNT'";
			$query[] = "update Config set is_hidden = 'no', description = 'Ports per row in VLANs tab' where varname = 'PORTS_PER_ROW'";
			$query[] = "INSERT INTO `Config` VALUES ('IPV4_ADDRS_PER_PAGE','256','uint','no','no','IPv4 addresses per page')";
			$query[] = "INSERT INTO `Config` VALUES ('DEFAULT_RACK_HEIGHT','42','uint','yes','no','Default rack height')";
			// After Dictionary transformation we ought to list 337 stock records in DB. Add more.
			$new_words = array();
			$new_words[338] = array (12 => 'Dell PowerConnect 2216');
			$new_words[] = array (12 => 'Dell PowerConnect 2224');
			$new_words[] = array (12 => 'Dell PowerConnect 2324');
			$new_words[] = array (12 => 'Dell PowerConnect 2708');
			$new_words[] = array (12 => 'Dell PowerConnect 2716');
			$new_words[] = array (12 => 'Dell PowerConnect 2724');
			$new_words[] = array (12 => 'Dell PowerConnect 2748');
			$new_words[] = array (12 => 'Dell PowerConnect 3424');
			$new_words[] = array (12 => 'Dell PowerConnect 3424P');
			$new_words[] = array (12 => 'Dell PowerConnect 3448');
			$new_words[] = array (12 => 'Dell PowerConnect 3448P');
			$new_words[] = array (12 => 'Dell PowerConnect 5324');
			$new_words[] = array (12 => 'Dell PowerConnect 6224');
			$new_words[] = array (12 => 'Dell PowerConnect 6224P');
			$new_words[] = array (12 => 'Dell PowerConnect 6224F');
			$new_words[] = array (12 => 'Dell PowerConnect 6248');
			$new_words[] = array (12 => 'Dell PowerConnect 6248P');
			$new_words[] = array (11 => 'Dell PowerEdge 6850');
			$new_words[] = array (11 => 'Dell PowerEdge 6950');
			$new_words[] = array (11 => 'Dell PowerEdge R900');
			$new_words[] = array (11 => 'Dell PowerEdge 4400');
			$new_words[] = array (11 => 'Dell PowerEdge 2650');
			$new_words[] = array (11 => 'Dell PowerEdge 2550');
			$new_words[] = array (11 => 'Dell PowerEdge 750');
			$new_words[] = array (11 => 'Dell PowerEdge 2450');
			$new_words[] = array (11 => 'Dell PowerEdge 850');
			$new_words[] = array (11 => 'Dell PowerEdge 1850');
			$new_words[] = array (11 => 'Dell PowerEdge 860');
			$new_words[] = array (11 => 'Dell PowerEdge 2900');
			$new_words[] = array (11 => 'Dell PowerEdge 2970');
			$new_words[] = array (11 => 'Dell PowerEdge SC1435');
			$new_words[] = array (12 => 'Cisco Catalyst 6509');
			$new_words[] = array (12 => 'Cisco ME 6524GS-8S');
			$new_words[] = array (12 => 'Cisco ME 6524GT-8S');
			$new_words[] = array (12 => 'Cisco Catalyst 4503-E');
			$new_words[] = array (12 => 'Cisco Catalyst 4506-E');
			$new_words[] = array (12 => 'Cisco Catalyst 4507R-E');
			$new_words[] = array (12 => 'Cisco Catalyst 4510R-E');
			$new_words[] = array (12 => 'Cisco Catalyst 3750-24TE-M');
			$new_words[] = array (12 => 'Cisco Catalyst 4948-10GE');
			$new_words[] = array (12 => 'Cisco ME 4924-10GE');
			$new_words[] = array (12 => 'Cisco Catalyst 2960-24');
			$new_words[] = array (12 => 'Cisco Catalyst 2950-24');
			$new_words[] = array (12 => 'Cisco Catalyst 2950-12');
			$new_words[] = array (12 => 'Cisco Catalyst 2950C-24');
			$new_words[] = array (12 => 'Cisco Catalyst 2950G-24-DC');
			$new_words[] = array (12 => 'Cisco Catalyst 2950SX-48');
			$new_words[] = array (12 => 'Cisco Catalyst 2950SX-24');
			$new_words[] = array (12 => 'Cisco Catalyst 2950T-24');
			$new_words[] = array (12 => 'Cisco Catalyst 2950T-48');
			$new_words[] = array (12 => 'Cisco Catalyst 2950G-12');
			$new_words[] = array (12 => 'Cisco Catalyst 2950G-24');
			$new_words[] = array (12 => 'Cisco Catalyst 2950G-48');
			$new_words[] = array (12 => 'Cisco Catalyst 3508G XL');
			$new_words[] = array (12 => 'Cisco Catalyst 3512 XL');
			$new_words[] = array (12 => 'Cisco Catalyst 3524 XL');
			$new_words[] = array (12 => 'Cisco Catalyst 3524 PWR XL');
			$new_words[] = array (12 => 'Cisco Catalyst 3548 XL');
			$new_words[] = array (12 => 'Cisco ME 2400-24TS-A');
			$new_words[] = array (12 => 'Cisco ME 2400-24TS-D');
			$new_words[] = array (12 => 'Cisco Catalyst 3550-12T');
			$new_words[] = array (12 => 'Cisco Catalyst 3550-12G');
			$new_words[] = array (12 => 'Cisco Catalyst 3550-24');
			$new_words[] = array (12 => 'Cisco Catalyst 3550-24 FX');
			$new_words[] = array (12 => 'Cisco Catalyst 3550-24 DC');
			$new_words[] = array (12 => 'Cisco Catalyst 3550-24 PWR');
			$new_words[] = array (12 => 'Cisco Catalyst 3550-48');
			$new_words[] = array (12 => 'Cisco ME 3400G-12CS-A');
			$new_words[] = array (12 => 'Cisco ME 3400G-12CS-D');
			$new_words[] = array (12 => 'Cisco ME 3400G-2CS-A');
			$new_words[] = array (12 => 'Cisco ME 3400-24TS-A');
			$new_words[] = array (12 => 'Cisco ME 3400-24TS-D');
			$new_words[] = array (12 => 'Cisco ME 3400-24FS-A');
			$new_words[] = array (12 => 'Foundry FastIron GS 624XGP');
			$new_words[] = array (12 => 'Foundry FastIron GS 624XGP-POE');
			$new_words[] = array (12 => 'Foundry FastIron LS 624');
			$new_words[] = array (12 => 'Foundry FastIron LS 648');
			$new_words[] = array (12 => 'Foundry NetIron M2404F');
			$new_words[] = array (12 => 'Foundry NetIron M2404C');
			$new_words[] = array (17 => 'Foundry BigIron RX-32');
			$new_words[] = array (13 => 'Debian 2.0 (hamm)');
			$new_words[] = array (13 => 'Debian 2.1 (slink)');
			$new_words[] = array (13 => 'Debian 2.2 (potato)');
			$new_words[] = array (13 => 'Debian 4.0 (etch)');
			$new_words[] = array (13 => 'ALTLinux Server 4.0');
			$new_words[] = array (13 => 'ALTLinux Sisyphus');
			$new_words[] = array (13 => 'openSUSE 10.0');
			$new_words[] = array (13 => 'openSUSE 10.1');
			$new_words[] = array (13 => 'openSUSE 10.2');
			$new_words[] = array (13 => 'openSUSE 10.3');
			$new_words[] = array (13 => 'Ubuntu 4.10');
			$new_words[] = array (13 => 'Ubuntu 5.04');
			$new_words[] = array (13 => 'Ubuntu 5.10');
			$new_words[] = array (13 => 'Ubuntu 6.06 LTS');
			$new_words[] = array (13 => 'Ubuntu 6.10');
			$new_words[] = array (13 => 'Ubuntu 7.04');
			$new_words[] = array (13 => 'Ubuntu 7.10');
			$new_words[] = array (13 => 'Ubuntu 8.04 LTS');
			$new_words[] = array (13 => 'RHEL5');
			$new_words[] = array (18 => 'Dell PowerVault 210S');
			$new_words[] = array (18 => 'Dell PowerVault 221S');
			$new_words[] = array (2 => 'dry contact');
			$new_words[] = array (2 => 'unknown');
			// Two above records ought to take keys 439 and 440.
			$query[] = "INSERT INTO `PortCompat` (`type1`, `type2`) VALUES (439,439)";
			$new_words[] = array (13 => 'CentOS-2');
			$new_words[] = array (13 => 'CentOS-3');
			$new_words[] = array (13 => 'CentOS-4');
			$new_words[] = array (13 => 'CentOS-5');
			foreach ($new_words as $dict_key => $tmp)
				foreach ($tmp as $chapter_no => $dict_value)
					$query[] = 'INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) ' .
						"VALUES (${chapter_no}, ${dict_key}, '${dict_value}')";
			// Resetting to defaults is worse, than remapping, but better than
			// leaving messed values.
			$query[] = "update Config set varvalue = '24' where varname = 'default_port_type' limit 1";
			// We are done.
			$query[] = "update Config set varvalue = '0.14.7' where varname = 'DB_VERSION'";
			break; // --------------------------------------------
		case '0.14.8':
			$query[] = "INSERT INTO `Config` (varname, varvalue, vartype, emptyok, is_hidden, description) VALUES ('REQUIRE_ASSET_TAG_FOR','4,7,8','string','yes','no','Require asset tag for the following object types')";
			$query[] = "alter table Port modify column id int(10) unsigned NOT NULL auto_increment";
			$query[] = "alter table Port modify column object_id int(10) unsigned NOT NULL";
			$query[] = "alter table Port modify column type int(10) unsigned NOT NULL";
			$query[] = "alter table Link modify column porta int(10) unsigned";
			$query[] = "alter table Link modify column portb int(10) unsigned";
			$query[] = "alter table PortForwarding modify column object_id int(10) unsigned not null";
			$query[] = "alter table PortForwarding modify column localport smallint(5) unsigned not null";
			$query[] = "alter table PortForwarding modify column remoteport smallint(5) unsigned not null";
			$query[] = "alter table IPBonds modify column object_id int(10) unsigned not null";
			$query[] = "alter table IPRanges modify column id int(10) unsigned not null";
			$query[] = "alter table IPRanges modify column mask int(10) unsigned not null";
			$query[] = "alter table Port add index `type` (type)";
			$query[] = "alter table PortCompat add index `type1` (type1)";
			$query[] = "alter table PortCompat add index `type2` (type2)";
			$query[] = "update Dictionary set dict_value = 'Debian 3.0 (woody)' where dict_key = 234";
			$query[] = "update Dictionary set dict_value = 'Debian 3.1 (sarge)' where dict_key = 235";
			$query[] = "update Dictionary set dict_value = 'Foundry BigIron 15000' where dict_key = 311";
			$query[] = "update Dictionary set dict_value = 'RHF7' where dict_key = 232";
			$query[] = "update Dictionary set dict_value = 'RHF8' where dict_key = 242";
			$query[] = "INSERT INTO `Attribute` (`attr_id`, `attr_type`, `attr_name`) VALUES (25,'string','UUID');";
			$query[] = "INSERT INTO `AttributeMap` (`objtype_id`, `attr_id`, `chapter_no`) VALUES (4,25,0);";
			$query[] = "update Dictionary set dict_value = '[[Cisco Catalyst 2970G-24T | http://www.cisco.com/en/US/products/hw/switches/ps5206/ps5313/index.html]]' where dict_key = 210";
			$query[] = "update Dictionary set dict_value = '[[Cisco Catalyst 2970G-24TS | http://www.cisco.com/en/US/products/hw/switches/ps5206/ps5437/index.html]]' where dict_key = 211";
			$query[] = "update Config set varvalue = '0.14.8' where varname = 'DB_VERSION'";
			break; // --------------------------------------------
		case '0.14.9':
			$query[] = "alter table IPRanges modify column id int(10) unsigned not null auto_increment";
			$query[] = "alter table Rack modify column height tinyint(3) unsigned not null default '42'";
			$query[] = "alter table Rack add column thumb_data blob after comment";
			$query[] = "
CREATE TABLE `IPLoadBalancer` (
  `object_id` int(10) unsigned default NULL,
  `rspool_id` int(10) unsigned default NULL,
  `vs_id` int(10) unsigned default NULL,
  `vsconfig` text,
  `rsconfig` text,
  UNIQUE KEY `LB-VS` (`object_id`,`vs_id`)
) ENGINE=MyISAM";
			$query[] = "
CREATE TABLE `IPRSPool` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` char(255) default NULL,
  `vsconfig` text,
  `rsconfig` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM";
			$query[] = "
CREATE TABLE `IPRealServer` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `inservice` enum('yes','no') NOT NULL default 'no',
  `rsip` int(10) unsigned default NULL,
  `rsport` smallint(5) unsigned default NULL,
  `rspool_id` int(10) unsigned default NULL,
  `rsconfig` text,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `pool-endpoint` (`rspool_id`,`rsip`,`rsport`)
) ENGINE=MyISAM";
			$query[] = "
CREATE TABLE `IPVirtualService` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `vip` int(10) unsigned default NULL,
  `vport` smallint(5) unsigned default NULL,
  `proto` enum('TCP','UDP') NOT NULL default 'TCP',
  `name` char(255) default NULL,
  `vsconfig` text,
  `rsconfig` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM";
			$query[] = "INSERT INTO `Config` VALUES ('DEFAULT_SLB_VS_PORT','','uint','yes','no','Default port of SLB virtual service')";
			$query[] = "INSERT INTO `Config` VALUES ('DEFAULT_SLB_RS_PORT','','uint','yes','no','Default port of SLB real server')";
			$query[] = "INSERT INTO `Config` VALUES ('IPV4_PERFORMERS','1,4,7,8,12,14','string','yes','no','IPv4-capable object types')";
			$query[] = "INSERT INTO `Config` VALUES ('NATV4_PERFORMERS','4,7,8','string','yes','no','NATv4-capable object types')";
			$query[] = "INSERT INTO `Config` VALUES ('USER_AUTH_SRC','database','string','no','no','User authentication source')";
			$query[] = "alter table RackSpace drop column problem_id";
			$query[] = "update Config set varvalue = '0.14.9' where varname = 'DB_VERSION'";
			break; // --------------------------------------------
		case '0.14.10':
			$query[] = "INSERT INTO `Config` (varname, varvalue, vartype, emptyok, is_hidden, description) VALUES ('DETECT_URLS','no','string','yes','no','Detect URLs in text fields')";
			$query[] = "alter table RackHistory modify column height tinyint(3) unsigned";
			$query[] = "alter table RackHistory add column thumb_data blob after comment";
			$query[] = "INSERT INTO `Config` VALUES ('RACK_PRESELECT_THRESHOLD','1','uint','no','no','Rack pre-selection threshold')";
			$query[] = "update Config set varvalue = '0.14.10' where varname = 'DB_VERSION'";
			break; // --------------------------------------------
		case '0.14.11':
			$new_words = array();
			$new_words[445] = array (1 => 'KVM switch');
			$new_words[] = array (2 => 'KVM (console)');
			$new_words[] = array (1 => 'multiplexer');
			$query[] = "update Dictionary set dict_value = 'network switch' where dict_key = 8";
			$query[] = "update Dictionary set dict_value = 'KVM (host)' where dict_key = 33";
			$query[] = "delete from PortCompat where type1 = 33 and type2 = 33";
			$query[] = "insert into PortCompat (type1, type2) values (33, 446)";
			$query[] = "insert into PortCompat (type1, type2) values (446, 33)";
			$query[] = "insert into Chapter (chapter_no, sticky, chapter_name) values (21, 'no', 'KVM switch models')";
			$query[] = "insert into Chapter (chapter_no, sticky, chapter_name) values (22, 'no', 'multiplexer models')";
			$query[] = "update Chapter set chapter_name = 'network switch models' where chapter_no = 12";
			$new_words[] = array (21 => '[[Avocent DSR1021 | http://www.avocent.com/WorkArea/linkit.aspx?LinkIdentifier=id&ItemID=2418]]');
			$new_words[] = array (21 => '[[Avocent DSR1022 | http://www.avocent.com/WorkArea/linkit.aspx?LinkIdentifier=id&ItemID=2498]]');
			$new_words[] = array (21 => '[[Avocent DSR1024 | http://www.avocent.com/WorkArea/linkit.aspx?LinkIdentifier=id&ItemID=2588]]');
			$new_words[] = array (21 => '[[Avocent DSR1031 | http://www.avocent.com/WorkArea/linkit.aspx?LinkIdentifier=id&ItemID=2804]]');
			$new_words[] = array (21 => '[[Avocent DSR1020 | http://www.avocent.com/WorkArea/linkit.aspx?LinkIdentifier=id&ItemID=2340]]');
			$new_words[] = array (21 => '[[Avocent DSR2020 | http://www.avocent.com/WorkArea/linkit.aspx?LinkIdentifier=id&ItemID=2884]]');
			$new_words[] = array (21 => '[[Avocent DSR4020 | http://www.avocent.com/WorkArea/linkit.aspx?LinkIdentifier=id&ItemID=3100]]');
			$new_words[] = array (21 => '[[Avocent DSR8020 | http://www.avocent.com/WorkArea/linkit.aspx?LinkIdentifier=id&ItemID=3358]]');
			$new_words[] = array (21 => '[[Avocent DSR1030 | http://www.avocent.com/WorkArea/linkit.aspx?LinkIdentifier=id&ItemID=2726]]');
			$new_words[] = array (21 => '[[Avocent DSR2030 | http://www.avocent.com/WorkArea/linkit.aspx?LinkIdentifier=id&ItemID=2988]]');
			$new_words[] = array (21 => '[[Avocent DSR2035 | http://www.avocent.com/WorkArea/linkit.aspx?LinkIdentifier=id&ItemID=3050]]');
			$new_words[] = array (21 => '[[Avocent DSR4030 | http://www.avocent.com/WorkArea/linkit.aspx?LinkIdentifier=id&ItemID=3196]]');
			$new_words[] = array (21 => '[[Avocent DSR8030 | http://www.avocent.com/WorkArea/linkit.aspx?LinkIdentifier=id&ItemID=3472]]');
			$new_words[] = array (21 => '[[Avocent DSR8035 | http://www.avocent.com/WorkArea/linkit.aspx?LinkIdentifier=id&ItemID=3580]]');
			$new_words[] = array (21 => '[[Avocent AutoView 1415 | http://www.avocent.com/WorkArea/linkit.aspx?LinkIdentifier=id&ItemID=1612]]');
			$new_words[] = array (21 => '[[Avocent AutoView 1515 | http://www.avocent.com/WorkArea/linkit.aspx?LinkIdentifier=id&ItemID=1736]]');
			$new_words[] = array (21 => '[[Avocent AutoView 2015 | http://www.avocent.com/WorkArea/linkit.aspx?LinkIdentifier=id&ItemID=1930]]');
			$new_words[] = array (21 => '[[Avocent AutoView 2020 | http://www.avocent.com/WorkArea/linkit.aspx?LinkIdentifier=id&ItemID=2012]]');
			$new_words[] = array (21 => '[[Avocent AutoView 2030 | http://www.avocent.com/WorkArea/linkit.aspx?LinkIdentifier=id&ItemID=2114]]');
			$new_words[] = array (21 => '[[Avocent AutoView 3100 | http://www.avocent.com/WorkArea/linkit.aspx?LinkIdentifier=id&ItemID=2222]]');
			$new_words[] = array (21 => '[[Avocent AutoView 3200 | http://www.avocent.com/WorkArea/linkit.aspx?LinkIdentifier=id&ItemID=2266]]');
			$new_words[] = array (21 => '[[Avocent SwitchView 1000 4-port | http://www.avocent.com/WorkArea/linkit.aspx?LinkIdentifier=id&ItemID=4016]]');
			$new_words[] = array (21 => '[[Avocent SwitchView 1000 8-port | http://www.avocent.com/WorkArea/linkit.aspx?LinkIdentifier=id&ItemID=4094]]');
			$new_words[] = array (21 => '[[Avocent SwitchView 1000 16-port | http://www.avocent.com/WorkArea/linkit.aspx?LinkIdentifier=id&ItemID=3934]]');
			$new_words[] = array (22 => '[[Cronyx FMUX/S-4E1 | http://www.cronyx.ru/hardware/fmux-ring.html]]');
			$new_words[] = array (22 => '[[Cronyx FMUX/S-4E1/ETS | http://www.cronyx.ru/hardware/fmux-ring.html]]');
			$new_words[] = array (22 => '[[Cronyx FMUX/S-4E1/M | http://www.cronyx.ru/hardware/fmux-ring.html]]');
			$new_words[] = array (22 => '[[Cronyx FMUX/S-8E1 | http://www.cronyx.ru/hardware/fmux-ring.html]]');
			$new_words[] = array (22 => '[[Cronyx FMUX/S-8E1/ETS | http://www.cronyx.ru/hardware/fmux-ring.html]]');
			$new_words[] = array (22 => '[[Cronyx FMUX/S-8E1/M | http://www.cronyx.ru/hardware/fmux-ring.html]]');
			$new_words[] = array (22 => '[[Cronyx FMUX/S-16E1 | http://www.cronyx.ru/hardware/fmux-ring.html]]');
			$new_words[] = array (22 => '[[Cronyx FMUX/S-16E1/ETS | http://www.cronyx.ru/hardware/fmux-ring.html]]');
			$new_words[] = array (22 => '[[Cronyx FMUX/S-16E1/M | http://www.cronyx.ru/hardware/fmux-ring.html]]');
			$new_words[] = array (22 => '[[Cronyx E1-XL/S | http://www.cronyx.ru/hardware/e1xl-s.html]]');
			$new_words[] = array (22 => '[[Cronyx E1-DXC/S | http://www.cronyx.ru/hardware/e1dxc-s.html]]');
			$new_words[] = array (22 => '[[Cronyx FMUX-4-E2 | http://www.cronyx.ru/hardware/fmux4-e2.html]]');
			$new_words[] = array (22 => '[[Cronyx FMUX-4-E3 | http://www.cronyx.ru/hardware/fmux16-e3.html]]');
			$new_words[] = array (22 => '[[Cronyx FMUX/SAT | http://www.cronyx.ru/hardware/fmux-sat.html]]');
			$new_words[] = array (22 => '[[Cronyx E1-XL/S-IP | http://www.cronyx.ru/hardware/e1xl-ip.html]]');
			$new_words[] = array (17 => '[[RAD FCD-IPM | http://www.rad.com/Article/0,6583,36426-E1_T1_or_Fractional_E1_T1_Modular_Access_Device_with_Integrated_Router,00.html]]');
			$new_words[] = array (22 => '[[RAD FCD-E1M | http://www.rad.com/Article/0,6583,36723-E1_T1_Modular_Access_Multiplexer,00.html]]');
			$new_words[] = array (22 => '[[RAD FCD-T1M | http://www.rad.com/Article/0,6583,36723-E1_T1_Modular_Access_Multiplexer,00.html]]');
			$new_words[] = array (22 => '[[RAD FCD-155E | http://www.rad.com/Article/0,6583,36276-Ethernet_over_SDH_SONET_ADM,00.html]]');
			$query[] = "insert into AttributeMap (objtype_id, attr_id, chapter_no) values (445, 1, 0)";
			$query[] = "insert into AttributeMap (objtype_id, attr_id, chapter_no) values (445, 2, 21)";
			$query[] = "insert into AttributeMap (objtype_id, attr_id, chapter_no) values (445, 3, 0)";
			$query[] = "insert into AttributeMap (objtype_id, attr_id, chapter_no) values (445, 5, 0)";
			$query[] = "insert into AttributeMap (objtype_id, attr_id, chapter_no) values (445, 14, 0)";
			$query[] = "insert into AttributeMap (objtype_id, attr_id, chapter_no) values (445, 22, 0)";
			$query[] = "insert into AttributeMap (objtype_id, attr_id, chapter_no) values (447, 1, 0)";
			$query[] = "insert into AttributeMap (objtype_id, attr_id, chapter_no) values (447, 2, 22)";
			$query[] = "insert into AttributeMap (objtype_id, attr_id, chapter_no) values (447, 3, 0)";
			$query[] = "insert into AttributeMap (objtype_id, attr_id, chapter_no) values (447, 5, 0)";
			$query[] = "insert into AttributeMap (objtype_id, attr_id, chapter_no) values (447, 14, 0)";
			$query[] = "insert into AttributeMap (objtype_id, attr_id, chapter_no) values (447, 22, 0)";
			foreach ($new_words as $dict_key => $tmp)
				foreach ($tmp as $chapter_no => $dict_value)
					$query[] = 'INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) ' .
						"VALUES (${chapter_no}, ${dict_key}, '${dict_value}')";
			$query[] = "update Rack set thumb_data = NULL";
			$query[] = "update Config set varvalue = '0.14.11' where varname = 'DB_VERSION'";
			break; // --------------------------------------------
		case '0.14.12':
			$query[] = "INSERT INTO `Config` VALUES ('DEFAULT_IPV4_RS_INSERVICE','no','string','no','no','Inservice status for new SLB real servers')";
			$query[] = "INSERT INTO `Config` VALUES ('AUTOPORTS_CONFIG','4 = 1*33*kvm + 2*24*eth%u;15 = 1*446*kvm','string','yes','no','AutoPorts configuration')";
			$query[] = "INSERT INTO `Config` VALUES ('DEFAULT_OBJECT_TYPE','4','uint','yes','no','Default object type for new objects')";
			$query[] = "insert into Chapter (chapter_no, sticky, chapter_name) values (23, 'no', 'console models')";
			$query[] = "INSERT INTO `AttributeMap` (`objtype_id`, `attr_id`, `chapter_no`) VALUES (15, 2, 23)";
			$query[] = "alter table Dictionary modify column dict_value char(255)";
			$new_words[491] = array (21 => '[[Aten CS78 | http://www.aten.com/products/productItem.php?pcid=20070130111936003&psid=20070130133658002&pid=20070319151852001&layerid=subClass2]]');
			$new_words[] = array (21 => '[[Aten ACS1208A | http://www.aten.com/products/productItem.php?pcid=20070130111936003&psid=20070130133658002&pid=20050224111025006&layerid=subClass2]]');
			$new_words[] = array (21 => '[[Aten ACS1216A | http://www.aten.com/products/productItem.php?pcid=20070130111936003&psid=20070130133658002&pid=20050224111953008&layerid=subClass2]]');
			$new_words[] = array (21 => '[[Aten CS1754 | http://www.aten.com/products/productItem.php?pcid=20070130111936003&psid=20070130133658002&pid=20050217161051008&layerid=subClass2]]');
			$new_words[] = array (21 => '[[Aten CS1758 | http://www.aten.com/products/productItem.php?pcid=20070130111936003&psid=20070130133658002&pid=20050224093143008&layerid=subClass2]]');
			$new_words[] = array (21 => '[[Aten CS9134 | http://www.aten.com/products/productItem.php?pcid=2005010513171002&psid=20070130133658002&pid=20050217172845005&layerid=subClass2]]');
			$new_words[] = array (21 => '[[Aten CS9138 | http://www.aten.com/products/productItem.php?pcid=20070130111936003&psid=20070130133658002&pid=20050224094519006&layerid=subClass2]]');
			$new_words[] = array (21 => '[[Aten CS1708 | http://www.aten.com/products/productItem.php?pcid=20070130111936003&psid=20070130133658002&pid=2005022410563008&layerid=subClass2]]');
			$new_words[] = array (21 => '[[Aten CS1716 | http://www.aten.com/products/productItem.php?pcid=20070130111936003&psid=20070130133658002&pid=20050224110022008&layerid=subClass2]]');
			$new_words[] = array (21 => '[[Aten CS1004 | http://www.aten.com/products/productItem.php?pcid=20070130111936003&psid=20070130133658002&pid=20050224100546008&layerid=subClass2]]');
			$new_words[] = array (21 => '[[Aten CS228 | http://www.aten.com/products/productItem.php?pcid=20070130111936003&psid=20070130133658002&pid=20050224114323008&layerid=subClass2]]');
			$new_words[] = array (21 => '[[Aten CS428 | http://www.aten.com/products/productItem.php?pcid=20070130111936003&psid=20070130133658002&pid=20050224114721008&layerid=subClass2]]');
			$new_words[] = array (21 => '[[Aten CS138A | http://www.aten.com/products/productItem.php?pcid=20070130111936003&psid=20070130133658002&pid=20050224111458007&layerid=subClass2]]');
			$new_words[] = array (21 => '[[Aten CS88A | http://www.aten.com/products/productItem.php?pcid=20070130111936003&psid=20070130133658002&pid=2005022411042006&layerid=subClass2]]');
			$new_words[] = array (21 => '[[Aten KM0832 | http://www.aten.com/products/productItem.php?pcid=2005010513171002&psid=20060411131626002&pid=20060628154826001&layerid=subClass1]]');
			$new_words[] = array (21 => '[[Aten KM0216 | http://www.aten.com/products/productItem.php?pcid=2006041110563001&psid=20060411131626002&pid=20060417153950007&layerid=subClass1]]');
			$new_words[] = array (21 => '[[Aten KM0432 | http://www.aten.com/products/productItem.php?pcid=2006041110563001&psid=20060411131626002&pid=2006041715359007&layerid=subClass1]]');
			$new_words[] = array (21 => '[[Aten KH1508 | http://www.aten.com/products/productItem.php?pcid=2006041110563001&psid=20060411130954001&pid=20061101174038001&layerid=subClass1]]');
			$new_words[] = array (21 => '[[Aten KH1516 | http://www.aten.com/products/productItem.php?pcid=2006041110563001&psid=20060411130954001&pid=20061101175320001&layerid=subClass1]]');
			$new_words[] = array (21 => '[[Aten KH0116 | http://www.aten.com/products/productItem.php?pcid=2006041110563001&psid=20060411130954001&pid=20060411145734003&layerid=subClass1]]');
			$new_words[] = array (21 => '[[Aten KH98 | http://www.aten.com/products/productItem.php?pcid=2005010513171002&psid=2007012911116003&pid=20061221104352001&layerid=subClass1]]');
			$new_words[] = array (23 => '[[Aten KL1100 | http://www.aten.com/products/productItem.php?pcid=2005010513171002&psid=20060411131050002&pid=20071225113046001&layerid=subClass1]]');
			$new_words[] = array (23 => '[[Aten KL1508 | http://www.aten.com/products/productItem.php?pcid=2006041110563001&psid=20060411131050002&pid=20070710020717009&layerid=subClass1]]');
			$new_words[] = array (23 => '[[Aten KL1516 | http://www.aten.com/products/productItem.php?pcid=2006041110563001&psid=20060411131050002&pid=20070716232614001&layerid=subClass1]]');
			$new_words[] = array (23 => '[[Aten KL9108 | http://www.aten.com/products/productItem.php?pcid=2005010513171002&psid=20060411131050002&pid=20060811153413009&layerid=subClass1]]');
			$new_words[] = array (23 => '[[Aten KL9116 | http://www.aten.com/products/productItem.php?pcid=2006041110563001&psid=20060411131050002&pid=2006081115384001&layerid=subClass1]]');
			$new_words[] = array (23 => '[[Aten KL3116 | http://www.aten.com/products/productItem.php?pcid=2005010513171002&psid=20060411131050002&pid=20060913162532009&layerid=subClass1]]');
			$new_words[] = array (23 => '[[Aten KL1116 | http://www.aten.com/products/productItem.php?pcid=2006041110563001&psid=20060411131050002&pid=20060420101520005&layerid=subClass1]]');
			$new_words[] = array (23 => '[[Aten CS1208DL | http://www.aten.com/products/productItem.php?pcid=2005010513171002&psid=20060411131050002&pid=2005022413505007&layerid=subClass1]]');
			$new_words[] = array (23 => '[[Aten CS1216DL | http://www.aten.com/products/productItem.php?pcid=2005010513171002&psid=20060411131050002&pid=2005022413505007&layerid=subClass1]]');
			$new_words[] = array (23 => '[[Aten CS1200L | http://www.aten.com/products/productItem.php?pcid=2005010513171002&psid=20060411131050002&pid=20050224140854008&layerid=subClass1]]');
			$new_words[] = array (23 => '[[Aten CL1758 | http://www.aten.com/products/productItem.php?pcid=2005010513171002&psid=20060411131050002&pid=20051229164553003&layerid=subClass1]]');
			$new_words[] = array (23 => '[[Aten CL1208 | http://www.aten.com/products/productItem.php?pcid=2005010513171002&psid=20060411131050002&pid=2005072215482&layerid=subClass1]]');
			$new_words[] = array (23 => '[[Aten CL1216 | http://www.aten.com/products/productItem.php?pcid=2005010513171002&psid=20060411131050002&pid=2005072215482&layerid=subClass1]]');
			$new_words[] = array (23 => '[[Aten CL1200 | http://www.aten.com/products/productItem.php?pcid=2005010513171002&psid=20060411131050002&pid=20050722165040002&layerid=subClass1]]');
			$new_words[] = array (23 => '[[Aten ACS1208AL | http://www.aten.com/products/productItem.php?pcid=2005010513171002&psid=20060411131050002&pid=2005022413597003&layerid=subClass1]]');
			$new_words[] = array (23 => '[[Aten ACS1216AL | http://www.aten.com/products/productItem.php?pcid=2005010513171002&psid=20060411131050002&pid=2005022413597003&layerid=subClass1]]');
			$new_words[] = array (22 => '[[Tainet MUXpro 8216 | http://www.tainet.net/Product/muxpro820_8216.htm]]');
			$new_words[] = array (22 => '[[Tainet Mercury 3600+ | http://www.tainet.net/Product/mercury.htm]]');
			$new_words[] = array (22 => '[[Tainet Mercury 3820 | http://www.tainet.net/Product/mercury.htm]]');
			$new_words[] = array (22 => '[[Tainet Mercury 3630 | http://www.tainet.net/Product/mercury.htm]]');
			$new_words[] = array (22 => '[[Tainet Mercury 3630E | http://www.tainet.net/Product/mercury.htm]]');
			$new_words[] = array (22 => '[[Tainet DSD-08A | http://www.tainet.net/Product/dsd08a.htm]]');
			$new_words[] = array (11 => '[[HP ProLiant DL160 | http://h10010.www1.hp.com/wwpc/us/en/en/WF05a/15351-15351-3328412-241644-3328421-3580694.html]]');
			$new_words[] = array (11 => '[[HP ProLiant DL180 | http://h10010.www1.hp.com/wwpc/us/en/en/WF05a/15351-15351-3328412-241644-3328421-3580698.html]]');
			$new_words[] = array (11 => '[[HP ProLiant DL185 | http://h10010.www1.hp.com/wwpc/us/en/en/WF05a/15351-15351-3328412-241644-3328421-3579900.html]]');
			$new_words[] = array (11 => '[[HP ProLiant DL365 | http://h10010.www1.hp.com/wwpc/us/en/en/WF05a/15351-15351-3328412-241644-241475-3186080.html]]');
			$new_words[] = array (11 => '[[HP ProLiant DL320s | http://h10010.www1.hp.com/wwpc/us/en/en/WF05a/15351-15351-3328412-241644-241475-3232017.html]]');
			$new_words[] = array (11 => '[[HP ProLiant DL320p | http://h10010.www1.hp.com/wwpc/us/en/en/WF05a/15351-15351-3328412-241644-241475-3579703.html]]');
			$new_words[] = array (11 => '[[HP ProLiant ML115 | http://h10010.www1.hp.com/wwpc/us/en/en/WF05a/15351-15351-241434-241646-3328424-3330535.html]]');
			$old_words = array();
			$old_words[487] = '[[RAD FCD-IPM | http://www.rad.com/Article/0,6583,36426-E1_T1_or_Fractional_E1_T1_Modular_Access_Device_with_Integrated_Router,00.html]]';
			$old_words[484] = '[[Cronyx FMUX-16-E3 | http://www.cronyx.ru/hardware/fmux16-e3.html]]';
			$old_words[101] = '[[HP ProLiant DL140 | http://h10010.www1.hp.com/wwpc/us/en/en/WF05a/15351-15351-3328412-241644-3328421-1842838.html]]';
			$old_words[102] = '[[HP ProLiant DL145 | http://h10010.www1.hp.com/wwpc/us/en/en/WF05a/15351-15351-3328412-241644-3328421-3219755.html]]';
			$old_words[103] = '[[HP ProLiant DL320 | http://h10010.www1.hp.com/wwpc/us/en/en/WF05a/15351-15351-3328412-241644-241475-3201178.html]]';
			$old_words[104] = '[[HP ProLiant DL360 | http://h10010.www1.hp.com/wwpc/us/en/en/WF05a/15351-15351-3328412-241644-241475-1121486.html]]';
			$old_words[105] = '[[HP ProLiant DL380 | http://h10010.www1.hp.com/wwpc/us/en/en/WF05a/15351-15351-3328412-241644-241475-1121516.html]]';
			$old_words[106] = '[[HP ProLiant DL385 | http://h10010.www1.hp.com/wwpc/us/en/en/WF05a/15351-15351-3328412-241644-241475-3219233.html]]';
			$old_words[107] = '[[HP ProLiant DL580 | http://h10010.www1.hp.com/wwpc/us/en/en/WF05a/15351-15351-3328412-241644-3328422-3454575.html]]';
			$old_words[108] = '[[HP ProLiant DL585 | http://h10010.www1.hp.com/wwpc/us/en/en/WF05a/15351-15351-3328412-241644-3328422-3219717.html]]';
			$old_words[109] = '[[HP ProLiant ML110 | http://h10010.www1.hp.com/wwpc/us/en/en/WF05a/15351-15351-241434-241646-3328424-3577708.html]]';
			$old_words[110] = '[[HP ProLiant ML150 | http://h10010.www1.hp.com/wwpc/us/en/en/WF05a/15351-15351-241434-241646-3328424-3580609.html]]';
			$old_words[111] = '[[HP ProLiant ML310 | http://h10010.www1.hp.com/wwpc/us/en/en/WF05a/15351-15351-241434-241646-241477-3580655.html]]';
			$old_words[112] = '[[HP ProLiant ML350 | http://h10010.www1.hp.com/wwpc/us/en/en/WF05a/15351-15351-241434-241646-241477-1121586.html]]';
			$old_words[113] = '[[HP ProLiant ML370 | http://h10010.www1.hp.com/wwpc/us/en/en/WF05a/15351-15351-241434-241646-241477-1121474.html]]';
			$old_words[114] = '[[HP ProLiant ML570 | http://h10010.www1.hp.com/wwpc/us/en/en/WF05a/15351-15351-241434-241646-3328425-1842779.html]]';
			foreach ($old_words as $dict_key => $dict_value)
				$query[] = "update Dictionary set dict_value = '${dict_value}' where dict_key = ${dict_key} limit 1";
			foreach ($new_words as $dict_key => $tmp)
				foreach ($tmp as $chapter_no => $dict_value)
					$query[] = 'INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) ' .
						"VALUES (${chapter_no}, ${dict_key}, '${dict_value}')";
			$query[] = "alter database character set utf8";
			$query[] = "update Config set varvalue = '0.14.12' where varname = 'DB_VERSION'";
			break; // --------------------------------------------
		case '0.15.0':
			$new_words[541] = array (12 => '[[Force10%GPASS%S2410CP | http://www.force10networks.com/products/s2410.asp]]');
			$new_words[] = array (12 => '[[Force10%GPASS%S50N | http://www.force10networks.com/products/s50n.asp]]');
			$new_words[] = array (12 => '[[Force10%GPASS%S50V | http://www.force10networks.com/products/s50v.asp]]');
			$new_words[] = array (12 => '[[Force10%GPASS%S25P | http://www.force10networks.com/products/s25p.asp]]');
			$new_words[] = array (12 => '[[Force10%GPASS%C150| http://www.force10networks.com/products/cseries.asp]]');
			$new_words[] = array (12 => '[[Force10%GPASS%C300| http://www.force10networks.com/products/cseries.asp]]');
			$new_words[] = array (12 => '[[Force10%GPASS%E300 | http://www.force10networks.com/products/eseries.asp]]');
			$new_words[] = array (12 => '[[Force10%GPASS%E600 | http://www.force10networks.com/products/eseries.asp]]');
			$new_words[] = array (12 => '[[Force10%GPASS%E1200 | http://www.force10networks.com/products/eseries.asp]]');
			$new_words[] = array (12 => '[[NETGEAR%GPASS%JGS524F | http://www.netgear.com/Products/Switches/UnmanagedSwitches/JGS524F.aspx]]');
			$new_words[] = array (12 => '[[NETGEAR%GPASS%JGS516 | http://www.netgear.com/Products/Switches/UnmanagedSwitches/JGS516.aspx]]');
			$new_words[] = array (12 => '[[NETGEAR%GPASS%JFS524 | http://www.netgear.com/Products/Switches/UnmanagedSwitches/JFS524.aspx]]');
			$new_words[] = array (12 => '[[NETGEAR%GPASS%JFS524F | http://www.netgear.com/Products/Switches/UnmanagedSwitches/JFS524F.aspx]]');
			$new_words[] = array (12 => '[[NETGEAR%GPASS%JGS524 | http://www.netgear.com/Products/Switches/UnmanagedSwitches/JGS524.aspx]]');
			$new_words[] = array (12 => '[[NETGEAR%GPASS%FS524 | http://www.netgear.com/Products/Switches/UnmanagedSwitches/FS524.aspx]]');
			$new_words[] = array (12 => '[[NETGEAR%GPASS%JFS516 | http://www.netgear.com/Products/Switches/UnmanagedSwitches/JFS516.aspx]]');
			$new_words[] = array (12 => '[[NETGEAR%GPASS%GSM7224R | http://www.netgear.com/Products/Switches/Layer2ManagedSwitches/GSM7224R.aspx]]');
			$new_words[] = array (12 => '[[NETGEAR%GPASS%GSM7248 | http://www.netgear.com/Products/Switches/Layer2ManagedSwitches/GSM7248.aspx]]');
			$new_words[] = array (12 => '[[NETGEAR%GPASS%GSM7212 | http://www.netgear.com/Products/Switches/Layer2ManagedSwitches/GSM7212.aspx]]');
			$new_words[] = array (12 => '[[NETGEAR%GPASS%FSM726S | http://www.netgear.com/Products/Switches/Layer2ManagedSwitches/FSM726S.aspx]]');
			$new_words[] = array (12 => '[[NETGEAR%GPASS%GSM7248R | http://www.netgear.com/Products/Switches/Layer2ManagedSwitches/GSM7248R.aspx]]');
			$new_words[] = array (12 => '[[NETGEAR%GPASS%GSM7224 | http://www.netgear.com/Products/Switches/Layer2ManagedSwitches/GSM7224.aspx]]');
			$new_words[] = array (12 => '[[NETGEAR%GPASS%FSM750S | http://www.netgear.com/Products/Switches/Layer2ManagedSwitches/FSM750S.aspx]]');
			$new_words[] = array (12 => '[[NETGEAR%GPASS%FSM726 | http://www.netgear.com/Products/Switches/Layer2ManagedSwitches/FSM726.aspx]]');
			$new_words[] = array (12 => '[[NETGEAR%GPASS%GS724TP | http://www.netgear.com/Products/Switches/SmartSwitches/GS724TP.aspx]]');
			$new_words[] = array (12 => '[[NETGEAR%GPASS%GS748TS | http://www.netgear.com/Products/Switches/SmartSwitches/GS748TS.aspx]]');
			$new_words[] = array (12 => '[[NETGEAR%GPASS%GS724T | http://www.netgear.com/Products/Switches/SmartSwitches/GS724T.aspx]]');
			$new_words[] = array (12 => '[[NETGEAR%GPASS%FS728TP | http://www.netgear.com/Products/Switches/SmartSwitches/FS728TP.aspx]]');
			$new_words[] = array (12 => '[[NETGEAR%GPASS%FS752TS | http://www.netgear.com/Products/Switches/SmartSwitches/FS752TS.aspx]]');
			$new_words[] = array (12 => '[[NETGEAR%GPASS%FS728TS | http://www.netgear.com/Products/Switches/SmartSwitches/FS728TS.aspx]]');
			$new_words[] = array (12 => '[[NETGEAR%GPASS%FS726T | http://www.netgear.com/Products/Switches/SmartSwitches/FS726T.aspx]]');
			$new_words[] = array (12 => '[[NETGEAR%GPASS%GS748TP | http://www.netgear.com/Products/Switches/SmartSwitches/GS748TP.aspx]]');
			$new_words[] = array (12 => '[[NETGEAR%GPASS%GS724TS | http://www.netgear.com/Products/Switches/SmartSwitches/GS724TS.aspx]]');
			$new_words[] = array (12 => '[[NETGEAR%GPASS%GS748T | http://www.netgear.com/Products/Switches/SmartSwitches/GS748T.aspx]]');
			$new_words[] = array (12 => '[[NETGEAR%GPASS%GS716T | http://www.netgear.com/Products/Switches/SmartSwitches/GS716T.aspx]]');
			$new_words[] = array (12 => '[[NETGEAR%GPASS%FS752TPS | http://www.netgear.com/Products/Switches/SmartSwitches/FS752TPS.aspx]]');
			$new_words[] = array (12 => '[[NETGEAR%GPASS%FS750T2 | http://www.netgear.com/Products/Switches/SmartSwitches/FS750T2.aspx]]');
			$new_words[] = array (12 => '[[NETGEAR%GPASS%FS726TP | http://www.netgear.com/Products/Switches/SmartSwitches/FS726TP.aspx]]');
			$new_words[] = array (12 => '[[NETGEAR%GPASS%FSM7328PS | http://www.netgear.com/Products/Switches/Layer3ManagedSwitches/FSM7328PS.aspx]]');
			$new_words[] = array (12 => '[[NETGEAR%GPASS%GSM7352S | http://www.netgear.com/Products/Switches/Layer3ManagedSwitches/GSM7352S.aspx]]');
			$new_words[] = array (12 => '[[NETGEAR%GPASS%GSM7324 | http://www.netgear.com/Products/Switches/Layer3ManagedSwitches/GSM7324.aspx]]');
			$new_words[] = array (12 => '[[NETGEAR%GPASS%FSM7326P | http://www.netgear.com/Products/Switches/Layer3ManagedSwitches/FSM7326P.aspx]]');
			$new_words[] = array (12 => '[[NETGEAR%GPASS%FSM7352PS | http://www.netgear.com/Products/Switches/Layer3ManagedSwitches/FSM7352PS.aspx]]');
			$new_words[] = array (12 => '[[NETGEAR%GPASS%GSM7328FS | http://www.netgear.com/Products/Switches/Layer3ManagedSwitches/GSM7328FS.aspx]]');
			$new_words[] = array (12 => '[[NETGEAR%GPASS%GSM7328S | http://www.netgear.com/Products/Switches/Layer3ManagedSwitches/GSM7328S.aspx]]');
			$new_words[] = array (12 => '[[NETGEAR%GPASS%GSM7312 | http://www.netgear.com/Products/Switches/Layer3ManagedSwitches/GSM7312.aspx]]');
			$new_words[] = array (12 => '[[NETGEAR%GPASS%FSM7328S | http://www.netgear.com/Products/Switches/Layer3ManagedSwitches/FSM7328S.aspx]]');
			$new_words[] = array (12 => '[[NETGEAR%GPASS%FSM7352S | http://www.netgear.com/Products/Switches/Layer3ManagedSwitches/FSM7352S.aspx]]');
			$new_words[] = array (12 => '[[D-Link%GPASS%DES-6500 | http://www.dlink.com/products/?sec=0&pid=341]]');
			$new_words[] = array (12 => '[[D-Link%GPASS%DWS-3227 | http://www.dlink.com/products/?sec=0&pid=506]]');
			$new_words[] = array (12 => '[[D-Link%GPASS%DWS-3227P | http://www.dlink.com/products/?sec=0&pid=507]]');
			$new_words[] = array (12 => '[[D-Link%GPASS%DWS-3250 | http://www.dlink.com/products/?sec=0&pid=468]]');
			$new_words[] = array (12 => '[[D-Link%GPASS%DWS-1008 | http://www.dlink.com/products/?sec=0&pid=434]]');
			$new_words[] = array (12 => '[[D-Link%GPASS%DGS-3612G | http://www.dlink.com/products/?sec=0&pid=557]]');
			$new_words[] = array (12 => '[[D-Link%GPASS%DGS-3627 | http://www.dlink.com/products/?sec=0&pid=639]]');
			$new_words[] = array (12 => '[[D-Link%GPASS%DGS-3650 | http://www.dlink.com/products/?sec=0&pid=640]]');
			$new_words[] = array (12 => '[[D-Link%GPASS%DGS-3324SR | http://www.dlink.com/products/?sec=0&pid=294]]');
			$new_words[] = array (12 => '[[D-Link%GPASS%DGS-3324SRi | http://www.dlink.com/products/?sec=0&pid=309]]');
			$new_words[] = array (12 => '[[D-Link%GPASS%DXS-3326GSR | http://www.dlink.com/products/?sec=0&pid=339]]');
			$new_words[] = array (12 => '[[D-Link%GPASS%DXS-3350SR | http://www.dlink.com/products/?sec=0&pid=340]]');
			$new_words[] = array (12 => '[[D-Link%GPASS%DES-3828 | http://www.dlink.com/products/?sec=0&pid=439]]');
			$new_words[] = array (12 => '[[D-Link%GPASS%DES-3828P | http://www.dlink.com/products/?sec=0&pid=440]]');
			$new_words[] = array (12 => '[[D-Link%GPASS%DGS-3100-24 | http://www.dlink.com/products/?sec=0&pid=635]]');
			$new_words[] = array (12 => '[[D-Link%GPASS%DGS-3100-24P | http://www.dlink.com/products/?sec=0&pid=636]]');
			$new_words[] = array (12 => '[[D-Link%GPASS%DGS-3100-48 | http://www.dlink.com/products/?sec=0&pid=637]]');
			$new_words[] = array (12 => '[[D-Link%GPASS%DGS-3100-48P | http://www.dlink.com/products/?sec=0&pid=638]]');
			$new_words[] = array (12 => '[[D-Link%GPASS%DXS-3227 | http://www.dlink.com/products/?sec=0&pid=483]]');
			$new_words[] = array (12 => '[[D-Link%GPASS%DXS-3227P | http://www.dlink.com/products/?sec=0&pid=497]]');
			$new_words[] = array (12 => '[[D-Link%GPASS%DXS-3250 | http://www.dlink.com/products/?sec=0&pid=443]]');
			$new_words[] = array (12 => '[[D-Link%GPASS%DGS-3024 | http://www.dlink.com/products/?sec=0&pid=404]]');
			$new_words[] = array (12 => '[[D-Link%GPASS%DGS-3224TGR | http://www.dlink.com/products/?sec=0&pid=269]]');
			$new_words[] = array (12 => '[[D-Link%GPASS%DGS-3048 | http://www.dlink.com/products/?sec=0&pid=496]]');
			$new_words[] = array (12 => '[[D-Link%GPASS%DES-3228PA | http://www.dlink.com/products/?sec=0&pid=644]]');
			$new_words[] = array (12 => '[[D-Link%GPASS%DES-3028 | http://www.dlink.com/products/?sec=0&pid=630]]');
			$new_words[] = array (12 => '[[D-Link%GPASS%DES-3028P | http://www.dlink.com/products/?sec=0&pid=631]]');
			$new_words[] = array (12 => '[[D-Link%GPASS%DES-3052 | http://www.dlink.com/products/?sec=0&pid=632]]');
			$new_words[] = array (12 => '[[D-Link%GPASS%DES-3052P | http://www.dlink.com/products/?sec=0&pid=633]]');
			$new_words[] = array (12 => '[[D-Link%GPASS%DES-3010FA | http://www.dlink.com/products/?sec=0&pid=423]]');
			$new_words[] = array (12 => '[[D-Link%GPASS%DES-3010GA | http://www.dlink.com/products/?sec=0&pid=424]]');
			$new_words[] = array (12 => '[[D-Link%GPASS%DES-3010PA | http://www.dlink.com/products/?sec=0&pid=469]]');
			$new_words[] = array (12 => '[[D-Link%GPASS%DES-3226L | http://www.dlink.com/products/?sec=0&pid=298]]');
			$new_words[] = array (12 => '[[D-Link%GPASS%DES-3526 | http://www.dlink.com/products/?sec=0&pid=330]]');
			$new_words[] = array (12 => '[[D-Link%GPASS%DES-3550 | http://www.dlink.com/products/?sec=0&pid=331]]');
			$new_words[] = array (12 => '[[D-Link%GPASS%DGS-1216T | http://www.dlink.com/products/?sec=0&pid=324]]');
			$new_words[] = array (12 => '[[D-Link%GPASS%DGS-1224T | http://www.dlink.com/products/?sec=0&pid=329]]');
			$new_words[] = array (12 => '[[D-Link%GPASS%DGS-1248T | http://www.dlink.com/products/?sec=0&pid=367]]');
			$new_words[] = array (12 => '[[D-Link%GPASS%DES-1316 | http://www.dlink.com/products/?sec=0&pid=353]]');
			$new_words[] = array (12 => '[[D-Link%GPASS%DES-1228 | http://www.dlink.com/products/?sec=0&pid=540]]');
			$new_words[] = array (12 => '[[D-Link%GPASS%DES-1228P | http://www.dlink.com/products/?sec=0&pid=541]]');
			$new_words[] = array (12 => '[[D-Link%GPASS%DES-1252 | http://www.dlink.com/products/?sec=0&pid=555]]');
			$new_words[] = array (12 => '[[D-Link%GPASS%DGS-1016D | http://www.dlink.com/products/?sec=0&pid=337]]');
			$new_words[] = array (12 => '[[D-Link%GPASS%DGS-1024D | http://www.dlink.com/products/?sec=0&pid=338]]');
			$new_words[] = array (12 => '[[D-Link%GPASS%DSS-24+ | http://www.dlink.com/products/?sec=0&pid=73]]');
			$new_words[] = array (12 => '[[D-Link%GPASS%DES-1024D | http://www.dlink.com/products/?sec=0&pid=75]]');
			$new_words[] = array (12 => '[[D-Link%GPASS%DES-1026G | http://www.dlink.com/products/?sec=0&pid=76]]');
			$new_words[] = array (21 => '[[D-Link%GPASS%DKVM-16 | http://www.dlink.com/products/?sec=0&pid=228]]');
			$new_words[] = array (21 => '[[D-Link%GPASS%DKVM-8E | http://www.dlink.com/products/?sec=0&pid=161]]');
			$new_words[] = array (22 => '[[Raisecom%GPASS%RC702 | http://www.raisecom-international.com/p/RC702.htm]]');
			$new_words[] = array (22 => '[[Raisecom%GPASS%RC702-GE | http://www.raisecom-international.com/p/RC702GE.htm]]');
			$new_words[] = array (22 => '[[Raisecom%GPASS%ISCOM4300 | http://www.raisecom-international.com/p/ISCOM4300.htm]]');
			$new_words[] = array (22 => '[[Raisecom%GPASS%RC953-FE4E1 | http://www.raisecom-international.com/p/RC953FE4E1.htm]]');
			$new_words[] = array (22 => '[[Raisecom%GPASS%RC953-FX4E1 | http://www.raisecom-international.com/p/RC953FE4E1.htm]]');
			$new_words[] = array (22 => '[[Raisecom%GPASS%RC953-FE8E1 | http://www.raisecom-international.com/p/RC953FE4E1.htm]]');
			$new_words[] = array (22 => '[[Raisecom%GPASS%RC953-FX8E1 | http://www.raisecom-international.com/p/RC953FE4E1.htm]]');
			$new_words[] = array (22 => '[[Raisecom%GPASS%RC953-8FE16E1 | http://www.raisecom-international.com/p/RC9538FE16E1.htm]]');
			$new_words[] = array (22 => '[[Raisecom%GPASS%RC953E-3FE16E1 | http://www.raisecom-international.com/p/RC953E-3FE16E1.htm]]');
			$new_words[] = array (22 => '[[Raisecom%GPASS%RC953-GESTM1 | http://www.raisecom-international.com/p/RC957.htm]]');
			$new_words[] = array (22 => '[[Raisecom%GPASS%OPCOM3100-155 | http://www.raisecom-international.com/p/OPCOM3100.htm]]');
			$new_words[] = array (22 => '[[Raisecom%GPASS%OPCOM3101-155 | http://www.raisecom-international.com/p/OPCOM3101.htm]]');
			$new_words[] = array (22 => '[[Raisecom%GPASS%RC831-120 | http://www.raisecom-international.com/p/RC831.htm]]');
			$new_words[] = array (22 => '[[Raisecom%GPASS%RC831-120-BL | http://www.raisecom-international.com/p/RC831.htm]]');
			$new_words[] = array (22 => '[[Raisecom%GPASS%RC831-240 | http://www.raisecom-international.com/p/RC831.htm]]');
			$new_words[] = array (22 => '[[Raisecom%GPASS%RC831-240E | http://www.raisecom-international.com/p/RC831.htm]]');
			$new_words[] = array (22 => '[[Raisecom%GPASS%RCMS2801-480GE-BL | http://www.raisecom-international.com/p/RCMS280X.htm]]');
			$new_words[] = array (22 => '[[Raisecom%GPASS%RCMS2801-120FE | http://www.raisecom-international.com/p/RCMS2801.htm]]');
			$new_words[] = array (22 => '[[Raisecom%GPASS%RCMS2801-120FE-BL | http://www.raisecom-international.com/p/RCMS2801.htm]]');
			$new_words[] = array (22 => '[[Raisecom%GPASS%RCMS2801-240FE | http://www.raisecom-international.com/p/RCMS2801.htm]]');
			$new_words[] = array (22 => '[[Raisecom%GPASS%RCMS2801-240FE-BL | http://www.raisecom-international.com/p/RCMS2801.htm]]');
			$new_words[] = array (22 => '[[Raisecom%GPASS%RCMS2801-240EFE | http://www.raisecom-international.com/p/RCMS2801.htm]]');
			$new_words[] = array (22 => '[[Raisecom%GPASS%RCMS2811-120FE | http://www.raisecom-international.com/p/RCMS2811.htm]]');
			$new_words[] = array (22 => '[[Raisecom%GPASS%RCMS2811-240FE | http://www.raisecom-international.com/p/RCMS2811.htm]]');
			$new_words[] = array (22 => '[[Raisecom%GPASS%RCMS2811-240FE-BL | http://www.raisecom-international.com/p/RCMS2811.htm]]');
			$new_words[] = array (22 => '[[Raisecom%GPASS%RCMS2811-480FE | http://www.raisecom-international.com/p/RCMS2811.htm]]');
			$new_words[] = array (22 => '[[Raisecom%GPASS%RCMS2811-480FE-BL | http://www.raisecom-international.com/p/RCMS2811.htm]]');
			$new_words[] = array (22 => '[[Raisecom%GPASS%RCMS2811-240EFE | http://www.raisecom-international.com/p/RCMS2811-240EFE.htm]]');
			$new_words[] = array (22 => '[[Raisecom%GPASS%RCMS2104-120 | http://www.raisecom-international.com/p/RCMS2000120.htm]]');
			$new_words[] = array (22 => '[[Raisecom%GPASS%RCMS2304-120 | http://www.raisecom-international.com/p/RCMS2000120.htm]]');
			$new_words[] = array (22 => '[[Raisecom%GPASS%RCMS2504-120 | http://www.raisecom-international.com/p/RCMS2000120.htm]]');
			$new_words[] = array (22 => '[[Raisecom%GPASS%RCMS2104-240 | http://www.raisecom-international.com/p/RCMS2000120.htm]]');
			$new_words[] = array (22 => '[[Raisecom%GPASS%RCMS2304-240 | http://www.raisecom-international.com/p/RCMS2000120.htm]]');
			$new_words[] = array (22 => '[[Raisecom%GPASS%RCMS2504-240 | http://www.raisecom-international.com/p/RCMS2000120.htm]]');
			$new_words[] = array (22 => '[[Raisecom%GPASS%RC801-120B | http://www.raisecom-international.com/p/RC800120.htm]]');
			$new_words[] = array (22 => '[[Raisecom%GPASS%RC801-240B | http://www.raisecom-international.com/p/RC800120.htm]]');
			$new_words[] = array (22 => '[[Raisecom%GPASS%RC801-480B | http://www.raisecom-international.com/p/RC800120.htm]]');
			$new_words[] = array (22 => '[[Raisecom%GPASS%RC803-120B | http://www.raisecom-international.com/p/RC800120.htm]]');
			$new_words[] = array (22 => '[[Raisecom%GPASS%RC803-240B | http://www.raisecom-international.com/p/RC800120.htm]]');
			$new_words[] = array (22 => '[[Raisecom%GPASS%RC803-480B | http://www.raisecom-international.com/p/RC800120.htm]]');
			$new_words[] = array (22 => '[[Raisecom%GPASS%RC805-120B | http://www.raisecom-international.com/p/RC800120.htm]]');
			$new_words[] = array (22 => '[[Raisecom%GPASS%RC805-240B | http://www.raisecom-international.com/p/RC800120.htm]]');
			$new_words[] = array (22 => '[[Raisecom%GPASS%RC805-480B | http://www.raisecom-international.com/p/RC800120.htm]]');
			$new_words[] = array (2 => 'async serial (DB-9)');  // 681
			$new_words[] = array (2 => 'async serial (DB-25)'); // 682
			$new_words[] = array (12 => '[[Force10%GPASS%S2410P | http://www.force10networks.com/products/s2410.asp]]');
			$new_words[] = array (12 => '[[Extreme Networks%GPASS%Summit X150-24t | http://www.extremenetworks.com/products/summit-x150.aspx]]');
			$new_words[] = array (12 => '[[Extreme Networks%GPASS%Summit X150-48t | http://www.extremenetworks.com/products/summit-x150.aspx]]');
			$new_words[] = array (12 => '[[Extreme Networks%GPASS%Summit X150-24p | http://www.extremenetworks.com/products/summit-x150.aspx]]');
			$new_words[] = array (12 => '[[Extreme Networks%GPASS%Summit X250e-24t | http://www.extremenetworks.com/products/summit-x250e.aspx]]');
			$new_words[] = array (12 => '[[Extreme Networks%GPASS%Summit X250e-48t | http://www.extremenetworks.com/products/summit-x250e.aspx]]');
			$new_words[] = array (12 => '[[Extreme Networks%GPASS%Summit X250e-24p | http://www.extremenetworks.com/products/summit-x250e.aspx]]');
			$new_words[] = array (12 => '[[Extreme Networks%GPASS%Summit X250e-48p | http://www.extremenetworks.com/products/summit-x250e.aspx]]');
			$new_words[] = array (12 => '[[Extreme Networks%GPASS%Summit X250e-24x | http://www.extremenetworks.com/products/summit-x250e.aspx]]');
			$new_words[] = array (12 => '[[Extreme Networks%GPASS%Summit X450-24t | http://www.extremenetworks.com/products/summit-x450.aspx]]');
			$new_words[] = array (12 => '[[Extreme Networks%GPASS%Summit X450-24x | http://www.extremenetworks.com/products/summit-x450.aspx]]');
			$new_words[] = array (12 => '[[Extreme Networks%GPASS%Summit X450a-24t | http://www.extremenetworks.com/products/summit-x450a.aspx]]');
			$new_words[] = array (12 => '[[Extreme Networks%GPASS%Summit X450a-48t | http://www.extremenetworks.com/products/summit-x450a.aspx]]');
			$new_words[] = array (12 => '[[Extreme Networks%GPASS%Summit X450a-24x | http://www.extremenetworks.com/products/summit-x450a.aspx]]');
			$new_words[] = array (12 => '[[Extreme Networks%GPASS%Summit X450e-24p | http://www.extremenetworks.com/products/summit-x450e.aspx]]');
			$new_words[] = array (12 => '[[Extreme Networks%GPASS%Summit X450e-48p | http://www.extremenetworks.com/products/summit-x450e.aspx]]');
			$new_words[] = array (12 => '[[Extreme Networks%GPASS%Summit 200-24fx | http://www.extremenetworks.com/products/summit-200.aspx]]');
			$new_words[] = array (12 => '[[Extreme Networks%GPASS%Summit 200-24 | http://www.extremenetworks.com/products/summit-200.aspx]]');
			$new_words[] = array (12 => '[[Extreme Networks%GPASS%Summit 200-48 | http://www.extremenetworks.com/products/summit-200.aspx]]');
			$new_words[] = array (12 => '[[Extreme Networks%GPASS%Summit 300-24 | http://www.extremenetworks.com/products/summit-300.aspx]]');
			$new_words[] = array (12 => '[[Extreme Networks%GPASS%Summit 300-48 | http://www.extremenetworks.com/products/summit-300.aspx]]');
			$new_words[] = array (12 => '[[Extreme Networks%GPASS%Summit 400-24p | http://www.extremenetworks.com/products/summit-400-24p.aspx]]');
			$new_words[] = array (12 => '[[Extreme Networks%GPASS%Summit 400-24t | http://www.extremenetworks.com/products/summit-400-24t.aspx]]');
			$new_words[] = array (12 => '[[Extreme Networks%GPASS%Summit 400-48t | http://www.extremenetworks.com/products/summit-400-48t.aspx]]');
			$new_words[] = array (12 => '[[Extreme Networks%GPASS%Summit48si | http://www.extremenetworks.com/products/summit-48si.aspx]]');
			$new_words[] = array (12 => '[[Extreme Networks%GPASS%Alpine 3804 | http://www.extremenetworks.com/products/Alpine-3800.aspx]]');
			$new_words[] = array (12 => '[[Extreme Networks%GPASS%Alpine 3808 | http://www.extremenetworks.com/products/Alpine-3800.aspx]]');
			$new_words[] = array (12 => '[[Extreme Networks%GPASS%BlackDiamond 6808 | http://www.extremenetworks.com/products/blackdiamond-6800.aspx]]');
			$new_words[] = array (12 => '[[Extreme Networks%GPASS%BlackDiamond 8806 | http://www.extremenetworks.com/products/blackdiamond-8800.aspx]]');
			$new_words[] = array (12 => '[[Extreme Networks%GPASS%BlackDiamond 8810 | http://www.extremenetworks.com/products/blackdiamond-8800.aspx]]');
			$new_words[] = array (12 => '[[Extreme Networks%GPASS%BlackDiamond 10808 | http://www.extremenetworks.com/products/blackdiamond-10808.aspx]]');
			$new_words[] = array (12 => '[[Extreme Networks%GPASS%BlackDiamond 12802R | http://www.extremenetworks.com/products/blackdiamond-12800r.aspx]]');
			$new_words[] = array (12 => '[[Extreme Networks%GPASS%BlackDiamond 12804R | http://www.extremenetworks.com/products/blackdiamond-12800r.aspx]]');
			$new_words[] = array (12 => '[[Extreme Networks%GPASS%BlackDiamond 12804C | http://www.extremenetworks.com/products/blackdiamond-12804c.aspx]]');
			$new_words[] = array (17 => '[[Cisco%GPASS%ASR 1002 | http://cisco.com/en/US/products/ps9436/index.html]]');
			$new_words[] = array (17 => '[[Cisco%GPASS%ASR 1004 | http://cisco.com/en/US/products/ps9437/index.html]]');
			$new_words[] = array (17 => '[[Cisco%GPASS%ASR 1006 | http://cisco.com/en/US/products/ps9438/index.html]]');
			$new_words[] = array (13 => '[[BSD%GSKIP%OpenBSD 3.3 | http://openbsd.org/33.html]]');
			$new_words[] = array (13 => '[[BSD%GSKIP%OpenBSD 3.4 | http://openbsd.org/34.html]]');
			$new_words[] = array (13 => '[[BSD%GSKIP%OpenBSD 3.5 | http://openbsd.org/35.html]]');
			$new_words[] = array (13 => '[[BSD%GSKIP%OpenBSD 3.6 | http://openbsd.org/36.html]]');
			$new_words[] = array (13 => '[[BSD%GSKIP%OpenBSD 3.7 | http://openbsd.org/37.html]]');
			$new_words[] = array (13 => '[[BSD%GSKIP%OpenBSD 3.8 | http://openbsd.org/38.html]]');
			$new_words[] = array (13 => '[[BSD%GSKIP%OpenBSD 3.9 | http://openbsd.org/39.html]]');
			$new_words[] = array (13 => '[[BSD%GSKIP%OpenBSD 4.0 | http://openbsd.org/40.html]]');
			$new_words[] = array (13 => '[[BSD%GSKIP%OpenBSD 4.1 | http://openbsd.org/41.html]]');
			$new_words[] = array (13 => '[[BSD%GSKIP%OpenBSD 4.2 | http://openbsd.org/42.html]]');
			$new_words[] = array (13 => '[[BSD%GSKIP%OpenBSD 4.3 | http://openbsd.org/43.html]]');
			$new_words[] = array (12 => '[[Cisco Catalyst 4900M | http://www.cisco.com/en/US/products/ps9310/index.html]]');
			$new_words[] = array (13 => '[[BSD%GSKIP%FreeBSD 7.x | http://www.freebsd.org/releases/7.0R/announce.html]]');
			$new_words[] = array (13 => '[[BSD%GSKIP%NetBSD 2.0 | http://netbsd.org/releases/formal-2.0/]]');
			$new_words[] = array (13 => '[[BSD%GSKIP%NetBSD 2.1 | http://netbsd.org/releases/formal-2.0/NetBSD-2.1.html]]');
			$new_words[] = array (13 => '[[BSD%GSKIP%NetBSD 3.0 | http://netbsd.org/releases/formal-3/]]');
			$new_words[] = array (13 => '[[BSD%GSKIP%NetBSD 3.1 | http://netbsd.org/releases/formal-3/NetBSD-3.1.html]]');
			$new_words[] = array (13 => '[[BSD%GSKIP%NetBSD 4.0 | http://netbsd.org/releases/formal-4/NetBSD-4.0.html]]');
			$new_words[] = array (12 => '[[3Com%GPASS%Baseline 2016 | http://www.3com.com/products/en_US/detail.jsp?tab=features&pathtype=purchase&sku=3C16470B]]');
			$new_words[] = array (12 => '[[3Com%GPASS%Baseline 2024 | http://www.3com.com/products/en_US/detail.jsp?tab=features&pathtype=purchase&sku=3C16471B]]');
			$new_words[] = array (12 => '[[3Com%GPASS%Baseline 2126-G | http://www.3com.com/products/en_US/detail.jsp?tab=features&pathtype=purchase&sku=3C16472]]');
			$new_words[] = array (12 => '[[3Com%GPASS%Baseline 2816 | http://www.3com.com/products/en_US/detail.jsp?tab=features&pathtype=purchase&sku=3C16478]]');
			$new_words[] = array (12 => '[[3Com%GPASS%Baseline 2824 | http://www.3com.com/products/en_US/detail.jsp?tab=features&pathtype=purchase&sku=3C16479]]');
			$new_words[] = array (12 => '[[3Com%GPASS%Baseline 2226 Plus | http://www.3com.com/products/en_US/detail.jsp?tab=features&pathtype=purchase&sku=3C16475CS]]');
			$new_words[] = array (12 => '[[3Com%GPASS%Baseline 2426-PWR Plus | http://www.3com.com/products/en_US/detail.jsp?tab=features&pathtype=purchase&sku=3C16491]]');
			$new_words[] = array (12 => '[[3Com%GPASS%Baseline 2250 Plus | http://www.3com.com/products/en_US/detail.jsp?tab=features&pathtype=purchase&sku=3C16476CS]]');
			$new_words[] = array (12 => '[[3Com%GPASS%Baseline 2916-SFP Plus | http://www.3com.com/products/en_US/detail.jsp?tab=features&pathtype=purchase&sku=3CBLSG16]]');
			$new_words[] = array (12 => '[[3Com%GPASS%Baseline 2924-SFP Plus | http://www.3com.com/products/en_US/detail.jsp?tab=features&pathtype=purchase&sku=3CBLSG24]]');
			$new_words[] = array (12 => '[[3Com%GPASS%Baseline 2924-PWR Plus | http://www.3com.com/products/en_US/detail.jsp?tab=features&pathtype=purchase&sku=3CBLSG24PWR]]');
			$new_words[] = array (12 => '[[3Com%GPASS%Baseline 2948-SFP Plus | http://www.3com.com/products/en_US/detail.jsp?tab=features&pathtype=purchase&sku=3CBLSG48]]');
			$new_words[] = array (12 => '[[3Com%GPASS%3870 24-port | http://www.3com.com/products/en_US/detail.jsp?tab=features&pathtype=purchase&sku=3CR17450-91]]');
			$new_words[] = array (12 => '[[3Com%GPASS%3870 48-port | http://www.3com.com/products/en_US/detail.jsp?tab=features&pathtype=purchase&sku=3CR17451-91]]');
			$new_words[] = array (12 => '[[3Com%GPASS%4200 26-port | http://www.3com.com/products/en_US/detail.jsp?tab=features&sku=3C17300A&pathtype=purchase]]');
			$new_words[] = array (12 => '[[3Com%GPASS%4200 28-port | http://www.3com.com/products/en_US/detail.jsp?tab=features&sku=3C17304A&pathtype=purchase]]');
			$new_words[] = array (12 => '[[3Com%GPASS%4200 50-port | http://www.3com.com/products/en_US/detail.jsp?tab=features&sku=3C17302A&pathtype=purchase]]');
			$new_words[] = array (12 => '[[3Com%GPASS%4200G 12-port | http://www.3com.com/products/en_US/detail.jsp?tab=features&sku=3CR17660-91&pathtype=purchase]]');
			$new_words[] = array (12 => '[[3Com%GPASS%4200G 24-port | http://www.3com.com/products/en_US/detail.jsp?tab=features&sku=3CR17661-91&pathtype=purchase]]');
			$new_words[] = array (12 => '[[3Com%GPASS%4200G PWR 24-port | http://www.3com.com/products/en_US/detail.jsp?tab=features&sku=3CR17671-91&pathtype=purchase]]');
			$new_words[] = array (12 => '[[3Com%GPASS%4200G 48-port | http://www.3com.com/products/en_US/detail.jsp?tab=features&sku=3CR17662-91&pathtype=purchase]]');
			$new_words[] = array (12 => '[[3Com%GPASS%4210 26-port | http://www.3com.com/products/en_US/detail.jsp?tab=features&pathtype=purchase&sku=3CR17333-91]]');
			$new_words[] = array (12 => '[[3Com%GPASS%4210 52-port | http://www.3com.com/products/en_US/detail.jsp?tab=features&pathtype=purchase&sku=3CR17334-91]]');
			$new_words[] = array (12 => '[[3Com%GPASS%4210 26-port PWR | http://www.3com.com/products/en_US/detail.jsp?tab=features&pathtype=purchase&sku=3CR17343-91]]');
			$new_words[] = array (12 => '[[3Com%GPASS%SS3 4400 48-port | http://www.3com.com/products/en_US/detail.jsp?tab=features&pathtype=purchase&sku=3C17204-US]]');
			$new_words[] = array (12 => '[[3Com%GPASS%SS3 4400 24-port | http://www.3com.com/products/en_US/detail.jsp?tab=features&pathtype=purchase&sku=3C17203-US]]');
			$new_words[] = array (12 => '[[3Com%GPASS%SS3 4400 PWR | http://www.3com.com/products/en_US/detail.jsp?tab=features&pathtype=purchase&sku=3C17205-US]]');
			$new_words[] = array (12 => '[[3Com%GPASS%SS3 4400 SE 24-port | http://www.3com.com/products/en_US/detail.jsp?tab=features&pathtype=purchase&sku=3C17206-US]]');
			$new_words[] = array (12 => '[[3Com%GPASS%4500 26-port | http://www.3com.com/products/en_US/detail.jsp?tab=features&pathtype=purchase&sku=3CR17561-91]]');
			$new_words[] = array (12 => '[[3Com%GPASS%4500 50-port | http://www.3com.com/products/en_US/detail.jsp?tab=features&pathtype=purchase&sku=3CR17562-91]]');
			$new_words[] = array (12 => '[[3Com%GPASS%4500 PWR 26-port | http://www.3com.com/products/en_US/detail.jsp?tab=features&pathtype=purchase&sku=3CR17571-91]]');
			$new_words[] = array (12 => '[[3Com%GPASS%4500 PWR 50-port | http://www.3com.com/products/en_US/detail.jsp?tab=features&pathtype=purchase&sku=3CR17572-91]]');
			$new_words[] = array (12 => '[[3Com%GPASS%4500G 24-port | http://www.3com.com/products/en_US/detail.jsp?tab=features&pathtype=purchase&sku=3CR17761-91]]');
			$new_words[] = array (12 => '[[3Com%GPASS%4500G 48-port | http://www.3com.com/products/en_US/detail.jsp?tab=features&pathtype=purchase&sku=3CR17762-91]]');
			$new_words[] = array (12 => '[[3Com%GPASS%4500G PWR 24-port | http://www.3com.com/products/en_US/detail.jsp?tab=features&pathtype=purchase&sku=3CR17771-91]]');
			$new_words[] = array (12 => '[[3Com%GPASS%4500G PWR 48-port | http://www.3com.com/products/en_US/detail.jsp?tab=features&pathtype=purchase&sku=3CR17772-91]]');
			$new_words[] = array (12 => '[[3Com%GPASS%5500-EI 28-port | http://www.3com.com/products/en_US/detail.jsp?tab=features&pathtype=purchase&sku=3CR17161-91]]');
			$new_words[] = array (12 => '[[3Com%GPASS%5500-EI 52-port | http://www.3com.com/products/en_US/detail.jsp?tab=features&pathtype=purchase&sku=3CR17162-91]]');
			$new_words[] = array (12 => '[[3Com%GPASS%5500-EI 28-port PWR | http://www.3com.com/products/en_US/detail.jsp?tab=features&pathtype=purchase&sku=3CR17171-91]]');
			$new_words[] = array (12 => '[[3Com%GPASS%5500-EI 52-port PWR | http://www.3com.com/products/en_US/detail.jsp?tab=features&pathtype=purchase&sku=3CR17172-91]]');
			$new_words[] = array (12 => '[[3Com%GPASS%5500-EI 28-port FX | http://www.3com.com/products/en_US/detail.jsp?tab=features&pathtype=purchase&sku=3CR17181-91]]');
			$new_words[] = array (12 => '[[3Com%GPASS%5500G-EI 24-port | http://www.3com.com/products/en_US/detail.jsp?tab=features&pathtype=purchase&sku=3CR17250-91]]');
			$new_words[] = array (12 => '[[3Com%GPASS%5500G-EI 48-port | http://www.3com.com/products/en_US/detail.jsp?tab=features&pathtype=purchase&sku=3CR17251-91]]');
			$new_words[] = array (12 => '[[3Com%GPASS%5500G-EI PWR 24-port | http://www.3com.com/products/en_US/detail.jsp?tab=features&pathtype=purchase&sku=3CR17252-91]]');
			$new_words[] = array (12 => '[[3Com%GPASS%5500G-EI 48-port PWR | http://www.3com.com/products/en_US/detail.jsp?tab=features&pathtype=purchase&sku=3CR17253-91]]');
			$new_words[] = array (12 => '[[3Com%GPASS%5500G-EI 24-port SFP | http://www.3com.com/products/en_US/detail.jsp?tab=features&pathtype=purchase&sku=3CR17258-91]]');
			$new_words[] = array (12 => '[[3Com%GPASS%7754 | http://www.3com.com/products/en_US/detail.jsp?tab=features&pathtype=purchase&sku=3C16894]]');
			$new_words[] = array (12 => '[[3Com%GPASS%7757 | http://www.3com.com/products/en_US/detail.jsp?tab=features&pathtype=purchase&sku=3C16895]]');
			$new_words[] = array (12 => '[[3Com%GPASS%7758 | http://www.3com.com/products/en_US/detail.jsp?tab=features&pathtype=purchase&sku=3C16896]]');
			$new_words[] = array (12 => '[[3Com%GPASS%8807 | http://www.3com.com/products/en_US/detail.jsp?tab=features&sku=3C17502A&pathtype=purchase]]');
			$new_words[] = array (12 => '[[3Com%GPASS%8810 | http://www.3com.com/products/en_US/detail.jsp?tab=features&sku=3C17501A&pathtype=purchase]]');
			$new_words[] = array (12 => '[[3Com%GPASS%8814 | http://www.3com.com/products/en_US/detail.jsp?tab=features&sku=3C17500A&pathtype=purchase]]');
			$new_words[] = array (13 => 'Linux%GSKIP%RHF9');
			$query[] = "insert into PortCompat (type1, type2) values (29, 681)";
			$query[] = "insert into PortCompat (type1, type2) values (29, 682)";
			$query[] = "insert into PortCompat (type1, type2) values (681, 29)";
			$query[] = "insert into PortCompat (type1, type2) values (681, 681)";
			$query[] = "insert into PortCompat (type1, type2) values (681, 682)";
			$query[] = "insert into PortCompat (type1, type2) values (682, 29)";
			$query[] = "insert into PortCompat (type1, type2) values (682, 681)";
			$query[] = "insert into PortCompat (type1, type2) values (682, 682)";
			$query[] =
"
CREATE TABLE `TagStorage` (
  `target_realm` enum('object','ipv4net','rack','ipv4vs','ipv4rspool') NOT NULL default 'object',
  `target_id` int(10) unsigned NOT NULL,
  `tag_id` int(10) unsigned default NULL,
  UNIQUE KEY `entity_tag` (`target_realm`,`target_id`,`tag_id`),
  KEY `target_id` (`target_id`)
) TYPE=MyISAM;
";
			$query[] =
"
CREATE TABLE `TagTree` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `parent_id` int(10) unsigned default NULL,
  `tag` char(255) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `tag` (`tag`)
) TYPE=MyISAM;
";
			$old_words[29] = 'async serial (RJ-45)';

			$old_words[43] = 'IBM xSeries%GPASS%305';
			$old_words[44] = 'IBM xSeries%GPASS%306';
			$old_words[45] = 'IBM xSeries%GPASS%306m';
			$old_words[46] = 'IBM xSeries%GPASS%326m';
			$old_words[47] = 'IBM xSeries%GPASS%330';
			$old_words[48] = 'IBM xSeries%GPASS%335';
			$old_words[54] = 'IBM xSeries%GPASS%346';
			$old_words[59] = 'IBM xSeries%GPASS%326';
			$old_words[68] = 'IBM xSeries%GPASS%3250';
			$old_words[69] = 'IBM xSeries%GPASS%3455';
			$old_words[70] = 'IBM xSeries%GPASS%3550';
			$old_words[71] = 'IBM xSeries%GPASS%3650';
			$old_words[72] = 'IBM xSeries%GPASS%3655';
			$old_words[73] = 'IBM xSeries%GPASS%3650 T';
			$old_words[74] = 'IBM xSeries%GPASS%3755';
			$old_words[75] = 'IBM xSeries%GPASS%3850';

			$old_words[92] = 'IBM pSeries%GPASS%185';
			$old_words[93] = 'IBM pSeries%GPASS%505';
			$old_words[94] = 'IBM pSeries%GPASS%505Q';
			$old_words[95] = 'IBM pSeries%GPASS%510';
			$old_words[96] = 'IBM pSeries%GPASS%510Q';
			$old_words[97] = 'IBM pSeries%GPASS%520';
			$old_words[98] = 'IBM pSeries%GPASS%520Q';
			$old_words[99] = 'IBM pSeries%GPASS%550';
			$old_words[100] = 'IBM pSeries%GPASS%550Q';

			$old_words[212] = 'Linux%GSKIP%RHFC1';
			$old_words[213] = 'Linux%GSKIP%RHFC2';
			$old_words[214] = 'Linux%GSKIP%RHFC3';
			$old_words[215] = 'Linux%GSKIP%RHFC4';
			$old_words[216] = 'Linux%GSKIP%RHFC5';
			$old_words[217] = 'Linux%GSKIP%RHFC6';
			$old_words[232] = 'Linux%GSKIP%RHF7';
			$old_words[242] = 'Linux%GSKIP%RHF8';
			$old_words[225] = 'Linux%GSKIP%RHEL1';
			$old_words[226] = 'Linux%GSKIP%RHEL2';
			$old_words[227] = 'Linux%GSKIP%RHEL3';
			$old_words[228] = 'Linux%GSKIP%RHEL4';
			$old_words[436] = 'Linux%GSKIP%RHEL5';

			$old_words[229] = 'Linux%GSKIP%ALTLinux Master 2.0';
			$old_words[230] = 'Linux%GSKIP%ALTLinux Master 2.2';
			$old_words[231] = 'Linux%GSKIP%ALTLinux Master 2.4';
			$old_words[243] = 'Linux%GSKIP%ALTLinux Master 4.0';
			$old_words[231] = 'Linux%GSKIP%ALTLinux Master 2.4';
			$old_words[422] = 'Linux%GSKIP%ALTLinux Server 4.0';
			$old_words[423] = 'Linux%GSKIP%ALTLinux Sisyphus';

			$old_words[233] = 'Linux%GSKIP%SLES10';
			$old_words[424] = 'Linux%GSKIP%openSUSE 10.0';
			$old_words[425] = 'Linux%GSKIP%openSUSE 10.1';
			$old_words[426] = 'Linux%GSKIP%openSUSE 10.2';
			$old_words[427] = 'Linux%GSKIP%openSUSE 10.3';

			$old_words[428] = 'Linux%GSKIP%Ubuntu 4.10';
			$old_words[429] = 'Linux%GSKIP%Ubuntu 5.04';
			$old_words[430] = 'Linux%GSKIP%Ubuntu 5.10';
			$old_words[431] = 'Linux%GSKIP%Ubuntu 6.06 LTS';
			$old_words[432] = 'Linux%GSKIP%Ubuntu 6.10';
			$old_words[433] = 'Linux%GSKIP%Ubuntu 7.04';
			$old_words[434] = 'Linux%GSKIP%Ubuntu 7.10';
			$old_words[435] = 'Linux%GSKIP%Ubuntu 8.04 LTS';

			$old_words[418] = 'Linux%GSKIP%Debian 2.0 (hamm)';
			$old_words[419] = 'Linux%GSKIP%Debian 2.1 (slink)';
			$old_words[420] = 'Linux%GSKIP%Debian 2.2 (potato)';
			$old_words[234] = 'Linux%GSKIP%Debian 3.0 (woody)';
			$old_words[235] = 'Linux%GSKIP%Debian 3.1 (sarge)';
			$old_words[421] = 'Linux%GSKIP%Debian 4.0 (etch)';

			$old_words[236] = 'BSD%GSKIP%FreeBSD 1.x';
			$old_words[237] = 'BSD%GSKIP%FreeBSD 2.x';
			$old_words[238] = 'BSD%GSKIP%FreeBSD 3.x';
			$old_words[239] = 'BSD%GSKIP%FreeBSD 4.x';
			$old_words[240] = 'BSD%GSKIP%FreeBSD 5.x';
			$old_words[241] = 'BSD%GSKIP%FreeBSD 6.x';
			$old_words[732] = '[[BSD%GSKIP%FreeBSD 7.0 | http://www.freebsd.org/releases/7.0R/announce.html]]';

			$old_words[441] = 'Linux%GSKIP%CentOS-2';
			$old_words[442] = 'Linux%GSKIP%CentOS-3';
			$old_words[443] = 'Linux%GSKIP%CentOS-4';
			$old_words[444] = 'Linux%GSKIP%CentOS-5';

			$old_words[218] = 'BSD%GSKIP%Solaris 8';
			$old_words[219] = 'BSD%GSKIP%Solaris 9';
			$old_words[220] = 'BSD%GSKIP%Solaris 10';

			$old_words[49] = 'Sun%GPASS%Ultra 10';
			$old_words[50] = 'Sun%GPASS%Enterprise 420R';
			$old_words[51] = '[[Sun%GPASS%Fire X2100 | http://www.sun.com/servers/entry/x2100/]]';
			$old_words[52] = '[[Sun%GPASS%Fire E4900 | http://www.sun.com/servers/midrange/sunfire_e4900/index.xml]]';
			$old_words[53] = 'Sun%GPASS%Netra X1';
			$old_words[57] = 'Sun%GPASS%Fire V210';
			$old_words[58] = 'Sun%GPASS%Fire V240';
			$old_words[60] = 'Sun%GPASS%Netra t1 105';
			$old_words[61] = 'Sun%GPASS%Enterprise 4500';
			$old_words[64] = 'Sun%GPASS%Ultra 5';
			$old_words[76] = '[[Sun%GPASS%Fire X4600 | http://www.sun.com/servers/x64/x4600/]]';
			$old_words[77] = '[[Sun%GPASS%Fire X4500 | http://www.sun.com/servers/x64/x4500/]]';
			$old_words[78] = '[[Sun%GPASS%Fire X4200 | http://www.sun.com/servers/entry/x4200/]]';
			$old_words[79] = '[[Sun%GPASS%Fire X4100 | http://www.sun.com/servers/entry/x4100/]]';
			$old_words[80] = '[[Sun%GPASS%Fire X2100 M2 | http://www.sun.com/servers/entry/x2100/]]';
			$old_words[81] = '[[Sun%GPASS%Fire X2200 M2 | http://www.sun.com/servers/x64/x2200/]]';
			$old_words[82] = 'Sun%GPASS%Fire V40z';
			$old_words[83] = 'Sun%GPASS%Fire V125';
			$old_words[84] = '[[Sun%GPASS%Fire V215 | http://www.sun.com/servers/entry/v215/]]';
			$old_words[85] = '[[Sun%GPASS%Fire V245 | http://www.sun.com/servers/entry/v245/]]';
			$old_words[86] = '[[Sun%GPASS%Fire V445 | http://www.sun.com/servers/entry/v445/]]';
			$old_words[87] = 'Sun%GPASS%Fire V440';
			$old_words[88] = '[[Sun%GPASS%Fire V490 | http://www.sun.com/servers/midrange/v490/]]';
			$old_words[89] = '[[Sun%GPASS%Fire V890 | http://www.sun.com/servers/midrange/v890/]]';
			$old_words[90] = '[[Sun%GPASS%Fire E2900 | http://www.sun.com/servers/midrange/sunfire_e2900/index.xml]]';
			$old_words[91] = 'Sun%GPASS%Fire V1280';

			$old_words[55] = 'Dell PowerEdge%GPASS%1650';
			$old_words[56] = 'Dell PowerEdge%GPASS%2850';
			$old_words[62] = 'Dell PowerEdge%GPASS%1950';
			$old_words[63] = 'Dell PowerEdge%GPASS%1550';
			$old_words[65] = 'Dell PowerEdge%GPASS%2950';
			$old_words[66] = 'Dell PowerEdge%GPASS%650';
			$old_words[67] = 'Dell PowerEdge%GPASS%4600';
			$old_words[355] = 'Dell PowerEdge%GPASS%6850';
			$old_words[356] = 'Dell PowerEdge%GPASS%6950';
			$old_words[357] = 'Dell PowerEdge%GPASS%R900';
			$old_words[358] = 'Dell PowerEdge%GPASS%4400';
			$old_words[359] = 'Dell PowerEdge%GPASS%2650';
			$old_words[360] = 'Dell PowerEdge%GPASS%2550';
			$old_words[361] = 'Dell PowerEdge%GPASS%750';
			$old_words[362] = 'Dell PowerEdge%GPASS%2450';
			$old_words[363] = 'Dell PowerEdge%GPASS%850';
			$old_words[364] = 'Dell PowerEdge%GPASS%1850';
			$old_words[365] = 'Dell PowerEdge%GPASS%860';
			$old_words[366] = 'Dell PowerEdge%GPASS%2900';
			$old_words[367] = 'Dell PowerEdge%GPASS%2970';
			$old_words[368] = 'Dell PowerEdge%GPASS%SC1435';

			$old_words[101] = '[[HP ProLiant%GPASS%DL140 | http://h10010.www1.hp.com/wwpc/us/en/en/WF05a/15351-15351-3328412-241644-3328421-1842838.html]]';
			$old_words[102] = '[[HP ProLiant%GPASS%DL145 | http://h10010.www1.hp.com/wwpc/us/en/en/WF05a/15351-15351-3328412-241644-3328421-3219755.html]]';
			$old_words[103] = '[[HP ProLiant%GPASS%DL320 | http://h10010.www1.hp.com/wwpc/us/en/en/WF05a/15351-15351-3328412-241644-241475-3201178.html]]';
			$old_words[104] = '[[HP ProLiant%GPASS%DL360 | http://h10010.www1.hp.com/wwpc/us/en/en/WF05a/15351-15351-3328412-241644-241475-1121486.html]]';
			$old_words[105] = '[[HP ProLiant%GPASS%DL380 | http://h10010.www1.hp.com/wwpc/us/en/en/WF05a/15351-15351-3328412-241644-241475-1121516.html]]';
			$old_words[106] = '[[HP ProLiant%GPASS%DL385 | http://h10010.www1.hp.com/wwpc/us/en/en/WF05a/15351-15351-3328412-241644-241475-3219233.html]]';
			$old_words[107] = '[[HP ProLiant%GPASS%DL580 | http://h10010.www1.hp.com/wwpc/us/en/en/WF05a/15351-15351-3328412-241644-3328422-3454575.html]]';
			$old_words[108] = '[[HP ProLiant%GPASS%DL585 | http://h10010.www1.hp.com/wwpc/us/en/en/WF05a/15351-15351-3328412-241644-3328422-3219717.html]]';
			$old_words[109] = '[[HP ProLiant%GPASS%ML110 | http://h10010.www1.hp.com/wwpc/us/en/en/WF05a/15351-15351-241434-241646-3328424-3577708.html]]';
			$old_words[110] = '[[HP ProLiant%GPASS%ML150 | http://h10010.www1.hp.com/wwpc/us/en/en/WF05a/15351-15351-241434-241646-3328424-3580609.html]]';
			$old_words[111] = '[[HP ProLiant%GPASS%ML310 | http://h10010.www1.hp.com/wwpc/us/en/en/WF05a/15351-15351-241434-241646-241477-3580655.html]]';
			$old_words[112] = '[[HP ProLiant%GPASS%ML350 | http://h10010.www1.hp.com/wwpc/us/en/en/WF05a/15351-15351-241434-241646-241477-1121586.html]]';
			$old_words[113] = '[[HP ProLiant%GPASS%ML370 | http://h10010.www1.hp.com/wwpc/us/en/en/WF05a/15351-15351-241434-241646-241477-1121474.html]]';
			$old_words[114] = '[[HP ProLiant%GPASS%ML570 | http://h10010.www1.hp.com/wwpc/us/en/en/WF05a/15351-15351-241434-241646-3328425-1842779.html]]';
			$old_words[534] = '[[HP ProLiant%GPASS%DL160 | http://h10010.www1.hp.com/wwpc/us/en/en/WF05a/15351-15351-3328412-241644-3328421-3580694.html]]';
			$old_words[535] = '[[HP ProLiant%GPASS%DL180 | http://h10010.www1.hp.com/wwpc/us/en/en/WF05a/15351-15351-3328412-241644-3328421-3580698.html]]';
			$old_words[536] = '[[HP ProLiant%GPASS%DL185 | http://h10010.www1.hp.com/wwpc/us/en/en/WF05a/15351-15351-3328412-241644-3328421-3579900.html]]';
			$old_words[537] = '[[HP ProLiant%GPASS%DL365 | http://h10010.www1.hp.com/wwpc/us/en/en/WF05a/15351-15351-3328412-241644-241475-3186080.html]]';
			$old_words[538] = '[[HP ProLiant%GPASS%DL320s | http://h10010.www1.hp.com/wwpc/us/en/en/WF05a/15351-15351-3328412-241644-241475-3232017.html]]';
			$old_words[539] = '[[HP ProLiant%GPASS%DL320p | http://h10010.www1.hp.com/wwpc/us/en/en/WF05a/15351-15351-3328412-241644-241475-3579703.html]]';
			$old_words[540] = '[[HP ProLiant%GPASS%ML115 | http://h10010.www1.hp.com/wwpc/us/en/en/WF05a/15351-15351-241434-241646-3328424-3330535.html]]';

			foreach ($new_words as $dict_key => $tmp)
				foreach ($tmp as $chapter_no => $dict_value)
					$query[] = 'INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) ' .
						"VALUES (${chapter_no}, ${dict_key}, '${dict_value}')";
			foreach ($old_words as $dict_key => $dict_value)
				$query[] = "update Dictionary set dict_value = '${dict_value}' where dict_key = ${dict_key} limit 1";
			$query[] = "alter table Rack add unique name_in_row (row_id, name)";
			$query[] = "delete from UserPermission where page = 'help'";
			$query[] = "alter table Config change column varvalue varvalue char(255) NOT NULL";
			$query[] = "INSERT INTO `Config` (varname, varvalue, vartype, emptyok, is_hidden, description) VALUES ('SHOW_EXPLICIT_TAGS','yes','string','no','no','Show explicit tags')";
			$query[] = "INSERT INTO `Config` (varname, varvalue, vartype, emptyok, is_hidden, description) VALUES ('SHOW_IMPLICIT_TAGS','yes','string','no','no','Show implicit tags')";
			$query[] = "INSERT INTO `Config` (varname, varvalue, vartype, emptyok, is_hidden, description) VALUES ('SHOW_AUTOMATIC_TAGS','no','string','no','no','Show automatic tags')";
			// Decommission the Protocols dictionary chapter.
			$query[] = "alter table PortForwarding add column proto_new enum('TCP','UDP') not null default 'TCP' after proto";
			$query[] = "update PortForwarding set proto_new = 'TCP' where proto = 336";
			$query[] = "update PortForwarding set proto_new = 'UDP' where proto = 337";
			$query[] = "alter table PortForwarding drop primary key";
			$query[] = "alter table PortForwarding drop column proto";
			$query[] = "alter table PortForwarding change column proto_new proto enum('TCP','UDP') not null default 'TCP'";
			$query[] = "alter table PortForwarding add primary key (`object_id`,`proto`,`localip`,`localport`,`remoteip`,`remoteport`)";
			$query[] = "delete from Dictionary where chapter_no = 20";
			$query[] = "delete from Chapter where chapter_no = 20";
			$query[] = "update Config set varvalue = '0.15.0' where varname = 'DB_VERSION'";
			break; // --------------------------------------------
		case '0.15.1':
			$query[] = "INSERT INTO `Config` VALUES ('IPV4_AUTO_RELEASE','1','uint','no','no','Auto-release IPv4 addresses on allocation')";
			$query[] = "update Config set varvalue = '0.15.1' where varname = 'DB_VERSION'";
			break;
		case '0.16.0':
			if (!defined ('MB_CASE_LOWER'))
			{
				die ('<b>Cannot upgrade due to multibyte extension not present. See the README for details.</b>');
			}
			$query[] = 'alter table TagStorage modify column tag_id int(10) unsigned not null;';
			$query[] = "alter table TagStorage modify column target_realm enum('object','ipv4net','rack','ipv4vs','ipv4rspool','user') NOT NULL default 'object'";
			$query[] = "create table Script (script_name char(64) not null primary key, script_text text)";
			// Do the same getUserPermissions() does, but without the function.
			// We need to generate more specific rules first, otherwise they will
			// never work.
			$tq =
				"select UserPermission.user_id, user_name, page, tab, access from " .
				"UserPermission natural left join UserAccount where (user_name is not null) or " .
				"(user_name is null and UserPermission.user_id = 0) order by user_id desc, page desc, tab desc";
			$tr = $dbxlink->query ($tq);
			$code = "allow {\$userid_1}\ndeny true\n";
			// copied and pasted from fixContext()
			$pmap = array
			(
				'accounts' => 'userlist',
				'rspools' => 'ipv4rsplist',
				'rspool' => 'ipv4rsp',
				'vservices' => 'ipv4vslist',
				'vservice' => 'ipv4vs',
			);
			$tmap = array();
			$tmap['objects']['newmulti'] = 'addmore';
			$tmap['objects']['newobj'] = 'addmore';
			$tmap['object']['switchvlans'] = 'livevlans';
			$tmap['object']['slb'] = 'editrspvs';
			$tmap['object']['portfwrd'] = 'nat4';
			$tmap['object']['network'] = 'ipv4';
			while ($row = $tr->fetch (PDO::FETCH_ASSOC))
			{
				// map, if appropriate
				$row['page'] = isset ($pmap[$row['page']]) ? $pmap[$row['page']] : $row['page'];
				$row['tab'] = isset ($tmap[$row['page']][$row['tab']]) ? $tmap[$row['page']][$row['tab']] : $row['tab'];
				// build a rule
				$conjunction = '';
				$rule = $row['access'] == 'yes' ? 'allow' : 'deny';
				if ($row['user_id'] != 0)
				{
					$rule .= " {\$username_${row['user_name']}}";
					$conjunction = 'and ';
				}
				if ($row['page'] != '%')
				{
					$rule .= " ${conjunction}{\$page_${row['page']}}";
					$conjunction = 'and ';
				}
				if ($row['tab'] != '%')
					$rule .= " ${conjunction}{\$tab_${row['tab']}}";
				if ($rule == 'allow' or $rule == 'deny')
					continue;
				$code .= "${rule}\n";
			}
			$query[] = "insert into Script (script_name, script_text) values ('RackCode', '${code}')";
			$query[] = 'drop table UserPermission';
			$new_words[791] = array (13 => '[[Linux%GSKIP%openSUSE 11.0 | http://en.opensuse.org/OpenSUSE_11.0]]');
			$new_words[] = array (11 => '[[SGI%GPASS%Altix XE250 | http://www.sgi.com/products/servers/altix/xe/configs.html]]');
			$new_words[] = array (11 => '[[SGI%GPASS%Altix XE310 | http://www.sgi.com/products/servers/altix/xe/configs.html]]');
			$new_words[] = array (11 => '[[SGI%GPASS%Altix XE320 | http://www.sgi.com/products/servers/altix/xe/configs.html]]');
			foreach ($new_words as $dict_key => $tmp)
				foreach ($tmp as $chapter_no => $dict_value)
					$query[] = 'INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) ' .
						"VALUES (${chapter_no}, ${dict_key}, '${dict_value}')";
			$query[] = "update Config set varvalue = '0.16.0' where varname = 'DB_VERSION'";
			break;
		case '0.16.1':
			$query[] = 'alter table Script modify column script_text longtext';
			$query[] = "INSERT INTO `Config` (varname, varvalue, vartype, emptyok, is_hidden, description) VALUES ('SHOW_LAST_TAB','no','string','yes','no','Remember last tab shown for each page')";
			$query[] = "INSERT INTO `Config` (varname, varvalue, vartype, emptyok, is_hidden, description) VALUES ('COOKIE_TTL','1209600','uint','yes','no','Cookies lifetime in seconds')";
			$query[] = "update Config set varvalue = '0.16.1' where varname = 'DB_VERSION'";
			break;
		case '0.16.2':
			$query[] = "alter table IPBonds modify column type enum('regular','shared','virtual','router')";
			$query[] = "update Dictionary set dict_value = 'spacer' where dict_key = 11";
			$query[] = "INSERT INTO `Config` (varname, varvalue, vartype, emptyok, is_hidden, description) VALUES ('EXT_IPV4_VIEW','yes','string','no','no','Display parent network info for IPv4 addresses')";
			$query[] = "ALTER TABLE RackSpace ADD KEY `RackSpace_object_id` (`object_id`)";
			$query[] = "update Config set varvalue = '0.16.2' where varname = 'DB_VERSION'";
			break;
		case '0.16.3':
			// The network table list used to allow duplicate "prefix-masklen" pairs, which was a bad idea.
			// The code, which verified each new network to be "unique", didn't work for "upper" IPv4 space.
			// To enable the relevant index now, it is necessary to process all ghost networks, which could
			// be accumulated over the time, and move all tags assigned to them to the "master" record, which
			// we recognize to be the one with the lowest ID.
			$q = 'select ip, mask, count(*) as c from IPRanges group by ip, mask having c > 1';
			$r = $dbxlink->query ($q);
			// Let's hope there's not many dupes, cause sub-queries won't work.
			$dupes = $r->fetchAll (PDO::FETCH_ASSOC);
			unset ($r);
			foreach ($dupes as $d)
			{
				$firstid = 0;
				$q = "select id from IPRanges where ip = ${d['ip']} and mask = ${d['mask']} order by id";
				$r = $dbxlink->query ($q);
				while ($row = $r->fetch (PDO::FETCH_ASSOC))
				{
					if (!$firstid)
					{
						$firstid = $row['id'];
						continue;
					}
					// Rewrite tags, but don't rebuild the chains. Let regular code sort it out.
					// One of the next two queries will fail.
					$query[] = "update TagStorage set target_id = ${firstid} where target_id = ${row['id']} and target_realm = 'ipv4net'";
					$query[] = "delete from TagStorage where target_id = ${row['id']} and target_realm = 'ipv4net'";
					$query[] = "delete from IPRanges where id = ${row['id']}";
				}
				unset ($r);
			}
			$query[] = 'alter table IPRanges add unique `base-len` (`ip`, `mask`)';
			$query[] = 'alter table IPVirtualService drop key `endpoint`';
			// fix the default value (only seen when upgrading from pre-0.16.0 to pre-0.16.3)
			$query[] = "alter table TagStorage modify column target_realm enum('object','ipv4net','rack','ipv4vs','ipv4rspool','user') NOT NULL default 'object'";
			$query[] = "INSERT INTO `Config` (varname, varvalue, vartype, emptyok, is_hidden, description) VALUES ('TREE_THRESHOLD','25','uint','yes','no','Tree view auto-collapse threshold')";
			$query[] = "INSERT INTO `Config` (varname, varvalue, vartype, emptyok, is_hidden, description) VALUES ('IPV4_JAYWALK','no','string','no','no','Enable IPv4 address allocations w/o covering network')";
			$query[] = "INSERT INTO `Config` (varname, varvalue, vartype, emptyok, is_hidden, description) VALUES ('ADDNEW_AT_TOP','yes','string','no','no','Render \"add new\" line at top of the list')";
			$query[] = "update Config set description = 'Extended IPv4 view' where varname = 'EXT_IPV4_VIEW'";
			$query[] = "update Config set varvalue = '0.16.3' where varname = 'DB_VERSION'";
			break;
		case '0.16.4':
			$query[] = "INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (12,795,'[[Cisco%GPASS%Catalyst 3032-DEL | http://www.cisco.com/en/US/products/ps8772/index.html]]')";
			$query[] = "INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (13,796,'Linux%GSKIP%Ubuntu 8.10')";
			$query[] = "INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (13,797,'[[BSD%GSKIP%OpenBSD 4.4 | http://openbsd.org/44.html]]')";
			$query[] = "update Config set varvalue = '0.16.4' where varname = 'DB_VERSION'";
			break;
		default:
			showError ("executeUpgradeBatch () failed, because batch '${batchid}' isn't defined", __FILE__);
			die;
			break;
	}
	$failures = array();
	$ndots = 0;
	echo "<pre>Executing database upgrade batch '${batchid}':\n";
	foreach ($query as $q)
	{
		$result = $dbxlink->query ($q);
		if ($result != NULL)
			echo '.';
		else
		{
			echo '!';
			$errorInfo = $dbxlink->errorInfo();
			$failures[] = array ($q, $errorInfo[2]);
		}
		if (++$ndots == 50)
		{
			echo "\n";
			flush();
			$ndots = 0;
		}
	}
	echo '<br>';
	if (!count ($failures))
		echo "No errors!\n";
	else
	{
		echo "The following queries failed:\n<font color=red>";
		foreach ($failures as $f)
		{
			list ($q, $i) = $f;
			echo "${q} // ${i}\n";
		}
	}
	echo '</font></pre>';
}

// ******************************************************************
//
//                  Execution starts here
//
// ******************************************************************

$root = (empty($_SERVER['HTTPS'])?'http':'https').
	'://'.
	(isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:($_SERVER['SERVER_NAME'].($_SERVER['SERVER_PORT']=='80'?'':$_SERVER['SERVER_PORT']))).
	dirname($_SERVER['PHP_SELF']);
if (substr ($root, -1) != '/')
	$root .= '/';

// The below will be necessary as long as we rely on showError()
require_once 'inc/interface.php';

require_once 'inc/config.php';
require_once 'inc/database.php';
if (file_exists ('inc/secret.php'))
	require_once 'inc/secret.php';
else
	die ("Database connection parameters are read from inc/secret.php file, " .
		"which cannot be found.\nCopy provided inc/secret-sample.php to " .
		"inc/secret.php and modify to your setup.\n\nThen reload the page.");

try
{
	$dbxlink = new PDO ($pdo_dsn, $db_username, $db_password);
}
catch (PDOException $e)
{
	die ("Database connection failed:\n\n" . $e->getMessage());
}

// Now we need to be sure that the current user is the administrator.
// The rest doesn't matter within this context.
// We still continue to use the current authenticator though, but this will
// last only till the UserAccounts remains the same. After that this file
// will have to dig into the DB for the user accounts.
require_once 'inc/auth.php';

// 1. This didn't fail sanely, because getUserAccounts() depended on showError()
// 2. getUserAccounts() doesn't work for old DBs since 0.16.0. Let's have own
// copy until it breaks too.

function getUserAccounts_local ()
{
	global $dbxlink;
	$query = 'select user_id, user_name, user_password_hash from UserAccount order by user_name';
	if (($result = $dbxlink->query ($query)) == NULL)
		die ('SQL query failed in ' . __FUNCTION__);
	$ret = array();
	while ($row = $result->fetch (PDO::FETCH_ASSOC))
		foreach (array ('user_id', 'user_name', 'user_password_hash') as $cname)
			$ret[$row['user_name']][$cname] = $row[$cname];
	return $ret;
}

$accounts = getUserAccounts_local();

// Only administrator is always authenticated locally, so reject others
// for authenticate() to succeed.

if
(
	!isset ($_SERVER['PHP_AUTH_USER']) or
	!isset ($_SERVER['PHP_AUTH_PW']) or
	$accounts[$_SERVER['PHP_AUTH_USER']]['user_id'] != 1 or
	!authenticated_via_database (escapeString ($_SERVER['PHP_AUTH_USER']), escapeString ($_SERVER['PHP_AUTH_PW']))
)
{
	header ('WWW-Authenticate: Basic realm="RackTables upgrade"');
	header ('HTTP/1.0 401 Unauthorized');
	showError ('You must be authenticated as an administrator to complete the upgrade.', __FILE__);
	die;
}

$dbver = getDatabaseVersion();
echo 'Code version: ' . CODE_VERSION . '<br>';
echo 'Database version: ' . $dbver . '<br>';
if ($dbver == CODE_VERSION)
{
	die ("<p align=justify>No action is necessary. " .
		"Proceed to the <a href='${root}'>main page</a>, " .
		"check your data and have a nice day.</p>");
}

foreach (getDBUpgradePath ($dbver, CODE_VERSION) as $batchid)
{
	executeUpgradeBatch ($batchid);
	printReleaseNotes ($batchid);
}

echo '<br>Database version == ' . getDatabaseVersion();
echo "<p align=justify>Your database seems to be up-to-date. " .
	"Now the best thing to do would be to follow to the <a href='${root}'>main page</a> " .
	"and explore your data. Have a nice day.</p>";

?>
