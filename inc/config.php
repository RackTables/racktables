<?
/*
*
*  This is RackTables public configuration file.
*
*/

$enterprise = 'MyCompanyName';
// This is the name of hash used to store account password hashes in the database.
define ('PASSWORD_HASH', 'sha1');
define ('VERSION', '0.14.6');

$rtwidth[0] = 9;
$rtwidth[1] = 21;
$rtwidth[2] = 9;

// Free atoms. They are available for allocation to objects.
// They are not stored in the database.
// HSV: 180-25-75
$color['F'] = '8fbfbf';

// Absent atoms.
// HSV: 0-0-75
$color['A'] = 'bfbfbf';

// Unusable atoms. Some problems keep them to be 'F'.
// HSV: 0-25-75
$color['U'] = 'bf8f8f';

// Taken atoms. object_id should be present then.
// HSV: 180-50-50
$color['T'] = '408080';

// Taken atoms with highlight. They are not stored in the database and
// are only used for highlighting.
// HSV: 180-50-100
$color['Th'] = '80ffff';

// Taken atoms with object problem. This is detected at runtime.
// HSV: 0-50-50
$color['Tw'] = '804040';

// An object can be both current and problematic. We run highlightObject() first
// and markupObjectProblems() second.
// HSV: 0-50-100
$color['Thw'] = 'ff8080';

$nextorder['odd'] = 'even';
$nextorder['even'] = 'odd';

// Taken from the database, RJ-45/100Base-TX
$default_port_type = 6;

// Number of lines in object mass-adding form.
define ('MASSCOUNT', 15);
define ('MAXSELSIZE', 30);

// FIXME: This is taken right from the database.
define ('TYPE_SERVER', 4);
define ('TYPE_SWITCH', 8);
define ('TYPE_ROUTER', 7);

// Row-scope picture scale factor.
define ('ROW_SCALE', 2);

// Max switch port per one row on the switchvlans dynamic tab.
define ('PORTS_PER_ROW', 12);

// New-style image declarations.
$image['error']['path'] = 'pix/error.png';
$image['error']['width'] = 76;
$image['error']['height'] = 17;
$image['favicon']['path'] = 'pix/racktables.ico';
$image['favicon']['width'] = 16;
$image['favicon']['height'] = 16;
$image['logo']['path'] = 'pix/defaultlogo.png';
$image['logo']['width'] = 210;
$image['logo']['height'] = 40;
$image['rackspace']['path'] = 'pix/racks.png';
$image['rackspace']['width'] = 218;
$image['rackspace']['height'] = 200;
$image['objects']['path'] = 'pix/server.png';
$image['objects']['width'] = 218;
$image['objects']['height'] = 200;
$image['ipv4space']['path'] = 'pix/addressspace.png';
$image['ipv4space']['width'] = 218;
$image['ipv4space']['height'] = 200;
$image['config']['path'] = 'pix/configuration.png';
$image['config']['width'] = 218;
$image['config']['height'] = 200;
$image['reports']['path'] = 'pix/report.png';
$image['reports']['width'] = 218;
$image['reports']['height'] = 200;
$image['help']['path'] = 'pix/help.png';
$image['help']['width'] = 218;
$image['help']['height'] = 200;
$image['reserve']['path'] = 'pix/stop.png';
$image['reserve']['width'] = 16;
$image['reserve']['height'] = 16;
$image['useup']['path'] = 'pix/go.png';
$image['useup']['width'] = 16;
$image['useup']['height'] = 16;
$image['blockuser'] = $image['reserve'];
$image['unblockuser'] = $image['useup'];
$image['link']['path'] = 'pix/link.png';
$image['link']['width'] = 24;
$image['link']['height'] = 24;
$image['unlink']['path'] = 'pix/unlink.png';
$image['unlink']['width'] = 24;
$image['unlink']['height'] = 24;
$image['add']['path'] = 'pix/greenplus.png';
$image['add']['width'] = 16;
$image['add']['height'] = 16;
$image['delete']['path'] = 'pix/delete_s.gif';
$image['delete']['width'] = 16;
$image['delete']['height'] = 16;
$image['grant'] = $image['add'];
$image['revoke'] = $image['delete'];
$image['helphint']['path'] = 'pix/helphint.png';
$image['helphint']['width'] = 24;
$image['helphint']['height'] = 24;

?>
