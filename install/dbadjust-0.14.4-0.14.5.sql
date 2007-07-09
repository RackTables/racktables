--
-- This script should be executed to upgrade database from version 0.14.4 to 0.14.5.
--

-- applies to RT <= 0.14.4
delete from IPAddress where name = '' and reserved = 'no';


-- applies to RT <= 0.14.4
alter table Attribute AUTO_INCREMENT = 10000;
alter table Chapter AUTO_INCREMENT = 10000;
alter table Dictionary AUTO_INCREMENT = 10000;
alter table UserAccount AUTO_INCREMENT = 10000;
update UserAccount set user_id = user_id + 10000 where user_id > 1;
update UserPermission set user_id = user_id + 10000 where user_id > 1;
update Attribute set attr_id = attr_id + 10000 where attr_id > 24;
update AttributeMap set attr_id = attr_id + 10000 where attr_id > 24;
update Chapter set chapter_no = chapter_no + 10000 where chapter_no > 20;
update AttributeMap set chapter_no = chapter_no + 10000 where chapter_no > 20;
