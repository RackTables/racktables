<?php
include ('../inc/init.php');
include ('DAL_Common.php');

function getSAummary($start,$limit)
{
 if ($_GET["op"] == 'summary') {
    $_arg = 'Object Status';
    if (! empty($_POST["label"]))
         $_arg = $_POST["label"];
    $_output = getSummaryObjects($_arg);
    $_output['IPs'] = getSummaryIPs();
    $_output['Space'] = getSummarySpace();
}
 
header('Content-Type: application/json; charset=utf-8');
if (! empty($_output))
    echo json_encode($_output);