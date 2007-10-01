--
-- This script should be executed to upgrade database from version 0.14.5 to 0.14.6.
--

INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (13,25,'FreeBSD 1.x');
INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (13,26,'FreeBSD 2.x');
INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (13,27,'FreeBSD 3.x');
INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (13,28,'FreeBSD 4.x');
INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (13,29,'FreeBSD 5.x');
INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (13,30,'FreeBSD 6.x');
INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (13,31,'RHFC8');
INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (13,32,'ALTLinux Master 4.0');
INSERT INTO `PortCompat` (`type1`, `type2`) VALUES (20,20);
INSERT INTO `PortCompat` (`type1`, `type2`) VALUES (21,21);
INSERT INTO `PortCompat` (`type1`, `type2`) VALUES (22,22);
INSERT INTO `PortCompat` (`type1`, `type2`) VALUES (23,23);
INSERT INTO `PortCompat` (`type1`, `type2`) VALUES (24,24);
INSERT INTO `PortCompat` (`type1`, `type2`) VALUES (25,25);
INSERT INTO `PortCompat` (`type1`, `type2`) VALUES (26,26);
INSERT INTO `PortCompat` (`type1`, `type2`) VALUES (27,27);
INSERT INTO `PortCompat` (`type1`, `type2`) VALUES (28,28);
INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (2,20,'KVM');
INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (2,21,'1000Base-ZX');
INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (2,22,'10GBase-ER');
INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (2,23,'10GBase-LR');
INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (2,24,'10GBase-LRM');
INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (2,25,'10GBase-ZR');
INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (2,26,'10GBase-LX4');
INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (2,27,'10GBase-CX4');
INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (2,28,'10GBase-Kx');
INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (12,114,'Cisco Catalyst 2970G-24T');
INSERT INTO `Dictionary` (`chapter_no`, `dict_key`, `dict_value`) VALUES (12,115,'Cisco Catalyst 2970G-24TS');
