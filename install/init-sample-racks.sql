set names 'utf8';

INSERT INTO `RackObject` (id, name, label, barcode, objtype_id, asset_no, has_problems, comment) VALUES 
(905,'london router','bbrtr1',NULL,7,'net247','no',''),
(906,'londonswitch1','',NULL,8,NULL,'no',''),
(907,'New-York router 1','bbrtr2a',NULL,7,'net55','no',''),
(908,'moscow router','bbrtr3',NULL,7,NULL,'no',NULL),
(909,'tokyo router','bbrtr4',NULL,7,NULL,'no',NULL),
(910,'London server 1','lserver01',NULL,4,'srv500','no',''),
(911,'London server 2','lserver02',NULL,4,'srv501','no',''),
(912,'London server 3','lserver03',NULL,4,'srv502','no',''),
(913,'London server 4','lserver04',NULL,4,'srv503','yes','this one needs replacement'),
(914,'London server 5','lserver05',NULL,4,'srv504','no',''),
(915,'london LB','llb',NULL,8,'net1000','no',''),
(916,'shared storage','',NULL,5,NULL,'no',''),
(917,'london-NAS','',NULL,7,'net1001','no',''),
(918,'London server 6','lserver06',NULL,4,'srv505','no',NULL),
(919,'London server 7','lserver07',NULL,4,'srv506','no',NULL),
(920,'backup library','lbackup',NULL,6,'misc200','no',NULL),
(921,'lserver06 array','lserver06 array',NULL,5,NULL,'no',NULL),
(922,'lserver07 array','lserver07 array',NULL,5,NULL,'no',NULL),
(923,'Tokyo server 1','tserver01',NULL,4,'srv654','no',NULL),
(924,'Tokyo server 2','tserver02',NULL,4,'srv848','no',NULL),
(925,'Tokyo server 3','tserver03',NULL,4,'srv139','no',NULL),
(926,'Tokyo switch','tswitch',NULL,8,'net385','no',NULL),
(927,'New-York router 2','bbrtr2b',NULL,7,'net498','no',NULL),
(928,'New-York IPVS LB A','nylba',NULL,4,'net554','no',NULL),
(929,'New-York IPVS LB B','nylbb',NULL,4,'net555','no',NULL),
(930,'New-York server switch A','nyswitcha',NULL,8,'net084','no',NULL),
(931,'New-York server switch B','nyswitchb',NULL,8,'net486','no',NULL),
(932,'New-York server 1A','nysrv1a',NULL,4,'srv287','no',NULL),
(933,'New-York server 1B','nysrv1b',NULL,4,'srv288','no',NULL),
(934,'New-York server 2A','nysrv2a',NULL,4,NULL,'no',NULL),
(935,'New-York server 2B','nysrv2b',NULL,4,NULL,'no',NULL),
(936,'New-York server 3A','nysrv3a',NULL,4,NULL,'no',NULL),
(937,'New-York server 3B','nysrv3b',NULL,4,NULL,'no',NULL),
(938,'New-York server 4A','nysrv4a',NULL,4,NULL,'no',NULL),
(939,'New-York server 4B','nysrv4b',NULL,4,NULL,'no',NULL),
(940,'New-York server 5A','nysrv5a',NULL,4,NULL,'no',NULL),
(941,'New-York server 5B','nysrv5b',NULL,4,NULL,'no',NULL),
(942,'wing A UPS','',NULL,12,NULL,'no',NULL),
(943,'wing B UPS','',NULL,12,NULL,'no',NULL),
(944,'network UPS','',NULL,12,NULL,'no',NULL),
(945,NULL,'',NULL,9,NULL,'no',NULL),
(946,NULL,'',NULL,9,NULL,'no',NULL),
(947,NULL,'',NULL,2,NULL,'no',NULL),
(948,NULL,'',NULL,2,NULL,'no',NULL),
(949,NULL,'',NULL,2,NULL,'no',NULL),
(950,NULL,'',NULL,2,NULL,'no',NULL),
(951,NULL,'',NULL,2,NULL,'no',NULL),
(952,NULL,'',NULL,2,NULL,'no',NULL),
(953,NULL,'',NULL,2,NULL,'no',NULL),
(954,NULL,'',NULL,2,NULL,'no',NULL),
(955,NULL,'',NULL,2,NULL,'no',NULL),
(956,'mps1','','200161',4,NULL,'no',NULL),
(957,'mps2','','200283',4,NULL,'no',NULL),
(958,'mps3','','200881',4,NULL,'no',NULL),
(959,'mps4','','200458',4,NULL,'no',NULL),
(960,'mps5','','200112',4,NULL,'no',NULL),
(961,'mskswitch','',NULL,8,'sw0001','no',NULL),
(962,'moscow kvm switch','','300221',445,'sw0002','no',NULL);

INSERT INTO `AttributeValue` VALUES (905,2,NULL,269,NULL);
INSERT INTO `AttributeValue` VALUES (906,2,NULL,165,NULL);
INSERT INTO `AttributeValue` VALUES (915,2,NULL,118,NULL);
INSERT INTO `AttributeValue` VALUES (915,4,NULL,245,NULL);
INSERT INTO `AttributeValue` VALUES (917,2,NULL,267,NULL);
INSERT INTO `AttributeValue` VALUES (916,2,NULL,320,NULL);
INSERT INTO `AttributeValue` VALUES (910,2,NULL,57,NULL);
INSERT INTO `AttributeValue` VALUES (910,4,NULL,220,NULL);
INSERT INTO `AttributeValue` VALUES (911,4,NULL,220,NULL);
INSERT INTO `AttributeValue` VALUES (911,2,NULL,57,NULL);
INSERT INTO `AttributeValue` VALUES (912,4,NULL,220,NULL);
INSERT INTO `AttributeValue` VALUES (912,2,NULL,57,NULL);
INSERT INTO `AttributeValue` VALUES (913,2,NULL,57,NULL);
INSERT INTO `AttributeValue` VALUES (913,4,NULL,220,NULL);
INSERT INTO `AttributeValue` VALUES (914,2,NULL,57,NULL);
INSERT INTO `AttributeValue` VALUES (914,4,NULL,220,NULL);
INSERT INTO `AttributeValue` VALUES (918,2,NULL,88,NULL);
INSERT INTO `AttributeValue` VALUES (918,4,NULL,220,NULL);
INSERT INTO `AttributeValue` VALUES (919,2,NULL,88,NULL);
INSERT INTO `AttributeValue` VALUES (919,4,NULL,220,NULL);
INSERT INTO `AttributeValue` VALUES (920,2,NULL,326,NULL);
INSERT INTO `AttributeValue` VALUES (921,2,NULL,318,NULL);
INSERT INTO `AttributeValue` VALUES (922,2,NULL,318,NULL);
INSERT INTO `AttributeValue` VALUES (923,2,NULL,102,NULL);
INSERT INTO `AttributeValue` VALUES (923,4,NULL,233,NULL);
INSERT INTO `AttributeValue` VALUES (926,2,NULL,126,NULL);
INSERT INTO `AttributeValue` VALUES (926,4,NULL,244,NULL);
INSERT INTO `AttributeValue` VALUES (925,2,NULL,102,NULL);
INSERT INTO `AttributeValue` VALUES (925,4,NULL,233,NULL);
INSERT INTO `AttributeValue` VALUES (924,2,NULL,102,NULL);
INSERT INTO `AttributeValue` VALUES (924,4,NULL,233,NULL);
INSERT INTO `AttributeValue` VALUES (907,2,NULL,269,NULL);
INSERT INTO `AttributeValue` VALUES (907,4,NULL,258,NULL);
INSERT INTO `AttributeValue` VALUES (927,2,NULL,269,NULL);
INSERT INTO `AttributeValue` VALUES (927,4,NULL,258,NULL);
INSERT INTO `AttributeValue` VALUES (956,2,NULL,62,NULL);
INSERT INTO `AttributeValue` VALUES (956,4,NULL,791,NULL);
INSERT INTO `AttributeValue` VALUES (957,2,NULL,62,NULL);
INSERT INTO `AttributeValue` VALUES (957,4,NULL,791,NULL);
INSERT INTO `AttributeValue` VALUES (958,2,NULL,62,NULL);
INSERT INTO `AttributeValue` VALUES (958,4,NULL,791,NULL);
INSERT INTO `AttributeValue` VALUES (959,2,NULL,62,NULL);
INSERT INTO `AttributeValue` VALUES (959,4,NULL,791,NULL);
INSERT INTO `AttributeValue` VALUES (960,2,NULL,62,NULL);
INSERT INTO `AttributeValue` VALUES (960,4,NULL,791,NULL);
INSERT INTO `AttributeValue` VALUES (961,2,NULL,755,NULL);
INSERT INTO `AttributeValue` VALUES (962,2,NULL,470,NULL);

INSERT INTO `RackRow` (`id`, `name`) VALUES (50000,'Moscow');
INSERT INTO `RackRow` (`id`, `name`) VALUES (50001,'Tokyo');
INSERT INTO `RackRow` (`id`, `name`) VALUES (50002,'New-York');
INSERT INTO `RackRow` (`id`, `name`) VALUES (50003,'London');

INSERT INTO `IPv4Address` VALUES (180879678,'default gw','no');
INSERT INTO `IPv4Address` VALUES (180879617,'',NULL);
INSERT INTO `IPv4Address` VALUES (180879680,'network','yes');
INSERT INTO `IPv4Address` VALUES (180879743,'broadcast','yes');
INSERT INTO `IPv4Address` VALUES (180879681,'',NULL);
INSERT INTO `IPv4Address` VALUES (180879683,'',NULL);
INSERT INTO `IPv4Address` VALUES (180879684,'',NULL);
INSERT INTO `IPv4Address` VALUES (180879685,'',NULL);
INSERT INTO `IPv4Address` VALUES (180879616,'network','yes');
INSERT INTO `IPv4Address` VALUES (180879679,'broadcast','yes');
INSERT INTO `IPv4Address` VALUES (180879686,'',NULL);
INSERT INTO `IPv4Address` VALUES (180879687,'',NULL);
INSERT INTO `IPv4Address` VALUES (180879360,'',NULL);
INSERT INTO `IPv4Address` VALUES (180879361,'',NULL);
INSERT INTO `IPv4Address` VALUES (180880192,'network','yes');
INSERT INTO `IPv4Address` VALUES (180880255,'broadcast','yes');
INSERT INTO `IPv4Address` VALUES (180880448,'network','yes');
INSERT INTO `IPv4Address` VALUES (180880511,'broadcast','yes');
INSERT INTO `IPv4Address` VALUES (180880384,'network','yes');
INSERT INTO `IPv4Address` VALUES (180880447,'broadcast','yes');
INSERT INTO `IPv4Address` VALUES (180880128,'network','no');
INSERT INTO `IPv4Address` VALUES (180880191,'broadcast','no');
INSERT INTO `IPv4Address` VALUES (180879936,'network','yes');
INSERT INTO `IPv4Address` VALUES (180879999,'broadcast','yes');
INSERT INTO `IPv4Address` VALUES (180879872,'network','yes');
INSERT INTO `IPv4Address` VALUES (180879935,'broadcast','yes');
INSERT INTO `IPv4Address` VALUES (180880193,'',NULL);
INSERT INTO `IPv4Address` VALUES (180880194,'',NULL);
INSERT INTO `IPv4Address` VALUES (180880195,'',NULL);
INSERT INTO `IPv4Address` VALUES (180879618,'',NULL);
INSERT INTO `IPv4Address` VALUES (180879677,'',NULL);
INSERT INTO `IPv4Address` VALUES (180879676,'',NULL);
INSERT INTO `IPv4Address` VALUES (180879675,'',NULL);
INSERT INTO `IPv4Address` VALUES (180880254,'',NULL);
INSERT INTO `IPv4Address` VALUES (180880504,'for field engineer','yes');

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

INSERT INTO `IPv4RSPool` VALUES (1,'Apache servers',NULL,NULL);
INSERT INTO `IPv4RSPool` VALUES (2,'Resin servers',NULL,NULL);

INSERT INTO `IPv4VS` VALUES (1,180879877,80,'TCP','virtual web','lvs_sched wlc\r\nlvs_method NAT\r\ndelay_loop 3\r\nalpha\r\nomega\r\nquorum 3\r\nhysteresis 1\r\n\r\n# this is a comment\r\n# VS name is %VNAME%\r\n#\r\n','HTTP_GET {\r\nurl {\r\npath /\r\nstatus_code 200\r\n}\r\nconnect_timeout 1\r\n}');
INSERT INTO `IPv4VS` VALUES (2,180879878,80,'TCP','virtual app','lvs_sched wlc\r\nlvs_method NAT\r\ndelay_loop 3\r\nalpha\r\nomega\r\nquorum 3\r\nhysteresis 1\r\n\r\n','HTTP_GET {\r\nurl {\r\npath /\r\nstatus_code 200\r\n}\r\nconnect_timeout 1\r\n}');

INSERT INTO `IPv4LB` VALUES (928,1,1,NULL,NULL);
INSERT INTO `IPv4LB` VALUES (929,1,1,NULL,NULL);
INSERT INTO `IPv4LB` VALUES (929,2,2,NULL,NULL);

INSERT INTO `IPv4RS` VALUES (1,'yes',180879973,80,1,NULL);
INSERT INTO `IPv4RS` VALUES (2,'yes',180879974,80,1,NULL);
INSERT INTO `IPv4RS` VALUES (3,'no',180879975,80,1,NULL);
INSERT INTO `IPv4RS` VALUES (4,'yes',180879976,80,1,NULL);
INSERT INTO `IPv4RS` VALUES (5,'yes',180879977,80,1,NULL);
INSERT INTO `IPv4RS` VALUES (6,'no',180879978,8080,2,NULL);
INSERT INTO `IPv4RS` VALUES (7,'yes',180879979,8080,2,NULL);
INSERT INTO `IPv4RS` VALUES (8,'yes',180879980,8080,2,NULL);
INSERT INTO `IPv4RS` VALUES (9,'yes',180879981,8080,2,NULL);
INSERT INTO `IPv4RS` VALUES (10,'yes',180879982,8080,2,NULL);

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

INSERT INTO `Link` VALUES (3057,3071);
INSERT INTO `Link` VALUES (3058,3084);
INSERT INTO `Link` VALUES (3059,3069);
INSERT INTO `Link` VALUES (3062,3094);
INSERT INTO `Link` VALUES (3063,3079);
INSERT INTO `Link` VALUES (3070,3083);
INSERT INTO `Link` VALUES (3072,3080);
INSERT INTO `Link` VALUES (3073,3086);
INSERT INTO `Link` VALUES (3074,3088);
INSERT INTO `Link` VALUES (3075,3090);
INSERT INTO `Link` VALUES (3076,3092);
INSERT INTO `Link` VALUES (3077,3097);
INSERT INTO `Link` VALUES (3078,3095);
INSERT INTO `Link` VALUES (3082,3085);
INSERT INTO `Link` VALUES (3099,3112);
INSERT INTO `Link` VALUES (3101,3113);
INSERT INTO `Link` VALUES (3102,3114);
INSERT INTO `Link` VALUES (3103,3115);
INSERT INTO `Link` VALUES (3104,3116);
INSERT INTO `Link` VALUES (3105,3117);
INSERT INTO `Link` VALUES (3106,3118);
INSERT INTO `Link` VALUES (3142,3160);
INSERT INTO `Link` VALUES (3159,3161);
INSERT INTO `Link` VALUES (3134,3143);
INSERT INTO `Link` VALUES (3119,3147);
INSERT INTO `Link` VALUES (3131,3148);
INSERT INTO `Link` VALUES (3137,3151);
INSERT INTO `Link` VALUES (3140,3154);
INSERT INTO `Link` VALUES (3128,3158);
INSERT INTO `Link` VALUES (3127,3162);
INSERT INTO `Link` VALUES (3130,3163);
INSERT INTO `Link` VALUES (3133,3164);
INSERT INTO `Link` VALUES (3136,3165);
INSERT INTO `Link` VALUES (3139,3168);
INSERT INTO `Link` VALUES (3145,3171);
INSERT INTO `Link` VALUES (3124,3126);
INSERT INTO `Link` VALUES (3181,3186);
INSERT INTO `Link` VALUES (3182,3187);
INSERT INTO `Link` VALUES (3183,3190);
INSERT INTO `Link` VALUES (3184,3188);
INSERT INTO `Link` VALUES (3185,3189);

INSERT INTO `IPv4NAT` (`object_id`, `proto`, `localip`, `localport`, `remoteip`, `remoteport`, `description`) VALUES (915,'TCP',180879675,80,180879681,80,'');
INSERT INTO `IPv4NAT` (`object_id`, `proto`, `localip`, `localport`, `remoteip`, `remoteport`, `description`) VALUES (915,'TCP',180879675,80,180879682,80,'');
INSERT INTO `IPv4NAT` (`object_id`, `proto`, `localip`, `localport`, `remoteip`, `remoteport`, `description`) VALUES (915,'TCP',180879675,80,180879683,80,'');
INSERT INTO `IPv4NAT` (`object_id`, `proto`, `localip`, `localport`, `remoteip`, `remoteport`, `description`) VALUES (915,'UDP',180879676,53,180879684,53,'');
INSERT INTO `IPv4NAT` (`object_id`, `proto`, `localip`, `localport`, `remoteip`, `remoteport`, `description`) VALUES (915,'UDP',180879676,53,180879685,53,'');
INSERT INTO `IPv4NAT` (`object_id`, `proto`, `localip`, `localport`, `remoteip`, `remoteport`, `description`) VALUES (915,'TCP',180879677,443,180879686,443,'');
INSERT INTO `IPv4NAT` (`object_id`, `proto`, `localip`, `localport`, `remoteip`, `remoteport`, `description`) VALUES (915,'TCP',180879677,443,180879687,443,'');

INSERT INTO `Rack` (id, name, row_id, height, comment) VALUES
(40,'L01',50003,42,'test'),
(41,'L02',50003,12,'network equipment mini-rack'),
(42,'L03',50003,42,''),
(43,'NY100',50002,16,''),
(44,'NY101',50002,42,'server farm wing A'),
(45,'M01',50000,42,''),
(46,'NY102',50002,42,'server farm wing B'),
(47,'T01',50001,16,'');

INSERT INTO `RackSpace` VALUES (41,9,'interior','T',905);
INSERT INTO `RackSpace` VALUES (41,9,'front','T',905);
INSERT INTO `RackSpace` VALUES (41,10,'interior','T',905);
INSERT INTO `RackSpace` VALUES (41,10,'front','T',905);
INSERT INTO `RackSpace` VALUES (41,11,'interior','T',905);
INSERT INTO `RackSpace` VALUES (41,11,'front','T',905);
INSERT INTO `RackSpace` VALUES (41,7,'front','T',906);
INSERT INTO `RackSpace` VALUES (41,7,'interior','T',906);
INSERT INTO `RackSpace` VALUES (43,16,'interior','T',907);
INSERT INTO `RackSpace` VALUES (43,16,'rear','T',907);
INSERT INTO `RackSpace` VALUES (43,15,'interior','T',907);
INSERT INTO `RackSpace` VALUES (43,15,'rear','T',907);
INSERT INTO `RackSpace` VALUES (43,14,'interior','T',907);
INSERT INTO `RackSpace` VALUES (43,14,'rear','T',907);
INSERT INTO `RackSpace` VALUES (47,15,'interior','T',909);
INSERT INTO `RackSpace` VALUES (47,15,'rear','T',909);
INSERT INTO `RackSpace` VALUES (47,14,'interior','T',909);
INSERT INTO `RackSpace` VALUES (47,14,'rear','T',909);
INSERT INTO `RackSpace` VALUES (47,13,'interior','T',909);
INSERT INTO `RackSpace` VALUES (47,13,'rear','T',909);
INSERT INTO `RackSpace` VALUES (45,20,'interior','T',908);
INSERT INTO `RackSpace` VALUES (45,20,'rear','T',908);
INSERT INTO `RackSpace` VALUES (45,19,'interior','T',908);
INSERT INTO `RackSpace` VALUES (45,19,'rear','T',908);
INSERT INTO `RackSpace` VALUES (45,18,'interior','T',908);
INSERT INTO `RackSpace` VALUES (45,18,'rear','T',908);
INSERT INTO `RackSpace` VALUES (40,2,'front','T',910);
INSERT INTO `RackSpace` VALUES (40,2,'interior','T',910);
INSERT INTO `RackSpace` VALUES (40,2,'rear','T',910);
INSERT INTO `RackSpace` VALUES (40,4,'front','T',911);
INSERT INTO `RackSpace` VALUES (40,4,'interior','T',911);
INSERT INTO `RackSpace` VALUES (40,4,'rear','T',911);
INSERT INTO `RackSpace` VALUES (40,6,'front','T',912);
INSERT INTO `RackSpace` VALUES (40,6,'interior','T',912);
INSERT INTO `RackSpace` VALUES (40,6,'rear','T',912);
INSERT INTO `RackSpace` VALUES (40,8,'front','T',913);
INSERT INTO `RackSpace` VALUES (40,8,'interior','T',913);
INSERT INTO `RackSpace` VALUES (40,8,'rear','T',913);
INSERT INTO `RackSpace` VALUES (40,10,'front','T',914);
INSERT INTO `RackSpace` VALUES (40,10,'interior','T',914);
INSERT INTO `RackSpace` VALUES (40,10,'rear','T',914);
INSERT INTO `RackSpace` VALUES (40,23,'rear','T',916);
INSERT INTO `RackSpace` VALUES (40,23,'interior','T',916);
INSERT INTO `RackSpace` VALUES (40,23,'front','T',916);
INSERT INTO `RackSpace` VALUES (41,5,'front','T',915);
INSERT INTO `RackSpace` VALUES (41,5,'interior','T',915);
INSERT INTO `RackSpace` VALUES (41,4,'interior','T',915);
INSERT INTO `RackSpace` VALUES (41,4,'front','T',915);
INSERT INTO `RackSpace` VALUES (40,22,'front','T',916);
INSERT INTO `RackSpace` VALUES (40,22,'interior','T',916);
INSERT INTO `RackSpace` VALUES (40,22,'rear','T',916);
INSERT INTO `RackSpace` VALUES (42,7,'front','T',918);
INSERT INTO `RackSpace` VALUES (42,7,'interior','T',918);
INSERT INTO `RackSpace` VALUES (42,7,'rear','T',918);
INSERT INTO `RackSpace` VALUES (42,6,'front','T',918);
INSERT INTO `RackSpace` VALUES (42,6,'interior','T',918);
INSERT INTO `RackSpace` VALUES (42,6,'rear','T',918);
INSERT INTO `RackSpace` VALUES (42,5,'front','T',918);
INSERT INTO `RackSpace` VALUES (42,5,'interior','T',918);
INSERT INTO `RackSpace` VALUES (42,5,'rear','T',918);
INSERT INTO `RackSpace` VALUES (42,4,'front','T',918);
INSERT INTO `RackSpace` VALUES (42,4,'interior','T',918);
INSERT INTO `RackSpace` VALUES (42,4,'rear','T',918);
INSERT INTO `RackSpace` VALUES (42,3,'front','T',918);
INSERT INTO `RackSpace` VALUES (42,3,'interior','T',918);
INSERT INTO `RackSpace` VALUES (42,3,'rear','T',918);
INSERT INTO `RackSpace` VALUES (42,13,'front','T',919);
INSERT INTO `RackSpace` VALUES (42,13,'interior','T',919);
INSERT INTO `RackSpace` VALUES (42,13,'rear','T',919);
INSERT INTO `RackSpace` VALUES (42,12,'front','T',919);
INSERT INTO `RackSpace` VALUES (42,12,'interior','T',919);
INSERT INTO `RackSpace` VALUES (42,12,'rear','T',919);
INSERT INTO `RackSpace` VALUES (42,11,'front','T',919);
INSERT INTO `RackSpace` VALUES (42,11,'interior','T',919);
INSERT INTO `RackSpace` VALUES (42,11,'rear','T',919);
INSERT INTO `RackSpace` VALUES (42,10,'front','T',919);
INSERT INTO `RackSpace` VALUES (42,10,'interior','T',919);
INSERT INTO `RackSpace` VALUES (42,10,'rear','T',919);
INSERT INTO `RackSpace` VALUES (42,9,'front','T',919);
INSERT INTO `RackSpace` VALUES (42,9,'interior','T',919);
INSERT INTO `RackSpace` VALUES (42,9,'rear','T',919);
INSERT INTO `RackSpace` VALUES (42,37,'front','T',920);
INSERT INTO `RackSpace` VALUES (42,37,'interior','T',920);
INSERT INTO `RackSpace` VALUES (42,37,'rear','T',920);
INSERT INTO `RackSpace` VALUES (42,36,'front','T',920);
INSERT INTO `RackSpace` VALUES (42,36,'interior','T',920);
INSERT INTO `RackSpace` VALUES (42,36,'rear','T',920);
INSERT INTO `RackSpace` VALUES (42,35,'front','T',920);
INSERT INTO `RackSpace` VALUES (42,35,'interior','T',920);
INSERT INTO `RackSpace` VALUES (42,35,'rear','T',920);
INSERT INTO `RackSpace` VALUES (42,34,'front','T',920);
INSERT INTO `RackSpace` VALUES (42,34,'interior','T',920);
INSERT INTO `RackSpace` VALUES (42,34,'rear','T',920);
INSERT INTO `RackSpace` VALUES (42,33,'front','T',920);
INSERT INTO `RackSpace` VALUES (42,33,'interior','T',920);
INSERT INTO `RackSpace` VALUES (42,33,'rear','T',920);
INSERT INTO `RackSpace` VALUES (42,32,'front','T',920);
INSERT INTO `RackSpace` VALUES (42,32,'interior','T',920);
INSERT INTO `RackSpace` VALUES (42,32,'rear','T',920);
INSERT INTO `RackSpace` VALUES (42,31,'front','T',920);
INSERT INTO `RackSpace` VALUES (42,31,'interior','T',920);
INSERT INTO `RackSpace` VALUES (42,31,'rear','T',920);
INSERT INTO `RackSpace` VALUES (42,30,'front','T',920);
INSERT INTO `RackSpace` VALUES (42,30,'interior','T',920);
INSERT INTO `RackSpace` VALUES (42,30,'rear','T',920);
INSERT INTO `RackSpace` VALUES (42,16,'front','T',921);
INSERT INTO `RackSpace` VALUES (42,16,'interior','T',921);
INSERT INTO `RackSpace` VALUES (42,16,'rear','T',921);
INSERT INTO `RackSpace` VALUES (42,15,'front','T',921);
INSERT INTO `RackSpace` VALUES (42,15,'interior','T',921);
INSERT INTO `RackSpace` VALUES (42,15,'rear','T',921);
INSERT INTO `RackSpace` VALUES (42,18,'front','T',922);
INSERT INTO `RackSpace` VALUES (42,18,'interior','T',922);
INSERT INTO `RackSpace` VALUES (42,18,'rear','T',922);
INSERT INTO `RackSpace` VALUES (42,17,'front','T',922);
INSERT INTO `RackSpace` VALUES (42,17,'interior','T',922);
INSERT INTO `RackSpace` VALUES (42,17,'rear','T',922);
INSERT INTO `RackSpace` VALUES (47,11,'interior','T',926);
INSERT INTO `RackSpace` VALUES (47,11,'rear','T',926);
INSERT INTO `RackSpace` VALUES (47,1,'front','T',923);
INSERT INTO `RackSpace` VALUES (47,1,'interior','T',923);
INSERT INTO `RackSpace` VALUES (47,1,'rear','T',923);
INSERT INTO `RackSpace` VALUES (47,3,'front','T',924);
INSERT INTO `RackSpace` VALUES (47,3,'interior','T',924);
INSERT INTO `RackSpace` VALUES (47,3,'rear','T',924);
INSERT INTO `RackSpace` VALUES (47,5,'front','T',925);
INSERT INTO `RackSpace` VALUES (47,5,'interior','T',925);
INSERT INTO `RackSpace` VALUES (47,5,'rear','T',925);
INSERT INTO `RackSpace` VALUES (43,12,'interior','T',927);
INSERT INTO `RackSpace` VALUES (43,12,'rear','T',927);
INSERT INTO `RackSpace` VALUES (43,11,'interior','T',927);
INSERT INTO `RackSpace` VALUES (43,11,'rear','T',927);
INSERT INTO `RackSpace` VALUES (43,10,'interior','T',927);
INSERT INTO `RackSpace` VALUES (43,10,'rear','T',927);
INSERT INTO `RackSpace` VALUES (43,2,'front','T',944);
INSERT INTO `RackSpace` VALUES (43,2,'interior','T',944);
INSERT INTO `RackSpace` VALUES (43,2,'rear','T',944);
INSERT INTO `RackSpace` VALUES (43,1,'front','T',944);
INSERT INTO `RackSpace` VALUES (43,1,'interior','T',944);
INSERT INTO `RackSpace` VALUES (43,1,'rear','T',944);
INSERT INTO `RackSpace` VALUES (44,5,'front','T',942);
INSERT INTO `RackSpace` VALUES (44,5,'interior','T',942);
INSERT INTO `RackSpace` VALUES (44,5,'rear','T',942);
INSERT INTO `RackSpace` VALUES (44,4,'front','T',942);
INSERT INTO `RackSpace` VALUES (44,4,'interior','T',942);
INSERT INTO `RackSpace` VALUES (44,4,'rear','T',942);
INSERT INTO `RackSpace` VALUES (44,3,'front','T',942);
INSERT INTO `RackSpace` VALUES (44,3,'interior','T',942);
INSERT INTO `RackSpace` VALUES (44,3,'rear','T',942);
INSERT INTO `RackSpace` VALUES (44,2,'front','T',942);
INSERT INTO `RackSpace` VALUES (44,2,'interior','T',942);
INSERT INTO `RackSpace` VALUES (44,2,'rear','T',942);
INSERT INTO `RackSpace` VALUES (44,1,'front','T',942);
INSERT INTO `RackSpace` VALUES (44,1,'interior','T',942);
INSERT INTO `RackSpace` VALUES (44,1,'rear','T',942);
INSERT INTO `RackSpace` VALUES (46,5,'front','T',943);
INSERT INTO `RackSpace` VALUES (46,5,'interior','T',943);
INSERT INTO `RackSpace` VALUES (46,5,'rear','T',943);
INSERT INTO `RackSpace` VALUES (46,4,'front','T',943);
INSERT INTO `RackSpace` VALUES (46,4,'interior','T',943);
INSERT INTO `RackSpace` VALUES (46,4,'rear','T',943);
INSERT INTO `RackSpace` VALUES (46,3,'front','T',943);
INSERT INTO `RackSpace` VALUES (46,3,'interior','T',943);
INSERT INTO `RackSpace` VALUES (46,3,'rear','T',943);
INSERT INTO `RackSpace` VALUES (46,2,'front','T',943);
INSERT INTO `RackSpace` VALUES (46,2,'interior','T',943);
INSERT INTO `RackSpace` VALUES (46,2,'rear','T',943);
INSERT INTO `RackSpace` VALUES (46,1,'front','T',943);
INSERT INTO `RackSpace` VALUES (46,1,'interior','T',943);
INSERT INTO `RackSpace` VALUES (46,1,'rear','T',943);
INSERT INTO `RackSpace` VALUES (44,6,'front','U',NULL);
INSERT INTO `RackSpace` VALUES (44,6,'interior','U',NULL);
INSERT INTO `RackSpace` VALUES (44,6,'rear','U',NULL);
INSERT INTO `RackSpace` VALUES (46,6,'front','U',NULL);
INSERT INTO `RackSpace` VALUES (46,6,'interior','U',NULL);
INSERT INTO `RackSpace` VALUES (46,6,'rear','U',NULL);
INSERT INTO `RackSpace` VALUES (46,17,'rear','T',954);
INSERT INTO `RackSpace` VALUES (45,20,'front','T',953);
INSERT INTO `RackSpace` VALUES (41,6,'front','T',952);
INSERT INTO `RackSpace` VALUES (40,1,'rear','T',949);
INSERT INTO `RackSpace` VALUES (46,37,'rear','T',931);
INSERT INTO `RackSpace` VALUES (46,37,'interior','T',931);
INSERT INTO `RackSpace` VALUES (46,40,'rear','T',929);
INSERT INTO `RackSpace` VALUES (46,40,'interior','T',929);
INSERT INTO `RackSpace` VALUES (46,15,'rear','T',941);
INSERT INTO `RackSpace` VALUES (46,15,'interior','T',941);
INSERT INTO `RackSpace` VALUES (46,15,'front','T',941);
INSERT INTO `RackSpace` VALUES (46,13,'front','T',939);
INSERT INTO `RackSpace` VALUES (46,11,'rear','T',937);
INSERT INTO `RackSpace` VALUES (46,11,'interior','T',937);
INSERT INTO `RackSpace` VALUES (46,9,'interior','T',935);
INSERT INTO `RackSpace` VALUES (46,7,'interior','T',933);
INSERT INTO `RackSpace` VALUES (47,16,'rear','T',946);
INSERT INTO `RackSpace` VALUES (44,38,'rear','T',930);
INSERT INTO `RackSpace` VALUES (44,40,'interior','T',928);
INSERT INTO `RackSpace` VALUES (44,15,'rear','T',940);
INSERT INTO `RackSpace` VALUES (44,13,'rear','T',938);
INSERT INTO `RackSpace` VALUES (44,11,'rear','T',936);
INSERT INTO `RackSpace` VALUES (44,9,'rear','T',934);
INSERT INTO `RackSpace` VALUES (44,7,'rear','T',932);
INSERT INTO `RackSpace` VALUES (41,12,'rear','A',NULL);
INSERT INTO `RackSpace` VALUES (41,11,'rear','A',NULL);
INSERT INTO `RackSpace` VALUES (41,10,'rear','A',NULL);
INSERT INTO `RackSpace` VALUES (41,9,'rear','A',NULL);
INSERT INTO `RackSpace` VALUES (41,8,'rear','A',NULL);
INSERT INTO `RackSpace` VALUES (41,7,'rear','A',NULL);
INSERT INTO `RackSpace` VALUES (41,6,'rear','A',NULL);
INSERT INTO `RackSpace` VALUES (41,5,'rear','A',NULL);
INSERT INTO `RackSpace` VALUES (41,4,'rear','A',NULL);
INSERT INTO `RackSpace` VALUES (41,3,'rear','A',NULL);
INSERT INTO `RackSpace` VALUES (41,2,'rear','A',NULL);
INSERT INTO `RackSpace` VALUES (41,1,'rear','A',NULL);
INSERT INTO `RackSpace` VALUES (47,9,'rear','T',955);
INSERT INTO `RackSpace` VALUES (44,17,'rear','T',951);
INSERT INTO `RackSpace` VALUES (43,9,'rear','T',948);
INSERT INTO `RackSpace` VALUES (42,14,'rear','T',950);
INSERT INTO `RackSpace` VALUES (42,2,'rear','T',947);
INSERT INTO `RackSpace` VALUES (46,38,'rear','T',931);
INSERT INTO `RackSpace` VALUES (46,38,'interior','T',931);
INSERT INTO `RackSpace` VALUES (46,41,'rear','T',929);
INSERT INTO `RackSpace` VALUES (46,41,'interior','T',929);
INSERT INTO `RackSpace` VALUES (46,13,'rear','T',939);
INSERT INTO `RackSpace` VALUES (46,13,'interior','T',939);
INSERT INTO `RackSpace` VALUES (46,11,'front','T',937);
INSERT INTO `RackSpace` VALUES (46,9,'rear','T',935);
INSERT INTO `RackSpace` VALUES (46,9,'front','T',935);
INSERT INTO `RackSpace` VALUES (46,7,'rear','T',933);
INSERT INTO `RackSpace` VALUES (46,7,'front','T',933);
INSERT INTO `RackSpace` VALUES (43,13,'rear','T',945);
INSERT INTO `RackSpace` VALUES (44,37,'rear','T',930);
INSERT INTO `RackSpace` VALUES (44,37,'interior','T',930);
INSERT INTO `RackSpace` VALUES (44,38,'interior','T',930);
INSERT INTO `RackSpace` VALUES (44,40,'rear','T',928);
INSERT INTO `RackSpace` VALUES (44,41,'rear','T',928);
INSERT INTO `RackSpace` VALUES (44,41,'interior','T',928);
INSERT INTO `RackSpace` VALUES (44,15,'interior','T',940);
INSERT INTO `RackSpace` VALUES (44,15,'front','T',940);
INSERT INTO `RackSpace` VALUES (44,13,'interior','T',938);
INSERT INTO `RackSpace` VALUES (44,13,'front','T',938);
INSERT INTO `RackSpace` VALUES (44,11,'interior','T',936);
INSERT INTO `RackSpace` VALUES (44,11,'front','T',936);
INSERT INTO `RackSpace` VALUES (44,9,'interior','T',934);
INSERT INTO `RackSpace` VALUES (44,9,'front','T',934);
INSERT INTO `RackSpace` VALUES (44,7,'interior','T',932);
INSERT INTO `RackSpace` VALUES (44,7,'front','T',932);
INSERT INTO `RackSpace` VALUES (41,2,'front','T',917);
INSERT INTO `RackSpace` VALUES (41,2,'interior','T',917);
INSERT INTO `RackSpace` VALUES (41,1,'front','T',917);
INSERT INTO `RackSpace` VALUES (41,1,'interior','T',917);
INSERT INTO `RackSpace` VALUES (45,2,'front','T',956);
INSERT INTO `RackSpace` VALUES (45,2,'interior','T',956);
INSERT INTO `RackSpace` VALUES (45,2,'rear','T',956);
INSERT INTO `RackSpace` VALUES (45,4,'front','T',957);
INSERT INTO `RackSpace` VALUES (45,4,'interior','T',957);
INSERT INTO `RackSpace` VALUES (45,4,'rear','T',957);
INSERT INTO `RackSpace` VALUES (45,6,'front','T',958);
INSERT INTO `RackSpace` VALUES (45,6,'interior','T',958);
INSERT INTO `RackSpace` VALUES (45,6,'rear','T',958);
INSERT INTO `RackSpace` VALUES (45,8,'front','T',959);
INSERT INTO `RackSpace` VALUES (45,8,'interior','T',959);
INSERT INTO `RackSpace` VALUES (45,8,'rear','T',959);
INSERT INTO `RackSpace` VALUES (45,10,'front','T',960);
INSERT INTO `RackSpace` VALUES (45,10,'interior','T',960);
INSERT INTO `RackSpace` VALUES (45,10,'rear','T',960);
INSERT INTO `RackSpace` VALUES (45,35,'interior','T',961);
INSERT INTO `RackSpace` VALUES (45,35,'rear','T',961);
INSERT INTO `RackSpace` VALUES (45,34,'interior','T',962);
INSERT INTO `RackSpace` VALUES (45,34,'rear','T',962);

INSERT INTO `TagTree` (`id`, `parent_id`, `tag`) VALUES (16,NULL,'Geo');
INSERT INTO `TagTree` (`id`, `parent_id`, `tag`) VALUES (17,NULL,'network');
INSERT INTO `TagTree` (`id`, `parent_id`, `tag`) VALUES (1,16,'east');
INSERT INTO `TagTree` (`id`, `parent_id`, `tag`) VALUES (2,16,'west');
INSERT INTO `TagTree` (`id`, `parent_id`, `tag`) VALUES (3,NULL,'testing');
INSERT INTO `TagTree` (`id`, `parent_id`, `tag`) VALUES (4,NULL,'production');
INSERT INTO `TagTree` (`id`, `parent_id`, `tag`) VALUES (5,1,'far east');
INSERT INTO `TagTree` (`id`, `parent_id`, `tag`) VALUES (6,2,'far west');
INSERT INTO `TagTree` (`id`, `parent_id`, `tag`) VALUES (7,1,'Москва');
INSERT INTO `TagTree` (`id`, `parent_id`, `tag`) VALUES (8,2,'London');
INSERT INTO `TagTree` (`id`, `parent_id`, `tag`) VALUES (9,6,'New-York');
INSERT INTO `TagTree` (`id`, `parent_id`, `tag`) VALUES (10,5,'東京');
INSERT INTO `TagTree` (`id`, `parent_id`, `tag`) VALUES (11,17,'WAN link');
INSERT INTO `TagTree` (`id`, `parent_id`, `tag`) VALUES (12,NULL,'racks');
INSERT INTO `TagTree` (`id`, `parent_id`, `tag`) VALUES (13,12,'tall racks');
INSERT INTO `TagTree` (`id`, `parent_id`, `tag`) VALUES (14,12,'low racks');
INSERT INTO `TagTree` (`id`, `parent_id`, `tag`) VALUES (15,NULL,'load balancer');
INSERT INTO `TagTree` (`id`, `parent_id`, `tag`) VALUES (18,17,'small network');
INSERT INTO `TagTree` (`id`, `parent_id`, `tag`) VALUES (19,17,'medium network');
INSERT INTO `TagTree` (`id`, `parent_id`, `tag`) VALUES (20,5,'北京');
INSERT INTO `TagTree` (`id`, `parent_id`, `tag`) VALUES (21,5,'서울');
INSERT INTO `TagTree` (`id`, `parent_id`, `tag`) VALUES (22,5,'Владивосток');
INSERT INTO `TagTree` (`id`, `parent_id`, `tag`) VALUES (23,NULL,'XKCD');
INSERT INTO `TagTree` (`id`, `parent_id`, `tag`) VALUES (24,23,'romance');
INSERT INTO `TagTree` (`id`, `parent_id`, `tag`) VALUES (25,23,'sarcasm');
INSERT INTO `TagTree` (`id`, `parent_id`, `tag`) VALUES (26,23,'math');
INSERT INTO `TagTree` (`id`, `parent_id`, `tag`) VALUES (27,23,'language');

INSERT INTO `TagStorage` VALUES ('object',905,3);
INSERT INTO `TagStorage` VALUES ('object',905,8);
INSERT INTO `TagStorage` VALUES ('object',908,4);
INSERT INTO `TagStorage` VALUES ('object',908,7);
INSERT INTO `TagStorage` VALUES ('object',909,3);
INSERT INTO `TagStorage` VALUES ('object',909,10);
INSERT INTO `TagStorage` VALUES ('object',910,8);
INSERT INTO `TagStorage` VALUES ('object',911,8);
INSERT INTO `TagStorage` VALUES ('object',912,8);
INSERT INTO `TagStorage` VALUES ('object',913,8);
INSERT INTO `TagStorage` VALUES ('object',914,8);
INSERT INTO `TagStorage` VALUES ('object',918,8);
INSERT INTO `TagStorage` VALUES ('object',919,8);
INSERT INTO `TagStorage` VALUES ('object',923,10);
INSERT INTO `TagStorage` VALUES ('object',924,10);
INSERT INTO `TagStorage` VALUES ('object',925,10);
INSERT INTO `TagStorage` VALUES ('object',928,9);
INSERT INTO `TagStorage` VALUES ('object',928,15);
INSERT INTO `TagStorage` VALUES ('object',929,9);
INSERT INTO `TagStorage` VALUES ('object',929,15);
INSERT INTO `TagStorage` VALUES ('object',932,9);
INSERT INTO `TagStorage` VALUES ('object',933,9);
INSERT INTO `TagStorage` VALUES ('object',934,9);
INSERT INTO `TagStorage` VALUES ('object',935,9);
INSERT INTO `TagStorage` VALUES ('object',936,9);
INSERT INTO `TagStorage` VALUES ('object',937,9);
INSERT INTO `TagStorage` VALUES ('object',938,9);
INSERT INTO `TagStorage` VALUES ('object',939,9);
INSERT INTO `TagStorage` VALUES ('object',940,9);
INSERT INTO `TagStorage` VALUES ('object',941,9);
INSERT INTO `TagStorage` VALUES ('object',956,4);
INSERT INTO `TagStorage` VALUES ('object',956,7);
INSERT INTO `TagStorage` VALUES ('object',957,4);
INSERT INTO `TagStorage` VALUES ('object',957,7);
INSERT INTO `TagStorage` VALUES ('object',958,4);
INSERT INTO `TagStorage` VALUES ('object',958,7);
INSERT INTO `TagStorage` VALUES ('object',959,4);
INSERT INTO `TagStorage` VALUES ('object',959,7);
INSERT INTO `TagStorage` VALUES ('object',960,4);
INSERT INTO `TagStorage` VALUES ('object',960,7);
INSERT INTO `TagStorage` VALUES ('object',961,4);
INSERT INTO `TagStorage` VALUES ('object',961,7);
INSERT INTO `TagStorage` VALUES ('object',962,4);
INSERT INTO `TagStorage` VALUES ('object',962,7);
INSERT INTO `TagStorage` VALUES ('ipv4net',96,8);
INSERT INTO `TagStorage` VALUES ('ipv4net',96,19);
INSERT INTO `TagStorage` VALUES ('ipv4net',97,8);
INSERT INTO `TagStorage` VALUES ('ipv4net',97,19);
INSERT INTO `TagStorage` VALUES ('ipv4net',98,9);
INSERT INTO `TagStorage` VALUES ('ipv4net',98,19);
INSERT INTO `TagStorage` VALUES ('ipv4net',99,9);
INSERT INTO `TagStorage` VALUES ('ipv4net',99,19);
INSERT INTO `TagStorage` VALUES ('ipv4net',102,7);
INSERT INTO `TagStorage` VALUES ('ipv4net',102,8);
INSERT INTO `TagStorage` VALUES ('ipv4net',102,11);
INSERT INTO `TagStorage` VALUES ('ipv4net',102,18);
INSERT INTO `TagStorage` VALUES ('ipv4net',103,8);
INSERT INTO `TagStorage` VALUES ('ipv4net',103,9);
INSERT INTO `TagStorage` VALUES ('ipv4net',103,11);
INSERT INTO `TagStorage` VALUES ('ipv4net',103,18);
INSERT INTO `TagStorage` VALUES ('ipv4net',104,9);
INSERT INTO `TagStorage` VALUES ('ipv4net',104,10);
INSERT INTO `TagStorage` VALUES ('ipv4net',104,11);
INSERT INTO `TagStorage` VALUES ('ipv4net',104,18);
INSERT INTO `TagStorage` VALUES ('ipv4net',105,7);
INSERT INTO `TagStorage` VALUES ('ipv4net',105,10);
INSERT INTO `TagStorage` VALUES ('ipv4net',105,11);
INSERT INTO `TagStorage` VALUES ('ipv4net',105,18);
INSERT INTO `TagStorage` VALUES ('ipv4net',106,7);
INSERT INTO `TagStorage` VALUES ('ipv4net',106,19);
INSERT INTO `TagStorage` VALUES ('ipv4net',107,7);
INSERT INTO `TagStorage` VALUES ('ipv4net',107,19);
INSERT INTO `TagStorage` VALUES ('ipv4net',108,10);
INSERT INTO `TagStorage` VALUES ('ipv4net',108,19);
INSERT INTO `TagStorage` VALUES ('rack',40,8);
INSERT INTO `TagStorage` VALUES ('rack',40,13);
INSERT INTO `TagStorage` VALUES ('rack',41,8);
INSERT INTO `TagStorage` VALUES ('rack',41,14);
INSERT INTO `TagStorage` VALUES ('rack',42,8);
INSERT INTO `TagStorage` VALUES ('rack',42,13);
INSERT INTO `TagStorage` VALUES ('rack',43,9);
INSERT INTO `TagStorage` VALUES ('rack',43,14);
INSERT INTO `TagStorage` VALUES ('rack',44,9);
INSERT INTO `TagStorage` VALUES ('rack',44,13);
INSERT INTO `TagStorage` VALUES ('rack',45,7);
INSERT INTO `TagStorage` VALUES ('rack',45,13);
INSERT INTO `TagStorage` VALUES ('rack',46,9);
INSERT INTO `TagStorage` VALUES ('rack',46,13);
INSERT INTO `TagStorage` VALUES ('rack',47,10);
INSERT INTO `TagStorage` VALUES ('rack',47,14);
