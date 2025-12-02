<?php

// $get_Lvl = isset($_GET['lvl']) ? $_GET['lvl'] : null;

// $client_ip = $_SERVER['REMOTE_ADDR'];


// // Tentukan URL berdasarkan IP
// if ($client_ip=='182.16.163.206') {
//     $url = "http://172.18.34.2:8080/rutan_space/div/age.php";
// } else {
//     $url = "http://182.16.163.206:911/rutan_space/div/age.php";
// }
// // Redirect ke URL yang sesuai
// header("Location: $url");
// exit;
?>

<?php
// if( $_SERVER['REQUEST_SCHEME']=='https' ){
//     header("location: http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
//     exit(1);
// }
// if( $_SERVER['REQUEST_SCHEME']=='https' ){
//     $port = 3080;
// } else {
//     $port = 3484;
// }

// Dapatkan IP klien
$client_ip = $_SERVER['REMOTE_ADDR'];

if ($client_ip=='182.16.163.206') {
    $url = "https://hub.rutan.co.id:3484/public-dashboards/ff7d229ea3ea450fa249452bd26b52f1?kiosk=tv";
} else {
    $url = "https://hub.rutan.cloud:3080/public-dashboards/ff7d229ea3ea450fa249452bd26b52f1?kiosk=tv";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>AGE</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="logo.ico" type="image/x-icon">
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