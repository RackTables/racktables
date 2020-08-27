#!/bin/sh

THISDIR=`dirname "$0"`
BASEDIR=`readlink -f "$THISDIR/.."`

which php >/dev/null || {
	echo 'ERROR: PHP CLI binary is not available!' >&2
	exit 1
}
echo "Running pre-PHPUnit express tests using the base directory '$BASEDIR'."

printTestResult()
{
	printf '%-50s : %s\n' "${1:?}" "${2:?}"
}

testPHPSyntaxOnly()
{
	local INPUT="${1:?}"

	if php --syntax-check "$INPUT" >/dev/null 2>&1; then
		printTestResult "$INPUT" 'OK (syntax only)'
		return 0
	else
		printTestResult "$INPUT" 'ERROR: PHP syntax check failed'
		return 1
	fi
}

testPHPExitCodeAndOutput()
{
	local INPUT="${1:?}"
	local TEMPFILE="${2:?}"
	local fname rc curdir

	fname=`basename "$INPUT"`
	curdir=`pwd`
	cd `dirname "$INPUT"`
	php "$fname" > "$TEMPFILE"
	rc=$?
	cd "$curdir"
	if [ $rc -eq 0 -a ! -s "$TEMPFILE" ]; then
		printTestResult "$INPUT" 'OK'
		return 0
	else
		[ $rc -ne 0 ] && printTestResult "$INPUT" "ERROR: PHP interpreter returned code $rc"
		[ -s "$TEMPFILE" ] && printTestResult "$INPUT" 'ERROR: produces output when parsed'
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
TEMPFILE=`mktemp --tmpdir racktables_unittest.XXXXXX`
for f in wwwroot/inc/*.php plugins/*/plugin.php; do
	if [ "$f" = "wwwroot/inc/init.php" ]; then
		testPHPSyntaxOnly "$f" || errors=`expr $errors + 1`
	else
		testPHPExitCodeAndOutput "$f" "$TEMPFILE" || errors=`expr $errors + 1`
	fi
	files=`expr $files + 1`
done
for f in tests/*.php; do
	[ -h "$f" ] && continue
	testPHPSyntaxOnly "$f" || errors=`expr $errors + 1`
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
