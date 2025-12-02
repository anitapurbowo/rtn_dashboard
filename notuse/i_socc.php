<?php
if( $_SERVER['REQUEST_SCHEME']=='https' ){
    header("location: http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
    exit(1);
}

$get_bln = isset($_GET['bln']) ? $_GET['bln'] : null;
$get_thn = isset($_GET['thn']) ? $_GET['thn'] : null;
$get_pilih = isset($_GET['pilih']) ? $_GET['pilih'] : '--';


$client_ip = $_SERVER['REMOTE_ADDR'];

// Tentukan URL berdasarkan IP
if ($client_ip=='182.16.163.206') {
    $url = "http://172.18.34.2:8080/rutan_space/dash/salesorder/soclosecancel/socc.php?bln=" . $get_bln . "&thn=" . $get_thn . "&pilih=" . str_replace(" ", "%20",$get_pilih);
} else {
    $url = "http://182.16.163.206:911/rutan_space/dash/salesorder/soclosecancel/socc.php?bln=" . $get_bln . "&thn=" . $get_thn. "&pilih=" . str_replace(" ", "%20",$get_pilih);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar SO Cancel dan Close</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../../logo.ico" type="image/x-icon">
    <style>
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
            overflow: hidden;
        }

        iframe {
            width: 100%;
            height: 100%;
            border: none;
        }
    </style>
</head>
<body>

    <iframe 
        src=<?= $url ?> 
        title="DASHBOARD" 
        frameborder="0">
    </iframe>

</body>
</html>
