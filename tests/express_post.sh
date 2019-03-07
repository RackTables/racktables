#!/bin/sh

THISDIR=`dirname "$0"`
BASEDIR=`readlink -f "$THISDIR/.."`

echo "Running post-PHPUnit express tests using the base directory '$BASEDIR'."
cd "$BASEDIR/wwwroot"
# PHPUnit would fail if this was not a unit testing database, hence
# at this point is is OK to let the scripts below make changes.
echo 'Testing cleanup_ldap_cache.php'; ../scripts/cleanup_ldap_cache.php || exit 1
echo 'Testing reload_dictionary.php'; ../scripts/reload_dictionary.php || exit 1
