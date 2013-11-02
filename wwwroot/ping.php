<?php

$host=$_POST['host'];

exec("fping -r2 -t100 $host",$output, $status);

print $status; 

?>
