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
