<?php

ini_set('display_errors', 0);
error_reporting(E_ALL);

// ---------------- Helpers ----------------
function base64url_encode(string $data): string {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}
function base64url_decode(string $data) {
    $pad = 4 - (strlen($data) % 4);
    if ($pad < 4) $data .= str_repeat('=', $pad);
    return base64_decode(strtr($data, '-_', '+/'));
}

/**
 * Encode plain text into token (NON-EXPIRING).
 * - $text: plain text string
 * - $key: secret key
 */
function text_token_encode(string $text, string $key): string {
    // Build payload wrapper; no _exp so token won't expire
    $payload = [
        'data' => $text
    ];
    $json = json_encode($payload, JSON_UNESCAPED_UNICODE);
    if ($json === false) throw new Exception('JSON encode failed');

    // derive keys from master key (sha512)
    $master = hash('sha512', $key, true);
    $encKey = substr($master, 0, 32);
    $macKey = substr($master, 32, 32);

    $ivlen = openssl_cipher_iv_length('aes-256-cbc');
    $iv = openssl_random_pseudo_bytes($ivlen);
    $cipher = openssl_encrypt($json, 'aes-256-cbc', $encKey, OPENSSL_RAW_DATA, $iv);
    if ($cipher === false) throw new Exception('Encrypt failed');

    $data = $iv . $cipher;
    $hmac = hash_hmac('sha256', $data, $macKey, true);
    return base64url_encode($iv . $hmac . $cipher);
}

/**
 * Decode token -> returns plain text (original input).
 * Throws Exception on any error.
 * (No expiry check — tokens are non-expiring)
 */
function text_token_decode(string $token, string $key): string {
    $raw = base64url_decode($token);
    if ($raw === false) throw new Exception('Invalid base64url token');

    $master = hash('sha512', $key, true);
    $encKey = substr($master, 0, 32);
    $macKey = substr($master, 32, 32);

    $ivlen = openssl_cipher_iv_length('aes-256-cbc');
    $hmaclen = 32;

    if (strlen($raw) < ($ivlen + $hmaclen + 1)) throw new Exception('Token too short / malformed');

    $iv = substr($raw, 0, $ivlen);
    $hmac = substr($raw, $ivlen, $hmaclen);
    $cipher = substr($raw, $ivlen + $hmaclen);

    $calc = hash_hmac('sha256', $iv . $cipher, $macKey, true);
    if (!hash_equals($calc, $hmac)) throw new Exception('HMAC verification failed (tampered or wrong key)');

    $json = openssl_decrypt($cipher, 'aes-256-cbc', $encKey, OPENSSL_RAW_DATA, $iv);
    if ($json === false) throw new Exception('Decrypt failed (wrong key / corrupt)');

    $payload = json_decode($json, true);
    if (!is_array($payload) || !isset($payload['data'])) throw new Exception('Invalid payload');

    // NOTE: no expiry check here (tokens are permanent until you decide to rotate/expire them manually)

    return (string)$payload['data'];
}

// ---------------- UI handling ----------------
$errors = [];
$encode_token = '';
$decode_text = '';
$openssl_ok = function_exists('openssl_encrypt') && function_exists('openssl_decrypt');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'encode') {
        $secret = trim($_POST['secret_encode'] ?? '');
        $text = $_POST['text'] ?? '';

        if (!$openssl_ok) $errors[] = 'OpenSSL extension is not available in PHP.';
        if ($secret === '') $errors[] = 'Secret key is required.';

        if (empty($errors)) {
            try {
                $encode_token = text_token_encode((string)$text, $secret);
            } catch (Exception $e) {
                $errors[] = 'Encode error: ' . $e->getMessage();
            }
        }
    } elseif ($action === 'decode') {
        $secret = trim($_POST['secret_decode'] ?? '');
        $token = trim($_POST['token'] ?? '');

        if (!$openssl_ok) $errors[] = 'OpenSSL extension is not available in PHP.';
        if ($secret === '') $errors[] = 'Secret key is required.';
        if ($token === '') $errors[] = 'Token is required.';

        if (empty($errors)) {
            try {
                $decode_text = text_token_decode($token, $secret);
            } catch (Exception $e) {
                $errors[] = 'Decode error: ' . $e->getMessage();
            }
        }
    }
}
?>

