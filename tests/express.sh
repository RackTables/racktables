#!/bin/sh

THISDIR=`dirname $0`
BASEDIR=`readlink -f "$THISDIR/.."`
: ${PHPUNIT_BIN:=phpunit}

case `"$PHPUNIT_BIN" --version` in
	'PHPUnit 4.'*|'PHPUnit 5.'*)
		BOOTSTRAP_FILE=bootstrap_v4v5.php
		;;
	'PHPUnit 6.'*|'PHPUnit 7.'*)
		BOOTSTRAP_FILE=bootstrap_v6v7.php
		;;
	*)
		echo 'ERROR: failed to find a known version of PHPUnit'
		"$PHPUNIT_BIN" --version
		exit 5
esac

echo "Running express tests using the base directory '$BASEDIR'"
echo "and PHPUnit bootstrap file '$BOOTSTRAP_FILE'."

# Every file in wwwroot/inc/ must be a valid PHP input file and must not
# produce any output when parsed by PHP (because, for instance, a plain text
# file is a valid PHP input file).
echo
cd "$BASEDIR"
files=0
errors=0
TEMPFILE=`mktemp /tmp/racktables_unittest.XXXXXX`
FORMAT='%-40s : %s\n'
for f in wwwroot/inc/*.php plugins/*/plugin.php; do
	if [ "$f" = "wwwroot/inc/init.php" ]; then
		printf "$FORMAT" "$f" 'not tested'
		continue # see below
	fi
	fname=`basename "$f"`
	cd `dirname "$f"`
	php "$fname" > "$TEMPFILE"
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
	cd "$BASEDIR"
done
for f in tests/*.php; do
	if php --syntax-check "$f" >/dev/null 2>&1; then
		printf "$FORMAT" "$f" 'OK (syntax only)'
	else
		printf "$FORMAT" "$f" "ERROR: PHP syntax check failed"
		errors=`expr $errors + 1`
	fi
	files=`expr $files + 1`
done
echo '---------------------------------------------------'
echo "Files parsed: $files, failed tests: $errors"
rm -f "$TEMPFILE"
[ $errors -eq 0 ] || exit 1

# A side effect of syncdomain.php is testing whether init.php is functional.
echo
cd "$BASEDIR/wwwroot"
echo 'Testing syncdomain.php'; ../scripts/syncdomain.php --help || exit 1
echo 'Testing cleanup_ldap_cache.php'; ../scripts/cleanup_ldap_cache.php || exit 1
echo 'Testing reload_dictionary.php'; ../scripts/reload_dictionary.php || exit 1

# At this point it makes sense to test specific functions.
echo
cd "$BASEDIR/tests"
"$PHPUNIT_BIN" --group small --bootstrap $BOOTSTRAP_FILE || exit 1
