# Welcome!
Thank you for selecting RackTables as your datacenter management solution!
If you are looking for documentation or wish to send feedback, please
look for the respective links at the [project's web-site](https://racktables.org).

This is a fork of the main project with modifications that I personnally want and are made available for you to use/modify.

# Quick install
The following steps are expected to be ran as **root** and on a debian system (tested on debian 12).   
If you are using a different distribution of Linux please make sure you install the correct packages in the next step.
## 1. Install prerequisites
```
apt install mariadb-server nginx php-fpm php-gd php-mysql php-mbstring php-bcmath php-json php-snmp
```

## 2. Git clone project
``` 
cd /var/www/html
git clone https://github.com/tinihawk/racktables.git
```

## 3. Run installer script
```
cd /var/www/html/racktables
chmod +x install.sh
./install.sh
```

## 4. Connect to the server
Navigate to `http://<server ip address>` and you should be met with a login page.
The default username is `admin` and the password is also `admin`

# Manual install RackTables
<details>

<summary>Click here to expand</summary>

## 1. Prepare the server

RackTables needs a webserver that supports PHP 7.0+ (tested with 8.2) for front-end and
a MySQL/MariaDB server version 5 or later for back-end. 

### 1.1 Install prerequisites

| Distribution       | command                                                                              |
| ------------------ | ------------------------------------------------------------------------------------ |
| Debian based       | `apt install mariadb-server nginx php-fpm php-gd php-mysql php-mbstring php-bcmath php-json php-snmp` |


### 1.2 Prepare nginx
Create the `/etc/nginx/sites-available/racktables.conf` configuration file with the following content :
```
server {
        listen 80;
        listen [::]:80;
        root /var/www/html/racktables/wwwroot;
        #index index.php;
        autoindex on;
        location / {
                        try_files $uri $uri/ =404;
        }

        location ~ \.php$ {
                try_files $uri =404;
                fastcgi_split_path_info ^(.+?\.php)(/.+)?$;
                fastcgi_pass unix:/var/run/php/php-fpm.sock;
                fastcgi_index index.php;
                include fastcgi_params;
                fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        }
}
```
Create a symlink between `sites-available` and `sites-enabled`
```
cd /etc/nginx/sites-enabled/
ln -s ../sites-available/racktables.conf racktables.conf 
```

Remove default webserver and restart nginx
```
rm -rf /etc/nginx/sites-enabled/default
nginx -t
nginx -s reload
```

### 1.3 Download project
Symlinks don't work with nginx so we download directly into `/var/www/html`
```
cd /var/www/html
git clone https://github.com/tinihawk/racktables.git
```

## 2 Server configuration
Once the files are downloaded locally you can browse to `http://<server ip address>` and follow the instructions.  
You will need the secret.php file to be writable by all.

### 2.1 Harden the secret file
When prompted, set `secret.php` permissions as so :
```
chown www-data:www-data /var/www/html/racktables/wwwroot/inc/secret.php
chmod 400 /var/www/html/racktables/wwwroot/inc/secret.php 
```
</details>

# How to upgrade RackTables

0. **Backup your database** and check the release notes below before actually
   starting the upgrade.
1. Remove all existing files except configuration (the `inc/secret.php` file)
   and local plugins (in the `plugins/` directory).
2. Put the contents of the new tar.gz/zip archive into the place.
3. Open the RackTables page in a browser. The software will detect version
   mismatch and display a message telling to log in as admin to finish
   the upgrade.
4. Do that and report any errors to the bug tracker or the mailing list.

