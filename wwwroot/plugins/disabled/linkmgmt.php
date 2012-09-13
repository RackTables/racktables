<?php
/*
 * Link Management
 *
 *  Displays links between Ports.
 *  Allows you to create/delete front and backend links between ports
 *	Link object backend ports by name (e.g. handy for patch panels)
 *  Change CableIDs
 *  Change Port Reservation Comment
 *
 *  
 * e.g.
 * 	(Object)>[port] -- front --> [port]<(Object) == back == > (Object)>[port] -- front --> [port]<(Object)
 *
 *
 *
 * INSTALL:
 *
 *	- create LinkBackend Table

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

 *	- copy linkmgmt.php to inc/ directory
 *	- copy jquery.jeditable.mini.js to js/ directory
 * 	- add "include 'inc/linkmgmt.php';" to inc/local.php
 *
 * TESTED on FreeBSD 9.0, nginx/1.0.11, php 5.3.9
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
 *
 *
 */

/*************************
 * TODO
 *
 * - code cleanups
 * - bug fixing
 *
 * - csv list
 *
 * - fix $opspec_list for unlink
 *
 */

require_once 'inc/popup.php';

$tab['object']['linkmgmt'] = 'Link Management';
$tabhandler['object']['linkmgmt'] = 'linkmgmt_tabhandler';
//$trigger['object']['linkmgmt'] = 'linkmgmt_tabtrigger';

$ophandler['object']['linkmgmt']['update'] = 'linkmgmt_opupdate';
//$ophandler['object']['linkmgmt']['linkPort'] = 'linkmgmt_oplinkPort';
$ophandler['object']['linkmgmt']['unlinkPort'] = 'linkmgmt_opunlinkPort';
$ophandler['object']['linkmgmt']['PortLinkDialog'] = 'linkmgmt_opPortLinkDialog';
$ophandler['object']['linkmgmt']['Help'] = 'linkmgmt_opHelp';


/* -------------------------------------------------- */

$lm_cache = array(
		'rackinfo' => array()
		);

/* -------------------------------------------------- */

//function linkmgmt_tabtrigger() {
//	return 'std';
//}
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

function linkmgmt_opupdate() {
	
	if(!isset($_POST['id']))
		exit;
	
	$ids = preg_split('/[^0-9]/',$_POST['id']);
	$retval = strip_tags($_POST['value']);

	if(isset($ids[1]))
		portlist::_updatecable($ids[0],$ids[1], $retval);
	else
		commitUpdatePortComment($ids[0], $retval);
		
	/* return what jeditable should display after edit */
	echo $retval;
	exit;	
} /* opupdate */

/* -------------------------------------------------- */

function linkmgmt_opunlinkPort() {
	$port_id = $_REQUEST['port_id'];
	$linktype = $_REQUEST['linktype'];

	if($linktype == 'back')
		$table = 'LinkBackend';
	else
		$table = 'Link';

	$retval = usePreparedDeleteBlade ($table, array('porta' => $port_id, 'portb' => $port_id), 'OR');
	
	if($retval == 0)
		echo " Link not found";
	else
		echo " $retval Links deleted";

	header('Location: ?page='.$_REQUEST['page'].'&tab='.$_REQUEST['tab'].'&object_id='.$_REQUEST['object_id']);
	exit;
} /* opunlinkPort */

/* -------------------------------------------------- */

function linkmgmt_oplinkPort() {

	$linktype = $_REQUEST['linktype'];
	$cable = $_REQUEST['cable'];

	if(!isset($_REQUEST['link_list'])) {
		//portlist::var_dump_html($_REQUEST);
		$porta = $_REQUEST['port'];
		$portb = $_REQUEST['remote_port'];

		$link_list[] = "${porta}_${portb}";
	} else
		$link_list = $_REQUEST['link_list'];

	foreach($link_list as $link){
	
		$ids = preg_split('/[^0-9]/',$link);
		$porta = $ids[0];;
		$portb = $ids[1];

		$ret = linkmgmt_linkPorts($porta, $portb, $linktype, $cable);

		error_log("$ret - $porta - $portb");
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
		$table = 'LinkBackend';
	else
		$table = 'Link';

        global $dbxlink;
        $dbxlink->exec ('LOCK TABLES '.$table.' WRITE');
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

	if (isset ($_REQUEST['do_link'])) {
        	$text .= getOutputOf ('linkmgmt_oplinkPort');
	}
        else
		if(isset($_REQUEST['byname']))
        		$text .= getOutputOf ('linkmgmt_renderPopupPortSelectorbyName');
		else
        		$text .= getOutputOf ('linkmgmt_renderPopupPortSelector');

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
 */
function linkmgmt_findSparePorts($port_info, $filter, $linktype) {
	
	// all ports with no backend link
 	/* port:object -> front linked port:object */	
	$query = 'select Port.id, CONCAT(RackObject.name, " : ", Port.name, 
			IFNULL(CONCAT(" -- ", Link.cable," --> ",lnkPort.name, " : ", lnkObject.name),"") ) 
		from Port
		left join LinkBackend on Port.id in (LinkBackend.porta,LinkBackend.portb)
		left join RackObject on RackObject.id = Port.object_id
		left join Link on Port.id in (Link.porta, Link.portb)
		left join Port as lnkPort on lnkPort.id = ((Link.porta ^ Link.portb) ^ Port.id)
		left join RackObject as lnkObject on lnkObject.id = lnkPort.object_id';

	$qparams = array();

	 // self and linked ports filter
        $query .= " WHERE Port.id <> ? ". 
		    "AND LinkBackend.porta is NULL ";
        $qparams[] = $port_info['id'];

	 // rack filter
        if (! empty ($filter['racks']))
        {
                $query .= 'AND Port.object_id IN (SELECT DISTINCT object_id FROM RackSpace WHERE rack_id IN (' .
                        questionMarks (count ($filter['racks'])) . ')) ';
                $qparams = array_merge ($qparams, $filter['racks']);
        }

	// objectname filter
        if (! empty ($filter['objects']))
        {
                $query .= 'AND RackObject.name like ? ';
                $qparams[] = '%' . $filter['objects'] . '%';
        }
        // portname filter
        if (! empty ($filter['ports']))
        {
                $query .= 'AND Port.name LIKE ? ';
                $qparams[] = '%' . $filter['ports'] . '%';
        }
        // ordering
        $query .= ' ORDER BY RackObject.name';


	$result = usePreparedSelectBlade ($query, $qparams);

	$row = $result->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_UNIQUE|PDO::FETCH_COLUMN);

	/* [id] => displaystring */	
	return $row;
	
} /* findSparePorts */

/* -------------------------------------------------- */

/*
 * similar to findSparePorts but finds Ports with same name
 */
function linkmgmt_findSparePortsbyName($object_id, $remote_object, $linktype) {
	
	// all ports with same name on object and remote_object and without existing backend link
	$query = 'select CONCAT(Port.id,"_",rPort.id), CONCAT(RackObject.name, " : ", Port.name, " -?-> ", rPort.name, " : ", rObject.name) 
		from Port
		left join LinkBackend on Port.id in (LinkBackend.porta,LinkBackend.portb)
		left join RackObject on RackObject.id = Port.object_id
		left join Port as rPort on rPort.name = Port.Name
		left join RackObject as rObject on rObject.id = rPort.object_id
		left join LinkBackend as rLinkBackend on rPort.id in (rLinkBackend.porta, rLinkBackend.portb)';

	$qparams = array();

	 // self and linked ports filter
        $query .= " WHERE Port.object_id = ? ". 
		  "AND rPort.object_id = ? ".
		  "AND LinkBackend.porta is NULL ".
		  "AND rLinkBackend.porta is NULL ";
        $qparams[] = $object_id;
        $qparams[] = $remote_object;

        // ordering
        $query .= ' ORDER BY Port.name';

	$result = usePreparedSelectBlade ($query, $qparams);

	$row = $result->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_UNIQUE|PDO::FETCH_COLUMN);

	/* [id] => displaystring */	
	return $row;
	
} /* findSparePortsbyName */

/* -------------------------------------------------- */

/*
 * like renderPopupPortSelector in popup.php extenden with linktype
 */
function linkmgmt_renderPopupPortSelector()
{
        assertUIntArg ('port');
        $port_id = $_REQUEST['port'];
	$linktype = $_REQUEST['linktype'];
	$object_id = $_REQUEST['object_id'];
        $port_info = getPortInfo ($port_id);
        $in_rack = isset ($_REQUEST['in_rack']);

//	portlist::var_dump_html($port_info);
//	portlist::var_dump_html($_REQUEST);

        // fill port filter structure
        $filter = array
        (
                'racks' => array(),
                'objects' => '',
                'ports' => '',
        );
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
        $spare_ports = array();
        if
        (
                $in_rack ||
                ! empty ($filter['objects']) ||
                ! empty ($filter['ports'])
        )
                $spare_ports = linkmgmt_findSparePorts ($port_info, $filter, $linktype);

        // display search form
        echo 'Link '.$linktype.' of ' . formatPort ($port_info) . ' to...';
        echo '<form method=POST>';
        startPortlet ($linktype.' Port list filter');
       // echo '<input type=hidden name="module" value="popup">';
       // echo '<input type=hidden name="helper" value="portlist">';

        echo '<input type=hidden name="port" value="' . $port_id . '">';
        echo '<table align="center" valign="bottom"><tr>';
        echo '<td class="tdleft"><label>Object name:<br><input type=text size=8 name="filter-obj" value="' . htmlspecialchars ($filter['objects'], ENT_QUOTES) . '"></label></td>';
        echo '<td class="tdleft"><label>Port name:<br><input type=text size=6 name="filter-port" value="' . htmlspecialchars ($filter['ports'], ENT_QUOTES) . '"></label></td>';
        echo '<td class="tdleft" valign="bottom"><label><input type=checkbox name="in_rack"' . ($in_rack ? ' checked' : '') . '>Nearest racks</label></td>';
        echo '<td valign="bottom"><input type=submit value="show '.$linktype.' ports"></td>';
        echo '</tr></table>';
        finishPortlet();

        // display results
        startPortlet ('Compatible spare '.$linktype.' ports');
	echo('back Object:Port -- front cableID --> front Port:Object');

        if (empty ($spare_ports))
                echo '(nothing found)';
        else
        {
                echo getSelect ($spare_ports, array ('name' => 'remote_port', 'size' => getConfigVar ('MAXSELSIZE')), NULL, FALSE);
                echo "<p>$linktype Cable ID: <input type=text id=cable name=cable>";
                echo "<p><input type='submit' value='Link $linktype' name='do_link'>";
        }
        finishPortlet();
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

	if(isset($_REQUEST['remote_object']))
		$remote_object = $_REQUEST['remote_object'];
	else
		$remote_object = NULL;

	if($remote_object)
		$link_list = linkmgmt_findSparePortsbyName($object_id, $remote_object, $linktype);

        // display search form
        echo 'Link '.$linktype.' of ' . formatPortLink($object_id, $object['name'], NULL, NULL) . ' Ports by Name to...';
        echo '<form method=POST>';
        startPortlet ('Object list');

	$objectlist = getNarrowObjectList();

	/* remove self from list */
	unset($objectlist[$object_id]);

        echo '<table align="center" valign="bottom"><tr>';
        echo getSelect ($objectlist, array ('name' => 'remote_object', 'size' => getConfigVar ('MAXSELSIZE')), NULL, FALSE);
        echo '<td valign="bottom"><input type=submit value="show '.$linktype.' ports"></td>';
        echo '</tr></table>';
        finishPortlet();

        // display results
        startPortlet ('Possible Backend Link List');
	echo "Select links to create:<br>";
        if (empty ($link_list))
                echo '(nothing found)';
        else
        {
                echo getSelect ($link_list, array ('name' => 'link_list[]', 'multiple' => 'multiple','size' => getConfigVar ('MAXSELSIZE')), NULL, FALSE);
                echo "<p>$linktype Cable ID: <input type=text id=cable name=cable>";
                echo "<p><input type='submit' value='Link $linktype' name='do_link'>";
        }
        finishPortlet();
        echo '</form>';

} /* linkmgmt_renderPopUpPortSelector */

/* ------------------------------------------------ */ 

function linkmgmt_tabhandler($object_id) {

	$target = makeHrefProcess(portlist::urlparams('op','update'));

 	addJS('js/jquery.jeditable.mini.js');

	/* init jeditable fields/tags */
	addJS(<<<END
$(document).ready(function() { $(".editcmt").editable("$target",{placeholder : "add comment"}); });
$(document).ready(function() { $(".editcable").editable("$target",{placeholder : "edit cableID"}); });
END
		, TRUE);

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

//	$plist->var_dump_html($plist->list);


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
                                array('op' => 'PortLinkDialog','linktype' => 'back','byname' => '1'))).'","name","height=800,width=400");><a>Link Object Ports by Name</a></span></td>';

	if($allback) {
				
		echo '<td width=200><a href="'.makeHref(portlist::urlparams('allback','0','0'))
			.'">Collapse Backend Links on same Object</a></td>';
	} else
		echo '<td width=200><a href="'.makeHref(portlist::urlparams('allback','1','0'))
			.'">Expand Backend Links on same Object</a></td>';

	/* Help */
	echo '<td width=200><span onclick=window.open("'.makeHrefProcess(portlist::urlparamsarray(
                                array('op' => 'Help'))).'","name","height=400,width=500");><a>Help</a></span></td>';

	if(isset($_REQUEST['hl_port_id']))
		$hl_port_id = $_REQUEST['hl_port_id'];
	else
		$hl_port_id = NULL;	

	echo '</tr></table>';

	echo '<br><br><table>';

	foreach($ports as $port) {

		$plist = new portlist($port, $object_id, $allports, $allback);

		$plist->printportlistrow(TRUE, $hl_port_id);
		
	}

	echo "</table>";

} /* renderObjectLinks */

/* --------------------------------------------------- */
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

	const B2B_LINK_BGCOLOR = '#d8d8d8';
	const CURRENT_PORT_BGCOLOR = '#ffff99';
	const CURRENT_OBJECT_BGCOLOR = '#ff0000';
	const HL_PORT_BGCOLOR = '#00ff00';

	/* Possible LOOP detected after count links print only */
	const MAX_LOOP_COUNT = 13;

	private $loopcount;

	function __construct($port, $object_id, $allports = FALSE, $allback = FALSE) {
		

		$this->object_id = $object_id;

		$this->port = $port;

		$port_id = $port['id'];
		
		$this->port_id = $port_id;
		
		$this->first_id = $port_id;
		$this->last_id = $port_id;

		$this->allback = $allback;

		/* Front Port */
		$this->count = 0;
		$this->_getportlist($this->_getportdata($port_id),FALSE);
		$this->front_count = $this->count;

		/* Back Port */
		$this->count = 0;
		$this->_getportlist($this->_getportdata($port_id), TRUE, FALSE);
		$this->back_count = $this->count;

		$this->count = $this->front_count + $this->back_count;


		if(!$allports)
			if($this->count == 0 || ( ($this->count == 1) && (!empty($this->list[$port_id]['back'])) ) ) {
				$this->list = array();
				$this->first_id = NULL;
			}

		//$this->var_dump_html($this->list);

	} /* __construct */


	/*
         * gets front and back port of src_port
	 * and adds it to the list 
	 */
	/* !!! recursive */
	function _getportlist(&$src_port, $back = FALSE, $first = TRUE) {
		
		$id = $src_port['id'];

		if($back) 
			$linktype = 'back';
		else
			$linktype = 'front';
		
		if(!empty($src_port[$linktype])) {

			$dst_port_id = $src_port[$linktype]['id'];

			if(!$this->_loopdetect($src_port,$dst_port_id,$linktype)) {
				//error_log("no loop $linktype>".$dst_port_id);
				$this->count++;
				$this->_getportlist($this->_getportdata($dst_port_id), !$back, $first);
			}
		} else {
			if($first) {
				$this->first_id = $id;
			//	$this->front_count = $this->count; /* doesn't work on loops */
			} else {
				$this->last_id = $id;
			//	$this->back_count = $this->count; /* doesn't work on loops */
			}

		}

	} /* _getportlist */	

	/*
	 * as name suggested
	 */
	function _loopdetect(&$src_port, $dst_port_id, $linktype) {

		/* */
		if(array_key_exists($dst_port_id, $this->list)) {

			$dst_port = $this->list[$dst_port_id];

			$src_port[$linktype]['loop'] = $dst_port_id;

		//	echo "LOOP :".$src_port['id']."-->".$dst_port_id;

			return TRUE;
	
		} else {
			//error_log(__FUNCTION__."$dst_port_id not exists");
			return FALSE;
		}
		
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
				'SELECT Port.id, Link.cable, Port.name,
				 CONCAT(Link.porta,"_",Link.portb) as link_id from Link
				 join Port
				 where (? in (Link.porta,Link.portb)) and ((Link.porta ^ Link.portb) ^ ? ) = Port.id',
				array($port_id, $port_id)
       		 );
       		 $frontrow = $result->fetchAll(PDO::FETCH_ASSOC);

      		$result = usePreparedSelectBlade
       		(
				'SELECT Port.id, LinkBackend.cable, Port.name,
				 CONCAT(LinkBackend.porta,"_",LinkBackend.portb) as link_id from LinkBackend
				 join Port
				 where (? in (LinkBackend.porta,LinkBackend.portb)) and ((LinkBackend.porta ^ LinkBackend.portb) ^ ? ) = Port.id',
				array($port_id, $port_id)
       		 );
       		 $backrow = $result->fetchAll(PDO::FETCH_ASSOC);

		$retval = $datarow[0];

		if(!empty($frontrow))
			$retval['front']= $frontrow[0];
		else
			$retval['front'] = array();

		if(!empty($backrow))
			$retval['back'] = $backrow[0];
		else
			$retval['back'] = array();

	//	$this->var_dump_html($retval);

		/* return reference */
		return ($this->list[$port_id] = &$retval);

	} /* _getportdata */

	/*
	 */
	function printport(&$port) {
		/* set bgcolor for current port */
		if($port['id'] == $this->port_id) { 
			$bgcolor = 'bgcolor='.self::CURRENT_PORT_BGCOLOR;
			$idtag = ' id='.$port['id'];
		} else {
			$bgcolor = '';
			$idtag = '';
		}

		$mac = trim(preg_replace('/(..)/','$1:',$port['l2address']),':');

		$title = "Label: ${port['label']}\nMAC: $mac\nPortID: ${port['id']}";

		echo '<td'.$idtag.' align=center '.$bgcolor.' title="'.$title.'"><pre>[<a href="'
			.makeHref(array('page'=>'object', 'tab' => 'linkmgmt', 'object_id' => $port['object_id'], 'hl_port_id' => $port['id']))
			.'#'.$port['id']
			.'">'.$port['name'].'</a>]</pre></td>';

	} /* printport */

	/*
	 */
	function printcomment(&$port) {
		
		if(!empty($port['reservation_comment'])) {
			$prefix = '<b>Reserved: </b>';
		} else
			$prefix = '';

		echo '<td>'.$prefix.'<i><a class=editcmt id='.$port['id'].'>'.$port['reservation_comment'].'</a></i></td>';

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
	function printlink(&$link, $linktype) {

		if($linktype == 'back')
			$arrow = '====>';
		else
			$arrow = '---->';

		/* link */
		echo '<td align=center>';

		echo '<pre><a class=editcable id='.$link['link_id'].'>'.$link['cable']
			."</a></pre><pre>$arrow</pre>"
			.$this->_printUnLinkPort($link['id'], $linktype);

		echo '</td>';
	} /* printlink */

	/*
	 * print cableID dst_port:dst_object
	 */
	function _printportlink($port_id, &$link, $back = FALSE) {

		//$port_id = $link['id'];

		$port = $this->list[$port_id];
		$object_id = $port['object_id'];
		$obj_name = $port['obj_name'];

		$loop = FALSE;

		if($back) {
			$linktype = 'back';
		} else {
			$linktype = 'front';
		}

		$sameobject = FALSE;

		if(isset($link['loop']))
			$loop = TRUE;

		if($link != NULL) {

			$src_port_id = $port[$linktype]['id'];
			$src_object_id = $this->list[$src_port_id]['object_id'];

			if(!$this->allback && $object_id == $src_object_id && $back) {
				$sameobject = TRUE;
			} else {
				$this->printlink($link, $linktype);
			}

		} else {
			$this->_LinkPort($port_id, $linktype);

			if(!$back)
				$this->printcomment($port);
		} 	

		if($back) {
			if(!$sameobject)
				$this->printobject($object_id,$obj_name);
			echo "<td>></td>";
		}

		$this->printport($port);

		/* align ports nicely */
		if($port['id'] == $this->port_id)
			echo '</td></tr></table></td><td><table align=left><tr>';

		if($loop)
			echo '<td bgcolor=#ff9966>LOOP</td>';


		if(!$back) {
			echo "<td><</td>";
			$this->printobject($object_id,$obj_name);

			if(empty($port['back']))
				$this->_LinkPort($port_id, 'back');
		} else
			if( ($port['id'] != $this->port_id) && empty($port['front'])) {
				$this->printcomment($port);
				$this->_LinkPort($port_id, 'front');
			}
		
		if($loop) {
			if(isset($link['loopmaxcount']))
				$reason = " (MAX LOOP COUNT reached)";
			else
				$reason = '';

			showWarning("Possible Loop on Port ($linktype) ".$port['name'].$reason);
			return FALSE;
		}

		return TRUE;

	} /* _printport */

	/*
	 * print <tr>..</tr>
	 */
	function printportlistrow($first = TRUE, $hl_port_id = NULL) {
	
		$this->loopcount = 0;

		if($this->first_id == NULL)
			return;

		if($first)
			$id = $this->first_id;
		else
			$id = $this->last_id;

		if($hl_port_id == $this->port_id)
			$hlbgcolor = "bgcolor=".self::HL_PORT_BGCOLOR;
		else
			$hlbgcolor = "";

		$link = NULL;

		$port = $this->list[$id];

		$title = "linkcount: ".$this->count." (".$this->front_count."/".$this->back_count.")";

		/* Current Port */
		echo '<tr '.$hlbgcolor.'><td nowrap="nowrap" bgcolor='.self::CURRENT_PORT_BGCOLOR.' title="'.$title.'">'.$this->port['name'].': </td>';
		
		echo "<td><table align=right><tr><td>";
		
		$back = !empty($this->list[$id]['front']);

		$this->_printportlink($id, $link, $back);

		$this->_printportlist($id, !$back);
		echo "</td></tr></table></td></tr>";

		/* horizontal line */
                echo '<tr><td height=1 colspan=3 bgcolor=#e0e0e0></td></tr>';

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

		$link = &$this->list[$src_port_id][$linktype];
	
		if(!empty($link)) {
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

			if(!$this->_printportlink($dst_port_id, $link, $back))
					return;

               	        $this->_printportlist($dst_port_id,!$back);
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
                                'SELECT rack_id, Rack.name as Rack_name, row_id, RackRow.name as Row_name
                                FROM RackSpace
                                LEFT JOIN EntityLink on RackSpace.object_id = EntityLink.parent_entity_id
                                JOIN Rack on Rack.id = RackSpace.rack_id
                                JOIN RackRow on RackRow.id = Rack.row_id
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
	 *
	 */
       function _LinkPort($port_id, $linktype = 'front') {
	
               $helper_args = array
                        (
                                'port' => $port_id,
                        );

                echo "<td align=center>";

		if($linktype == 'front') {

                        echo "<span";
                        $popup_args = 'height=700, width=400, location=no, menubar=no, '.
                                'resizable=yes, scrollbars=yes, status=no, titlebar=no, toolbar=no';
                        echo " ondblclick='window.open(\"" . makeHrefForHelper ('portlist', $helper_args);
                        echo "\",\"findlink\",\"${popup_args}\");'";
                        // end of onclick=
                        echo " onclick='window.open(\"" . makeHrefForHelper ('portlist', $helper_args);
                        echo "\",\"findlink\",\"${popup_args}\");'";
                        // end of onclick=
                        echo '>';
                        printImageHREF ('plug', 'Link this port');
                        echo "</span>";

		} else {
			/* backend link */

			echo '<span onclick=window.open("'.makeHrefProcess(portlist::urlparamsarray(
				array('op' => 'PortLinkDialog','port' => $port_id,'linktype' => $linktype))).'","name","height=800,width=400");'
                        .'>';
                        printImageHREF ('plug', $linktype.' Link this port');
                        echo "</span>";
			
		}

		echo "</td>";

        } /* _LinkPort */

	/* 
  	 * return link cut symbol
	 *
         * TODO $opspec_list
	 */
	function _printUnLinkPort($port_id, $linktype) {

		$src_port = $this->list[$port_id];

		$link = $src_port[$linktype];

		$dst_port = $this->list[$link['id']];

		/* use RT unlink for front link, linkmgmt unlink for back links */
		if($linktype == 'back')
			$tab = 'linkmgmt';
		else
			$tab = 'ports';
		

 		return '<a href='.
                               makeHrefProcess(array(
					'op'=>'unlinkPort',
					'port_id'=>$port_id,
					'object_id'=>$this->object_id,
					'tab' => $tab,
					'linktype' => $linktype)).
                       ' onclick="return confirm(\'unlink ports '.$src_port['name']. ' -> '.$dst_port['name']
					.' ('.$linktype.') with cable ID: '.$src_port[$linktype]['cable'].'?\');">'.
                       getImageHREF ('cut', $linktype.' Unlink this port').'</a>';

	} /* _printUnLinkPort */


	/*
         * 
         */
        function urlparams($name, $value, $defaultvalue = NULL) {

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
        function urlparamsarray($params) {

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

	/* for debugging only */
	function var_dump_html(&$var) {
		echo "<pre>------------------Start Var Dump -------------------------\n";
		var_dump($var);
		echo "\n---------------------END Var Dump ------------------------</pre>";
	}

} /* portlist */

/* -------------------------------------------------- */

?>
