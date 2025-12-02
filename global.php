<?php

header('Cache-Control: no-cache');
header('Access-Control-Allow-Origin: *');
header('Pragma: no-cache');

// if( $_SERVER['REQUEST_SCHEME']=='https' ){
//     header("location: http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
//     exit(1);
// }

$get_Lvl = isset($_GET['lvl']) ? $_GET['lvl'] : null;

// Dapatkan IP klien
$client_ip = $_SERVER['REMOTE_ADDR'];

if ($client_ip=='182.16.163.206') {
    $url = "https://hub.rutan.co.id:3484/public-dashboards/2a8689e89b794f4996890f9cb84a707b?kiosk=tv";
} else {
    $url = "https://hub.rutan.cloud:3080/public-dashboards/2a8689e89b794f4996890f9cb84a707b?kiosk=tv";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>DASHBOARD DIREKSI</title>
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