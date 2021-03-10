<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT, GET, POST");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header("Content-Type: application/json");

ini_set('memory_limit', '-1');
ini_set('time_limit', '180');
ignore_user_abort();
date_default_timezone_set('America/Chicago');
include "../GConB.php";

$s = "Select * from rtgctl order by whs, seq";
$r = db2_exec($con, $s);
while ($row = db2_fetch_assoc($r)){
    
    $data['root'][] = $row;
}


$data['success'] = true;
echo $_GET['callback'] . '(' . json_encode($data). ')';