<?php

/*
Bumping up of CODE_VERSION requires precise timing as described in the
Developer's Guide. Otherwise working copies updated from git (for example,
committers' copies) can run into issues:
1. The source is rendered unfunctional after "git pull", asking users to
   finish the "upgrade".
2. Once the batch for the "upgrade" is executed, the queries that get added
   to the batch later are likely to receive no real execution.
3. In case the executed part of such partial batch is found incorrect later,
   but before the release, fixing the wrong queries will be harder, hence they
   have already been executed.
*/

define ('CODE_VERSION', '0.20.14');

?>
