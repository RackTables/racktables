<?php
/*
 * Link Management
 *
 *	Features:
 *		- create links between ports
 *		- create backend links between ports
 *		- visually display links / chains
 *			 e.g.
 * 			(Object)>[port] -- front --> [port]<(Object) == back == > (Object)>[port] -- front --> [port]<(Object)
 * 		- Link object backend ports by name (e.g. handy for patch panels)
 *		- change/create CableID (needs jquery.jeditable.mini.js)
 *		- change/create Port Reservation Comment (needs jquery.jeditable.mini.js)
 *		- multiple backend links for supported port types (e.g. AC-in, DC)
 *		- GraphViz Maps (Objects, Ports and Links) (needs GraphViz_Image 1.3.0)
 *			- object,port or link  highligthing (just click on it)
 *			- context menu to link and unlink ports
 *
 *	Usage:
 *		1. select "Link Management" tab
 *		2. you should see link chains of all linked ports
 *		3. to display all ports hit "Show All Ports" in the left upper corner
 *		4. to link all ports with the same name of two different objects use "Link Object Ports by Name"
 *			a. select the other object you want to backend link to
 *			b. "show back ports" gives you the list of possible backend links
 *				!! Important port names have to be the same on both objects !!
 *				e.g. (Current Object):Portname -?-> Portname:(Selected Object)
 *			c. select all backend link to create (Ctrl + a for all)
 *			d. Enter backend CableID for all selected links
 *			e. "Link back" create the backend links
 *		5. If you have an backend link within the same Object the link isn't shown until
 *		   "Expand Backend Links on same Object" is hit
 *		6. "Object Map" displays Graphviz Map of current object
 *		7. To get a Graphviz Map of a single port click the port name on the left
 *
 *
 * Requirements:
 *	PHP 5
 *	GraphViz_Image 1.3.0 or newer
 *
 * INSTALL:
 *
 *	- create LinkBackend Table in your RackTables database

CREATE TABLE `LinkBackend` (
  `porta` int(10) unsigned NOT NULL DEFAULT '0',
  `portb` int(10) unsigned NOT NULL DEFAULT '0',
  `cable` char(64) DEFAULT NULL,
  PRIMARY KEY (`porta`,`portb`),
  UNIQUE KEY `porta` (`porta`),
  UNIQUE KEY `portb` (`portb`),
  CONSTRAINT `LinkBackend-FK-a` FOREIGN KEY (`porta`) REFERENCES `Port` (`id`) ON DELETE CASCADE,
  CONSTRAINT `LinkBackend-FK-b` FOREIGN KEY (`portb`) REFERENCES `Port` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

 * Multilink table

CREATE TABLE `LinkBackend` (
  `porta` int(10) unsigned NOT NULL DEFAULT '0',
  `portb` int(10) unsigned NOT NULL DEFAULT '0',
  `cable` char(64) DEFAULT NULL,
  PRIMARY KEY (`porta`,`portb`),
  KEY `LinkBackend_FK_b` (`portb`),
  CONSTRAINT `LinkBackend_FK_a` FOREIGN KEY (`porta`) REFERENCES `Port` (`id`) ON DELETE CASCADE,
  CONSTRAINT `LinkBackend_FK_b` FOREIGN KEY (`portb`) REFERENCES `Port` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8

 *	- copy linkmgmt.php to inc/ directory
 *	- copy jquery.jeditable.mini.js to js/ directory (http://www.appelsiini.net/download/jquery.jeditable.mini.js)
 * 	- add "include 'inc/linkmgmt.php';" to inc/local.php
 *
 * TESTED on FreeBSD 9.0, nginx/1.0.11, php 5.3.9
 *	GraphViz_Image 1.3.0
 *	and RackTables 0.19.11
 *
 * (c)2012 Maik Ehinger <m.ehinger@ltur.de>
 */

/*************************
 * Change Log
 *
 * 15.01.12	new loopdetection
 * 18.01.12	code cleanups
 * 23.01.12	add href to printport
 * 24.01.12	add opHelp
 * 25.01.12	max loop count handling changed
 *		add port label to port tooltip
 * 28.02.12 	changed printportlistrow() first from TRUE to FALSE
 *		add portlist::hasbackend()
 * 29.02.12	fix update cable id for backend links
 *			add linkmgmt_commitUpdatePortLink
 * 04.03.12	add set_reserve_comment and set_link permission handling
 * 18.07.12	add transform:rotate to Back Link image
 * 19.07.12	new Description with usage
 * 01.08.12	fix whitespaces
 *		make portlist::urlparams, urlparamsarray, hasbackend static
 * 03.08.12	fix display order for objects without links
 * 06.08.12	add port count to Link by Name
 *		change "Link by Name" dialog design
 * 10.08.12	add portlist::_getlinkportsymbol
 *		rename _LinkPort -> _printlinkportsymbol
 * 16.08.12	add multlink support (breaks column alignment!)
 *		add GraphViz Maps ( with port / object highlighting )
 *
 *
 */

/*************************
 * TODO
 *
 * - code cleanups
 * - bug fixing
 *
 * - fix loopdectect for multiport
 *	MAX_LOOP_COUNT
 *	loop highlight gv map
 *
 * - fix column alignment with multilinks
 *
 * - put selected object/port top left of graph
 * - multlink count for Graphviz maps empty or full dot
 *
 * - csv list
 *
 * - fix $opspec_list for unlink
 *
 */


//require_once 'inc/popup.php';
//require_once 'inc/interface.php'; /* renderCell */
require_once 'Image/GraphViz.php';

$tab['object']['linkmgmt'] = 'Link Management';
$tabhandler['object']['linkmgmt'] = 'linkmgmt_tabhandler';
//$trigger['object']['linkmgmt'] = 'linkmgmt_tabtrigger';

$ophandler['object']['linkmgmt']['update'] = 'linkmgmt_opupdate';
$ophandler['object']['linkmgmt']['unlinkPort'] = 'linkmgmt_opunlinkPort';
$ophandler['object']['linkmgmt']['PortLinkDialog'] = 'linkmgmt_opPortLinkDialog';
$ophandler['object']['linkmgmt']['Help'] = 'linkmgmt_opHelp';

$ophandler['object']['linkmgmt']['map'] = 'linkmgmt_opmap';
$ophandler['object']['linkmgmt']['mapinfo'] = 'linkmgmt_opmapinfo';

/* ------------------------------------------------- */

Const MULTILINK = TRUE;

/* -------------------------------------------------- */

$lm_multilink_port_types = array(
				16, /* AC-in */
				//1322, /* AC-out */
				1399, /* DC */
				);

/* -------------------------------------------------- */

$lm_cache = array(
		'allowcomment' => TRUE, /* RackCode ${op_set_reserve_comment} */
		'allowlink' => TRUE, /* RackCode ${op_set_link} */
		'rackinfo' => array(),
		);

/* -------------------------------------------------- */

//function linkmgmt_tabtrigger() {
//	return 'std';
//} /* linkmgmt_tabtrigger */

/* -------------------------------------------------- */

function linkmgmt_opHelp() {
?>
	<table cellspacing=10><tr><th>Help</th><tr>
		<tr><td width=150></td><td width=150 style="font-weight:bold;color:<?php echo portlist::CURRENT_OBJECT_BGCOLOR; ?>">Current Object</td></tr>
		<tr><td></td><td bgcolor=<?php echo portlist::CURRENT_PORT_BGCOLOR; ?>>[current port]</td></tr>
		<tr><td>front link</td><td>[port]<(Object)</td><td>back link</td></tr>
		<tr><td>back link</td><td>(Object)>[port]</td><td>front link</td></tr>
		<tr><td></td><td><pre>----></pre></td><td>Front link</td></tr>
		<tr><td></td><td><pre>====></pre></td><td>Backend link</td></tr>
		<tr><td></td><td>Link Symbol</td><td>Create new link</td></tr>
		<tr><td></td><td>Cut Symbol</td><td>Delete link</td></tr>

	</table>

<?php
	exit;
} /* opHelp */

/* -------------------------------------------------- */

function linkmgmt_opmapinfo() {

	$object_id = NULL;
	$port_id = NULL;
	$remote_id = NULL;
	$linktype = NULL;

	if(isset($_REQUEST['object_id']))
		$object_id = $_REQUEST['object_id'];

	if(isset($_REQUEST['port_id']))
		$port_id = $_REQUEST['port_id'];

	if(isset($_REQUEST['remote_id']))
		$remote_id = $_REQUEST['remote_id'];

	if(isset($_REQUEST['linktype']))
		$linktype = $_REQUEST['linktype'];

	$debug = NULL;
	if(isset($_REQUEST['debug']))
		$debug['value'] = $_REQUEST['debug'];

	$info = array();

	echo "<table style=\"font-size:12;\"><tr>";

	if($port_id != NULL)
	{
		$port = new linkmgmt_RTport($port_id);

		echo "<td>";
		$port->printtable('both');
		echo "</td>";

		if($debug)
			$debug['port'] = &$port;

		if($remote_id != NULL)
		{

			$remote_port = new linkmgmt_RTport($remote_id);

			echo "<td><table align=\"center\">";

			// TODO cableid
			echo "<tr><td><pre>".($linktype == 'back' ? ' ===> ' : ' ---> ')."</pre></td></tr>";

			$port->printunlinktr($linktype, $remote_port);

			echo "</table></td>";


			echo "<td>";
			$remote_port->printtable('both');
			echo "</td>";

			if($debug)
				$debug['remote_port'] = &$remote_port;

		}
		else
			$port->printunlinktr();


	}
	echo "</tr><tr>";

	echo "<td>";
	$object = linkmgmt_RTport::printobjecttable($object_id);
	echo "</td>";

	if($debug)
		$debug['object'] = &$object;

	if($remote_id != NULL)
	{

		echo "<td></td>"; /* link */
		echo "<td>";
		$remote_object = linkmgmt_RTport::printobjecttable($remote_port->port['object_id']);
		echo "</td>";

		if($debug)
			$debug['remote_object'] = &$remote_object;
	}

	echo "</tr></table>";

	if($debug)
	{
		echo "<pre>--- Debug ---";
		var_dump($debug);
		echo "</pre>";
	}

	exit;
}

/* -------------------------------------- */

class linkmgmt_RTport {

	private $port_id = NULL;

	public $port = false;

	function __construct($port_id) {

		$this->port = getPortInfo($port_id);

		if($this->port === false)
			return;

		/* successfully get port info */
		$this->port_id = $port_id;

	} /* __construct */

	function isvalid() {
		return ($port_id !== NULL);
	}

	function getlinks($type = 'front') {
	} /* getlinks */

	function printtable($linktype = 'front') {

		if($this->port_id == NULL)
			return;

		echo "<table>";

		$urlparams = array(
					'module' => 'redirect',
					'page' => 'object',
					'tab' => 'linkmgmt',
					'op' => 'map',
					'object_id' => $this->port['object_id'],
					'port_id' => $this->port_id,
					'usemap' => 1,
				);

		echo '<tr><td><a title="don\'t highlight port" href="?'.http_build_query($urlparams).'">-phl</a></td>';

		$urlparams['hl'] = 'p';
		echo '<td><a title="highlight port" href="?'.http_build_query($urlparams).'">+phl</a></td></tr>';

		$this->_printinforow($this->port,
					array(
						'id' => 'Port ID',
						'name' => 'Port Name',
						'oif_name' => 'Port Type',
						'l2address' => 'MAC',
						'reservation_comment' => 'comment',
					)
		); /* printinforow */

		$this->printlinktr($linktype);

		echo "</table>";
	} /* printtable */

	function printlinktr($linktype = 'front') {
		if($this->port_id === NULL)
			return;

                $urlparams = array(
				'tab' => 'linkmgmt',
                                'op'=>'PortLinkDialog',
                                'port'=>$this->port_id,
                                'object_id'=>$this->port['object_id'],
				'linktype' => $linktype,
				);

		echo "<tr><td align=\"center\"><a href='".
                                makeHrefProcess($urlparams).
                        "'>";
                        printImageHREF ('plug', 'Link this port');
                        echo "</a></td></tr>";
	} /* link */

	function printunlinktr($linktype = 'front', $remote_port = NULL) {
		if($this->port_id === NULL)
			return;

		$urlparams = array(
					'tab' => 'linkmgmt',
                                        'op'=>'unlinkPort',
                                        'port_id'=>$this->port_id,
                                        'object_id'=>$this->port['object_id'],
					'linktype' => $linktype,
				);

		$confirmmsg = "unlink port ".$this->port['name'];

		if($remote_port !== NULL)
		{
			$urlparams['remote_id'] = $remote_port->port['id'];
			$confirmmsg .= ' -> '.$remote_port->port['name'];
		}

		$confirmmsg .= " ($linktype)"; // TODO cableid

		echo "<tr><td align=\"center\"><a href='".makeHrefProcess($urlparams).
		"' onclick=\"return confirm('$confirmmsg');\">";
		printImageHREF ('cut', 'Unlink this port');
		echo "</a></td></tr>";

	} /* unlink */

	/* TODO move to object class */
	static function printobjecttable($object_id = NULL) {

		if($object_id === NULL)
			return;

		$object = spotEntity ('object', $object_id);

		if($object === false)
			return;

		echo "<table><tr><td>";
		renderCell($object);
	//	renderLBCell($object_id);
	//	renderRackObject($object_id);
		echo "</td></tr><tr><td><table>";

		self::_printinforow($object,
				array(
					'id' => 'ID',
					'name' => 'Name',
					'label' => 'Label',
					'Rack_name' => 'Rack',
					'Row_name' => 'Row',
				)

		); /* printinforow */

		$urlparams = array(
					'module' => 'redirect',
					'page' => 'object',
					'tab' => 'linkmgmt',
					'op' => 'map',
					'object_id' => $object_id,
					'usemap' => 1,
				);

		echo '<tr><td><a title="don\'t highlight object" href="?'.http_build_query($urlparams).'">-ohl</a></td>';

		$urlparams['hl'] = 'o';
		echo '<td><a title="highlight object" href="?'.http_build_query($urlparams).'">+ohl</a></td></tr>';

		echo "</table></td></tr></table>";

		return $object;

	} /* printobjecttable */

	static function _printinforow(&$data, $config) {

		foreach($config as $key => $name)
		{
			$value = $data[$key];
			if(!empty($value))
				echo "<tr><td align=\"right\" nowrap=\"nowrap\" style=\"font-size:10;\">$name:</td><td nowrap=\"nowrap\">$value</td></tr>";
		}

	} /* _printinforow */
} /* class RTport */

/* -------------------------------------------------- */

function linkmgmt_opmap() {

	/* TODO disable errors -> corrupts image data */

	$object_id = NULL;
	$port_id = NULL;
	$remote_id = NULL;
	$allports = false;
	$usemap = false;
	$command = NULL;

	/* highlight object */
	$hl = NULL;
	if(isset($_REQUEST['hl']))
	{
		$hl = $_REQUEST['hl'];
		unset($_REQUEST['hl_object_id']);
		unset($_REQUEST['hl_port_id']);

		if($hl == 'o')
		{
			unset($_GET['port_id']);
			unset($_GET['remote_id']);
		}

	}

	if(!$hl && isset($_REQUEST['hl_object_id']))
	{
		$hl = 'o';
		$object_id = $_REQUEST['hl_object_id'];
		unset($_REQUEST['object_id']);
		unset($_REQUEST['hl_port_id']);
		unset($_REQUEST['port_id']);
	}

	if(isset($_REQUEST['object_id']))
		$object_id = $_REQUEST['object_id'];

	if(isset($_REQUEST['type']))
	{
		$type = $_REQUEST['type'];
	}
	else
		$type = 'gif';

	/* highlight port */
	if(!$hl && isset($_REQUEST['hl_port_id']))
	{
		$hl = 'p';
		$port_id = $_REQUEST['hl_port_id'];
		unset($_REQUEST['port_id']);
	}

	if(isset($_REQUEST['allports']))
	{
		$allports = $_REQUEST['allports'];
	}

	if(isset($_REQUEST['port_id']))
	{
		$port_id = $_REQUEST['port_id'];
	}

	if(isset($_REQUEST['usemap']))
		$usemap = $_REQUEST['usemap'];

	if($hl == 'p' && $port_id === NULL)
	{
		unset($_GET['hl']);
		unset($_GET['port_id']);
		unset($_GET['remote_id']);
	}

	if($hl == 'o')
		unset($_GET['remote_id']);

	if(isset($_REQUEST['remote_id']))
		$remote_id = $_REQUEST['remote_id'];

	/* show all objects */
	if(isset($_REQUEST['all']))
	{
		$object_id = NULL;
		$port_id = NULL;
		$hl = NULL;
		unset($_GET['hl']);
	}

	if(isset($_REQUEST['cmd']))
		$command = $_REQUEST['cmd'];

	if(isset($_REQUEST['debug']))
		$debug = $_REQUEST['debug'];

	$gvmap = new linkmgmt_gvmap($object_id, $port_id, $allports, $hl, $remote_id);

	switch($type) {
		case 'gif':
		case 'png':
		case 'bmp':
		case 'jpeg':
		case 'tif':
		case 'wbmp':
			$ctype = "image/$type";
			break;
		case 'jpg':
			$ctype = "image/jpeg";
			break;
		case 'svg':
			$ctype = 'image/svg+xml';
			break;
		case 'pdf':
			$ctype = 'application/pdf';
			break;
		case 'cmapx':
			$ctype = 'text/plain';
			break;

	}

	if($usemap)
	{

		/* add context menu to Ports, Objects, Links, ...
		 */

		echo "<script>
			function initcontextmenu() {
				maps = document.getElementsByTagName('map');
                                for(i=0;i<maps.length;i++) {
					areas = maps[i].childNodes;

					for(j=0;j<areas.length;j++) {
						if(areas[j].nodeType == 1)
						{
						//	console.log(areas[j].id);
						//	attr = document.createAttribute('onmouseover','ahh');
						//	areas[j].setAttribute(attr);
						//	areas[j].onmouseover = 'menu(this);';
							areas[j].oncontextmenu = 'menu(this);';
						}
					}

                                }

			};

			function menu(parent) {

			//	console.log('Menu');
			//	console.log('--' + parent);
				event = window.event;

				ids = parent.id.split('-');

				if(ids[0] == 'graph1')
					return false;

				object_id = ids[0];

				url ='?module=redirect&page=object&tab=linkmgmt&op=mapinfo&object_id=' + object_id;

			//	links ='<li><a href=' + object_id + '>Object</a></li>';

				if(ids[1] != '')
				{
					port_id = ids[1];
					url += '&port_id=' + port_id;
				//	links += '<li><a href=' + port_id + '>Port</a></li>';

					if(ids[2] != '')
					{
						remote_id = ids[2];

						if(ids[3] != '')
						{
							linktype = ids[3];
							url += '&remote_id=' + remote_id + '&linktype=' + linktype;
						//	links += '<li><a href=' + port_id + '_' + remote_id + '_' + linktype + '>Unlink</a></li>';
						}
					}

				}


				xmlHttp = new XMLHttpRequest();
				xmlHttp.open('GET', url, false);
				xmlHttp.send(null);

				infodiv = document.getElementById('info');
				infodiv.innerHTML = xmlHttp.responseText;

		//		linkdiv = document.getElementById('link');
		//		linkdiv.innerHTML = links;

				menudiv = document.getElementById('menu');
				menudiv.style.position  = 'absolute';
				menudiv.style.top  = (event.clientY + document.body.scrollTop) + 'px';
				menudiv.style.left  = (event.clientX + document.body.scrollLeft) + 'px';
				menudiv.style.display  = '';

				return false;
			};

			function mousedown() {
				console.log('mouse down');

				event = window.event;

				if(event.button != 2)
					return true;

				menudiv = document.getElementById('menu');

				menudiv.style.display = 'none';

				return false;
			};

			</script>";

		echo "<body oncontextmenu=\"return false\" onmousedown=\"mousedown();\" onload=\"initcontextmenu();\">";

		echo "<div id=\"menu\" style=\"display:none; background-color:#ffff90\">
				<div id=\"info\"></div>
				<ul id=\"link\" style=\"list-style-type:none\"></ul>
			</div>";

		echo $gvmap->fetch('cmapx', $command);

		echo "<img src=\"data:$ctype;base64,".
			base64_encode($gvmap->fetch($type, $command)).
			"\" usemap=#map$object_id />";

		if($debug)
		{
			echo "<pre>";
			echo var_dump($gvmap->dump());
			echo "</pre>";

			echo "<pre>".$gvmap->parse()."</pre>";
		}
	}
	else
	{

		header("Content-Type: $ctype");
		echo $gvmap->fetch($type, $command);

	}

	exit;
}

/* ------------------------------------- */
class linkmgmt_gvmap {

	private $object_id = NULL;
	private $port_id = NULL;
	private $remote_id = NULL;

	private $gv = NULL;

	private $ports = array();

	private $allports = false;
	private $back = NULL;

	private $alpa = 'ff';

	function __construct($object_id = NULL, $port_id = NULL, $allports = false, $hl = NULL, $remote_id = NULL) {
		$this->allports = $allports;

		$hl_object_id = NULL;
		$hl_port_id = NULL;

		$hllabel = "";

		error_reporting( E_ALL ^ E_NOTICE ^ E_STRICT);
		$graphattr = array(
					'rankdir' => 'RL',
				//	'ranksep' => '0',
					'nodesep' => '0',
				//	'overlay' => false,
				);

		unset($_GET['module']);

		$_GET['all'] = 1;

		$graphattr['URL'] = makeHrefProcess($_GET);

		unset($_GET['all']);

		$this->gv = new Image_GraphViz(true, $graphattr, "map".$object_id);

		switch($hl)
		{
			case 'p':
			case 'port':
				$hllabel = " (Port highlight)";
				$hl_object_id = $object_id;
				$hl_port_id = $port_id;
				$port_id = NULL;
				$this->alpha = '30';
				break;
			case 'o':
			case 'object':
				$hllabel = " (Object highlight)";
				$hl_object_id = $object_id;
				$object_id = NULL;
				$this->alpha = '30';
				break;

		}

		$this->object_id = $object_id;
		$this->port_id = $port_id;
		$this->remote_id = $remote_id;

		if($object_id === NULL)
		{
			unset($_GET['all']);
			$_GET['hl'] = 'o';

			$this->gv->addAttributes(array(
						'label' => 'Showing all objects'.$hllabel,
						'labelloc' => 't',
						)
				);

			$objects = listCells('object');

			foreach($objects as $obj)
				$this->_add($this->gv, $obj['id'], NULL);
		}
		else
		{
			$object = spotEntity ('object', $object_id);

			$this->gv->addAttributes(array(
						'label' => "Graph for ${object['name']}$hllabel",
						'labelloc' => 't',
						)
				);

			$this->_add($this->gv, $object_id, $port_id);

			$children = getEntityRelatives ('children', 'object', $object_id); //'entity_id'

			foreach($children as $child)
				$this->_add($this->gv, $child['entity_id'], NULL);
		}

		/* highlight object/port */
		if($hl !== NULL)
		{

			$this->alpha = 'ff';

			$this->ports = array();
			$this->back = NULL;

			$this->object_id = $hl_object_id;
			$this->port_id = $hl_port_id;

			$hlgv = new Image_GraphViz(true, $graphattr);

			$this->_add($hlgv, $hl_object_id , $hl_port_id);

			/* merge higlight graph */
			// edgedfrom - from - to - id
			foreach($hlgv->graph['edgesFrom'] as $from => $nodes) {
				foreach($nodes as $to => $ports) {
				// TODO ports id

				if(isset($this->gv->graph['edgesFrom'][$from][$to]))
					$this->gv->graph['edgesFrom'][$from][$to] = $hlgv->graph['edgesFrom'][$from][$to];
				else
					if(isset($this->gv->graph['edgesFrom'][$to][$from]))
					{
						unset($this->gv->graph['edgesFrom'][$to][$from]);
						$this->gv->graph['edgesFrom'][$from][$to] = $hlgv->graph['edgesFrom'][$from][$to];
					}
				}
			}
			// leads to duplicate edges from->to and to->from
			//$this->gv->graph['edgesFrom'] = $hlgv->graph['edgesFrom'] + $this->gv->graph['edgesFrom'];

			/* merge nodes */
			foreach($hlgv->graph['nodes'] as $cluster => $node)
			{
				$this->gv->graph['nodes'][$cluster] = $hlgv->graph['nodes'][$cluster] + $this->gv->graph['nodes'][$cluster];
			}

			$this->gv->graph['clusters'] = $hlgv->graph['clusters'] + $this->gv->graph['clusters'];
			$this->gv->graph['subgraphs'] = $hlgv->graph['subgraphs'] + $this->gv->graph['subgraphs'];

		}

	//	portlist::var_dump_html($this->gv);

	//	echo $this->gv->parse();
		error_reporting( E_ALL ^ E_NOTICE);
	}

	// !!!recursiv !!!
	function _add($gv, $object_id, $port_id = NULL) {
		global $lm_multilink_port_types;

		/* used only for Graphviz ...
		 * !! numeric ids cause Image_Graphviz problems on nested clusters !!
		 */
		$cluster_id = "c$object_id";

		if($port_id === NULL)
		{
			if(
				isset($gv->graph['clusters'][$cluster_id]) ||
				isset($gv->graph['subgraphs'][$cluster_id])
			)
			return;
		}
		else
		{
			if(isset($this->ports[$port_id]))
				return;
		}

		$object = spotEntity ('object', $object_id);
	//	$object['attr'] = getAttrValues($object_id);

		$clusterattr = array();

		$this->_getcolor('cluster', 'default', $this->alpha, $clusterattr, 'color');
		$this->_getcolor('cluster', 'default', $this->alpha, $clusterattr, 'fontcolor');

		if($this->object_id == $object_id)
		{
			$clusterattr['rank'] = 'source';

			$this->_getcolor('cluster', 'current', $this->alpha, $clusterattr, 'color');
			$this->_getcolor('cluster', 'current', $this->alpha, $clusterattr, 'fontcolor');
		}

		$clusterattr['tooltip'] = "${object['name']}";

		unset($_GET['module']); // makeHrefProcess adds this
		unset($_GET['port_id']);
		unset($_GET['remote_id']);
		$_GET['object_id'] = $object_id;
		$_GET['hl'] = 'o';

		$clusterattr['URL'] = makeHrefProcess($_GET);

		//has_problems
		if($object['has_problems'] != 'no')
		{
			$clusterattr['style'] = 'filled';
			$this->_getcolor('cluster', 'problem', $this->alpha, $clusterattr, 'fillcolor');
		}

		$clustertitle = "${object['name']}";

		if(!empty($object['container_name']))
			$clustertitle .= "<BR/>${object['container_name']}";

		if(!empty($object['Row_name']) || !empty($object['Rack_name']))
			$clustertitle .= "<BR/>${object['Row_name']} / ${object['Rack_name']}";

		$embedin = $object['container_id'];
		if(empty($embedin))
			$embedin = 'default';
		else
		{
			$embedin = "c$embedin"; /* see cluster_id */

			/* add container / cluster if not already exists */
			$this->_add($gv, $object['container_id'], NULL);
		}

		$clusterattr['id'] = "$object_id----"; /* used for js context menu */

		$gv->addCluster($cluster_id, $clustertitle, $clusterattr, $embedin);

		if($this->back != 'front' || $port_id === NULL || $this->allports)
		$front = $this->_getObjectPortsAndLinks($object_id, 'front', $port_id);
		else
		$front = array();

		if($this->back != 'back' || $port_id === NULL || $this->allports)
		$backend = $this->_getObjectPortsAndLinks($object_id, 'back', $port_id);
		else
		$backend = array();

		$ports = array_merge($front,$backend);

		if(empty($ports))
		{
			/* needed because of  gv_image empty cluster bug (invalid foreach argument) */
			$gv->addNode('dummy', array(
					//	'label' =>'No Ports found/connected',
						'label' =>'',
						'fontsize' => 0,
						'size' => 0,
						'width' => 0,
						'height' => 0,
						'shape' => 'point',
						'style' => 'invis',
						), $cluster_id);
		}

		foreach($ports as $key => $port) {

			$this->back = $port['linktype'];

			$nodelabel = "${port['name']}";

			if($port['iif_id'] != '1' )
				$nodelabel .= "<BR/><FONT POINT-SIZE=\"8\">${port['iif_name']}</FONT>";

			$nodelabel .= "<BR/><FONT POINT-SIZE=\"8\">${port['oif_name']}</FONT>";

			$nodeattr = array(
						'label' => $nodelabel,
					);

			$this->_getcolor('port', 'default',$this->alpha, $nodeattr, 'fontcolor');
			$this->_getcolor('oif_id', $port['oif_id'],$this->alpha, $nodeattr, 'color');

			if($this->port_id == $port['id']) {
				$nodeattr['style'] = 'filled';
				$nodeattr['fillcolor'] = $this->_getcolor('port', 'current', $this->alpha);
			}

			if($this->remote_id == $port['id']) {
				$nodeattr['style'] = 'filled';
				$nodeattr['fillcolor'] = $this->_getcolor('port', 'remote', $this->alpha);
			}

			$nodeattr['tooltip'] = "${port['name']}";

			unset($_GET['module']);
			unset($_GET['remote_id']);
			$_GET['object_id'] = $port['object_id'];
			$_GET['port_id'] = $port['id'];
			$_GET['hl'] = 'p';

			$nodeattr['URL'] = makeHrefProcess($_GET);
			$nodeattr['id'] = "${port['object_id']}-${port['id']}--"; /* for js context menu */

			$gv->addNode($port['id'],
						$nodeattr,
						"c${port['object_id']}"); /* see cluster_id */

			$this->ports[$port['id']] = true;

			if(!empty($port['remote_id'])) {

				$this->_add($gv, $port['remote_object_id'], ($port_id === NULL ? NULL : $port['remote_id']));

				if(
					!isset($gv->graph['edgesFrom'][$port['id']][$port['remote_id']]) &&
					!isset($gv->graph['edgesFrom'][$port['remote_id']][$port['id']])
				) {

					$linktype = $port['linktype'];

					$edgetooltip = $port['object_name'].':'.$port['name'].
							' - '.$port['cableid'].' -> '.
							$port['remote_name'].':'.$port['remote_object_name'];

					$edgeattr = array(
							'fontsize' => 8,
							'label' => $port['cableid'],
							'tooltip' => $edgetooltip,
							'sametail' => $linktype,
							'samehead' => $linktype,
						);

					$this->_getcolor('edge', 'default', $this->alpha, $edgeattr, 'color');
					$this->_getcolor('edge', 'default', $this->alpha, $edgeattr, 'fontcolor');

					if($linktype == 'back' )
					{
						$edgeattr['style'] =  'dashed';
						$edgeattr['arrowhead'] = 'none';
						$edgeattr['arrowtail'] = 'none';

						/* multilink ports */
						if(in_array($port['oif_id'], $lm_multilink_port_types))
						{
							$edgeattr['dir'] = 'both';
							$edgeattr['arrowtail'] = 'dot';
						}

						if(in_array($port['remote_oif_id'], $lm_multilink_port_types))
						{
							$edgeattr['dir'] = 'both';
							$edgeattr['arrowhead'] = 'dot';
						}
					}

					if(
						($port['id'] == $this->port_id && $port['remote_id'] == $this->remote_id) ||
						($port['id'] == $this->remote_id && $port['remote_id'] == $this->port_id)
					)
					{
						$this->_getcolor('edge', 'highlight', 'ff', $edgeattr, 'color');
						$edgeattr['penwidth'] = 2; /* bold */
					}

					unset($_GET['module']);
					$_GET['object_id'] = $port['object_id'];
					$_GET['port_id'] = $port['id'];
					$_GET['remote_id'] = $port['remote_id'];

					$edgeattr['URL'] = makeHrefProcess($_GET);

					$edgeattr['id'] = $port['object_id']."-".$port['id']."-".$port['remote_id']."-".$linktype; /* for js context menu  */

					$gv->addEdge(array($port['id'] => $port['remote_id']),
								$edgeattr,
								array(
									$port['id'] => $linktype,
									$port['remote_id'] => $linktype,
								)
							);
				}
			}

		}

	//	portlist::var_dump_html($port);
	}

	function fetch($type = 'png', $command = NULL) {
		error_reporting( E_ALL ^ E_NOTICE ^ E_STRICT);
		$ret = $this->gv->fetch($type, $command);
		error_reporting( E_ALL ^ E_NOTICE ^ E_STRICT);
		return $ret;
	}

	function image($type = 'png', $command = NULL) {
		$this->gv->image($type, $command);
	}

	function parse() {
		return $this->gv->parse();
	}

	/* should be compatible with getObjectPortsAndLinks from RT database.php */
	function _getObjectPortsAndLinks($object_id, $linktype = 'front', $port_id = NULL) {

		if($linktype == 'front')
			$linktable = 'Link';
		else
			$linktable = 'LinkBackend';

		$qparams = array();

		$query = "SELECT
				'$linktype' as linktype,
				Port.*,
				Port.type AS oif_id,
				PortInnerInterface.iif_name as iif_name,
				Dictionary.dict_value as oif_name,
				RackObject.id as object_id, RackObject.name as object_name,
				LinkTable.cable as cableid,
				remoteRackObject.id as remote_object_id, remoteRackObject.name as remote_object_name,
				remotePort.id as remote_id, remotePort.name as remote_name,
				remotePort.type AS remote_oif_id,
				remotePortInnerInterface.iif_name as remote_iif_name,
				remoteDictionary.dict_value as remote_oif_name
			FROM Port";

		// JOIN
		$join = "	LEFT JOIN PortInnerInterface on PortInnerInterface.id = Port.iif_id
				LEFT JOIN Dictionary on Dictionary.dict_key = Port.type
				LEFT JOIN $linktable as LinkTable on Port.id in (LinkTable.porta, LinkTable.portb)
				LEFT JOIN RackObject on RackObject.id = Port.object_id
				LEFT JOIN Port as remotePort on remotePort.id = ((LinkTable.porta ^ LinkTable.portb) ^ Port.id)
				LEFT JOIN RackObject as remoteRackObject on remoteRackObject.id = remotePort.object_id
				LEFT JOIN PortInnerInterface as remotePortInnerInterface on remotePortInnerInterface.id = remotePort.iif_id
				LEFT JOIN Dictionary as remoteDictionary on remoteDictionary.dict_key = remotePort.type
			";

		// WHERE
		if($port_id === NULL)
		{
			$where = " WHERE RackObject.id = ?";
			$qparams[] = $object_id;
		}
		else
		{
		//	$where = " WHERE Port.id = ? and remotePort.id is not NULL";
			$where = " WHERE Port.id = ?";
			$qparams[] = $port_id;
		}

		// ORDER
		$order = " ORDER by oif_name, Port.Name";

		$query .= $join.$where.$order;

		$result = usePreparedSelectBlade ($query, $qparams);

		$row = $result->fetchAll(PDO::FETCH_ASSOC);

		return $row;
	}

	function _getcolor($type = 'object', $key = 'default', $alpha = 'ff', &$array = NULL , $arraykey = 'color') {

		$object = array(
				'current' => '#ff0000',
				);
		$port = array(
				'current' => '#ffff90',
				'remote' => '#ffffD0',
				);

		$cluster = array(
				'current' => '#ff0000',
				'problem' => '#ff3030',
				);

		$edge = array (
				'highlight' => '#ff0000',
				);

		$oif_id = array(
				'16' => '#800000', /* AC-in */
				'1322' => '#ff4500', /* AC-out */
				'24' => '#000080', /* 1000base-t */
				);

		$defaultcolor = '#000000'; /* black */
		$default = true;

		if(isset(${$type}[$key]))
		{
			$default = false;
			$color = ${$type}[$key];
		}
		else
			$color = $defaultcolor;


		if($alpha != 'ff' || $default == false)
		{
			$color .= $alpha;

			if($array !== NULL)
				$array[$arraykey] = $color;
			else
				return $color;
		}
		else
			return $defaultcolor;

	} /* _getcolor */

	function dump() {
		var_dump($this->gv);
	}

} /* class gvmap */

/* -------------------------------------------------- */

function linkmgmt_opupdate() {

	if(!isset($_POST['id']))
		exit;

	$ids = explode('_',$_POST['id'],3);
	$retval = strip_tags($_POST['value']);

	if(isset($ids[1])) {
		if(permitted(NULL, NULL, 'set_link'))
			if(isset($ids[2]) && $ids[2] == 'back')
				linkmgmt_commitUpdatePortLink($ids[0], $ids[1], $retval, TRUE);
			else
				linkmgmt_commitUpdatePortLink($ids[0], $ids[1], $retval);
		else
			$retval = "Permission denied!";
	} else {
		if(permitted(NULL, NULL, 'set_reserve_comment'))
			commitUpdatePortComment($ids[0], $retval);
		else
			$retval = "Permission denied!";
	}

	/* return what jeditable should display after edit */
	echo $retval;

	exit;
} /* opupdate */

/* -------------------------------------------------- */

/* similar to commitUpatePortLink in database.php with backend support */
function linkmgmt_commitUpdatePortLink($port_id1, $port_id2, $cable = NULL, $backend = FALSE) {

	/* TODO check permissions */

	if($backend)
		$table = 'LinkBackend';
	else
		$table = 'Link';

	return usePreparedExecuteBlade
		(
			"UPDATE $table SET cable=\"".(mb_strlen ($cable) ? $cable : NULL).
			"\" WHERE ( porta = ? and portb = ?) or (portb = ? and porta = ?)",
			array (
				$port_id1, $port_id2,
				$port_id1, $port_id2)
		);

} /* linkmgmt_commitUpdatePortLink */

/* -------------------------------------------------- */

function linkmgmt_opunlinkPort() {
	$port_id = $_REQUEST['port_id'];
	$linktype = $_REQUEST['linktype'];

	portlist::var_dump_html($_REQUEST);

	/* check permissions */
	if(!permitted(NULL, NULL, 'set_link')) {
		exit;
	}

	if($linktype == 'back')
	{
		$table = 'LinkBackend';
		$remote_id = $_REQUEST['remote_id'];

		$retval =  usePreparedExecuteBlade
			(
				"DELETE FROM $table WHERE ( porta = ? and portb = ?) or (portb = ? and porta = ?)",
				array (
					$port_id, $remote_id,
					$port_id, $remote_id)
			);
	}
	else
	{
		$table = 'Link';

		$retval = usePreparedDeleteBlade ($table, array('porta' => $port_id, 'portb' => $port_id), 'OR');
	}

	if($retval == 0)
		echo " Link not found";
	else
		echo " $retval Links deleted";


	unset($_GET['module']);
	unset($_GET['op']);

	header('Location: ?'.http_build_query($_GET));
	//header('Location: ?page='.$_REQUEST['page'].'&tab='.$_REQUEST['tab'].'&object_id='.$_REQUEST['object_id']);
	exit;
} /* opunlinkPort */

/* -------------------------------------------------- */

function linkmgmt_oplinkPort() {

	$linktype = $_REQUEST['linktype'];
	$cable = $_REQUEST['cable'];

	/* check permissions */
	if(!permitted(NULL, NULL, 'set_link')) {
		echo("Permission denied!");
		return;
	}

	if(!isset($_REQUEST['link_list'])) {
		//portlist::var_dump_html($_REQUEST);
		$porta = $_REQUEST['port'];

		foreach($_REQUEST['remote_ports'] as $portb)
		{
			$link_list[] = "${porta}_${portb}";

			/* with no MULTILINK process first value only */
			if(!MULTILINK)
				break;
		}
	} else
		$link_list = $_REQUEST['link_list'];

	foreach($link_list as $link){

		$ids = preg_split('/[^0-9]/',$link);
		$porta = $ids[0];;
		$portb = $ids[1];

		$ret = linkmgmt_linkPorts($porta, $portb, $linktype, $cable);

		//error_log("$ret - $porta - $portb");
		$port_info = getPortInfo ($porta);
		$remote_port_info = getPortInfo ($portb);
		showSuccess(
                        sprintf
                        (
                                'Port %s %s successfully linked with port %s %s',
                                formatPortLink ($port_info['id'], $port_info['name'], NULL, NULL),
				$linktype,
                                formatPort ($remote_port_info),
				$linktype
                        )
                );
	}

	addJS (<<<END
window.opener.location.reload(true);
window.close();
END
                , TRUE);

	return;
} /* oplinkPort */

/* -------------------------------------------------- */

/*
 * same as in database.php extendend with linktype
 */
function linkmgmt_linkPorts ($porta, $portb, $linktype, $cable = NULL)
{
        if ($porta == $portb)
                throw new InvalidArgException ('porta/portb', $porta, "Ports can't be the same");

	if($linktype == 'back')
	{
		$table = 'LinkBackend';
		$multilink = MULTILINK;
	}
	else
	{
		$table = 'Link';
		$multilink = false;
	}

        global $dbxlink;
        $dbxlink->exec ('LOCK TABLES '.$table.' WRITE');

	if(!$multilink)
	{
		$result = usePreparedSelectBlade
		(
			'SELECT COUNT(*) FROM '.$table.' WHERE porta IN (?,?) OR portb IN (?,?)',
			array ($porta, $portb, $porta, $portb)
		);

	        if ($result->fetchColumn () != 0)
	        {
			$dbxlink->exec ('UNLOCK TABLES');
			return "$linktype Port ${porta} or ${portb} is already linked";
		}
	        $result->closeCursor ();
	}

        if ($porta > $portb)
        {
                $tmp = $porta;
                $porta = $portb;
                $portb = $tmp;
        }
        $ret = FALSE !== usePreparedInsertBlade
        (
                $table,
                array
                (
                        'porta' => $porta,
                        'portb' => $portb,
                        'cable' => mb_strlen ($cable) ? $cable : ''
                )
        );
        $dbxlink->exec ('UNLOCK TABLES');
        $ret = $ret and FALSE !== usePreparedExecuteBlade
        (
                'UPDATE Port SET reservation_comment=NULL WHERE id IN(?, ?)',
                array ($porta, $portb)
        );
        return $ret ? '' : 'query failed';
}

/* -------------------------------------------------- */

/*
 * similar to renderPopupHTML in popup.php
 */
function linkmgmt_opPortLinkDialog() {
//	portlist::var_dump_html($_REQUEST);
header ('Content-Type: text/html; charset=UTF-8');
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" style="height: 100%;">
<?php

	$text = '<div style="background-color: #f0f0f0; border: 1px solid #3c78b5; padding: 10px; text-align: center;
 margin: 5px;">';

	if(permitted(NULL,NULL,"set_link"))
		if (isset ($_REQUEST['do_link'])) {
			$text .= getOutputOf ('linkmgmt_oplinkPort');
		}
		else
			if(isset($_REQUEST['byname']))
				$text .= getOutputOf ('linkmgmt_renderPopupPortSelectorbyName');
			else
				$text .= getOutputOf ('linkmgmt_renderPopupPortSelector');
	else
		$text .= "Permission denied!";

        $text .= '</div>';

	echo '<head><title>RackTables pop-up</title>';
        printPageHeaders();
        echo '</head>';
        echo '<body style="height: 100%;">' . $text . '</body>';
?>
</html>
<?php
	exit;
} /* opPortLinkDialog */

/* -------------------------------------------------- */

/*
 * like findSparePorts in popup.php extended with linktype
 *
 * multilink
 *
 */
function linkmgmt_findSparePorts($port_info, $filter, $linktype, $multilink = false, $objectsonly = false, $byname = false, $src_object_id = NULL) {


	/*
		$linktable ports that will be returned if not linked in this table
		$linkinfotable display link for info only show backend links if you want front link a port

		front: select ports no front connection and port compat, filter, ...

		back:

	 */

	if($linktype == 'back')
	{
		$linktable = 'LinkBackend';
		$linkinfotable = 'Link';
	}
	else
	{
		$linktable = 'Link';
		$linkinfotable = 'LinkBackend';
	}

	$qparams = array();
	$whereparams = array();

	// all ports with no link
	/* port:object -> linked port:object */
	$query = 'SELECT';
	$join = "";
	$where = " WHERE";
	$group = "";
	$order = " ORDER BY";

	if($objectsonly)
	{
		$query .= " remotePort.object_id, CONCAT(remoteRackObject.name, ' (', count(remotePort.id), ')') as name";
		$group .= " GROUP by remoteRackObject.id";
	}
	else
		if($byname)
		{
			$query .= ' CONCAT(localPort.id, "_", remotePort.id),
				 CONCAT(localRackObject.name, " : ", localPort.Name, " -?-> ", remotePort.name, " : ", remoteRackObject.name)';
		}
		else
		{
			$query .= " remotePort.id, CONCAT(remoteRackObject.name, ' : ', remotePort.name,
				IFNULL(CONCAT(' -- ', infolnk.cable, ' --> ', InfoPort.name, ' : ', InfoRackObject.name),'') ) as Text";
		}

	$query .= " FROM Port as remotePort";
	$join .= " LEFT JOIN RackObject as remoteRackObject on remotePort.object_id = remoteRackObject.id";
	$order .= " remoteRackObject.name";

	if($byname)
	{
		/* by name */
		$join .= " JOIN Port as localPort on remotePort.name = localPort.name";
		$where .= " remotePort.object_id <> ? AND localPort.object_id = ?";
		$whereparams[] = $src_object_id;
		$whereparams[] = $src_object_id;

		/* own port not linked */
		$join .= " LEFT JOIN $linktable as localLink on localPort.id in (localLink.porta, localLink.portb)";
		$join .= " LEFT JOIN RackObject as localRackObject on localRackObject.id = localPort.object_id";
		$where .= " AND localLink.porta is NULL";
	}
	else
	{
		/* exclude current port */
		$where .= " remotePort.id <> ?";
		$whereparams[] = $port_info['id'];
		$order .= " ,remotePort.name";

		/* add info to remoteport */
		$join .= " LEFT JOIN $linkinfotable as infolnk on remotePort.id in (infolnk.porta, infolnk.portb)";
		$join .= " LEFT JOIN Port as InfoPort on InfoPort.id = ((infolnk.porta ^ infolnk.portb) ^ remotePort.id)";
		$join .= " LEFT JOIN RackObject as InfoRackObject on InfoRackObject.id = InfoPort.object_id";
	}

	/* only ports which are not linked already */
	$join .= " LEFT JOIN $linktable as lnk on remotePort.id in (lnk.porta, lnk.portb)";
	$where .= " AND lnk.porta is NULL";

	if($linktype == 'front')
	{
		/* port compat */
		$join .= ' INNER JOIN PortInnerInterface pii ON remotePort.iif_id = pii.id
			INNER JOIN Dictionary d ON d.dict_key = remotePort.type';
		// porttype filter (non-strict match)
		$join .= ' INNER JOIN (
			SELECT Port.id FROM Port
			INNER JOIN
			(
				SELECT DISTINCT pic2.iif_id
					FROM PortInterfaceCompat pic2
					INNER JOIN PortCompat pc ON pc.type2 = pic2.oif_id';

                if ($port_info['iif_id'] != 1)
                {
                        $join .= " INNER JOIN PortInterfaceCompat pic ON pic.oif_id = pc.type1 WHERE pic.iif_id = ?";
                        $qparams[] = $port_info['iif_id'];
                }
                else
                {
                        $join .= " WHERE pc.type1 = ?";
                        $qparams[] = $port_info['oif_id'];
                }
                $join .= " AND pic2.iif_id <> 1
			 ) AS sub1 USING (iif_id)
			UNION
			SELECT Port.id
			FROM Port
			INNER JOIN PortCompat ON type1 = type
			WHERE iif_id = 1 and type2 = ?
			) AS sub2 ON sub2.id = remotePort.id";
			$qparams[] = $port_info['oif_id'];
	}


	$qparams = array_merge($qparams, $whereparams);

	 // rack filter
        if (! empty ($filter['racks']))
        {
                $where .= ' AND remotePort.object_id IN (SELECT DISTINCT object_id FROM RackSpace WHERE rack_id IN (' .
                        questionMarks (count ($filter['racks'])) . ')) ';
                $qparams = array_merge ($qparams, $filter['racks']);
        }

	// object_id filter
        if (! empty ($filter['object_id']))
        {
                $where .= ' AND remoteRackObject.id = ?';
                $qparams[] = $filter['object_id'];
        }
	else
	// objectname filter
        if (! empty ($filter['objects']))
        {
                $where .= ' AND remoteRackObject.name like ? ';
                $qparams[] = '%' . $filter['objects'] . '%';
        }

        // portname filter
        if (! empty ($filter['ports']))
        {
                $where .= ' AND remotePort.name LIKE ? ';
                $qparams[] = '%' . $filter['ports'] . '%';
        }

	$query .= $join.$where.$group.$order;

	$result = usePreparedSelectBlade ($query, $qparams);

	$row = $result->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_UNIQUE|PDO::FETCH_COLUMN);

	/* [id] => displaystring */
	return $row;

} /* findSparePorts */

/* -------------------------------------------------- */

/*
 * like renderPopupPortSelector in popup.php extenden with linktype
 */
function linkmgmt_renderPopupPortSelector()
{
	global $lm_multilink_port_types;

        assertUIntArg ('port');
        $port_id = $_REQUEST['port'];

	$showlinktypeswitch = false;

	if(isset($_GET['linktype']))
		$linktype = $_GET['linktype'];
	else
		$linktype = 'front';

	if($linktype == 'both')
	{

		/*
		 * 	use POST front/back_view to set linktype
		 *	and show linktype switch button
		 */

		$showlinktypeswitch = true;

		if(isset($_POST['front_view']))
			$linktype = 'front';
		else
		if(isset($_POST['back_view']))
			$linktype = 'back';
		else
			$linktype = 'front';
	}

	$object_id = $_REQUEST['object_id'];
        $port_info = getPortInfo ($port_id);

	$multilink = MULTILINK && $linktype == 'back' && in_array($port_info['oif_id'], $lm_multilink_port_types);

        if(isset ($_REQUEST['in_rack']))
		$in_rack = $_REQUEST['in_rack'] != 'off';
	else
		$in_rack = true;

//	portlist::var_dump_html($port_info);
//	portlist::var_dump_html($_GET);
//	portlist::var_dump_html($_POST);

        // fill port filter structure
        $filter = array
        (
                'racks' => array(),
                'objects' => '',
                'object_id' => '',
                'ports' => '',
        );

	$remote_object = NULL;
	if(isset($_REQUEST['remote_object']))
	{
		$remote_object = $_REQUEST['remote_object'];

		if($remote_object != 'NULL')
			$filter['object_id'] = $remote_object;
	}

        if (isset ($_REQUEST['filter-obj']))
                $filter['objects'] = $_REQUEST['filter-obj'];
        if (isset ($_REQUEST['filter-port']))
                $filter['ports'] = $_REQUEST['filter-port'];
        if ($in_rack)
        {
                $object = spotEntity ('object', $port_info['object_id']);
                if ($object['rack_id'])
                        $filter['racks'] = getProximateRacks ($object['rack_id'], getConfigVar ('PROXIMITY_RANGE'));
        }

	$objectlist = array('NULL' => '- Show All -');
	$objectlist = $objectlist + linkmgmt_findSparePorts($port_info, $filter, $linktype, $multilink, true);

	$spare_ports = linkmgmt_findSparePorts ($port_info, $filter, $linktype, $multilink);

	$maxsize  = getConfigVar('MAXSELSIZE');
	$objectcount = count($objectlist);

	if($linktype == 'back')
		$notlinktype = 'front';
	else
		$notlinktype = 'back';

        // display search form
        echo 'Link '.$linktype.' of ' . formatPort ($port_info) . ' to...';
        echo '<form method=POST>';
        startPortlet ($linktype.' Port list filter');
       // echo '<input type=hidden name="module" value="popup">';
       // echo '<input type=hidden name="helper" value="portlist">';

        echo '<input type=hidden name="port" value="' . $port_id . '">';
        echo '<table><tr><td valign="top"><table><tr><td>';

	echo '<table align="center"><tr>';

//	echo '<td nowrap="nowrap"><input type="hidden" name="linktype" value="front" /><input type="checkbox" name="linktype" value="back"'.($linktype == 'back' ? ' checked="checked"' : '' ).'>link backend</input></td></tr><tr>';
        echo '<td class="tdleft"><label>Object name:<br><input type=text size=8 name="filter-obj" value="' . htmlspecialchars ($filter['objects'], ENT_QUOTES) . '"></label></td>';
        echo '<td class="tdleft"><label>Port name:<br><input type=text size=6 name="filter-port" value="' . htmlspecialchars ($filter['ports'], ENT_QUOTES) . '"></label></td>';
        echo '<td class="tdleft" valign="bottom"><input type="hidden" name="in_rack" value="off" /><label><input type=checkbox value="1" name="in_rack"'.($in_rack ? ' checked="checked"' : '').'>Nearest racks</label></td>';
        echo '</tr></table>';

	echo '</td></tr><tr><td>';
        echo 'Object name (count ports)<br>';
        echo getSelect ($objectlist, array ('name' => 'remote_object',
						'size' => ($objectcount <= $maxsize ? $objectcount : $maxsize)),
						 $remote_object, FALSE);

	echo '</td></tr></table></td>';
        echo '<td valign="top"><table><tr><td><input type=submit value="update objects / ports"></td></tr>';

	if($showlinktypeswitch)
		echo '<tr height=150px><td><input type=submit value="Switch to '.$notlinktype.' view" name="'.$notlinktype.'_view"></tr></td>';

	echo '</table></td>';

        finishPortlet();
        echo '</td><td>';

        // display results
        startPortlet ('Compatible spare '.$linktype.' ports');
	echo "spare $linktype Object:Port -- $notlinktype cableID -->  $notlinktype Port:Object<br>";

	if($multilink)
		echo "Multilink<br>";

        if (empty ($spare_ports))
                echo '(nothing found)';
        else
        {
		$linkcount = count($spare_ports);

                $options = array(
				'name' => 'remote_ports[]',
				'size' => getConfigVar ('MAXSELSIZE'),
				'size' => ($linkcount <= $maxsize ? $linkcount : $maxsize),
				);

		if($multilink)
			$options['multiple'] = 'multiple';

                echo getSelect ($spare_ports, $options, NULL, FALSE);

                echo "<p>$linktype Cable ID: <input type=text id=cable name=cable>";
                echo "<p><input type='submit' value='Link $linktype' name='do_link'>";
        }
        finishPortlet();
        echo '</td></tr></table>';
        echo '</form>';

} /* linkmgmt_renderPopUpPortSelector */

/* -------------------------------------------------- */

/*
 * similar to renderPopupPortSelector but let you select the destination object
 * and displays possible backend links with ports of the same name
 */
function linkmgmt_renderPopupPortSelectorbyName()
{
	$linktype = $_REQUEST['linktype'];
	$object_id = $_REQUEST['object_id'];

	$object = spotEntity ('object', $object_id);

	$objectlist = linkmgmt_findSparePorts(NULL, NULL, $linktype, false, true, TRUE, $object_id);

	$objectname = (isset($objectlist[$object_id]) ? $objectlist[$object_id] : $object['name']." (0)" );

	/* remove self from list */
	unset($objectlist[$object_id]);

	if(isset($_REQUEST['remote_object']))
		$remote_object = $_REQUEST['remote_object'];
	else
	{
		/* choose first object from list */
		$keys = array_keys($objectlist);

		if(isset($keys[0]))
			$remote_object = $keys[0];
		else
			$remote_object = NULL;
	}

	if($remote_object)
	{
		$filter['object_id'] = $remote_object;
		$link_list = linkmgmt_findSparePorts(NULL, $filter, $linktype, false, false, TRUE, $object_id);
	}
	else
		$link_list = linkmgmt_findSparePorts(NULL, NULL, $linktype, false, false, TRUE, $object_id);

        // display search form
        echo 'Link '.$linktype.' of ' . formatPortLink($object_id, $objectname, NULL, NULL) . ' Ports by Name to...';
        echo '<form method=POST>';

        echo '<table align="center"><tr><td>';
        startPortlet ('Object list');

	$maxsize  = getConfigVar('MAXSELSIZE');
	$objectcount = count($objectlist);

        echo 'Object name (count ports)<br>';
        echo getSelect ($objectlist, array ('name' => 'remote_object',
						'size' => ($objectcount <= $maxsize ? $objectcount : $maxsize)),
						 $remote_object, FALSE);
        echo '</td><td><input type=submit value="show '.$linktype.' ports>"></td>';
        finishPortlet();

        echo '<td>';
        // display results
        startPortlet ('Possible Backend Link List');
	echo "Select links to create:<br>";
        if (empty ($link_list))
                echo '(nothing found)';
        else
        {
		$linkcount = count($link_list);

		$options = array(
				'name' => 'link_list[]',
				'size' => ($linkcount <= $maxsize ? $linkcount : $maxsize),
				'multiple' => 'multiple',
				);

                echo getSelect ($link_list,$options, NULL, FALSE);

                echo "<p>$linktype Cable ID: <input type=text id=cable name=cable>";
                echo "<p><input type='submit' value='Link $linktype' name='do_link'>";
        }
        finishPortlet();
        echo '</td></tr></table>';
        echo '</form>';

} /* linkmgmt_renderPopUpPortSelectorByName */

/* ------------------------------------------------ */

function linkmgmt_tabhandler($object_id) {
	global $lm_cache;

	$target = makeHrefProcess(portlist::urlparams('op','update'));

	addJS('js/jquery.jeditable.mini.js');

	/* TODO  if (permitted (NULL, 'ports', 'set_reserve_comment')) */
	/* TODO Link / unlink permissions  */

	$lm_cache['allowcomment'] = permitted(NULL, NULL, 'set_reserve_comment'); /* RackCode {$op_set_reserve_comment} */
	$lm_cache['allowlink'] = permitted(NULL, NULL, 'set_link'); /* RackCode {$op_set_link} */

	//portlist::var_dump_html($lm_cache);

	/* init jeditable fields/tags */
	if($lm_cache['allowcomment'])
		addJS('$(document).ready(function() { $(".editcmt").editable("'.$target.'",{placeholder : "add comment"}); });' , TRUE);

	if($lm_cache['allowlink'])
		addJS('$(document).ready(function() { $(".editcable").editable("'.$target.'",{placeholder : "edit cableID"}); });' , TRUE);

	/* linkmgmt for current object */
	linkmgmt_renderObjectLinks($object_id);

	/* linkmgmt for every child */
	//$parents = getEntityRelatives ('parents', 'object', $object_id);
	$children = getEntityRelatives ('children', 'object', $object_id); //'entity_id'

	//portlist::var_dump_html($children);

	foreach($children as $child) {
		echo '<h1>Links for Child: '.$child['name'].'</h1>';
		linkmgmt_renderObjectLinks($child['entity_id']);
	}

	return;

} /* tabhandler */

/* -------------------------------------------------- */
function linkmgmt_renderObjectLinks($object_id) {

	$object = spotEntity ('object', $object_id);
        $object['attr'] = getAttrValues($object_id);

	/* get ports */
	/* calls getObjectPortsAndLinks */
	amplifyCell ($object);

	//$ports = getObjectPortsAndLinks($object_id);
	$ports = $object['ports'];

	/* reindex array so key starts at 0 */
	$ports = array_values($ports);

	/* URL param handling */
	if(isset($_GET['allports'])) {
		$allports = $_GET['allports'];
	} else
		$allports = FALSE;

	if(isset($_GET['allback'])) {
		$allback = $_GET['allback'];
	} else
		$allback = FALSE;

	echo '<table><tr>';

	if($allports) {

		echo '<td width=200><a href="'.makeHref(portlist::urlparams('allports','0','0'))
			.'">Hide Ports without link</a></td>';
	} else
		echo '<td width=200><a href="'.makeHref(portlist::urlparams('allports','1','0'))
			.'">Show All Ports</a></td>';

	echo '<td width=200><span onclick=window.open("'.makeHrefProcess(portlist::urlparamsarray(
                                array('op' => 'PortLinkDialog','linktype' => 'back','byname' => '1'))).'","name","height=700,width=800,scrollbars=yes");><a>Link Object Ports by Name</a></span></td>';

	if($allback) {

		echo '<td width=200><a href="'.makeHref(portlist::urlparams('allback','0','0'))
			.'">Collapse Backend Links on same Object</a></td>';
	} else
		echo '<td width=200><a href="'.makeHref(portlist::urlparams('allback','1','0'))
			.'">Expand Backend Links on same Object</a></td>';
	
	/* Graphviz map */
	//echo '<td width=100><span onclick=window.open("'.makeHrefProcess(portlist::urlparamsarray(
        //                        array('op' => 'map','usemap' => 1))).'","name","height=800,width=800,scrollbars=yes");><a>Object Map</a></span></td>';

	/* Help */
	echo '<td width=200><span onclick=window.open("'.makeHrefProcess(portlist::urlparamsarray(
                                array('op' => 'Help'))).'","name","height=400,width=500");><a>Help</a></span></td>';

	if(isset($_REQUEST['hl_port_id']))
		$hl_port_id = $_REQUEST['hl_port_id'];
	else
		$hl_port_id = NULL;

	echo '</tr></table>';


	echo '<br><br><table id=renderobjectlinks0>';

	/*  switch display order depending on backend links */
	$first = portlist::hasbackend($object_id);

	$rowcount = 0;
	foreach($ports as $key => $port) {

		$plist = new portlist($port, $object_id, $allports, $allback);

		//echo "<td><img src=\"index.php?module=redirect&page=object&tab=linkmgmt&op=map&object_id=$object_id&port_id=${port['id']}&allports=$allports\" ></td>";

		if($plist->printportlistrow($first, $hl_port_id, ($rowcount % 2 ? portlist::ALTERNATE_ROW_BGCOLOR : "#ffffff")) )
			$rowcount++;

	}

	echo "</table>";

} /* renderObjectLinks */

/* -------------------------------------------------- */

/*
 * Portlist class
 * gets all linked ports to spezified port
 * and prints this list as table row
 *
 */
class portlist {

	public $list = array();

	private $object_id;
	private $port_id;
	private $port;

	private $first_id;
	private $front_count;

	private $last_id;
	private $back_count;

	private $count = 0;

	private $allback = FALSE;

	private $multilink = MULTILINK;

	const B2B_LINK_BGCOLOR = '#d8d8d8';
	const CURRENT_PORT_BGCOLOR = '#ffff99';
	const CURRENT_OBJECT_BGCOLOR = '#ff0000';
	const HL_PORT_BGCOLOR = '#00ff00';
	const ALTERNATE_ROW_BGCOLOR = '#f0f0f0';

	/* Possible LOOP detected after count links print only */
	const MAX_LOOP_COUNT = 13;

	private $loopcount;

	private $gv = NULL;

	function __construct($port, $object_id, $allports = FALSE, $allback = FALSE) {

		$this->object_id = $object_id;

		$this->port = $port;

		$port_id = $port['id'];

		$this->port_id = $port_id;

		$this->first_id = $port_id;
		$this->last_id = $port_id;

		$this->allback = $allback;

		$this->_getportlists($port_id);

		if(!$allports)
			if($this->count == 0 || ( ($this->count == 1) && (!empty($this->list[$port_id]['back'])) ) ) {
				$this->list = array();
				$this->first_id = NULL;
			}

	//	$this->var_dump_html($this->list);

	} /* __construct */


	/*
	 * get front and back portlist
	 */
	function _getportlists($port_id) {

		/* Front Port */
		$this->count = 0;
		$this->_getportlist($this->_getportdata($port_id),FALSE, TRUE);
		$this->front_count = $this->count;

		/* Back Port */
		$this->count = 0;
		$this->_getportlist($this->_getportdata($port_id), TRUE, FALSE);
		$this->back_count = $this->count;

		$this->count = $this->front_count + $this->back_count;
	}

	/*
         * gets front or back port of src_port
	 * and adds it to the list
	 */
	/* !!! recursive */
	function _getportlist(&$src_port, $back = FALSE, $first = TRUE) {

		$src_port_id = $src_port['id'];

		if($back)
			$linktype = 'back';
		else
			$linktype = 'front';

		if(!empty($src_port[$linktype])) {

			/* multilink */
			foreach($src_port[$linktype] as &$src_link) {
				$dst_port_id = $src_link['id'];

				if(!$this->_loopdetect($src_port,$dst_port_id,$src_link,$linktype)) {
					//error_log("no loop $linktype>".$dst_port_id);
					$this->count++;
					$this->_getportlist($this->_getportdata($dst_port_id), !$back, $first);
				}
			}

		} else {
			if($first) {
				$this->first_id = $src_port_id;
			//	$this->front_count = $this->count; /* doesn't work on loops */
			} else {
				$this->last_id = $src_port_id;
			//	$this->back_count = $this->count; /* doesn't work on loops */
			}

		}

	} /* _getportlist */

	/*
	 * as name suggested
	 */
	function _loopdetect(&$src_port, $dst_port_id, &$src_link, $linktype) {

		/* TODO multilink*/
		if(array_key_exists($dst_port_id, $this->list)) {

		//	$dst_port = $this->list[$dst_port_id];

			//echo "LOOP :".$src_port['id']."-->".$dst_port_id;

			/* print loop at least once */
			if($dst_port_id == $this->port_id)
			{
				$src_link['loop'] = $dst_port_id;
				return TRUE;
			}

		}

		return FALSE;

	} /* _loopdetect */

	/*
	 * get all data for one port
	 *	name, object, front link, back link
	 */
	function &_getportdata($port_id) {
		/* sql bitwise xor: porta ^ portb */
		//select cable, ((porta ^ portb) ^ 4556) as port from Link where (4556 in (porta, portb));

		//error_log("_getportdata $port_id");

		/* TODO single sql ? */

		$result = usePreparedSelectBlade
		(
			'SELECT Port.id, Port.name, Port.label, Port.type, Port.l2address, Port.object_id, Port.reservation_comment,
					RackObject.name as "obj_name"
				 from Port
				 join RackObject on RackObject.id = Port.object_id
				 where Port.id = ?',
				array($port_id)
		);
		$datarow = $result->fetchAll(PDO::FETCH_ASSOC);

		$result = usePreparedSelectBlade
		(
				'SELECT Port.id, Link.cable, Port.name, Port.label, Port.type, Port.l2address, Port.object_id,
				 CONCAT(Link.porta,"_",Link.portb) as link_id from Link
				 join Port
				 where (? in (Link.porta,Link.portb)) and ((Link.porta ^ Link.portb) ^ ? ) = Port.id',
				array($port_id, $port_id)
		);
		$frontrow = $result->fetchAll(PDO::FETCH_ASSOC);

		$result = usePreparedSelectBlade
		(
				'SELECT Port.id, LinkBackend.cable, Port.name, Port.label, Port.type, Port.l2address, Port.object_id,
				 CONCAT(LinkBackend.porta,"_",LinkBackend.portb,"_back") as link_id from LinkBackend
				 join Port
				 where (? in (LinkBackend.porta,LinkBackend.portb)) and ((LinkBackend.porta ^ LinkBackend.portb) ^ ? ) = Port.id',
				array($port_id, $port_id)
		);
		$backrow = $result->fetchAll(PDO::FETCH_ASSOC);

		$retval = $datarow[0];

		if(!empty($frontrow))
			$retval['front']= $frontrow;
		else
			$retval['front'] = array();

		if(!empty($backrow))
			$retval['back'] = $backrow;
		else
			$retval['back'] = array();

	//	$this->var_dump_html($retval);

		/* return reference */
		return ($this->list[$port_id] = &$retval);

	} /* _getportdata */

	/*
	 */
	function printport(&$port, $multilink = false) {

		/* set bgcolor for current port */
		if($port['id'] == $this->port_id) {
			$bgcolor = 'bgcolor='.self::CURRENT_PORT_BGCOLOR;
			$idtag = ' id='.$port['id'];
		} else {
			$bgcolor = '';
			$idtag = '';
		}

		$mac = trim(preg_replace('/(..)/','$1:',$port['l2address']),':');

		$title = "Label: ${port['label']}\nMAC: $mac\nTypeID: ${port['type']}\nPortID: ${port['id']}";

		echo '<td'.$idtag.' align=center '.$bgcolor.' title="'.$title.'"><pre>[<a href="'
			.makeHref(array('page'=>'object', 'tab' => 'linkmgmt', 'object_id' => $port['object_id'], 'hl_port_id' => $port['id']))
			.'#'.$port['id']
			.'">'.$port['name'].'</a>]</pre>'.($multilink ? $this->_getlinkportsymbol($port['id'], 'back') : '' ).'</td>';

	} /* printport */

	/*
	 */
	function printcomment(&$port) {

		if(!empty($port['reservation_comment'])) {
			$prefix = '<b>Reserved: </b>';
		} else
			$prefix = '';

		echo '<td>'.$prefix.'<i><a class="editcmt" id='.$port['id'].'>'.$port['reservation_comment'].'</a></i></td>';

	} /* printComment */


	/*
	 */
	function printobject($object_id, $object_name) {
		if($object_id == $this->object_id) {
                        $color='color: '.self::CURRENT_OBJECT_BGCOLOR;
                } else {
                        $color='';
                }

                echo '<td><table align=center cellpadding=5 cellspacing=0 border=1><tr><td align=center><a style="font-weight:bold;'
                        .$color.'" href="'.makeHref(array('page'=>'object', 'tab' => 'linkmgmt', 'object_id' => $object_id))
                        .'"><pre>'.$object_name.'</pre></a><pre>'.$this->_getRackInfo($object_id, 'font-size:80%')
                        .'</pre></td></tr></table></td>';

	} /* printobject */

	/*
	 */
	function printlink($src_port_id, &$dst_link, $linktype) {

		if($linktype == 'back')
			$arrow = '====>';
		else
			$arrow = '---->';

		/* link */
		echo '<td align=center>';

		echo '<pre><a class="editcable" id='.$dst_link['link_id'].'>'.$dst_link['cable']
			."</a></pre><pre>$arrow</pre>"
			.$this->_printUnLinkPort($src_port_id, $dst_link, $linktype);

		echo '</td>';
	} /* printlink */

	/*
	 * print cableID dst_port:dst_object
	 */
	function _printportlink($src_port_id, $dst_port_id, &$dst_link, $back = FALSE) {

		global $lm_multilink_port_types;

		$multilink = MULTILINK;

	if(!isset($this->list[$dst_port_id]))
	{
		/* get port not in list */
	//	echo "<td>AHHH $src_port_id $dst_port_id --> $back</td>";
	//	echo "<td>load".$this->var_dump_html($dst_link)." tree</td>";
//		echo "<td>".$dst_link['cable']." ".$dst_link['name']."</td><td>not displayed</td>";

		if($back)
			echo "<td>></td>";

		// TODO check if multilink is needed here
		$this->printport($dst_link, $multilink && in_array($dst_link['type'], $lm_multilink_port_types));
		echo "<td>...</td>";

		return TRUE;

	//	$this->_getportlist($this->list[$src_port_id], $back, !$back);
	}

	$dst_port = $this->list[$dst_port_id];
	$object_id = $dst_port['object_id'];
	$obj_name = $dst_port['obj_name'];

	$loop = FALSE;
	$edgeport = ($dst_link == NULL) || empty($dst_port['front']) || empty($dst_port['back']);

	if($back) {
		$linktype = 'back';
	} else {
		$linktype = 'front';
	}

	$sameobject = FALSE;

	if(isset($dst_link['loop']))
		$loop = TRUE;

	if($dst_link != NULL) {

		$src_object_id = $this->list[$src_port_id]['object_id'];

		if(!$this->allback && $object_id == $src_object_id && $back) {
			$sameobject = TRUE;
		} else {
			$this->printlink($src_port_id, $dst_link, $linktype);
		}

	} else {
		$this->_printlinkportsymbol($dst_port_id, $linktype);
		$edgeport = true;

			if(!$back)
				$this->printcomment($dst_port);
		}

		if($back) {
			if(!$sameobject)
				$this->printobject($object_id,$obj_name);

			echo "<td>></td>";

			/* align ports nicely */
			if($dst_port['id'] == $this->port_id)
				echo '</td></tr></table id=printportlink1></td><td><table align=left><tr>';
		}

		/* print [portname] */
		// TODO check multilink symbols front/back edgeports
		$this->printport($dst_port, $multilink && in_array($dst_port['type'], $lm_multilink_port_types));

		if($loop)
			echo '<td bgcolor=#ff9966>LOOP</td>';

		if(!$back) {

			/* align ports nicely */
			if($dst_port['id'] == $this->port_id)
				echo '</td></tr></table id=printportlink2></td><td><table align=left><tr>';

			echo "<td><</td>";
			$this->printobject($object_id,$obj_name);

			if(empty($dst_port['back']))
				$this->_printlinkportsymbol($dst_port_id, 'back');
		} else
			if(empty($dst_port['front'])) {
				$this->printcomment($dst_port);
				$this->_printlinkportsymbol($dst_port_id, 'front');
			}

		if($loop) {
			if(isset($dst_link['loopmaxcount']))
				$reason = " (MAX LOOP COUNT reached)";
			else
				$reason = '';

			showWarning("Possible Loop on Port ($linktype) ".$dst_port['name'].$reason);
			return FALSE;
		}

		return TRUE;

	} /* _printportlink */

	/*
	 * print <tr>..</tr>
	 */
	function printportlistrow($first = TRUE, $hl_port_id = NULL, $rowbgcolor = '#ffffff') {

		$this->loopcount = 0;

		if($this->first_id == NULL)
			return false;

		if($first)
			$id = $this->first_id;
		else
			$id = $this->last_id;

		if($hl_port_id == $this->port_id)
			$hlbgcolor = "bgcolor=".self::HL_PORT_BGCOLOR;
		else
			$hlbgcolor = "bgcolor=$rowbgcolor";

		$link = NULL;

		$port = $this->list[$id];

		$urlparams = array(
				'module' => 'redirect',
				'page' => 'object',
				'tab' => 'linkmgmt',
				'op' => 'map',
				'usemap' => 1,
				'object_id' => $port['object_id'],
				);

		if($hl_port_id !== NULL)
			$urlparams['hl_port_id'] = $hl_port_id;
		else
			$urlparams['port_id'] = $id;

		$title = "linkcount: ".$this->count." (".$this->front_count."/".$this->back_count.")\nTypeID: ${port['type']}\nPortID: $id";

		$onclick = 'onclick=window.open("'.makeHrefProcess(portlist::urlparamsarray(
                                $urlparams)).'","Map","height=500,width=800,scrollbars=yes");';

		/* Current Port */
		echo '<tr '.$hlbgcolor.'><td nowrap="nowrap" bgcolor='.self::CURRENT_PORT_BGCOLOR.' title="'.$title.
			'"><a '.$onclick.'>'.
			$this->port['name'].': </a></td>';

		echo "<td><table id=printportlistrow1 align=right><tr><td>";

		$back = empty($this->list[$id]['back']);

		$this->_printportlink(NULL, $id, $link, $back);

		$this->_printportlist($id, !$back);
		echo "</td></tr></table id=printportlistrow2></td></tr>";

		/* horizontal line */
                echo '<tr><td height=1 colspan=3 bgcolor=#e0e0e0></td></tr>';

		return true;

	} /* printportlist */

	/*
	 * print <td>
	 * prints all ports in a list starting with start_port_id
	 */
	/* !!! recursive */
	function _printportlist($src_port_id, $back = FALSE) {

                if($back)
                        $linktype = 'back';
                else
                        $linktype = 'front';

		if(!empty($this->list[$src_port_id][$linktype])) {

			$linkcount = count($this->list[$src_port_id][$linktype]);

			if($linkcount > 1)
				echo "<td bgcolor=#f00000></td><td><table id=_printportlist1>";

			$lastkey = $linkcount - 1;

			foreach($this->list[$src_port_id][$linktype] as $key => &$link) {

				if($linkcount > 1) {
					echo "<tr style=\"background-color:".( $key % 2 ? self::ALTERNATE_ROW_BGCOLOR : "#ffffff" )."\"><td><table id=_printportlist2><tr>";
				}

				$dst_port_id = $link['id'];

				$this->loopcount++;

				if($this->loopcount > self::MAX_LOOP_COUNT) {
				//	$src_port_name = $this->list[$src_port_id]['name'];
				//	$dst_port_name = $this->list[$dst_port_id]['name'];

					$link['loop'] = $dst_port_id;
					$link['loopmaxcount'] = $dst_port_id;

					/* loop warning is handeld in _printportlink() */
					//showWarning("MAX LOOP COUNT reached $src_port_name -> $dst_port_name".self::MAX_LOOP_COUNT);
					//return; /* return after _printportlink */
				}

				if(!$this->_printportlink($src_port_id, $dst_port_id, $link, $back))
				{
					return;
				}

				$this->_printportlist($dst_port_id,!$back);
			
				if($linkcount > 1) {
					echo "</tr></table></td></tr>"
						.( $key != $lastkey ? "<tr><td height=1 colspan=100% bgcolor=#c0c0c0><td></tr>" : "");
				}
			}

			if($linkcount > 1)
				echo "</table></td>";
		}
	} /* _printportlist */

	/*
         *  returns linked Row / Rack Info for object_id
         *
         */
        function _getRackInfo($object_id, $style = '') {
                global $lm_cache;

                $rackinfocache = $lm_cache['rackinfo'];

                /* if not in cache get it */
                if(!array_key_exists($object_id,$rackinfocache)) {

                        /* SQL from database.php SQLSchema 'object' */
                        $result = usePreparedSelectBlade
                        (
                                'SELECT rack_id, Rack.name as Rack_name, row_id, Row.name as Row_name
                                FROM RackSpace
                                LEFT JOIN EntityLink on RackSpace.object_id = EntityLink.parent_entity_id
                                JOIN Rack on Rack.id = RackSpace.rack_id
                                JOIN Row on Row.id = Rack.row_id
                                WHERE ( RackSpace.object_id = ? ) or (EntityLink.child_entity_id = ?)
                                ORDER by rack_id asc limit 1',
                                array($object_id, $object_id)
                         );
                         $row = $result->fetchAll(PDO::FETCH_ASSOC);

                        if(!empty($row)) {

                                $rackinfocache[$object_id] = $row[0];
                        }

                }

                $obj = &$rackinfocache[$object_id];

                if(empty($obj))
                        return  '<span style="'.$style.'">Unmounted</span>';
                else
                        return '<a style="'.$style.'" href='.makeHref(array('page'=>'row', 'row_id'=>$obj['row_id'])).'>'.$obj['Row_name']
                                .'</a>/<a style="'.$style.'" href='.makeHref(array('page'=>'rack', 'rack_id'=>$obj['rack_id'])).'>'
                                .$obj['Rack_name'].'</a>';

        } /* _getRackInfo */

	/*
	 * return link symbol
	 */
	function _getlinkportsymbol($port_id, $linktype) {
		$retval = '<span onclick=window.open("'.makeHrefProcess(portlist::urlparamsarray(
			array('op' => 'PortLinkDialog','port' => $port_id,'linktype' => $linktype ))).'","name","height=800,width=800");'
		        .'>';

                $img = getImageHREF ('plug', $linktype.' Link this port');

		if($linktype == 'back')
			$img = str_replace('<img',
				'<img style="transform:rotate(180deg);-o-transform:rotate(180deg);-ms-transform:rotate(180deg);-moz-transform:rotate(180deg);-webkit-transform:rotate(180deg);"',
				$img);

		$retval .= $img;
		$retval .= "</span>";
		return $retval;

	} /* _getlinkportsymbol */

	/*
	 * print link symbol
	 *
	 */
       function _printlinkportsymbol($port_id, $linktype = 'front') {
		global $lm_cache;

		if(!$lm_cache['allowlink'])
			return;

                echo "<td align=center>";

		echo $this->_getlinkportsymbol($port_id, $linktype);

		echo "</td>";

        } /* _printlinkportsymbol */

	/*
	 * return link cut symbol
	 *
         * TODO $opspec_list
	 */
	function _printUnLinkPort($src_port_id, &$dst_link, $linktype) {
		global $lm_cache;

		if(!$lm_cache['allowlink'])
			return '';

		$src_port = $this->list[$src_port_id];

		$dst_port = $this->list[$dst_link['id']];

		/* use RT unlink for front link, linkmgmt unlink for back links */
		if($linktype == 'back')
			$tab = 'linkmgmt';
		else
			$tab = 'ports';

		return '<a href='.
                               makeHrefProcess(array(
					'op'=>'unlinkPort',
					'port_id'=>$src_port_id,
					'remote_id' => $dst_port['id'],
					'object_id'=>$this->object_id,
					'tab' => $tab,
					'linktype' => $linktype)).
                       ' onclick="return confirm(\'unlink ports '.$src_port['name']. ' -> '.$dst_port['name']
					.' ('.$linktype.') with cable ID: '.$dst_link['cable'].'?\');">'.
                       getImageHREF ('cut', $linktype.' Unlink this port').'</a>';

	} /* _printUnLinkPort */


	/*
	 *
         */
	static function urlparams($name, $value, $defaultvalue = NULL) {

                $urlparams = $_GET;

	        if($value == $defaultvalue) {

			/* remove param */
			unset($urlparams[$name]);

		} else {

			$urlparams[$name] = $value;

		}

                return $urlparams;

        } /* urlparams */

	/*
         * $params = array('name' => 'value', ...)
         */
	static function urlparamsarray($params) {

                $urlparams = $_GET;

		foreach($params as $name => $value) {

	                if($value == NULL) {

				/* remove param */
				unset($urlparams[$name]);

			} else {

				$urlparams[$name] = $value;

			}
		}

                return $urlparams;

        } /* urlparamsarray */

	/* */
	static function hasbackend($object_id) {
		/* sql bitwise xor: porta ^ portb */
		//select cable, ((porta ^ portb) ^ 4556) as port from Link where (4556 in (porta, portb));

		$result = usePreparedSelectBlade
		(
				'SELECT count(*) from Port
				 join LinkBackend on (porta = id or portb = id )
				 where object_id = ?',
				array($object_id)
		);
		$retval = $result->fetchColumn();

		return $retval != 0;

	} /* hasbackend */

	/* for debugging only */
	function var_dump_html(&$var) {
		echo "<pre>------------------Start Var Dump -------------------------\n";
		var_dump($var);
		echo "\n---------------------END Var Dump ------------------------</pre>";
	}

} /* portlist */

/* -------------------------------------------------- */

?>
