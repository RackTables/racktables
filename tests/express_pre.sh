#!/bin/sh

THISDIR=$(dirname "$0")
BASEDIR=$(readlink -f "$THISDIR/..")

command -v php >/dev/null || {
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
	INPUT="${1:?}"

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
	INPUT="${1:?}"
	TEMPFILE=$(mktemp --tmpdir racktables_unittest.XXXXXX)
	myrc=2

	fname=$(basename "$INPUT")
	curdir=$(pwd)
	cd "$(dirname "$INPUT")" || return 1
	php "$fname" > "$TEMPFILE"
	rc=$?
	cd "$curdir" || return 1
	if [ $rc -eq 0 ] && [ ! -s "$TEMPFILE" ]; then
		printTestResult "$INPUT" 'OK'
		myrc=0
	else
		[ $rc -ne 0 ] && printTestResult "$INPUT" "ERROR: PHP interpreter returned code $rc"
		[ -s "$TEMPFILE" ] && printTestResult "$INPUT" 'ERROR: produces output when parsed'
		myrc=1
	fi
	rm -f "$TEMPFILE"
	return $myrc
}

# Every file in wwwroot/inc/ must be a valid PHP input file and must not
# produce any output when parsed by PHP (because, for instance, a plain text
# file is a valid PHP input file).
echo
cd "$BASEDIR" || exit 1
files=0
errors=0
for f in wwwroot/inc/*.php plugins/*/plugin.php; do
	if [ "$f" = "wwwroot/inc/init.php" ]; then
		testPHPSyntaxOnly "$f" || errors=$((errors + 1))
	else
		testPHPExitCodeAndOutput "$f" || errors=$((errors + 1))
	fi
	files=$((files + 1))
done
for f in tests/*.php; do
	[ -h "$f" ] && continue
	testPHPSyntaxOnly "$f" || errors=$((errors + 1))
	files=$((files + 1))
done
echo '---------------------------------------------------'
echo "Files parsed: $files, failed: $errors"
[ $errors -eq 0 ] || exit 1

# The command-line scripts among other things prove that init.php actually works.
echo
cd "$BASEDIR/wwwroot" || exit 1
# Requires init.php, prints usage and leaves the database intact.
echo 'Testing syncdomain.php'; ../scripts/syncdomain.php --help || exit 1
