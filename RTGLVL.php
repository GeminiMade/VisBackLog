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
 
if (isset($_GET['records'])){
    $recs1 = json_decode($_GET['records']);
    $recs2 = json_decode(json_encode($recs1), true);
 
    $scan = $recs2['SCAN'];
    $plvl = $recs2['PRODLVL'];
    $lvl = ($plvl == 'true') ? $recs2['LVLID']:'010';
    $pl = ($plvl == 'true') ? 'Y':'N';
    if (trim($lvl)  == '') $lvl = '100';
    $s = "Update RTGLVL set PRODLVL = '$pl', LVLID = $lvl where scan = '$scan' with NC";
    db2_exec($con, $s);
    $recs2['LVLID'] = $lvl;
    $data['ASQL'] = $s;
    $data['AMSG'] = db2_stmt_errormsg();
    $data['root'][] = $recs2;
    
} else {


$s = "Select * from Scan_To_Level order by WFSEQ, LVLID";
$r = db2_exec($con, $s);
while ($row = db2_fetch_assoc($r)){
    
    $row['PRODLVL'] = (trim($row['PRODLVL']) == 'Y') ? true: false;
    
    $data['root'][] = $row;
}
}

$data['success'] = true;
echo $_GET['callback'] . '(' . json_encode($data). ')';