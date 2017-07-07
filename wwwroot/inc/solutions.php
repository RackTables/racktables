<?php

# This file is a part of RackTables, a datacenter and server room management
# framework. See accompanying file "COPYING" for the full copyright and
# licensing information.

/*

This file contains functions that produce a complete HTTP response (headers
and body) and are either self-contained or depend on just a small amount of
other code such that they can do the job quicker than the functions that
implement the "interface" module.

*/

require_once 'slb-interface.php';

define ('RE_STATIC_URI', '#^(?:[[:alnum:]]+[[:alnum:]_.-]*/)+[[:alnum:]\._-]+\.([[:alpha:]]+)$#');

function castRackImageException ($e)
{
	$m = array
	(
		'EntityNotFoundException' => 'rack_not_found',
		'RTPermissionDenied' => 'access_denied',
		'InvalidRequestArgException' => 'rack_arg_error',
	);
	$c = get_class ($e);
	return array_key_exists ($c, $m) ? new RTImageError ($m[$c]) : $e;
}

function dispatchImageRequest()
{
	global $pageno, $tabno;
	switch ($img = genericAssertion ('img', 'string'))
	{
	case 'minirack': // rack security context
		$pageno = 'rack';
		$tabno = 'default';
		try
		{
			fixContext();
			assertPermission();
		}
		catch (RackTablesError $e)
		{
			throw castRackImageException ($e);
		}
		dispatchMiniRackThumbRequest (getBypassValue());
		break;
	case 'midirack': // rack security context
		$pageno = 'rack';
		$tabno = 'default';
		try
		{
			fixContext();
			assertPermission();
			$scale = genericAssertion ('scale', 'uint');
			$object_id = array_key_exists ('object_id', $_REQUEST) ?
				genericAssertion ('object_id', 'uint') : NULL;
		}
		catch (RackTablesError $e)
		{
			throw castRackImageException ($e);
		}
		// Scaling or highlighting implies no caching and thus no extra wrapper code around.
		header ('Content-Type: image/png');
		printRackThumbImage (getBypassValue(), $scale, $object_id);
		break;
	case 'preview': // file security context
		$pageno = 'file';
		$tabno = 'download';
		fixContext();
		assertPermission();
		renderImagePreview (getBypassValue());
		break;
	case 'cactigraph':
		$pageno = 'object';
		$tabno = 'cacti';
		fixContext();
		assertPermission();
		$graph_id = genericAssertion ('graph_id', 'uint');
		if (! array_key_exists ($graph_id, getCactiGraphsForObject (getBypassValue())))
			throw new InvalidRequestArgException ('graph_id', $graph_id);
		proxyCactiRequest (genericAssertion ('server_id', 'uint'), $graph_id);
		break;
	case 'muningraph':
		$pageno = 'object';
		$tabno = 'munin';
		fixContext();
		assertPermission();
		$graph = genericAssertion ('graph', 'string');
		if (! array_key_exists ($graph, getMuninGraphsForObject (getBypassValue())))
			throw new InvalidRequestArgException ('graph', $graph);
		proxyMuninRequest (genericAssertion ('server_id', 'uint'), $graph);
		break;
	default:
		throw new InvalidRequestArgException ('img', $img);
	}
}

// XXX: deprecated
function renderErrorImage ()
{
	header("Content-Type: image/png");
	echo base64_decode (IMG_76x17_ERROR);
}

// XXX: deprecated
function renderAccessDeniedImage()
{
	header ('Content-Type: image/png');
	echo base64_decode (IMG_1x1_BLACK);
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

function createTrueColorOrThrow ($context, $width, $height)
{
	// Sometimes GD is missing even though it was available at install time.
	if
	(
		! function_exists ('imagecreatetruecolor') ||
		FALSE === $img = @imagecreatetruecolor ($width, $height)
	)
		throw new RTImageError ($context);
	return $img;
}

# Generate a complete HTTP response for a 1:1 minirack image, use and update
# SQL cache where appropriate.
function dispatchMiniRackThumbRequest ($rack_id)
{
	if (NULL !== ($thumbcache = loadThumbCache ($rack_id)))
	{
		header ('Content-Type: image/png');
		echo $thumbcache;
		return;
	}
	ob_start();
	printRackThumbImage ($rack_id);
	$capture = ob_get_clean();
	header ('Content-Type: image/png');
	echo $capture;
	usePreparedExecuteBlade
	(
		'REPLACE INTO RackThumbnail SET rack_id=?, thumb_data=?',
		array ($rack_id, base64_encode ($capture))
	);
}

# Generate a binary PNG image for a rack contents.
function printRackThumbImage ($rack_id, $scale = 1, $object_id = NULL)
{
	$rackData = spotEntity ('rack', $rack_id);
	amplifyCell ($rackData);
	if ($object_id !== NULL)
		highlightObject ($rackData, $object_id);
	global $rtwidth;
	$offset[0] = 3;
	$offset[1] = 3 + $rtwidth[0];
	$offset[2] = 3 + $rtwidth[0] + $rtwidth[1];
	$totalheight = 3 + 3 + $rackData['height'] * 2;
	$totalwidth = $offset[2] + $rtwidth[2] + 3;
	$img = createTrueColorOrThrow ('rack_php_gd_error', $totalwidth, $totalheight);
	# It was measured, that caching palette in an array is faster, than
	# calling colorFromHex() multiple times. It matters, when user's
	# browser is trying to fetch many minirack images in parallel.
	$color = array
	(
		'F' => colorFromHex ($img, '8fbfbf'),
		'A' => colorFromHex ($img, 'bfbfbf'),
		'U' => colorFromHex ($img, 'bf8f8f'),
		'T' => colorFromHex ($img, '408080'),
		'Th' => colorFromHex ($img, '80ffff'),
		'Tw' => colorFromHex ($img, '804040'),
		'Thw' => colorFromHex ($img, 'ff8080'),
		'black' => colorFromHex ($img, '000000'),
		'gray' => colorFromHex ($img, 'c0c0c0'),
	);
	$border_color = ($rackData['has_problems'] == 'yes') ? $color['Thw'] : $color['gray'];
	imagerectangle ($img, 0, 0, $totalwidth - 1, $totalheight - 1, $color['black']);
	imagerectangle ($img, 1, 1, $totalwidth - 2, $totalheight - 2, $border_color);
	imagerectangle ($img, 2, 2, $totalwidth - 3, $totalheight - 3, $color['black']);
	for ($unit_no = 1; $unit_no <= $rackData['height']; $unit_no++)
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
	if ($scale > 1)
	{
		$resized = imagecreate ($totalwidth * $scale, $totalheight * $scale);
		imagecopyresized ($resized, $img, 0, 0, 0, 0, $totalwidth * $scale, $totalheight * $scale, $totalwidth, $totalheight);
		imagedestroy ($img);
		$img = $resized;
	}
	imagepng ($img);
	imagedestroy ($img);
}

function renderProgressBarImage ($done)
{
	if ($done > 100)
		throw new RTImageError ('pbar_arg_error');
	$img = createTrueColorOrThrow ('pbar_php_gd_error', 100, 10);
	switch (array_fetch ($_REQUEST, 'theme', 'rackspace'))
	{
		case 'sparenetwork':
			$color['T'] = colorFromHex ($img, '808080');
			$color['F'] = colorFromHex ($img, 'c0c0c0');
			break;
		case 'rackspace': // teal
		default:
			$color['T'] = colorFromHex ($img, '408080');
			$color['F'] = colorFromHex ($img, '8fbfbf');
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
	header("Content-Type: image/png");
	imagepng ($img);
	imagedestroy ($img);
}

function renderProgressBar4Image ($px1, $px2, $px3)
{
	$width = 100;
	$height = 10;
	$img = createTrueColorOrThrow ('pbar_php_gd_error', $width, 10);
	$offsets = array ($px1, $px2, $px3, $width - $px1 - $px2 - $px3);
	$colors = array
	(
		colorFromHex ($img, '408080'),
		colorFromHex ($img, '8fbfbf'),
		colorFromHex ($img, '808080'),
		colorFromHex ($img, 'c0c0c0'),
	);
	$pos =  0;
	for ($i = 0; $i < count ($offsets); $i++)
	{
		$off = $offsets[$i];
		$clr = $colors[$i];
		if ($pos + $off > $width || $off < 0)
			throw new RTImageError ('pbar_arg_error');
		if ($off > 0)
			imagefilledrectangle ($img, $pos, 0, $pos + $off, $height, $clr);
		$pos += $off;
	}

	for ($x = $width / 5; $x < $width; $x += $width / 5)
	{
		$p = 0; $k = count ($offsets) - 1;
		for ($j = 0; $j < count ($offsets); $j++)
			if ($x < ($p += $offsets[$j]))
			{
				$k = $j;
				break;
			}
		switch ($k)
		{
			case 0:
				$cc = 1;
				break;
			case 1:
				$cc = 0;
				break;
			case 2:
				$cc = 3;
				break;
			case 3:
				$cc = 2;
				break;
		}
		imagesetpixel ($img, $x, 0, $colors[$cc]);
		imagesetpixel ($img, $x, 1, $colors[$cc]);
		imagesetpixel ($img, $x, 4, $colors[$cc]);
		imagesetpixel ($img, $x, 5, $colors[$cc]);
		imagesetpixel ($img, $x, 8, $colors[$cc]);
		imagesetpixel ($img, $x, 9, $colors[$cc]);
	}
	header("Content-Type: image/png");
	imagepng ($img);
	imagedestroy ($img);
}

// XXX: deprecated
function renderProgressBarError()
{
	header ('Content-Type: image/png');
	echo base64_decode (IMG_100x10_PBAR_ERROR);
}

function renderImagePreview ($file_id)
{
	if ($image = getFileCache ($file_id)) //Cache Hit
	{
		header("Content-Type: image/jpeg");
		echo $image;
		return;
	}
	//Cache Miss
	$file = getFile ($file_id);
	$image = imagecreatefromstring ($file['contents']);
	unset ($file);
	$width = imagesx ($image);
	$height = imagesy ($image);
	if ($width > getConfigVar ('PREVIEW_IMAGE_MAXPXS') || $height > getConfigVar ('PREVIEW_IMAGE_MAXPXS'))
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
	header ('Content-Type: image/jpeg');
	ob_start();
	imagejpeg ($image);
	imagedestroy ($image);
	commitAddFileCache ($file_id, ob_get_flush());
}

function printStatic404()
{
	header ('HTTP/1.0 404 Not Found');
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
		! preg_match (RE_STATIC_URI, $URI, $matches) ||
		! array_key_exists (strtolower ($matches[1]), $content_type)
	)
		printStatic404();
	global $local_staticdir, $racktables_staticdir;
	if (isset ($local_staticdir))
		$fh = @fopen ("${local_staticdir}/${URI}", 'r');
	if (! isset ($fh) || FALSE === $fh)
		$fh = @fopen ("${racktables_staticdir}/${URI}", 'r');
	if (FALSE === $fh)
		printStatic404();
	if (FALSE !== $stat = fstat ($fh))
		if (checkCachedResponse (max ($stat['mtime'], $stat['ctime']), 0))
			exit;
	header ('Content-Type: ' . $content_type[$matches[1]]);
	fpassthru ($fh);
	fclose ($fh);
}

function proxyCactiRequest ($server_id, $graph_id)
{
	$ret = array();
	$servers = getCactiServers();
	if (! array_key_exists ($server_id, $servers))
		throw new InvalidRequestArgException ('server_id', $server_id, 'there is no such server');
	$cacti_url = $servers[$server_id]['base_url'];
	$url = "${cacti_url}/graph_image.php?action=view&local_graph_id=${graph_id}&rra_id=" . getConfigVar ('CACTI_RRA_ID');
	$postvars = 'action=login&login_username=' . $servers[$server_id]['username'];
	$postvars .= '&login_password=' . $servers[$server_id]['password'];

	$session = curl_init();
//	curl_setopt ($session, CURLOPT_VERBOSE, TRUE);

	// Initial options up here so a specific type can override them
	curl_setopt ($session, CURLOPT_FOLLOWLOCATION, FALSE);
	curl_setopt ($session, CURLOPT_TIMEOUT, 10);
	curl_setopt ($session, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt ($session, CURLOPT_URL, $url);

	if (isset($_SESSION['CACTICOOKIE'][$cacti_url]))
		curl_setopt ($session, CURLOPT_COOKIE, $_SESSION['CACTICOOKIE'][$cacti_url]);

	// Request the image
	$ret['contents'] = curl_exec ($session);
	$ret['type'] = curl_getinfo ($session, CURLINFO_CONTENT_TYPE);
	$ret['size'] = curl_getinfo ($session, CURLINFO_SIZE_DOWNLOAD);

	// Not an image, probably the login page
	if (preg_match ('/^text\/html.*/i', $ret['type']))
	{
		// Request to set the cookies
		curl_setopt ($session, CURLOPT_HEADER, TRUE);
		curl_setopt ($session, CURLOPT_COOKIE, "");	// clear the old cookie
		$headers = curl_exec ($session);

		// Get the cookies from the headers
		preg_match('/Set-Cookie: ([^;]*)/i', $headers, $cookies);
		array_shift($cookies);  // Remove 'Set-Cookie: ...' value
		$cookie_header = implode(";", $cookies);
		$_SESSION['CACTICOOKIE'][$cacti_url] = $cookie_header; // store for later use by this user

		// CSRF security in 0.8.8h, regexp version
		if (preg_match("/sid:([a-z0-9,]+)\"/", $ret['contents'], $csf_output))
		{
			if (array_key_exists(1, $csf_output))
				$postvars .="&__csrf_magic=$csf_output[1]";
		}
		// POST Login
		curl_setopt ($session, CURLOPT_COOKIE, $cookie_header);
		curl_setopt ($session, CURLOPT_HEADER, FALSE);
		curl_setopt ($session, CURLOPT_POST, TRUE);
		curl_setopt ($session, CURLOPT_POSTFIELDS, $postvars);
		curl_exec ($session);

		// Request the image
		curl_setopt ($session, CURLOPT_HTTPGET, TRUE);
		$ret['contents'] = curl_exec ($session);
		$ret['type'] = curl_getinfo ($session, CURLINFO_CONTENT_TYPE);
		$ret['size'] = curl_getinfo ($session, CURLINFO_SIZE_DOWNLOAD);
	}

	curl_close ($session);

	if ($ret['type'] != NULL)
		header("Content-Type: {$ret['type']}");
	if ($ret['size'] > 0)
		header("Content-Length: {$ret['size']}");

	echo $ret['contents'];
}

function proxyMuninRequest ($server_id, $graph)
{
	try
	{
		list ($host, $domain) = getMuninNameAndDomain (getBypassValue());
	}
	catch (InvalidArgException $e)
	{
		throw new RTImageError ('munin_graph');
	}

	$ret = array();
	$servers = getMuninServers();
	if (! array_key_exists ($server_id, $servers))
		throw new InvalidRequestArgException ('server_id', $server_id, 'there is no such server');
	$munin_url = $servers[$server_id]['base_url'];
	$url = "${munin_url}/${domain}/${host}.${domain}/${graph}-day.png";

	$session = curl_init();

	// Initial options up here so a specific type can override them
	curl_setopt ($session, CURLOPT_FOLLOWLOCATION, FALSE);
	curl_setopt ($session, CURLOPT_TIMEOUT, 10);
	curl_setopt ($session, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt ($session, CURLOPT_URL, $url);

	if (isset($_SESSION['MUNINCOOKIE'][$munin_url]))
		curl_setopt ($session, CURLOPT_COOKIE, $_SESSION['MUNINCOOKIE'][$munin_url]);

	// Request the image
	$ret['contents'] = curl_exec ($session);
	$ret['type'] = curl_getinfo ($session, CURLINFO_CONTENT_TYPE);
	$ret['size'] = curl_getinfo ($session, CURLINFO_SIZE_DOWNLOAD);

	curl_close ($session);

	if ($ret['type'] != NULL)
		header ("Content-Type: {$ret['type']}");
	if ($ret['size'] > 0)
		header ("Content-Length: {$ret['size']}");

	echo $ret['contents'];
}

function printSVGMessageBar ($text = 'lost message', $textattrs = array(), $rectattrs = array())
{
	$mytextattrs = array
	(
		'fill' => 'black',
		'x' => '85',
		'y' => '15',
		'font-size' => '100%',
		'text-anchor' => 'middle',
		'font-family' => 'monospace',
		'font-weight' => 'bold',
	);
	$myrectattrs = array
	(
		'fill' => 'white',
		'stroke' => 'black',
		'x' => '0',
		'y' => '0',
		'width' => '170px',
		'height' => '20px',
		'stroke-width' => '1px',
	);
	foreach ($textattrs as $k => $v)
		$mytextattrs[$k] = $v;
	foreach ($rectattrs as $k => $v)
		$myrectattrs[$k] = $v;
	echo "<svg width='" . $myrectattrs['width'] . "' height='" . $myrectattrs['height'] . "' version='1.1' ";
	echo "xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink'>\n";
	echo '<rect';
	foreach ($myrectattrs as $k => $v)
		echo " ${k}='${v}'";
	echo " />\n<text";
	foreach ($mytextattrs as $k => $v)
		echo " ${k}='${v}'";
	echo ">${text}</text>\n";
	echo "</svg>\n";
}

?>
