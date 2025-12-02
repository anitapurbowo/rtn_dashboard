<?php

$getBln = '';
$getThn = date('Y');
$data_pilih = '';

$getBln = isset($_REQUEST['bln']) ? $_REQUEST['bln'] :  '';
$getThn = isset($_REQUEST['thn']) ? $_REQUEST['thn'] :  date('Y');
$data_pilih = isset($_REQUEST['pilih']) ? $_REQUEST['pilih'] :  '--';

$getBln = $bulanMap[$getBln] ?? '';

$pembagi = $pembagi_global; 

//--------------------------------------------------------------
// Inisialisasi curl
$curl = curl_init();
$postData = [
    'target' => 'GetSalesOrderCancelCloseByMonthYear',
    'key' => 'fEhrNZwcNWd6d5duZhfp',
    'iddbase' => 'DB00000001',
    'idapi' => 'API0000815',
    'BulanAPI0000815' => $getBln,
    'TahunAPI0000815' => $getThn,
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

$data = $responseData['result'] ?? [];
$kolom_cab = array_column($data, 'Cab');
$kolom_tgl = array_column($data, 'TglClose');

array_multisort(
    $kolom_tgl, SORT_DESC, SORT_STRING, // Kriteria 1
    $kolom_cab, SORT_ASC, SORT_STRING, // Kriteria 2
    $data                      // Array utama yang akan diurutkan
);

$datatampil = array_values($data);

$data_close = [];
$data_cancel = [];

foreach ($datatampil as $item) {
    // var_dump($data_pilih);
    // die();
    if ($data_pilih != '--' && $item['Kategori'] != $data_pilih) {
        continue;
    }

    if (isset($item['SttSO'])) {
        if ($item['SttSO'] == 'CLOSE') {
            $data_close[] = $item;
        } 
        elseif ($item['SttSO'] == 'CANCEL') {
            $data_cancel[] = $item;
        }
    }
}

foreach ($datatampil as $r) {
    $cab = isset($r['Cab']) && $r['Cab'] !== '' ? $r['Cab'] : 'UNKNOWN';
    if (!isset($counts_close[$cab])) $counts_close[$cab] = 0;
    $count[$cab]++;
}


$periode_file = $getBln . '-' . $getThn;

?>