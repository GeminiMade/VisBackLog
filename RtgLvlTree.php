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

$s = "Select * from rtggrp order by wfseq";
$r = db2_exec($con, $s);
while ($row = db2_fetch_assoc($r)){
  //  
    $grplvl = $row['LVLGRP'];
    $y['text'] = $row['LVLGRPD'];
    $y['info'] = $row['LVLGRPINFO'];
    $y['seq'] = $row['WFSEQ'];
  
   // $y['expanded']= true;
    $y['iconCls']= "task-folder";
    $s1 = "Select * from scan_to_level where LVLGRP = '$grplvl' order by LVLID";
    $r1 = db2_exec($con, $s1);
    while ($row1= db2_fetch_assoc($r1)){
        $z['LVLID'] =  $row1['LVLID'];
        $z['LVLD'] = trim($row1['LVLD']);
        
        $z['PRDLVL'] = (trim($row1['PRODLVL']) == 'Y') ? true: false;
        $z['SCAND'] = $row1['SCAND'];
        $z['SCAN'] = $row1['SCAN'];
        $z['iconCls']= "task-folder";
        $z['expanded']= false;
        $z['leaf'] = true;
        $zdata[] = $z;
    }
    if ($zdata !== null){
    $y['children'] = $zdata;
    $y['expanded']= true;
    $y['leaf'] = false;
    } else {
        $y['leaf'] = true;
        $y['expanded']= false;
    }
    $xdata[] = $y;
}
//$ydata['expanded']= true;
$ydata['children'] = $xdata;
//$data['expanded']= true;
$data   = $ydata;
$data['success'] = true;
echo $_GET['callback'] . '(' . json_encode($data). ')';