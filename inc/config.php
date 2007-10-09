<?
/*
*
*  This is RackTables public configuration file.
*
*/


/* The following parameters/constants are necessary, although they are unlikely
 * to change. They just have to be stored somewhere and this is the place.
 */

// This is the name of hash used to store account password hashes in the database.
define ('PASSWORD_HASH', 'sha1');
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



/*******************************************************************************
 * The following parameters are likely to be changed by user, thus they
 * are listed below until we implement a configuration storage to move
 * them there.
 */

$enterprise = 'MyCompanyName';

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

/*******************************************************************************
 * And finally there are some things that we'd still like to see in the
 * configuration storage, but not changeable by user.
 */
define ('VERSION', '0.14.6');

?>
