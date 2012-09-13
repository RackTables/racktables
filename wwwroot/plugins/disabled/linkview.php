<?php
/*
 * Link View (beta)
 *
 * Displays all Links of an Object (and childs) in the form:
 * 
 * Object Port --Cable--> Port Object( Port -- Cable --> Port Object)* 
 *
 *
 * Edit $lv_b2b_port_type_ids to fit your configuration.
 * These port type ids are used to connect e.g two Patch Panels Back to Back (B2B).
 * They can be found in RT Dictionary : Chapter 'PortOuterInterface' 
 * You should create your own B2B Interfaces to make this work correctly
 *
 * The port names on each B2B End are matched by Name. So make sure the names are identical on both sides.
 *	e.g:	    RJ45-11:ObjectA:B2B -----> B2B:ObjectB:RJ45-11 	Port is named RJ45-11 on both Ends!
 *
 *
 * COLORS:
 *	Current Object		= red
 *	Current Port		= yellow background
 * 	B2B Links		= grey background
 * 
 *
 * INSTALL:
 *
 * 	in RT create B2B ports (Dictionary : Chapter 'PortOuterInterface')
 * 	add your B2B key to $lv_b2b_port_type_ids
 *	copy linkview.php to inc/ directory
 *	copy jquery.jeditable.mini.js to js/ directory
 * 	add "include 'inc/linkview.php';" to inc/local.php
 *
 * TESTED on FreeBSD 8.2, nginx/1.0.8, php 5.3.8
 *
 * (c)2011 Maik Ehinger <m.ehinger@ltur.de>
 */

/*************************
 * Change Log
 * 
 * 01.12.11	separate B2B Link display
 * 02.12.11	add display of children port links
 * 06.12.11	add unlink symbols
 * 07.12.11	changed SQL queries to retrieve reservation_comments too
 *		fix display of reservation_comments
 *		add b2blink cache
 * 08.12.11	change display order, try to move current port to a leftmost possition
 *		change b2blink cache to cache sql result only (before _setStartEnd() is called)
 * 09.12.11	changed printtablerow()
 * 12.12.11	changed printtablerow() (added port_id != NULL)
 *		also show name of ports which are not linked
 * 14.12.11	add showallports
 *		add linkcount
 * 16.12.11	add link port
 * 19.12.11	fix for empty color
 * 22.12.11	make cable and reservation comments editable (jquery jeditable)
 *		add file jquery.jeditable.mini.js
 * 03.01.12	fix allports
 *		add printPort()
 *		add l2address as port tooltip
 *		add printComment()
 *		changed printtablerow() replace switch(TRUE) part
 * 04.01.12	add one b2b link handling
 *		add linkview::urlparams()
 *		add confirm to onclick of unlink href
 *		change 'add cable' -> 'edit cableID'
 *
 */

/*************************
 * TODO
 *
 * - code cleanups
 * - bug fixing
 *
 * - inter object links; link ports within one object 1:n or n:1(e.g mediaconverter, network taps )
 *
 * - fix $opspec_list for unlink
 *
 *
 */

$tab['object']['linkview'] = 'Link View';
$tabhandler['object']['linkview'] = 'linkview_tabhandler';

/* RT port_type_ids of Back 2 Back Ports */
/* DO NOT LEAVE EMPTY !!! */
/* Key from Dictionary : Chapter 'PortOuterInterface' */
$lv_b2b_port_type_ids = array(
	50071, /* user defined MRJ21 */
);

/* Possible LOOP detected after count links */
#const LV_MAX_LINK_COUNT = 10;


/* -------------------------------------------------- */

$lv_cache = array(
		'b2blink' => array(),
		'rackinfo' => array()
		);

/* -------------------------------------------------- */

function linkview_tabhandler($object_id) {
	global $lv_cache;


	//linkview::var_dump_html($_SERVER);

	$target = $_SERVER['REQUEST_URI'].'&action=update';

	if(isset($_GET['action'])) {

		if($_GET['action'] == 'update') {
			$ids = preg_split('/[^0-9]/',$_POST['id']);
			$retval = strip_tags($_POST['value']);

			if(isset($ids[1]))
				linkview::_updatecable($ids[0],$ids[1], $retval);
			else
				commitUpdatePortComment($ids[0], $retval);
		
			/* return what jeditable should display after edit */
			echo $retval;
			exit;	
		}
	}

 	addJS('js/jquery.jeditable.mini.js');

	/* init jeditable fields/tags */
	addJS(<<<END
$(document).ready(function() { $(".editcmt").editable("$target",{placeholder : "add comment"}); });
$(document).ready(function() { $(".editcable").editable("$target",{placeholder : "edit cableID"}); });
END
		, TRUE);

	/* linkview for current object */
	linkview_renderObjectLinks($object_id);

	/* linkview for every child */
	//$parents = getEntityRelatives ('parents', 'object', $object_id);
	$children = getEntityRelatives ('children', 'object', $object_id); //'entity_id'

	//linkview::var_dump_html($children);

	foreach($children as $child) {
		echo '<h1>Links for Child: '.$child['name'].'</h1>';
		linkview_renderObjectLinks($child['entity_id']);
	}

}

/* -------------------------------------------------- */
function linkview_renderObjectLinks($object_id) {

	global $lv_b2b_port_type_ids;

 	$object = spotEntity ('object', $object_id);
        $object['attr'] = getAttrValues($object_id);

	/* get ports */
	/* calls getObjectPortsAndLinks */
	amplifyCell ($object);

	//$ports = getObjectPortsAndLinks($object_id);
	$ports = $object['ports'];

	//linkview::var_dump_html($ports);

	$b2blinks = array();

	/* URL param handling */

	if(isset($_GET['allports'])) {
		$showallports = $_GET['allports'];
	} else
		$showallports = FALSE;

	if(isset($_GET['oneb2b'])) {
		$oneb2b = $_GET['oneb2b'];
	} else
		$oneb2b = TRUE;

	echo '<table><tr>';

	if($showallports) {
				
		echo '<td width=200><a href="'.makeHref(linkview::urlparams('allports','0','0'))
			.'">Hide Ports without link</a></td>';
	} else
		echo '<td width=200><a href="'.makeHref(linkview::urlparams('allports','1','0'))
			.'">Show All Ports</a></td>';

	if($oneb2b)
		echo '<td width=200><a href="'.makeHref(linkview::urlparams('oneb2b','0','1'))
			.'">All B2B links</a></td>';
	else
		echo '<td width=200><a href="'.makeHref(linkview::urlparams('oneb2b','1','1'))
			.'">First B2B link only</a></td>';

	echo '</tr></table>';

	/* END URL param handling */

//	echo "<h1>Links</h1>";
	echo '<table border=0>';

	foreach($ports as $port) {

		$lv = new linkview($port, $object_id, $showallports, $oneb2b);
		if($lv->islinked()) {
			if(!in_array($port['oif_id'], $lv_b2b_port_type_ids)) {	
				
				$lv->printtablerow();

			} else {
				/* b2b link */
				$b2blinks[] = $lv;
			}
		}
	}

	echo '</table>';

	/* print B2B links */
	if(!empty($b2blinks)) {
		echo "<h1>B2B Links</h1>";
		echo '<table>';

		foreach($b2blinks as $b2b) {
			if($b2b->islinked()) {
				$b2b->printtablerow();

			}
		}
		echo '</table>';
	}

}

/* -------------------------------------------------- */
/* --------------------------------------------------- */


class linkview {

	const MAX_LINK_COUNT = 10; #LV_MAX_LINK_COUNT;
	
	const B2B_LINK_BGCOLOR = '#d8d8d8';
	const CURRENT_PORT_BGCOLOR = '#ffff99';
	
	private $linklist = array();
	private $linkcount = 0;

	private $obj_id = 0;

	private $oneb2b = TRUE;

	function __construct(&$port, $object_id, $allports = FALSE, $oneb2b = TRUE) {
		global $lv_b2b_port_type_ids;

		$this->port = $port;
		$this->obj_id = $object_id;
		$this->oneb2b = $oneb2b;

		$port_id = $port['id'];
		$port_type_id = $port['oif_id'];

		$isb2b = in_array($port_type_id, $lv_b2b_port_type_ids);

		$hasb2blinks = TRUE;

		if(!$isb2b) {
			/* if current object has B2B links reverse display order, so B2B object will be displyed to the right of current object */
			$hasb2blinks = $this->_getObjectB2BPartner($object_id,FALSE,FALSE); /* order doesn't matter; don't add to linklist */ 

			if ($hasb2blinks)
				$reverse = TRUE;
			else
				$reverse = FALSE;
		} else
			$reverse = FALSE;

		$currentportlink = $this->_getLinkPartner($port_id, $reverse);
		$startlink = $currentportlink;

		/* show all ports */
		if(!$allports)
			if(!$hasb2blinks & $this->linkcount == 0) {
					$this->linklist = array();
					return;
			}

		if($isb2b) {
			/* B2B Link no further processing needed */
			return;
		}

		/* check if current port start object has active b2b links*/

		$count=0;
	
		while($startlink) {

			if($startlink['start.obj_id'] == NULL) {
				break;
			}

			$b2blink = $this->_getObjectB2BPartner($startlink['start.obj_id'], TRUE); /* prepend reversed link to list */
	
			if($b2blink) {

				$endlink = $this->_getPortB2BPartner($b2blink['start.obj_id'],$startlink['start.port_name'], TRUE );
				
				if($endlink) {

					if($endlink['start.port_id'] == NULL) {
					//	echo "Start Port Id == NULL<br>";
						break;
					}

					$startlink = $endlink;
				} else {
					break;
				}
			} else {
				break;
			}
	
				$count++;
				if($count>self::MAX_LINK_COUNT) {
					/* LOOP ? */
					showWarning("Possible LOOP detected for Port: ".$port['name'].' on '.$port['object_name']);
					break;
				}
				
		}
	


		/* check if current port link end object has active b2b links*/
	
		$startlink = $currentportlink;

		$count=0;
	
		while($startlink) {

			if($startlink['end.obj_id'] == NULL) {
				break;
			}

			$b2blink = $this->_getObjectB2BPartner($startlink['end.obj_id']); /* append link to list */
	
			if($b2blink) {
				$endlink = $this->_getPortB2BPartner($b2blink['end.obj_id'],$startlink['end.port_name']);
				
				if($endlink) {

					if($endlink['end.port_id'] == NULL) {
						break;
					}

					$startlink = $endlink;
				} else {
					break;
				}
			} else {
				break;
			}
	
				$count++;
				if($count>self::MAX_LINK_COUNT) {
					/* LOOP ? */
					showWarning("Possible LOOP detected for Port: ".$port['name'].' on '.$port['object_name']);
					break;
				}
				
		}

		/* show all ports */
		if(!$allports)
			if(($this->linkcount == 0 | ($this->linkcount < 2 & $hasb2blinks))) {
				$this->linklist = array();
			}

	//	$this->var_dump_html($this->linklist);	

	} /* __construct */


	function islinked() {
		return !empty($this->linklist);
	}

	function printtablerow() {

		global $lv_b2b_port_type_ids;

		$port_id = $this->port['id'];

		if(empty($this->linklist))
			return;

	//	$this->var_dump_html($this->linklist);

		echo '<tr><td title="linkcount: '.$this->linkcount.'">'.$this->port['name'].': </td><td><table border=0 align=right><tr>';

		$last_end_obj_id = NULL;

		$last_port = array('start.obj_id' => NULL, 'end.obj_id' => NULL);

		foreach($this->linklist as $key => $port) {
			$start_obj_id = $port['start.obj_id'];

			if($last_end_obj_id != NULL & $last_end_obj_id != $start_obj_id)
				$this->printObject($last_port['end.obj_id'],$last_port['end.obj_name']);

			/* print start object */
			if($start_obj_id != NULL & ($start_obj_id == $last_end_obj_id | $key == 0)) {

				$this->printObject($port['start.obj_id'],$port['start.obj_name']);

			}

			/* make ports of current object algin in one column */
			if($port['start.port_id'] == $port_id)
	             		echo '</tr></table></td><td><table border=0 align=left><tr>';

			/* print links */
			echo '<td><table align=center>';

			foreach($port['links'] as $link) {

				/* start port */
				if($link['start.port_id'] != NULL ) {

					/* set bgcolor for B2B links */
					if(in_array($link['start.port_type_id'], $lv_b2b_port_type_ids)) {
						$bgcolor = 'bgcolor='.self::B2B_LINK_BGCOLOR;
						$arrow = '=====>';
					} else {
						$bgcolor = '';
						$arrow = '----->';
					}
	
					echo '<tr '.$bgcolor.'>';
					
					/* start port name */
					$this->printPort($link,'start');

				} else {
					$this->_LinkPort($link['end.port_id']);

					/* print comment */
					$this->printComment($link,'end');

				}

				/* link */
				echo '<td align=center>';

				if(!empty($link['start.port_id']) & !empty($link['end.port_id'])) {

					echo '<pre><a class=editcable id='.$link['porta_id'].'_'.$link['portb_id'].'>'.$link['cable'].'</a></pre><pre>'.$arrow.'</pre>'
						.$this->_UnLinkPort($link,'start');

				}

				echo '</td>';

				/* end port */
				if($link['end.port_id'] != NULL) {
		
					/* end port name */
					$this->printPort($link,'end');

				} else {

					/* print comment */
					$this->printComment($link,'start');

					$this->_LinkPort($link['start.port_id']);
				}

				echo '</tr>';
				
				/* only display first link for single line output */
				if($this->oneb2b)
					break;
			}

			echo '</table></td>';
			/* end print links */

			/* make ports of current object align in one column */
			if($port['end.port_id'] == $port_id)
	             		echo '</tr></table></td><td><table border=0 align=left><tr>';

			$last_end_obj_id = $port['end.obj_id'];
			$last_port = $port;
		
		}
		
		/* print last object in list */
		if($last_port['end.obj_id'] != NULL) {

			$this->printObject($port['end.obj_id'],$port['end.obj_name']);
		}

		echo '</tr></table></td></tr>';

		/* horizontal line */
		echo '<tr><td height=2 colspan=3 bgcolor=#909090></td></tr>';

	} /* printTableRow */

	function printObject($object_id, $object_name) {
	
		if($object_id == $this->obj_id) {
			$color='color: #f00000';
		} else {
			$color='';
		}

		echo '<td><table align=center cellpadding=5 cellspacing=0 border=1><tr><td align=center><a style="font-weight:bold;'
			.$color.'" href="'.makeHref(array('page'=>'object', 'tab' => 'ports', 'object_id' => $object_id))
			.'"><pre>'.$object_name.'</pre></a><pre>'.$this->_getRackInfo($object_id, 'font-size:80%')
			.'</pre></td></tr></table></td>';	


	} /* printObject */

	function printPort(&$link, $startend = 'start') {
		
		/* set bgcolor for current port */
		if($link[$startend.'.port_id'] == $this->port['id']) 
			$bgcolor = 'bgcolor='.self::CURRENT_PORT_BGCOLOR;
		else
			$bgcolor = '';
	
		echo '<td align=center '.$bgcolor.' title="MAC: '.$link[$startend.'.l2address'].'"><pre>'.$link[$startend.'.port_name'].'</pre></td>';

	} /* printPort */

	function printComment(&$link, $startend = 'start') {
		
		if(!empty($link[$startend.'.reservation_comment'])) {
			$prefix = '<b>Reserved: </b>';
		} else
			$prefix = '';

		echo '<td>'.$prefix.'<i><a class=editcmt id='.$link[$startend.'.port_id'].'>'.$link[$startend.'.reservation_comment'].'</a></i></td>';

	} /* printComment */

	/* base SQL query string */
	const SQLquery = 'select Link.cable, porta.id as "porta_id", portb.id as "portb_id",
				 porta.id as "porta.port_id", porta.name as "porta.port_name", porta.type as "porta.port_type_id",
				 porta.reservation_comment as "porta.reservation_comment", porta.l2address as "porta.l2address",
				 obja.id as "porta.obj_id", obja.objtype_id as "porta.objtype_id", obja.name as "porta.obj_name",
				 portb.id as "portb.port_id", portb.name as "portb.port_name", portb.type as "portb.port_type_id",
				 portb.reservation_comment as "portb.reservation_comment", portb.l2address as "portb.l2address",
				 objb.id as "portb.obj_id", objb.objtype_id as "portb.objtype_id", objb.name as "portb.obj_name"
				 from Port as porta
				 left join Link on (porta.id = Link.porta)
				 left join RackObject as obja on (porta.object_id = obja.id)
				 left join Port as portb on (portb.id = Link.portb)
				 left join RackObject as objb on (portb.object_id = objb.id)';

	/*
 	 * returns the link for port_id 
	 * changes linklist
	 *
	 */
	function _getLinkPartner($port_id, $prepend = FALSE) {

      		$result = usePreparedSelectBlade
       		(
			self::SQLquery
				.'where (? in (porta.id,portb.id))'
				.'order by portb.id desc limit 1',
				array($port_id)
       		 );
       		 $row = $result->fetchAll(PDO::FETCH_ASSOC);

//		$this->var_dump_html($row);
	
		if(!empty($row)) {


			/* TODO foreach row ? */

			$this->_setStartEnd($row[0],$port_id, NULL, NULL, $prepend);
			$row[0]['links'] = $row;
			
			if($prepend) {
				/* prepend */
				array_unshift($this->linklist, $row[0]);	
			} else {
				/* append */
				$this->linklist[] = $row[0];
			}
			
			$this->_linkcount($row[0]);

			return $row[0];
		}
	
	}

	/*
 	 * try to find linked objects witch are linked over b2bports
	 * changes linklist
	 */
	function _getObjectB2BPartner($object_id, $prepend = FALSE, $add = TRUE) {

		global $lv_cache, $lv_b2b_port_type_ids;

		$b2blinkcache = &$lv_cache['b2blink'];

		if(!array_key_exists($object_id,$b2blinkcache)) {

			$b2bports = implode(',',$lv_b2b_port_type_ids);

			$result = usePreparedSelectBlade
	 		(
				self::SQLquery
				  .'where (!("" in (Link.portb,Link.porta)))'
				  .'and  ( ( (porta.type in (?)) and (obja.id = ?) ) or 
				       ( (portb.type in (?)) and (objb.id = ?) ))',
				array(
					$b2bports, $object_id,
					$b2bports, $object_id
				)
       		 	);
       		 	$row = $result->fetchAll(PDO::FETCH_ASSOC);

			/* cache sql result */
			$b2blinkcache[$object_id] = $row;
		
		} 

		$b2blinkrow = $b2blinkcache[$object_id];

	//	$this->var_dump_html($b2blinkrow);

		if(!empty($b2blinkrow)) {

			foreach($b2blinkrow as &$value) {
				$this->_setStartEnd($value,NULL,$object_id, NULL, $prepend);
			}

			$b2blinkrow[0]['links'] = $b2blinkrow;

			if($add) {
				if($prepend) {
					/* prepend */
					array_unshift($this->linklist, $b2blinkrow[0]);	
				} else {
					/* append */
					$this->linklist[] = $b2blinkrow[0];
				}

				$this->_linkcount($b2blinkrow[0]);

			}
			return $b2blinkrow[0];
		}

	}


	/*
 	 * try to find the link/port on the B2B Object identified by port_name
	 * changes linklist
	 *
	 */
	function _getPortB2BPartner($object_id, $port_name, $prepend = FALSE) {
	
		global $lv_b2b_port_type_ids;

		/* 
		 * $matches = array();
		 *
	 	 *	if(!preg_match('/\\d+$/',$port_name,$matches))
	 	 *	return;
		 *
		 *  $regexp = '[^0-9]'.$matches[0].'$';
		 */

		$port_name = strtolower($port_name);

		$b2bports = implode(',',$lv_b2b_port_type_ids);
		
       		 $result = usePreparedSelectBlade
       		 (
				 self::SQLquery
				.'where (( (porta.type not in (?)) and (obja.id = ?) and (LOWER(porta.name) = ?) ) 
				 or   ( (portb.type not in (?)) and (objb.id = ?) and (LOWER(portb.name) = ?) ))'
				.'order by portb.id desc limit 1',
				array(
					$b2bports, $object_id, $port_name,
					$b2bports, $object_id, $port_name
					/* $b2bports, $object_id,$regexp,
					 * $b2bports, $object_id,$regexp
					 */
				)	
       		 );
       		 $row = $result->fetchAll(PDO::FETCH_ASSOC);

		if(!empty($row)) {
			

			/* TODO foreach row ? */

			$this->_setStartEnd($row[0], NULL, $object_id, $port_name, $prepend);

			$row[0]['links'] = $row;

			if($prepend) {
				/* prepend */
				array_unshift($this->linklist, $row[0]);	
			} else {
				/* append */
				$this->linklist[] = $row[0];
			}

			$this->_linkcount($row[0]);
			
			return $row[0];
		}

	} /* _getPortB2BPartner */

	function _linkcount($link) {

		if( ($link['start.port_id'] != NULL & $link['start.reservation_comment'] != NULL ) | 
			($link['end.port_id'] != NULL & $link['end.reservation_comment'] != NULL ) |
			($link['start.port_id'] != NULL & $link['end.port_id'] != NULL) ) 
				$this->linkcount++;


	} /* _linkcount */

	/* sets link start and end  dependent of port_id, obj_id or objtype_id */
	/* returns link end */
	function _setStartEnd(&$link, $port_id = NULL, $obj_id = NULL, $port_name = NULL, $reverse = FALSE) {

		$porta_id = $link['porta.port_id'];
		$porta_objtype_id = $link['porta.objtype_id'];
		$porta_obj_id = $link['porta.obj_id'];
		$porta_name = $link['porta.port_name'];

		$portb_id = $link['portb.port_id'];
		$portb_objtype_id = $link['portb.objtype_id'];
		$portb_obj_id = $link['portb.obj_id'];
		$portb_name = $link['portb.port_name'];

		$start = NULL;
		$end = NULL;

		/* port_id */
		if(!empty($port_id)) {

			if($porta_id == $port_id) {
				$start = 'porta';
				$end = 'portb';
			 } else 
				if($portb_id == $port_id) {
					$start = 'portb';	
					$end = 'porta';	
				}
		 }


		/* obj_id */
		if(!empty($obj_id)) {

			if($porta_obj_id == $obj_id) {
				$start = 'porta';
				$end = 'portb';
			} else {
				if($portb_obj_id == $obj_id) {
					$start = 'portb';	
					$end = 'porta';	
				}
			}
		}

		if(!empty($port_name)) {
			
			if($porta_obj_id == $portb_obj_id) {
				if ($porta_name == $port_name ) {
					$start = 'porta';
					$end = 'portb';
				} else {
					$start = 'portb';	
					$end = 'porta';	
				}
			}
		}

		if($reverse) {
			$tmp = $start;
			$start = $end;
			$end = $tmp;
		}
	
		/* set array keys start. and end. */
		foreach(array('port_id','port_name','port_type_id','obj_id','obj_name','objtype_id','reservation_comment','l2address') as $name) {

			$link['start.'.$name] = $link[$start.'.'.$name];
			unset($link[$start.'.'.$name]);
			$link['end.'.$name] = $link[$end.'.'.$name];
			unset($link[$end.'.'.$name]);
		}
		
		return $link['end.port_id'];

	} /* _setStartEnd */

	/*
	 *  returns linked Row / Rack Info for object_id
	 *
	 */
	function _getRackInfo($object_id, $style = '') {
		global $lv_cache;

		$rackinfocache = $lv_cache['rackinfo'];
	
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
			return	'<span style="'.$style.'">Unmounted</span>';
		else
			return '<a style="'.$style.'" href='.makeHref(array('page'=>'row', 'row_id'=>$obj['row_id'])).'>'.$obj['Row_name']
			 	.'</a>/<a style="'.$style.'" href='.makeHref(array('page'=>'rack', 'rack_id'=>$obj['rack_id'])).'>'
				.$obj['Rack_name'].'</a>';

	} /* _getRackInfo */


	function _LinkPort($port_id) {
			echo "<td align=center><span";
                        $helper_args = array
                        (
                                'port' => $port_id,
                        );
                        $popup_args = 'height=700, width=400, location=no, menubar=no, '.
                                'resizable=yes, scrollbars=yes, status=no, titlebar=no, toolbar=no';
                        echo " ondblclick='window.open(\"" . makeHrefForHelper ('portlist', $helper_args);
                        echo "\",\"findlink\",\"${popup_args}\");'";
                        // end of onclick=
                        echo " onclick='window.open(\"" . makeHrefForHelper ('portlist', $helper_args);
                        echo "\",\"findlink\",\"${popup_args}\");'";
                        // end of onclick=
                        echo '>';
                        // end of <a>
                        printImageHREF ('plug', 'Link this port');
                        echo "</span>";
                        //echo " <input type=text name=reservation_comment></td>";
	} /* _LinkPort */

	/* 
  	 * return linked cut symbol
	 *
         * TODO $opspec_list
	 */
	function _UnLinkPort(&$link, $startend) {
		
 		return '<a href='.
                               makeHrefProcess(array(
					'op'=>'unlinkPort',
					'port_id'=>$link[$startend.'.port_id'],
					'object_id'=>$link[$startend.'.obj_id'],
					'tab' => 'ports')).
                       ' onclick="return confirm(\'unlink ports '.$link['start.port_name']. ' -> '.$link['end.port_name']
					.' with cable ID: '.$link['cable'].'?\');">'.
                       getImageHREF ('cut', 'Unlink this port').'</a>';

	} /* _UnLinkPort */

	/*
	 *
	 *
	 */
	function _updatecable($porta_id, $portb_id, $newcable) {
		/* TODO add id to Link Table to uniquely identify the link */

		$result = usePreparedUpdateBlade('Link', 
							array('cable' => $newcable),
							array('porta' => $porta_id, 'portb' => $portb_id)
						);


		if($result === FALSE)
			showError('Update Error');
		else {

			if($result != 1)
				showError("MOre than one row changed!!!! Rows: $result");
		}
		
		

	} /* _updatecable */

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

	/* for debugging only */
	function var_dump_html(&$var) {
		echo "<pre>------------------Start Var Dump -------------------------\n";
		var_dump($var);
		echo "\n---------------------END Var Dump ------------------------</pre>";
	}

}
/* end linkview class */

?>
