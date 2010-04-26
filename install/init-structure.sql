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
) ENGINE=InnoDB;

CREATE TABLE `AttributeValue` (
  `object_id` int(10) unsigned default NULL,
  `attr_id` int(10) unsigned default NULL,
  `string_value` char(128) default NULL,
  `uint_value` int(10) unsigned default NULL,
  `float_value` float default NULL,
  UNIQUE KEY `object_id` (`object_id`,`attr_id`),
  CONSTRAINT `AttributeValue-FK-object_id` FOREIGN KEY (`object_id`) REFERENCES `RackObject` (`id`)
) ENGINE=InnoDB;

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
  `is_userdefined` enum('yes','no') NOT NULL default 'no',
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

CREATE TABLE `IPv4RSPool` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` char(255) default NULL,
  `vsconfig` text,
  `rsconfig` text,
  PRIMARY KEY  (`id`)
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
) ENGINE=InnoDB;

CREATE TABLE `IPv4LB` (
  `object_id` int(10) unsigned default NULL,
  `rspool_id` int(10) unsigned default NULL,
  `vs_id` int(10) unsigned default NULL,
  `vsconfig` text,
  `rsconfig` text,
  UNIQUE KEY `LB-VS` (`object_id`,`vs_id`),
  KEY `IPv4LB-FK-rspool_id` (`rspool_id`),
  KEY `IPv4LB-FK-vs_id` (`vs_id`),
  CONSTRAINT `IPv4LB-FK-vs_id` FOREIGN KEY (`vs_id`) REFERENCES `IPv4VS` (`id`),
  CONSTRAINT `IPv4LB-FK-object_id` FOREIGN KEY (`object_id`) REFERENCES `RackObject` (`id`),
  CONSTRAINT `IPv4LB-FK-rspool_id` FOREIGN KEY (`rspool_id`) REFERENCES `IPv4RSPool` (`id`)
) ENGINE=InnoDB;

CREATE TABLE `IPv4Network` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `ip` int(10) unsigned NOT NULL,
  `mask` int(10) unsigned NOT NULL,
  `name` char(255) default NULL,
  `comment` text,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `base-len` (`ip`,`mask`)
) ENGINE=InnoDB;

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
  PRIMARY KEY  (`id`),
  KEY `object_id` (`object_id`)
) ENGINE=MyISAM;

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
  CONSTRAINT `PortInterfaceCompat-FK-iif_id` FOREIGN KEY (`iif_id`) REFERENCES `PortInnerInterface` (`id`)
) ENGINE=InnoDB;

CREATE TABLE `Port` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `object_id` int(10) unsigned NOT NULL,
  `name` char(255) NOT NULL,
  `iif_id` int(10) unsigned NOT NULL,
  `type` int(10) unsigned NOT NULL,
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
  CONSTRAINT `Port-FK-object_id` FOREIGN KEY (`object_id`) REFERENCES `RackObject` (`id`)
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
  KEY `object_id` (`object_id`),
  CONSTRAINT `IPv4NAT-FK-object_id` FOREIGN KEY (`object_id`) REFERENCES `RackObject` (`id`)
) ENGINE=InnoDB;

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

CREATE TABLE `TagTree` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `parent_id` int(10) unsigned default NULL,
  `tag` char(255) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `tag` (`tag`),
  KEY `TagTree-K-parent_id` (`parent_id`),
  CONSTRAINT `TagTree-K-parent_id` FOREIGN KEY (`parent_id`) REFERENCES `TagTree` (`id`)
) TYPE=InnoDB;

CREATE TABLE `TagStorage` (
  `entity_realm` enum('file','ipv4net','ipv4vs','ipv4rspool','object','rack','user') NOT NULL default 'object',
  `entity_id` int(10) unsigned NOT NULL,
  `tag_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `entity_tag` (`entity_realm`,`entity_id`,`tag_id`),
  KEY `entity_id` (`entity_id`),
  KEY `TagStorage-FK-tag_id` (`tag_id`),
  CONSTRAINT `TagStorage-FK-tag_id` FOREIGN KEY (`tag_id`) REFERENCES `TagTree` (`id`)
) TYPE=InnoDB;

CREATE TABLE `UserAccount` (
  `user_id` int(10) unsigned NOT NULL auto_increment,
  `user_name` char(64) NOT NULL,
  `user_password_hash` char(40) default NULL,
  `user_realname` char(64) default NULL,
  PRIMARY KEY  (`user_id`),
  UNIQUE KEY `user_name` (`user_name`)
) ENGINE=MyISAM AUTO_INCREMENT=10000;

CREATE TABLE `UserConfig` (
  `varname` char(32) NOT NULL,
  `varvalue` char(255) NOT NULL,
  `user` char(64) NOT NULL,
  UNIQUE KEY `user_varname` (`user`,`varname`)
) TYPE=InnoDB;

CREATE TABLE `VLANDomain` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `description` char(255) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `description` (`description`)
) ENGINE=InnoDB;

CREATE TABLE `VLANEligibleOIF` (
  `oif_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`oif_id`)
) ENGINE=InnoDB;

CREATE TABLE `VLANValidID` (
  `vlan_id` int(10) unsigned NOT NULL default '1',
  PRIMARY KEY  (`vlan_id`)
) ENGINE=InnoDB;

CREATE TABLE `VLANDescription` (
  `domain_id` int(10) unsigned NOT NULL,
  `vlan_id` int(10) unsigned NOT NULL default '0',
  `vlan_type` enum('ondemand','compulsory','alien') NOT NULL default 'ondemand',
  `vlan_descr` char(255) default NULL,
  PRIMARY KEY  (`domain_id`,`vlan_id`),
  KEY `vlan_id` (`vlan_id`),
  CONSTRAINT `VLANDescription-FK-domain_id` FOREIGN KEY (`domain_id`) REFERENCES `VLANDomain` (`id`),
  CONSTRAINT `VLANDescription-FK-vlan_id` FOREIGN KEY (`vlan_id`) REFERENCES `VLANValidID` (`vlan_id`)
) ENGINE=InnoDB;

CREATE TABLE `VLANIPv4` (
  `domain_id` int(10) unsigned NOT NULL,
  `vlan_id` int(10) unsigned NOT NULL,
  `ipv4net_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `network-domain` (`ipv4net_id`,`domain_id`),
  KEY `VLANIPv4-FK-compound` (`domain_id`,`vlan_id`),
  CONSTRAINT `VLANIPv4-FK-compound` FOREIGN KEY (`domain_id`, `vlan_id`) REFERENCES `VLANDescription` (`domain_id`, `vlan_id`) ON DELETE CASCADE,
  CONSTRAINT `VLANIPv4-FK-ipv4net_id` FOREIGN KEY (`ipv4net_id`) REFERENCES `IPv4Network` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `VLANSwitchTemplate` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `max_local_vlans` int(10) unsigned default NULL,
  `description` char(255) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `description` (`description`)
) ENGINE=InnoDB;

CREATE TABLE `VLANSTRule` (
  `vst_id` int(10) unsigned NOT NULL,
  `rule_no` int(10) unsigned NOT NULL,
  `port_pcre` char(255) NOT NULL,
  `port_role` enum('access','trunk','uplink','downlink') NOT NULL default 'access',
  `wrt_vlans` char(255) default NULL,
  UNIQUE KEY `vst-rule` (`vst_id`,`rule_no`),
  CONSTRAINT `VLANSTRule-FK-vst_id` FOREIGN KEY (`vst_id`) REFERENCES `VLANSwitchTemplate` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `VLANSwitch` (
  `object_id` int(10) unsigned NOT NULL,
  `domain_id` int(10) unsigned NOT NULL,
  `template_id` int(10) unsigned NOT NULL,
  `mutex_rev` int(10) unsigned NOT NULL default '0',
  `last_edited` timestamp NOT NULL default '0000-00-00 00:00:00',
  `last_push_failed` timestamp NOT NULL default '0000-00-00 00:00:00',
  `last_push_done` timestamp NOT NULL default '0000-00-00 00:00:00',
  `last_pull_failed` timestamp NOT NULL default '0000-00-00 00:00:00',
  `last_pull_done` timestamp NOT NULL default '0000-00-00 00:00:00',
  UNIQUE KEY `object_id` (`object_id`),
  KEY `domain_id` (`domain_id`),
  KEY `template_id` (`template_id`),
  CONSTRAINT `VLANSwitch-FK-template_id` FOREIGN KEY (`template_id`) REFERENCES `VLANSwitchTemplate` (`id`),
  CONSTRAINT `VLANSwitch-FK-object_id` FOREIGN KEY (`object_id`) REFERENCES `RackObject` (`id`),
  CONSTRAINT `VLANSwitch-FK-domain_id` FOREIGN KEY (`domain_id`) REFERENCES `VLANDomain` (`id`)
) ENGINE=InnoDB;

CREATE TABLE `CachedPVM` (
  `object_id` int(10) unsigned NOT NULL,
  `port_name` char(255) NOT NULL,
  `vlan_mode` enum('access','trunk') NOT NULL default 'access',
  PRIMARY KEY  (`object_id`,`port_name`),
  CONSTRAINT `CachedPVM-FK-object_id` FOREIGN KEY (`object_id`) REFERENCES `RackObject` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `CachedPAV` (
  `object_id` int(10) unsigned NOT NULL,
  `port_name` char(255) NOT NULL,
  `vlan_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`object_id`,`port_name`,`vlan_id`),
  KEY `vlan_id` (`vlan_id`),
  CONSTRAINT `CachedPAV-FK-vlan_id` FOREIGN KEY (`vlan_id`) REFERENCES `VLANValidID` (`vlan_id`),
  CONSTRAINT `CachedPAV-FK-object-port` FOREIGN KEY (`object_id`, `port_name`) REFERENCES `CachedPVM` (`object_id`, `port_name`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `CachedPNV` (
  `object_id` int(10) unsigned NOT NULL,
  `port_name` char(255) NOT NULL,
  `vlan_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`object_id`,`port_name`,`vlan_id`),
  UNIQUE KEY `port_id` (`object_id`,`port_name`),
  CONSTRAINT `CachedPNV-FK-compound` FOREIGN KEY (`object_id`, `port_name`, `vlan_id`) REFERENCES `CachedPAV` (`object_id`, `port_name`, `vlan_id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `PortVLANMode` (
  `object_id` int(10) unsigned NOT NULL,
  `port_name` char(255) NOT NULL,
  `vlan_mode` enum('access','trunk') NOT NULL default 'access',
  PRIMARY KEY  (`object_id`,`port_name`),
  CONSTRAINT `PortVLANMode-FK-object-port` FOREIGN KEY (`object_id`, `port_name`) REFERENCES `CachedPVM` (`object_id`, `port_name`)
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

CREATE TABLE `PortNativeVLAN` (
  `object_id` int(10) unsigned NOT NULL,
  `port_name` char(255) NOT NULL,
  `vlan_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`object_id`,`port_name`,`vlan_id`),
  UNIQUE KEY `port_id` (`object_id`,`port_name`),
  CONSTRAINT `PortNativeVLAN-FK-compound` FOREIGN KEY (`object_id`, `port_name`, `vlan_id`) REFERENCES `PortAllowedVLAN` (`object_id`, `port_name`, `vlan_id`) ON DELETE CASCADE
) ENGINE=InnoDB;

