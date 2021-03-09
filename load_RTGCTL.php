<?php

ini_set('memory_limit', '-1');
ini_set('time_limit', '180');
ignore_user_abort();
date_default_timezone_set('America/Chicago');
include "../GConB.php";
// define variables
$cntrlid = 1;
$lvlid = 100;
$cls = ' ';
$optn = ' ';
$item = ' ';
$seq = 0;
$std = 0;
// Read BLR.TNS file
$s = "Select * from BLR.TNS";
$r = db2_exec($con, $s);
while ($row = db2_fetch_assoc($r)){
    $ctlid = $row['CTLID'];
    echo "<h5>$ctlid</h5>";
    $scan = $row['FSCAN'];
    $std = $row['STNSTR'];
    // get the warehouehouses associated with the group ID 
    $s1 = "Select * from BLR.DGRPD where grpid = $ctlid";
    $r1 = db2_exec($con, $s1);
    while ($row1 = db2_fetch_assoc($r1)){
        $whs = $row1['WHS'];
       
        echo "<br>$whs $scan, $seq";
        $s2 = "Merge into RTGCTL t using table(values(
$cntrlid, $lvlid, $whs, '$cls', '$optn', '$item', '$scan', $seq,$std)) s 
(CTLID, LVLID, WHS, CLS, OPTN, ITEM, SCAN, SEQ, STDPRD) on
s.WHS = t.WHS and s.SCAN = t.SCAN  
When not matched then insert
 values(s.CTLID, s.LVLID, s.WHS, s.CLS, s.OPTN, s.ITEM, s.SCAN, s.SEQ, s..STDPRD) 
with NC";
        $r2 = db2_exec($con, $s2);
        $msg = db2_stmt_errormsg();
        if (trim($msg) !== ''){
            echo "<br> $s2, $msg";
        }
        $ctlid +=1;
    }
    $seq += 10;
}