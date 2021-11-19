# This directory stores PHPUnit tests.

To run a specific test:
```
$ phpunit TestName
```

To run all small tests (several seconds to complete):
```
$ phpunit --group small
```

To run all tests (several minutes to complete):
```
$ phpunit
```

## The Unit Testing Database

Tests should never be run against a production instance of RackTables.

The `bootstrap.php` script (configured for use in `phpunit.xml`) calls
method `TestHelper::ensureUsingUnitTestDatabase()` to check that the
database DSN contains the string "_unittest".

Checking the DSN is a relatively easy way to ensure that tests are
only run with a dedicated testing database.  While PHPUnit supports
[Database_TestCases](https://phpunit.de/manual/current/en/database.html),
the RackTables tests do not yet use that framework.  This may be
incorporated at a future date.


## Creating and configuring a Unit Testing Database

Assuming you have installed your development RackTables using the web
interface, you will already have a working database.  You can clone
that database to a new dedicated unit testing database (where the
database name contains the string "_unittest") from the command line.
Note that you should use the `$db_username` variable contained in
`wwwroot/inc/secret.php` instead of "racktables_user".

```
  mysql -u root -p
  CREATE DATABASE racktables_unittest CHARACTER SET utf8 COLLATE utf8_general_ci;
  CREATE USER IF NOT EXISTS racktables_user@localhost IDENTIFIED BY 'MY_SECRET_PASSWORD';
  GRANT ALL PRIVILEGES ON racktables_unittest.* TO racktables_user@localhost;
  exit
```

Then duplicate the existing database to your new unit testing database:

```
  mysqldump -u racktables_user -p MY_SECRET_PASSWORD racktables_db | \ 
      mysql -u racktables_user -p MY_SECRET_PASSWORD racktables_unittest
```

Edit the `secret.php` file and change the dbname in `$pdo_dsn` to the new
"_unittest" database, e.g.:
```
  $pdo_dsn = 'mysql:host=127.0.0.1;dbname=racktables_unittest';
```
To switch to another database, edit the `secret.php` file.
