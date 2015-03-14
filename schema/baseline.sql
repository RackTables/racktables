/* Baseline schema */

alter database character set utf8 collate utf8_unicode_ci;
set names 'utf8';
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;

CREATE TABLE `Atom` (
  `molecule_id` int(10) unsigned default NULL,
  `rack_id` int(10) unsigned default NULL,
  `unit_no` int(10) unsigned default NULL,
  `atom` enum('front','interior','rear') default NULL,
  CONSTRAINT `Atom-FK-molecule_id` FOREIGN KEY (`molecule_id`) REFERENCES `Molecule` (`id`) ON DELETE CASCADE,
  CONSTRAINT `Atom-FK-rack_id` FOREIGN KEY (`rack_id`) REFERENCES `Object` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `Attribute` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `type` enum('string','uint','float','dict','date') default NULL,
  `name` char(64) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB;

CREATE TABLE `AttributeMap` (
  `objtype_id` int(10) unsigned NOT NULL default '1',
  `attr_id` int(10) unsigned NOT NULL default '1',
  `chapter_id` int(10) unsigned default NULL,
  `sticky` enum('yes','no') default 'no',
  UNIQUE KEY `objtype_id` (`objtype_id`,`attr_id`),
  KEY `attr_id` (`attr_id`),
  KEY `chapter_id` (`chapter_id`),
  CONSTRAINT `AttributeMap-FK-chapter_id` FOREIGN KEY (`chapter_id`) REFERENCES `Chapter` (`id`),
  CONSTRAINT `AttributeMap-FK-attr_id` FOREIGN KEY (`attr_id`) REFERENCES `Attribute` (`id`)
) ENGINE=InnoDB;

CREATE TABLE `AttributeValue` (
  `object_id` int(10) unsigned NOT NULL,
  -- Default value intentionally breaks the constraint, this blocks
  -- any insertion that doesn't have 'object_tid' on the column list.
  `object_tid` int(10) unsigned NOT NULL default '0',
  `attr_id` int(10) unsigned NOT NULL,
  `string_value` char(255) default NULL,
  `uint_value` int(10) unsigned default NULL,
  `float_value` float default NULL,
  PRIMARY KEY (`object_id`,`attr_id`),
  KEY `attr_id-uint_value` (`attr_id`,`uint_value`),
  KEY `attr_id-string_value` (`attr_id`,`string_value`(12)),
  KEY `id-tid` (`object_id`,`object_tid`),
  KEY `object_tid-attr_id` (`object_tid`,`attr_id`),
  CONSTRAINT `AttributeValue-FK-map` FOREIGN KEY (`object_tid`, `attr_id`) REFERENCES `AttributeMap` (`objtype_id`, `attr_id`),
  CONSTRAINT `AttributeValue-FK-object` FOREIGN KEY (`object_id`, `object_tid`) REFERENCES `Object` (`id`, `objtype_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `CachedPAV` (
  `object_id` int(10) unsigned NOT NULL,
  `port_name` char(255) NOT NULL,
  `vlan_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`object_id`,`port_name`,`vlan_id`),
  KEY `vlan_id` (`vlan_id`),
  CONSTRAINT `CachedPAV-FK-object-port` FOREIGN KEY (`object_id`, `port_name`) REFERENCES `CachedPVM` (`object_id`, `port_name`) ON DELETE CASCADE,
  CONSTRAINT `CachedPAV-FK-vlan_id` FOREIGN KEY (`vlan_id`) REFERENCES `VLANValidID` (`vlan_id`)
) ENGINE=InnoDB;

CREATE TABLE `CachedPNV` (
  `object_id` int(10) unsigned NOT NULL,
  `port_name` char(255) NOT NULL,
  `vlan_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`object_id`,`port_name`,`vlan_id`),
  UNIQUE KEY `port_id` (`object_id`,`port_name`),
  CONSTRAINT `CachedPNV-FK-compound` FOREIGN KEY (`object_id`, `port_name`, `vlan_id`) REFERENCES `CachedPAV` (`object_id`, `port_name`, `vlan_id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `CachedPVM` (
  `object_id` int(10) unsigned NOT NULL,
  `port_name` char(255) NOT NULL,
  `vlan_mode` enum('access','trunk') NOT NULL default 'access',
  PRIMARY KEY  (`object_id`,`port_name`),
  CONSTRAINT `CachedPVM-FK-object_id` FOREIGN KEY (`object_id`) REFERENCES `Object` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `CactiGraph` (
  `object_id` int(10) unsigned NOT NULL,
  `server_id` int(10) unsigned NOT NULL,
  `graph_id` int(10) unsigned NOT NULL,
  `caption`  char(255) DEFAULT NULL,
  PRIMARY KEY (`object_id`,`server_id`,`graph_id`),
  KEY `graph_id` (`graph_id`),
  KEY `server_id` (`server_id`),
  CONSTRAINT `CactiGraph-FK-server_id` FOREIGN KEY (`server_id`) REFERENCES `CactiServer` (`id`),
  CONSTRAINT `CactiGraph-FK-object_id` FOREIGN KEY (`object_id`) REFERENCES `Object` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `CactiServer` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `base_url` char(255) DEFAULT NULL,
  `username` char(64) DEFAULT NULL,
  `password` char(64) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE `Chapter` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `sticky` enum('yes','no') default 'no',
  `name` char(128) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB;

CREATE TABLE `Config` (
  `varname` char(32) NOT NULL,
  `varvalue` text NOT NULL,
  `vartype` enum('string','uint') NOT NULL default 'string',
  `emptyok` enum('yes','no') NOT NULL default 'no',
  `is_hidden` enum('yes','no') NOT NULL default 'yes',
  `is_userdefined` enum('yes','no') NOT NULL default 'no',
  `description` text,
  PRIMARY KEY  (`varname`)
) ENGINE=InnoDB;

CREATE TABLE `Dictionary` (
  `chapter_id` int(10) unsigned NOT NULL,
  `dict_key` int(10) unsigned NOT NULL auto_increment,
  `dict_sticky` enum('yes','no') DEFAULT 'no',
  `dict_value` char(255) default NULL,
  PRIMARY KEY  (`dict_key`),
  UNIQUE KEY `dict_unique` (`chapter_id`,`dict_value`,`dict_sticky`),
  CONSTRAINT `Dictionary-FK-chapter_id` FOREIGN KEY (`chapter_id`) REFERENCES `Chapter` (`id`)
) ENGINE=InnoDB;

CREATE TABLE `EntityLink` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_entity_type` enum('location','object','rack','row') NOT NULL,
  `parent_entity_id` int(10) unsigned NOT NULL,
  `child_entity_type` enum('location','object','rack','row') NOT NULL,
  `child_entity_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `EntityLink-unique` (`parent_entity_type`,`parent_entity_id`,`child_entity_type`,`child_entity_id`),
  KEY `EntityLink-compound` (`parent_entity_type`,`child_entity_type`,`child_entity_id`)
) ENGINE=InnoDB;

CREATE TABLE `File` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` char(255) NOT NULL,
  `type` char(255) NOT NULL,
  `size` int(10) unsigned NOT NULL,
  `ctime` datetime NOT NULL,
  `mtime` datetime NOT NULL,
  `atime` datetime NOT NULL,
  `thumbnail` longblob,
  `contents` longblob NOT NULL,
  `comment` text,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB;

CREATE TABLE `FileLink` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `file_id` int(10) unsigned NOT NULL,
  `entity_type` enum('ipv4net','ipv4rspool','ipv4vs','ipvs','ipv6net','location','object','rack','row','user') NOT NULL default 'object',
  `entity_id` int(10) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `FileLink-file_id` (`file_id`),
  UNIQUE KEY `FileLink-unique` (`file_id`,`entity_type`,`entity_id`),
  CONSTRAINT `FileLink-File_fkey` FOREIGN KEY (`file_id`) REFERENCES `File` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `IPv4Address` (
  `ip` int(10) unsigned NOT NULL default '0',
  `name` char(255) NOT NULL default '',
  `comment` char(255) NOT NULL default '',
  `reserved` enum('yes','no') default NULL,
  PRIMARY KEY  (`ip`)
) ENGINE=InnoDB;

CREATE TABLE `IPv4Allocation` (
  `object_id` int(10) unsigned NOT NULL default '0',
  `ip` int(10) unsigned NOT NULL default '0',
  `name` char(255) NOT NULL default '',
  `type` enum('regular','shared','virtual','router','point2point') NOT NULL DEFAULT 'regular',
  PRIMARY KEY  (`object_id`,`ip`),
  KEY `ip` (`ip`),
  CONSTRAINT `IPv4Allocation-FK-object_id` FOREIGN KEY (`object_id`) REFERENCES `Object` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `IPv4LB` (
  `object_id` int(10) unsigned default NULL,
  `rspool_id` int(10) unsigned default NULL,
  `vs_id` int(10) unsigned default NULL,
  `prio` varchar(255) default NULL,
  `vsconfig` text,
  `rsconfig` text,
  UNIQUE KEY `LB-VS` (`object_id`,`vs_id`),
  KEY `IPv4LB-FK-rspool_id` (`rspool_id`),
  KEY `IPv4LB-FK-vs_id` (`vs_id`),
  CONSTRAINT `IPv4LB-FK-vs_id` FOREIGN KEY (`vs_id`) REFERENCES `IPv4VS` (`id`),
  CONSTRAINT `IPv4LB-FK-object_id` FOREIGN KEY (`object_id`) REFERENCES `Object` (`id`),
  CONSTRAINT `IPv4LB-FK-rspool_id` FOREIGN KEY (`rspool_id`) REFERENCES `IPv4RSPool` (`id`)
) ENGINE=InnoDB;

CREATE TABLE `IPv4Log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `ip` int(10) unsigned NOT NULL,
  `date` datetime NOT NULL,
  `user` varchar(64) NOT NULL,
  `message` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ip-date` (`ip`,`date`)
) ENGINE=InnoDB;

CREATE TABLE `IPv6Log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `ip` binary(16) NOT NULL,
  `date` datetime NOT NULL,
  `user` varchar(64) NOT NULL,
  `message` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ip-date` (`ip`,`date`)
) ENGINE=InnoDB;

CREATE TABLE `IPv4NAT` (
  `object_id` int(10) unsigned NOT NULL default '0',
  `proto` enum('TCP','UDP','ALL') NOT NULL default 'TCP',
  `localip` int(10) unsigned NOT NULL default '0',
  `localport` smallint(5) unsigned NOT NULL default '0',
  `remoteip` int(10) unsigned NOT NULL default '0',
  `remoteport` smallint(5) unsigned NOT NULL default '0',
  `description` char(255) default NULL,
  PRIMARY KEY  (`object_id`,`proto`,`localip`,`localport`,`remoteip`,`remoteport`),
  KEY `localip` (`localip`),
  KEY `remoteip` (`remoteip`),
  KEY `object_id` (`object_id`),
  CONSTRAINT `IPv4NAT-FK-object_id` FOREIGN KEY (`object_id`) REFERENCES `Object` (`id`)
) ENGINE=InnoDB;

CREATE TABLE `IPv4Network` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `ip` int(10) unsigned NOT NULL default '0',
  `mask` int(10) unsigned NOT NULL default '0',
  `name` char(255) default NULL,
  `comment` text,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `base-len` (`ip`,`mask`)
) ENGINE=InnoDB;

CREATE TABLE `IPv4RS` (
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
  CONSTRAINT `IPv4RS-FK` FOREIGN KEY (`rspool_id`) REFERENCES `IPv4RSPool` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `IPv4RSPool` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` char(255) default NULL,
  `vsconfig` text,
  `rsconfig` text,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB;

CREATE TABLE `IPv4VS` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `vip` varbinary(16) NOT NULL,
  `vport` smallint(5) unsigned default NULL,
  `proto` enum('TCP','UDP','MARK') NOT NULL default 'TCP',
  `name` char(255) default NULL,
  `vsconfig` text,
  `rsconfig` text,
  PRIMARY KEY  (`id`),
  KEY `vip` (`vip`)
) ENGINE=InnoDB;

CREATE TABLE `IPv6Address` (
  `ip` binary(16) NOT NULL,
  `name` char(255) NOT NULL default '',
  `comment` char(255) NOT NULL default '',
  `reserved` enum('yes','no') default NULL,
  PRIMARY KEY  (`ip`)
) ENGINE=InnoDB;

CREATE TABLE `IPv6Allocation` (
  `object_id` int(10) unsigned NOT NULL default '0',
  `ip` binary(16) NOT NULL,
  `name` char(255) NOT NULL default '',
  `type` enum('regular','shared','virtual','router','point2point') NOT NULL DEFAULT 'regular',
  PRIMARY KEY  (`object_id`,`ip`),
  KEY `ip` (`ip`),
  CONSTRAINT `IPv6Allocation-FK-object_id` FOREIGN KEY (`object_id`) REFERENCES `Object` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `IPv6Network` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `ip` binary(16) NOT NULL,
  `mask` int(10) unsigned NOT NULL,
  `last_ip` binary(16) NOT NULL,
  `name` char(255) default NULL,
  `comment` text,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `ip` (`ip`,`mask`)
) ENGINE=InnoDB;

CREATE TABLE `LDAPCache` (
  `presented_username` char(64) NOT NULL,
  `successful_hash` char(40) NOT NULL,
  `first_success` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `last_retry` timestamp NOT NULL default '0000-00-00 00:00:00',
  `displayed_name` char(128) default NULL,
  `memberof` text,
  UNIQUE KEY `presented_username` (`presented_username`),
  KEY `scanidx` (`presented_username`,`successful_hash`)
) ENGINE=InnoDB;

CREATE TABLE `Link` (
  `porta` int(10) unsigned NOT NULL default '0',
  `portb` int(10) unsigned NOT NULL default '0',
  `cable` char(64) DEFAULT NULL,
  PRIMARY KEY  (`porta`,`portb`),
  UNIQUE KEY `porta` (`porta`),
  UNIQUE KEY `portb` (`portb`),
  CONSTRAINT `Link-FK-a` FOREIGN KEY (`porta`) REFERENCES `Port` (`id`) ON DELETE CASCADE,
  CONSTRAINT `Link-FK-b` FOREIGN KEY (`portb`) REFERENCES `Port` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `Molecule` (
  `id` int(10) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB;

CREATE TABLE `MountOperation` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `object_id` int(10) unsigned NOT NULL default '0',
  `ctime` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `user_name` char(64) default NULL,
  `old_molecule_id` int(10) unsigned default NULL,
  `new_molecule_id` int(10) unsigned default NULL,
  `comment` text,
  PRIMARY KEY  (`id`),
  KEY `object_id` (`object_id`),
  CONSTRAINT `MountOperation-FK-object_id` FOREIGN KEY (`object_id`) REFERENCES `Object` (`id`) ON DELETE CASCADE,
  CONSTRAINT `MountOperation-FK-old_molecule_id` FOREIGN KEY (`old_molecule_id`) REFERENCES `Molecule` (`id`) ON DELETE CASCADE,
  CONSTRAINT `MountOperation-FK-new_molecule_id` FOREIGN KEY (`new_molecule_id`) REFERENCES `Molecule` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

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
) ENGINE=InnoDB;

CREATE TABLE `MuninServer` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `base_url` char(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE `ObjectLog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `object_id` int(10) unsigned NOT NULL,
  `user` char(64) NOT NULL,
  `date` datetime NOT NULL,
  `content` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `object_id` (`object_id`),
  KEY `date` (`date`),
  CONSTRAINT `ObjectLog-FK-object_id` FOREIGN KEY (`object_id`) REFERENCES `Object` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `ObjectParentCompat` (
  `parent_objtype_id` int(10) unsigned NOT NULL,
  `child_objtype_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `parent_child` (`parent_objtype_id`,`child_objtype_id`)
) ENGINE=InnoDB;

CREATE TABLE `PatchCableConnector` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `origin` enum('default','custom') NOT NULL DEFAULT 'custom',
  `connector` char(32) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `connector_per_origin` (`connector`,`origin`)
) ENGINE=InnoDB;

CREATE TABLE `PatchCableConnectorCompat` (
  `pctype_id` int(10) unsigned NOT NULL,
  `connector_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`pctype_id`,`connector_id`),
  KEY `connector_id` (`connector_id`),
  CONSTRAINT `PatchCableConnectorCompat-FK-connector_id` FOREIGN KEY (`connector_id`) REFERENCES `PatchCableConnector` (`id`),
  CONSTRAINT `PatchCableConnectorCompat-FK-pctype_id` FOREIGN KEY (`pctype_id`) REFERENCES `PatchCableType` (`id`)
) ENGINE=InnoDB;

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
) ENGINE=InnoDB;

CREATE TABLE `PatchCableHeapLog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `heap_id` int(10) unsigned NOT NULL,
  `date` datetime NOT NULL,
  `user` char(64) NOT NULL,
  `message` char(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `heap_id-date` (`heap_id`,`date`),
  CONSTRAINT `PatchCableHeapLog-FK-heap_id` FOREIGN KEY (`heap_id`) REFERENCES `PatchCableHeap` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `PatchCableOIFCompat` (
  `pctype_id` int(10) unsigned NOT NULL,
  `oif_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`pctype_id`,`oif_id`),
  KEY `oif_id` (`oif_id`),
  CONSTRAINT `PatchCableOIFCompat-FK-oif_id` FOREIGN KEY (`oif_id`) REFERENCES `PortOuterInterface` (`id`),
  CONSTRAINT `PatchCableOIFCompat-FK-pctype_id` FOREIGN KEY (`pctype_id`) REFERENCES `PatchCableType` (`id`)
) ENGINE=InnoDB;

CREATE TABLE `PatchCableType` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `origin` enum('default','custom') NOT NULL DEFAULT 'custom',
  `pctype` char(64) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pctype_per_origin` (`pctype`,`origin`)
) ENGINE=InnoDB;

CREATE TABLE `Port` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `object_id` int(10) unsigned NOT NULL default '0',
  `name` char(255) NOT NULL default '',
  `iif_id` int(10) unsigned NOT NULL,
  `type` int(10) unsigned NOT NULL default '0',
  `l2address` char(64) default NULL,
  `reservation_comment` char(255) default NULL,
  `label` char(255) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `object_iif_oif_name` (`object_id`,`iif_id`,`type`,`name`),
  KEY `type` (`type`),
  KEY `comment` (`reservation_comment`),
  KEY `l2address` (`l2address`),
  KEY `Port-FK-iif-oif` (`iif_id`,`type`),
  CONSTRAINT `Port-FK-iif-oif` FOREIGN KEY (`iif_id`, `type`) REFERENCES `PortInterfaceCompat` (`iif_id`, `oif_id`),
  CONSTRAINT `Port-FK-object_id` FOREIGN KEY (`object_id`) REFERENCES `Object` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `PortAllowedVLAN` (
  `object_id` int(10) unsigned NOT NULL,
  `port_name` char(255) NOT NULL,
  `vlan_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`object_id`,`port_name`,`vlan_id`),
  KEY `vlan_id` (`vlan_id`),
  CONSTRAINT `PortAllowedVLAN-FK-object-port` FOREIGN KEY (`object_id`, `port_name`) REFERENCES `PortVLANMode` (`object_id`, `port_name`) ON DELETE CASCADE,
  CONSTRAINT `PortAllowedVLAN-FK-vlan_id` FOREIGN KEY (`vlan_id`) REFERENCES `VLANValidID` (`vlan_id`)
) ENGINE=InnoDB;

CREATE TABLE `PortCompat` (
  `type1` int(10) unsigned NOT NULL default '0',
  `type2` int(10) unsigned NOT NULL default '0',
  UNIQUE KEY `type1_2` (`type1`,`type2`),
  KEY `type2` (`type2`),
  CONSTRAINT `PortCompat-FK-oif_id1` FOREIGN KEY (`type1`) REFERENCES `PortOuterInterface` (`id`),
  CONSTRAINT `PortCompat-FK-oif_id2` FOREIGN KEY (`type2`) REFERENCES `PortOuterInterface` (`id`)
) ENGINE=InnoDB;

CREATE TABLE `PortInnerInterface` (
  `id` int(10) unsigned NOT NULL,
  `iif_name` char(16) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `iif_name` (`iif_name`)
) ENGINE=InnoDB;

CREATE TABLE `PortInterfaceCompat` (
  `iif_id` int(10) unsigned NOT NULL,
  `oif_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `pair` (`iif_id`,`oif_id`),
  CONSTRAINT `PortInterfaceCompat-FK-iif_id` FOREIGN KEY (`iif_id`) REFERENCES `PortInnerInterface` (`id`),
  CONSTRAINT `PortInterfaceCompat-FK-oif_id` FOREIGN KEY (`oif_id`) REFERENCES `PortOuterInterface` (`id`)
) ENGINE=InnoDB;

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

CREATE TABLE `PortNativeVLAN` (
  `object_id` int(10) unsigned NOT NULL,
  `port_name` char(255) NOT NULL,
  `vlan_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`object_id`,`port_name`,`vlan_id`),
  UNIQUE KEY `port_id` (`object_id`,`port_name`),
  CONSTRAINT `PortNativeVLAN-FK-compound` FOREIGN KEY (`object_id`, `port_name`, `vlan_id`) REFERENCES `PortAllowedVLAN` (`object_id`, `port_name`, `vlan_id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `PortOuterInterface` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `oif_name` char(48) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `oif_name` (`oif_name`)
) ENGINE=InnoDB;

CREATE TABLE `PortVLANMode` (
  `object_id` int(10) unsigned NOT NULL,
  `port_name` char(255) NOT NULL,
  `vlan_mode` enum('access','trunk') NOT NULL default 'access',
  PRIMARY KEY  (`object_id`,`port_name`),
  CONSTRAINT `PortVLANMode-FK-object-port` FOREIGN KEY (`object_id`, `port_name`) REFERENCES `CachedPVM` (`object_id`, `port_name`)
) ENGINE=InnoDB;

CREATE TABLE `Object` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` char(255) default NULL,
  `label` char(255) default NULL,
  `objtype_id` int(10) unsigned NOT NULL default '1',
  `asset_no` char(64) default NULL,
  `has_problems` enum('yes','no') NOT NULL default 'no',
  `comment` text,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `asset_no` (`asset_no`),
  KEY `id-tid` (`id`,`objtype_id`),
  KEY `type_id` (`objtype_id`,`id`)
) ENGINE=InnoDB;

CREATE TABLE `ObjectHistory` (
  `id` int(10) unsigned default NULL,
  `name` char(255) default NULL,
  `label` char(255) default NULL,
  `objtype_id` int(10) unsigned default NULL,
  `asset_no` char(64) default NULL,
  `has_problems` enum('yes','no') NOT NULL default 'no',
  `comment` text,
  `ctime` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `user_name` char(64) default NULL,
  KEY `id` (`id`),
  CONSTRAINT `ObjectHistory-FK-object_id` FOREIGN KEY (`id`) REFERENCES `Object` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `RackSpace` (
  `rack_id` int(10) unsigned NOT NULL default '0',
  `unit_no` int(10) unsigned NOT NULL default '0',
  `atom` enum('front','interior','rear') NOT NULL default 'interior',
  `state` enum('A','U','T') NOT NULL default 'A',
  `object_id` int(10) unsigned default NULL,
  PRIMARY KEY  (`rack_id`,`unit_no`,`atom`),
  KEY `RackSpace_object_id` (`object_id`),
  CONSTRAINT `RackSpace-FK-rack_id` FOREIGN KEY (`rack_id`) REFERENCES `Object` (`id`),
  CONSTRAINT `RackSpace-FK-object_id` FOREIGN KEY (`object_id`) REFERENCES `Object` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `RackThumbnail` (
  `rack_id` int(10) unsigned NOT NULL,
  `thumb_data` blob,
  UNIQUE KEY `rack_id` (`rack_id`),
  CONSTRAINT `RackThumbnail-FK-rack_id` FOREIGN KEY (`rack_id`) REFERENCES `Object` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `Script` (
  `script_name` char(64) NOT NULL,
  `script_text` longtext,
  PRIMARY KEY  (`script_name`)
) ENGINE=InnoDB;

CREATE TABLE `TagStorage` (
  `entity_realm` enum('file','ipv4net','ipv4rspool','ipv4vs','ipvs','ipv6net','location','object','rack','user','vst') NOT NULL default 'object',
  `entity_id` int(10) unsigned NOT NULL,
  `tag_id` int(10) unsigned NOT NULL default '0',
  `tag_is_assignable` enum('yes','no') NOT NULL DEFAULT 'yes',
  `user` char(64) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  UNIQUE KEY `entity_tag` (`entity_realm`,`entity_id`,`tag_id`),
  KEY `entity_id` (`entity_id`),
  KEY `TagStorage-FK-tag_id` (`tag_id`),
  KEY `tag_id-tag_is_assignable` (`tag_id`,`tag_is_assignable`),
  CONSTRAINT `TagStorage-FK-TagTree` FOREIGN KEY (`tag_id`, `tag_is_assignable`) REFERENCES `TagTree` (`id`, `is_assignable`)
) ENGINE=InnoDB;

CREATE TABLE `TagTree` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `parent_id` int(10) unsigned default NULL,
  `is_assignable` enum('yes','no') NOT NULL DEFAULT 'yes',
  `tag` char(255) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `tag` (`tag`),
  KEY `TagTree-K-parent_id` (`parent_id`),
  KEY `id-is_assignable` (`id`,`is_assignable`),
  CONSTRAINT `TagTree-K-parent_id` FOREIGN KEY (`parent_id`) REFERENCES `TagTree` (`id`)
) ENGINE=InnoDB;

CREATE TABLE `UserAccount` (
  `user_id` int(10) unsigned NOT NULL auto_increment,
  `user_name` char(64) NOT NULL default '',
  `user_password_hash` char(40) default NULL,
  `user_realname` char(64) default NULL,
  PRIMARY KEY  (`user_id`),
  UNIQUE KEY `user_name` (`user_name`)
) ENGINE=InnoDB;

CREATE TABLE `UserConfig` (
  `varname` char(32) NOT NULL,
  `varvalue` text NOT NULL,
  `user` char(64) NOT NULL,
  UNIQUE KEY `user_varname` (`user`,`varname`),
  KEY `varname` (`varname`),
  CONSTRAINT `UserConfig-FK-varname` FOREIGN KEY (`varname`) REFERENCES `Config` (`varname`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `VLANDescription` (
  `domain_id` int(10) unsigned NOT NULL,
  `vlan_id` int(10) unsigned NOT NULL default '0',
  `vlan_type` enum('ondemand','compulsory','alien') NOT NULL default 'ondemand',
  `vlan_descr` char(255) default NULL,
  PRIMARY KEY  (`domain_id`,`vlan_id`),
  KEY `vlan_id` (`vlan_id`),
  CONSTRAINT `VLANDescription-FK-domain_id` FOREIGN KEY (`domain_id`) REFERENCES `VLANDomain` (`id`) ON DELETE CASCADE,
  CONSTRAINT `VLANDescription-FK-vlan_id` FOREIGN KEY (`vlan_id`) REFERENCES `VLANValidID` (`vlan_id`)
) ENGINE=InnoDB;

CREATE TABLE `VLANDomain` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `description` char(255) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `description` (`description`)
) ENGINE=InnoDB;

CREATE TABLE `VLANIPv4` (
  `domain_id` int(10) unsigned NOT NULL,
  `vlan_id` int(10) unsigned NOT NULL,
  `ipv4net_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `network-domain-vlan` (`ipv4net_id`,`domain_id`,`vlan_id`),
  KEY `VLANIPv4-FK-compound` (`domain_id`,`vlan_id`),
  CONSTRAINT `VLANIPv4-FK-compound` FOREIGN KEY (`domain_id`, `vlan_id`) REFERENCES `VLANDescription` (`domain_id`, `vlan_id`) ON DELETE CASCADE,
  CONSTRAINT `VLANIPv4-FK-ipv4net_id` FOREIGN KEY (`ipv4net_id`) REFERENCES `IPv4Network` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `VLANIPv6` (
  `domain_id` int(10) unsigned NOT NULL,
  `vlan_id` int(10) unsigned NOT NULL,
  `ipv6net_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `network-domain-vlan` (`ipv6net_id`,`domain_id`,`vlan_id`),
  KEY `VLANIPv6-FK-compound` (`domain_id`,`vlan_id`),
  CONSTRAINT `VLANIPv6-FK-compound` FOREIGN KEY (`domain_id`, `vlan_id`) REFERENCES `VLANDescription` (`domain_id`, `vlan_id`) ON DELETE CASCADE,
  CONSTRAINT `VLANIPv6-FK-ipv6net_id` FOREIGN KEY (`ipv6net_id`) REFERENCES `IPv6Network` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `VLANSTRule` (
  `vst_id` int(10) unsigned NOT NULL,
  `rule_no` int(10) unsigned NOT NULL,
  `port_pcre` char(255) NOT NULL,
  `port_role` enum('access','trunk','anymode','uplink','downlink','none') NOT NULL default 'none',
  `wrt_vlans` text,
  `description` char(255) default NULL,
  UNIQUE KEY `vst-rule` (`vst_id`,`rule_no`),
  CONSTRAINT `VLANSTRule-FK-vst_id` FOREIGN KEY (`vst_id`) REFERENCES `VLANSwitchTemplate` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

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
  CONSTRAINT `VLANSwitch-FK-object_id` FOREIGN KEY (`object_id`) REFERENCES `Object` (`id`),
  CONSTRAINT `VLANSwitch-FK-template_id` FOREIGN KEY (`template_id`) REFERENCES `VLANSwitchTemplate` (`id`)
) ENGINE=InnoDB;

CREATE TABLE `VLANSwitchTemplate` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `mutex_rev` int(10) NOT NULL,
  `description` char(255) default NULL,
  `saved_by` char(64) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `description` (`description`)
) ENGINE=InnoDB;

CREATE TABLE `VLANValidID` (
  `vlan_id` int(10) unsigned NOT NULL default '1',
  PRIMARY KEY  (`vlan_id`)
) ENGINE=InnoDB;

CREATE TABLE `VS` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(255) DEFAULT NULL,
  `vsconfig` text,
  `rsconfig` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB;

CREATE TABLE `VSIPs` (
  `vs_id` int(10) unsigned NOT NULL,
  `vip` varbinary(16) NOT NULL,
  `vsconfig` text,
  `rsconfig` text,
  PRIMARY KEY (`vs_id`,`vip`),
  KEY `vip` (`vip`),
  CONSTRAINT `VSIPs-vs_id` FOREIGN KEY (`vs_id`) REFERENCES `VS` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `VSPorts` (
  `vs_id` int(10) unsigned NOT NULL,
  `proto` enum('TCP','UDP','MARK') NOT NULL,
  `vport` int(10) unsigned NOT NULL,
  `vsconfig` text,
  `rsconfig` text,
  PRIMARY KEY (`vs_id`,`proto`,`vport`),
  KEY `proto-vport` (`proto`,`vport`),
  CONSTRAINT `VS-vs_id` FOREIGN KEY (`vs_id`) REFERENCES `VS` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

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
  CONSTRAINT `VSEnabledIPs-FK-object_id` FOREIGN KEY (`object_id`) REFERENCES `Object` (`id`) ON DELETE CASCADE,
  CONSTRAINT `VSEnabledIPs-FK-rspool_id` FOREIGN KEY (`rspool_id`) REFERENCES `IPv4RSPool` (`id`) ON DELETE CASCADE,
  CONSTRAINT `VSEnabledIPs-FK-vs_id-vip` FOREIGN KEY (`vs_id`, `vip`) REFERENCES `VSIPs` (`vs_id`, `vip`) ON DELETE CASCADE
) ENGINE=InnoDB;

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
) ENGINE=InnoDB;

SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;


-- ###############################
-- Triggers

-- Set the delimiter for trigger creation, or mysql complains about
-- syntax errors during execution.
delimiter //

CREATE TRIGGER `EntityLink-before-insert` BEFORE INSERT ON `EntityLink` FOR EACH ROW
EntityLinkTrigger:BEGIN
  DECLARE parent_objtype, child_objtype, count INTEGER;

  -- forbid linking an entity to itself
  IF NEW.parent_entity_type = NEW.child_entity_type AND NEW.parent_entity_id = NEW.child_entity_id THEN
    SET NEW.parent_entity_id = NULL;
    LEAVE EntityLinkTrigger;
  END IF;

  -- in some scenarios, only one parent is allowed
  CASE CONCAT(NEW.parent_entity_type, '.', NEW.child_entity_type)
    WHEN 'location.location' THEN
      SELECT COUNT(*) INTO count FROM EntityLink WHERE parent_entity_type = 'location' AND child_entity_type = 'location' AND child_entity_id = NEW.child_entity_id;
    WHEN 'location.row' THEN
      SELECT COUNT(*) INTO count FROM EntityLink WHERE parent_entity_type = 'location' AND child_entity_type = 'row' AND child_entity_id = NEW.child_entity_id;
    WHEN 'row.rack' THEN
      SELECT COUNT(*) INTO count FROM EntityLink WHERE parent_entity_type = 'row' AND child_entity_type = 'rack' AND child_entity_id = NEW.child_entity_id;
    ELSE
      -- some other scenario, assume it is valid
      SET count = 0;
  END CASE; 
  IF count > 0 THEN
    SET NEW.parent_entity_id = NULL;
    LEAVE EntityLinkTrigger;
  END IF;

  IF NEW.parent_entity_type = 'object' AND NEW.child_entity_type = 'object' THEN
    -- lock objects to prevent concurrent link establishment
    SELECT objtype_id INTO parent_objtype FROM Object WHERE id = NEW.parent_entity_id FOR UPDATE;
    SELECT objtype_id INTO child_objtype FROM Object WHERE id = NEW.child_entity_id FOR UPDATE;

    -- only permit the link if object types are compatibile
    SELECT COUNT(*) INTO count FROM ObjectParentCompat WHERE parent_objtype_id = parent_objtype AND child_objtype_id = child_objtype;
    IF count = 0 THEN
      SET NEW.parent_entity_id = NULL;
    END IF;
  END IF;
END;
// -- execute

CREATE TRIGGER `EntityLink-before-update` BEFORE UPDATE ON `EntityLink` FOR EACH ROW
EntityLinkTrigger:BEGIN
  DECLARE parent_objtype, child_objtype, count INTEGER;

  -- forbid linking an entity to itself
  IF NEW.parent_entity_type = NEW.child_entity_type AND NEW.parent_entity_id = NEW.child_entity_id THEN
    SET NEW.parent_entity_id = NULL;
    LEAVE EntityLinkTrigger;
  END IF;

  -- in some scenarios, only one parent is allowed
  CASE CONCAT(NEW.parent_entity_type, '.', NEW.child_entity_type)
    WHEN 'location.location' THEN
      SELECT COUNT(*) INTO count FROM EntityLink WHERE parent_entity_type = 'location' AND child_entity_type = 'location' AND child_entity_id = NEW.child_entity_id AND id != NEW.id;
    WHEN 'location.row' THEN
      SELECT COUNT(*) INTO count FROM EntityLink WHERE parent_entity_type = 'location' AND child_entity_type = 'row' AND child_entity_id = NEW.child_entity_id AND id != NEW.id;
    WHEN 'row.rack' THEN
      SELECT COUNT(*) INTO count FROM EntityLink WHERE parent_entity_type = 'row' AND child_entity_type = 'rack' AND child_entity_id = NEW.child_entity_id AND id != NEW.id;
    ELSE
      -- some other scenario, assume it is valid
      SET count = 0;
  END CASE; 
  IF count > 0 THEN
    SET NEW.parent_entity_id = NULL;
    LEAVE EntityLinkTrigger;
  END IF;

  IF NEW.parent_entity_type = 'object' AND NEW.child_entity_type = 'object' THEN
    -- lock objects to prevent concurrent link establishment
    SELECT objtype_id INTO parent_objtype FROM Object WHERE id = NEW.parent_entity_id FOR UPDATE;
    SELECT objtype_id INTO child_objtype FROM Object WHERE id = NEW.child_entity_id FOR UPDATE;

    -- only permit the link if object types are compatibile
    SELECT COUNT(*) INTO count FROM ObjectParentCompat WHERE parent_objtype_id = parent_objtype AND child_objtype_id = child_objtype;
    IF count = 0 THEN
      SET NEW.parent_entity_id = NULL;
    END IF;
  END IF;
END;
// -- execute

CREATE TRIGGER `Link-before-insert` BEFORE INSERT ON `Link` FOR EACH ROW
LinkTrigger:BEGIN
  DECLARE tmp, porta_type, portb_type, count INTEGER;

  IF NEW.porta = NEW.portb THEN
    -- forbid connecting a port to itself
    SET NEW.porta = NULL;
    LEAVE LinkTrigger;
  ELSEIF NEW.porta > NEW.portb THEN
    -- force porta < portb
    SET tmp = NEW.porta;
    SET NEW.porta = NEW.portb;
    SET NEW.portb = tmp;
  END IF; 

  -- lock ports to prevent concurrent link establishment
  SELECT type INTO porta_type FROM Port WHERE id = NEW.porta FOR UPDATE;
  SELECT type INTO portb_type FROM Port WHERE id = NEW.portb FOR UPDATE;

  -- only permit the link if ports are compatibile
  SELECT COUNT(*) INTO count FROM PortCompat WHERE (type1 = porta_type AND type2 = portb_type) OR (type1 = portb_type AND type2 = porta_type);
  IF count = 0 THEN
    SET NEW.porta = NULL;
  END IF;
END;
// -- execute

CREATE TRIGGER `Link-before-update` BEFORE UPDATE ON `Link` FOR EACH ROW
LinkTrigger:BEGIN
  DECLARE tmp, porta_type, portb_type, count INTEGER;

  IF NEW.porta = NEW.portb THEN
    -- forbid connecting a port to itself
    SET NEW.porta = NULL;
    LEAVE LinkTrigger;
  ELSEIF NEW.porta > NEW.portb THEN
    -- force porta < portb
    SET tmp = NEW.porta;
    SET NEW.porta = NEW.portb;
    SET NEW.portb = tmp;
  END IF; 

  -- lock ports to prevent concurrent link establishment
  SELECT type INTO porta_type FROM Port WHERE id = NEW.porta FOR UPDATE;
  SELECT type INTO portb_type FROM Port WHERE id = NEW.portb FOR UPDATE;

  -- only permit the link if ports are compatibile
  SELECT COUNT(*) INTO count FROM PortCompat WHERE (type1 = porta_type AND type2 = portb_type) OR (type1 = portb_type AND type2 = porta_type);
  IF count = 0 THEN
    SET NEW.porta = NULL;
  END IF;
END;
// -- execute

-- Restore delimiter to usual value.
delimiter ;


-- ###############################
-- Views

CREATE VIEW `Location` AS SELECT O.id, O.name, O.has_problems, O.comment, P.id AS parent_id, P.name AS parent_name
FROM `Object` O
LEFT JOIN (
  `Object` P INNER JOIN `EntityLink` EL
  ON EL.parent_entity_id = P.id AND P.objtype_id = 1562 AND EL.parent_entity_type = 'location' AND EL.child_entity_type = 'location'
) ON EL.child_entity_id = O.id
WHERE O.objtype_id = 1562;

CREATE VIEW `Row` AS SELECT O.id, O.name, L.id AS location_id, L.name AS location_name
  FROM `Object` O
  LEFT JOIN `EntityLink` EL ON O.id = EL.child_entity_id AND EL.parent_entity_type = 'location' AND EL.child_entity_type = 'row'
  LEFT JOIN `Object` L ON EL.parent_entity_id = L.id AND L.objtype_id = 1562
  WHERE O.objtype_id = 1561;

CREATE VIEW `Rack` AS SELECT O.id, O.name AS name, O.asset_no, O.has_problems, O.comment,
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
  WHERE O.objtype_id = 1560;

CREATE VIEW `RackObject` AS SELECT id, name, label, objtype_id, asset_no, has_problems, comment FROM `Object`
 WHERE `objtype_id` NOT IN (1560, 1561, 1562);

