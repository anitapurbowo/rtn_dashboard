<?php

if( $_SERVER['REQUEST_SCHEME']=='https' ){
    header("location: http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
    exit(1);
}

$get_Lvl = isset($_GET['lvl']) ? $_GET['lvl'] : null;
$get_Cab = isset($_GET['cab']) ? $_GET['cab'] : '01';

// Dapatkan IP klien
$client_ip = $_SERVER['REMOTE_ADDR'];

// $cabang = '02';

$urldash = '';
$dashboards = [
		'02' => ['url' => '/0d338c7f558443308484b70349ad11fa?kiosk=tv', 'rep' => ['02' => 'Surabaya', '03' => 'Semarang','10' => 'Subang']],
		'03' => ['url' => '/818027656b4a47b0a11c80cc71adc81e?kiosk=tv', 'rep' => []],
		'04' => ['url' => '', 'rep' => ''],
		'05' => ['url' => '/bdce259eaded4cc484ca295ecec5a7f9?kiosk=tv', 'rep' => []],
		'06' => ['url' => '/7e08c4bba2364d608814b3be7350ad1d?kiosk=tv', 'rep' => ['06' => 'Palembang', '05' => 'Lampung']],
		'07' => ['url' => '/581d12400dbb49e1bf1462d061154545?kiosk=tv', 'rep' => ['07' => 'Medan', '11' => 'Bireuen']],
		'08' => ['url' => '/a6a9c8ab8f814c14a961f6080bc79658?kiosk=tv', 'rep' => ['08' => 'Makassar', '12' => 'Sidrap']],
		'09' => ['url' => '/1941cecc040b436f9ae01b3bd06bec85?kiosk=tv', 'rep' => []],
		'10' => ['url' => '/d40f785e33ef48e6958a86b402c492b8?kiosk=tv', 'rep' => []],
		'11' => ['url' => '/29931f1bf9af4afcb73d92d593b5c26b?kiosk=tv', 'rep' => []],
		'12' => ['url' => '/5f87624001a848d69bad8e6351f01aaa?kiosk=tv', 'rep' => []]
	];

$urldash = $dashboards[$get_Cab]['url'] ?? '';

$url_local = "https://hub.rutan.co.id:3484/public-dashboards";
$url_public = "https://hub.rutan.cloud:3080/public-dashboards";

if ($client_ip=='182.16.163.206') {
    $url = $url_local . $urldash;
} else {
    $url = $url_public . $urldash;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>DASHBOARD</title>
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
        crossorigin="anonymous"
        src="<?= $url ?>" 
        title="DASHBOARD" 
        frameborder="0">
    </iframe>

</body>
</html>
