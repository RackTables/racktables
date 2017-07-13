#!/bin/sh

THISDIR=`dirname $0`
BASEDIR=`readlink -f "$THISDIR/.."`

echo "Running express tests using the base directory '$BASEDIR'"

# Every file in wwwroot/inc/ must be a valid PHP input file and must not
# produce any output when parsed by PHP (because, for instance, a plain text
# file is a valid PHP input file).
echo
cd "$BASEDIR/wwwroot/inc"
files=0
errors=0
TEMPFILE=`mktemp /tmp/racktables_unittest.XXXXXX`
FORMAT='%-25s : %s\n'
for f in *.php; do
	[ "$f" = "init.php" ] && continue # see below
	php "$f" > "$TEMPFILE"
	rc=$?
	if [ $rc -eq 0 -a ! -s "$TEMPFILE" ]; then
		printf "$FORMAT" "$f" 'OK'
	else
		if [ $rc -ne 0 ]; then
			printf "$FORMAT" "$f" "ERROR: PHP interpreter returned code $rc"
			errors=`expr $errors + 1`
		fi
		if [ -s "$TEMPFILE" ]; then
			printf "$FORMAT" "$f" 'ERROR: produces output when parsed'
			errors=`expr $errors + 1`
		fi
	fi
	files=`expr $files + 1`
done
echo '------------------------------------'
echo "Files parsed: $files, failed tests: $errors"
rm -f "$TEMPFILE"
[ $errors -eq 0 ] || exit 1

# A side effect of syncdomain.php is testing whether init.php is functional.
echo
cd "$BASEDIR/wwwroot"
echo 'Testing syncdomain.php'; ../scripts/syncdomain.php --help || exit 1
echo 'Testing cleanup_ldap_cache.php'; ../scripts/cleanup_ldap_cache.php || exit 1

# At this point it makes sense to test specific functions.
echo
cd "$BASEDIR/tests"
phpunit --group small || exit 1
