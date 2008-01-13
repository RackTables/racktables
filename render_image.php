<?php

// This page outputs PNG rack thumbnail.

require 'inc/init.php';

assertStringArg ('img');
switch ($_REQUEST['img'])
{
	case 'minirack':
		// Thumbnails are rendered in security context of rackspace.
		$pageno = 'rackspace';
		$tabno = 'default';
		authorize();
		assertUIntArg ('rack_id');
		renderRackThumb ($_REQUEST['rack_id']);
		break;
	case 'progressbar':
		assertUIntArg ('done', TRUE);
		renderProgressBarImage ($_REQUEST['done']);
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
function generateMiniRack ($rack_id)
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
	imagerectangle ($img, 0, 0, $totalwidth - 1, $totalheight - 1, colorFromHex ($img, '000000'));
	imagerectangle ($img, 1, 1, $totalwidth - 2, $totalheight - 2, colorFromHex ($img, 'c0c0c0'));
	imagerectangle ($img, 2, 2, $totalwidth - 3, $totalheight - 3, colorFromHex ($img, '000000'));
	for ($unit_no = $rackData['height']; $unit_no > 0; $unit_no--)
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
	$color['T'] = colorFromHex ($img, getConfigVar ('color_T'));
	$color['F'] = colorFromHex ($img, getConfigVar ('color_F'));
	imagefilledrectangle ($img, 0, 0, $done, 10, $color['T']);
	imagefilledrectangle ($img, $done, 0, 100, 10, $color['F']);
	header("Content-type: image/png");
	imagepng ($img);
	imagedestroy ($img);
}

?>
