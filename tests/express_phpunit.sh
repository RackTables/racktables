#!/bin/sh

THISDIR=`dirname "$0"`
: ${PHPUNIT_BIN:=phpunit}

case `"$PHPUNIT_BIN" --version` in
	'PHPUnit 6.'*|'PHPUnit 7.'*)
		BOOTSTRAP_FILE=bootstrap_v6v7.php
		;;
	*)
		echo 'ERROR: failed to find a known version of PHPUnit'
		"$PHPUNIT_BIN" --version
		exit 5
esac

# At this point it makes sense to test specific functions.
echo "Running PHPUnit tests using bootstrap file '$BOOTSTRAP_FILE'."

cd "$THISDIR"
"$PHPUNIT_BIN" --group small --bootstrap $BOOTSTRAP_FILE || exit 1
