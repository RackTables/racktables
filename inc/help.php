<?
/*
*
*  This file contains help rendering functions for RackTables.
*
*/

// Generate a link to the help tab.
function genHelpLink ($tabno)
{
	global $root;
	return "${root}?page=help&tab=${tabno}";
}

// Checks if a topic is present for page and tab and render a hinting element;
// do nothing otherwise.
function lookupHelpTopic ($pageno, $tabno)
{
	global $helptab;
	if (!isset ($helptab[$pageno][$tabno]))
		return;
	echo "<li style='position: absolute; top: 80px; right: 0px;'><a style='background-color: #f0f0f0; border: 0;' href='" . genHelpLink ($helptab[$pageno][$tabno]);
	echo "' alt='Help' title='Help is available for this page'>";
	printImageHREF ('helphint');
	echo '</a>';
	return;
}

// Prints the help page content.
function renderHelpTab ($tabno)
{
	switch ($tabno)
	{
//------------------------------------------------------------------------
		case 'default':
			startPortlet ('Hello there!');
			echo '
This is the help system of a working RackTables installation. Select one of the
tabs above to find information on specific topics. If you are new to this
software, just follow to the next tab.
';
			finishPortlet();
			break;
//------------------------------------------------------------------------
		case 'quickstart':
			startPortlet ('The 1st rack');
			echo
'
The datacenter world is built up from resources. The first resource to start
with is rackspace, which in turn is built up from racks. To create your first
rack, open Configuration->Dictionary page and go to "Edit words" tab.
<p>
Here you see a bunch of portlets, each holding some odd data. The one you need
right now is called "RackRow (3)". The only thing you need to do now is to think
about the name you want to assign to the first group of your racks and to type
it into the form and press OK. This can be changed later, so a simple "server
room" is Ok.
<p>
Now get back to the main page and head into Rackspace page. You will see you
rack row with zero racks. Click it and go to "Add new rack" tab. This is the
moment where you create the rack itself, supplying its name and height. The rack
is empty.
';
			finishPortlet();
			startPortlet ('The 1st object');
			echo
'
To populate the rack, you need some stuff called objects. Let\'s assume you
have a server. 
';
			finishPortlet();
			break;
//------------------------------------------------------------------------
		case 'rackspace':
			startPortlet ('Rack design');
			echo
				"Rack design defines the physical layout of a rack cabinet. " .
				"Most common reason to use the tab is absence of back rails, although " .
				"any other design can be defined.<p>" .
				"In this tab you can change mounting atoms' state between 'free' and 'absent'.<br>" .
				"A selected checkbox means atom presence.";
			finishPortlet();
			startPortlet ('Rackspace problems');
			echo
				"Rack problems prevent free rackspace from being used for mounting. Such rackspace is considered " .
				"unusable. After the problem is gone, the atom can become free again. " .
				"In this tab you can change atoms' state from free to unusable and back.<br>" .
				"A selected checkbox means a problem.";
			finishPortlet();
			break;
//------------------------------------------------------------------------
		case 'iprange':
			startPortlet ('IP Range');
			echo 
				"This tab manages IPv4 resources. All IPv4 addresses are grouped to subnets. Subnets are flat and don't make a hierarchy. " .
				"In other words, the whole IPv4 range you have can be divided into subnets. " .
				"Every IPv4 address there must belong to one and only one subnetwork.";
			finishPortlet();
			startPortlet ('IP Address');
			echo 
				"Every IP address can be either bound to an interface or free. On the other hand, it can " .
				"be either reserved or not. That makes 4 possible states: bound - reserved, bound - unreserved, free - reserved ".
				"free - unreserved. The first state is considered as \"conflicting\" and will be shown red-highlited. ".
				"An IP address may have a \"Name\" assigned to it, which is intended to be used as a short comment. ".
				"An example would be \"The default GW\" or \"Reserved for field engineer\" ".
				"Binding an address to an interface is called \"allocation\". The interface is a rack object plus an interface name. " .
				"The interface name can be the same as a physical port label on that box or something else. " .
				"If you are binding it to a linux box with 2 physical ports, you might want to name interfaces as " .
				"eth0, eth1, eth0:4, eth1.110, etc, whereas your physical port names will be eth1 and eth2 " .
				"The difference between ports and interfaces is that say a switch may have 24 ports and only 1 interface, ".
				"which is accessable from any of those ports. Generally, one IP address can be bound only to one interface, ".
				"otherwise it's considered as a \"collision\". However, there are exceptions and a tool to mark ".
				"those exceptions. There is a \"bond type\" or \"interface type\", which can be either \"Regular\" ".
				"or \"Shared\" or \"Virtual\". Shared means that 2 or more peers share the same IP address ".
				"like it's done in VRRP or HSRP. Usually, there is only one box possessing it at a time ".
				"but when it dies, another one will have it. Shared bonds will not conflict with each other, ".
				"but will conflict with regular bindings of the same IP address. Virtual interface is ".
				"an assignment that usually don't broadcast itself through the network, but will allow ".
				"the OS to accept packets with that IP address sent to the box. This is widely used ".
				"in loadbalancing technics where loadbalancers simply do ARP proxy; they rewrite L2 address ".
				"in L2 frames with target's address and resend them back to the network. Virtual interfaces ".
				"do not conflict with any other interface types. Note: do not use virtual interfaces if ".
				"your loadbalancer uses NAT. There is a NAT tab for that instead.";

			finishPortlet();
			break;
//------------------------------------------------------------------------
		case 'ports':
			startPortlet ('Ports');
			echo
				"A port or physical interface is a small thingy on your box you can connect a cable to. ".
				"So far this software can only handle network ports. No power outlets yet. Each port can ".
				"have a local name, that is how this port is visible from the OS point of view. For linux box ".
				"that will be eth0, eth1, etc. Visible lable is what is written on the port on the box ".
				"Depending on the manufacturer you may observe labels as \"1\", \"2\", etc or something else. ".
				"Port type is an essential property that allows port connections to be properly arranged. ".
				"It lets you know that you won't be able to connect optical and copper ports together with one cable. ".
				"Some ports have an L2 address. It's helpful to populate those, as you may find it handy to ".
				"find ports by L2 addresses while investigating your STP tree. Now you can link or reserve ports ".
				"Reserving a port is simply adding a comment to it, thus preventing it to be linked. ".
				"A good reservation can be \"Reserved for a field engineer laptop\". Linking ports is creating ".
				"a connection between them. That is plugging a cable to them. Only ports with compatible ".
				"types can be linked. Say, RJ-45/100-Base TX can be linked to RJ-45/1000-Base TX, but can't ".
				"be linked to LC/100-BASE FX. In many cases you'll need to add a bunch of ports from a switch. ".
				"In this case there is a text area and a format selector. Just choose your device and format, ".
				"paste the output to the textarea and click \"Parse output\" button. Also, you need to ".
				"choose which port type is to be used, since it's not possible to guess that from the output.";
			finishPortlet();
			break;
//------------------------------------------------------------------------
		case 'portfwrd':
			startPortlet ('NATv4');
			echo
				"Boxes can translate their own L4 addresses to other L4 addresses on other boxes. This is called ".
				"NAT. In protocol selection box you can choose 2 protocols so far, UDP and TCP. Source is one of ".
				"IP addresses assigned to the box and after a colon is a box for numerical port. As a target you ".
				"have to choose a target IP address and port it will be translated to. Add a decription if you like. ".
				"After submitting the form you will find that if there was an object assined to the target IP address ".
				"it will be shown as well. A single source IP address/port can be assigned to multiple target IP ".
				"addresses/ports. That will represent an L4 loadbalancing. And vice versa, multiple sources can be ".
				"translated to one target";
			finishPortlet();
			break;
//------------------------------------------------------------------------
		case 'workflow':
			startPortlet ('People');
			echo
'
<div class=helptext>
<ul>
	<li>Datacenter engineers</li>
	<li>System administrators</li>
	<li>Network administrators</li>
	<li>Helpdesk</li>
</ul>
</div>
';
			finishPortlet();
			startPortlet ('Common tasks');
			echo
'
<div class=helptext>
<ul>
	<li>Resource allocation</li>
	<li>Search</li>
	<li>Changes tracking</li>
</ul>
</div>
';
			finishPortlet();
			startPortlet ('Effective collaboration and best practices');
			echo
'
<div class=helptext>
<ul>
	<li>Resource allocation</li>
	<li>Search</li>
	<li>Changes tracking</li>
</ul>
</div>
';
			finishPortlet();
			break;
//------------------------------------------------------------------------
		case 'objects':
			startPortlet ('Object life cycle');
			echo
'
<div class=helptext>
<ul>
	<li>Creation</li>
	<li>Resource allocation</li>
	<li>Possible changes</li>
	<li>Retiring</li>
</ul>
</div>
';
			finishPortlet();
			break;
//------------------------------------------------------------------------
		default:
			startPortlet ('Oops!');
			echo "There was no help text found for help tab '${tabno}' in renderHelpTab().";
			finishPortlet();
			break;
//------------------------------------------------------------------------
	}
}

?>
