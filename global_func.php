<?php

@require_once('token_tool.php');

$secret = 'RUTAN_DASHBOARD';
global $secret;
$pembagi_global = 1000000;

$allowedLevels = ['DIR', 'AAI', 'AGE', 'ACCD', 'dtC'];

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

function getNamaCabang($kodecabang) {
    // $cabang = strtolower($kodecabang);
    $namacabang = [
        '01' => 'pusat',
        '02' => 'surabaya',
        '03' => 'semarang',
        '04' => 'jakarta',
        '05' => 'lampung', 
        '06' => 'palembang',
        '07' => 'medan',
        '08' => 'makassar',
        '09' => 'kalimantan',
        '10' => 'subang',
        '11' => 'bireuen',
        '12' => 'sidrap'
    ];
    return $namacabang[$kodecabang] ?? '';
}

function getCabang($kodecabang) {
    $branchMap = [
        '02' => ['code' => 'S',  'name' => 'Surabaya'],
        '03' => ['code' => 'SR', 'name' => 'Semarang'],
        '04' => ['code' => 'V',  'name' => 'Jakarta'],
        '05' => ['code' => 'WL',  'name' => 'Lampung'],
        '06' => ['code' => 'W', 'name' => 'Palembang'],
        '07' => ['code' => 'Y', 'name' => 'Medan'],
        '08' => ['code' => 'U', 'name' => 'Makassar'],
        '09' => ['code' => 'K', 'name' => 'Kalimantan'],
        '10' => ['code' => 'SS', 'name' => 'Subang'],
        '11' => ['code' => 'YB', 'name' => 'Bireuen'],
        '12' => ['code' => 'US', 'name' => 'Sidrap']
        ];
    return $branchMap[$kodecabang] ?? ['code' => 'XX', 'name' => 'Unknown'];
}

function getNamaBulan($bulanangka) {
    $bulanMap = [
        '01' => 'Januari',
        '02' => 'Februari',
        '03' => 'Maret',
        '04' => 'April',
        '05' => 'Mei',
        '06' => 'Juni',
        '07' => 'Juli',
        '08' => 'Agustus',
        '09' => 'September',
        '10' => 'Oktober',
        '11' => 'November',
        '12' => 'Desember'
    ];
    return $bulanMap[$bulanangka] ?? 'Bulan Tidak Diketahui';
}

function getBulan($bulanangka) {
    $bulanMap = [
        'JAN' => 1,
        'FEB' => 2,
        'MAR' => 3,
        'APR' => 4,
        'MAY' => 5,
        'JUN' => 6,
        'JUL' => 7,
        'AGT' => 8,
        'SEP' => 9,
        'OCT' => 10,
        'NOV' => 11,
        'DEC' => 12
    ];
    return $bulanMap[$bulanangka] ?? '00';
}

function cek_credential($t) {
    $secret = $GLOBALS['secret'] ?? '';
    $get_Lvl = '-';
    if (empty($secret)) {
        http_response_code(500);
        echo "<h2>(99) Server misconfiguration: TOKEN_SECRET not found.</h2>";
        error_log("[token] TOKEN_SECRET kosong saat decode request dari " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
        exit;
    }

    $token = $t;
    try {
        $decoded = text_token_decode($token, $secret);
        $get_Lvl = $decoded;
    } catch (Exception $e) {
        http_response_code(403);
        echo "<h2>Invalid token: " . htmlspecialchars($e->getMessage()) . "</h2>";
        error_log("[token] decode failed: " . $e->getMessage() . " from " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
        exit;
    }
    return $get_Lvl;
}

function formatRupiah($angka, $koma) {
    return 'Rp ' . number_format($angka, $koma, ',', '.');
}
?>

