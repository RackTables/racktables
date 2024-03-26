CREATE TABLE IF NOT EXISTS `AttributeExtend` (
  `name` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `required` enum('Y','N') DEFAULT NULL,
  `group` varchar(128) DEFAULT NULL COMMENT 'main group',
  `sub_group` varchar(128) DEFAULT NULL COMMENT 'sub group',
  `sort` tinyint(3) unsigned DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

REPLACE INTO `AttributeExtend` (`name`, `required`, `group`, `sub_group`, `sort`) VALUES
	('HW type', 'Y', 'System', NULL, 2),
	('FiMo AssetID', 'N', 'Common', NULL, 1),
	('Model', 'N', 'Common', NULL, 2),
	('PN', 'N', 'Common', NULL, 3),
	('Purpose', 'N', 'Common', NULL, 4),
	('Asset Name', 'N', 'Common', NULL, 5),
	('Asset ID', 'N', 'Common', NULL, 6),
	('Company ID', 'N', 'Common', NULL, 7),
	('Company ID', 'N', 'Common', NULL, 8),
	('Custodian Dept', 'N', 'Common', NULL, 9),
	('Custodian EID', 'N', 'Common', NULL, 10),
	('Custodian', 'N', 'Common', NULL, 11),
	('Location', 'N', 'Common', NULL, 12),
	('Manufacturer', 'N', 'Common', NULL, 13),
	('Stock Date', 'N', 'Common', NULL, 14);