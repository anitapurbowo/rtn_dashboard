<?php
header('Content-Type: application/json');
include_once('../global_func.php');

$tahunIni = date('Y');
$tanggalAwal = $tahunIni . '-01-01';
$tanggalAkhir = $tahunIni . '-12-31';

//------------------------
$curl = curl_init();
$postData = [
    'target' => 'GetDataRealisasiAmountDiluarProyek',
    'key' => 'RVTnpsUhPZbQ4EQLL9li',
    'iddbase' => 'DB00000033',
    'idapi' => 'API0000747',
    'TanggalMulaiAPI0000747' => $tanggalAwal,
    'TanggalSampaiAPI0000747' => $tanggalAkhir
];
curl_setopt_array($curl, [
    CURLOPT_URL => 'https://api.rutan.cloud',
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query($postData),
    CURLOPT_RETURNTRANSFER => true
]);

$response = curl_exec($curl);
if (curl_errno($curl)) {
    die("Curl error: " . curl_error($curl));
};
curl_close($curl);
//------------------------
$responseData = json_decode($response, true);
echo json_encode($responseData);
// echo(print_r($responseData['result'], true));
?>