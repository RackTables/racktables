INSERT INTO `UserPermission` (`user_id`, `page`, `tab`, `access`) VALUES (1,'%','%','yes');
INSERT INTO `UserPermission` (`user_id`, `page`, `tab`, `access`) VALUES (0,'help','%','yes');
INSERT INTO `UserAccount` (`user_id`, `user_name`, `user_enabled`, `user_password_hash`, `user_realname`)
VALUES (1,'admin','yes',sha1(

# Uncomment and change the next line to your password, e.g.:
# 'mysecretpassword'
# ... and comment out the line below:
PLEASE_READ_THE_INSTALL_DOCUMENTATION

),'RackTables Administrator');
