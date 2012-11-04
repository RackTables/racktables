set names 'utf8';

INSERT INTO `Molecule` VALUES (1);

INSERT INTO `Object` (id, name, label, objtype_id, asset_no, has_problems, comment) VALUES
(905,'london router','bbrtr1',7,'net247','no',NULL),
(906,'londonswitch1','',8,NULL,'no',NULL),
(907,'New-York router 1','bbrtr2a',7,'net55','no',NULL),
(908,'moscow router','bbrtr3',7,NULL,'no',NULL),
(909,'tokyo router','bbrtr4',7,NULL,'no',NULL),
(910,'London server 1','lserver01',4,'srv500','no',NULL),
(911,'London server 2','lserver02',4,'srv501','no',NULL),
(912,'London server 3','lserver03',4,'srv502','no',NULL),
(913,'London server 4','lserver04',4,'srv503','yes','this one needs replacement'),
(914,'London server 5','lserver05',4,'srv504','no',NULL),
(915,'london LB','llb',8,'net1000','no',NULL),
(916,'shared storage','',5,NULL,'no',NULL),
(917,'london-NAS','',7,'net1001','no',NULL),
(918,'London server 6','lserver06',4,'srv505','no',NULL),
(919,'London server 7','lserver07',4,'srv506','no',NULL),
(920,'backup library','lbackup',6,'misc200','no',NULL),
(921,'lserver06 array','lserver06 array',5,NULL,'no',NULL),
(922,'lserver07 array','lserver07 array',5,NULL,'no',NULL),
(923,'Tokyo server 1','tserver01',4,'srv654','no',NULL),
(924,'Tokyo server 2','tserver02',4,'srv848','no',NULL),
(925,'Tokyo server 3','tserver03',4,'srv139','no',NULL),
(926,'Tokyo switch','tswitch',8,'net385','no',NULL),
(927,'New-York router 2','bbrtr2b',7,'net498','no',NULL),
(928,'New-York IPVS LB A','nylba',4,'net554','no',NULL),
(929,'New-York IPVS LB B','nylbb',4,'net555','no',NULL),
(930,'New-York server switch A','nyswitcha',8,'net084','no',NULL),
(931,'New-York server switch B','nyswitchb',8,'net486','no',NULL),
(932,'New-York server 1A','nysrv1a',4,'srv287','no',NULL),
(933,'New-York server 1B','nysrv1b',4,'srv288','no',NULL),
(934,'New-York server 2A','nysrv2a',4,NULL,'no',NULL),
(935,'New-York server 2B','nysrv2b',4,NULL,'no',NULL),
(936,'New-York server 3A','nysrv3a',4,NULL,'no',NULL),
(937,'New-York server 3B','nysrv3b',4,NULL,'no',NULL),
(938,'New-York server 4A','nysrv4a',4,NULL,'no',NULL),
(939,'New-York server 4B','nysrv4b',4,NULL,'no',NULL),
(940,'New-York server 5A','nysrv5a',4,NULL,'no',NULL),
(941,'New-York server 5B','nysrv5b',4,NULL,'no',NULL),
(942,'wing A UPS',NULL,12,NULL,'no',NULL),
(943,'wing B UPS',NULL,12,NULL,'no',NULL),
(944,'network UPS',NULL,12,NULL,'no',NULL),
(945,NULL,NULL,9,NULL,'no',NULL),
(946,NULL,NULL,9,NULL,'no',NULL),
(947,NULL,NULL,2,NULL,'no',NULL),
(948,NULL,NULL,2,NULL,'no',NULL),
(949,NULL,NULL,2,NULL,'no',NULL),
(950,NULL,NULL,2,NULL,'no',NULL),
(951,NULL,NULL,2,NULL,'no',NULL),
(952,NULL,NULL,2,NULL,'no',NULL),
(953,NULL,NULL,2,NULL,'no',NULL),
(954,NULL,NULL,2,NULL,'no',NULL),
(955,NULL,NULL,2,NULL,'no',NULL),
(956,'mps1',NULL,4,NULL,'no',NULL),
(957,'mps2',NULL,4,NULL,'no',NULL),
(958,'mps3',NULL,4,NULL,'no',NULL),
(959,'mps4',NULL,4,NULL,'no',NULL),
(960,'mps5',NULL,4,NULL,'no',NULL),
(961,'mskswitch',NULL,8,'sw0001','no',NULL),
(962,'moscow kvm switch',NULL,445,'sw0002','no',NULL),
(963,'Row 1',NULL,1561,NULL,'no',NULL),
(964,'Row A',NULL,1561,NULL,'no',NULL),
(965,'CF-4',NULL,1561,NULL,'no',NULL),
(966,'Row 1',NULL,1561,NULL,'no',NULL),
(967,'L01',NULL,1560,NULL,'no','test'),
(968,'L02',NULL,1560,NULL,'no','network equipment mini-rack'),
(969,'L03',NULL,1560,NULL,'no',NULL),
(970,'NY100',NULL,1560,NULL,'no',NULL),
(971,'NY101',NULL,1560,NULL,'no','server farm wing A'),
(972,'M01',NULL,1560,NULL,'no',NULL),
(973,'NY102',NULL,1560,NULL,'no','server farm wing B'),
(974,'T01',NULL,1560,NULL,'no',NULL),
(975,'Moscow',NULL,1562,NULL,'no',NULL),
(976,'Tokyo',NULL,1562,NULL,'no',NULL),
(977,'New-York',NULL,1562,NULL,'no',NULL),
(978,'London',NULL,1562,NULL,'no',NULL),
(979,'sw-a1',NULL,8,NULL,'no',NULL),
(980,'sw-a2',NULL,8,NULL,'no',NULL),
(981,'sw-d1',NULL,8,NULL,'no',NULL);

INSERT INTO `Atom` VALUES (1,967,35,'front');
INSERT INTO `Atom` VALUES (1,967,35,'interior');

INSERT INTO `EntityLink` (`parent_entity_type`, `parent_entity_id`, `child_entity_type`, `child_entity_id`) VALUES
('row',966,'rack',967),
('row',966,'rack',968),
('row',966,'rack',969),
('row',965,'rack',970),
('row',965,'rack',971),
('row',963,'rack',972),
('row',965,'rack',973),
('row',964,'rack',974),
('location',975,'row',963),
('location',976,'row',964),
('location',977,'row',965),
('location',978,'row',966),
('rack',968,'object',979),
('rack',969,'object',980);

INSERT INTO `AttributeValue` VALUES (905,7,2,NULL,269,NULL);
INSERT INTO `AttributeValue` VALUES (906,8,2,NULL,165,NULL);
INSERT INTO `AttributeValue` VALUES (915,8,2,NULL,118,NULL);
INSERT INTO `AttributeValue` VALUES (915,8,4,NULL,245,NULL);
INSERT INTO `AttributeValue` VALUES (917,7,2,NULL,267,NULL);
INSERT INTO `AttributeValue` VALUES (916,5,2,NULL,320,NULL);
INSERT INTO `AttributeValue` VALUES (910,4,2,NULL,57,NULL);
INSERT INTO `AttributeValue` VALUES (910,4,4,NULL,220,NULL);
INSERT INTO `AttributeValue` VALUES (911,4,4,NULL,220,NULL);
INSERT INTO `AttributeValue` VALUES (911,4,2,NULL,57,NULL);
INSERT INTO `AttributeValue` VALUES (912,4,4,NULL,220,NULL);
INSERT INTO `AttributeValue` VALUES (912,4,2,NULL,57,NULL);
INSERT INTO `AttributeValue` VALUES (913,4,2,NULL,57,NULL);
INSERT INTO `AttributeValue` VALUES (913,4,4,NULL,220,NULL);
INSERT INTO `AttributeValue` VALUES (914,4,2,NULL,57,NULL);
INSERT INTO `AttributeValue` VALUES (914,4,4,NULL,220,NULL);
INSERT INTO `AttributeValue` VALUES (918,4,2,NULL,88,NULL);
INSERT INTO `AttributeValue` VALUES (918,4,4,NULL,220,NULL);
INSERT INTO `AttributeValue` VALUES (919,4,2,NULL,88,NULL);
INSERT INTO `AttributeValue` VALUES (919,4,4,NULL,220,NULL);
INSERT INTO `AttributeValue` VALUES (920,6,2,NULL,326,NULL);
INSERT INTO `AttributeValue` VALUES (921,5,2,NULL,318,NULL);
INSERT INTO `AttributeValue` VALUES (922,5,2,NULL,318,NULL);
INSERT INTO `AttributeValue` VALUES (923,4,2,NULL,102,NULL);
INSERT INTO `AttributeValue` VALUES (923,4,4,NULL,233,NULL);
INSERT INTO `AttributeValue` VALUES (926,8,2,NULL,126,NULL);
INSERT INTO `AttributeValue` VALUES (926,8,4,NULL,244,NULL);
INSERT INTO `AttributeValue` VALUES (925,4,2,NULL,102,NULL);
INSERT INTO `AttributeValue` VALUES (925,4,4,NULL,233,NULL);
INSERT INTO `AttributeValue` VALUES (924,4,2,NULL,102,NULL);
INSERT INTO `AttributeValue` VALUES (924,4,4,NULL,233,NULL);
INSERT INTO `AttributeValue` VALUES (907,7,2,NULL,269,NULL);
INSERT INTO `AttributeValue` VALUES (907,7,4,NULL,258,NULL);
INSERT INTO `AttributeValue` VALUES (927,7,2,NULL,269,NULL);
INSERT INTO `AttributeValue` VALUES (927,7,4,NULL,258,NULL);
INSERT INTO `AttributeValue` VALUES (956,4,2,NULL,62,NULL);
INSERT INTO `AttributeValue` VALUES (956,4,4,NULL,791,NULL);
INSERT INTO `AttributeValue` VALUES (957,4,2,NULL,62,NULL);
INSERT INTO `AttributeValue` VALUES (957,4,4,NULL,791,NULL);
INSERT INTO `AttributeValue` VALUES (958,4,2,NULL,62,NULL);
INSERT INTO `AttributeValue` VALUES (958,4,4,NULL,791,NULL);
INSERT INTO `AttributeValue` VALUES (959,4,2,NULL,62,NULL);
INSERT INTO `AttributeValue` VALUES (959,4,4,NULL,791,NULL);
INSERT INTO `AttributeValue` VALUES (960,4,2,NULL,62,NULL);
INSERT INTO `AttributeValue` VALUES (960,4,4,NULL,791,NULL);
INSERT INTO `AttributeValue` VALUES (961,8,2,NULL,755,NULL);
INSERT INTO `AttributeValue` VALUES (962,445,2,NULL,470,NULL);
INSERT INTO `AttributeValue` VALUES (967,1560,27,NULL,42,NULL);
INSERT INTO `AttributeValue` VALUES (967,1560,29,NULL,1,NULL);
INSERT INTO `AttributeValue` VALUES (968,1560,27,NULL,12,NULL);
INSERT INTO `AttributeValue` VALUES (968,1560,29,NULL,2,NULL);
INSERT INTO `AttributeValue` VALUES (969,1560,27,NULL,42,NULL);
INSERT INTO `AttributeValue` VALUES (969,1560,29,NULL,3,NULL);
INSERT INTO `AttributeValue` VALUES (970,1560,27,NULL,16,NULL);
INSERT INTO `AttributeValue` VALUES (970,1560,29,NULL,1,NULL);
INSERT INTO `AttributeValue` VALUES (971,1560,27,NULL,42,NULL);
INSERT INTO `AttributeValue` VALUES (971,1560,29,NULL,2,NULL);
INSERT INTO `AttributeValue` VALUES (972,1560,27,NULL,42,NULL);
INSERT INTO `AttributeValue` VALUES (972,1560,29,NULL,1,NULL);
INSERT INTO `AttributeValue` VALUES (973,1560,27,NULL,42,NULL);
INSERT INTO `AttributeValue` VALUES (973,1560,29,NULL,3,NULL);
INSERT INTO `AttributeValue` VALUES (974,1560,27,NULL,16,NULL);
INSERT INTO `AttributeValue` VALUES (974,1560,29,NULL,1,NULL);
INSERT INTO `AttributeValue` VALUES (979,8,2,NULL,167,NULL);
INSERT INTO `AttributeValue` VALUES (979,8,4,NULL,252,NULL);
INSERT INTO `AttributeValue` VALUES (980,8,2,NULL,1337,NULL);
INSERT INTO `AttributeValue` VALUES (980,8,4,NULL,1369,NULL);
INSERT INTO `AttributeValue` VALUES (981,8,2,NULL,188,NULL);
INSERT INTO `AttributeValue` VALUES (981,8,4,NULL,252,NULL);

INSERT INTO `CachedPVM` VALUES (979,'gi0/1','trunk');
INSERT INTO `CachedPVM` VALUES (979,'gi0/10','access');
INSERT INTO `CachedPVM` VALUES (979,'gi0/11','access');
INSERT INTO `CachedPVM` VALUES (979,'gi0/12','access');
INSERT INTO `CachedPVM` VALUES (979,'gi0/13','access');
INSERT INTO `CachedPVM` VALUES (979,'gi0/14','access');
INSERT INTO `CachedPVM` VALUES (979,'gi0/15','access');
INSERT INTO `CachedPVM` VALUES (979,'gi0/16','access');
INSERT INTO `CachedPVM` VALUES (979,'gi0/17','access');
INSERT INTO `CachedPVM` VALUES (979,'gi0/18','access');
INSERT INTO `CachedPVM` VALUES (979,'gi0/19','access');
INSERT INTO `CachedPVM` VALUES (979,'gi0/2','trunk');
INSERT INTO `CachedPVM` VALUES (979,'gi0/20','access');
INSERT INTO `CachedPVM` VALUES (979,'gi0/21','access');
INSERT INTO `CachedPVM` VALUES (979,'gi0/22','access');
INSERT INTO `CachedPVM` VALUES (979,'gi0/23','access');
INSERT INTO `CachedPVM` VALUES (979,'gi0/24','trunk');
INSERT INTO `CachedPVM` VALUES (979,'gi0/3','access');
INSERT INTO `CachedPVM` VALUES (979,'gi0/4','access');
INSERT INTO `CachedPVM` VALUES (979,'gi0/5','access');
INSERT INTO `CachedPVM` VALUES (979,'gi0/6','access');
INSERT INTO `CachedPVM` VALUES (979,'gi0/7','access');
INSERT INTO `CachedPVM` VALUES (979,'gi0/8','access');
INSERT INTO `CachedPVM` VALUES (979,'gi0/9','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/0/1','trunk');
INSERT INTO `CachedPVM` VALUES (980,'gi0/0/10','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/0/11','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/0/12','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/0/13','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/0/14','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/0/15','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/0/16','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/0/17','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/0/18','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/0/19','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/0/2','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/0/20','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/0/21','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/0/22','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/0/23','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/0/24','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/0/25','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/0/26','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/0/27','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/0/28','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/0/29','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/0/3','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/0/30','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/0/31','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/0/32','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/0/33','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/0/34','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/0/35','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/0/36','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/0/37','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/0/38','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/0/39','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/0/4','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/0/40','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/0/41','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/0/42','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/0/43','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/0/44','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/0/45','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/0/46','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/0/47','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/0/48','trunk');
INSERT INTO `CachedPVM` VALUES (980,'gi0/0/5','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/0/6','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/0/7','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/0/8','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/0/9','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/1','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/10','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/11','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/12','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/13','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/14','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/15','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/16','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/17','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/18','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/19','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/2','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/20','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/21','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/22','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/23','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/24','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/25','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/26','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/27','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/28','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/29','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/3','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/30','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/31','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/32','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/33','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/34','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/35','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/36','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/37','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/38','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/39','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/4','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/40','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/41','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/42','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/43','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/44','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/45','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/46','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/47','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/48','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/5','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/6','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/7','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/8','access');
INSERT INTO `CachedPVM` VALUES (980,'gi0/9','access');
INSERT INTO `CachedPVM` VALUES (981,'gi0/1','trunk');
INSERT INTO `CachedPVM` VALUES (981,'gi0/10','trunk');
INSERT INTO `CachedPVM` VALUES (981,'gi0/11','trunk');
INSERT INTO `CachedPVM` VALUES (981,'gi0/12','trunk');
INSERT INTO `CachedPVM` VALUES (981,'gi0/2','trunk');
INSERT INTO `CachedPVM` VALUES (981,'gi0/3','trunk');
INSERT INTO `CachedPVM` VALUES (981,'gi0/4','trunk');
INSERT INTO `CachedPVM` VALUES (981,'gi0/5','trunk');
INSERT INTO `CachedPVM` VALUES (981,'gi0/6','trunk');
INSERT INTO `CachedPVM` VALUES (981,'gi0/7','trunk');
INSERT INTO `CachedPVM` VALUES (981,'gi0/8','trunk');
INSERT INTO `CachedPVM` VALUES (981,'gi0/9','trunk');

INSERT INTO `CachedPAV` VALUES (979,'gi0/1',7);
INSERT INTO `CachedPAV` VALUES (979,'gi0/1',8);
INSERT INTO `CachedPAV` VALUES (979,'gi0/1',9);
INSERT INTO `CachedPAV` VALUES (979,'gi0/10',9);
INSERT INTO `CachedPAV` VALUES (979,'gi0/11',9);
INSERT INTO `CachedPAV` VALUES (979,'gi0/12',9);
INSERT INTO `CachedPAV` VALUES (979,'gi0/13',3);
INSERT INTO `CachedPAV` VALUES (979,'gi0/14',9);
INSERT INTO `CachedPAV` VALUES (979,'gi0/15',9);
INSERT INTO `CachedPAV` VALUES (979,'gi0/16',9);
INSERT INTO `CachedPAV` VALUES (979,'gi0/17',9);
INSERT INTO `CachedPAV` VALUES (979,'gi0/18',9);
INSERT INTO `CachedPAV` VALUES (979,'gi0/19',9);
INSERT INTO `CachedPAV` VALUES (979,'gi0/2',7);
INSERT INTO `CachedPAV` VALUES (979,'gi0/2',8);
INSERT INTO `CachedPAV` VALUES (979,'gi0/2',9);
INSERT INTO `CachedPAV` VALUES (979,'gi0/20',9);
INSERT INTO `CachedPAV` VALUES (979,'gi0/21',9);
INSERT INTO `CachedPAV` VALUES (979,'gi0/22',9);
INSERT INTO `CachedPAV` VALUES (979,'gi0/23',9);
INSERT INTO `CachedPAV` VALUES (979,'gi0/24',3);
INSERT INTO `CachedPAV` VALUES (979,'gi0/24',7);
INSERT INTO `CachedPAV` VALUES (979,'gi0/24',8);
INSERT INTO `CachedPAV` VALUES (979,'gi0/24',9);
INSERT INTO `CachedPAV` VALUES (979,'gi0/3',9);
INSERT INTO `CachedPAV` VALUES (979,'gi0/4',9);
INSERT INTO `CachedPAV` VALUES (979,'gi0/5',9);
INSERT INTO `CachedPAV` VALUES (979,'gi0/6',9);
INSERT INTO `CachedPAV` VALUES (979,'gi0/7',9);
INSERT INTO `CachedPAV` VALUES (979,'gi0/8',9);
INSERT INTO `CachedPAV` VALUES (979,'gi0/9',9);
INSERT INTO `CachedPAV` VALUES (980,'gi0/0/1',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/0/1',11);
INSERT INTO `CachedPAV` VALUES (980,'gi0/0/1',12);
INSERT INTO `CachedPAV` VALUES (980,'gi0/0/1',13);
INSERT INTO `CachedPAV` VALUES (980,'gi0/0/10',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/0/11',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/0/12',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/0/13',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/0/14',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/0/15',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/0/16',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/0/17',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/0/18',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/0/19',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/0/2',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/0/20',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/0/21',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/0/22',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/0/23',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/0/24',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/0/25',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/0/26',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/0/27',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/0/28',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/0/29',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/0/3',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/0/30',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/0/31',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/0/32',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/0/33',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/0/34',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/0/35',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/0/36',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/0/37',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/0/38',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/0/39',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/0/4',3);
INSERT INTO `CachedPAV` VALUES (980,'gi0/0/40',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/0/41',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/0/42',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/0/43',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/0/44',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/0/45',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/0/46',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/0/47',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/0/48',3);
INSERT INTO `CachedPAV` VALUES (980,'gi0/0/48',5);
INSERT INTO `CachedPAV` VALUES (980,'gi0/0/48',11);
INSERT INTO `CachedPAV` VALUES (980,'gi0/0/48',12);
INSERT INTO `CachedPAV` VALUES (980,'gi0/0/48',13);
INSERT INTO `CachedPAV` VALUES (980,'gi0/0/5',5);
INSERT INTO `CachedPAV` VALUES (980,'gi0/0/6',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/0/7',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/0/8',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/0/9',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/1',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/10',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/11',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/12',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/13',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/14',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/15',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/16',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/17',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/18',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/19',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/2',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/20',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/21',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/22',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/23',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/24',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/25',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/26',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/27',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/28',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/29',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/3',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/30',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/31',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/32',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/33',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/34',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/35',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/36',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/37',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/38',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/39',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/4',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/40',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/41',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/42',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/43',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/44',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/45',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/46',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/47',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/48',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/5',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/6',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/7',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/8',1);
INSERT INTO `CachedPAV` VALUES (980,'gi0/9',1);
INSERT INTO `CachedPAV` VALUES (981,'gi0/1',3);
INSERT INTO `CachedPAV` VALUES (981,'gi0/1',7);
INSERT INTO `CachedPAV` VALUES (981,'gi0/1',8);
INSERT INTO `CachedPAV` VALUES (981,'gi0/1',9);
INSERT INTO `CachedPAV` VALUES (981,'gi0/10',1);
INSERT INTO `CachedPAV` VALUES (981,'gi0/11',1);
INSERT INTO `CachedPAV` VALUES (981,'gi0/12',1);
INSERT INTO `CachedPAV` VALUES (981,'gi0/2',1);
INSERT INTO `CachedPAV` VALUES (981,'gi0/3',3);
INSERT INTO `CachedPAV` VALUES (981,'gi0/3',5);
INSERT INTO `CachedPAV` VALUES (981,'gi0/3',11);
INSERT INTO `CachedPAV` VALUES (981,'gi0/3',12);
INSERT INTO `CachedPAV` VALUES (981,'gi0/3',13);
INSERT INTO `CachedPAV` VALUES (981,'gi0/4',1);
INSERT INTO `CachedPAV` VALUES (981,'gi0/5',1);
INSERT INTO `CachedPAV` VALUES (981,'gi0/6',1);
INSERT INTO `CachedPAV` VALUES (981,'gi0/7',1);
INSERT INTO `CachedPAV` VALUES (981,'gi0/8',1);
INSERT INTO `CachedPAV` VALUES (981,'gi0/9',1);

INSERT INTO `CachedPNV` VALUES (979,'gi0/1',9);
INSERT INTO `CachedPNV` VALUES (979,'gi0/10',9);
INSERT INTO `CachedPNV` VALUES (979,'gi0/11',9);
INSERT INTO `CachedPNV` VALUES (979,'gi0/12',9);
INSERT INTO `CachedPNV` VALUES (979,'gi0/13',3);
INSERT INTO `CachedPNV` VALUES (979,'gi0/14',9);
INSERT INTO `CachedPNV` VALUES (979,'gi0/15',9);
INSERT INTO `CachedPNV` VALUES (979,'gi0/16',9);
INSERT INTO `CachedPNV` VALUES (979,'gi0/17',9);
INSERT INTO `CachedPNV` VALUES (979,'gi0/18',9);
INSERT INTO `CachedPNV` VALUES (979,'gi0/19',9);
INSERT INTO `CachedPNV` VALUES (979,'gi0/2',9);
INSERT INTO `CachedPNV` VALUES (979,'gi0/20',9);
INSERT INTO `CachedPNV` VALUES (979,'gi0/21',9);
INSERT INTO `CachedPNV` VALUES (979,'gi0/22',9);
INSERT INTO `CachedPNV` VALUES (979,'gi0/23',9);
INSERT INTO `CachedPNV` VALUES (979,'gi0/3',9);
INSERT INTO `CachedPNV` VALUES (979,'gi0/4',9);
INSERT INTO `CachedPNV` VALUES (979,'gi0/5',9);
INSERT INTO `CachedPNV` VALUES (979,'gi0/6',9);
INSERT INTO `CachedPNV` VALUES (979,'gi0/7',9);
INSERT INTO `CachedPNV` VALUES (979,'gi0/8',9);
INSERT INTO `CachedPNV` VALUES (979,'gi0/9',9);
INSERT INTO `CachedPNV` VALUES (980,'gi0/0/1',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/0/10',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/0/11',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/0/12',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/0/13',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/0/14',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/0/15',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/0/16',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/0/17',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/0/18',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/0/19',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/0/2',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/0/20',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/0/21',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/0/22',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/0/23',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/0/24',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/0/25',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/0/26',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/0/27',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/0/28',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/0/29',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/0/3',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/0/30',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/0/31',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/0/32',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/0/33',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/0/34',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/0/35',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/0/36',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/0/37',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/0/38',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/0/39',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/0/4',3);
INSERT INTO `CachedPNV` VALUES (980,'gi0/0/40',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/0/41',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/0/42',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/0/43',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/0/44',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/0/45',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/0/46',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/0/47',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/0/5',5);
INSERT INTO `CachedPNV` VALUES (980,'gi0/0/6',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/0/7',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/0/8',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/0/9',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/1',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/10',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/11',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/12',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/13',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/14',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/15',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/16',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/17',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/18',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/19',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/2',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/20',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/21',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/22',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/23',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/24',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/25',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/26',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/27',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/28',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/29',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/3',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/30',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/31',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/32',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/33',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/34',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/35',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/36',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/37',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/38',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/39',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/4',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/40',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/41',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/42',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/43',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/44',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/45',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/46',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/47',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/48',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/5',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/6',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/7',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/8',1);
INSERT INTO `CachedPNV` VALUES (980,'gi0/9',1);
INSERT INTO `CachedPNV` VALUES (981,'gi0/10',1);
INSERT INTO `CachedPNV` VALUES (981,'gi0/11',1);
INSERT INTO `CachedPNV` VALUES (981,'gi0/12',1);
INSERT INTO `CachedPNV` VALUES (981,'gi0/2',1);
INSERT INTO `CachedPNV` VALUES (981,'gi0/4',1);
INSERT INTO `CachedPNV` VALUES (981,'gi0/5',1);
INSERT INTO `CachedPNV` VALUES (981,'gi0/6',1);
INSERT INTO `CachedPNV` VALUES (981,'gi0/7',1);
INSERT INTO `CachedPNV` VALUES (981,'gi0/8',1);
INSERT INTO `CachedPNV` VALUES (981,'gi0/9',1);

INSERT INTO `PortVLANMode` VALUES (979,'gi0/1','trunk');
INSERT INTO `PortVLANMode` VALUES (979,'gi0/10','access');
INSERT INTO `PortVLANMode` VALUES (979,'gi0/11','access');
INSERT INTO `PortVLANMode` VALUES (979,'gi0/12','access');
INSERT INTO `PortVLANMode` VALUES (979,'gi0/13','access');
INSERT INTO `PortVLANMode` VALUES (979,'gi0/14','access');
INSERT INTO `PortVLANMode` VALUES (979,'gi0/15','access');
INSERT INTO `PortVLANMode` VALUES (979,'gi0/16','access');
INSERT INTO `PortVLANMode` VALUES (979,'gi0/17','access');
INSERT INTO `PortVLANMode` VALUES (979,'gi0/18','access');
INSERT INTO `PortVLANMode` VALUES (979,'gi0/19','access');
INSERT INTO `PortVLANMode` VALUES (979,'gi0/2','trunk');
INSERT INTO `PortVLANMode` VALUES (979,'gi0/20','access');
INSERT INTO `PortVLANMode` VALUES (979,'gi0/21','access');
INSERT INTO `PortVLANMode` VALUES (979,'gi0/22','access');
INSERT INTO `PortVLANMode` VALUES (979,'gi0/23','access');
INSERT INTO `PortVLANMode` VALUES (979,'gi0/24','trunk');
INSERT INTO `PortVLANMode` VALUES (979,'gi0/3','access');
INSERT INTO `PortVLANMode` VALUES (979,'gi0/4','access');
INSERT INTO `PortVLANMode` VALUES (979,'gi0/5','access');
INSERT INTO `PortVLANMode` VALUES (979,'gi0/6','access');
INSERT INTO `PortVLANMode` VALUES (979,'gi0/7','access');
INSERT INTO `PortVLANMode` VALUES (979,'gi0/8','access');
INSERT INTO `PortVLANMode` VALUES (979,'gi0/9','access');
INSERT INTO `PortVLANMode` VALUES (980,'gi0/0/1','trunk');
INSERT INTO `PortVLANMode` VALUES (980,'gi0/0/10','access');
INSERT INTO `PortVLANMode` VALUES (980,'gi0/0/11','access');
INSERT INTO `PortVLANMode` VALUES (980,'gi0/0/12','access');
INSERT INTO `PortVLANMode` VALUES (980,'gi0/0/13','access');
INSERT INTO `PortVLANMode` VALUES (980,'gi0/0/14','access');
INSERT INTO `PortVLANMode` VALUES (980,'gi0/0/15','access');
INSERT INTO `PortVLANMode` VALUES (980,'gi0/0/16','access');
INSERT INTO `PortVLANMode` VALUES (980,'gi0/0/17','access');
INSERT INTO `PortVLANMode` VALUES (980,'gi0/0/18','access');
INSERT INTO `PortVLANMode` VALUES (980,'gi0/0/19','access');
INSERT INTO `PortVLANMode` VALUES (980,'gi0/0/2','access');
INSERT INTO `PortVLANMode` VALUES (980,'gi0/0/20','access');
INSERT INTO `PortVLANMode` VALUES (980,'gi0/0/21','access');
INSERT INTO `PortVLANMode` VALUES (980,'gi0/0/22','access');
INSERT INTO `PortVLANMode` VALUES (980,'gi0/0/23','access');
INSERT INTO `PortVLANMode` VALUES (980,'gi0/0/24','access');
INSERT INTO `PortVLANMode` VALUES (980,'gi0/0/25','access');
INSERT INTO `PortVLANMode` VALUES (980,'gi0/0/26','access');
INSERT INTO `PortVLANMode` VALUES (980,'gi0/0/27','access');
INSERT INTO `PortVLANMode` VALUES (980,'gi0/0/28','access');
INSERT INTO `PortVLANMode` VALUES (980,'gi0/0/29','access');
INSERT INTO `PortVLANMode` VALUES (980,'gi0/0/3','access');
INSERT INTO `PortVLANMode` VALUES (980,'gi0/0/30','access');
INSERT INTO `PortVLANMode` VALUES (980,'gi0/0/31','access');
INSERT INTO `PortVLANMode` VALUES (980,'gi0/0/32','access');
INSERT INTO `PortVLANMode` VALUES (980,'gi0/0/33','access');
INSERT INTO `PortVLANMode` VALUES (980,'gi0/0/34','access');
INSERT INTO `PortVLANMode` VALUES (980,'gi0/0/35','access');
INSERT INTO `PortVLANMode` VALUES (980,'gi0/0/36','access');
INSERT INTO `PortVLANMode` VALUES (980,'gi0/0/37','access');
INSERT INTO `PortVLANMode` VALUES (980,'gi0/0/38','access');
INSERT INTO `PortVLANMode` VALUES (980,'gi0/0/39','access');
INSERT INTO `PortVLANMode` VALUES (980,'gi0/0/4','access');
INSERT INTO `PortVLANMode` VALUES (980,'gi0/0/40','access');
INSERT INTO `PortVLANMode` VALUES (980,'gi0/0/41','access');
INSERT INTO `PortVLANMode` VALUES (980,'gi0/0/42','access');
INSERT INTO `PortVLANMode` VALUES (980,'gi0/0/43','access');
INSERT INTO `PortVLANMode` VALUES (980,'gi0/0/44','access');
INSERT INTO `PortVLANMode` VALUES (980,'gi0/0/45','access');
INSERT INTO `PortVLANMode` VALUES (980,'gi0/0/46','access');
INSERT INTO `PortVLANMode` VALUES (980,'gi0/0/47','access');
INSERT INTO `PortVLANMode` VALUES (980,'gi0/0/48','trunk');
INSERT INTO `PortVLANMode` VALUES (980,'gi0/0/5','access');
INSERT INTO `PortVLANMode` VALUES (980,'gi0/0/6','access');
INSERT INTO `PortVLANMode` VALUES (980,'gi0/0/7','access');
INSERT INTO `PortVLANMode` VALUES (980,'gi0/0/8','access');
INSERT INTO `PortVLANMode` VALUES (980,'gi0/0/9','access');
INSERT INTO `PortVLANMode` VALUES (981,'gi0/1','trunk');
INSERT INTO `PortVLANMode` VALUES (981,'gi0/10','trunk');
INSERT INTO `PortVLANMode` VALUES (981,'gi0/11','trunk');
INSERT INTO `PortVLANMode` VALUES (981,'gi0/12','trunk');
INSERT INTO `PortVLANMode` VALUES (981,'gi0/2','trunk');
INSERT INTO `PortVLANMode` VALUES (981,'gi0/3','trunk');
INSERT INTO `PortVLANMode` VALUES (981,'gi0/4','trunk');
INSERT INTO `PortVLANMode` VALUES (981,'gi0/5','trunk');
INSERT INTO `PortVLANMode` VALUES (981,'gi0/6','trunk');
INSERT INTO `PortVLANMode` VALUES (981,'gi0/7','trunk');
INSERT INTO `PortVLANMode` VALUES (981,'gi0/8','trunk');
INSERT INTO `PortVLANMode` VALUES (981,'gi0/9','trunk');

INSERT INTO `PortAllowedVLAN` VALUES (979,'gi0/1',7);
INSERT INTO `PortAllowedVLAN` VALUES (979,'gi0/1',8);
INSERT INTO `PortAllowedVLAN` VALUES (979,'gi0/1',9);
INSERT INTO `PortAllowedVLAN` VALUES (979,'gi0/10',9);
INSERT INTO `PortAllowedVLAN` VALUES (979,'gi0/11',9);
INSERT INTO `PortAllowedVLAN` VALUES (979,'gi0/12',9);
INSERT INTO `PortAllowedVLAN` VALUES (979,'gi0/13',3);
INSERT INTO `PortAllowedVLAN` VALUES (979,'gi0/14',9);
INSERT INTO `PortAllowedVLAN` VALUES (979,'gi0/15',9);
INSERT INTO `PortAllowedVLAN` VALUES (979,'gi0/16',9);
INSERT INTO `PortAllowedVLAN` VALUES (979,'gi0/17',9);
INSERT INTO `PortAllowedVLAN` VALUES (979,'gi0/18',9);
INSERT INTO `PortAllowedVLAN` VALUES (979,'gi0/19',9);
INSERT INTO `PortAllowedVLAN` VALUES (979,'gi0/2',7);
INSERT INTO `PortAllowedVLAN` VALUES (979,'gi0/2',8);
INSERT INTO `PortAllowedVLAN` VALUES (979,'gi0/2',9);
INSERT INTO `PortAllowedVLAN` VALUES (979,'gi0/20',9);
INSERT INTO `PortAllowedVLAN` VALUES (979,'gi0/21',9);
INSERT INTO `PortAllowedVLAN` VALUES (979,'gi0/22',9);
INSERT INTO `PortAllowedVLAN` VALUES (979,'gi0/23',9);
INSERT INTO `PortAllowedVLAN` VALUES (979,'gi0/24',3);
INSERT INTO `PortAllowedVLAN` VALUES (979,'gi0/24',7);
INSERT INTO `PortAllowedVLAN` VALUES (979,'gi0/24',8);
INSERT INTO `PortAllowedVLAN` VALUES (979,'gi0/24',9);
INSERT INTO `PortAllowedVLAN` VALUES (979,'gi0/3',9);
INSERT INTO `PortAllowedVLAN` VALUES (979,'gi0/4',9);
INSERT INTO `PortAllowedVLAN` VALUES (979,'gi0/5',9);
INSERT INTO `PortAllowedVLAN` VALUES (979,'gi0/6',9);
INSERT INTO `PortAllowedVLAN` VALUES (979,'gi0/7',9);
INSERT INTO `PortAllowedVLAN` VALUES (979,'gi0/8',9);
INSERT INTO `PortAllowedVLAN` VALUES (979,'gi0/9',9);
INSERT INTO `PortAllowedVLAN` VALUES (980,'gi0/0/1',1);
INSERT INTO `PortAllowedVLAN` VALUES (980,'gi0/0/1',11);
INSERT INTO `PortAllowedVLAN` VALUES (980,'gi0/0/1',12);
INSERT INTO `PortAllowedVLAN` VALUES (980,'gi0/0/1',13);
INSERT INTO `PortAllowedVLAN` VALUES (980,'gi0/0/10',1);
INSERT INTO `PortAllowedVLAN` VALUES (980,'gi0/0/11',1);
INSERT INTO `PortAllowedVLAN` VALUES (980,'gi0/0/12',1);
INSERT INTO `PortAllowedVLAN` VALUES (980,'gi0/0/13',1);
INSERT INTO `PortAllowedVLAN` VALUES (980,'gi0/0/14',1);
INSERT INTO `PortAllowedVLAN` VALUES (980,'gi0/0/15',1);
INSERT INTO `PortAllowedVLAN` VALUES (980,'gi0/0/16',1);
INSERT INTO `PortAllowedVLAN` VALUES (980,'gi0/0/17',1);
INSERT INTO `PortAllowedVLAN` VALUES (980,'gi0/0/18',1);
INSERT INTO `PortAllowedVLAN` VALUES (980,'gi0/0/19',1);
INSERT INTO `PortAllowedVLAN` VALUES (980,'gi0/0/2',1);
INSERT INTO `PortAllowedVLAN` VALUES (980,'gi0/0/20',1);
INSERT INTO `PortAllowedVLAN` VALUES (980,'gi0/0/21',1);
INSERT INTO `PortAllowedVLAN` VALUES (980,'gi0/0/22',1);
INSERT INTO `PortAllowedVLAN` VALUES (980,'gi0/0/23',1);
INSERT INTO `PortAllowedVLAN` VALUES (980,'gi0/0/24',1);
INSERT INTO `PortAllowedVLAN` VALUES (980,'gi0/0/25',1);
INSERT INTO `PortAllowedVLAN` VALUES (980,'gi0/0/26',1);
INSERT INTO `PortAllowedVLAN` VALUES (980,'gi0/0/27',1);
INSERT INTO `PortAllowedVLAN` VALUES (980,'gi0/0/28',1);
INSERT INTO `PortAllowedVLAN` VALUES (980,'gi0/0/29',1);
INSERT INTO `PortAllowedVLAN` VALUES (980,'gi0/0/3',1);
INSERT INTO `PortAllowedVLAN` VALUES (980,'gi0/0/30',1);
INSERT INTO `PortAllowedVLAN` VALUES (980,'gi0/0/31',1);
INSERT INTO `PortAllowedVLAN` VALUES (980,'gi0/0/32',1);
INSERT INTO `PortAllowedVLAN` VALUES (980,'gi0/0/33',1);
INSERT INTO `PortAllowedVLAN` VALUES (980,'gi0/0/34',1);
INSERT INTO `PortAllowedVLAN` VALUES (980,'gi0/0/35',1);
INSERT INTO `PortAllowedVLAN` VALUES (980,'gi0/0/36',1);
INSERT INTO `PortAllowedVLAN` VALUES (980,'gi0/0/37',1);
INSERT INTO `PortAllowedVLAN` VALUES (980,'gi0/0/38',1);
INSERT INTO `PortAllowedVLAN` VALUES (980,'gi0/0/39',1);
INSERT INTO `PortAllowedVLAN` VALUES (980,'gi0/0/4',3);
INSERT INTO `PortAllowedVLAN` VALUES (980,'gi0/0/40',1);
INSERT INTO `PortAllowedVLAN` VALUES (980,'gi0/0/41',1);
INSERT INTO `PortAllowedVLAN` VALUES (980,'gi0/0/42',1);
INSERT INTO `PortAllowedVLAN` VALUES (980,'gi0/0/43',1);
INSERT INTO `PortAllowedVLAN` VALUES (980,'gi0/0/44',1);
INSERT INTO `PortAllowedVLAN` VALUES (980,'gi0/0/45',1);
INSERT INTO `PortAllowedVLAN` VALUES (980,'gi0/0/46',1);
INSERT INTO `PortAllowedVLAN` VALUES (980,'gi0/0/47',1);
INSERT INTO `PortAllowedVLAN` VALUES (980,'gi0/0/48',3);
INSERT INTO `PortAllowedVLAN` VALUES (980,'gi0/0/48',5);
INSERT INTO `PortAllowedVLAN` VALUES (980,'gi0/0/48',11);
INSERT INTO `PortAllowedVLAN` VALUES (980,'gi0/0/48',12);
INSERT INTO `PortAllowedVLAN` VALUES (980,'gi0/0/48',13);
INSERT INTO `PortAllowedVLAN` VALUES (980,'gi0/0/5',5);
INSERT INTO `PortAllowedVLAN` VALUES (980,'gi0/0/6',1);
INSERT INTO `PortAllowedVLAN` VALUES (980,'gi0/0/7',1);
INSERT INTO `PortAllowedVLAN` VALUES (980,'gi0/0/8',1);
INSERT INTO `PortAllowedVLAN` VALUES (980,'gi0/0/9',1);
INSERT INTO `PortAllowedVLAN` VALUES (981,'gi0/1',3);
INSERT INTO `PortAllowedVLAN` VALUES (981,'gi0/1',7);
INSERT INTO `PortAllowedVLAN` VALUES (981,'gi0/1',8);
INSERT INTO `PortAllowedVLAN` VALUES (981,'gi0/1',9);
INSERT INTO `PortAllowedVLAN` VALUES (981,'gi0/10',1);
INSERT INTO `PortAllowedVLAN` VALUES (981,'gi0/11',1);
INSERT INTO `PortAllowedVLAN` VALUES (981,'gi0/12',1);
INSERT INTO `PortAllowedVLAN` VALUES (981,'gi0/2',1);
INSERT INTO `PortAllowedVLAN` VALUES (981,'gi0/3',3);
INSERT INTO `PortAllowedVLAN` VALUES (981,'gi0/3',5);
INSERT INTO `PortAllowedVLAN` VALUES (981,'gi0/3',11);
INSERT INTO `PortAllowedVLAN` VALUES (981,'gi0/3',12);
INSERT INTO `PortAllowedVLAN` VALUES (981,'gi0/3',13);
INSERT INTO `PortAllowedVLAN` VALUES (981,'gi0/4',1);
INSERT INTO `PortAllowedVLAN` VALUES (981,'gi0/5',1);
INSERT INTO `PortAllowedVLAN` VALUES (981,'gi0/6',1);
INSERT INTO `PortAllowedVLAN` VALUES (981,'gi0/7',1);
INSERT INTO `PortAllowedVLAN` VALUES (981,'gi0/8',1);
INSERT INTO `PortAllowedVLAN` VALUES (981,'gi0/9',1);

INSERT INTO `PortNativeVLAN` VALUES (979,'gi0/1',9);
INSERT INTO `PortNativeVLAN` VALUES (979,'gi0/10',9);
INSERT INTO `PortNativeVLAN` VALUES (979,'gi0/11',9);
INSERT INTO `PortNativeVLAN` VALUES (979,'gi0/12',9);
INSERT INTO `PortNativeVLAN` VALUES (979,'gi0/13',3);
INSERT INTO `PortNativeVLAN` VALUES (979,'gi0/14',9);
INSERT INTO `PortNativeVLAN` VALUES (979,'gi0/15',9);
INSERT INTO `PortNativeVLAN` VALUES (979,'gi0/16',9);
INSERT INTO `PortNativeVLAN` VALUES (979,'gi0/17',9);
INSERT INTO `PortNativeVLAN` VALUES (979,'gi0/18',9);
INSERT INTO `PortNativeVLAN` VALUES (979,'gi0/19',9);
INSERT INTO `PortNativeVLAN` VALUES (979,'gi0/2',9);
INSERT INTO `PortNativeVLAN` VALUES (979,'gi0/20',9);
INSERT INTO `PortNativeVLAN` VALUES (979,'gi0/21',9);
INSERT INTO `PortNativeVLAN` VALUES (979,'gi0/22',9);
INSERT INTO `PortNativeVLAN` VALUES (979,'gi0/23',9);
INSERT INTO `PortNativeVLAN` VALUES (979,'gi0/3',9);
INSERT INTO `PortNativeVLAN` VALUES (979,'gi0/4',9);
INSERT INTO `PortNativeVLAN` VALUES (979,'gi0/5',9);
INSERT INTO `PortNativeVLAN` VALUES (979,'gi0/6',9);
INSERT INTO `PortNativeVLAN` VALUES (979,'gi0/7',9);
INSERT INTO `PortNativeVLAN` VALUES (979,'gi0/8',9);
INSERT INTO `PortNativeVLAN` VALUES (979,'gi0/9',9);
INSERT INTO `PortNativeVLAN` VALUES (980,'gi0/0/1',1);
INSERT INTO `PortNativeVLAN` VALUES (980,'gi0/0/10',1);
INSERT INTO `PortNativeVLAN` VALUES (980,'gi0/0/11',1);
INSERT INTO `PortNativeVLAN` VALUES (980,'gi0/0/12',1);
INSERT INTO `PortNativeVLAN` VALUES (980,'gi0/0/13',1);
INSERT INTO `PortNativeVLAN` VALUES (980,'gi0/0/14',1);
INSERT INTO `PortNativeVLAN` VALUES (980,'gi0/0/15',1);
INSERT INTO `PortNativeVLAN` VALUES (980,'gi0/0/16',1);
INSERT INTO `PortNativeVLAN` VALUES (980,'gi0/0/17',1);
INSERT INTO `PortNativeVLAN` VALUES (980,'gi0/0/18',1);
INSERT INTO `PortNativeVLAN` VALUES (980,'gi0/0/19',1);
INSERT INTO `PortNativeVLAN` VALUES (980,'gi0/0/2',1);
INSERT INTO `PortNativeVLAN` VALUES (980,'gi0/0/20',1);
INSERT INTO `PortNativeVLAN` VALUES (980,'gi0/0/21',1);
INSERT INTO `PortNativeVLAN` VALUES (980,'gi0/0/22',1);
INSERT INTO `PortNativeVLAN` VALUES (980,'gi0/0/23',1);
INSERT INTO `PortNativeVLAN` VALUES (980,'gi0/0/24',1);
INSERT INTO `PortNativeVLAN` VALUES (980,'gi0/0/25',1);
INSERT INTO `PortNativeVLAN` VALUES (980,'gi0/0/26',1);
INSERT INTO `PortNativeVLAN` VALUES (980,'gi0/0/27',1);
INSERT INTO `PortNativeVLAN` VALUES (980,'gi0/0/28',1);
INSERT INTO `PortNativeVLAN` VALUES (980,'gi0/0/29',1);
INSERT INTO `PortNativeVLAN` VALUES (980,'gi0/0/3',1);
INSERT INTO `PortNativeVLAN` VALUES (980,'gi0/0/30',1);
INSERT INTO `PortNativeVLAN` VALUES (980,'gi0/0/31',1);
INSERT INTO `PortNativeVLAN` VALUES (980,'gi0/0/32',1);
INSERT INTO `PortNativeVLAN` VALUES (980,'gi0/0/33',1);
INSERT INTO `PortNativeVLAN` VALUES (980,'gi0/0/34',1);
INSERT INTO `PortNativeVLAN` VALUES (980,'gi0/0/35',1);
INSERT INTO `PortNativeVLAN` VALUES (980,'gi0/0/36',1);
INSERT INTO `PortNativeVLAN` VALUES (980,'gi0/0/37',1);
INSERT INTO `PortNativeVLAN` VALUES (980,'gi0/0/38',1);
INSERT INTO `PortNativeVLAN` VALUES (980,'gi0/0/39',1);
INSERT INTO `PortNativeVLAN` VALUES (980,'gi0/0/4',3);
INSERT INTO `PortNativeVLAN` VALUES (980,'gi0/0/40',1);
INSERT INTO `PortNativeVLAN` VALUES (980,'gi0/0/41',1);
INSERT INTO `PortNativeVLAN` VALUES (980,'gi0/0/42',1);
INSERT INTO `PortNativeVLAN` VALUES (980,'gi0/0/43',1);
INSERT INTO `PortNativeVLAN` VALUES (980,'gi0/0/44',1);
INSERT INTO `PortNativeVLAN` VALUES (980,'gi0/0/45',1);
INSERT INTO `PortNativeVLAN` VALUES (980,'gi0/0/46',1);
INSERT INTO `PortNativeVLAN` VALUES (980,'gi0/0/47',1);
INSERT INTO `PortNativeVLAN` VALUES (980,'gi0/0/5',5);
INSERT INTO `PortNativeVLAN` VALUES (980,'gi0/0/6',1);
INSERT INTO `PortNativeVLAN` VALUES (980,'gi0/0/7',1);
INSERT INTO `PortNativeVLAN` VALUES (980,'gi0/0/8',1);
INSERT INTO `PortNativeVLAN` VALUES (980,'gi0/0/9',1);
INSERT INTO `PortNativeVLAN` VALUES (981,'gi0/10',1);
INSERT INTO `PortNativeVLAN` VALUES (981,'gi0/11',1);
INSERT INTO `PortNativeVLAN` VALUES (981,'gi0/12',1);
INSERT INTO `PortNativeVLAN` VALUES (981,'gi0/2',1);
INSERT INTO `PortNativeVLAN` VALUES (981,'gi0/4',1);
INSERT INTO `PortNativeVLAN` VALUES (981,'gi0/5',1);
INSERT INTO `PortNativeVLAN` VALUES (981,'gi0/6',1);
INSERT INTO `PortNativeVLAN` VALUES (981,'gi0/7',1);
INSERT INTO `PortNativeVLAN` VALUES (981,'gi0/8',1);
INSERT INTO `PortNativeVLAN` VALUES (981,'gi0/9',1);

INSERT INTO `IPv4Address` VALUES (180879678,'default gw','','no');
INSERT INTO `IPv4Address` VALUES (180879617,'','',NULL);
INSERT INTO `IPv4Address` VALUES (180879680,'network','','yes');
INSERT INTO `IPv4Address` VALUES (180879743,'broadcast','','yes');
INSERT INTO `IPv4Address` VALUES (180879681,'','',NULL);
INSERT INTO `IPv4Address` VALUES (180879683,'','',NULL);
INSERT INTO `IPv4Address` VALUES (180879684,'','',NULL);
INSERT INTO `IPv4Address` VALUES (180879685,'','',NULL);
INSERT INTO `IPv4Address` VALUES (180879616,'network','','yes');
INSERT INTO `IPv4Address` VALUES (180879679,'broadcast','','yes');
INSERT INTO `IPv4Address` VALUES (180879686,'','',NULL);
INSERT INTO `IPv4Address` VALUES (180879687,'','',NULL);
INSERT INTO `IPv4Address` VALUES (180879360,'','',NULL);
INSERT INTO `IPv4Address` VALUES (180879361,'','',NULL);
INSERT INTO `IPv4Address` VALUES (180880192,'network','','yes');
INSERT INTO `IPv4Address` VALUES (180880255,'broadcast','','yes');
INSERT INTO `IPv4Address` VALUES (180880448,'network','','yes');
INSERT INTO `IPv4Address` VALUES (180880511,'broadcast','','yes');
INSERT INTO `IPv4Address` VALUES (180880384,'network','','yes');
INSERT INTO `IPv4Address` VALUES (180880447,'broadcast','','yes');
INSERT INTO `IPv4Address` VALUES (180880128,'network','','no');
INSERT INTO `IPv4Address` VALUES (180880191,'broadcast','','no');
INSERT INTO `IPv4Address` VALUES (180879936,'network','','yes');
INSERT INTO `IPv4Address` VALUES (180879999,'broadcast','','yes');
INSERT INTO `IPv4Address` VALUES (180879872,'network','','yes');
INSERT INTO `IPv4Address` VALUES (180879935,'broadcast','','yes');
INSERT INTO `IPv4Address` VALUES (180880193,'','',NULL);
INSERT INTO `IPv4Address` VALUES (180880194,'','',NULL);
INSERT INTO `IPv4Address` VALUES (180880195,'','',NULL);
INSERT INTO `IPv4Address` VALUES (180879618,'','',NULL);
INSERT INTO `IPv4Address` VALUES (180879677,'','',NULL);
INSERT INTO `IPv4Address` VALUES (180879676,'','',NULL);
INSERT INTO `IPv4Address` VALUES (180879675,'','',NULL);
INSERT INTO `IPv4Address` VALUES (180880254,'','',NULL);
INSERT INTO `IPv4Address` VALUES (180880504,'for field engineer','','yes');

INSERT INTO `IPv4Allocation` VALUES (905,180879678,'fa2/0','router');
INSERT INTO `IPv4Allocation` VALUES (906,180879617,'','regular');
INSERT INTO `IPv4Allocation` VALUES (910,180879681,'eth0','regular');
INSERT INTO `IPv4Allocation` VALUES (911,180879682,'eth0','regular');
INSERT INTO `IPv4Allocation` VALUES (912,180879683,'eth0','regular');
INSERT INTO `IPv4Allocation` VALUES (913,180879684,'eth0','regular');
INSERT INTO `IPv4Allocation` VALUES (914,180879685,'eth0','regular');
INSERT INTO `IPv4Allocation` VALUES (918,180879686,'bge0','regular');
INSERT INTO `IPv4Allocation` VALUES (919,180879687,'bge0','regular');
INSERT INTO `IPv4Allocation` VALUES (908,180879360,'se1/0','regular');
INSERT INTO `IPv4Allocation` VALUES (905,180879361,'se1/1','regular');
INSERT INTO `IPv4Allocation` VALUES (923,180880193,'eth0','regular');
INSERT INTO `IPv4Allocation` VALUES (924,180880194,'eth0','regular');
INSERT INTO `IPv4Allocation` VALUES (925,180880195,'eth0','regular');
INSERT INTO `IPv4Allocation` VALUES (915,180879618,'telnet access','regular');
INSERT INTO `IPv4Allocation` VALUES (915,180879677,'VIP3','regular');
INSERT INTO `IPv4Allocation` VALUES (915,180879676,'VIP2','regular');
INSERT INTO `IPv4Allocation` VALUES (915,180879675,'VIP1','regular');
INSERT INTO `IPv4Allocation` VALUES (910,180879675,'','virtual');
INSERT INTO `IPv4Allocation` VALUES (911,180879675,'','virtual');
INSERT INTO `IPv4Allocation` VALUES (912,180879675,'','virtual');
INSERT INTO `IPv4Allocation` VALUES (919,180879677,'','virtual');
INSERT INTO `IPv4Allocation` VALUES (918,180879677,'','virtual');
INSERT INTO `IPv4Allocation` VALUES (909,180880254,'fa2/0','router');
INSERT INTO `IPv4Allocation` VALUES (907,180879363,'se1/1','regular');
INSERT INTO `IPv4Allocation` VALUES (905,180879362,'se1/0','regular');
INSERT INTO `IPv4Allocation` VALUES (914,180879676,'','virtual');
INSERT INTO `IPv4Allocation` VALUES (913,180879676,'','virtual');
INSERT INTO `IPv4Allocation` VALUES (932,180879973,'','regular');
INSERT INTO `IPv4Allocation` VALUES (934,180879974,'','regular');
INSERT INTO `IPv4Allocation` VALUES (936,180879975,'','regular');
INSERT INTO `IPv4Allocation` VALUES (938,180879976,'','regular');
INSERT INTO `IPv4Allocation` VALUES (940,180879977,'','regular');
INSERT INTO `IPv4Allocation` VALUES (933,180879978,'','regular');
INSERT INTO `IPv4Allocation` VALUES (935,180879979,'','regular');
INSERT INTO `IPv4Allocation` VALUES (937,180879980,'','regular');
INSERT INTO `IPv4Allocation` VALUES (939,180879981,'','regular');
INSERT INTO `IPv4Allocation` VALUES (941,180879982,'','regular');
INSERT INTO `IPv4Allocation` VALUES (928,180879877,'','regular');
INSERT INTO `IPv4Allocation` VALUES (929,180879878,'','regular');
INSERT INTO `IPv4Allocation` VALUES (956,180880449,'eth0','regular');
INSERT INTO `IPv4Allocation` VALUES (957,180880450,'eth0','regular');
INSERT INTO `IPv4Allocation` VALUES (958,180880451,'eth0','regular');
INSERT INTO `IPv4Allocation` VALUES (959,180880452,'eth0','regular');
INSERT INTO `IPv4Allocation` VALUES (960,180880453,'eth0','regular');
INSERT INTO `IPv4Allocation` VALUES (908,180880510,'fa2/0','router');
INSERT INTO `IPv4Allocation` VALUES (962,180880386,'','regular');
INSERT INTO `IPv4Allocation` VALUES (909,180879366,'se1/0','regular');
INSERT INTO `IPv4Allocation` VALUES (908,180879367,'se1/1','regular');
INSERT INTO `IPv4Allocation` VALUES (909,180879365,'se1/1','regular');
INSERT INTO `IPv4Allocation` VALUES (907,180879364,'se1/0','regular');
INSERT INTO `IPv4Allocation` VALUES (907,180879998,'','shared');
INSERT INTO `IPv4Allocation` VALUES (927,180879998,'','shared');
INSERT INTO `IPv4Allocation` VALUES (908,180880446,'fa1/0','router');
INSERT INTO `IPv4Allocation` VALUES (961,180880385,'','regular');

INSERT INTO `IPv6Allocation` VALUES ('908', 0xFC0014500001BEEF0000000000000001, 'fa1/0', 'router');
INSERT INTO `IPv6Allocation` VALUES ('919', 0xFC0014500001DEAD0000000000000001, 'bge1', 'router');

INSERT INTO `IPv4Network` (`id`, `ip`, `mask`, `name`) VALUES (96,180879616,26,'London network devices and VIPs');
INSERT INTO `IPv4Network` (`id`, `ip`, `mask`, `name`) VALUES (97,180879680,26,'London HA server farm');
INSERT INTO `IPv4Network` (`id`, `ip`, `mask`, `name`) VALUES (98,180879872,26,'New-York network devices');
INSERT INTO `IPv4Network` (`id`, `ip`, `mask`, `name`) VALUES (99,180879936,26,'New-York servers');
INSERT INTO `IPv4Network` (`id`, `ip`, `mask`, `name`) VALUES (108,180880192,26,'Tokyo server farm');
INSERT INTO `IPv4Network` (`id`, `ip`, `mask`, `name`) VALUES (102,180879360,31,'M-L P2P');
INSERT INTO `IPv4Network` (`id`, `ip`, `mask`, `name`) VALUES (103,180879362,31,'L-NY P2P');
INSERT INTO `IPv4Network` (`id`, `ip`, `mask`, `name`) VALUES (104,180879364,31,'NY-T P2P');
INSERT INTO `IPv4Network` (`id`, `ip`, `mask`, `name`) VALUES (105,180879366,31,'T-M P2P');
INSERT INTO `IPv4Network` (`id`, `ip`, `mask`, `name`) VALUES (106,180880384,26,'Moscow network devices');
INSERT INTO `IPv4Network` (`id`, `ip`, `mask`, `name`) VALUES (107,180880448,26,'Moscow servers');

INSERT INTO `IPv6Network` (`id`, `ip`, `mask`, `last_ip`, `name`, `comment`) VALUES ('1', 0xFC000000000000000000000000000000, '7', 0xFDFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF, 'Local IPv6', null);
INSERT INTO `IPv6Network` (`id`, `ip`, `mask`, `last_ip`, `name`, `comment`) VALUES ('2', 0xFC001450000100000000000000000000, '48', 0xFC0014500001FFFFFFFFFFFFFFFFFFFF, 'v6 Suballoc 1', null);
INSERT INTO `IPv6Network` (`id`, `ip`, `mask`, `last_ip`, `name`, `comment`) VALUES ('3', 0xFC001450000200000000000000000000, '48', 0xFC0014500002FFFFFFFFFFFFFFFFFFFF, 'v6 Suballoc 2', null);
INSERT INTO `IPv6Network` (`id`, `ip`, `mask`, `last_ip`, `name`, `comment`) VALUES ('4', 0xFC0014500001DEAD0000000000000000, '64', 0xFC0014500001DEADFFFFFFFFFFFFFFFF, 'dead net', null);
INSERT INTO `IPv6Network` (`id`, `ip`, `mask`, `last_ip`, `name`, `comment`) VALUES ('5', 0xFC0014500001BEEF0000000000000000, '64', 0xFC0014500001BEEFFFFFFFFFFFFFFFFF, 'beef net', null);

INSERT INTO `IPv4RSPool` VALUES (1,'Apache servers',NULL,NULL);
INSERT INTO `IPv4RSPool` VALUES (2,'Resin servers',NULL,NULL);

INSERT INTO `IPv4VS` VALUES (1,0x0AC80205,80,'TCP','virtual web','lvs_sched wlc\ndelay_loop 3\nalpha\nomega\nquorum 3\nhysteresis 1\n\n# this is a comment\n# VS name is %VNAME%\n#\n','CHECK=%CHECK_TCP%\n');
INSERT INTO `IPv4VS` VALUES (2,0x0AC80206,80,'TCP','virtual app','lvs_sched wlc\r\nlvs_method NAT\r\ndelay_loop 3\r\nalpha\r\nomega\r\nquorum 3\r\nhysteresis 1\r\n\r\n','HTTP_GET {\r\nurl {\r\npath /\r\nstatus_code 200\r\n}\r\nconnect_timeout 1\r\n}');
INSERT INTO `IPv4VS` VALUES (3,0x0000000C,NULL,'MARK','test fwmark service','lvs_sched wrr\nVIP=10.200.2.5\nMETHOD=TUN\nDELAY_LOOP=10','CHECK=%CHECK_TCP%');

INSERT INTO `IPv4LB` VALUES (928,1,1,NULL,NULL,NULL);
INSERT INTO `IPv4LB` VALUES (929,1,1,NULL,NULL,NULL);
INSERT INTO `IPv4LB` VALUES (929,2,2,NULL,NULL,NULL);

INSERT INTO `IPv4RS` VALUES (1,'yes',0x0AC80265,80,1,NULL,NULL);
INSERT INTO `IPv4RS` VALUES (2,'yes',0x0AC80266,80,1,NULL,NULL);
INSERT INTO `IPv4RS` VALUES (3,'no',0x0AC80267,80,1,NULL,NULL);
INSERT INTO `IPv4RS` VALUES (4,'yes',0x0AC80268,80,1,NULL,NULL);
INSERT INTO `IPv4RS` VALUES (5,'yes',0x0AC80269,80,1,NULL,NULL);
INSERT INTO `IPv4RS` VALUES (6,'no',0x0AC8026A,8080,2,NULL,NULL);
INSERT INTO `IPv4RS` VALUES (7,'yes',0x0AC8026B,8080,2,NULL,NULL);
INSERT INTO `IPv4RS` VALUES (8,'yes',0x0AC8026C,8080,2,NULL,NULL);
INSERT INTO `IPv4RS` VALUES (9,'yes',0x0AC8026D,8080,2,NULL,NULL);
INSERT INTO `IPv4RS` VALUES (10,'yes',0x0AC8026E,8080,2,NULL,NULL);

INSERT INTO PortInterfaceCompat (iif_id, oif_id) VALUES
(1,32);
INSERT INTO `Port` VALUES (3057,905,'se1/0',1,32,NULL,NULL,'');
INSERT INTO `Port` VALUES (3058,905,'se1/1',1,32,NULL,NULL,'');
INSERT INTO `Port` VALUES (3059,905,'fa2/0',1,19,'00000000A001',NULL,'');
INSERT INTO `Port` VALUES (3060,905,'fa2/1',1,19,'00000000A002','ISP uplink','');
INSERT INTO `Port` VALUES (3063,906,'gi1',1,24,'00000000B001',NULL,'1');
INSERT INTO `Port` VALUES (3062,906,'gi2',1,24,'00000000B002',NULL,'2');
INSERT INTO `Port` VALUES (3064,906,'gi3',1,24,'00000000B003',NULL,'3');
INSERT INTO `Port` VALUES (3065,906,'gi4',1,24,'00000000B004',NULL,'4');
INSERT INTO `Port` VALUES (3066,906,'gi5',1,24,'00000000B005',NULL,'5');
INSERT INTO `Port` VALUES (3067,906,'gi6',1,24,'00000000B006',NULL,'6');
INSERT INTO `Port` VALUES (3068,906,'gi7',1,24,'00000000B007',NULL,'7');
INSERT INTO `Port` VALUES (3069,906,'gi8',1,24,'00000000B008',NULL,'8');
INSERT INTO `Port` VALUES (3070,907,'se1/0',1,32,NULL,NULL,'');
INSERT INTO `Port` VALUES (3071,907,'se1/1',1,32,NULL,NULL,'');
INSERT INTO `Port` VALUES (3072,915,'e1',1,19,NULL,NULL,'1');
INSERT INTO `Port` VALUES (3073,915,'e2',1,19,NULL,NULL,'2');
INSERT INTO `Port` VALUES (3074,915,'e3',1,19,NULL,NULL,'3');
INSERT INTO `Port` VALUES (3075,915,'e4',1,19,NULL,NULL,'4');
INSERT INTO `Port` VALUES (3076,915,'e5',1,19,NULL,NULL,'5');
INSERT INTO `Port` VALUES (3077,915,'e6',1,19,NULL,NULL,'6');
INSERT INTO `Port` VALUES (3078,915,'e7',1,19,NULL,NULL,'7');
INSERT INTO `Port` VALUES (3079,915,'e8',1,19,NULL,NULL,'8');
INSERT INTO `Port` VALUES (3080,910,'eth0',1,24,NULL,NULL,'1');
INSERT INTO `Port` VALUES (3081,910,'eth1',1,24,NULL,NULL,'2');
INSERT INTO `Port` VALUES (3082,909,'se1/0',1,32,NULL,NULL,'');
INSERT INTO `Port` VALUES (3083,909,'se1/1',1,32,NULL,NULL,'');
INSERT INTO `Port` VALUES (3084,908,'se1/0',1,32,NULL,NULL,'');
INSERT INTO `Port` VALUES (3085,908,'se1/1',1,32,NULL,NULL,'');
INSERT INTO `Port` VALUES (3086,911,'eth0',1,24,NULL,NULL,'1');
INSERT INTO `Port` VALUES (3087,911,'eth1',1,24,NULL,NULL,'2');
INSERT INTO `Port` VALUES (3088,912,'eth0',1,24,NULL,NULL,'1');
INSERT INTO `Port` VALUES (3089,912,'eth1',1,24,NULL,NULL,'2');
INSERT INTO `Port` VALUES (3090,913,'eth0',1,24,NULL,NULL,'1');
INSERT INTO `Port` VALUES (3091,913,'eth1',1,24,NULL,NULL,'2');
INSERT INTO `Port` VALUES (3092,914,'eth0',1,24,NULL,NULL,'1');
INSERT INTO `Port` VALUES (3093,914,'eth1',1,24,NULL,NULL,'2');
INSERT INTO `Port` VALUES (3094,917,'fa0/0',1,19,NULL,NULL,'');
INSERT INTO `Port` VALUES (3095,919,'bge0',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3096,919,'bge1',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3097,918,'bge0',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3098,918,'bge1',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3099,909,'fa2/0',1,19,NULL,NULL,'');
INSERT INTO `Port` VALUES (3100,909,'fa2/1',1,19,NULL,'ISP uplink','');
INSERT INTO `Port` VALUES (3101,926,'fa1',1,19,NULL,NULL,'1');
INSERT INTO `Port` VALUES (3102,926,'fa2',1,19,NULL,NULL,'2');
INSERT INTO `Port` VALUES (3103,926,'fa3',1,19,NULL,NULL,'3');
INSERT INTO `Port` VALUES (3104,926,'fa4',1,19,NULL,NULL,'4');
INSERT INTO `Port` VALUES (3105,926,'fa5',1,19,NULL,NULL,'5');
INSERT INTO `Port` VALUES (3106,926,'fa6',1,19,NULL,NULL,'6');
INSERT INTO `Port` VALUES (3107,926,'fa7',1,19,NULL,NULL,'7');
INSERT INTO `Port` VALUES (3108,926,'fa8',1,19,NULL,NULL,'8');
INSERT INTO `Port` VALUES (3109,926,'fa9',1,19,NULL,NULL,'9');
INSERT INTO `Port` VALUES (3110,926,'fa10',1,19,NULL,NULL,'10');
INSERT INTO `Port` VALUES (3111,926,'fa11',1,19,NULL,NULL,'11');
INSERT INTO `Port` VALUES (3112,926,'fa12',1,19,NULL,NULL,'12');
INSERT INTO `Port` VALUES (3113,923,'eth0',1,24,NULL,NULL,'1');
INSERT INTO `Port` VALUES (3114,923,'eth1',1,24,NULL,NULL,'2');
INSERT INTO `Port` VALUES (3115,924,'eth0',1,24,NULL,NULL,'1');
INSERT INTO `Port` VALUES (3116,924,'eth1',1,24,NULL,NULL,'2');
INSERT INTO `Port` VALUES (3117,925,'eth0',1,24,NULL,NULL,'1');
INSERT INTO `Port` VALUES (3118,925,'eth1',1,24,NULL,NULL,'2');
INSERT INTO `Port` VALUES (3119,908,'fa2/0',1,19,NULL,NULL,'');
INSERT INTO `Port` VALUES (3120,908,'fa2/1',1,19,NULL,'ISP uplink','');
INSERT INTO `Port` VALUES (3121,907,'fa2/0',1,19,NULL,NULL,'');
INSERT INTO `Port` VALUES (3122,907,'fa2/1',1,19,NULL,NULL,'');
INSERT INTO `Port` VALUES (3123,927,'gi3/0',3,1202,NULL,'ISP uplink','');
INSERT INTO `Port` VALUES (3124,927,'gi4/0',3,1202,NULL,NULL,'');
INSERT INTO `Port` VALUES (3125,907,'gi3/0',3,1202,NULL,'ISP uplink','');
INSERT INTO `Port` VALUES (3126,907,'gi4/0',3,1202,NULL,NULL,'');
INSERT INTO `Port` VALUES (3127,956,'kvm',1,33,NULL,NULL,'');
INSERT INTO `Port` VALUES (3128,956,'eth0',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3129,956,'eth1',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3130,957,'kvm',1,33,NULL,NULL,'');
INSERT INTO `Port` VALUES (3131,957,'eth0',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3132,957,'eth1',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3133,958,'kvm',1,33,NULL,NULL,'');
INSERT INTO `Port` VALUES (3134,958,'eth0',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3135,958,'eth1',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3136,959,'kvm',1,33,NULL,NULL,'');
INSERT INTO `Port` VALUES (3137,959,'eth0',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3138,959,'eth1',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3139,960,'kvm',1,33,NULL,NULL,'');
INSERT INTO `Port` VALUES (3140,960,'eth0',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3141,960,'eth1',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3142,908,'con0',1,29,NULL,NULL,'console');
INSERT INTO `Port` VALUES (3143,961,'1',1,24,'01040104AA00',NULL,'');
INSERT INTO `Port` VALUES (3144,961,'2',1,24,'01040104AA01','for field engineer','');
INSERT INTO `Port` VALUES (3145,961,'3',1,24,'01040104AA02',NULL,'');
INSERT INTO `Port` VALUES (3146,961,'4',1,24,'01040104AA03',NULL,'');
INSERT INTO `Port` VALUES (3147,961,'5',1,24,'01040104AA04',NULL,'');
INSERT INTO `Port` VALUES (3148,961,'6',1,24,'01040104AA05',NULL,'');
INSERT INTO `Port` VALUES (3149,961,'7',1,24,'01040104AA06',NULL,'');
INSERT INTO `Port` VALUES (3150,961,'8',1,24,'01040104AA07',NULL,'');
INSERT INTO `Port` VALUES (3151,961,'9',1,24,'01040104AA08',NULL,'');
INSERT INTO `Port` VALUES (3152,961,'10',1,24,'01040104AA09',NULL,'');
INSERT INTO `Port` VALUES (3153,961,'11',1,24,'01040104AA0A',NULL,'');
INSERT INTO `Port` VALUES (3154,961,'12',1,24,'01040104AA0B',NULL,'');
INSERT INTO `Port` VALUES (3155,961,'13',1,24,'01040104AA0C',NULL,'');
INSERT INTO `Port` VALUES (3156,961,'14',1,24,'01040104AA0D',NULL,'');
INSERT INTO `Port` VALUES (3157,961,'15',1,24,'01040104AA0E',NULL,'');
INSERT INTO `Port` VALUES (3158,961,'16',1,24,'01040104AA0F',NULL,'');
INSERT INTO `Port` VALUES (3159,961,'con',1,681,NULL,NULL,'console');
INSERT INTO `Port` VALUES (3160,956,'ttyS0',1,681,NULL,NULL,'serial A');
INSERT INTO `Port` VALUES (3161,956,'ttyS1',1,681,NULL,NULL,'serial B');
INSERT INTO `Port` VALUES (3162,962,'tail1',1,446,NULL,NULL,'');
INSERT INTO `Port` VALUES (3163,962,'tail2',1,446,NULL,NULL,'');
INSERT INTO `Port` VALUES (3164,962,'tail3',1,446,NULL,NULL,'');
INSERT INTO `Port` VALUES (3165,962,'tail4',1,446,NULL,NULL,'');
INSERT INTO `Port` VALUES (3166,962,'tail5',1,446,NULL,NULL,'');
INSERT INTO `Port` VALUES (3167,962,'tail6',1,446,NULL,NULL,'');
INSERT INTO `Port` VALUES (3168,962,'tail7',1,446,NULL,NULL,'');
INSERT INTO `Port` VALUES (3169,962,'tail8',1,446,NULL,NULL,'');
INSERT INTO `Port` VALUES (3170,962,'head',1,33,NULL,'monitor connected','');
INSERT INTO `Port` VALUES (3171,962,'net',1,19,'020002003333',NULL,'');
INSERT INTO `Port` VALUES (3178,927,'fa1/0',1,19,NULL,NULL,'');
INSERT INTO `Port` VALUES (3179,908,'fa1/0',1,19,NULL,NULL,'');
INSERT INTO `Port` VALUES (3180,955,'in',1,16,NULL,'from local distribution','');
INSERT INTO `Port` VALUES (3181,955,'out1',1,1322,NULL,NULL,'');
INSERT INTO `Port` VALUES (3182,955,'out2',1,1322,NULL,NULL,'');
INSERT INTO `Port` VALUES (3183,955,'out3',1,1322,NULL,NULL,'');
INSERT INTO `Port` VALUES (3184,955,'out4',1,1322,NULL,NULL,'');
INSERT INTO `Port` VALUES (3185,955,'out5',1,1322,NULL,NULL,'');
INSERT INTO `Port` VALUES (3186,923,'ps',1,16,NULL,NULL,'');
INSERT INTO `Port` VALUES (3187,924,'ps',1,16,NULL,NULL,'');
INSERT INTO `Port` VALUES (3188,925,'ps',1,16,NULL,NULL,'');
INSERT INTO `Port` VALUES (3189,926,'ps',1,16,NULL,NULL,'');
INSERT INTO `Port` VALUES (3190,909,'ps',1,16,NULL,NULL,'');
INSERT INTO `Port` VALUES (3191,979,'gi0/1',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3192,979,'gi0/2',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3193,979,'gi0/3',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3194,979,'gi0/4',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3195,979,'gi0/5',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3196,979,'gi0/6',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3197,979,'gi0/7',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3198,979,'gi0/8',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3199,979,'gi0/9',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3200,979,'gi0/10',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3201,979,'gi0/11',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3202,979,'gi0/12',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3203,979,'gi0/13',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3204,979,'gi0/14',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3205,979,'gi0/15',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3206,979,'gi0/16',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3207,979,'gi0/17',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3208,979,'gi0/18',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3209,979,'gi0/19',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3210,979,'gi0/20',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3211,979,'gi0/21',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3212,979,'gi0/22',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3213,979,'gi0/23',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3214,979,'gi0/24',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3215,980,'gi0/0/1',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3216,980,'gi0/0/2',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3217,980,'gi0/0/3',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3218,980,'gi0/0/4',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3219,980,'gi0/0/5',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3220,980,'gi0/0/6',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3221,980,'gi0/0/7',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3222,980,'gi0/0/8',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3223,980,'gi0/0/9',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3224,980,'gi0/0/10',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3225,980,'gi0/0/11',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3226,980,'gi0/0/12',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3227,980,'gi0/0/13',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3228,980,'gi0/0/14',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3229,980,'gi0/0/15',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3230,980,'gi0/0/16',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3231,980,'gi0/0/17',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3232,980,'gi0/0/18',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3233,980,'gi0/0/19',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3234,980,'gi0/0/20',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3235,980,'gi0/0/21',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3236,980,'gi0/0/22',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3237,980,'gi0/0/23',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3238,980,'gi0/0/24',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3239,980,'gi0/0/25',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3240,980,'gi0/0/26',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3241,980,'gi0/0/27',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3242,980,'gi0/0/28',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3243,980,'gi0/0/29',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3244,980,'gi0/0/30',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3245,980,'gi0/0/31',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3246,980,'gi0/0/32',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3247,980,'gi0/0/33',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3248,980,'gi0/0/34',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3249,980,'gi0/0/35',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3250,980,'gi0/0/36',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3251,980,'gi0/0/37',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3252,980,'gi0/0/38',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3253,980,'gi0/0/39',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3254,980,'gi0/0/40',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3255,980,'gi0/0/41',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3256,980,'gi0/0/42',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3257,980,'gi0/0/43',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3258,980,'gi0/0/44',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3259,980,'gi0/0/45',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3260,980,'gi0/0/46',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3261,980,'gi0/0/47',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3262,980,'gi0/0/48',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3263,981,'gi0/1',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3264,981,'gi0/2',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3265,981,'gi0/3',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3266,981,'gi0/4',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3267,981,'gi0/5',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3268,981,'gi0/6',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3269,981,'gi0/7',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3270,981,'gi0/8',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3271,981,'gi0/9',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3272,981,'gi0/10',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3273,981,'gi0/11',1,24,NULL,NULL,'');
INSERT INTO `Port` VALUES (3274,981,'gi0/12',1,24,NULL,NULL,'');

INSERT INTO `Link` VALUES (3057,3071,NULL);
INSERT INTO `Link` VALUES (3058,3084,NULL);
INSERT INTO `Link` VALUES (3059,3069,NULL);
INSERT INTO `Link` VALUES (3062,3094,NULL);
INSERT INTO `Link` VALUES (3063,3079,NULL);
INSERT INTO `Link` VALUES (3070,3083,NULL);
INSERT INTO `Link` VALUES (3072,3080,NULL);
INSERT INTO `Link` VALUES (3073,3086,NULL);
INSERT INTO `Link` VALUES (3074,3088,NULL);
INSERT INTO `Link` VALUES (3075,3090,NULL);
INSERT INTO `Link` VALUES (3076,3092,NULL);
INSERT INTO `Link` VALUES (3077,3097,NULL);
INSERT INTO `Link` VALUES (3078,3095,NULL);
INSERT INTO `Link` VALUES (3082,3085,NULL);
INSERT INTO `Link` VALUES (3099,3112,NULL);
INSERT INTO `Link` VALUES (3101,3113,NULL);
INSERT INTO `Link` VALUES (3102,3114,NULL);
INSERT INTO `Link` VALUES (3103,3115,NULL);
INSERT INTO `Link` VALUES (3104,3116,NULL);
INSERT INTO `Link` VALUES (3105,3117,NULL);
INSERT INTO `Link` VALUES (3106,3118,NULL);
INSERT INTO `Link` VALUES (3142,3160,NULL);
INSERT INTO `Link` VALUES (3159,3161,NULL);
INSERT INTO `Link` VALUES (3134,3143,NULL);
INSERT INTO `Link` VALUES (3119,3147,NULL);
INSERT INTO `Link` VALUES (3131,3148,NULL);
INSERT INTO `Link` VALUES (3137,3151,NULL);
INSERT INTO `Link` VALUES (3140,3154,NULL);
INSERT INTO `Link` VALUES (3128,3158,NULL);
INSERT INTO `Link` VALUES (3127,3162,NULL);
INSERT INTO `Link` VALUES (3130,3163,NULL);
INSERT INTO `Link` VALUES (3133,3164,NULL);
INSERT INTO `Link` VALUES (3136,3165,NULL);
INSERT INTO `Link` VALUES (3139,3168,NULL);
INSERT INTO `Link` VALUES (3145,3171,NULL);
INSERT INTO `Link` VALUES (3124,3126,NULL);
INSERT INTO `Link` VALUES (3181,3186,NULL);
INSERT INTO `Link` VALUES (3182,3187,NULL);
INSERT INTO `Link` VALUES (3183,3190,NULL);
INSERT INTO `Link` VALUES (3184,3188,NULL);
INSERT INTO `Link` VALUES (3185,3189,NULL);
INSERT INTO `Link` VALUES (3096,3219,NULL);
INSERT INTO `Link` VALUES (3098,3191,NULL);
INSERT INTO `Link` VALUES (3214,3263,NULL);
INSERT INTO `Link` VALUES (3262,3265,NULL);

INSERT INTO `IPv4NAT` (`object_id`, `proto`, `localip`, `localport`, `remoteip`, `remoteport`, `description`) VALUES (915,'TCP',180879675,80,180879681,80,'');
INSERT INTO `IPv4NAT` (`object_id`, `proto`, `localip`, `localport`, `remoteip`, `remoteport`, `description`) VALUES (915,'TCP',180879675,80,180879682,80,'');
INSERT INTO `IPv4NAT` (`object_id`, `proto`, `localip`, `localport`, `remoteip`, `remoteport`, `description`) VALUES (915,'TCP',180879675,80,180879683,80,'');
INSERT INTO `IPv4NAT` (`object_id`, `proto`, `localip`, `localport`, `remoteip`, `remoteport`, `description`) VALUES (915,'UDP',180879676,53,180879684,53,'');
INSERT INTO `IPv4NAT` (`object_id`, `proto`, `localip`, `localport`, `remoteip`, `remoteport`, `description`) VALUES (915,'UDP',180879676,53,180879685,53,'');
INSERT INTO `IPv4NAT` (`object_id`, `proto`, `localip`, `localport`, `remoteip`, `remoteport`, `description`) VALUES (915,'TCP',180879677,443,180879686,443,'');
INSERT INTO `IPv4NAT` (`object_id`, `proto`, `localip`, `localport`, `remoteip`, `remoteport`, `description`) VALUES (915,'TCP',180879677,443,180879687,443,'');

INSERT INTO `RackSpace` VALUES (968,9,'interior','T',905);
INSERT INTO `RackSpace` VALUES (968,9,'front','T',905);
INSERT INTO `RackSpace` VALUES (968,10,'interior','T',905);
INSERT INTO `RackSpace` VALUES (968,10,'front','T',905);
INSERT INTO `RackSpace` VALUES (968,11,'interior','T',905);
INSERT INTO `RackSpace` VALUES (968,11,'front','T',905);
INSERT INTO `RackSpace` VALUES (968,7,'front','T',906);
INSERT INTO `RackSpace` VALUES (968,7,'interior','T',906);
INSERT INTO `RackSpace` VALUES (970,16,'interior','T',907);
INSERT INTO `RackSpace` VALUES (970,16,'rear','T',907);
INSERT INTO `RackSpace` VALUES (970,15,'interior','T',907);
INSERT INTO `RackSpace` VALUES (970,15,'rear','T',907);
INSERT INTO `RackSpace` VALUES (970,14,'interior','T',907);
INSERT INTO `RackSpace` VALUES (970,14,'rear','T',907);
INSERT INTO `RackSpace` VALUES (974,15,'interior','T',909);
INSERT INTO `RackSpace` VALUES (974,15,'rear','T',909);
INSERT INTO `RackSpace` VALUES (974,14,'interior','T',909);
INSERT INTO `RackSpace` VALUES (974,14,'rear','T',909);
INSERT INTO `RackSpace` VALUES (974,13,'interior','T',909);
INSERT INTO `RackSpace` VALUES (974,13,'rear','T',909);
INSERT INTO `RackSpace` VALUES (972,20,'interior','T',908);
INSERT INTO `RackSpace` VALUES (972,20,'rear','T',908);
INSERT INTO `RackSpace` VALUES (972,19,'interior','T',908);
INSERT INTO `RackSpace` VALUES (972,19,'rear','T',908);
INSERT INTO `RackSpace` VALUES (972,18,'interior','T',908);
INSERT INTO `RackSpace` VALUES (972,18,'rear','T',908);
INSERT INTO `RackSpace` VALUES (967,2,'front','T',910);
INSERT INTO `RackSpace` VALUES (967,2,'interior','T',910);
INSERT INTO `RackSpace` VALUES (967,2,'rear','T',910);
INSERT INTO `RackSpace` VALUES (967,4,'front','T',911);
INSERT INTO `RackSpace` VALUES (967,4,'interior','T',911);
INSERT INTO `RackSpace` VALUES (967,4,'rear','T',911);
INSERT INTO `RackSpace` VALUES (967,6,'front','T',912);
INSERT INTO `RackSpace` VALUES (967,6,'interior','T',912);
INSERT INTO `RackSpace` VALUES (967,6,'rear','T',912);
INSERT INTO `RackSpace` VALUES (967,8,'front','T',913);
INSERT INTO `RackSpace` VALUES (967,8,'interior','T',913);
INSERT INTO `RackSpace` VALUES (967,8,'rear','T',913);
INSERT INTO `RackSpace` VALUES (967,10,'front','T',914);
INSERT INTO `RackSpace` VALUES (967,10,'interior','T',914);
INSERT INTO `RackSpace` VALUES (967,10,'rear','T',914);
INSERT INTO `RackSpace` VALUES (967,23,'rear','T',916);
INSERT INTO `RackSpace` VALUES (967,23,'interior','T',916);
INSERT INTO `RackSpace` VALUES (967,23,'front','T',916);
INSERT INTO `RackSpace` VALUES (968,5,'front','T',915);
INSERT INTO `RackSpace` VALUES (968,5,'interior','T',915);
INSERT INTO `RackSpace` VALUES (968,4,'interior','T',915);
INSERT INTO `RackSpace` VALUES (968,4,'front','T',915);
INSERT INTO `RackSpace` VALUES (967,22,'front','T',916);
INSERT INTO `RackSpace` VALUES (967,22,'interior','T',916);
INSERT INTO `RackSpace` VALUES (967,22,'rear','T',916);
INSERT INTO `RackSpace` VALUES (969,7,'front','T',918);
INSERT INTO `RackSpace` VALUES (969,7,'interior','T',918);
INSERT INTO `RackSpace` VALUES (969,7,'rear','T',918);
INSERT INTO `RackSpace` VALUES (969,6,'front','T',918);
INSERT INTO `RackSpace` VALUES (969,6,'interior','T',918);
INSERT INTO `RackSpace` VALUES (969,6,'rear','T',918);
INSERT INTO `RackSpace` VALUES (969,5,'front','T',918);
INSERT INTO `RackSpace` VALUES (969,5,'interior','T',918);
INSERT INTO `RackSpace` VALUES (969,5,'rear','T',918);
INSERT INTO `RackSpace` VALUES (969,4,'front','T',918);
INSERT INTO `RackSpace` VALUES (969,4,'interior','T',918);
INSERT INTO `RackSpace` VALUES (969,4,'rear','T',918);
INSERT INTO `RackSpace` VALUES (969,3,'front','T',918);
INSERT INTO `RackSpace` VALUES (969,3,'interior','T',918);
INSERT INTO `RackSpace` VALUES (969,3,'rear','T',918);
INSERT INTO `RackSpace` VALUES (969,13,'front','T',919);
INSERT INTO `RackSpace` VALUES (969,13,'interior','T',919);
INSERT INTO `RackSpace` VALUES (969,13,'rear','T',919);
INSERT INTO `RackSpace` VALUES (969,12,'front','T',919);
INSERT INTO `RackSpace` VALUES (969,12,'interior','T',919);
INSERT INTO `RackSpace` VALUES (969,12,'rear','T',919);
INSERT INTO `RackSpace` VALUES (969,11,'front','T',919);
INSERT INTO `RackSpace` VALUES (969,11,'interior','T',919);
INSERT INTO `RackSpace` VALUES (969,11,'rear','T',919);
INSERT INTO `RackSpace` VALUES (969,10,'front','T',919);
INSERT INTO `RackSpace` VALUES (969,10,'interior','T',919);
INSERT INTO `RackSpace` VALUES (969,10,'rear','T',919);
INSERT INTO `RackSpace` VALUES (969,9,'front','T',919);
INSERT INTO `RackSpace` VALUES (969,9,'interior','T',919);
INSERT INTO `RackSpace` VALUES (969,9,'rear','T',919);
INSERT INTO `RackSpace` VALUES (969,37,'front','T',920);
INSERT INTO `RackSpace` VALUES (969,37,'interior','T',920);
INSERT INTO `RackSpace` VALUES (969,37,'rear','T',920);
INSERT INTO `RackSpace` VALUES (969,36,'front','T',920);
INSERT INTO `RackSpace` VALUES (969,36,'interior','T',920);
INSERT INTO `RackSpace` VALUES (969,36,'rear','T',920);
INSERT INTO `RackSpace` VALUES (969,35,'front','T',920);
INSERT INTO `RackSpace` VALUES (969,35,'interior','T',920);
INSERT INTO `RackSpace` VALUES (969,35,'rear','T',920);
INSERT INTO `RackSpace` VALUES (969,34,'front','T',920);
INSERT INTO `RackSpace` VALUES (969,34,'interior','T',920);
INSERT INTO `RackSpace` VALUES (969,34,'rear','T',920);
INSERT INTO `RackSpace` VALUES (969,33,'front','T',920);
INSERT INTO `RackSpace` VALUES (969,33,'interior','T',920);
INSERT INTO `RackSpace` VALUES (969,33,'rear','T',920);
INSERT INTO `RackSpace` VALUES (969,32,'front','T',920);
INSERT INTO `RackSpace` VALUES (969,32,'interior','T',920);
INSERT INTO `RackSpace` VALUES (969,32,'rear','T',920);
INSERT INTO `RackSpace` VALUES (969,31,'front','T',920);
INSERT INTO `RackSpace` VALUES (969,31,'interior','T',920);
INSERT INTO `RackSpace` VALUES (969,31,'rear','T',920);
INSERT INTO `RackSpace` VALUES (969,30,'front','T',920);
INSERT INTO `RackSpace` VALUES (969,30,'interior','T',920);
INSERT INTO `RackSpace` VALUES (969,30,'rear','T',920);
INSERT INTO `RackSpace` VALUES (969,16,'front','T',921);
INSERT INTO `RackSpace` VALUES (969,16,'interior','T',921);
INSERT INTO `RackSpace` VALUES (969,16,'rear','T',921);
INSERT INTO `RackSpace` VALUES (969,15,'front','T',921);
INSERT INTO `RackSpace` VALUES (969,15,'interior','T',921);
INSERT INTO `RackSpace` VALUES (969,15,'rear','T',921);
INSERT INTO `RackSpace` VALUES (969,18,'front','T',922);
INSERT INTO `RackSpace` VALUES (969,18,'interior','T',922);
INSERT INTO `RackSpace` VALUES (969,18,'rear','T',922);
INSERT INTO `RackSpace` VALUES (969,17,'front','T',922);
INSERT INTO `RackSpace` VALUES (969,17,'interior','T',922);
INSERT INTO `RackSpace` VALUES (969,17,'rear','T',922);
INSERT INTO `RackSpace` VALUES (974,11,'interior','T',926);
INSERT INTO `RackSpace` VALUES (974,11,'rear','T',926);
INSERT INTO `RackSpace` VALUES (974,1,'front','T',923);
INSERT INTO `RackSpace` VALUES (974,1,'interior','T',923);
INSERT INTO `RackSpace` VALUES (974,1,'rear','T',923);
INSERT INTO `RackSpace` VALUES (974,3,'front','T',924);
INSERT INTO `RackSpace` VALUES (974,3,'interior','T',924);
INSERT INTO `RackSpace` VALUES (974,3,'rear','T',924);
INSERT INTO `RackSpace` VALUES (974,5,'front','T',925);
INSERT INTO `RackSpace` VALUES (974,5,'interior','T',925);
INSERT INTO `RackSpace` VALUES (974,5,'rear','T',925);
INSERT INTO `RackSpace` VALUES (970,12,'interior','T',927);
INSERT INTO `RackSpace` VALUES (970,12,'rear','T',927);
INSERT INTO `RackSpace` VALUES (970,11,'interior','T',927);
INSERT INTO `RackSpace` VALUES (970,11,'rear','T',927);
INSERT INTO `RackSpace` VALUES (970,10,'interior','T',927);
INSERT INTO `RackSpace` VALUES (970,10,'rear','T',927);
INSERT INTO `RackSpace` VALUES (970,2,'front','T',944);
INSERT INTO `RackSpace` VALUES (970,2,'interior','T',944);
INSERT INTO `RackSpace` VALUES (970,2,'rear','T',944);
INSERT INTO `RackSpace` VALUES (970,1,'front','T',944);
INSERT INTO `RackSpace` VALUES (970,1,'interior','T',944);
INSERT INTO `RackSpace` VALUES (970,1,'rear','T',944);
INSERT INTO `RackSpace` VALUES (971,5,'front','T',942);
INSERT INTO `RackSpace` VALUES (971,5,'interior','T',942);
INSERT INTO `RackSpace` VALUES (971,5,'rear','T',942);
INSERT INTO `RackSpace` VALUES (971,4,'front','T',942);
INSERT INTO `RackSpace` VALUES (971,4,'interior','T',942);
INSERT INTO `RackSpace` VALUES (971,4,'rear','T',942);
INSERT INTO `RackSpace` VALUES (971,3,'front','T',942);
INSERT INTO `RackSpace` VALUES (971,3,'interior','T',942);
INSERT INTO `RackSpace` VALUES (971,3,'rear','T',942);
INSERT INTO `RackSpace` VALUES (971,2,'front','T',942);
INSERT INTO `RackSpace` VALUES (971,2,'interior','T',942);
INSERT INTO `RackSpace` VALUES (971,2,'rear','T',942);
INSERT INTO `RackSpace` VALUES (971,1,'front','T',942);
INSERT INTO `RackSpace` VALUES (971,1,'interior','T',942);
INSERT INTO `RackSpace` VALUES (971,1,'rear','T',942);
INSERT INTO `RackSpace` VALUES (973,5,'front','T',943);
INSERT INTO `RackSpace` VALUES (973,5,'interior','T',943);
INSERT INTO `RackSpace` VALUES (973,5,'rear','T',943);
INSERT INTO `RackSpace` VALUES (973,4,'front','T',943);
INSERT INTO `RackSpace` VALUES (973,4,'interior','T',943);
INSERT INTO `RackSpace` VALUES (973,4,'rear','T',943);
INSERT INTO `RackSpace` VALUES (973,3,'front','T',943);
INSERT INTO `RackSpace` VALUES (973,3,'interior','T',943);
INSERT INTO `RackSpace` VALUES (973,3,'rear','T',943);
INSERT INTO `RackSpace` VALUES (973,2,'front','T',943);
INSERT INTO `RackSpace` VALUES (973,2,'interior','T',943);
INSERT INTO `RackSpace` VALUES (973,2,'rear','T',943);
INSERT INTO `RackSpace` VALUES (973,1,'front','T',943);
INSERT INTO `RackSpace` VALUES (973,1,'interior','T',943);
INSERT INTO `RackSpace` VALUES (973,1,'rear','T',943);
INSERT INTO `RackSpace` VALUES (971,6,'front','U',NULL);
INSERT INTO `RackSpace` VALUES (971,6,'interior','U',NULL);
INSERT INTO `RackSpace` VALUES (971,6,'rear','U',NULL);
INSERT INTO `RackSpace` VALUES (973,6,'front','U',NULL);
INSERT INTO `RackSpace` VALUES (973,6,'interior','U',NULL);
INSERT INTO `RackSpace` VALUES (973,6,'rear','U',NULL);
INSERT INTO `RackSpace` VALUES (973,17,'rear','T',954);
INSERT INTO `RackSpace` VALUES (972,20,'front','T',953);
INSERT INTO `RackSpace` VALUES (968,6,'front','T',952);
INSERT INTO `RackSpace` VALUES (967,1,'rear','T',949);
INSERT INTO `RackSpace` VALUES (973,37,'rear','T',931);
INSERT INTO `RackSpace` VALUES (973,37,'interior','T',931);
INSERT INTO `RackSpace` VALUES (973,40,'rear','T',929);
INSERT INTO `RackSpace` VALUES (973,40,'interior','T',929);
INSERT INTO `RackSpace` VALUES (973,15,'rear','T',941);
INSERT INTO `RackSpace` VALUES (973,15,'interior','T',941);
INSERT INTO `RackSpace` VALUES (973,15,'front','T',941);
INSERT INTO `RackSpace` VALUES (973,13,'front','T',939);
INSERT INTO `RackSpace` VALUES (973,11,'rear','T',937);
INSERT INTO `RackSpace` VALUES (973,11,'interior','T',937);
INSERT INTO `RackSpace` VALUES (973,9,'interior','T',935);
INSERT INTO `RackSpace` VALUES (973,7,'interior','T',933);
INSERT INTO `RackSpace` VALUES (974,16,'rear','T',946);
INSERT INTO `RackSpace` VALUES (971,38,'rear','T',930);
INSERT INTO `RackSpace` VALUES (971,40,'interior','T',928);
INSERT INTO `RackSpace` VALUES (971,15,'rear','T',940);
INSERT INTO `RackSpace` VALUES (971,13,'rear','T',938);
INSERT INTO `RackSpace` VALUES (971,11,'rear','T',936);
INSERT INTO `RackSpace` VALUES (971,9,'rear','T',934);
INSERT INTO `RackSpace` VALUES (971,7,'rear','T',932);
INSERT INTO `RackSpace` VALUES (968,12,'rear','A',NULL);
INSERT INTO `RackSpace` VALUES (968,11,'rear','A',NULL);
INSERT INTO `RackSpace` VALUES (968,10,'rear','A',NULL);
INSERT INTO `RackSpace` VALUES (968,9,'rear','A',NULL);
INSERT INTO `RackSpace` VALUES (968,8,'rear','A',NULL);
INSERT INTO `RackSpace` VALUES (968,7,'rear','A',NULL);
INSERT INTO `RackSpace` VALUES (968,6,'rear','A',NULL);
INSERT INTO `RackSpace` VALUES (968,5,'rear','A',NULL);
INSERT INTO `RackSpace` VALUES (968,4,'rear','A',NULL);
INSERT INTO `RackSpace` VALUES (968,3,'rear','A',NULL);
INSERT INTO `RackSpace` VALUES (968,2,'rear','A',NULL);
INSERT INTO `RackSpace` VALUES (968,1,'rear','A',NULL);
INSERT INTO `RackSpace` VALUES (974,9,'rear','T',955);
INSERT INTO `RackSpace` VALUES (971,17,'rear','T',951);
INSERT INTO `RackSpace` VALUES (970,9,'rear','T',948);
INSERT INTO `RackSpace` VALUES (969,14,'rear','T',950);
INSERT INTO `RackSpace` VALUES (969,2,'rear','T',947);
INSERT INTO `RackSpace` VALUES (973,38,'rear','T',931);
INSERT INTO `RackSpace` VALUES (973,38,'interior','T',931);
INSERT INTO `RackSpace` VALUES (973,41,'rear','T',929);
INSERT INTO `RackSpace` VALUES (973,41,'interior','T',929);
INSERT INTO `RackSpace` VALUES (973,13,'rear','T',939);
INSERT INTO `RackSpace` VALUES (973,13,'interior','T',939);
INSERT INTO `RackSpace` VALUES (973,11,'front','T',937);
INSERT INTO `RackSpace` VALUES (973,9,'rear','T',935);
INSERT INTO `RackSpace` VALUES (973,9,'front','T',935);
INSERT INTO `RackSpace` VALUES (973,7,'rear','T',933);
INSERT INTO `RackSpace` VALUES (973,7,'front','T',933);
INSERT INTO `RackSpace` VALUES (970,13,'rear','T',945);
INSERT INTO `RackSpace` VALUES (971,37,'rear','T',930);
INSERT INTO `RackSpace` VALUES (971,37,'interior','T',930);
INSERT INTO `RackSpace` VALUES (971,38,'interior','T',930);
INSERT INTO `RackSpace` VALUES (971,40,'rear','T',928);
INSERT INTO `RackSpace` VALUES (971,41,'rear','T',928);
INSERT INTO `RackSpace` VALUES (971,41,'interior','T',928);
INSERT INTO `RackSpace` VALUES (971,15,'interior','T',940);
INSERT INTO `RackSpace` VALUES (971,15,'front','T',940);
INSERT INTO `RackSpace` VALUES (971,13,'interior','T',938);
INSERT INTO `RackSpace` VALUES (971,13,'front','T',938);
INSERT INTO `RackSpace` VALUES (971,11,'interior','T',936);
INSERT INTO `RackSpace` VALUES (971,11,'front','T',936);
INSERT INTO `RackSpace` VALUES (971,9,'interior','T',934);
INSERT INTO `RackSpace` VALUES (971,9,'front','T',934);
INSERT INTO `RackSpace` VALUES (971,7,'interior','T',932);
INSERT INTO `RackSpace` VALUES (971,7,'front','T',932);
INSERT INTO `RackSpace` VALUES (968,2,'front','T',917);
INSERT INTO `RackSpace` VALUES (968,2,'interior','T',917);
INSERT INTO `RackSpace` VALUES (968,1,'front','T',917);
INSERT INTO `RackSpace` VALUES (968,1,'interior','T',917);
INSERT INTO `RackSpace` VALUES (972,2,'front','T',956);
INSERT INTO `RackSpace` VALUES (972,2,'interior','T',956);
INSERT INTO `RackSpace` VALUES (972,2,'rear','T',956);
INSERT INTO `RackSpace` VALUES (972,4,'front','T',957);
INSERT INTO `RackSpace` VALUES (972,4,'interior','T',957);
INSERT INTO `RackSpace` VALUES (972,4,'rear','T',957);
INSERT INTO `RackSpace` VALUES (972,6,'front','T',958);
INSERT INTO `RackSpace` VALUES (972,6,'interior','T',958);
INSERT INTO `RackSpace` VALUES (972,6,'rear','T',958);
INSERT INTO `RackSpace` VALUES (972,8,'front','T',959);
INSERT INTO `RackSpace` VALUES (972,8,'interior','T',959);
INSERT INTO `RackSpace` VALUES (972,8,'rear','T',959);
INSERT INTO `RackSpace` VALUES (972,10,'front','T',960);
INSERT INTO `RackSpace` VALUES (972,10,'interior','T',960);
INSERT INTO `RackSpace` VALUES (972,10,'rear','T',960);
INSERT INTO `RackSpace` VALUES (972,35,'interior','T',961);
INSERT INTO `RackSpace` VALUES (972,35,'rear','T',961);
INSERT INTO `RackSpace` VALUES (972,34,'interior','T',962);
INSERT INTO `RackSpace` VALUES (972,34,'rear','T',962);
INSERT INTO `RackSpace` VALUES (967,35,'front','T',981);
INSERT INTO `RackSpace` VALUES (967,35,'interior','T',981);

INSERT INTO `Script` VALUES ('DefaultRSConfig','CONNECT_TIMEOUT=1\nCONNECT_PORT=%RSPORT%\nCHECK_TCP=`TCP_CHECK {\n	connect_port %CONNECT_PORT%\n	connect_timeout %CONNECT_TIMEOUT% \n}\'\n%CHECK%\n');
INSERT INTO `Script` VALUES ('DefaultVSConfig','METHOD=NAT\nlvs_method %METHOD%\n');

INSERT INTO `TagTree` (`id`, `parent_id`, `tag`) VALUES (1,NULL,'testing');
INSERT INTO `TagTree` (`id`, `parent_id`, `tag`) VALUES (2,NULL,'production');
INSERT INTO `TagTree` (`id`, `parent_id`, `tag`) VALUES (10,NULL,'network');
INSERT INTO `TagTree` (`id`, `parent_id`, `tag`) VALUES (5,10,'WAN link');
INSERT INTO `TagTree` (`id`, `parent_id`, `tag`) VALUES (6,NULL,'racks');
INSERT INTO `TagTree` (`id`, `parent_id`, `tag`) VALUES (7,6,'tall racks');
INSERT INTO `TagTree` (`id`, `parent_id`, `tag`) VALUES (8,6,'low racks');
INSERT INTO `TagTree` (`id`, `parent_id`, `tag`) VALUES (9,NULL,'load balancer');
INSERT INTO `TagTree` (`id`, `parent_id`, `tag`) VALUES (11,10,'small network');
INSERT INTO `TagTree` (`id`, `parent_id`, `tag`) VALUES (12,10,'medium network');
INSERT INTO `TagTree` (`id`, `parent_id`, `tag`) VALUES (16,NULL,'XKCD');
INSERT INTO `TagTree` (`id`, `parent_id`, `tag`) VALUES (17,16,'romance');
INSERT INTO `TagTree` (`id`, `parent_id`, `tag`) VALUES (18,16,'sarcasm');
INSERT INTO `TagTree` (`id`, `parent_id`, `tag`) VALUES (19,16,'math');
INSERT INTO `TagTree` (`id`, `parent_id`, `tag`) VALUES (20,16,'language');
INSERT INTO `TagTree` (`id`, `parent_id`, `tag`) VALUES (21,NULL,'vlan template');
INSERT INTO `TagTree` (`id`, `parent_id`, `tag`) VALUES (22,21,'access switch template');
INSERT INTO `TagTree` (`id`, `parent_id`, `tag`) VALUES (23,21,'distribution switch template');

INSERT INTO `TagStorage` (`entity_realm`, `entity_id`, `tag_id`, `user`, `date`) VALUES
('object',905,1,'john_doe','2012-06-01'),
('object',908,2,'john_doe','2012-06-01'),
('object',909,1,'john_doe','2012-06-01'),
('object',928,9,'john_doe','2012-06-01'),
('object',929,9,'john_doe','2012-06-01'),
('object',956,2,'john_doe','2012-06-01'),
('object',957,2,'john_doe','2012-06-01'),
('object',958,2,'john_doe','2012-06-01'),
('object',959,2,'john_doe','2012-06-01'),
('object',960,2,'john_doe','2012-06-01'),
('object',961,2,'john_doe','2012-06-01'),
('object',962,2,'john_doe','2012-06-01'),
('ipv4net',96,12,'john_doe','2012-06-01'),
('ipv4net',97,12,'john_doe','2012-06-01'),
('ipv4net',98,12,'john_doe','2012-06-01'),
('ipv4net',99,12,'john_doe','2012-06-01'),
('ipv4net',102,5,'john_doe','2012-06-01'),
('ipv4net',102,11,'john_doe','2012-06-01'),
('ipv4net',103,5,'john_doe','2012-06-01'),
('ipv4net',103,11,'john_doe','2012-06-01'),
('ipv4net',104,5,'john_doe','2012-06-01'),
('ipv4net',104,11,'john_doe','2012-06-01'),
('ipv4net',105,5,'john_doe','2012-06-01'),
('ipv4net',105,11,'john_doe','2012-06-01'),
('ipv4net',106,12,'john_doe','2012-06-01'),
('ipv4net',107,12,'john_doe','2012-06-01'),
('ipv4net',108,12,'john_doe','2012-06-01'),
('rack',967,7,'john_doe','2012-06-01'),
('rack',968,8,'john_doe','2012-06-01'),
('rack',969,7,'john_doe','2012-06-01'),
('rack',970,8,'john_doe','2012-06-01'),
('rack',971,7,'john_doe','2012-06-01'),
('rack',972,7,'john_doe','2012-06-01'),
('rack',973,7,'john_doe','2012-06-01'),
('rack',974,8,'john_doe','2012-06-01'),
('ipv6net','1','10','john_doe','2012-06-01'),
('ipv6net','2','12','john_doe','2012-06-01'),
('ipv6net','3','12','john_doe','2012-06-01'),
('ipv6net','4','18','john_doe','2012-06-01'),
('ipv6net','5','18','john_doe','2012-06-01'),
('object',979,12,'john_doe','2012-06-01'),
('object',980,12,'john_doe','2012-06-01'),
('object',981,12,'john_doe','2012-06-01'),
('vst',1,22,'john_doe','2012-06-01'),
('vst',2,22,'john_doe','2012-06-01'),
('vst',3,23,'john_doe','2012-06-01');

INSERT INTO `VLANDomain` VALUES
(2,'English'),
(1,'русский'),
(3,'українська'),
(4,'日本語');

INSERT INTO `VLANDescription` VALUES
(1,1,'compulsory','default'),
(1,2,'ondemand','второй'),
(1,3,'ondemand','третий'),
(1,4,'ondemand','четвёртый'),
(1,5,'ondemand','пятый'),
(1,6,'ondemand','шестой'),
(1,7,'ondemand','седьмой'),
(1,8,'ondemand','восьмой'),
(1,9,'ondemand','девятый'),
(1,10,'ondemand','десятый'),
(1,11,'ondemand','одиннадцатый'),
(1,12,'ondemand','двенадцатый'),
(1,13,'ondemand','тринадцатый'),
(1,14,'ondemand','четырнадцатый'),
(1,15,'ondemand','пятнадцатый'),
(2,1,'compulsory','default'),
(2,2,'ondemand','second'),
(2,3,'ondemand','third'),
(2,4,'ondemand','fourth'),
(2,5,'ondemand','fifth'),
(2,6,'ondemand','sixth'),
(2,7,'ondemand','seventh'),
(2,8,'ondemand','eighth'),
(2,9,'ondemand','ninth'),
(2,10,'ondemand','tenth'),
(2,11,'ondemand','eleventh'),
(2,12,'ondemand','twelfth'),
(2,13,'ondemand','thirteenth'),
(2,14,'ondemand','fourteenth'),
(2,15,'ondemand','fifteenth'),
(3,1,'compulsory','default'),
(3,2,'ondemand','другий'),
(3,3,'ondemand','третій'),
(3,4,'ondemand','четвертий'),
(3,5,'ondemand','п\'ятий'),
(3,6,'ondemand','шостий'),
(3,7,'ondemand','сьомий'),
(3,8,'ondemand','восьмий'),
(3,9,'ondemand','дев\'ятий'),
(3,10,'ondemand','десятий'),
(3,11,'ondemand','одинадцятий'),
(3,12,'ondemand','дванадцятий'),
(3,13,'ondemand','тринадцятий'),
(3,14,'ondemand','чотирнадцятий'),
(3,15,'ondemand','п\'ятнадцятий'),
(4,1,'compulsory','default'),
(4,2,'ondemand','第二'),
(4,3,'ondemand','第三'),
(4,4,'ondemand','第四'),
(4,5,'ondemand','第五'),
(4,6,'ondemand','第六'),
(4,7,'ondemand','第七'),
(4,8,'ondemand','第八'),
(4,9,'ondemand','第九'),
(4,10,'ondemand','第十'),
(4,11,'ondemand','第十一'),
(4,12,'ondemand','第十二'),
(4,13,'ondemand','第十三'),
(4,14,'ondemand','第十四'),
(4,15,'ondemand','第十五');

INSERT INTO `VLANSwitchTemplate` VALUES (1,1,'24 ports switch','admin');
INSERT INTO `VLANSwitchTemplate` VALUES (2,1,'48 ports switch','admin');
INSERT INTO `VLANSwitchTemplate` VALUES (3,1,'distribution switch','admin');

INSERT INTO `VLANSTRule` VALUES (1,100,'#[^\\d]24#','uplink','','uplink');
INSERT INTO `VLANSTRule` VALUES (1,999,'#.#','anymode','','user-defined');
INSERT INTO `VLANSTRule` VALUES (2,100,'#[^\\d]48#','uplink','','uplink');
INSERT INTO `VLANSTRule` VALUES (2,999,'#.#','anymode','','user-defined');
INSERT INTO `VLANSTRule` VALUES (3,999,'/./','downlink','','access switches');

INSERT INTO `VLANSwitch` VALUES (979,2,1,4,'no',0,'2012-09-09 16:11:57','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00');
INSERT INTO `VLANSwitch` VALUES (980,2,2,4,'no',0,'2012-09-09 16:16:22','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00');
INSERT INTO `VLANSwitch` VALUES (981,2,3,1,'no',0,'2012-09-09 16:16:53','0000-00-00 00:00:00','0000-00-00 00:00:00','0000-00-00 00:00:00');
