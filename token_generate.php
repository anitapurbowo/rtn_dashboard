<?php
    include './token_tool.php';
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Text Token Encode / Decode (Non-expiring)</title>
<style>
body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial;max-width:980px;margin:18px auto;padding:12px;color:#111;background:#f7f8fb}
h1{font-size:20px;margin:0 0 12px}
.grid{display:flex;gap:12px;align-items:flex-start}
.box{flex:1;background:#fff;border:1px solid #e6e9ee;padding:14px;border-radius:10px}
textarea, input[type=text]{width:100%;padding:8px;border:1px solid #ccc;border-radius:6px;font-family:monospace}
label{display:block;margin:8px 0 6px;font-weight:600}
.btn{display:inline-block;padding:8px 12px;border-radius:8px;border:0;background:#0b74de;color:#fff;cursor:pointer}
.err{background:#fff6f6;border:1px solid #f5c6cb;padding:10px;border-radius:8px;color:#8a1f1f}
.note{font-size:13px;color:#444;margin-top:8px}
pre{background:#f6f8fa;padding:10px;border-radius:8px;overflow:auto}
.small{font-size:13px;color:#666}
</style>
</head>
<body>
<h1>Text Token Encode / Decode (non-expiring)</h1>

<?php if (!$openssl_ok): ?>
    <div class="err">OpenSSL extension tidak tersedia di PHP — tool tidak bisa berjalan.</div>
<?php endif; ?>

<?php if (!empty($errors)): ?>
    <div class="err"><strong>Errors:</strong><ul><?php foreach($errors as $e) echo '<li>'.htmlspecialchars($e).'</li>'; ?></ul></div>
<?php endif; ?>

<div class="grid">
    <div class="box">
        <h3>Encode (plain text → token)</h3>
        <form method="post" autocomplete="off">
            <input type="hidden" name="action" value="encode">
            <label>Secret key</label>
            <input type="text" name="secret_encode" placeholder="Masukkan secret key..." value="<?php echo isset($_POST['secret_encode'])?htmlspecialchars($_POST['secret_encode']):'';?>">

            <label>Plain text</label>
            <textarea name="text" rows="6" placeholder="Tulis teks di sini..."><?php echo isset($_POST['text'])?htmlspecialchars($_POST['text']):'';?></textarea>

            <div style="margin-top:10px">
                <button class="btn" type="submit">Encode → Generate token</button>
            </div>
        </form>

        <?php if ($encode_token): ?>
            <div style="margin-top:10px">
                <label>Token (URL-safe)</label>
                <textarea readonly onclick="this.select()"><?php echo htmlspecialchars($encode_token);?></textarea>
                <div class="note small">Token ini <strong>tidak akan kedaluwarsa</strong> — simpan/rotasi secret jika ingin membatalkan token.</div>
            </div>
        <?php endif; ?>
    </div>

    <div class="box">
        <h3>Decode (token → plain text)</h3>
        <form method="post" autocomplete="off">
            <input type="hidden" name="action" value="decode">
            <label>Secret key</label>
            <input type="text" name="secret_decode" value="<?php echo isset($_POST['secret_decode'])?htmlspecialchars($_POST['secret_decode']):'';?>" placeholder="Masukkan secret key yang sama">

            <label>Token</label>
            <textarea name="token" rows="6"><?php echo isset($_POST['token'])?htmlspecialchars($_POST['token']):'';?></textarea>

            <div style="margin-top:10px">
                <button class="btn" type="submit">Decode token</button>
            </div>
        </form>

        <?php if ($decode_text !== ''): ?>
            <div style="margin-top:10px">
                <label>Plain text</label>
                <pre><?php echo htmlspecialchars($decode_text);?></pre>
            </div>
        <?php endif; ?>
    </div>
</div>

<hr style="margin-top:14px">
<div class="box" style="margin-top:12px">
    <strong>Petunjuk & keamanan</strong>
    <ul class="small">
        <li>Token yang dihasilkan <strong>tidak memiliki expiry</strong>. Untuk "membatalkan" token, Anda harus mengganti/rotasi secret key.</li>
        <li>Jangan gunakan secret produksi pada server publik. Simpan secret di environment / config aman.</li>
        <li>Gunakan HTTPS saat mengakses halaman ini.</li>
    </ul>
</div>
</body>
</html>