<?php


ob_start();
try {
require 'inc/init.php';

assertStringArg ('img', __FILE__);
switch ($_REQUEST['img'])
{
	case 'minirack': // rack security context
		assertUIntArg ('rack_id', __FILE__);
		$pageno = 'rack';
		$tabno = 'default';
		fixContext();
		if (!permitted())
			renderAccessDeniedImage();
		else
			renderRackThumb ($_REQUEST['rack_id']);
		break;
	case 'progressbar': // no security context
		assertUIntArg ('done', __FILE__, TRUE);
		renderProgressBarImage ($_REQUEST['done']);
		break;
	case 'view': // file security context
	case 'preview':
		assertUIntArg ('file_id', __FILE__);
		$pageno = 'file';
		$tabno = 'default';
		fixContext();
		if (!permitted())
			renderAccessDeniedImage();
		else
			renderFilePreview ($_REQUEST['file_id'], $_REQUEST['img']);
		break;
	default:
		renderError();
}

ob_end_flush();
}
catch (Exception $e)
{
	ob_end_clean();
	printException($e);
}



//------------------------------------------------------------------------
function renderError ()
{
	// A hardcoded value is worth of saving lots of code here.
	$img = imagecreatefrompng ('pix/error.png');
	header("Content-type: image/png");
	imagepng ($img);
	imagedestroy ($img);
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
	// only script wishing to acces the same data now.
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
		saveThumbCache ($rack_id, $capture);
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
	imagerectangle ($img, 0, 0, $totalwidth - 1, $totalheight - 1, $color['black']);
	imagerectangle ($img, 1, 1, $totalwidth - 2, $totalheight - 2, $color['gray']);
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
	header("Content-type: image/png");
	imagepng ($img);
	imagedestroy ($img);
}

function renderAccessDeniedImage ()
{
	$img = @imagecreatetruecolor (1, 1);
	imagefilledrectangle ($img, 0, 0, 1, 1, colorFromHex ($img, '000000'));
	header("Content-type: image/png");
	imagepng ($img);
	imagedestroy ($img);
}

function renderFilePreview ($file_id = 0, $mode = 'view')
{
	switch ($mode)
	{
	case 'view':
		// GFX files can become really big, if we uncompress them in memory just to
		// provide a PNG version of a file. To keep things working, just send the
		// contents as is for known MIME types.
		$file = getFile ($file_id);
		if (!in_array ($file['type'], array ('image/jpeg', 'image/png', 'image/gif')))
		{
			showError ('Invalid MIME type on file', __FUNCTION__);
			break;
		}
		header("Content-type: ${file['type']}");
		echo $file['contents'];
		break;
	case 'preview':
		$file = getFile ($file_id);
		$image = imagecreatefromstring ($file['contents']);
		unset ($file);
		$width = imagesx ($image);
		$height = imagesy ($image);
		if ($width > getConfigVar ('PREVIEW_IMAGE_MAXPXS') or $height > getConfigVar ('PREVIEW_IMAGE_MAXPXS'))
		{
			// TODO: cache thumbs for faster page generation
			$ratio = getConfigVar ('PREVIEW_IMAGE_MAXPXS') / max ($width, $height);
			$newwidth = $width * $ratio;
			$newheight = $height * $ratio;
			$resampled = imagecreatetruecolor ($newwidth, $newheight);
			imagecopyresampled ($resampled, $image, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
			imagedestroy ($image);
			$image = $resampled;
			unset ($resampled);
		}
		header("Content-type: image/png"); // don't announce content-length, it may have changed after resampling
		imagepng ($image);
		imagedestroy ($image);
		break;
	default:
		showError ('Invalid argument', __FUNCTION__);
		break;
	}
}

?>
