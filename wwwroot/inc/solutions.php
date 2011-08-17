<?php
/*

The purpose of this file is to contain functions, which generate a complete
HTTP response body and are either "dead ends" or depend on just a small
amount of other code (which should eventually be placed in a sort of
"first order" library file).

*/

define ('RE_STATIC_URI', '#^([[:alpha:]]+)/(?:[[:alpha:]]+/)*[[:alnum:]\._-]+\.([[:alpha:]]+)$#');

function dispatchImageRequest()
{
	genericAssertion ('img', 'string');
	global $pageno, $tabno;
	switch ($_REQUEST['img'])
	{
	case 'minirack': // rack security context
		$pageno = 'rack';
		$tabno = 'default';
		fixContext();
		assertPermission();
		renderRackThumb (getBypassValue());
		break;
	case 'preview': // file security context
		$pageno = 'file';
		$tabno = 'download';
		fixContext();
		assertPermission();
		renderFilePreview (getBypassValue());
		break;
	case 'cactigraph':
		$pageno = 'object';
		$tabno = 'cactigraph';
		fixContext();
		assertPermission();
		genericAssertion ('graph_id', 'uint');
		if (! array_key_exists ($_REQUEST['graph_id'], getCactiGraphsForObject (getBypassValue())))
			throw new InvalidRequestArgException ('graph_id', $_REQUEST['graph_id']);
		proxyCactiRequest ($_REQUEST['graph_id']);
		break;
	default:
		renderErrorImage();
	}
}

function renderErrorImage ()
{
	header("Content-type: image/png");
	// "ERROR", 76x17, red on white
	echo base64_decode
	(
		'iVBORw0KGgoAAAANSUhEUgAAAEwAAAARCAYAAAB3h0oCAAAAAXNSR0IArs4c6QAAALBJREFUWMPt' .
		'WFsOwCAIG4v3vzL7WEyWxQdVwM1A4l/F2iHVETPzESGOMyTAInURRP0suUhb2FIho/jWXO38w4KN' .
		'LPDGEt2jlgPBZxFKc2o8UT7Lj6SkAmfw1nx+28MkVWQlcjT9EOwjLqnpaNImi+I1j/YSl5RY/gx+' .
		'VCCF/MnkCz4JZQtvEUXx1nyW9jCUlPVLbTJ/3MO2dsnWRq2Nwl2wTarM51rhsVEnDhT/w7C4APaJ' .
		'ZhkIGYaUAAAAAElFTkSuQmCC'
	);
}

function renderAccessDeniedImage()
{
	header ('Content-type: image/png');
	// 1x1, single black pixel
	echo base64_decode
	(
		'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAIAAACQd1PeAAAAAXNSR0IArs4c6QAAAAxJREFUCNdj' .
		'YGBgAAAABAABJzQnCgAAAABJRU5ErkJggg=='
	);
}

// Having a local caching array speeds things up. A little.
function colorFromHex ($image, $hex)
{
	static $colorcache = array ();
	if (isset ($colorcache[$hex]))
		return $colorcache[$hex];
	$r = hexdec ('0x' . substr ($hex, 0, 2));
	$g = hexdec ('0x' . substr ($hex, 2, 2));
	$b = hexdec ('0x' . substr ($hex, 4, 2));
	$c = imagecolorallocate ($image, $r, $g, $b);
	$colorcache[$hex] = $c;
	return $c;
}

function renderRackThumb ($rack_id = 0)
{
	// Don't call DB extra times, hence we are most probably not the
	// only script wishing to access the same data now.
	if (NULL !== ($thumbcache = loadThumbCache ($rack_id)))
	{
		header("Content-type: image/png");
		echo $thumbcache;
		return;
	}
	ob_start();
	if (FALSE !== generateMiniRack ($rack_id))
	{
		$capture = ob_get_clean();
		header("Content-type: image/png");
		echo $capture;
		usePreparedExecuteBlade
		(
			'REPLACE INTO RackThumbnail SET rack_id=?, thumb_data=?',
			array ($rack_id, base64_encode ($capture))
		);
		return;
	}
	// error text in the buffer
	ob_end_flush();
}

// Output a binary string containing the PNG minirack. Indicate error with return code.
function generateMiniRack ($rack_id)
{
	if (NULL === ($rackData = spotEntity ('rack', $rack_id)))
		return FALSE;
	amplifyCell ($rackData);
	markupObjectProblems ($rackData);
	global $rtwidth;
	$rtdepth = 9;
	$offset[0] = 3;
	$offset[1] = 3 + $rtwidth[0];
	$offset[2] = 3 + $rtwidth[0] + $rtwidth[1];
	$totalheight = 3 + 3 + $rackData['height'] * 2;
	$totalwidth = $offset[2] + $rtwidth[2] + 3;
	$img = @imagecreatetruecolor ($totalwidth, $totalheight)
		or die("Cannot Initialize new GD image stream");
	// cache our palette as well
	$color = array();
	foreach (array ('F', 'A', 'U', 'T', 'Th', 'Tw', 'Thw') as $statecode)
		$color[$statecode] = colorFromHex ($img, getConfigVar ('color_' . $statecode));
	$color['black'] = colorFromHex ($img, '000000');
	$color['gray'] = colorFromHex ($img, 'c0c0c0');
	$border_color = ($rackData['has_problems'] == 'yes') ? $color['Thw'] : $color['gray'];
	imagerectangle ($img, 0, 0, $totalwidth - 1, $totalheight - 1, $color['black']);
	imagerectangle ($img, 1, 1, $totalwidth - 2, $totalheight - 2, $border_color);
	imagerectangle ($img, 2, 2, $totalwidth - 3, $totalheight - 3, $color['black']);
	for ($unit_no = 1; $unit_no <= $rackData['height']; $unit_no++)
	{
		for ($locidx = 0; $locidx < 3; $locidx++)
		{
			$colorcode = $rackData[$unit_no][$locidx]['state'];
			if (isset ($rackData[$unit_no][$locidx]['hl']))
				$colorcode = $colorcode . $rackData[$unit_no][$locidx]['hl'];
			imagerectangle
			(
				$img,
				$offset[$locidx],
				3 + ($rackData['height'] - $unit_no) * 2,
				$offset[$locidx] + $rtwidth[$locidx] - 1,
				3 + ($rackData['height'] - $unit_no) * 2 + 1,
				$color[$colorcode]
			);
		}
	}
	imagepng ($img);
	imagedestroy ($img);
	return TRUE;
}

function renderProgressBarImage ($done)
{
	if ($done > 100)
		throw new InvalidArgException ('done', $done);
	$img = @imagecreatetruecolor (100, 10);
	switch (isset ($_REQUEST['theme']) ? $_REQUEST['theme'] : 'rackspace')
	{
		case 'sparenetwork':
			$color['T'] = colorFromHex ($img, '808080');
			$color['F'] = colorFromHex ($img, 'c0c0c0');
			break;
		case 'rackspace': // teal
		default:
			$color['T'] = colorFromHex ($img, getConfigVar ('color_T'));
			$color['F'] = colorFromHex ($img, getConfigVar ('color_F'));
	}
	imagefilledrectangle ($img, 0, 0, $done, 10, $color['T']);
	imagefilledrectangle ($img, $done, 0, 100, 10, $color['F']);
	for ($x = 20; $x <= 80; $x += 20)
	{
		$cc = $x > $done ? $color['T'] : $color['F'];
		imagesetpixel ($img, $x, 0, $cc);
		imagesetpixel ($img, $x, 1, $cc);
		imagesetpixel ($img, $x, 4, $cc);
		imagesetpixel ($img, $x, 5, $cc);
		imagesetpixel ($img, $x, 8, $cc);
		imagesetpixel ($img, $x, 9, $cc);
	}
	header("Content-type: image/png");
	imagepng ($img);
	imagedestroy ($img);
}

function renderProgressBarError()
{
	header ('Content-type: image/png');
	// 100x10, red on pink, "progr. bar error"
	echo base64_decode
	(
		'iVBORw0KGgoAAAANSUhEUgAAAGQAAAAKCAYAAABCHPt+AAAAAXNSR0IArs4c6QAAAAZiS0dEAP8A' .
		'/wD/oL2nkwAAAAlwSFlzAAALEwAACxMBAJqcGAAAAAd0SU1FB9sDERYTJrBhF8sAAACvSURBVEjH' .
		'7VdRDoAgCMXmQbz/qbhJfdnMQQiDTZ3vL6MHvEA03Yg3rIRSABBhV1xwMBXyp/JatFVYq7La1Hft' .
		'N709xcXxWLqE4tbGr+GXdNDqw8STxSS0z9S695ZD+e05pXhHt8RRHqtebIdoRPASM2K+ePi18Gjz' .
		'Yuwz7AKpM2cpmjPUVx3qf0OIqyLKvl+POMp6+R3Jy9oxnD4C0nsPiTrfb35viO2QiOF6foYKD57g' .
		'f1uXQb2mAAAAAElFTkSuQmCC'
	);
}

function renderFilePreview ($file_id)
{
	if ($image = getFileCache ($file_id)) //Cache Hit
	{
		header("Content-type: image/jpeg"); 
		echo $image;
		return;
	}
	//Cache Miss
	$file = getFile ($file_id);
	$image = imagecreatefromstring ($file['contents']);
	unset ($file);
	$width = imagesx ($image);
	$height = imagesy ($image);
	if ($width > getConfigVar ('PREVIEW_IMAGE_MAXPXS') or $height > getConfigVar ('PREVIEW_IMAGE_MAXPXS'))
	{
		$ratio = getConfigVar ('PREVIEW_IMAGE_MAXPXS') / max ($width, $height);
		$newwidth = $width * $ratio;
		$newheight = $height * $ratio;
		$resampled = imagecreatetruecolor ($newwidth, $newheight);
		imagecopyresampled ($resampled, $image, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
		imagedestroy ($image);
		$image = $resampled;
		unset ($resampled);
	}
	header ('Content-type: image/jpeg');
	ob_start();
	imagejpeg ($image);
	imagedestroy ($image);
	commitAddFileCache ($file_id, ob_get_flush());
}

function printStatic404()
{
	header ('404 Not Found');
?><!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<html><head>
<title>404 Not Found</title>
</head><body>
<h1>Not Found</h1>
<p>The requested file was not found in this instance.</p>
<hr>
<address>RackTables static content proxy</address>
</body></html><?php
	exit;
}

function proxyStaticURI ($URI)
{
	$content_type = array
	(
		'css' => 'text/css',
		'js' => 'text/javascript',
		'html' => 'text/html',
		'png' => 'image/png',
		'gif' => 'image/gif',
		'jpg' => 'image/jpeg',
		'jpeg' => 'image/jpeg',
		'ico' => 'image/x-icon',
	);
	$matches = array();
	if
	(
		! preg_match (RE_STATIC_URI, $URI, $matches)
		or ! in_array ($matches[1], array ('pix', 'css', 'js'))
		or ! array_key_exists (strtolower ($matches[2]), $content_type)
	)
		printStatic404();
	global $racktables_staticdir;
	if (FALSE === $fh = fopen ("${racktables_staticdir}/${URI}", 'r'))
		printStatic404();
	if (FALSE !== $stat = fstat ($fh))
		if (checkCachedResponse (max ($stat['mtime'], $stat['ctime']), 0))
			exit;
	header ('Content-type: ' . $content_type[$matches[2]]);
	fpassthru ($fh);
	fclose ($fh);	
}

function proxyCactiRequest ($graph_id)
{
	$ret = array();
	$cacti_url = getConfigVar('CACTI_URL');
	$cacti_user = getConfigVar('CACTI_USERNAME');
	$cacti_pass = getConfigVar('CACTI_USERPASS');
	$url = $cacti_url . "graph_image.php?action=view&local_graph_id=" . $graph_id;
	$postvars = "action=login&login_username=" . $cacti_user . "&login_password=" . $cacti_pass;

	$session = curl_init();

	// Initial options up here so a specific type can override them
	curl_setopt ($session, CURLOPT_FOLLOWLOCATION, FALSE); 
	curl_setopt ($session, CURLOPT_TIMEOUT, 10);
	curl_setopt ($session, CURLOPT_RETURNTRANSFER, TRUE);

	curl_setopt ($session, CURLOPT_HEADER, TRUE);
	curl_setopt ($session, CURLOPT_URL, $url);
	$headers = curl_exec ($session);	// Initial request to set the cookies

	// Get the cookies from the headers
	preg_match('/Set-Cookie: ([^;]*)/i', $headers, $cookies);
	array_shift($cookies);  // Remove 'Set-Cookie: ...' value			
	$cookie_header = implode(";", $cookies);

	curl_setopt ($session, CURLOPT_COOKIE, $cookie_header);
	curl_setopt ($session, CURLOPT_HEADER, FALSE);
	curl_setopt ($session, CURLOPT_POST, TRUE);
	curl_setopt ($session, CURLOPT_POSTFIELDS, $postvars);

	curl_exec ($session);	// POST Login

	// Make the request
	$ret['contents'] = curl_exec ($session);
	$ret['type'] = "text/plain";
	$ret['type'] = curl_getinfo ($session, CURLINFO_CONTENT_TYPE);
	$ret['size'] = curl_getinfo ($session, CURLINFO_CONTENT_LENGTH_DOWNLOAD);

	curl_close ($session);

	header("Content-Type: {$ret['type']}");
	header("Content-Length: {$ret['size']}");
	echo $ret['contents'];
}

?>
