<?php

// This page outputs PNG rack thumbnail.

require 'inc/init.php';

// Thumbnails are rendered in security context of rackspace.
$pageno = 'rackspace';
$tabno = 'default';
authorize();

assertUIntArg ('rack_id');
renderRackThumb ($_REQUEST['rack_id']);

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
		$color[$statecode] = getConfigVar ('color_' . $statecode);
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
				colorFromHex ($img, $color[$colorcode])
			);
		}
	}
	header("Content-type: image/png");
	imagepng ($img);
	imagedestroy ($img);
}

?>
