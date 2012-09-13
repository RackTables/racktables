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
$tab['ipv4net']['rmsripe'] = 'Ripe Database Update';

// What Function should render the tab content
$tabhandler['ipv4net']['rmsripe'] = array('RMS_RIPE', 'getContent');

// Which Should we Call to handle post back
$ophandler['ipv4net']['rmsripe']['HandleRipeForm'] = array('RMS_RIPE', 'HandleRipeForm');


class RMS_RIPE
{
	const password = "sh0t the sher1ff";
	const updatesurl = "http://syncupdates.db.ripe.net";
	const whoisurl = "whois.ripe.net";
	
	
	static public function getContent(){
		$obj = new RMS_RIPE();
		$obj->getRipeForm();
	}
	
	function getRipeForm () {
			echo "<link rel=stylesheet type='text/css' href=/plugins/style.css />";
			echo "<div id=\"rms_addonWrapper\">";
			echo $this->RenderRipeForm();
			echo "</div>";
	}
	
	function HandleRipeForm () {
		$RIPEPOSTINFO = "";
		foreach ($_POST as $key => $value)
		{
			if ($key != "submit" && $value !=  "" ) 
			{
				$RIPEPOSTINFO .= $key.":".$value."\r\n";
			}

		}
		$RIPEPOSTINFO .= "password:".self::password."\r\n";
		print "<br/>POST<br/>";
		print "<textarea cols=\"100\" rows=\"20\">";
		print $RIPEPOSTINFO;	
		print "</textarea>";
		$RIPEPOSTINFO = urlencode($RIPEPOSTINFO);

		
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,self::updatesurl); // set url to post to
		curl_setopt($ch, CURLOPT_FAILONERROR, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // allow redirects
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); // return into a variable
		curl_setopt($ch, CURLOPT_TIMEOUT, 0); // times out after Ns
		curl_setopt($ch, CURLOPT_POST, 1); // set POST method
		curl_setopt($ch, CURLOPT_POSTFIELDS, "DATA=$RIPEPOSTINFO"); // add POST fields
		curl_setopt($ch, CURLOPT_FAILONERROR, 0);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_COOKIEFILE, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

		$result = curl_exec($ch); // run the whole process
		curl_close($ch);
		
		print "<br/>RESPONSE<br/>";
		print "<textarea cols=\"100\" rows=\"20\">";
		print $result; 
		print "</textarea>";
		
		
		return buildWideRedirectURL();
	}

	function RenderRipeForm () {
	$ipv4id = $_GET["id"];
	$netdata = spotEntity ('ipv4net', $ipv4id);
	
	$RIPEFORM = "";
	$RIPEFORM .= "<div id=\"ripeupdate\">";
	$RIPEFORM .= "<form method=post action='process.php?page=ipv4net&tab=rmsripe&op=HandleRipeForm&id=$ipv4id'>";
	$RIPEFORM .= "This form directly updates the RIPE database only make chnages if authorised to do so";
	$RIPEFORM .= "<ol>";
	
	// RIPE IP Range
	$startip = $netdata['ip_bin'] & $netdata['mask_bin'];
	$endip = $netdata['ip_bin'] | $netdata['mask_bin_inv'];		
	$inetnum = long2ip ($startip) ." - ".long2ip ($endip);
	
	// Talk to ripe servers and get back the template and current matching record
	$whoisurl = self::whoisurl;
	$RIPETEMPLATE = `jwhois -h $whoisurl -- -t inetnum`;
	$RIPEINFO = `jwhois -h $whoisurl -- -x -B -r -T inetnum $inetnum`;
	$RIPEMATCHCLASS = "DBMatchExact";
	if (strpos($RIPEINFO,"ERROR:101: no entries found"))
	{
		$RIPEMATCHCLASS = "DBMatch";
		$RIPEINFO = `jwhois -h $whoisurl -- -B -r -T inetnum $inetnum`;
		$RIPEFORM .= "<strong>No Exact Match for this range</strong><br/>";
		$RIPEFORM .= "Infomation below is for the enclosing range and is shown for refernece only<br/>";
		$RIPEFORM .= "Entries created below will be for the $inetnum range<br/>";
		$RIPEFORM .= "to modify the enclosing range please browse to the enclosing range instead<br/>";
	}

	
	
	$TEMPLATELINES = preg_split("/[\r\n]+/",$RIPETEMPLATE);
	foreach ($TEMPLATELINES as $line)
	{
		if  (preg_match('/^([a-z].*):[\s\t]+([^\s\t]*)[\s\t]+([^\s\t]+)[\s\t]+([^\s\t]+)/',$line,$TEMPLATEmatches))
		{
			$RIPEATTR = $TEMPLATEmatches[1];
			$RIPETYPE = $TEMPLATEmatches[2];
			$RIPEQTY = $TEMPLATEmatches[3];
			$RIPEKEY = $TEMPLATEmatches[4];
			preg_match_all("/$RIPEATTR:\s+(.*)/",$RIPEINFO ,$DBmatches, PREG_SET_ORDER);
			//$RIPEDBVAL = $DBmatches[1];
			if ($RIPEATTR == "inetnum") 
			{

				$RIPEFORM .= "<li><label for=\"$RIPEATTR \">$RIPEATTR:</label>$inetnum\n";
				$RIPEFORM .= "\t<div class=\"$RIPEMATCHCLASS\">$RIPEDBVAL&nbsp;</div>\n";
				$RIPEFORM .= "<input type=\"hidden\" name=\"$RIPEATTR\" value=\"$inetnum\"></li>\n";
			}
			else
			{
				$class = "";
				if ($RIPETYPE  == "[mandatory]" ) 
				{
					$class= "validate['required']";
				}
				

				foreach ($DBmatches as $RIPEDBVAL)
				{
					$RIPEFORM .= "<li>\n";
					$RIPEFORM .= "\t<label for=\"$RIPEATTR \">$RIPEATTR:</label>\n";
					$RIPEFORM .= "\t<div class=\"$RIPEMATCHCLASS\">$RIPEDBVAL[1]&nbsp;</div>\n";
					$RIPEFORM .= "\t<input type=text name=\"$RIPEATTR\" value=\"$RIPEDBVAL[1]\" class=\"$class\" >\n";
					$RIPEFORM .= "</li>\n";
				}
			}
		}



	}
		$RIPEFORM .= "<input name=\"submit\" id=\"submit\" type=\"submit\" class=\"submit validate['submit']\" value=\"submit\" /> ";
		
	$RIPEFORM .= "</ol>";
	$RIPEFORM .= "</form>";
	$RIPEFORM .= "</div>";

	
	return $RIPEFORM;

	}
}







?>
