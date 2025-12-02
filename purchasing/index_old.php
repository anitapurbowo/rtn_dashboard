------------------------------BEFORE 27 NOV 2025-------------------------------------
<?php
if( $_SERVER['REQUEST_SCHEME']=='https' ){
    header("location: http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
    exit(1);
}

$get_Lvl = isset($_GET['lvl']) ? $_GET['lvl'] : null;

$client_ip = $_SERVER['REMOTE_ADDR'];


// Tentukan URL berdasarkan IP
if ($client_ip=='182.16.163.206') {
    $url = "http://172.18.34.2:8080/rutan_space/div/purch/prNotYetFullFill.php";
} else {
    $url = "http://182.16.163.206:911/rutan_space/div/purch/prNotYetFullFill.php";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Purchase Request</title>
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
        title="Daftar Purchase Request Belum Terpenuhi" 
        frameborder="0">
    </iframe>

</body>
</html>