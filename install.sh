#!/bin/bash
# Pre checks
if ! nginx -t > /dev/null 2>&1;then
    echo "nginx is either not installed or not working properly, stopping"
    exit 1
elif ! mysql --version > /dev/null 2>&1;then
    echo "A proper mysql server isn't installed, stopping"
    exit 1
fi

# User input for database creation and secret.php
read -p "Enter database name [racktables_db]" dbName
read -p "Enter database username [racktables_user]" dbUser
read -p "Enter database password [racktables_password]" dbPass

dbName=${dbName:-racktables_db}
dbUser=${dbUser:-racktables_user}
dbPass=${dbPass:-racktables_password}

# We copy the secret.php preload file and then modify the values with the user input
cp preload/secret.php wwwroot/inc/secret.php

sed -i -e "s/racktables_db/$dbName/g" wwwroot/inc/secret.php
sed -i -e "s/racktables_user/$dbUser/g" wwwroot/inc/secret.php
sed -i -e "s/racktables_password/$dbPass/g" wwwroot/inc/secret.php

# Database creation based on user input
mysql -e "CREATE DATABASE $dbName CHARACTER SET utf8 COLLATE utf8_general_ci"
mysql -e "CREATE USER $dbUser@localhost IDENTIFIED BY '$dbPass'"
mysql -e "GRANT ALL PRIVILEGES ON $dbName.* TO $dbUser@localhost"

# Initialize database schema, default data, and admin user
php -d memory_limit=256M preload/cli_install.php

# Mofify file permissions based on racktables recommendation
chown www-data:www-data wwwroot/inc/secret.php
chmod 400 wwwroot/inc/secret.php 

# Copy the nginx configuration file for the service and remove the default
cp preload/racktables.conf /etc/nginx/sites-available/racktables.conf
rm -rf /etc/nginx/sites-enabled/default
ln -s ../sites-available/racktables.conf /etc/nginx/sites-enabled/racktables.conf
nginx -s reload

