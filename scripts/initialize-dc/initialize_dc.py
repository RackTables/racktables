#!/usr/bin/python

from objects import objects

object=objects()
location_json=object.create_locations()
row_json= object.create_rows(location_json)
rack_json=object.create_racks(row_json)
object.create_servertypes()
object.create_interfacespeed()
object.create_tags()
object.update_row_config()
object.create_ipv4_space()
