<?php
/*
 * This will load the alternate routings based on item
 *    from the ALTSCAN table to the Routing Control
 */

ini_set('memory_limit', '-1');
ini_set('time_limit', '180');
ignore_user_abort();
date_default_timezone_set('America/Chicago');
include "../GConB.php";
// get the last control ID from the control table
$s = "Select max(CTLID) as MAX from rtgctl;";
$r = db2_exec($con, $s);
$row = db2_fetch_assoc($r);
$ctlid = $row['MAX'] + 1;
// get the alt scan records
$s = "Select * from ALTSCAN order by whs, fscan, seq";
$r = db2_exec($con, $s);
while ($row = db2_fetch_assoc($r)){
    $whs = $row['WHS'];
    $item = $row['ITEM'];
    $fscan = $row['FSCAN'];
    $tscan = $row['TOSCAN'];
    $seq = $row['SEQ'];
    $lvlid = 300;
    
    // in the routing control record for this warehouse - find the corresponding 
    //  scan and add the corresponding data in sequence.
    
    $s1 = "Select * from RTGCTL where WHS = $whs and SCAN = '$fscan'";
    $r1 = db2_exec($con, $s1);
    $row1 = db2_fetch_assoc($r1);
    if (trim($row1['SCAN']) !== ''){
        $ctlseq= $row1['SEQ'];
        $rowseq = $ctlseq + $seq;
        $s2 = "Insert into RTGCTL values( $ctlid, $lvlid, $whs, ' ', '$item', ' ', '$tscan', $rowseq, 5) with NC";
        $r2 = db2_exec($con, $s2);
        $msg = db2_stmt_errormsg();
        if (trim($msg) !== ''){
            echo "<br> $s2, $msg";
        }
        $ctlid += 1;
    }
}