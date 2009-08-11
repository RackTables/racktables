alter database character set utf8;
set names 'utf8';

CREATE TABLE `Atom` (
  `molecule_id` int(10) unsigned default NULL,
  `rack_id` int(10) unsigned default NULL,
  `unit_no` int(10) unsigned default NULL,
  `atom` enum('front','interior','rear') default NULL
) ENGINE=MyISAM;

CREATE TABLE `Attribute` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `type` enum('string','uint','float','dict') default NULL,
  `name` char(64) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=10000;

CREATE TABLE `AttributeMap` (
  `objtype_id` int(10) unsigned NOT NULL default '1',
  `attr_id` int(10) unsigned NOT NULL default '1',
  `chapter_id` int(10) unsigned NULL,
  UNIQUE KEY `objtype_id` (`objtype_id`,`attr_id`)
) ENGINE=MyISAM;

CREATE TABLE `AttributeValue` (
  `object_id` int(10) unsigned default NULL,
  `attr_id` int(10) unsigned default NULL,
  `string_value` char(128) default NULL,
  `uint_value` int(10) unsigned default NULL,
  `float_value` float default NULL,
  UNIQUE KEY `object_id` (`object_id`,`attr_id`)
) ENGINE=MyISAM;

CREATE TABLE `Chapter` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `sticky` enum('yes','no') default 'no',
  `name` char(128) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=10000;

CREATE TABLE `Config` (
  `varname` char(32) NOT NULL,
  `varvalue` char(255) NOT NULL,
  `vartype` enum('string','uint') NOT NULL default 'string',
  `emptyok` enum('yes','no') NOT NULL default 'no',
  `is_hidden` enum('yes','no') NOT NULL default 'yes',
  `description` text,
  PRIMARY KEY  (`varname`)
) ENGINE=MyISAM;

CREATE TABLE `Dictionary` (
  `chapter_id` int(10) unsigned NOT NULL,
  `dict_key` int(10) unsigned NOT NULL auto_increment,
  `dict_value` char(255) default NULL,
  PRIMARY KEY  (`dict_key`),
  UNIQUE KEY `chap_to_val` (`chapter_id`,`dict_value`)
) ENGINE=MyISAM AUTO_INCREMENT=50000;

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
) ENGINE=InnoDB;

CREATE TABLE `FileLink` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `file_id` int(10) unsigned NOT NULL,
  `entity_type` enum('ipv4net','ipv4rspool','ipv4vs','object','rack','user') NOT NULL default 'object',
  `entity_id` int(10) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `FileLink-file_id` (`file_id`),
  UNIQUE KEY `FileLink-unique` (`file_id`,`entity_type`,`entity_id`),
  CONSTRAINT `FileLink-File_fkey` FOREIGN KEY (`file_id`) REFERENCES `File` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `IPv4Address` (
  `ip` int(10) unsigned NOT NULL,
  `name` char(255) NOT NULL,
  `reserved` enum('yes','no') default NULL,
  PRIMARY KEY  (`ip`)
) ENGINE=MyISAM;

CREATE TABLE `IPv4Allocation` (
  `object_id` int(10) unsigned NOT NULL,
  `ip` int(10) unsigned NOT NULL,
  `name` char(255) NOT NULL,
  `type` enum('regular','shared','virtual','router') default NULL,
  PRIMARY KEY  (`object_id`,`ip`)
) ENGINE=MyISAM;

CREATE TABLE `IPv4LB` (
  `object_id` int(10) unsigned default NULL,
  `rspool_id` int(10) unsigned default NULL,
  `vs_id` int(10) unsigned default NULL,
  `vsconfig` text,
  `rsconfig` text,
  UNIQUE KEY `LB-VS` (`object_id`,`vs_id`)
) ENGINE=MyISAM;

CREATE TABLE `IPv4RSPool` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` char(255) default NULL,
  `vsconfig` text,
  `rsconfig` text,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB;

CREATE TABLE `IPv4Network` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `ip` int(10) unsigned NOT NULL,
  `mask` int(10) unsigned NOT NULL,
  `name` char(255) default NULL,
  `comment` text,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `base-len` (`ip`,`mask`)
) ENGINE=MyISAM;

CREATE TABLE `IPv4RS` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `inservice` enum('yes','no') NOT NULL default 'no',
  `rsip` int(10) unsigned default NULL,
  `rsport` smallint(5) unsigned default NULL,
  `rspool_id` int(10) unsigned default NULL,
  `rsconfig` text,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `pool-endpoint` (`rspool_id`,`rsip`,`rsport`),
  CONSTRAINT `IPv4RS-FK` FOREIGN KEY (`rspool_id`) REFERENCES `IPv4RSPool` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `IPv4VS` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `vip` int(10) unsigned default NULL,
  `vport` smallint(5) unsigned default NULL,
  `proto` enum('TCP','UDP') NOT NULL default 'TCP',
  `name` char(255) default NULL,
  `vsconfig` text,
  `rsconfig` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;

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

CREATE TABLE `Molecule` (
  `id` int(10) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;

CREATE TABLE `MountOperation` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `object_id` int(10) unsigned NOT NULL,
  `ctime` timestamp NOT NULL,
  `user_name` char(64) default NULL,
  `old_molecule_id` int(10) unsigned default NULL,
  `new_molecule_id` int(10) unsigned default NULL,
  `comment` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;

CREATE TABLE `Port` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `object_id` int(10) unsigned NOT NULL,
  `name` char(255) NOT NULL,
  `type` int(10) unsigned NOT NULL,
  `l2address` char(64) default NULL,
  `reservation_comment` char(255) default NULL,
  `label` char(255) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `per_object` (`object_id`,`name`,`type`),
  KEY `type` (`type`),
  KEY `comment` (`reservation_comment`),
  KEY `l2address` (`l2address`)
) ENGINE=InnoDB;

CREATE TABLE `Link` (
  `porta` int(10) unsigned NOT NULL,
  `portb` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`porta`,`portb`),
  UNIQUE KEY `porta` (`porta`),
  UNIQUE KEY `portb` (`portb`),
  CONSTRAINT `Link-FK-a` FOREIGN KEY (`porta`) REFERENCES `Port` (`id`),
  CONSTRAINT `Link-FK-b` FOREIGN KEY (`portb`) REFERENCES `Port` (`id`)
) ENGINE=InnoDB;

CREATE TABLE `PortCompat` (
  `type1` int(10) unsigned NOT NULL,
  `type2` int(10) unsigned NOT NULL,
  UNIQUE KEY `type1_2` (`type1`,`type2`),
  KEY `type2` (`type2`)
) ENGINE=MyISAM;

CREATE TABLE `IPv4NAT` (
  `object_id` int(10) unsigned NOT NULL,
  `proto` enum('TCP','UDP') not null default 'TCP',
  `localip` int(10) unsigned NOT NULL,
  `localport` smallint(5) unsigned NOT NULL,
  `remoteip` int(10) unsigned NOT NULL,
  `remoteport` smallint(5) unsigned NOT NULL,
  `description` char(255) default NULL,
  PRIMARY KEY  (`object_id`,`proto`,`localip`,`localport`,`remoteip`,`remoteport`),
  KEY `localip` (`localip`),
  KEY `remoteip` (`remoteip`),
  KEY `object_id` (`object_id`)
) ENGINE=MyISAM;

CREATE TABLE `RackRow` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` char(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;

CREATE TABLE `Rack` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` char(255) default NULL,
  `row_id` int(10) unsigned NOT NULL default '1',
  `height` tinyint(3) unsigned NOT NULL default '42',
  `comment` text,
  `thumb_data` blob,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name_in_row` (`row_id`,`name`)
) ENGINE=MyISAM;

CREATE TABLE `RackHistory` (
  `id` int(10) unsigned default NULL,
  `name` char(255) default NULL,
  `row_id` int(10) unsigned default NULL,
  `height` tinyint(3) unsigned default NULL,
  `comment` text,
  `thumb_data` blob,
  `ctime` timestamp NOT NULL,
  `user_name` char(64) default NULL
) ENGINE=MyISAM;

CREATE TABLE `RackObject` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` char(255) default NULL,
  `label` char(255) default NULL,
  `barcode` char(16) default NULL,
  `objtype_id` int(10) unsigned NOT NULL default '1',
  `asset_no` char(64) default NULL,
  `has_problems` enum('yes','no') NOT NULL default 'no',
  `comment` text,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `RackObject_asset_no` (`asset_no`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `barcode` (`barcode`)
) ENGINE=MyISAM;

CREATE TABLE `RackObjectHistory` (
  `id` int(10) unsigned default NULL,
  `name` char(255) default NULL,
  `label` char(255) default NULL,
  `barcode` char(16) default NULL,
  `objtype_id` int(10) unsigned default NULL,
  `asset_no` char(64) default NULL,
  `has_problems` enum('yes','no') NOT NULL default 'no',
  `comment` text,
  `ctime` timestamp NOT NULL,
  `user_name` char(64) default NULL
) ENGINE=MyISAM;

CREATE TABLE `RackSpace` (
  `rack_id` int(10) unsigned NOT NULL default '0',
  `unit_no` int(10) unsigned NOT NULL default '0',
  `atom` enum('front','interior','rear') NOT NULL default 'interior',
  `state` enum('A','U','T','W') NOT NULL default 'A',
  `object_id` int(10) unsigned default NULL,
  PRIMARY KEY  (`rack_id`,`unit_no`,`atom`),
  KEY `RackSpace_object_id` (`object_id`)
) ENGINE=MyISAM;

CREATE TABLE `Script` (
  `script_name` char(64) NOT NULL,
  `script_text` longtext,
  PRIMARY KEY  (`script_name`)
) TYPE=MyISAM;

CREATE TABLE `TagStorage` (
  `entity_realm` enum('file','ipv4net','ipv4vs','ipv4rspool','object','rack','user') NOT NULL default 'object',
  `entity_id` int(10) unsigned NOT NULL,
  `tag_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `entity_tag` (`entity_realm`,`entity_id`,`tag_id`),
  KEY `entity_id` (`entity_id`)
) TYPE=MyISAM;

CREATE TABLE `TagTree` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `parent_id` int(10) unsigned default NULL,
  `valid_realm` set('file','ipv4net','ipv4vs','ipv4rspool','object','rack','user') NOT NULL default 'file,ipv4net,ipv4vs,ipv4rspool,object,rack,user',
  `tag` char(255) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `tag` (`tag`)
) TYPE=MyISAM;

CREATE TABLE `UserAccount` (
  `user_id` int(10) unsigned NOT NULL auto_increment,
  `user_name` char(64) NOT NULL,
  `user_password_hash` char(40) default NULL,
  `user_realname` char(64) default NULL,
  PRIMARY KEY  (`user_id`),
  UNIQUE KEY `user_name` (`user_name`)
) ENGINE=MyISAM AUTO_INCREMENT=10000;
