<?php

// RMS Ripe Database Update
// by James Tutton
// Version 0.1

// Installation:
// 1)  Add plugin to ./plugins/ folder;
// 2)  Make sure plugins folder is included in inc/local.php
/*
// Start Plugin Support to Racktables
foreach (glob($_SERVER['DOCUMENT_ROOT']."/plugins/*.php") as $filename) {
   include($filename);
}
// End Plugin Support to Racktables
*/


// Which Tab Section should we render on what should we Name the Tab
$tab['object']['bgppeer'] = 'BGP Session Details';

// What Function should render the tab content
$tabhandler['object']['bgppeer'] = array('RMS_BGP', 'getContent');
$tabhandler['bgp']['default'] = array('RMS_BGP', 'getBGPPage');
$tabhandler['bgp']['config'] = array('RMS_BGP', 'getBGPConfigPage');

// Which Should we Call to handle post back
//$ophandler['bgp']['default']['HandleBGPForm'] = array('RMS_BGP', 'HandleBGPForm');

$page['bgp']['title'] = 'BGP Peering';
$page['bgp']['parent'] = 'index';
$image['bgp']['path'] = 'pix/bgp.png';
$image['bgp']['width'] = 218;
$image['bgp']['height'] = 200;

define('IPV6_REGEX', "/^\s*((([0-9A-Fa-f]{1,4}:){7}([0-9A-Fa-f]{1,4}|:))|(([0-9A-Fa-f]{1,4}:){6}(:[0-9A-Fa-f]{1,4}|((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3})|:))|(([0-9A-Fa-f]{1,4}:){5}(((:[0-9A-Fa-f]{1,4}){1,2})|:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3})|:))|(([0-9A-Fa-f]{1,4}:){4}(((:[0-9A-Fa-f]{1,4}){1,3})|((:[0-9A-Fa-f]{1,4})?:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){3}(((:[0-9A-Fa-f]{1,4}){1,4})|((:[0-9A-Fa-f]{1,4}){0,2}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){2}(((:[0-9A-Fa-f]{1,4}){1,5})|((:[0-9A-Fa-f]{1,4}){0,3}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){1}(((:[0-9A-Fa-f]{1,4}){1,6})|((:[0-9A-Fa-f]{1,4}){0,4}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(:(((:[0-9A-Fa-f]{1,4}){1,7})|((:[0-9A-Fa-f]{1,4}){0,5}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:)))(%.+)?\s*$/");
define('IPV4_REGEX', "/^((?:25[0-5]|2[0-4][0-9]|[01]?[0-9]?[0-9]).){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9]?[0-9])$/m");
array_push($indexlayout[1],'bgp');

class RMS_BGP
{




	static public function getContent(){
		$obj = new RMS_BGP();
		//$obj->FormHandler();
		print $obj->Render();
	}

	static public function getBGPPage(){
		$obj = new RMS_BGP();
		$obj->FormHandler();
		print $obj->RenderBGPPage();
	}

	static public function getBGPConfigPage(){
		$obj = new RMS_BGP();
		$obj->FormHandler();
	}
	
	function Header() {
		print   "<link rel=stylesheet type='text/css' href=/plugins/style.css />";
		print   "<link rel=stylesheet type='text/css' href=/plugins/bgp.css />";
		print   "<div id=\"rms_addonWrapper\">";
	}

	function Footer() {
		print   "</div>";	
		
	}
	
	function Render() {
		
		$this->Header();
		$this->getBGPSessionList();
		$this->getBGPConfig();
		$this->Footer();
	}

	function FormHandler() {
		print "<pre class=\"blk\"><code>";
		foreach ($_POST['ip'] as $val) {
			print RenderBGPRouterConfig($val);
		}
                print "</code></pre>";

	}
	
	function RenderBGPPage() {
		
		$this->Header();
		$this->RenderFullBGPPeeringTable();
		$this->Footer();
	}
	
	function getBGPConfig() {
		$object_id = $_GET["object_id"];
			
		$objquery ="SELECT *
		FROM peeringdb.`peerParticipantsPublics` PP
		INNER JOIN peeringdb.`peerParticipants` P 
		ON PP.participant_id = P.id
		INNER JOIN peeringdb.mgmtPublicsIPs PIP 
		ON PIP.public_id = PP.public_id
		INNER JOIN (
			SELECT *
			FROM trunk.IPv4Network
			INNER JOIN trunk.`TagStorage` ON TagStorage.entity_id = IPv4Network.id
			WHERE tag_id =57
			) PeeringNetworks 
		ON address = CONCAT( INET_NTOA( PeeringNetworks.ip ) , '/', PeeringNetworks.mask )
		LEFT OUTER JOIN peeringdb.peerSessions PS ON PS.ip = local_ipaddr
		INNER JOIN trunk.RackObject 
		ON RackObject.id = $object_id
		AND RackObject.name = CONCAT('AS',P.asn)
		ORDER BY device";
	
		$objresult = usePreparedSelectBlade ($objquery);
		$objresult = $objresult->fetchall();
		if (count($objresult) > 0 )
		{
			foreach ($objresult as $object) {
				$device = $object['device'];
				$list[$device]['device'] = $device;
				$PeeringLan = $object['name'];
				$list[$device]['PeeringLans'][$PeeringLan]['name'] = $PeeringLan;
				$PeeringPoint = $object['local_ipaddr'];
				$list[$device]['PeeringLans'][$PeeringLan]['PeeringPoints'][$PeeringPoint]['IP'] = $object['local_ipaddr'];
			}
		}
		
		print "<h2>System Configuration Data</h2>";
		
		
		if (count($list) > 0 )
		{
			foreach ($list as $device )
			{
				$Device = $device['device'];
				if ($Device == '') {
					$Device = "Unconfigured";
				}
				print "<h3>$Device</h3>";
				$PeeringLans = $device['PeeringLans'];
				print "<pre class=\"blk\"><code>";
				foreach ($PeeringLans as $PeeringLan)
				{	
					$PeeringPoints = $PeeringLan['PeeringPoints'];
					foreach ($PeeringPoints as $PeerPoint)
					{
							RenderBGPRouterConfig ($PeerPoint['IP']);
					}
				}
		                print "</code></pre>";
			}
		}
	}
	
	function getBGPSessionList () {
		$object_id = $_GET["object_id"];
		
		$objquery ="SELECT *
		FROM peeringdb.`peerParticipantsPublics` PP
		INNER JOIN peeringdb.`peerParticipants` P 
		ON PP.participant_id = P.id
		INNER JOIN peeringdb.mgmtPublicsIPs PIP 
		ON PIP.public_id = PP.public_id
		INNER JOIN (
			SELECT *
			FROM trunk.IPv4Network
			INNER JOIN trunk.`TagStorage` ON TagStorage.entity_id = IPv4Network.id
			WHERE tag_id =57
			) PeeringNetworks 
		ON address = CONCAT( INET_NTOA( PeeringNetworks.ip ) , '/', PeeringNetworks.mask )
		LEFT OUTER JOIN peeringdb.peerSessions PS ON PS.ip = local_ipaddr
		INNER JOIN trunk.RackObject 
		ON RackObject.id = $object_id
		AND RackObject.name = CONCAT('AS',P.asn)
		ORDER BY device";
		
		$objresult = usePreparedSelectBlade ($objquery);
		$objresult = $objresult->fetchall();
		if (count($objresult) > 0 )
		{
			print  "<TABLE id=bgpsessions>\n";
			print  "<TR>";
			print  "<TH class='Device'>Device</TH>";
			print  "<TH class='Name'>Name</TH>";
			#print  "<TH class='Description'>Description</TH>";
			print  "<TH class='Password'>Password</TH>";
			print  "<TH class='PeerGroup'>PeerGroup</TH>";
			print  "<TH class='FilterList'>FilterList</TH>";
			print  "<TH class='Address' >Address</TH>";
			print  "</TR>\n";
			
			foreach ($objresult as $object)
			{
				print  "<TR class=BGPSession>";
				print  "<TD class='Device'>".$object['device']."</TD>";
				print  "<TD class='Name'>".$object['name']."</TD>";
				#print  "<TD class='Description'>".$object['Description']."</TD>";
				print  "<TD class='Password'>".$object['password']."</TD>";
				print  "<TD class='PeerGroup'>".$object['PeerGroup']."</TD>";
				print  "<TD class='FilterList'>";
				//$this->RenderFilterList($object['FilterList'],$object['device']);
				print "</TD>";
				print  "<TD class='Address'>";
				$this->RenderIPBlock($object['local_ipaddr'],$object_id);
				print "</TD>";
				print  "</TR>\n";
				
			}
			
			print  "</TABLE>\n";
		}
	}
	
	function RenderIPBlock($dottedquad,$object_id) {
		
		print "<table cellspacing=0 cellpadding='5' align='center' class='widetable'>\n";
		
		if ($netid = getIPv4AddressNetworkId ($dottedquad))
			{
				$netinfo = spotEntity ('ipv4net', $netid);
				loadIPv4AddrList ($netinfo);
			}
		$class = $alloc['addrinfo']['class'];
		$secondclass = ($hl_ip_addr == $dottedquad) ? 'tdleft port_highlight' : 'tdleft';
		print "<tr class='${class}' valign=top>";
		print "<td class='${secondclass}'>";
		if (NULL !== $netid)
			print "<a name='ip-$dottedquad' href='index.php?page=object&object_id=$object_id&hl_ipv4_addr=$dottedquad'>${dottedquad}</a>";
		else
			print $dottedquad;
		if (getConfigVar ('EXT_IPV4_VIEW') != 'yes')
			print '<small>/' . (NULL === $netid ? '??' : $netinfo['mask']) . '</small>';
		print '&nbsp;' . $aac[$alloc['type']];
		if (strlen ($alloc['addrinfo']['name']))
			print ' (' . niftyString ($alloc['addrinfo']['name']) . ')';
		print '</td>';
		if (getConfigVar ('EXT_IPV4_VIEW') == 'yes')
		{
			if (NULL === $netid)
				print '<td class=sparenetwork>N/A</td><td class=sparenetwork>N/A</td>';
			else
			{
				print "<td class='${secondclass}'>";
				renderCell ($netinfo);
				print "</td>";
			}
		}
		print "</tr>\n";
		print "</table>\n";
		
	}
	
	function RenderFilterList($FilterList,$Device) {
		
		$objquery ="SELECT *
		FROM peeringdb.`access-list` 
		WHERE number = $FilterList 
		AND device = '$Device'";

		$objresult = usePreparedSelectBlade ($objquery);
		$objresult = $objresult->fetchall();
		if (count($objresult) > 0 )
		{
			print  "<TABLE id=FilterLists>\n";
			print  "<TR>";
			print  "<TH class='Number'>Number</TH>";
			print  "<TH class='Action'>Action</TH>";
			print  "<TH class='Regex'>Regex</TH>";
			print  "</TR>\n";
			
			foreach ($objresult as $object)
			{
				print  "<TR class=FilterList>";
				print  "<TD class='Number'>".$object['number']."</TD>";
				print  "<TD class='Action'>".$object['action']."</TD>";
				print  "<TD class='Regex'>".$object['regex']."</TD>";
				print  "</TR>\n";
			}
			print  "</TABLE>\n";
		}
		
	}
	
	function RenderFullBGPPeeringTable (){
		$objquery ="select 
					peerParticipantsPublics.local_asn as asn,
					`peerParticipants`.name,
					RackObject.id as object_id,
					`peerParticipants`.info_type,
					`peerParticipants`.info_prefixes,
					`peerParticipants`.info_traffic,
					`peerParticipants`.policy_general,
					mgmtPublics.name as PeeringLan,
					local_ipaddr as ip 
					from peeringdb.`peerParticipantsPublics` 
					INNER JOIN peeringdb.`peerParticipants` 
					ON participant_id = `peerParticipants`.id 
					INNER JOIN peeringdb.mgmtPublicsIPs ON peeringdb.mgmtPublicsIPs.public_id = peeringdb.`peerParticipantsPublics`.public_id
					INNER JOIN (
						SELECT * from 
						IPv4Network 
						INNER JOIN `TagStorage` 
						ON TagStorage.entity_id = IPv4Network .id 
						WHERE	tag_id = 57
						) PeeringNetwroks ON address = CONCAT(INET_NTOA(PeeringNetwroks.ip),'/',PeeringNetwroks.mask)
					INNER JOIN peeringdb.mgmtPublics ON mgmtPublics.id = peeringdb.`peerParticipantsPublics`.public_id
					LEFT OUTER JOIN (
						SELECT * FROM  RackObject
						WHERE RackObject.objtype_id = 50065
						) RackObject ON RackObject.asset_no = local_asn
					ORDER BY  `peerParticipants`.name,
					mgmtPublics.name,local_ipaddr";
		
		$objresult = usePreparedSelectBlade ($objquery);
		$objresult = $objresult->fetchall();
		
		$list = array();
		foreach ($objresult as $object) {
			$asn = $object['asn'];
			
			$list[$asn]['asn'] = $asn;
			$list[$asn]['name'] = $object['name'];
			$list[$asn]['info_type'] = $object['info_type'];
			$list[$asn]['info_prefixes'] = $object['info_prefixes'];
			$list[$asn]['info_traffic'] = $object['info_traffic'];
			$list[$asn]['policy_general'] = $object['policy_general'];
			$PeeringLan = $object['PeeringLan'];
			$list[$asn]['PeeringLans'][$PeeringLan]['name'] = $PeeringLan;
			$list[$asn]['object_id'] = $object['object_id'];
			$PeeringPoint = $object['ip'];
			$list[$asn]['PeeringLans'][$PeeringLan]['PeeringPoints'][$PeeringPoint]['ip'] = $object['ip'];
		}
		
		if (count($list) > 0 )
			{
				print  "<form method=post action='/index.php?page=bgp&tab=config' target='_blank'>";
				print "<input name='submit' id='submit' type='submit' value='Generate Config in New Window' />";
				print  "<TABLE id=bgpsessions>\n";
				print  "<TR>";
				print  "<TH class='ASN'>ASN</TH>";
				print  "<TH class='Name'>Name</TH>";
				print  "<TH class='Stats'>Peer Stats</TH>";
				print  "<TH class='PeerPoints'>Peer Points</TH>";
				print  "</TR>\n";
				
				foreach ($list as $asn )
				{
					
					print    "<TR class=BGPSession>";
					
					
					if ($asn['object_id']) {
							print "<TD class='ASName ConfiguredASN'>";
							print "<a href='/index.php?page=object&object_id=".$asn['object_id']."'>".$asn['asn']."</a>";
							print "</TD>";
					}
					else
					{
						print "<TD class='ASName'>";					
						print $asn['asn'];
						print "</TD>";
					}
					
					print  "<TD class='Name'>".$asn['name']."</TD>";
					print  "<TD class='Stats'>";
					print "Type: " . $asn['info_type'] ."</br>";
					print "Policy: " .$asn['policy_general'] ."</br>";
					print "Prefixes: " . $asn['info_prefixes'] ."</br>";
					print "Traffic: " . $asn['info_traffic'] ."</br>";
					print "</TD>";
					print  "<TD class='PeerPoints'>";
					$PeeringLans = $asn['PeeringLans'];
					foreach ($PeeringLans as $PeeringLan)
					{	
						
						print "<table class=PeeringLan><tr><td class=name>".$PeeringLan['name']."</td><td class=session>";
						$PeeringPoints = $PeeringLan['PeeringPoints'];
						foreach ($PeeringPoints as $PeerPoint)
						{
							RenderSessionInfo($PeerPoint['ip']);
						}
						print "</td><tr></table>";
					}
					print "</TD>";
					print  "</TR>\n";
					
				}
				print "</TABLE>\n";
				print "<input name='submit' id='submit' type='submit' value='submit' />";
				print "</form>\n";
			}
	
	
	}
	
}



function RenderSessionInfo($SessionIP) {
		$objquery ="Select '$SessionIP' as ip,
					`peerSessions`.`Up/Down`,
					`peerSessions`.`State/PfxRcd`,
					`IPv4Allocation`.object_id as ip_object_id
					from (SELECT '$SessionIP' as IP, inet_aton('$SessionIP') as IpNum) SessionIP
			 		LEFT OUTER JOIN `IPv4Allocation` ON SessionIP.IpNum = IPv4Allocation.IP
					LEFT OUTER JOIN peeringdb.`peerSessions` ON inet_aton(`peerSessions`.IP) = SessionIP.IpNum;";

		#print $objquery;
		$objresult = usePreparedSelectBlade ($objquery);
		$objresult = $objresult->fetchall();
		$SessionInfo =  "<div class=##DivClass##>";
		$SessionInfo .=  "<input type=checkbox name='ip[]' value='$SessionIP'>";	
		
		if (count($objresult) > 0 )
		{
			
			foreach ($objresult as $object)
				if ($object['ip_object_id']) {
				$DivClass = "ConfiguredPeer";
					if ($object['Up/Down'] == "never") 
					{
						$DivClass = "MissConfiguredPeer";
					}
					switch ($object['State/PfxRcd'])
					{
						case "Active":
							$DivClass = "MissConfiguredPeer";
							break;
						case "Idle":
							$DivClass = "MissConfiguredPeer";
							break;
					}
					$SessionInfo = str_replace('##DivClass##',$DivClass,$SessionInfo);
					$SessionInfo .=  "<a href='/index.php?page=object&object_id=".$object['ip_object_id']."&hl_ipv4_addr=".$object['ip']."'>".$object['ip']."</a>";
					$SessionInfo .=  " Up ".$object['Up/Down'];
					$SessionInfo .=  " PfxRcd ".$object['State/PfxRcd'];
				}
				else
				{
					$SessionInfo .=  $object['ip'];
				}
				$SessionInfo .=  "</div>";
		}
		print $SessionInfo;
	}

function RenderBGPRouterConfig ($SessionIP){
	$objquery ="
SELECT local_ipaddr,local_asn,PeerGroup,password,FilterList, groupname as DefPeerGroup, description, P.name, PG.device
FROM peeringdb.`peerParticipantsPublics` PP
INNER JOIN peeringdb.`peerParticipants` P
ON PP.participant_id = P.id
INNER JOIN peeringdb.mgmtPublicsIPs PIP
ON PIP.public_id = PP.public_id
INNER JOIN (
		SELECT *
		FROM trunk.IPv4Network
		INNER JOIN trunk.`TagStorage` ON TagStorage.entity_id = IPv4Network.id
		WHERE tag_id =57
		) PeeringNetworks
ON address = CONCAT( INET_NTOA( PeeringNetworks.ip ) , '/', PeeringNetworks.mask )
LEFT OUTER JOIN peeringdb.peerGroups PG ON PeeringNetworks.id = PG.network_id
LEFT OUTER JOIN peeringdb.peerSessions PS ON PS.ip = local_ipaddr
WHERE PP.local_ipaddr =  '$SessionIP';";

	$objresult = usePreparedSelectBlade ($objquery);
	$objresult = $objresult->fetchall();
	if (count($objresult) > 0 )
	{
		foreach ($objresult as $object)	
		{
			print "! AUTO CONFIG FOR " . $object['device'] .  "\r\n";
			print "router bgp 5552\r\n";
			print "\tneighbor " . $object['local_ipaddr'] . " remote-as " . $object['local_asn'] ."\r\n";
			$PeerGroup = $object['PeerGroup'];

			if ($object['PeerGroup'] == '')
			{
				$PeerGroup = $object['DefPeerGroup'] ;
			}
			print "\tneighbor " . $object['local_ipaddr'] . " peer-group " . $PeerGroup ."\r\n";
			
			$description = $object['description'];
			if ($object['description'] == '')
                        {
                                $description = $object['name'] . ' AS' . $object['local_asn'];
                        }
 	
			print "\tneighbor " . $object['local_ipaddr'] . " description " . $description ."\r\n";
			if ($Password <> '')
			{
				print "\tneighbor " . $object['local_ipaddr'] . " password 7 " . $object['password'] ."\r\n";
			}else{
				print "\t! neighbor " . $object['local_ipaddr'] . " password 7 UNCOMMENT AND SET IF PASSWORD REQUIRED BY PEER \r\n";
			}
			print "!\r\n";
			// Needs php >= 5.2 
			//if (filter_var($PeeringIP, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6))
			if (preg_match(IPV6_REGEX,$SessionIP)) 
			{
				print "address-family ipv6\r\n";
			} 
			
			// Needs php >= 5.2 
			//if (filter_var($PeeringIP, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6))
			if (preg_match(IPV4_REGEX,$SessionIP)) 
			{
				print "address-family ipv4\r\n";
			}
			
			if ($object['FilterList'] <> 0)
			{
				print "\tneighbor " . $object['local_ipaddr'] . " filter-list " . $object['FilterList'] . " in\r\n";
			}
			
			print "\tneighbor " . $object['local_ipaddr'] . " activate\r\n";
			print "!\r\n";
			print "\r\n";
			
		}
	}
}


?>
