<?php

$get_Bln = isset($_GET['bln']) ? $_GET['bln'] : null;
$get_Dir = isset($_GET['dir']) ? $_GET['dir'] :  0;
$get_Thn = isset($_GET['thn']) ? $_GET['thn'] :  0;
$get_Cab = isset($_GET['var-CABANG']) ? $_GET['var-CABANG'] :  0;

$client_ip = $_SERVER['REMOTE_ADDR'];


// Tentukan URL berdasarkan IP
if ($client_ip=='182.16.163.206') {
    if ($get_Cab == '0') {
        $url = "http://172.18.34.2:8080/rutan_space/dash/Invoice/detailInvoice/detailinv.php?bln=" . $get_Bln ."&dir=". $get_Dir ."&thn=". $get_Thn;
    } else {
        $url = "http://172.18.34.2:8080/rutan_space/dash/Invoice/detailInvoice/detailinvcab.php?bln=" . $get_Bln ."&dir=". $get_Dir ."&thn=". $get_Thn ."&cab=". $get_Cab;
    }
    
} else {
    if ($get_Cab == '0') {
        $url = "http://182.16.163.206:911/rutan_space/dash/Invoice/detailInvoice/detailinv.php?bln=" . $get_Bln ."&dir=". $get_Dir ."&thn=". $get_Thn;
    } else {
        $url = "http://182.16.163.206:911/rutan_space/dash/Invoice/detailInvoice/detailinvcab.php?bln=" . $get_Bln ."&dir=". $get_Dir ."&thn=". $get_Thn ."&cab=". $get_Cab;
    }
}
// Redirect ke URL yang sesuai
header("Location: $url");
exit;
?>