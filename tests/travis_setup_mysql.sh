#!/bin/sh

if [ $# -ne 3 ]; then
	echo "This script creates a MySQL database for unit testing in Travis CI environment"
	echo "Don't run it on a production system because it may cause lots of damage."
	echo "Usage: $0 <MySQL database name> <MySQL user name> <MySQL user password>"
	exit 1
fi

DBNAME="$1"
USERNAME="$2"
PASSWORD="$3"
THISDIR=`dirname $0`
BASEDIR=`readlink -f "$THISDIR/.."`

if mysql -u root -e "SHOW TABLES FROM $DBNAME" >/dev/null 2>&1; then
	echo "Error: database $DBNAME already exists!"
	exit 1
fi

if [ -e "$BASEDIR/wwwroot/inc/secret.php" ]; then
	echo "Error: '$BASEDIR/wwwroot/inc/secret.php' already exists!"
	exit 1
fi

# The purpose of the explicit "mysql" DB below is not to fix a real bug
# but to prevent an error on my working copy when the MySQL client
# is configured (through ~/.my.cnf) to connect to the same database as
# I am trying to initialize with this script. In that specific case
# the client tries to connect to the database that doesn't yet exist
# and this script fails, hence the override to "mysql". -- Denis
mysql -u root mysql -e "CREATE DATABASE ${DBNAME} CHARACTER SET utf8 COLLATE utf8_general_ci;" || exit 2
mysql -u root -e "CREATE USER ${USERNAME}@localhost IDENTIFIED BY '${PASSWORD}';" || exit 2
mysql -u root -e "GRANT ALL PRIVILEGES ON ${DBNAME}.* TO ${USERNAME}@localhost;" || exit 2

cat > "$BASEDIR/wwwroot/inc/secret.php" <<EOF
<?php
\$pdo_dsn = 'mysql:host=localhost;port=3306;dbname=${DBNAME}';
\$db_username = '${USERNAME}';
\$db_password = '${PASSWORD}';
?>
EOF

cat > "$BASEDIR/cli_install.php" <<EOF
<?php
require_once 'wwwroot/inc/pre-init.php';
require_once 'wwwroot/inc/dictionary.php';
require_once 'wwwroot/inc/config.php';
require_once 'wwwroot/inc/install.php';
ob_start();
init_database_static();
ob_end_clean();
?>
EOF

cd "$BASEDIR"
php cli_install.php || exit 3
mysql -u root "$DBNAME" -e "INSERT INTO UserAccount (user_id, user_name, user_password_hash) VALUES (1, 'admin', SHA1('${PASSWORD}'));" || exit 3
