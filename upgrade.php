<?php

$relnotes = array
(
	'0.17.0' => <<<ENDOFTEXT
LDAP options have been moved to LDAP_options array. This means, that if you were
using LDAP authentication for users in version 0.16.x, it will break right after
upgrade to 0.17.0. To get things working again, adjust existing secret.php file
according to secret-sample.php file provided with 0.17.0 release.

This release is the first to take advantage of the foreign key support
provided by the InnoDB storage engine in MySQL.  The installer and
upgrader scripts check for InnoDB support and cannot complete without it.
If you have trouble, the first step is to make sure the 'skip-innodb'
option in my.cnf is commented out.

Another change is the addition of support for file uploads.  Files are stored
in the database.  There are several settings in php.ini which you may need to modify:
    file_uploads        - needs to be On
    upload_max_filesize - max size for uploaded files
    post_max_size       - max size of all form data submitted via POST (including files)

User accounts used to have 'enabled' flag, which allowed individual blocking and
unblocking of each. This flag was dropped in favor of existing mean of access
setup (RackCode). An unconditional denying rule is automatically added into RackCode
for such blocked account, so the effective security policy remains the same.
ENDOFTEXT
,
	'0.18.2' => <<<ENDOFTEXT
RackTables from its version 0.18.0 and later is not compatible with
RHEL/CentOS (at least with versions up to 5.5) Linux distributions
in their default installation. There are yet options to work around that:
1. Install RackTables on a server with a different distribution/OS.
2. Request Linux distribution vendor to fix the bug with PCRE.
3. Repair your RHEL/CentOS installation yourself by fixing its PCRE
RPM as explained here: http://bugs.centos.org/view.php?id=3252
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
		'0.16.4',
		'0.16.5',
		'0.16.6',
		'0.17.0',
		'0.17.1',
		'0.17.2',
		'0.17.3',
		'0.17.4',
		'0.17.5',
		'0.17.6',
		'0.17.7',
		'0.17.8',
		'0.17.9',
		'0.17.10',
		'0.17.11',
		'0.18.0',
		'0.18.1',
		'0.18.2',
		'0.18.3',
	);
	if (!in_array ($v1, $versionhistory) or !in_array ($v2, $versionhistory))
		return NULL;
	$skip = TRUE;
	$path = NULL;
	// Now collect all versions > $v1 and <= $v2
	foreach ($versionhistory as $v)
	{
		if ($skip and $v == $v1)
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
function executeUpgradeBatch ($batchid)
{
	$query = array();
	global $dbxlink;
	switch ($batchid)
	{
		case '0.16.5':
			$query[] = "INSERT INTO `Config` (varname, varvalue, vartype, emptyok, is_hidden, description) VALUES ('IPV4_TREE_SHOW_USAGE','yes','string','no','no','Show address usage in IPv4 tree')";
			$query[] = "update Config set varvalue = '0.16.5' where varname = 'DB_VERSION'";
			break;
		case '0.16.6':
			$query[] = "update Config set varvalue = '0.16.6' where varname = 'DB_VERSION'";
			break;
		case '0.17.0':
			// create tables for storing files (requires InnoDB support)
			if (!isInnoDBSupported ())
			{
				showFailure ("Cannot upgrade because InnoDB tables are not supported by your MySQL server. See the README for details.", __FILE__);
				die;
			}

			$query[] = "alter table Chapter change chapter_no id int(10) unsigned NOT NULL auto_increment";
			$query[] = "alter table Chapter change chapter_name name char(128) NOT NULL";
			$query[] = "alter table Chapter drop key chapter_name";
			$query[] = "alter table Chapter add UNIQUE KEY name (name)";
			$query[] = "alter table Attribute change attr_id id int(10) unsigned NOT NULL auto_increment";
			$query[] = "alter table Attribute change attr_type type enum('string','uint','float','dict') default NULL";
			$query[] = "alter table Attribute change attr_name name char(64) default NULL";
			$query[] = "alter table Attribute drop key attr_name";
			$query[] = "alter table Attribute add UNIQUE KEY name (name)";
			$query[] = "alter table AttributeMap change chapter_no chapter_id int(10) unsigned NOT NULL";
			$query[] = "alter table Dictionary change chapter_no chapter_id int(10) unsigned NOT NULL";
			// Only after the above call it is Ok to use reloadDictionary()
			$query = array_merge ($query, reloadDictionary ($batchid));
			// schema changes for file management
			$query[] = "
CREATE TABLE `File` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` char(255) NOT NULL,
  `type` char(255) NOT NULL,
  `size` int(10) unsigned NOT NULL,
  `ctime` datetime NOT NULL,
  `mtime` datetime NOT NULL,
  `atime` datetime NOT NULL,
  `contents` longblob NOT NULL,
  `comment` text,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB";
			$query[] = "
CREATE TABLE `FileLink` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `file_id` int(10) unsigned NOT NULL,
  `entity_type` enum('ipv4net','ipv4rspool','ipv4vs','object','rack','user') NOT NULL default 'object',
  `entity_id` int(10) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `FileLink-unique` (`file_id`,`entity_type`,`entity_id`),
  KEY `FileLink-file_id` (`file_id`),
  CONSTRAINT `FileLink-File_fkey` FOREIGN KEY (`file_id`) REFERENCES `File` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB";
			$query[] = "ALTER TABLE TagStorage MODIFY COLUMN target_realm enum('file','ipv4net','ipv4rspool','ipv4vs','object','rack','user') NOT NULL default 'object'";

			$query[] = "INSERT INTO `Chapter` (`id`, `sticky`, `name`) VALUES (24,'no','network security models')";
			$query[] = "INSERT INTO `Chapter` (`id`, `sticky`, `name`) VALUES (25,'no','wireless models')";
			$query[] = "INSERT INTO `AttributeMap` (`objtype_id`, `attr_id`, `chapter_id`) VALUES (798,1,0)";
			$query[] = "INSERT INTO `AttributeMap` (`objtype_id`, `attr_id`, `chapter_id`) VALUES (798,2,24)";
			$query[] = "INSERT INTO `AttributeMap` (`objtype_id`, `attr_id`, `chapter_id`) VALUES (798,3,0)";
			$query[] = "INSERT INTO `AttributeMap` (`objtype_id`, `attr_id`, `chapter_id`) VALUES (798,5,0)";
			$query[] = "INSERT INTO `AttributeMap` (`objtype_id`, `attr_id`, `chapter_id`) VALUES (798,14,0)";
			$query[] = "INSERT INTO `AttributeMap` (`objtype_id`, `attr_id`, `chapter_id`) VALUES (798,16,0)";
			$query[] = "INSERT INTO `AttributeMap` (`objtype_id`, `attr_id`, `chapter_id`) VALUES (798,17,0)";
			$query[] = "INSERT INTO `AttributeMap` (`objtype_id`, `attr_id`, `chapter_id`) VALUES (798,18,0)";
			$query[] = "INSERT INTO `AttributeMap` (`objtype_id`, `attr_id`, `chapter_id`) VALUES (798,20,0)";
			$query[] = "INSERT INTO `AttributeMap` (`objtype_id`, `attr_id`, `chapter_id`) VALUES (798,21,0)";
			$query[] = "INSERT INTO `AttributeMap` (`objtype_id`, `attr_id`, `chapter_id`) VALUES (798,22,0)";
			$query[] = "INSERT INTO `AttributeMap` (`objtype_id`, `attr_id`, `chapter_id`) VALUES (798,24,0)";
			$query[] = "INSERT INTO `AttributeMap` (`objtype_id`, `attr_id`, `chapter_id`) VALUES (965,1,0)";
			$query[] = "INSERT INTO `AttributeMap` (`objtype_id`, `attr_id`, `chapter_id`) VALUES (965,3,0)";
			$query[] = "INSERT INTO `AttributeMap` (`objtype_id`, `attr_id`, `chapter_id`) VALUES (965,2,25)";
			$query[] = 'alter table IPBonds rename to IPv4Allocation';
			$query[] = 'alter table PortForwarding rename to IPv4NAT';
			$query[] = 'alter table IPRanges rename to IPv4Network';
			$query[] = 'alter table IPAddress rename to IPv4Address';
			$query[] = 'alter table IPLoadBalancer rename to IPv4LB';
			$query[] = 'alter table IPRSPool rename to IPv4RSPool';
			$query[] = 'alter table IPRealServer rename to IPv4RS';
			$query[] = 'alter table IPVirtualService rename to IPv4VS';
			$query[] = "alter table TagStorage change column target_realm entity_realm enum('file','ipv4net','ipv4vs','ipv4rspool','object','rack','user') NOT NULL default 'object'";
			$query[] = 'alter table TagStorage change column target_id entity_id int(10) unsigned NOT NULL';
			$query[] = 'alter table TagStorage drop key entity_tag';
			$query[] = 'alter table TagStorage drop key target_id';
			$query[] = 'alter table TagStorage add UNIQUE KEY `entity_tag` (`entity_realm`,`entity_id`,`tag_id`)';
			$query[] = 'alter table TagStorage add KEY `entity_id` (`entity_id`)';
			$query[] = "INSERT INTO `Config` (varname, varvalue, vartype, emptyok, is_hidden, description) VALUES ('PREVIEW_TEXT_MAXCHARS','10240','uint','yes','no','Max chars for text file preview')";
			$query[] = "INSERT INTO `Config` (varname, varvalue, vartype, emptyok, is_hidden, description) VALUES ('PREVIEW_TEXT_ROWS','25','uint','yes','no','Rows for text file preview')";
			$query[] = "INSERT INTO `Config` (varname, varvalue, vartype, emptyok, is_hidden, description) VALUES ('PREVIEW_TEXT_COLS','80','uint','yes','no','Columns for text file preview')";
			$query[] = "INSERT INTO `Config` (varname, varvalue, vartype, emptyok, is_hidden, description) VALUES ('PREVIEW_IMAGE_MAXPXS','320','uint','yes','no','Max pixels per axis for image file preview')";
			$query[] = "INSERT INTO `Config` (varname, varvalue, vartype, emptyok, is_hidden, description) VALUES ('VENDOR_SIEVE','','string','yes','no','Vendor sieve configuration')";
			$query[] = "INSERT INTO `Config` (varname, varvalue, vartype, emptyok, is_hidden, description) VALUES ('IPV4LB_LISTSRC','{\$typeid_4}','string','yes','no','List source: IPv4 load balancers')";
			$query[] = "INSERT INTO `Config` (varname, varvalue, vartype, emptyok, is_hidden, description) VALUES ('IPV4OBJ_LISTSRC','{\$typeid_4} or {\$typeid_7} or {\$typeid_8} or {\$typeid_12} or {\$typeid_445} or {\$typeid_447}','string','yes','no','List source: IPv4-enabled objects')";
			$query[] = "INSERT INTO `Config` (varname, varvalue, vartype, emptyok, is_hidden, description) VALUES ('IPV4NAT_LISTSRC','{\$typeid_4} or {\$typeid_7} or {\$typeid_8}','string','yes','no','List source: IPv4 NAT performers')";
			$query[] = "INSERT INTO `Config` (varname, varvalue, vartype, emptyok, is_hidden, description) VALUES ('ASSETWARN_LISTSRC','{\$typeid_4} or {\$typeid_7} or {\$typeid_8}','string','yes','no','List source: object, for which asset tag should be set')";
			$query[] = "INSERT INTO `Config` (varname, varvalue, vartype, emptyok, is_hidden, description) VALUES ('NAMEWARN_LISTSRC','{\$typeid_4} or {\$typeid_7} or {\$typeid_8}','string','yes','no','List source: object, for which common name should be set')";
			$query[] = "INSERT INTO `Config` (varname, varvalue, vartype, emptyok, is_hidden, description) VALUES ('RACKS_PER_ROW','12','unit','yes','no','Racks per row')";
			$query[] = "INSERT INTO `Config` (varname, varvalue, vartype, emptyok, is_hidden, description) VALUES ('FILTER_PREDICATE_SIEVE','','string','yes','no','Predicate sieve regex(7)')";
			$query[] = "INSERT INTO `Config` (varname, varvalue, vartype, emptyok, is_hidden, description) VALUES ('FILTER_DEFAULT_ANDOR','or','string','no','no','Default list filter boolean operation (or/and)')";
			$query[] = "INSERT INTO `Config` (varname, varvalue, vartype, emptyok, is_hidden, description) VALUES ('FILTER_SUGGEST_ANDOR','yes','string','no','no','Suggest and/or selector in list filter')";
			$query[] = "INSERT INTO `Config` (varname, varvalue, vartype, emptyok, is_hidden, description) VALUES ('FILTER_SUGGEST_TAGS','yes','string','no','no','Suggest tags in list filter')";
			$query[] = "INSERT INTO `Config` (varname, varvalue, vartype, emptyok, is_hidden, description) VALUES ('FILTER_SUGGEST_PREDICATES','yes','string','no','no','Suggest predicates in list filter')";
			$query[] = "INSERT INTO `Config` (varname, varvalue, vartype, emptyok, is_hidden, description) VALUES ('FILTER_SUGGEST_EXTRA','no','string','no','no','Suggest extra expression in list filter')";
			$query[] = "delete from Config where varname = 'USER_AUTH_SRC'";
			$query[] = "delete from Config where varname = 'COOKIE_TTL'";
			$query[] = "delete from Config where varname = 'rtwidth_0'";
			$query[] = "delete from Config where varname = 'rtwidth_1'";
			$query[] = "delete from Config where varname = 'rtwidth_2'";
			$query[] = "delete from Config where varname = 'NAMEFUL_OBJTYPES'";
			$query[] = "delete from Config where varname = 'REQUIRE_ASSET_TAG_FOR'";
			$query[] = "delete from Config where varname = 'IPV4_PERFORMERS'";
			$query[] = "delete from Config where varname = 'NATV4_PERFORMERS'";
			$query[] = "alter table TagTree add column valid_realm set('file','ipv4net','ipv4vs','ipv4rspool','object','rack','user') not null default 'file,ipv4net,ipv4vs,ipv4rspool,object,rack,user' after parent_id";
			$result = $dbxlink->query ("select user_id, user_name, user_realname from UserAccount where user_enabled = 'no'");
			while ($row = $result->fetch (PDO::FETCH_ASSOC))
				$query[] = "update Script set script_text = concat('deny {\$userid_${row['user_id']}} # ${row['user_name']} (${row['user_realname']})\n', script_text) where script_name = 'RackCode'";
			$query[] = "update Script set script_text = NULL where script_name = 'RackCodeCache'";
			unset ($result);
			$query[] = "alter table UserAccount drop column user_enabled";

			$query[] = "CREATE TABLE RackRow ( id int(10) unsigned NOT NULL auto_increment, name char(255) NOT NULL, PRIMARY KEY  (`id`) ) ENGINE=MyISAM";

			$result = $dbxlink->query ("select dict_key, dict_value from Dictionary where chapter_no = 3");
			while($row = $result->fetch(PDO::FETCH_NUM))
				$query[] = "insert into RackRow set id=${row[0]}, name='${row[1]}'";
			unset ($result);
			$query[] = "delete from Dictionary where chapter_id = 3";
			$query[] = "delete from Chapter where id = 3";
			$query[] = "
CREATE TABLE `LDAPCache` (
  `presented_username` char(64) NOT NULL,
  `successful_hash` char(40) NOT NULL,
  `first_success` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `last_retry` timestamp NOT NULL default '0000-00-00 00:00:00',
  `displayed_name` char(128) default NULL,
  `memberof` text,
  UNIQUE KEY `presented_username` (`presented_username`),
  KEY `scanidx` (`presented_username`,`successful_hash`)
) ENGINE=InnoDB;";
			$query[] = "alter table UserAccount modify column user_password_hash char(40) NULL";
			$query[] = 'ALTER TABLE Rack DROP COLUMN deleted';
			$query[] = 'ALTER TABLE RackHistory DROP COLUMN deleted';
			$query[] = 'ALTER TABLE RackObject DROP COLUMN deleted';
			$query[] = 'ALTER TABLE RackObjectHistory DROP COLUMN deleted';
			// Can't be added straight due to many duplicates, even in "dictbase" data.
			$result = $dbxlink->query ('SELECT type1, type2, count(*) - 1 as excess FROM PortCompat GROUP BY type1, type2 HAVING excess > 0');
			while ($row = $result->fetch (PDO::FETCH_ASSOC))
				$query[] = "DELETE FROM PortCompat WHERE type1 = ${row['type1']} AND type2 = ${row['type2']} limit ${row['excess']}";
			unset ($result);
			$query[] = 'ALTER TABLE PortCompat DROP KEY type1';
			$query[] = 'ALTER TABLE PortCompat ADD UNIQUE `type1_2` (type1, type2)';
			$query[] = "UPDATE Config SET varvalue = '0.17.0' WHERE varname = 'DB_VERSION'";

			break;
		case '0.17.1':
			$query[] = "ALTER TABLE Dictionary DROP KEY `chap_to_key`";
			$query = array_merge ($query, reloadDictionary ($batchid));
			// Token set has changed, so the cache isn't valid any more.
			$query[] = "UPDATE Script SET script_text = NULL WHERE script_name = 'RackCodeCache'";
			$query[] = "UPDATE Config SET varvalue = '0.17.1' WHERE varname = 'DB_VERSION'";
			break;
		case '0.17.2':
			$query[] = "INSERT INTO `Chapter` (`id`, `sticky`, `name`) VALUES (26,'no','fibre channel switch models')";
			$query = array_merge ($query, reloadDictionary ($batchid));
			$query[] = "INSERT INTO `AttributeMap` (`objtype_id`, `attr_id`, `chapter_id`) VALUES (1055,2,26)";
			$query[] = "INSERT INTO `Config` (varname, varvalue, vartype, emptyok, is_hidden, description) VALUES ('DEFAULT_SNMP_COMMUNITY','public','string','no','no','Default SNMP Community string')";
			// wipe irrelevant records (ticket:250)
			$query[] = "DELETE FROM TagStorage WHERE entity_realm = 'file' AND entity_id NOT IN (SELECT id FROM File)";
			$query[] = "INSERT INTO `Config` (varname, varvalue, vartype, emptyok, is_hidden, description) VALUES ('IPV4_ENABLE_KNIGHT','yes','string','no','no','Enable IPv4 knight feature')";
			$query[] = "ALTER TABLE IPv4Network ADD COLUMN comment text AFTER name";
			$query[] = "ALTER TABLE Port ADD INDEX comment (reservation_comment)";
			$query[] = "ALTER TABLE Port DROP KEY l2address"; // UNIQUE
			$query[] = "ALTER TABLE Port ADD KEY (l2address)"; // not UNIQUE
			$query[] = "ALTER TABLE Port DROP KEY object_id";
			$query[] = "ALTER TABLE Port ADD UNIQUE KEY per_object (object_id, name, type)";
			$query[] = "INSERT INTO PortCompat (type1, type2) VALUES (20,1083)";
			$query[] = "INSERT INTO PortCompat (type1, type2) VALUES (21,1083)";
			$query[] = "INSERT INTO PortCompat (type1, type2) VALUES (1077,1077)";
			$query[] = "INSERT INTO PortCompat (type1, type2) VALUES (1083,20)";
			$query[] = "INSERT INTO PortCompat (type1, type2) VALUES (1083,21)";
			$query[] = "INSERT INTO PortCompat (type1, type2) VALUES (1083,1083)";
			$query[] = "INSERT INTO PortCompat (type1, type2) VALUES (1087,1087)";
			$query[] = "INSERT INTO `Chapter` (`id`, `sticky`, `name`) VALUES (27,'no','PDU models')";
			$query[] = "INSERT INTO `AttributeMap` (`objtype_id`, `attr_id`, `chapter_id`) VALUES (2,2,27)";
			$query[] = "UPDATE Config SET varvalue = '0.17.2' WHERE varname = 'DB_VERSION'";
			break;
		case '0.17.3':
			$query[] = "INSERT INTO `Config` (varname, varvalue, vartype, emptyok, is_hidden, description) VALUES ('TAGS_TOPLIST_SIZE','50','uint','yes','no','Tags top list size')";
			$query[] = "INSERT INTO `Config` (varname, varvalue, vartype, emptyok, is_hidden, description) VALUES ('TAGS_QUICKLIST_SIZE','20','uint','no','no','Tags quick list size')";
			$query[] = "INSERT INTO `Config` (varname, varvalue, vartype, emptyok, is_hidden, description) VALUES ('TAGS_QUICKLIST_THRESHOLD','50','uint','yes','no','Tags quick list threshold')";
			$query[] = "ALTER TABLE AttributeMap MODIFY COLUMN chapter_id int(10) unsigned NULL";
			$query[] = "UPDATE AttributeMap SET chapter_id = NULL WHERE attr_id IN (SELECT id FROM Attribute WHERE type != 'dict')";
			// ticket:239
			$query[] = 'UPDATE AttributeValue SET uint_value = 1018 WHERE uint_value = 731 AND attr_id IN (SELECT attr_id FROM AttributeMap WHERE chapter_id = 12)';
			$query[] = 'DELETE FROM Dictionary WHERE dict_key = 731';
			$query = array_merge ($query, reloadDictionary ($batchid));
			$query[] = "UPDATE Config SET vartype='uint' WHERE varname='RACKS_PER_ROW'";
			$query[] = "INSERT INTO `Config` (varname, varvalue, vartype, emptyok, is_hidden, description) VALUES ('ENABLE_MULTIPORT_FORM','no','string','no','no','Enable \"Add/update multiple ports\" form')";
			$query[] = "UPDATE Config SET varvalue = '0.17.3' WHERE varname = 'DB_VERSION'";
			break;
		case '0.17.4':
			$query[] = "ALTER TABLE Link ENGINE=InnoDB";
			$query[] = "ALTER TABLE Port ENGINE=InnoDB";
			$query[] = "ALTER TABLE IPv4RS ENGINE=InnoDB";
			$query[] = "ALTER TABLE IPv4RSPool ENGINE=InnoDB";
			$query[] = "ALTER TABLE AttributeValue ENGINE=InnoDB";
			$query[] = "ALTER TABLE RackObject ENGINE=InnoDB";
			$query[] = "ALTER TABLE IPv4NAT ENGINE=InnoDB";
			$query[] = "ALTER TABLE IPv4LB ENGINE=InnoDB";
			$query[] = "ALTER TABLE IPv4VS ENGINE=InnoDB";
			$query[] = "DELETE FROM IPv4RS WHERE rspool_id NOT IN (SELECT id FROM IPv4RSPool)";
			$query[] = "ALTER TABLE Link ADD CONSTRAINT `Link-FK-b` FOREIGN KEY (portb) REFERENCES Port (id)";
			$query[] = "ALTER TABLE Link ADD CONSTRAINT `Link-FK-a` FOREIGN KEY (porta) REFERENCES Port (id)";
			$query[] = "ALTER TABLE IPv4RS ADD CONSTRAINT `IPv4RS-FK` FOREIGN KEY (rspool_id) REFERENCES IPv4RSPool (id) ON DELETE CASCADE";
			$query[] = "ALTER TABLE AttributeValue ADD CONSTRAINT `AttributeValue-FK-object_id` FOREIGN KEY (object_id) REFERENCES RackObject (id)";
			$query[] = "ALTER TABLE IPv4NAT ADD CONSTRAINT `IPv4NAT-FK-object_id` FOREIGN KEY (object_id) REFERENCES RackObject (id)";
			$query[] = "ALTER TABLE Port ADD CONSTRAINT `Port-FK-object_id` FOREIGN KEY (object_id) REFERENCES RackObject (id)";
			$query[] = "ALTER TABLE IPv4LB ADD CONSTRAINT `IPv4LB-FK-rspool_id` FOREIGN KEY (rspool_id) REFERENCES IPv4RSPool (id)";
			$query[] = "ALTER TABLE IPv4LB ADD CONSTRAINT `IPv4LB-FK-object_id` FOREIGN KEY (object_id) REFERENCES RackObject (id)";
			$query[] = "ALTER TABLE IPv4LB ADD CONSTRAINT `IPv4LB-FK-vs_id` FOREIGN KEY (vs_id) REFERENCES IPv4VS (id)";
			$query = array_merge ($query, reloadDictionary ($batchid));
			$query[] = "UPDATE Config SET varvalue = '0.17.4' WHERE varname = 'DB_VERSION'";
			break;
		case '0.17.5':
			$query[] = "ALTER TABLE TagTree ENGINE=InnoDB";
			$query[] = "ALTER TABLE TagStorage ENGINE=InnoDB";
			$query[] = "ALTER TABLE TagStorage ADD CONSTRAINT `TagStorage-FK-tag_id` FOREIGN KEY (tag_id) REFERENCES TagTree (id)";
			$query[] = "ALTER TABLE TagTree ADD CONSTRAINT `TagTree-K-parent_id` FOREIGN KEY (parent_id) REFERENCES TagTree (id)";
			$query[] = 'INSERT INTO PortCompat (type1, type2) VALUES (21,1195)';
			$query[] = 'INSERT INTO PortCompat (type1, type2) VALUES (22,1196)';
			$query[] = 'INSERT INTO PortCompat (type1, type2) VALUES (23,1196)';
			$query[] = 'INSERT INTO PortCompat (type1, type2) VALUES (20,1195)';
			$query[] = 'INSERT INTO PortCompat (type1, type2) VALUES (25,1202)';
			$query[] = 'INSERT INTO PortCompat (type1, type2) VALUES (26,1202)';
			$query[] = 'INSERT INTO PortCompat (type1, type2) VALUES (27,1204)';
			$query[] = 'INSERT INTO PortCompat (type1, type2) VALUES (28,1204)';
			$query[] = 'INSERT INTO PortCompat (type1, type2) VALUES (1083,1195)';
			$query[] = 'INSERT INTO PortCompat (type1, type2) VALUES (1084,1084)';
			$query[] = 'INSERT INTO PortCompat (type1, type2) VALUES (1195,20)';
			$query[] = 'INSERT INTO PortCompat (type1, type2) VALUES (1195,21)';
			$query[] = 'INSERT INTO PortCompat (type1, type2) VALUES (1195,1083)';
			$query[] = 'INSERT INTO PortCompat (type1, type2) VALUES (1195,1195)';
			$query[] = 'INSERT INTO PortCompat (type1, type2) VALUES (1196,22)';
			$query[] = 'INSERT INTO PortCompat (type1, type2) VALUES (1196,23)';
			$query[] = 'INSERT INTO PortCompat (type1, type2) VALUES (1196,1196)';
			$query[] = 'INSERT INTO PortCompat (type1, type2) VALUES (1197,1197)';
			$query[] = 'INSERT INTO PortCompat (type1, type2) VALUES (1198,1199)';
			$query[] = 'INSERT INTO PortCompat (type1, type2) VALUES (1199,1198)';
			$query[] = 'INSERT INTO PortCompat (type1, type2) VALUES (1200,1200)';
			$query[] = 'INSERT INTO PortCompat (type1, type2) VALUES (1201,1201)';
			$query[] = 'INSERT INTO PortCompat (type1, type2) VALUES (1202,25)';
			$query[] = 'INSERT INTO PortCompat (type1, type2) VALUES (1202,26)';
			$query[] = 'INSERT INTO PortCompat (type1, type2) VALUES (1202,1202)';
			$query[] = 'INSERT INTO PortCompat (type1, type2) VALUES (1203,1203)';
			$query[] = 'INSERT INTO PortCompat (type1, type2) VALUES (1204,27)';
			$query[] = 'INSERT INTO PortCompat (type1, type2) VALUES (1204,28)';
			$query[] = 'INSERT INTO PortCompat (type1, type2) VALUES (1204,1204)';
			$query[] = 'INSERT INTO PortCompat (type1, type2) VALUES (1205,1205)';
			$query[] = 'INSERT INTO PortCompat (type1, type2) VALUES (1206,1207)';
			$query[] = 'INSERT INTO PortCompat (type1, type2) VALUES (1207,1206)';
			$query[] = 'INSERT INTO PortCompat (type1, type2) VALUES (1316,1316)';
			$query[] = 'INSERT INTO PortCompat (type1, type2) VALUES (16, 1322)';
			$query[] = 'INSERT INTO PortCompat (type1, type2) VALUES (1322, 16)';
			$query[] = 'DELETE FROM PortCompat WHERE type1 = 16 AND type2 = 16';
			for ($i = 1209; $i <= 1300; $i++)
				$query[] = "INSERT INTO PortCompat (type1, type2) VALUES (${i}, ${i})";
			$query[] = "
CREATE TABLE `PortInnerInterface` (
  `id` int(10) unsigned NOT NULL,
  `iif_name` char(16) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `iif_name` (`iif_name`)
) ENGINE=InnoDB";
			$query[] = "INSERT INTO `PortInnerInterface` VALUES (1,'hardwired')";
			$query[] = "INSERT INTO `PortInnerInterface` VALUES (2,'SFP-100')";
			$query[] = "INSERT INTO `PortInnerInterface` VALUES (3,'GBIC')";
			$query[] = "INSERT INTO `PortInnerInterface` VALUES (4,'SFP-1000')";
			$query[] = "INSERT INTO `PortInnerInterface` VALUES (5,'XENPAK')";
			$query[] = "INSERT INTO `PortInnerInterface` VALUES (6,'X2')";
			$query[] = "INSERT INTO `PortInnerInterface` VALUES (7,'XPAK')";
			$query[] = "INSERT INTO `PortInnerInterface` VALUES (8,'XFP')";
			$query[] = "INSERT INTO `PortInnerInterface` VALUES (9,'SFP+')";
			$query[] = "
CREATE TABLE `PortInterfaceCompat` (
  `iif_id` int(10) unsigned NOT NULL,
  `oif_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `pair` (`iif_id`,`oif_id`),
  CONSTRAINT `PortInterfaceCompat-FK-iif_id` FOREIGN KEY (`iif_id`) REFERENCES `PortInnerInterface` (`id`)
) ENGINE=InnoDB";
			$query[] = "ALTER TABLE Port ADD COLUMN iif_id int unsigned NOT NULL AFTER name"; // will set iif_id to 0
			$query[] = "UPDATE Port SET iif_id = 2 WHERE type = 1208";
			$query[] = "UPDATE Port SET iif_id = 3 WHERE type = 1078";
			$query[] = "UPDATE Port SET iif_id = 4 WHERE type = 1077";
			$query[] = "UPDATE Port SET iif_id = 5 WHERE type = 1079";
			$query[] = "UPDATE Port SET iif_id = 6 WHERE type = 1080";
			$query[] = "UPDATE Port SET iif_id = 7 WHERE type = 1081";
			$query[] = "UPDATE Port SET iif_id = 8 WHERE type = 1082";
			$query[] = "UPDATE Port SET iif_id = 9 WHERE type = 1084";
			$query[] = "UPDATE Port SET iif_id = 1 WHERE iif_id = 0";
			$query[] = 'ALTER TABLE Port ADD UNIQUE `object_iif_oif_name` (object_id, iif_id, type, name)';
			$query[] = 'ALTER TABLE Port DROP KEY `per_object`';
			$base1000 = array (24, 34, 1202, 1203, 1204, 1205, 1206, 1207);
			$base10000 = array (30, 35, 36, 37, 38, 39, 40);
			$PICdata = array
			(
				1 => array (16, 19, 24, 29, 31, 33, 446, 681, 682, 1322),
				2 => array (1208, 1195, 1196, 1197, 1198, 1199, 1200, 1201),
				3 => array_merge (array (1078), $base1000),
				4 => array_merge (array (1077), $base1000),
				5 => array_merge (array (1079), $base10000),
				6 => array_merge (array (1080), $base10000),
				7 => array_merge (array (1081), $base10000),
				8 => array_merge (array (1082), $base10000),
				9 => array_merge (array (1084), $base10000),
			);
			// make sure all IIF/OIF pairs referenced from Port exist in PortInterfaceCompat before enabling FK
			// iif_id doesn't exist at this point
			$result = $dbxlink->query ('SELECT DISTINCT type FROM Port WHERE type NOT IN (1208, 1078, 1077, 1079, 1080, 1081, 1082, 1084)');
			while ($row = $result->fetch (PDO::FETCH_ASSOC))
				if (FALSE === array_search ($row['type'], $PICdata[1]))
					array_push ($PICdata[1], $row['type']);
			unset ($result);
			foreach ($PICdata as $iif_id => $oif_ids)
				foreach ($oif_ids as $oif_id)
					$query[] = "INSERT INTO PortInterfaceCompat (iif_id, oif_id) VALUES (${iif_id}, ${oif_id})";
			$query[] = "ALTER TABLE Port ADD CONSTRAINT `Port-FK-iif-oif` FOREIGN KEY (`iif_id`, `type`) REFERENCES `PortInterfaceCompat` (`iif_id`, `oif_id`)";
			$query[] = 'UPDATE Port SET type = 1322 WHERE type = 16 AND (SELECT objtype_id FROM RackObject WHERE id = object_id) IN (2, 12)';
			$query = array_merge ($query, reloadDictionary ($batchid));
			$query[] = "DELETE FROM Config WHERE varname = 'default_port_type'";
			$query[] = "INSERT INTO Config VALUES ('DEFAULT_PORT_IIF_ID','1','uint','no','no','Default port inner interface ID')";
			$query[] = "INSERT INTO Config VALUES ('DEFAULT_PORT_OIF_IDS','1=24; 3=1078; 4=1077; 5=1079; 6=1080; 8=1082; 9=1084','string','no','no','Default port outer interface IDs')";
			$query[] = "INSERT INTO Config VALUES ('IPV4_TREE_RTR_AS_CELL','yes','string','no','no','Show full router info for each network in IPv4 tree view')";
			$query[] = "UPDATE Chapter SET name = 'PortOuterInterface' WHERE id = 2";
			// remap refs to duplicate records, which will be discarded (ticket:286)
			$query[] = 'UPDATE AttributeValue SET uint_value = 147 WHERE uint_value = 1020 AND attr_id = 2';
			$query[] = 'UPDATE AttributeValue SET uint_value = 377 WHERE uint_value = 1021 AND attr_id = 2';
			$query[] = 'INSERT INTO AttributeMap (objtype_id, attr_id) VALUES (2, 1), (2, 3), (2, 5)';
			$query[] = "UPDATE Config SET varvalue = '0.17.5' WHERE varname = 'DB_VERSION'";
			break;
		case '0.17.6':
			$query[] = "INSERT INTO `Chapter` (`id`, `sticky`, `name`) VALUES (28,'no','Voice/video hardware')";
			$query[] = "INSERT INTO `AttributeMap` (`objtype_id`, `attr_id`, `chapter_id`) VALUES (1323,1,NULL)";
			$query[] = "INSERT INTO `AttributeMap` (`objtype_id`, `attr_id`, `chapter_id`) VALUES (1323,2,28)";
			$query[] = "INSERT INTO `AttributeMap` (`objtype_id`, `attr_id`, `chapter_id`) VALUES (1323,3,NULL)";
			$query[] = "INSERT INTO `AttributeMap` (`objtype_id`, `attr_id`, `chapter_id`) VALUES (1323,5,NULL)";
			$query[] = "INSERT INTO `Config` (varname, varvalue, vartype, emptyok, is_hidden, description) VALUES ('PROXIMITY_RANGE','0','uint','yes','no','Proximity range (0 is current rack only)')";
			$query = array_merge ($query, reloadDictionary ($batchid));
			$query[] = "UPDATE Config SET varvalue = '0.17.6' WHERE varname = 'DB_VERSION'";
			break;
		case '0.17.7':
			$query[] = "UPDATE Config SET varvalue = '0.17.7' WHERE varname = 'DB_VERSION'";
			break;
		case '0.17.8':
			$query = array_merge ($query, reloadDictionary ($batchid));
			$query[] = "ALTER TABLE TagTree DROP COLUMN valid_realm";
			$query[] = "UPDATE Config SET varvalue = '0.17.8' WHERE varname = 'DB_VERSION'";
			break;
		case '0.17.9':
			$query[] = "ALTER table Config add `is_userdefined` enum('yes','no') NOT NULL default 'no' AFTER `is_hidden`";
			$query[] = "
CREATE TABLE `UserConfig` ( 
	`varname` char(32) NOT NULL, 
	`varvalue` char(255) NOT NULL, 
	`user` char(64) NOT NULL, 
	UNIQUE KEY `user_varname` (`user`,`varname`)
) TYPE=InnoDB";
			$query[] = "UPDATE Config SET is_userdefined = 'yes' WHERE varname IN
(
'MASSCOUNT',
'MAXSELSIZE',
'ROW_SCALE',
'PORTS_PER_ROW',
'IPV4_ADDRS_PER_PAGE',
'DEFAULT_RACK_HEIGHT',
'DEFAULT_SLB_VS_PORT',
'DEFAULT_SLB_RS_PORT',
'DETECT_URLS',
'RACK_PRESELECT_THRESHOLD',
'DEFAULT_IPV4_RS_INSERVICE',
'DEFAULT_OBJECT_TYPE',
'SHOW_EXPLICIT_TAGS',
'SHOW_IMPLICIT_TAGS',
'SHOW_AUTOMATIC_TAGS',
'IPV4_AUTO_RELEASE',
'SHOW_LAST_TAB',
'EXT_IPV4_VIEW',
'TREE_THRESHOLD',
'ADDNEW_AT_TOP',
'IPV4_TREE_SHOW_USAGE',
'PREVIEW_TEXT_MAXCHARS',
'PREVIEW_TEXT_ROWS',
'PREVIEW_TEXT_COLS',
'PREVIEW_IMAGE_MAXPXS',
'VENDOR_SIEVE',
'RACKS_PER_ROW'
)";
			$query[] = "UPDATE Config SET varvalue = '0.17.9' WHERE varname = 'DB_VERSION'";
			break;
		case '0.17.10':
			$query = array_merge ($query, reloadDictionary ($batchid));
			$query[] = "ALTER TABLE MountOperation ADD KEY (object_id)";
			$query[] = "INSERT INTO `Config` (varname, varvalue, vartype, emptyok, is_hidden, is_userdefined, description) VALUES ('STATIC_FILTER','yes','string','no','no','yes','Enable Filter Caching');";
			$query[] = "UPDATE Config SET varvalue = '0.17.10' WHERE varname = 'DB_VERSION'";
			break;
		case '0.17.11':
			$query = array_merge ($query, reloadDictionary ($batchid));
			$query[] = "INSERT INTO `Config` (varname, varvalue, vartype, emptyok, is_hidden, is_userdefined, description) VALUES ('ENABLE_BULKPORT_FORM','yes','string','no','no','yes','Enable \"Bulk Port\" form');";
			$query[] = "DELETE AttributeValue FROM AttributeValue JOIN Attribute where AttributeValue.attr_id = Attribute.id AND Attribute.type = 'dict' AND AttributeValue.uint_value = 0";
			$query[] = "UPDATE Config SET varvalue = '0.17.11' WHERE varname = 'DB_VERSION'";
			break;
		case '0.18.0':
			$query = array_merge ($query, reloadDictionary ($batchid));
			$query[] = "INSERT INTO `Config` (varname, varvalue, vartype, emptyok, is_hidden, is_userdefined, description) VALUES ('VLANSWITCH_LISTSRC', '', 'string', 'yes', 'no', 'yes', 'List of VLAN running switches')";
			$query[] = "INSERT INTO `Config` (varname, varvalue, vartype, emptyok, is_hidden, is_userdefined, description) VALUES ('VLANIPV4NET_LISTSRC', '', 'string', 'yes', 'no', 'yes', 'List of VLAN-based IPv4 networks')";
			$query[] = "INSERT INTO `Config` (varname, varvalue, vartype, emptyok, is_hidden, is_userdefined, description) VALUES ('DEFAULT_VDOM_ID','','uint','yes','no','yes','Default VLAN domain ID')";
			$query[] = "INSERT INTO `Config` (varname, varvalue, vartype, emptyok, is_hidden, is_userdefined, description) VALUES ('DEFAULT_VST_ID','','uint','yes','no','yes','Default VLAN switch template ID')";
			$query[] = "INSERT INTO `Config` (varname, varvalue, vartype, emptyok, is_hidden, is_userdefined, description) VALUES ('8021Q_DEPLOY_MINAGE','300','uint','no','no','no','802.1Q deploy minimum age')";
			$query[] = "INSERT INTO `Config` (varname, varvalue, vartype, emptyok, is_hidden, is_userdefined, description) VALUES ('8021Q_DEPLOY_MAXAGE','3600','uint','no','no','no','802.1Q deploy maximum age')";
			$query[] = "INSERT INTO `Config` (varname, varvalue, vartype, emptyok, is_hidden, is_userdefined, description) VALUES ('8021Q_DEPLOY_RETRY','10800','uint','no','no','no','802.1Q deploy retry timer')";
			$query[] = "INSERT INTO `Config` (varname, varvalue, vartype, emptyok, is_hidden, is_userdefined, description) VALUES ('8021Q_WRI_AFTER_CONFT','no','string','no','no','no','802.1Q: save device configuration after deploy')";
			$query[] = "INSERT INTO `Config` (varname, varvalue, vartype, emptyok, is_hidden, is_userdefined, description) VALUES ('8021Q_INSTANT_DEPLOY','no','string','no','no','yes','802.1Q: instant deploy')";
			$query[] = "INSERT INTO `Config` (varname, varvalue, vartype, emptyok, is_hidden, is_userdefined, description) VALUES ('IPV4_TREE_SHOW_VLAN','yes','string','no','no','yes','Show VLAN for each network in IPv4 tree')";
			$query[] = "ALTER TABLE IPv4Network ENGINE=InnoDB";
			$query[] = "SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0";
			$query[] = "
CREATE TABLE `CachedPAV` (
  `object_id` int(10) unsigned NOT NULL,
  `port_name` char(255) NOT NULL,
  `vlan_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`object_id`,`port_name`,`vlan_id`),
  KEY `vlan_id` (`vlan_id`),
  CONSTRAINT `CachedPAV-FK-object-port` FOREIGN KEY (`object_id`, `port_name`) REFERENCES `CachedPVM` (`object_id`, `port_name`) ON DELETE CASCADE,
  CONSTRAINT `CachedPAV-FK-vlan_id` FOREIGN KEY (`vlan_id`) REFERENCES `VLANValidID` (`vlan_id`)
) ENGINE=InnoDB
";
			$query[] = "
CREATE TABLE `CachedPNV` (
  `object_id` int(10) unsigned NOT NULL,
  `port_name` char(255) NOT NULL,
  `vlan_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`object_id`,`port_name`,`vlan_id`),
  UNIQUE KEY `port_id` (`object_id`,`port_name`),
  CONSTRAINT `CachedPNV-FK-compound` FOREIGN KEY (`object_id`, `port_name`, `vlan_id`) REFERENCES `CachedPAV` (`object_id`, `port_name`, `vlan_id`) ON DELETE CASCADE
) ENGINE=InnoDB
";
			$query[] = "
CREATE TABLE `CachedPVM` (
  `object_id` int(10) unsigned NOT NULL,
  `port_name` char(255) NOT NULL,
  `vlan_mode` enum('access','trunk') NOT NULL default 'access',
  PRIMARY KEY  (`object_id`,`port_name`),
  CONSTRAINT `CachedPVM-FK-object_id` FOREIGN KEY (`object_id`) REFERENCES `RackObject` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB
";
			$query[] = "
CREATE TABLE `PortAllowedVLAN` (
  `object_id` int(10) unsigned NOT NULL,
  `port_name` char(255) NOT NULL,
  `vlan_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`object_id`,`port_name`,`vlan_id`),
  KEY `vlan_id` (`vlan_id`),
  CONSTRAINT `PortAllowedVLAN-FK-object-port` FOREIGN KEY (`object_id`, `port_name`) REFERENCES `PortVLANMode` (`object_id`, `port_name`) ON DELETE CASCADE,
  CONSTRAINT `PortAllowedVLAN-FK-vlan_id` FOREIGN KEY (`vlan_id`) REFERENCES `VLANValidID` (`vlan_id`)
) ENGINE=InnoDB
";
			$query[] = "
CREATE TABLE `PortNativeVLAN` (
  `object_id` int(10) unsigned NOT NULL,
  `port_name` char(255) NOT NULL,
  `vlan_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`object_id`,`port_name`,`vlan_id`),
  UNIQUE KEY `port_id` (`object_id`,`port_name`),
  CONSTRAINT `PortNativeVLAN-FK-compound` FOREIGN KEY (`object_id`, `port_name`, `vlan_id`) REFERENCES `PortAllowedVLAN` (`object_id`, `port_name`, `vlan_id`) ON DELETE CASCADE
) ENGINE=InnoDB
";
			$query[] = "
CREATE TABLE `PortVLANMode` (
  `object_id` int(10) unsigned NOT NULL,
  `port_name` char(255) NOT NULL,
  `vlan_mode` enum('access','trunk') NOT NULL default 'access',
  PRIMARY KEY  (`object_id`,`port_name`),
  CONSTRAINT `PortVLANMode-FK-object-port` FOREIGN KEY (`object_id`, `port_name`) REFERENCES `CachedPVM` (`object_id`, `port_name`)
) ENGINE=InnoDB
";
			$query[] = "
CREATE TABLE `VLANDescription` (
  `domain_id` int(10) unsigned NOT NULL,
  `vlan_id` int(10) unsigned NOT NULL default '0',
  `vlan_type` enum('ondemand','compulsory','alien') NOT NULL default 'ondemand',
  `vlan_descr` char(255) default NULL,
  PRIMARY KEY  (`domain_id`,`vlan_id`),
  KEY `vlan_id` (`vlan_id`),
  CONSTRAINT `VLANDescription-FK-domain_id` FOREIGN KEY (`domain_id`) REFERENCES `VLANDomain` (`id`) ON DELETE CASCADE,
  CONSTRAINT `VLANDescription-FK-vlan_id` FOREIGN KEY (`vlan_id`) REFERENCES `VLANValidID` (`vlan_id`)
) ENGINE=InnoDB
";
			$query[] = "
CREATE TABLE `VLANDomain` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `description` char(255) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `description` (`description`)
) ENGINE=InnoDB
";
			$query[] = "
CREATE TABLE `VLANIPv4` (
  `domain_id` int(10) unsigned NOT NULL,
  `vlan_id` int(10) unsigned NOT NULL,
  `ipv4net_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `network-domain` (`ipv4net_id`,`domain_id`),
  KEY `VLANIPv4-FK-compound` (`domain_id`,`vlan_id`),
  CONSTRAINT `VLANIPv4-FK-compound` FOREIGN KEY (`domain_id`, `vlan_id`) REFERENCES `VLANDescription` (`domain_id`, `vlan_id`) ON DELETE CASCADE,
  CONSTRAINT `VLANIPv4-FK-ipv4net_id` FOREIGN KEY (`ipv4net_id`) REFERENCES `IPv4Network` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB
";
			$query[] = "
CREATE TABLE `VLANSTRule` (
  `vst_id` int(10) unsigned NOT NULL,
  `rule_no` int(10) unsigned NOT NULL,
  `port_pcre` char(255) NOT NULL,
  `port_role` enum('access','trunk','uplink','downlink','none') NOT NULL default 'none',
  `wrt_vlans` char(255) default NULL,
  `description` char(255) default NULL,
  UNIQUE KEY `vst-rule` (`vst_id`,`rule_no`),
  CONSTRAINT `VLANSTRule-FK-vst_id` FOREIGN KEY (`vst_id`) REFERENCES `VLANSwitchTemplate` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB
";
			$query[] = "
CREATE TABLE `VLANSwitch` (
  `object_id` int(10) unsigned NOT NULL,
  `domain_id` int(10) unsigned NOT NULL,
  `template_id` int(10) unsigned NOT NULL,
  `mutex_rev` int(10) unsigned NOT NULL default '0',
  `out_of_sync` enum('yes','no') NOT NULL default 'yes',
  `last_errno` int(10) unsigned NOT NULL default '0',
  `last_change` timestamp NOT NULL default '0000-00-00 00:00:00',
  `last_push_started` timestamp NOT NULL default '0000-00-00 00:00:00',
  `last_push_finished` timestamp NOT NULL default '0000-00-00 00:00:00',
  `last_error_ts` timestamp NOT NULL default '0000-00-00 00:00:00',
  UNIQUE KEY `object_id` (`object_id`),
  KEY `domain_id` (`domain_id`),
  KEY `template_id` (`template_id`),
  KEY `out_of_sync` (`out_of_sync`),
  KEY `last_errno` (`last_errno`),
  CONSTRAINT `VLANSwitch-FK-domain_id` FOREIGN KEY (`domain_id`) REFERENCES `VLANDomain` (`id`),
  CONSTRAINT `VLANSwitch-FK-object_id` FOREIGN KEY (`object_id`) REFERENCES `RackObject` (`id`),
  CONSTRAINT `VLANSwitch-FK-template_id` FOREIGN KEY (`template_id`) REFERENCES `VLANSwitchTemplate` (`id`)
) ENGINE=InnoDB
";
			$query[] = "
CREATE TABLE `VLANSwitchTemplate` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `max_local_vlans` int(10) unsigned default NULL,
  `description` char(255) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `description` (`description`)
) ENGINE=InnoDB
";
			$query[] = "
CREATE TABLE `VLANValidID` (
  `vlan_id` int(10) unsigned NOT NULL default '1',
  PRIMARY KEY  (`vlan_id`)
) ENGINE=InnoDB
";
			$query[] = "SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS";
			for ($i = 1; $i <= 4094; $i++)
				$query[] = "INSERT INTO VLANValidID (vlan_id) VALUES (${i})";
			$query[] = "UPDATE Config SET varvalue = '0.18.0' WHERE varname = 'DB_VERSION'";
			break;
		case '0.18.1':
			$query = array_merge ($query, reloadDictionary ($batchid));
			$query[] = "ALTER TABLE Atom ENGINE=InnoDB";
			$query[] = "ALTER TABLE AttributeMap ENGINE=InnoDB";
			$query[] = "ALTER TABLE Config ENGINE=InnoDB";
			$query[] = "ALTER TABLE IPv4Address ENGINE=InnoDB";
			$query[] = "ALTER TABLE IPv4Allocation ENGINE=InnoDB";
			$query[] = "ALTER TABLE Molecule ENGINE=InnoDB";
			$query[] = "ALTER TABLE MountOperation ENGINE=InnoDB";
			$query[] = "ALTER TABLE PortCompat ENGINE=InnoDB";
			$query[] = "ALTER TABLE Rack ENGINE=InnoDB";
			$query[] = "ALTER TABLE RackHistory ENGINE=InnoDB";
			$query[] = "ALTER TABLE RackObjectHistory ENGINE=InnoDB";
			$query[] = "ALTER TABLE RackRow ENGINE=InnoDB";
			$query[] = "ALTER TABLE RackSpace ENGINE=InnoDB";
			$query[] = "ALTER TABLE Script ENGINE=InnoDB";
			$query[] = "ALTER TABLE AttributeValue DROP FOREIGN KEY `AttributeValue-FK-object_id`";
			$query[] = "ALTER TABLE AttributeValue ADD CONSTRAINT `AttributeValue-FK-object_id` FOREIGN KEY (`object_id`) REFERENCES `RackObject` (`id`) ON DELETE CASCADE";
			$query[] = "ALTER TABLE RackObjectHistory ADD KEY (id)";
			$query[] = "ALTER TABLE RackObjectHistory ADD CONSTRAINT `RackObjectHistory-FK-object_id` FOREIGN KEY (id) REFERENCES `RackObject` (`id`) ON DELETE CASCADE";
			$query[] = "ALTER TABLE MountOperation ADD CONSTRAINT `MountOperation-FK-object_id` FOREIGN KEY (object_id) REFERENCES RackObject (id) ON DELETE CASCADE";
			$query[] = "ALTER TABLE RackSpace ADD CONSTRAINT `RackSpace-FK-object_id` FOREIGN KEY (object_id) REFERENCES RackObject (id) ON DELETE CASCADE";
			$query[] = "ALTER TABLE Link DROP FOREIGN KEY `Link-FK-a`";
			$query[] = "ALTER TABLE Link ADD CONSTRAINT `Link-FK-a` FOREIGN KEY (`porta`) REFERENCES `Port` (`id`) ON DELETE CASCADE";
			$query[] = "ALTER TABLE Link DROP FOREIGN KEY `Link-FK-b`";
			$query[] = "ALTER TABLE Link ADD CONSTRAINT `Link-FK-b` FOREIGN KEY (`portb`) REFERENCES `Port` (`id`) ON DELETE CASCADE";
			$query[] = "ALTER TABLE Port DROP FOREIGN KEY `Port-FK-object_id`";
			$query[] = "ALTER TABLE Port ADD CONSTRAINT `Port-FK-object_id` FOREIGN KEY (`object_id`) REFERENCES `RackObject` (`id`) ON DELETE CASCADE";
			$query[] = "ALTER TABLE AttributeMap MODIFY `chapter_id` int(10) unsigned default NULL";
			$query[] = "ALTER TABLE IPv4Address MODIFY `ip` int(10) unsigned NOT NULL default '0'";
			$query[] = "ALTER TABLE IPv4Address MODIFY `name` char(255) NOT NULL default ''";
			$query[] = "ALTER TABLE IPv4Allocation MODIFY `object_id` int(10) unsigned NOT NULL default '0'";
			$query[] = "ALTER TABLE IPv4Allocation MODIFY `ip` int(10) unsigned NOT NULL default '0'";
			$query[] = "ALTER TABLE IPv4Allocation MODIFY `name` char(255) NOT NULL default ''";
			$query[] = "ALTER TABLE IPv4NAT MODIFY `object_id` int(10) unsigned NOT NULL default '0'";
			$query[] = "ALTER TABLE IPv4NAT MODIFY `proto` enum('TCP','UDP') NOT NULL default 'TCP'";
			$query[] = "ALTER TABLE IPv4NAT MODIFY `localip` int(10) unsigned NOT NULL default '0'";
			$query[] = "ALTER TABLE IPv4NAT MODIFY `localport` smallint(5) unsigned NOT NULL default '0'";
			$query[] = "ALTER TABLE IPv4NAT MODIFY `remoteip` int(10) unsigned NOT NULL default '0'";
			$query[] = "ALTER TABLE IPv4NAT MODIFY `remoteport` smallint(5) unsigned NOT NULL default '0'";
			$query[] = "ALTER TABLE IPv4Network MODIFY `ip` int(10) unsigned NOT NULL default '0'";
			$query[] = "ALTER TABLE IPv4Network MODIFY `mask` int(10) unsigned NOT NULL default '0'";
			$query[] = "ALTER TABLE Link MODIFY `porta` int(10) unsigned NOT NULL default '0'";
			$query[] = "ALTER TABLE Link MODIFY `portb` int(10) unsigned NOT NULL default '0'";
			$query[] = "ALTER TABLE MountOperation MODIFY `object_id` int(10) unsigned NOT NULL default '0'";
			$query[] = "ALTER TABLE MountOperation MODIFY `ctime` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP";
			$query[] = "ALTER TABLE Port MODIFY `object_id` int(10) unsigned NOT NULL default '0'";
			$query[] = "ALTER TABLE Port MODIFY `name` char(255) NOT NULL default ''";
			$query[] = "ALTER TABLE Port MODIFY `type` int(10) unsigned NOT NULL default '0'";
			$query[] = "ALTER TABLE PortCompat MODIFY `type1` int(10) unsigned NOT NULL default '0'";
			$query[] = "ALTER TABLE PortCompat MODIFY `type2` int(10) unsigned NOT NULL default '0'";
			$query[] = "ALTER TABLE RackHistory MODIFY `ctime` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP";
			$query[] = "ALTER TABLE RackObjectHistory MODIFY `ctime` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP";
			$query[] = "ALTER TABLE TagStorage MODIFY `tag_id` int(10) unsigned NOT NULL default '0'";
			$query[] = "ALTER TABLE UserAccount MODIFY `user_name` char(64) NOT NULL default ''";
			$query[] = "UPDATE Config SET varvalue = '0.18.1' WHERE varname = 'DB_VERSION'";
			break;
		case '0.18.2':
			$query = array_merge ($query, reloadDictionary ($batchid));
			$query[] = "ALTER TABLE Rack ADD CONSTRAINT `Rack-FK-row_id` FOREIGN KEY (row_id) REFERENCES RackRow (id)";
			$query[] = "ALTER TABLE RackRow ADD UNIQUE KEY `name` (name)";
			$query[] = "INSERT INTO `Config` (varname, varvalue, vartype, emptyok, is_hidden, is_userdefined, description) VALUES ('CDP_RUNNERS_LISTSRC', '', 'string', 'yes', 'no', 'no', 'List of devices running CDP')";
			$query[] = "INSERT INTO `Config` (varname, varvalue, vartype, emptyok, is_hidden, is_userdefined, description) VALUES ('LLDP_RUNNERS_LISTSRC', '', 'string', 'yes', 'no', 'no', 'List of devices running LLDP')";
			$query[] = "UPDATE Config SET varvalue = '0.18.2' WHERE varname = 'DB_VERSION'";
			break;
		case '0.18.3':
			$query = array_merge ($query, reloadDictionary ($batchid));
			$query[] = "UPDATE Config SET varname='8021Q_WRI_AFTER_CONFT_LISTSRC', varvalue='false', description='802.1Q: save device configuration after deploy (RackCode)' WHERE varname='8021Q_WRI_AFTER_CONFT'";
			$query[] = "INSERT INTO `Config` (varname, varvalue, vartype, emptyok, is_hidden, is_userdefined, description) VALUES ('HNDP_RUNNERS_LISTSRC', '', 'string', 'yes', 'no', 'no', 'List of devices running HNDP (RackCode)')";
			$query[] = "UPDATE Config SET varvalue = '0.18.3' WHERE varname = 'DB_VERSION'";
			break;
		case '0.18.4':
			$query = array_merge ($query, reloadDictionary ($batchid));
			$query[] = "UPDATE Config SET varvalue = '0.18.4' WHERE varname = 'DB_VERSION'";
			break;
		default:
			showFailure ("executeUpgradeBatch () failed, because batch '${batchid}' isn't defined", __FILE__);
			die;
			break;
	}
	$failures = array();
	echo "<tr><th>Executing batch '${batchid}'</th><td>";
	foreach ($query as $q)
	{
		$result = $dbxlink->query ($q);
		if ($result == NULL)
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

// ******************************************************************
//
//                  Execution starts here
//
// ******************************************************************

// a clone of showError() to drop dependency on interface.php
function showFailure ($info = '', $location = 'N/A')
{
	if (preg_match ('/\.php$/', $location))
		$location = basename ($location);
	elseif ($location != 'N/A')
		$location = $location . '()';
	echo "<div class=msg_error>An error has occured in [${location}]. ";
	if (empty ($info))
		echo 'No additional information is available.';
	else
		echo "Additional information:<br><p>\n<pre>\n${info}\n</pre></p>";
	echo "This failure is most probably fatal.<br></div>\n";
}

require_once 'inc/config.php'; // for CODE_VERSION
require_once 'inc/database.php'; // for getDatabaseVersion()
require_once 'inc/dictionary.php';
// Enforce default value for now, releases prior to 0.17.0 didn't support 'httpd' auth source.
$user_auth_src = 'database';

if (file_exists ('inc/secret.php'))
	require_once 'inc/secret.php';
else
	die ('<center>There is no working RackTables instance here, <a href="install.php">install</a>?</center>');

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

function authenticate_admin ($username, $password)
{
	global $dbxlink;
	$prepared = $dbxlink->prepare ('SELECT COUNT(*) FROM UserAccount WHERE user_id=1 AND user_name=? AND user_password_hash=?');
	if (!$prepared->execute (array ($username, sha1 ($password))))
		die ('SQL query failed in ' . __FUNCTION__);
	$rows = $prepared->fetchAll (PDO::FETCH_NUM);
	return $rows[0][0] == 1;
}

switch ($user_auth_src)
{
	case 'database':
	case 'ldap': // authenticate against DB as well
		if
		(
			!isset ($_SERVER['PHP_AUTH_USER']) or
			!strlen ($_SERVER['PHP_AUTH_USER']) or
			!isset ($_SERVER['PHP_AUTH_PW']) or
			!strlen ($_SERVER['PHP_AUTH_PW']) or
			!authenticate_admin ($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'])
		)
		{
			header ('WWW-Authenticate: Basic realm="RackTables upgrade"');
			header ('HTTP/1.0 401 Unauthorized');
			showFailure ('You must be authenticated as an administrator to complete the upgrade.', __FILE__);
			die;
		}
		break; // cleared
	case 'httpd':
		if
		(
			!isset ($_SERVER['REMOTE_USER']) or
			!strlen ($_SERVER['REMOTE_USER'])
		)
		{
			showFailure ('System misconfiguration. The web-server didn\'t authenticate the user, although ought to do.');
			die;
		}
		break; // cleared
	default:
		showFailure ('authentication source misconfiguration', __FILE__);
		die;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head><title>RackTables upgrade script</title>
<link rel=stylesheet type='text/css' href='pi.css' />
</head>
<body>
<h1>Platform check status</h1>
<?php

if (!platform_is_ok())
	die ('</body></html>');

echo '<h1>Upgrade status</h1>';
$dbver = getDatabaseVersion();
echo '<table border=1 cellpadding=5>';
echo "<tr><th>Current status</th><td>Data version: ${dbver}<br>Code version: " . CODE_VERSION . "</td></tr>\n";

$path = getDBUpgradePath ($dbver, CODE_VERSION);
if ($path === NULL)
{
	echo "<tr><th>Upgrade path</th><td><font color=red>not found</font></td></tr>\n";
	echo "<tr><th>Summary</th><td>Check README for more information. RackTables releases prior to 0.16.4 ";
	echo "must be upgraded to 0.16.4 first.</td></tr>\n";
}
else
{
	if (!count ($path))
		echo "<tr><th>Summary</th><td>Come back later.</td></tr>\n";
	else
	{
		echo "<tr><th>Upgrade path</th><td>${dbver} &rarr; " . implode (' &rarr; ', $path) . "</td></tr>\n";
		foreach ($path as $batchid)
		{
			executeUpgradeBatch ($batchid);
			if (isset ($relnotes[$batchid]))
				echo "<tr><th>Release notes for ${batchid}</th><td><pre>" . $relnotes[$batchid] . "</pre></td></tr>\n";
		}
		echo "<tr><th>Summary</th><td>Upgrade complete, it is Ok to <a href='index.php'>enter</a> the system.</td></tr>\n";
	}
}
echo '</table>';
echo '</body></html>';

?>
