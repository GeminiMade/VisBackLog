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
$exlvl = '';
if (isset($_GET['Action'])){
    $action = $_GET['Action'];
    if (trim($action) == 'Drop'){
        
        $lvlid =  $_GET['SEQ'];
        $scan = $_GET['SCAN'];
        $grp = $_GET['GRP'];
        
        
        $s = "Update RTGLVL set lvlid = $lvlid, LVLGRP = '$grp' where scan = '$scan' with NC";
        db2_exec($con, $s);
        $data['ASQL'] = $s;
        $data['AMSG'] = db2_stmt_errormsg();
        $exlvl = $grp;
        
        
    }
    if (trim($action) == 'Update'){
        $scan = $_GET['SCAN'];
        $grp = $_GET['GRP'];
        $lvlid = $_GET['VAL'];
        $s = "Update RTGLVL set lvlid = $lvlid where scan = '$scan' with NC";
        db2_exec($con, $s);
        $data['ASQL'] = $s;
        $data['AMSG'] = db2_stmt_errormsg();
        $exlvl = $grp;
        
    }
    
    
    
}


$s = "Select * from rtggrp order by wfseq";
$r = db2_exec($con, $s);
while ($row = db2_fetch_assoc($r)){
  //  
    $grplvl = $row['LVLGRP'];
    $y['LVLGRP'] = $row['LVLGRP'];
    $y['text'] = trim($row['LVLGRPD']);
    $y['info'] = trim($row['LVLGRPINFO']);
    $y['seq'] = $row['WFSEQ'];
    $y['expanded']= false;
    $y['GRP'] = trim($grplvl) . '/' . trim($exlvl);
    if (trim($grplvl) == trim($exlvl)){
        $y['expanded']= true;
    }
  
   // $y['expanded']= true;
    $y['iconCls']= "task-folder";
    $s1 = "Select * from scan_to_level where LVLGRP = '$grplvl' order by LVLID";
    $r1 = db2_exec($con, $s1);
    while ($row1= db2_fetch_assoc($r1)){
        $z['LVLID'] =  $row1['LVLID'];
        $z['LVLD'] = trim($row1['LVLD']);
        $z['LVLGRP'] = $row1['LVLGRP'];
        $z['seq'] =  $row1['SEQ'];
        $z['PRDLVL'] = (trim($row1['PRODLVL']) == 'Y') ? true: false;
        $z['SCAND'] = $row1['SCAND'];
        $z['SCAN'] = $row1['SCAN'];
        $z['iconCls']= "task-folder";
        $z['expanded']= ($row1['LVLID'] == $exlvl)?  true: false;
        
        $z['leaf'] = true;
        $zdata[] = $z;
    }
    if ($zdata !== null){
    $y['children'] = $zdata;
    
    $y['leaf'] = false;
    } else {
        $y['leaf'] = true;
        
    }
    $data['children'][] = $y;
//    $xdata['children'] = $y;
$zdata = array();
   
}

//$data['children']= $xdata;
$data['expanded']= true;
//$data['text'] = 'Root'; 
//$data['success'] = true;

 echo $_GET['callback'] . '(' . json_encode($data). ')';
//echo json_encode($data);