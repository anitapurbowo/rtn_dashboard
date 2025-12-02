<?php
header('Content-Type: application/json');

$getAwal = '-';
$getAkhir = '-';
$getKodeCabang = 'KOSONGAN';
$dev = 0;

if (isset($_REQUEST['aksi']) && $_REQUEST['aksi'] == 'getInvoice') {
    $namaCabang = isset($_REQUEST['nama']) ? $_REQUEST['nama'] :  0;
    $getKodeCabang =  getKodeCabang($namaCabang);
    $getBln = isset($_REQUEST['bln']) ? $_REQUEST['bln'] :  0;
    $getThn = isset($_REQUEST['thn']) ? $_REQUEST['thn'] :  0;
    $getDir = isset($_REQUEST['dir']) ? $_REQUEST['dir'] :  0;
    $getAwal = isset($_REQUEST['awal']) ? $_REQUEST['awal'] :  date("Y-m-01", strtotime("$getThn-$getBln-01"));
    $getAkhir = isset($_REQUEST['akhir']) ? $_REQUEST['akhir'] :  date("Y-m-t", strtotime("$getThn-$getBln-01"));
    $dev = isset($_REQUEST['dev']) ? $_REQUEST['dev'] :  0;
}

function getKodeCabang($namacabang) {
    $cabang = strtolower($namacabang);
    $kodecabang = [
        'pusat' => '01',
        'surabaya' => '02',
        'semarang' => '03',
        'jakarta' => '04',
        'lampung' => '05', 
        'palembang' => '06',
        'medan' => '07',
        'makassar' => '08',
        'kalimantan' => '09',
        'subang' => '10',
        'bireuen' => '11',
        'sidrap' => '12'
    ];
    return $kodecabang[$cabang] ?? 'KOSONGAN';
}


$pembagi = 1000000; // Pembagi untuk format angka

//--------------------------------------------------------------
// Inisialisasi curl
$curl = curl_init();
$postData = [
    'target' => 'GetDetailRealisasi',
    'key' => 'Zn9mbMeY5ZeN0qvN08zT',
    'iddbase' => 'DB00000033',
    'idapi' => 'API0000751',
    'TanggalMulaiAPI0000751' => $getAwal,
    'TanggalSampaiAPI0000751' => $getAkhir,
    'ItCodeAPI0000751' => $getKodeCabang,
    'DivisiAPI0000751' => 'KOSONGAN',
    'ItemCodeAPI0000751' => 'KOSONGAN',
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

$exclude = [];
if ($getDir != 1) {
    $exclude = ['PRYHO', 'EXP', 'OTH'];
} else {
    $exclude = ['OTH'];
}

$data = $responseData['result'] ?? [];
$data = array_filter($data, function ($item) use ($exclude) {
    return !in_array($item['hasil'], $exclude) &&
           intval($item['docnum']) !== 0;
});


// if ($dev=='1'):
    // echo var_dump($getAkhir);
    // header('Content-Type: application/json');
     echo json_encode(array_values($data));
// endif;

?>