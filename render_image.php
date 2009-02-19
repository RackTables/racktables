<?php

$_REQUEST['page'] = 'rackspace';
$_REQUEST['tab'] = 'default';

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
			renderFilePreview ($_REQUEST['file_id']);
		break;
	default:
		renderError();
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
	header("Content-type: image/png");
	$thumbcache = loadThumbCache ($rack_id);
	if ($thumbcache !== NULL)
		echo $thumbcache;
	else
	{
		ob_start();
		generateMiniRack ($rack_id);
		$capture = ob_get_contents();
		ob_end_flush();
		saveThumbCache ($rack_id, $capture);
	}
}

// Output a binary string containing the PNG minirack.
function generateMiniRack ($rack_id = 0)
{
	if (($rackData = getRackData ($rack_id, TRUE)) == NULL)
	{
		renderError();
		return;
	}
	markupObjectProblems ($rackData);
	// Cache in a local array, because we are going to use those values often.
	$rtwidth = array
	(
		0 => getConfigVar ('rtwidth_0'),
		1 => getConfigVar ('rtwidth_1'),
		2 => getConfigVar ('rtwidth_2')
	);
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

function renderFilePreview ($file_id = 0)
{
	$file = getFile ($file_id);
	$image = imagecreatefromstring ($file['contents']);
	$width = imagesx ($image);
	$height = imagesy ($image);
	if ($_REQUEST['img'] == 'preview' and ($width > getConfigVar ('PREVIEW_IMAGE_MAXPXS') or $height > getConfigVar ('PREVIEW_IMAGE_MAXPXS')))
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
}

?>
