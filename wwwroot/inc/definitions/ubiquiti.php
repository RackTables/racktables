<?php

# This file is a part of RackTables, a datacenter and server room management
# framework. See accompanying file "COPYING" for the full copyright and
# licensing information.

/*
*
*  This file is a definitions file for Ubiquiti devices.
*
*/

$ubiquiti_switches = array(
"EdgeSwitch 16-Port 10G" => array(
 'dict_key' => 2625,
 'text' => 'Ubiquiti EdgeSwitch ES-16-XG',
 'processors' => array ('ubiquiti-chassis-13-to-16-10GBASE-T','ubiquiti-chassis-any-SFP+')
), 
"EdgeSwitch 48-Port Lite" => array(
 'dict_key' => 2624,
 'text' => 'Ubiquiti EdgeSwitch ES-48-LITE',
 'processors' => array ('ubiquiti-chassis-51-to-52-1000SFP','ubiquiti-chassis-any-1000T','ubiquiti-chassis-any-SFP+')
));

if (!isset($switch_model_lookup))
{
  $switch_model_lookup = array();
}

$switch_model_lookup['ubiquiti'] = $ubiquiti_switches;

?>
