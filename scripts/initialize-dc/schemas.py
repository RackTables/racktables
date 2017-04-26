#!/usr/bin/python

from sqlalchemy.ext.declarative import declarative_base
from sqlalchemy import Column, Integer, String,DateTime,Float,Enum
import datetime
import time
Base = declarative_base()


class Port(Base):
        __tablename__='Port'
        id = Column(Integer(unsigned=True), primary_key=True)
        object_id= Column(Integer(unsigned=True))
        name = Column(String)
        iif_id = Column(Integer(unsigned=True))
        type = Column(Integer(unsigned=True))
        l2address = Column(String)
        reservation_comment = Column(String)
        label = Column(String)

        def __init__(self,oid,name,iif_id,type,l2,reservation,label):
                self.object_id=oid
                self.name = name
                self.iif_id = iif_id
                self.type=type
                self.l2address=l2
                self.reservation_comment= reservation
                self.label= label

class Object(Base):
	__tablename__='Object'
	id = Column(Integer(unsigned=True), primary_key=True)
	name = Column(String)
	label = Column(String)
	objtype_id =  Column(Integer(unsigned=True))
	asset_no = Column(String,unique=True)
	has_problems = Column(Enum('yes','no'))
	comment = Column(String)
	
	def __init__(self,name,label,oid,asset_no,has_probs,comment):
		self.name=name
		self.label=label
		self.objtype_id=oid
		self.asset_no=asset_no
		self.has_problems=has_probs
		self.comment=comment

class ObjectHistory(Base):
	__tablename__='ObjectHistory'
	id = Column(Integer(unsigned=True), primary_key=True)
        name = Column(String)
        label = Column(String)
        objtype_id =  Column(Integer(unsigned=True))
        asset_no = Column(String)
        has_problems = Column(Enum('yes','no'))
        comment = Column(String)
	ctime= Column(DateTime)
	user_name =Column(String)

	def __init__(self,id,name,label,oid,asset_no,has_probs,comment,user):
		self.id=id
        	self.name=name
        	self.label=label
        	self.objtype_id=oid
        	self.asset_no=asset_no
        	self.has_problems=has_probs
        	self.comment=comment
		now=time.localtime()
		format = '%Y-%m-%d %H:%M:%S'
		self.ctime=time.strftime(format, now)
		self.user_name=user


class RackSpace(Base):
	__tablename__='RackSpace'
	rack_id = Column(Integer(unsigned=True), primary_key=True)
	unit_no = Column(Integer(unsigned=True), primary_key=True)
	atom = Column(Enum('front','interior','rear'),primary_key=True)
	state = Column(Enum('A','U','T'))
	object_id = Column(Integer(unsigned=True))

	def __init__(self,rack_id,unit_num,atom,state,oid):
		self.rack_id=rack_id
		self.unit_no=unit_num
		self.atom=atom
		self.state=state
		self.object_id=oid

class Molecule(Base):
	__tablename__='Molecule'
	id = Column(Integer(unsigned=True), primary_key=True)

	def __init__(self):
		return
	
class Atom(Base):
	__tablename__='Atom'
	molecule_id = Column(Integer(unsigned=True))
	rack_id = Column(Integer(unsigned=True),primary_key=True)
	unit_no = Column(Integer(unsigned=True),primary_key=True)
	atom= Column(Enum('front','interior','rear'))
	
	def __init__(self,mid,rid,uid,atom):
		self.molecule_id=mid
		self.rack_id=rid
		self.unit_no=uid
		self.atom=atom


class MountOperation(Base):
	__tablename__='MountOperation'
	id=Column(Integer(unsigned=True), primary_key=True)
	object_id=Column(Integer(unsigned=True))
	ctime = Column(DateTime)
	user_name=Column(String)
	old_molecule_id=Column(Integer(unsigned=True))
	new_molecule_id=Column(Integer(unsigned=True))
	comment=Column(String)
	
	def __init__(self,oid,old_mid,new_mid,user,comment):
		self.object_id=oid
		self.user_name=user
		self.old_molecule_id=old_mid
		self.new_molecule_id=new_mid
		self.comment=comment


class TagStorage(Base):
	__tablename__='TagStorage'
	entity_realm= Column(Enum('file','ipv4net','ipv4rspool','ipv4vs','ipvs','ipv6net','location','object','rack','user','vst'),primary_key=True)
	entity_id=Column(Integer(unsigned=True),primary_key=True)
	tag_id = Column(Integer(unsigned=True),primary_key=True)
	tag_is_assignable = Column(Enum('yes','no'))
	user = Column(String)
	date=Column(DateTime)	

	def __init__(self,entity_realm,entity_id,tag_id,tag_is_assignable,user):
		now=time.localtime()
      	 	format = '%Y-%m-%d %H:%M:%S'
    		self.date=time.strftime(format, now)
		self.entity_realm=entity_realm
		self.entity_id=entity_id
		self.tag_id=tag_id
		self.tag_is_assignable=tag_is_assignable
		self.user=user


class TagTree(Base):
	__tablename__='TagTree'
	id=Column(Integer(unsigned=True), primary_key=True)
	parent_id=Column(Integer(unsigned=True))
	is_assignable=Column(Enum('yes','no'))
	tag= Column(String)

	def __init__(self,flag,tag):
		self.is_assignable=flag
		self.tag=tag



class EntityLink(Base):
	__tablename__='EntityLink'
	id=Column(Integer(unsigned=True), primary_key=True)
	parent_entity_type= Column(Enum('location','object','rack','row'),unique=True)
	parent_entity_id=Column(Integer(unsigned=True),unique=True)
	child_entity_type= Column(Enum('location','object','rack','row'),unique=True)
	child_entity_id=Column(Integer(unsigned=True),unique=True)

	def __init__(self,parent_et,parent_eid,child_et,child_eid):
		self.parent_entity_type=parent_et
		self.parent_entity_id=parent_eid
		self.child_entity_type=child_et
		self.child_entity_id=child_eid

class AttributeValue(Base):
	__tablename__='AttributeValue'
	object_id =Column(Integer(unsigned=True), primary_key=True)
	object_tid=Column(Integer(unsigned=True))
	attr_id=Column(Integer(unsigned=True))
	string_value = Column(String)
	uint_value = Column(Integer(unsigned=True))
	float_value = Column(Float)

	def __init__(self,value,oid,ot_id,at_id):
		self.object_id=oid
		self.object_tid=ot_id
		self.attr_id=at_id
		self.uint_value=value


class AttributeMap(Base):
	__tablename__='AttributeMap'
	objtype_id = Column(Integer(unsigned=True),primary_key=True)
	attr_id = Column(Integer(unsigned=True),primary_key=True)
	chapter_id = Column(Integer(unsigned=True))
	sticky= Column(Enum('yes','no'))
	
	def __init__(self,otid,atid,cid,sticky):
		self.objtype_id=otid
		self.attr_id=atid
		self.chapter_id=cid
		self.sticky = sticky

class Config(Base):
	__tablename__='Config'
	varname= Column(String,primary_key=True)
	varvalue= Column(String)
	vartype = Column(Enum('string','uint'))	
	emptyok = Column(Enum('yes','no'))
	is_hidden = Column(Enum('yes','no'))
	is_userdefined = Column(Enum('yes','no'))
	description = Column(String)

	def __init__(self,name,value,type,empty,hidden,userdefined,desc):
		self.varname = name
		self.vartype= type
		self.varvalue = value
		self.emptyok = empty
		self.is_hidden = hidden
		self.is_userdefined = userdefined
		self.description = desc

class PortInterfaceCompat(Base):
	__tablename__='PortInterfaceCompat'
	iif_id = Column(Integer(unsigned=True),primary_key=True)
	oif_id=Column(Integer(unsigned=True),primary_key=True)
	
	def __init__(self,iid,oid):
		self.iif_id=iid
		self.oif_id=oid

class Dictionary(Base):
	__tablename__='Dictionary'
	chapter_id=Column(Integer(unsigned=True),unique=True)
	dict_key = Column(Integer(unsigned=True),primary_key=True)
	dict_sticky=Column(Enum('yes','no'))
	dict_value=Column(String)

	def __init__(self,id,sticky,value):
		self.chapter_id=id
		self.dict_sticky=sticky
		self.dict_value=value

class PortOuterInterface(Base):
	__tablename__ ='PortOuterInterface'
	id=Column(Integer(unsigned=True),primary_key=True)
	oif_name=Column(String)
	
	def __init__(self,oname):
		self.oif_name=oname

class IPv4Allocation(Base):
	__tablename__ ='IPv4Allocation'
	object_id=Column(Integer(unsigned=True),primary_key=True)
	ip=Column(Integer(unsigned=True),primary_key=True)
	name=Column(String)
	type=Column(Enum('regular','shared','virtual','router','point2point'))
	
	def __init__(self,oid,ip,name,type):
		self.object_id=oid
		self.ip=ip
		self.name=name
		self.type=type

class IPv4Network(Base):
	__tablename__='IPv4Network'
	id=Column(Integer(unsigned=True),primary_key=True)
	ip=Column(Integer(unsigned=True))
	mask=Column(Integer(unsigned=True))
	name=Column(String)
	comment=Column(String)

	def __init__(self,ip,mask,name):
		self.ip=ip
		self.mask=mask
		self.name=name

class IPv4Log(Base):
	__tablename__='IPv4Log'
	id=Column(Integer(unsigned=True),primary_key=True)
	ip=Column(Integer(unsigned=True))
	date=Column(DateTime)
	user=Column(String)
	message=Column(String)		

	def __init__(self,ip,user,msg):
		self.ip=ip
		self.user=user
		self.message=msg
		now=time.localtime()
                format = '%Y-%m-%d %H:%M:%S'
                self.date=time.strftime(format, now)
