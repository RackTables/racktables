#!/bin/sh -e

if [ -n "$PHPUNIT_BIN" ] && ! [ -e "$PHPUNIT_BIN" ]; then
	PHPUNIT_DIRNAME=$(dirname "$PHPUNIT_BIN")
	mkdir -p "$PHPUNIT_DIRNAME"
	PHPUNIT_BASENAME=$(basename "$PHPUNIT_BIN")
	curl -sSfL -o "$PHPUNIT_BIN" "https://phar.phpunit.de/$PHPUNIT_BASENAME"
	chmod a+x "$PHPUNIT_BIN"
fi
