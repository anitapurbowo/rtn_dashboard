<?php
    $getBln = isset($_GET['bln']) ? $_GET['bln'] :  date('n');
    $getDir = isset($_GET['dir']) ? $_GET['dir'] :  0;
    $getThn = isset($_GET['thn']) ? $_GET['thn'] :  date('Y');
    $getDev = isset($_GET['dev']) ? $_GET['dev'] :  0;
    $getCab = isset($_GET['cab']) ? $_GET['cab'] :  0;

    $get_Bln = getBulan($getBln) ?? null;
    $awal = date("Y-m-01", strtotime("$getThn-$get_Bln-01"));
    $akhir = date("Y-m-t", strtotime("$getThn-$get_Bln-01"));

    $pembagi = $pembagi_global;

    if (!$get_Bln) {
        die("BLN tidak ada");
    }

    // Ambil data monthly (server-side)
    $curl = curl_init();
    $postData = [
        'target' => 'GetDataRealisasiAmountDiluarProyek',
        'key' => 'RVTnpsUhPZbQ4EQLL9li',
        'iddbase' => 'DB00000033',
        'idapi' => 'API0000747',
        'TanggalMulaiAPI0000747' => $awal,
        'TanggalSampaiAPI0000747' => $akhir
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
    $responseData = json_decode($response, true);
    $data = $responseData['result'] ?? [];

    // Validasi
    if (!is_array($data) || empty($data)) {
        echo "<h3>Data kosong atau tidak sesuai.</h3>";
        echo "<pre>Response: " . htmlspecialchars($response) . "</pre>";
        exit;
    }

    // Filter by month (safely)
    $data_tampil = array_filter($data, function ($item) use ($get_Bln) {
        return isset($item['bulan']) && ((int)$item['bulan'] === (int)$get_Bln);
    });

    if ($getDir != 1) {
        $exclude = ['PRYHO', 'EXP', 'OTH'];
        $data_tampil = array_filter($data_tampil, function ($item) use ($exclude) {
            return !isset($item['hasil']) || !in_array($item['hasil'], $exclude);
        });
    }

    // Group per cabang (branch totals month)
    $grouped = [];
    foreach ($data_tampil as $row) {
        $cabang = trim($row['descript'] ?? '-');
        if ($cabang === '') $cabang = '-';
        if (!isset($grouped[$cabang])) {
            $grouped[$cabang] = ['target' => 0.0, 'real' => 0.0];
        }
        $grouped[$cabang]['target'] += (float)($row['amounttarget'] ?? 0);
        $grouped[$cabang]['real'] += (float)($row['amountreal'] ?? 0);
    }

    // Dept totals (try to compute AGE / AAI / NLU from items if present)
    $deptTotals = ['AAI' => 0.0, 'AGE' => 0.0, 'NLU' => 0.0];
    foreach ($data_tampil as $row) {
        // asumsi: ada field 'divisi' or 'hasil' — kita coba beberapa kemungkinan
        $div = strtoupper(trim($row['divisi'] ?? ($row['hasil'] ?? '')));
        $amt = (float)($row['amountreal'] ?? 0);
        if ($div === 'AAI') $deptTotals['AAI'] += $amt;
        elseif ($div === 'AGE') $deptTotals['AGE'] += $amt;
        elseif ($div === 'NLU' || strtoupper(trim($row['fokus'] ?? '')) === 'NLU') $deptTotals['NLU'] += $amt;
    }

    // Totals month
    $totalMonthReal = array_reduce(array_values($grouped), function($carry, $v){ return $carry + ($v['real'] ?? 0); }, 0);
    $totalMonthCount = count($grouped); // number of branches with data
?>