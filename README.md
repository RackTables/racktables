# Welcome!
Thank you for selecting RackTables as your datacenter management solution!
If you are looking for documentation or wish to send feedback, please
look for the respective links at [project's web-site](http://racktables.org).

# How to install RackTables

## 1. Prepare the server

RackTables uses a web-server with PHP (5.2.10 or newer) for front-end and a
MySQL/MariaDB server version 5 for back-end. The most commonly used web-server
for RackTables is Apache httpd.

### 1.1. Install MySQL server

| Distribution       | How to do                                                               |
| ------------------ | ----------------------------------------------------------------------- |
| Debian 6           | `aptitude install mysql-server-5.1`                                     |
| Debian 7           | `aptitude install mysql-server-5.1`                                     |
| Fedora 8-16        | `yum install mysql-server mysql`                                        |
| Fedora 23          | `dnf install mariadb-server mariadb`                                    |
| FreeBSD 10         | `pkg install mysql56-server`                                            |
| openSUSE 42.1      | `zypper install mysql-community-server`                                 |
| Scientific Linux 6 | `yum install mysql-server mysql`                                        |
| Ubuntu 14.04       | `apt-get install mysql-server`                                          |
| Ubuntu 16.04       | `apt-get install mysql-server`                                          |

### 1.2. Enable Unicode in the MySQL server

| Distribution       | How to do                                                                                                          |
| ------------------ | ------------------------------------------------------------------------------------------------------------------ |
| Debian 6           | add `character-set-server=utf8` line to `[mysqld]` section of `/etc/mysql/my.cnf` file and restart mysqld          |
| Debian 7           | add `character-set-server=utf8` line to `[mysqld]` section of `/etc/mysql/my.cnf` file and restart mysqld          |
| Fedora 8-16        | add `character-set-server=utf8` line to `[mysqld]` section of `/etc/my.cnf` file and restart mysqld                |
| Fedora 23          | ```printf "[mysqld]\ncharacter-set-server=utf8\n" > /etc/my.cnf.d/mysqld-charset.cnf; systemctl restart mariadb``` |
| openSUSE 42.1      | No action required, comes configured for UTF-8 by default.                                                         |
| Scientific Linux 6 | add `character-set-server=utf8` line to `[mysqld]` section of `/etc/my.cnf` file and restart mysqld                |
| Ubuntu 14.04       | ```printf "[mysqld]\ncharacter-set-server=utf8\n" > /etc/mysql/conf.d/charset.cnf; service mysql restart```        |
| Ubuntu 16.04       | ```printf "[mysqld]\ncharacter-set-server=utf8\n" > /etc/mysql/conf.d/charset.cnf; service mysql restart```        |

### 1.3. Install PHP and Apache httpd (or nginx)

| Distribution       | How to do                                                                            |
| ------------------ | ------------------------------------------------------------------------------------ |
| Debian 6           | `aptitude install libapache2-mod-php5 php5-gd php5-mysql php5-snmp`                  |
| Debian 7 (nginx)   | `aptitude install nginx php5-fpm` **(see note below)**                               |
| Fedora 8-16        | `yum install httpd php php-mysql php-pdo php-gd php-snmp php-mbstring php-bcmath`    |
| Fedora 23          | `dnf install httpd php php-mysql php-pdo php-gd php-snmp php-mbstring php-bcmath`    |
| FreeBSD 10         | see note 1.3.c                                                                       | 
| openSUSE 42.1      | `zypper install apache2-mod_php5 php5-gd php5-mbstring php5-mysql php5-bcmath`       |
| Scientific Linux 6 | `yum install httpd php php-mysql php-pdo php-gd php-mbstring php-bcmath`             |
| Ubuntu 14.04       | `apt-get install apache2-bin libapache2-mod-php5 php5-gd php5-mysql php5-snmp`       |
| Ubuntu 16.04       | `apt-get install apache2-bin libapache2-mod-php7.0 php7.0-gd php7.0-mysql php7.0-mbstring php7.0-bcmath php7.0-json php7.0-snmp`

#### 1.3.a. Debian 7 with nginx
Remember to adjust `server_name` in `server {}` section, otherwise your logout link
will point to localhost (and thus fail).
Notice, that fpm.sock is advised, keep the rest on default configuration, or
tweak to your needs. You may need to set `fastcgi_read_timeout 600;` if you use
some external addons like fping, which may take some time in certain situations.
Please note that setting aggresive caching for php scripts may result in stale
content - so maximum of 60 seconds is advised, but by default it is not enabled.

#### 1.3.b. [redacted]

#### 1.3.c. FreeBSD 10
There are 3 different ways how you can install RackTables and its dependencies on FreeBSD.

######A. use pkg (Binary Package Management) (not always the newest version)
```
# pkg install racktables
# pkg install mod_php56 mysql56-server
```
As of March 2017 this will install RackTables Version 0.20.11 and its dependencies (php 5.6, mysql-server 5.6 and apache 2.4).

######B. use the ports system (possibly more recent than pkg)
```
# cd /usr/ports/sysutils/racktables
# make install
# pkg install mod_php56 mysql56-server
```
As of March 2017 this will install RackTables Version 0.20.11 and build and install its dependencies (php 5.6, mysql-server 5.6 and apache 2.4).

######C. manual (newest version)
Install dependencies with pkg:
```
# pkg install php70-bcmath php70-curl php70-filter php70-gd php70-gmp php70-json php70-mbstring php70-openssl php70-pdo php70-pdo_mysql php70-session php70-simplexml php70-snmp php70-sockets
# pkg install mod_php70 mysql56-server
```

unpack tar.gz/zip archive to `/usr/local/www`

symblink racktables dir
```
# cd /usr/local/www
# ln -s RackTables-0.20.xx racktables
```

##### Common install steps
Apache users should create a racktables.conf file under their apache
Includes directory with the following contents:
```
AddType  application/x-httpd-php         .php
AddType  application/x-httpd-php-source  .phps

<Directory /usr/local/www/racktables/wwwroot>
	DirectoryIndex index.php
	Require all granted
</Directory>
Alias /racktables /usr/local/www/racktables/wwwroot
```

Start services:
```
#echo 'apache24_enable="YES"' >> /etc/rc.conf
#service apache24 start

#echo 'mysql_enable="YES"' >> /etc/rc.conf
#service mysql-server start
```

Browse to http://address.to.your.server/racktables/index.php and follow the instructions.

Note: set `secret.php` permissions when prompted.
```
# chown www:www /usr/local/www/racktables/wwwroot/inc/secret.php
# chmod 400 /usr/local/www/racktables/wwwroot/inc/secret.php
```


## 2. Copy the files
Unpack the tar.gz/zip archive to a directory of your choice and configure Apache
httpd to use `wwwroot` subdirectory as a new DocumentRoot. Alternatively,
symlinks to `wwwroot` or even to `index.php` from an existing DocumentRoot are
also possible and often advisable (see `README.Fedora`).

## 3. Run the installer
Open the configured RackTables URL and you will be prompted to configure
and initialize the application.

| Distribution    | Apache httpd UID:GID    | MySQL UNIX socket path           |
| --------------- | ----------------------- | -------------------------------- |
| Fedora 23       | `apache:apache`         | `/var/lib/mysql/mysql.sock`      |
| openSUSE 42.1   | `wwwrun:www`            | `/var/run/mysql/mysql.sock`      |
| Ubuntu 14.04    | `www-data:www-data`     | `/var/run/mysqld/mysqld.sock`    |
| Ubuntu 16.04    | `www-data:www-data`     | `/var/run/mysqld/mysqld.sock`    |

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

## Release notes

### Upgrading to 0.20.11

New `IPV4_TREE_SHOW_UNALLOCATED` configuration option introduced to disable
dsplaying unallocated networks in IPv4 space tree. Setting it also disables
the "knight" feature.

### Upgrading to 0.20.7

From now on the minimum (oldest) release of PHP that can run RackTables is
5.2.10. In particular, to continue running RackTables on CentOS 5 it is
necessary to replace its php* RPM packages with respective php53* packages
before the upgrade (except the JSON package, which PHP 5.3 provides internally).

Database triggers are used for some data consistency measures.  The database
user account must have the 'TRIGGER' privilege, which was introduced in
MySQL 5.1.7.

The `IPV4OBJ_LISTSRC` configuration option is reset to an expression which enables
the IP addressing feature for all object types except those listed.

Tags could now be assigned on the Edit/Properties tab using a text input with
auto-completion. Type a star '*' to view full tag tree in auto-complete menu.
It is worth to add the following line to the permissions script if the
old-fashioned 'Tags' tab is not needed any more:
```
  deny {$tab_tags} # this hides 'Tags' tab
```

This release converts collation of all DB fields to the `utf8_unicode_ci`. This
procedure may take some time, and could fail if there are rows that differ only
by letter case. If this happen, you'll see the failed SQL query in upgrade report
with the "Duplicate entry" error message. Feel free to continue using your
installation. If desired so, you could eliminate the case-duplicating rows
and re-apply the failed query.

### Upgrading to 0.20.6

New `MGMT_PROTOS` configuration option replaces the `TELNET_OBJS_LISTSRC`,
`SSH_OBJS_LISTSRC` and `RDP_OBJS_LISTSRC` options (converting existing settings as
necessary). `MGMT_PROTOS` allows to specify any management protocol for a
particular device list using a RackCode filter. The default value
(`ssh: {$typeid_4}, telnet: {$typeid_8}`) produces `ssh://server.fqdn` for
servers and `telnet://switch.fqdn` for network switches.

### Upgrading to 0.20.5

This release introduces the VS groups feature. VS groups is a new way to store
and display virtual services configuration. There is a new "ipvs" (VS group)
realm. All previously existing VS configuration remains functional and user
is free to convert it to the new format, which displays it in a more natural way
and allows to generate virtual_server_group keepalived configs. To convert a
virtual service to the new format, it is necessary to manually create a VS group
object and assign IP addresses to it. The VS group will display a "Migrate" tab
to convert the old-style VS objects, which can be removed after a successful
conversion.

The old-style VS configuration becomes **deprecated**. Its support will be removed
in a future major release. So it is strongly recommended to convert it to the
new format.

### Upgrading to 0.20.4

Please note that some dictionary items of Cisco Catalyst 2960 series switches
were renamed to meet official Cisco classification:

old name    | new name
------------|---------
2960-48TT   | 2960-48TT-L
2960-24TC   | 2960-24TC-L
2960-24TT   | 2960-24TT-L
2960-8TC    | 2960-8TC-L
2960G-48TC  | 2960G-48TC-L
2960G-24TC  | 2960G-24TC-L
2960G-8TC   | 2960G-8TC-L
C2960-24    | C2960-24-S
C2960G-24PC | C2960-24PC-L

The `DATETIME_FORMAT` configuration option used in setting date and time output
format now uses a [different](http://php.net/manual/en/function.strftime.php)
syntax. During upgrade the option is reset to
the default value, which is now %Y-%m-%d (YYYY-MM-DD) per ISO 8601.

This release intoduces two new configuration options:
`REVERSED_RACKS_LISTSRC` and `NEAREST_RACKS_CHECKBOX`.

### Upgrading to 0.20.1

The 0.20.0 release includes bug which breaks IP networks' capacity displaying on
32-bit architecture machines. To fix this, this release makes use of PHP's BC
Math module. It is a new reqiurement. Most PHP distributions have this module
already enabled, but if yours does not - you need yo recompile PHP.

Security context of 'ipaddress' page now includes tags from the network
containing an IP address. This means that you should audit your permission rules
to check there is no unintended allows of changing IPs based on network's
tagset. Example:
```
	allow {client network} and {New York}
```
This rule now not only allows any operation on NY client networks, but also any
operation with IP addresses included in those networks. To fix this, you should
change the rule this way:
```
	allow {client network} and {New York} and not {$page_ipaddress}
```

### Upgrading to 0.20.0

WARNING: This release have too many internal changes, some of them were waiting
more than a year to be released. So this release is considered "BETA" and is
recommended only to curiuos users, who agree to sacrifice the stability to the
progress.

Racks and Rows are now stored in the database as Objects.  The RackObject table
was renamed to Object.  SQL views were created to ease the migration of custom
reports and scripts.

New plugins engine instead of `local.php` file. To make your own code stored in
`local.php` work, you must move the `local.php` file into the `plugins/` directory.
The name of this file does not matter any more. You also can store multiple
files in that dir, separate your plugins by features, share them and try the
plugins from other people just placing them into `plugins/` dir, no more merging.

* `$path_to_local_php` variable has no special meaning any more.
* `$racktables_confdir` variable is now used only to search for `secret.php` file.
* `$racktables_plugins_dir` is a new overridable special variable pointing to `plugins/` directory.

Beginning with this version it is possible to delete IP prefixes, VLANs, Virtual
services and RS pools from within theirs properties tab. So please inspect your
permissions rules to assure there are no undesired allows for deletion of these
objects. To ensure this, you could try this code in the beginning of permissions
script:
```
allow {userid_1} and {$op_del}
deny {$op_del} and ({$tab_edit} or {$tab_properties})
```

Hardware gateways engine was rewritten in this version of RackTables. This means
that the file `gateways/deviceconfig/switch.secrets.php` is not used any more. To
get information about configuring connection properties and credentials in a new
way please read [this](http://wiki.racktables.org/index.php/Gateways).

This also means that recently added features based on old API (D-Link switches
and Linux gateway support contributed by Ilya Evseev) are not working any more
and waiting to be forward-ported to new gateways API. Sorry for that.

Two new config variables appeared in this version:
  - `SEARCH_DOMAINS`. Comma-separated list of DNS domains which are considered
    "base" for your network. If RackTables search engine finds multiple objects
    based on your search input, but there is only one which FQDN consists of
    your input and one of these search domains, you will be redirected to this
    object and other results will be discarded. Such behavior was unconditional
    since 0.19.3, which caused many objections from users. So welcome this
    config var.
  - `QUICK_LINK_PAGES`. Comma-separated list of RackTables pages to display links
    to them on top. Each user could have his own list.

Also some of config variables have changed their default values in this version.
This means that upgrade script will change their values if you have them in
previous default state. This could be inconvenient, but it is the most effective
way to encourage users to use new features. If this behavior is not what you
want, simply revert these variables' values:

variable                | old         | new   | comment
------------------------|-------------|-------|--------
`SHOW_LAST_TAB`         | no          | yes
`IPV4_TREE_SHOW_USAGE`  | yes         | no    | Networks' usage is still available by click.
`IPV4LB_LISTSRC`        | {$typeid_4} | false
`FILTER_DEFAULT_ANDOR`  | or          | and   | This implicitly enables the feature of dynamic tree shrinking.
`FILTER_SUGGEST_EXTRA`  | no          | yes   | Yes, we have extra logical filters!
`IPV4_TREE_RTR_AS_CELL` | yes         | no    | Display routers as simple text, not cell.

Also please note that variable `IPV4_TREE_RTR_AS_CELL` now has third special value
besides 'yes' and 'no': 'none'. Use 'none' value if you are experiencing low
performance on IP tree page. It will completely disable IP ranges scan for
used/spare IPs and the speed of IP tree will increase radically. The price is
you will not see the routers in IP tree at all.
