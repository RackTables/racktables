#!/bin/sh

THISDIR=`dirname "$0"`
BASEDIR=`readlink -f "$THISDIR/.."`

echo "Running pre-PHPUnit express tests using the base directory '$BASEDIR'."

testPHPSyntaxOnly()
{
	local FORMAT="${1:?}"
	local INPUT="${2:?}"

	if php --syntax-check "$INPUT" >/dev/null 2>&1; then
		printf "$FORMAT" "$INPUT" 'OK (syntax only)'
		return 0
	else
		printf "$FORMAT" "$INPUT" "ERROR: PHP syntax check failed"
		return 1
	fi
}

testPHPExitCodeAndOutput()
{
	local FORMAT="${1:?}"
	local INPUT="${2:?}"
	local TEMPFILE="${3:?}"
	local fname rc curdir

	fname=`basename "$INPUT"`
	curdir=`pwd`
	cd `dirname "$INPUT"`
	php "$fname" > "$TEMPFILE"
	rc=$?
	cd "$curdir"
	if [ $rc -eq 0 -a ! -s "$TEMPFILE" ]; then
		printf "$FORMAT" "$INPUT" 'OK'
		return 0
	else
		[ $rc -ne 0 ] && printf "$FORMAT" "$INPUT" "ERROR: PHP interpreter returned code $rc"
		[ -s "$TEMPFILE" ] && printf "$FORMAT" "$f" 'ERROR: produces output when parsed'
		return 1
	fi
}

# Every file in wwwroot/inc/ must be a valid PHP input file and must not
# produce any output when parsed by PHP (because, for instance, a plain text
# file is a valid PHP input file).
echo
cd "$BASEDIR"
files=0
errors=0
TEMPFILE=`mktemp /tmp/racktables_unittest.XXXXXX`
FORMAT='%-50s : %s\n'
for f in wwwroot/inc/*.php plugins/*/plugin.php; do
	if [ "$f" = "wwwroot/inc/init.php" ]; then
		testPHPSyntaxOnly "$FORMAT" "$f" || errors=`expr $errors + 1`
	else
		testPHPExitCodeAndOutput "$FORMAT" "$f" "$TEMPFILE" || errors=`expr $errors + 1`
	fi
	files=`expr $files + 1`
done
for f in tests/*.php; do
	[ -h "$f" ] && continue
	testPHPSyntaxOnly "$FORMAT" "$f" || errors=`expr $errors + 1`
	files=`expr $files + 1`
done
echo '---------------------------------------------------'
echo "Files parsed: $files, failed: $errors"
rm -f "$TEMPFILE"
[ $errors -eq 0 ] || exit 1

# The command-line scripts among other things prove that init.php actually works.
echo
cd "$BASEDIR/wwwroot"
# Requires init.php, prints usage and leaves the database intact.
echo 'Testing syncdomain.php'; ../scripts/syncdomain.php --help || exit 1
