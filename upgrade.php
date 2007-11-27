<?php

// At the moment we assume, that for any two releases we can
// sequentally execute all batches, that separate them, and
// nothing will break. If this changes one day, the function
// below will have to generate smarter upgrade paths, while
// the upper layer will remain the same.
// Returning an empty array means that no upgrade is necessary.
function getDBUpgradePath ($v1, $v2)
{
	$versionhistory = array ('0.14.4', '0.14.5', '0.14.6');
	if (!in_array ($v1, $versionhistory) || !in_array ($v2, $versionhistory))
	{
		showError ("An upgrade path has been requested for versions '${v1}' and '${v2}', " .
		  "and at least one of those isn't known to me.");
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
			$query[] = 'create table Dictionary_0_14_7_new (chapter_no int(10) unsigned NOT NULL, dict_key int(10) unsigned NOT NULL auto_increment PRIMARY KEY, dict_value char(128) default NULL)';

echo '<pre>';
			// Find all chapter numbers, which will require AttributeValue adjustment.
			$q2 = 'select distinct chapter_no from AttributeMap where chapter_no != 0';
			$r2 = $dbxlink->query ($q2);
			$chaplist = array();
			while ($row = $r2->fetch (PDO::FETCH_NUM))
				$chaplist[] = $row[0];
print_r ($chaplist);
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
				110, 111, 112, 113
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
			$newkey = 10000;
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
echo "Processing chapter ${chapter_no}\n";
				foreach ($words as $oldkey => $data)
				{
					$value = $data['value'];
					$newkey = $data['newkey'];
					// Even if the key doesn't change, go on to have
					// AttributeMap regenerated completely.
echo "oldkey == ${oldkey} newkey == ${newkey} value == ${value}\n";
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
						$q81 = "select object_id, attr_id from " .
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

			// We are done.
#			$query[] = "update Config set varvalue = '0.14.7' where varname = 'DB_VERSION'";
			break; // --------------------------------------------
		default:
			showError ("executeUpgradeBatch () failed, because batch '${batchid}' isn't defined");
			die;
			break;
	}
	$failures = array();
	$ndots = 0;
	echo "<pre>Executing database upgrade batch '${batchid}: ";
print_r ($query);
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
			$ndots = 0;
		}
	}
	echo '<br>';
	if (!count ($failures))
		echo "No errors!\n";
	else
	{
		echo "The following queries failed:\n";
		foreach ($failures as $f)
		{
			list ($q, $i) = $f;
			echo "${q} // ${i}\n";
		}
	}
	echo '</pre>';
}

// ******************************************************************
//
//                  Execution starts here
//
// ******************************************************************

$root = (empty($_SERVER['HTTPS'])?'http':'https').
	'://'.
	(isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:($_SERVER['SERVER_NAME'].($_SERVER['SERVER_PORT']=='80'?'':$_SERVER['SERVER_PORT']))).
	dirname($_SERVER['PHP_SELF']).'/';

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

if (isset ($_SERVER['PHP_AUTH_USER']))
	$_SERVER['PHP_AUTH_USER'] = escapeString ($_SERVER['PHP_AUTH_USER']);
if (isset ($_SERVER['PHP_AUTH_PW']))
	$_SERVER['PHP_AUTH_PW'] = escapeString ($_SERVER['PHP_AUTH_PW']);

// Now we need to be sure that the current user is the administrator.
// The rest doesn't matter within this context.
// We still continue to use the current authenticator though, but this will
// last only till the UserAccounts remains the same. After that this file
// will have to dig into the DB for the user accounts.
require_once 'inc/auth.php';

// This will not fail sanely, because getUserAccounts() depends on showError()
$accounts = getUserAccounts();

// Auth prompt risk being a little broken here due to config cache absence.
$configCache = array();
authenticate();
if ($accounts[$_SERVER['PHP_AUTH_USER']]['user_id'] != 1)
	die ('You are not allowed to upgrade the database. Ask your RackTables administrator to do this.');

$dbver = getDatabaseVersion();
echo 'Code version == ' . CODE_VERSION;
echo '<br>Database version == ' . $dbver;
if ($dbver == CODE_VERSION)
{
	executeUpgradeBatch ('0.14.7');
	die ("<p align=justify>Your database seems to be up-to-date. " .
		"Now the best thing to do would be to follow to the <a href='${root}'>main page</a> " .
		"and explore your data. Have a nice day.</p>");
}

foreach (getDBUpgradePath ($dbver, CODE_VERSION) as $batchid)
	executeUpgradeBatch ($batchid);

echo '<br>Database version == ' . getDatabaseVersion();
echo "<p align=justify>Your database seems to be up-to-date. " .
	"Now the best thing to do would be to follow to the <a href='${root}'>main page</a> " .
	"and explore your data. Have a nice day.</p>";

?>
