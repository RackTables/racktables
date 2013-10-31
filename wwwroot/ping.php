<?php

$host=$_POST['host'];

//if (define($host))

exec("fping -r2 -t100 $host",$output, $status);
//$result=`fping $host`;

print $status; //



?>
