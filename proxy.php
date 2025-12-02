<?php
// proxy.php
// Simple HTTP proxy for embedding in iframe. Use with caution.

ini_set('display_errors', 0);
error_reporting(0);

$allowed_hosts = [
    '182.16.163.206:911',
    '172.18.34.2:8080'
];

// Ambil parameter lvl
$lvl = isset($_REQUEST['lvl']) ? $_REQUEST['lvl'] : '';
// Tentukan target berdasarkan kebutuhan (contoh sederhana)
$client_ip = $_SERVER['REMOTE_ADDR'];
if ($client_ip == '182.16.163.206') {
    $target_host = '172.18.34.2:8080';
} else {
    $target_host = '182.16.163.206:911';
}

if (!in_array($target_host, $allowed_hosts)) {
    http_response_code(403);
    echo "Host not allowed.";
    exit;
}

$target_base = "http://{$target_host}/rutan_space/div/salesorder/div_detailso/detailso.php";
$query = http_build_query(['lvl' => $lvl]);

$target_url = $target_base . '?' . $query;

// Inisialisasi cURL
$ch = curl_init($target_url);

// Forward method and body (GET/POST)
$method = $_SERVER['REQUEST_METHOD'];
if ($method === 'POST') {
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents('php://input'));
}

// Forward some client headers as appropriate
$forward_headers = [];
$to_forward = ['User-Agent', 'Accept', 'Accept-Language', 'Cookie'];
foreach ($to_forward as $h) {
    $hk = 'HTTP_' . strtoupper(str_replace('-', '_', $h));
    if (!empty($_SERVER[$hk])) {
        $forward_headers[] = $h . ': ' . $_SERVER[$hk];
    }
}
if ($forward_headers) curl_setopt($ch, CURLOPT_HTTPHEADER, $forward_headers);

// Options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // ubah ke true di prod bila perlu
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

// Ambil response headers
curl_setopt($ch, CURLOPT_HEADER, true);
$response = curl_exec($ch);
if ($response === false) {
    http_response_code(502);
    echo "Proxy error: " . curl_error($ch);
    curl_close($ch);
    exit;
}
$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$resp_headers_raw = substr($response, 0, $header_size);
$resp_body = substr($response, $header_size);
$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Parse response headers dan kirim ulang ke client (tapi bersihkan header yang berbahaya)
$lines = preg_split("/\r\n|\n|\r/", $resp_headers_raw);
foreach ($lines as $line) {
    if (stripos($line, 'HTTP/') === 0) continue; // skip status-line
    if (trim($line) === '') continue;
    // Safety: skip hop-by-hop headers
    if (preg_match('/^(Transfer-Encoding|Content-Length|Connection|X-Frame-Options|Content-Security-Policy|X-Content-Security-Policy)/i', $line)) {
        continue;
    }
    header($line, false);
}

// Pastikan content-length sesuai body yang kita kirim
header('Content-Length: ' . strlen($resp_body));

// OPTIONAL: override X-Frame-Options to allow embedding by our site
// header("X-Frame-Options: ALLOWALL"); // jangan set di prod kecuali Anda mengerti risikonya

// Tulis body
http_response_code($status);
echo $resp_body;
