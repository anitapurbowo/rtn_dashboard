<?php
// header('Content-Type: application/json');
include_once('./../global_func.php');

$getBln = '';
$getThn = date('Y');
$data_pilih = '';

$getBln = isset($_REQUEST['bln']) ? $_REQUEST['bln'] :  '';
$getThn = isset($_REQUEST['thn']) ? $_REQUEST['thn'] :  date('Y');
$data_pilih = isset($_REQUEST['pilih']) ? $_REQUEST['pilih'] :  '--';

$getBln = $bulanMap[$getBln] ?? '';
// if ($getBln === null) {
//     die(json_encode(['error' => 'Invalid month']));
// }


$pembagi = 1000000; // Pembagi untuk format angka

$curl = curl_init();
$postData = [
    'target' => 'GetOpenPR',
    'key' => 'RT4qhTcXB99M3GFA59aV',
    'iddbase' => 'DB00000001',
    'idapi' => 'API0000818',
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
}
curl_close($curl);
//--------------------------------------------------------------

$responseData = json_decode($response, true);

$purchaseRequests = $responseData['result'] ?? [];
if (!empty($purchaseRequests) && is_array($purchaseRequests)) {
    usort($purchaseRequests, function($a, $b) {
        $timeA = isset($a['TglPR']) ? strtotime($a['TglPR']) : 0;
        $timeB = isset($b['TglPR']) ? strtotime($b['TglPR']) : 0;
        return $timeB - $timeA;
    });
} else {
    $purchaseRequests = [];
}

echo json_encode(['data' => $purchaseRequests]);

?>