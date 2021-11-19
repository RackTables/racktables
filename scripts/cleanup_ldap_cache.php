#!/usr/bin/env php
<?php

# This file is a part of RackTables, a datacenter and server room management
# framework. See accompanying file "COPYING" for the full copyright and
# licensing information.

// This script purges expired LDAP cache entries from the RackTables database.
// This maintenance job is optional and purely cosmetic -- the expired entries
// are not valid as far as RackTables authentication is concerned. It used to
// be done by RackTables front-end in the past but now you would need to run
// this script from a cron job (say, once a day) to achieve the same effect.

$script_mode = TRUE;
require_once 'inc/init.php';

if ($user_auth_src == 'ldap')
{
	constructLDAPOptions();
	discardLDAPCache ($LDAP_options['cache_expiry']);
}
