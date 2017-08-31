#!/usr/bin/env python

# This file is a part of RackTables, a datacenter and server room management
# framework. See accompanying file "COPYING" for the full copyright and
# licensing information.

# Requires Cisco ucsmsdk 0.9.1.0 or higher - https://communities.cisco.com/docs/DOC-64378
# This script provides an online stdin/stdout interface to the list of an UCS
# domain components. This is done through Python ucsmsdk interfacing with the UCS
# manager (domain controller) instance, a virtual service provided by physical
# UCS Fabric Interconnect switches. The list is output in a text format and is
# eventually available as PHP array through queryDevice() RackTables function.
#
# Script stdin is a multiline text in the following format:
#
# login <hostname> <username> <password>
# getmo
#
# Script stdout is also a multiline text consisting of OK/ERR lines optionally
# preceded with other content. In the case of enumeration the content is a
# series of COLUMNS and ROW lines with CSV data.

import sys
try:
	from ucsmsdk import *
	from ucsmsdk.ucshandle import *
	from ucsmsdk.mometa.network.NetworkElement import NetworkElement
	from ucsmsdk.mometa.equipment.EquipmentChassis import EquipmentChassis
	from ucsmsdk.mometa.compute.ComputeBlade import ComputeBlade
	from ucsmsdk.mometa.vnic.VnicFc import VnicFc
	from ucsmsdk.mometa.vnic.VnicEther import VnicEther
	from ucsmsdk.mometa.compute.ComputeRackUnit import ComputeRackUnit
except Exception, err:
	sys.stderr.write('UCS Python SDK is missing %s (Path: %s)\n' % (str(err), sys.path))
	sys.stderr.write('<P>Available at https://communities.cisco.com/docs/DOC-64378<P>\n')
	sys.exit(2)

loggedin = 0
while 1:
	try:
		line = sys.stdin.readline()
	except KeyboardInterrupt:
		break

	if not line:
		break

	words = line.split()
	if len(words) == 0:
		continue
	elif len(words) >= 1 and words[0] == "help":
		print "HELP: login <hostname> <username> <password>"
		print "HELP: Immediately try to log into specified UCS manager instance with given credentials."
		print "HELP: getmo"
		print "HELP: Retrieve lists of managed objects and output as a set of tables (requires active login)."
		print "OK"
	# endif "help"
	elif len(words) == 4 and words[0] == "login":
		try:
			if loggedin == 1:
				print "INFO Closing previous connection..."
				handle.Logout()
			handle=UcsHandle(words[1], words[2], words[3])
			if (handle.login() == False):
				loggedin = 0
				print "ERR could not log into " + words[1]
				sys.exit(0)
			loggedin = 1
			print "OK logged into " + words[1]
		except Exception, err:
			loggedin = 0
			print "ERR could not log into " + words[1]
	# endif "login"
	elif len(words) == 1 and words[0] == "getmo":
		if (loggedin != 1):
			print "ERR not logged in"
		else:
			try:
				print "COLUMNS type,serial,DN,model,OOB"
				for mo in handle.query_classid(class_id="NetworkElement"):
					print "ROW NetworkElement,"   + mo.serial + "," + mo.dn + "," + mo.model + "," + mo.oob_if_ip
				print "COLUMNS type,serial,DN,model"
				for ch in handle.query_classid(class_id="EquipmentChassis"):
					print "ROW EquipmentChassis," + ch.serial + "," + ch.dn + "," + ch.model
				print "COLUMNS type,serial,DN,model,assigned,slot"
				for bl in handle.query_classid(class_id="ComputeBlade"):
					print "ROW ComputeBlade,"     + bl.serial + "," + bl.dn + "," + bl.model + "," + bl.assigned_to_dn + "," + bl.slot_id
				print "COLUMNS type,DN,name,addr"
				for po in handle.query_classid(class_id="VnicEther") + handle.query_classid(class_id="VnicFc"):
					if (po.addr != "derived"):
						print "ROW VnicPort,"	  + po.dn + "," + po.name + "," + po.addr
				print "COLUMNS type,serial,DN,model,assigned"
				for rm in handle.query_classid(class_id="ComputeRackUnit"):
					print "ROW ComputeRackUnit,"	 + rm.serial + "," + rm.dn + "," + rm.model + "," + rm.assigned_to_dn
				print "OK enumeration complete"
			except Exception, err:
				print "ERR exception occured, logging out"
				loggedin = 0
	# endif "getmo"
	else:
		print "ERR command not supported (type \"help\" for help)"
# endfor
sys.exit(0)

