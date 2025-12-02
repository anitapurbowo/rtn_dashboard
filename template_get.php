<?php
session_start();

$curl = curl_init();

$postData = [
    'target' => 'GetDataHotOffer',
    'key' => 'Qn6O751N9aCGEhasP1HV',
    'iddbase' => 'DB00000004',
    'idapi' => 'API0000742',
    'use_logic' => '0',
    'FokusAPI0000742' => 'LU',
];
    
if ($get_Lvl == 'DIR' || $get_Lvl == 'ACCD') {  
} else if (substr($get_Lvl, 0, 3) == 'dtC') { 
    $postData['CabangAPI0000742'] = substr($_GET['lvl'], 3, 2); 
} else { 
    $postData['DivisiAPI0000742'] = $get_Lvl; 
}

if (isset($_GET['fokus']) && !empty($_GET['fokus'])) {
    $postData['FokusAPI0000742'] = $_GET['fokus'];
}

curl_setopt_array($curl, [
    CURLOPT_URL => 'https://api.rutan.cloud',
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query($postData),
    CURLOPT_RETURNTRANSFER => true
]);

$response = curl_exec($curl);

if (curl_errno($curl)) {
    die("Curl error: " . curl_error($curl));
}

curl_close($curl);
$responseData = json_decode($response, true);
$data = $responseData['result'] ?? [];

?>
