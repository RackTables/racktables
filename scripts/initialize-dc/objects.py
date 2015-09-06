#!/usr/sbin/python
import os
import json
import sqlalchemy
from sqlalchemy.orm import sessionmaker
from sqlalchemy import create_engine
from schemas import Object,ObjectHistory,RackSpace,Molecule,Atom,MountOperation,TagStorage,EntityLink,AttributeValue,TagTree,PortOuterInterface,AttributeMap,Config,Dictionary,PortInterfaceCompat,IPv4Network
import ipaddr

## This should be put in a config file but for now adding it here
BASEPATH="/var/racktables/"
MYSQLUSER="racktables_rw"
MYSQLPWD="mypassword"
MYSQLHOST="myhost"
MYSQLPORT="3306"
MYSQLDB="racktables"

###

class objects():
	object_path=None
	session=None
	def create_locations(self):
		location_file=os.path.join(self.object_path,"locations.json")
		return_hash={}
		if os.path.isfile(location_file):
			location_hash={}
			with open(location_file) as location_fh:
				location_hash=json.load(location_fh)
			for location in location_hash:
				new_object=Object(location,"DC",1562,location,"no",sqlalchemy.sql.null())
				self.session.add(new_object)
				self.session.flush()
				objectid=new_object.id
				history_object=ObjectHistory(int(objectid),location,"DC",1562,location,"no",sqlalchemy.sql.null(),"admin")
				self.session.add(history_object)
				self.session.flush()
				self.session.commit()
				return_hash[location]=int(objectid)
			return return_hash
		else:
			return return_hash
	def create_rows(self,location_json):
		location_hash=location_json
		rows_file=os.path.join(self.object_path,"rows.json")
		return_hash={}
		if os.path.isfile(rows_file):
			rows_hash={}
			with open(rows_file) as rows_fh:
				rows_hash=json.load(rows_fh)
			for location in location_hash:
				if location in rows_hash:
					for row in rows_hash[location]:
						row_object=Object(row,"ROW",1561,row,"no",sqlalchemy.sql.null())
						self.session.add(row_object)
						self.session.flush()
						rowid=row_object.id
						history_object=ObjectHistory(int(rowid),location,"DC",1562,location,"no",sqlalchemy.sql.null(),"admin")
						self.session.add(history_object)
						link=EntityLink("location",int(location_hash[location]),"row",int(rowid))
						self.session.add(link)
						self.session.flush()
						self.session.commit()
						return_hash[row]=rowid
			return return_hash
				
		else:
			return return_hash
		return rows_json
	def create_racks(self,rows_json):
		rows_hash=rows_json
		return_hash={}
		racks_file=os.path.join(self.object_path,"racks.json")
		if os.path.isfile(racks_file):
			racks_hash={}
			with open(racks_file) as racks_fh:
				racks_hash=json.load(racks_fh)
			for row in rows_hash:
				if row in racks_hash:
					for rack in racks_hash[row]:
						rack_object=Object(rack,"RACK",1560,rack,"no",sqlalchemy.sql.null())
						self.session.add(rack_object)
						self.session.flush()
						rackid=rack_object.id
						history_object=ObjectHistory(int(rackid),rack,"RACK",1560,rack,"no",sqlalchemy.sql.null(),"admin")
						self.session.add(history_object)
						link=EntityLink("row",int(rows_hash[row]),"rack",int(rackid))
						self.session.add(link)
						attribute=AttributeValue(48,int(rackid),1560,27)
						self.session.add(attribute)
						attribute=AttributeValue(1,int(rackid),1560,29)
						self.session.add(attribute)
						self.session.flush()
						self.session.commit()
						return_hash[rack]=rackid
						self.__initialize_racks(rackid,rack,row)
			return return_hash
		else:
			return return_hash
	def __initialize_racks(self,rack_id,rack_name,row):
		rack_file=os.path.join(self.object_path,str(row)+"/"+str(rack_name)+".json")
		if os.path.isfile(rack_file):
			rack_matrix={}
			rack_hash={}
			with open(rack_file) as rack_fh:
				rack_hash=json.load(rack_fh)
			for key in rack_hash:
				rack_matrix[int(key)]="1"
			for i in range(1,49):
				if i not in rack_matrix:
					front_object=RackSpace(rack_id,i,"front","A",sqlalchemy.sql.null())
					rear_object= RackSpace(rack_id,i,"rear","A",sqlalchemy.sql.null())
					interior_object = RackSpace(rack_id,i,"interior","A",sqlalchemy.sql.null())
					self.session.add(front_object)
					self.session.add(rear_object)
					self.session.add(interior_object)
				else:
					front_object=RackSpace(rack_id,i,"front","A",sqlalchemy.sql.null())
                                        rear_object= RackSpace(rack_id,i,"rear","A",sqlalchemy.sql.null())
					self.session.add(front_object)
                                        self.session.add(rear_object)
				self.session.flush()
				self.session.commit()
		else:
			print "Ignoring Initialization of %s as initialization matrix doesn't exist" %(rack_name) 
		return 0


	def  create_servertypes(self):
		instancetype_file=os.path.join(self.object_path,"servertypes.json")
		if os.path.isfile(instancetype_file):
			instancetype_hash={}
			with open(instancetype_file) as it_fh:
				instancetype_hash=json.load(it_fh)
			for server in instancetype_hash["Server"]:
				dict_object=Dictionary(11,"yes",server)
				self.session.add(dict_object)
			for chassis in instancetype_hash["Chassis"]:
				dict_object=Dictionary(31,"yes",chassis)
				self.session.add(dict_object)
			self.session.flush()
			self.session.commit()
	def create_interfacespeed(self):
		interface_file=os.path.join(self.object_path,"interfaces.json")
		if os.path.isfile(interface_file):
			interface_hash={}
			with open(interface_file) as in_fh:
				interface_hash=json.load(in_fh)
			for interface in interface_hash:
				interface_object=PortOuterInterface(interface)
				self.session.add(interface_object)
				self.session.flush()
				interfaceid=interface_object.id	
				icompact = PortInterfaceCompat(1,interfaceid)
				self.session.add(icompact)
				self.session.flush()
			self.session.commit()		
	def update_row_config(self):
		row_file= os.path.join(self.object_path,"rowconfig.json")
		if os.path.isfile(row_file):
			row_hash={}
			with open(row_file) as row_fh:
				row_hash=json.load(row_fh)
			for name in row_hash:
				self.session.query(Config).filter_by(varname=name).update({"varvalue": row_hash[name]})
			self.session.flush()
			self.session.close()
	def create_tags(self):
		tags=os.path.join(self.object_path,"tags.json")
		if os.path.isfile(tags):
			tags_hash={}
			with open(tags) as tags_fh:
				tags_hash=json.load(tags_fh)
			for tag in tags_hash["tags"]:
				tag_object=TagTree("yes",tag)
				self.session.add(tag_object)
			self.session.flush()
			self.session.commit()
	def create_ipv4_space(self):
		routerfile=os.path.join(self.object_path,"routers.json")
		if os.path.isfile(routerfile):
			router_map={}
			with open(routerfile) as router_fh:
				router_map=json.load(router_fh)
			for routerid in router_map["routers"]:
				subnet=routerid
				subnet=subnet.split("/")
				ipaddress=ipaddr.IPAddress(subnet[0])
				ipobject=IPv4Network(int(ipaddress),subnet[1],str(routerid))
				self.session.add(ipobject)
			self.session.flush()
			self.session.commit()

	def __init__(self):
		self.object_path=BASEPATH+"objects"
		engine = create_engine("mysql://"+MYSQLUSER+":"+MYSQLPWD+"@"+MYSQLHOST+":"+MYSQLPORT+"/"+MYSQLDB, echo=False)
		Session = sessionmaker(bind=engine)
		self.session=session = Session()
	def __del__(self):
		self.session.close()
		
