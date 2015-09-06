#!/usr/bin/python

from sqlalchemy import create_engine
from sqlalchemy.orm import sessionmaker
from schemas import Port,Object,ObjectHistory,RackSpace,Molecule,Atom,MountOperation,TagStorage,EntityLink,AttributeValue,TagTree,Dictionary,PortOuterInterface
from objects import objects
import sqlalchemy
import os
import json


## This should be put in a config file but for now adding it here
BASEPATH="/var/racktables/"
MYSQLUSER="racktables_rw"
MYSQLPWD="mypassword"
MYSQLHOST="myhost"
MYSQLPORT="3306"
MYSQLDB="racktables"
object_path=BASEPATH+"objects"
###



engine=create_engine("mysql://"+MYSQLUSER+":"+MYSQLPWD+"@"+MYSQLHOST+":"+MYSQLPORT+"/"+MYSQLDB, echo=False)
Session = sessionmaker(bind=engine)
session = Session()
objects=objects()

location_file=os.path.join(object_path,"locations.json")
rows_file=os.path.join(object_path,"rows.json")
racks_file=os.path.join(object_path,"racks.json")
location_hash={}
with open(location_file) as loc_fh:
	location_hash=json.load(loc_fh)
for location in location_hash:
	row_hash={}
	with open(rows_file) as row_fh:
		rows_hash=json.load(row_fh)
	for row in rows_hash[location]:
		rack_hash={}
		with open(racks_file) as rack_fh:
			rack_hash=json.load(rack_fh)
		for rack in rack_hash[row]:
			server_hash={}
			server_file=os.path.join(object_path,"servers/"+str(row)+"/"+str(rack)+".json")
			if os.path.isfile(server_file):
				with open(server_file) as server_fh:
					server_hash=json.load(server_fh)
				for server in server_hash:
					instancetype=session.query(Dictionary).filter(Dictionary.dict_value==server_hash[server]["ServerType"]).first().dict_key
					new_server = Object(server,"node",4,server,"no",sqlalchemy.sql.null())
					session.add(new_server)
					session.flush()
					serverid=new_server.id
					for tag_name in server_hash[server]["Tags"]:
						tag_id=session.query(TagTree).filter(TagTree.tag==tag_name).first().id
						tagstorage_object=TagStorage("object",serverid,tag_id,"yes","admin")
						session.add(tagstorage_object)
					molecule= Molecule()
					session.add(molecule)
					session.flush()
					rackid=session.query(Object).filter(Object.name==rack).first().id
					for unit in server_hash[server]["Racking"]:
						rack_object=RackSpace(rackid,unit,"interior","T",serverid)
						session.add(rack_object)
						atom=Atom(molecule.id,rackid,unit,"interior")
						session.add(atom)
						mount=MountOperation(serverid,sqlalchemy.sql.null(),molecule.id,"admin",sqlalchemy.sql.null())
						session.add(mount)
					node_attr=AttributeValue(int(instancetype),int(serverid),4,2)
					node_history= ObjectHistory(int(serverid),server,"node",4,server,"no",sqlalchemy.sql.null(),"admin")
					session.add(node_history)
					session.add(node_attr)
					session.flush()
			else:	
				print "Ignoring %s as Server details file doesnot exist"% rack
session.commit()
session.close()


