<?php
session_start();

@require_once('./../../global_func.php');

$get_Lvl = null;

if (!empty($_GET['t'])) {
    $get_Lvl = cek_credential($_GET['t']);
} else {
     echo "<h2>Server misconfiguration: TOKEN not found.</h2>";
}

function formatRupiah($angka, $koma) {
    return 'Rp ' . number_format($angka, $koma, ',', '.');
}

if ($get_Lvl == 'DIR' || $get_Lvl == 'AAI' || $get_Lvl == 'AGE' || $get_Lvl == 'ACCD' || substr($get_Lvl, 0, 3) == 'dtC') {
    $pembagi = $pembagi_global; // Pembagi untuk format angka

    if (!$get_Lvl) {
        die("ID tidak ditemukan");
    }
    $curl = curl_init();

    $postData = [
        'target' => 'GetDetailSoOpen',
        'key' => 'KubXtfo1jFa25hb1TKJ0',
        'iddbase' => 'DB00000001',
        'idapi' => 'API0000749',
        'use_logic' => '1',
    ];
        
    if ($get_Lvl == 'DIR' || $get_Lvl == 'ACCD') {  
    } else if (substr($get_Lvl, 0, 3) == 'dtC') { 
        $postData['CabangAPI0000749'] = substr($_GET['lvl'], 3, 2); 
    } else { 
        $postData['DivisiAPI0000749'] = $get_Lvl; 
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
    $data_awal = $responseData['result'] ?? [];
    // echo var_dump($responseData);

    if (!empty($_GET['lvl']) && strlen($_GET['lvl']) >= 5) {
        $lvlParam = substr($_GET['lvl'], 3, 2); 
        $branch = getCabang($lvlParam);

        $selectedBranchCode = $branch['code'];
        $selectedBranchName = $branch['name'];
    }

    if ($get_Lvl == 'DIR') {
        $data = $data_awal;
    } else {
        if ($get_Lvl != 'ACCD') {
            $data = array_filter($data_awal, function ($item) {
                return isset($item['Fokus']) && $item['Fokus'] === 'LU';
            });
        } else {
            $data = array_filter($data_awal, function ($item) {
                return isset($item['Jenis']) && $item['Jenis'] === 'KOM';
            });
        }
    }
    $data = array_values($data);

    if (!is_array($data) || empty($data)) {
        echo "<h3>Data kosong atau tidak sesuai. $get_Lvl </h3>";
        exit;
    }

    $distinct_data = array_values(array_reduce($data, function ($carry, $item) {
        if (isset($item['DocNum']) && !isset($carry[$item['DocNum']])) {
            $carry[$item['DocNum']] = $item;
        }
        return $carry;
    }, []));

    $totalKom = 0;
    $totalPro = 0;
    $totalExport = 0;
    $countKom = 0;
    $countItemKom = 0;
    $countProyek = 0;
    $countItemProyek = 0;
    $countExport = 0;
    $countItemExport = 0;

    foreach ($data as $doc) {
        $jenis = isset($doc['Jenis']) ? strtoupper(trim($doc['Jenis'])) : '';
        $amt = isset($doc['Total']) && is_numeric($doc['Total']) ? (float)$doc['Total'] : 0;
        if ($jenis === 'KOM') {
            $totalKom += $amt;
            $countItemKom++;
        } else if ($jenis === 'PRO') {
            $totalPro += $amt;
            $countItemProyek++;
        } else if ($jenis === 'EXP') {
            $totalExport += $amt;
            $countItemExport++;
        }
    }

    foreach ($distinct_data as $item) {
         $jenis = isset($item['Jenis']) ? strtoupper(trim($item['Jenis'])) : '';
        if ($jenis === 'KOM') {
            $countKom++;
        } else if ($jenis === 'PRO') {
            $countProyek++;
        } else if ($jenis === 'EXP') {
            $countExport++;
        }
    }

    $dataKom = array_filter($data, function ($item) {
        return isset($item['Jenis']) && $item['Jenis'] === 'KOM';
    });

    $dataProyek = array_filter($data, function ($item) {
        return isset($item['Jenis']) && $item['Jenis'] === 'PRO';
    });

    $dataExport = array_filter($data, function ($item) {
        return isset($item['Jenis']) && $item['Jenis'] === 'EXP';
    });

    $totalSO = count($distinct_data);

    /*
     * NEW: Branch chart & Top-items calculation (fixed & safe)
     */
    $branches = ['S','SR', 'SS','W','WL','Y','YB','U','US','K'];
    // init totals per branch (in IDR)
    $branchTotals = array_fill_keys($branches, 0.0);

    // Sum total per branch using data rows (summing amounts)
    foreach ($data as $so) {
        $cab = isset($so['CabCode']) ? strtoupper(trim($so['CabCode'])) : '';
        $amt = isset($so['Total']) && is_numeric($so['Total']) ? (float)$so['Total'] : 0;
        if (in_array($cab, $branches, true)) {
            $branchTotals[$cab] += $amt;
        } else {
            // ignore branches not in the static list per requirement
        }
    }

    // Dept totals (AAI / AGE) aggregated by Divisi
    $deptTotals = ['AAI_LU' => 0.0, 'AGE_LU' => 0.0, 'NLU' => 0.0];

    foreach ($data as $so) {
        $div = isset($so['Divisi']) ? strtoupper(trim($so['Divisi'])) : '';
        $fok = isset($so['Fokus']) ? strtoupper(trim($so['Fokus'])) : '';
        $amt = isset($so['Total']) && is_numeric($so['Total']) ? (float)$so['Total'] : 0;

        if ($div === 'AAI' && $fok === 'LU') {
            $deptTotals['AAI_LU'] += $amt;
        } elseif ($div === 'AGE' && $fok === 'LU') {
            $deptTotals['AGE_LU'] += $amt;
        } elseif ($fok === 'NLU') {
            $deptTotals['NLU'] += $amt;
        }
    }

    // --- NEW: Salesman totals for dtC users ---
    $salesTotals = []; // key = SlpName => sum(Total)
    foreach ($data as $so) {
        $slp = isset($so['SlpName']) && trim($so['SlpName']) !== '' ? trim($so['SlpName']) : '-';
        $amt = isset($so['Total']) && is_numeric($so['Total']) ? (float)$so['Total'] : 0;
        if (!isset($salesTotals[$slp])) $salesTotals[$slp] = 0.0;
        $salesTotals[$slp] += $amt;
    }

    // prepare JS arrays (values converted to juta) - ensure ordered according to $branches
    $branchValuesOrdered = [];
    foreach ($branches as $b) {
        $branchValuesOrdered[] = round( (isset($branchTotals[$b]) ? floatval($branchTotals[$b]) : 0.0) / $pembagi, 2 );
    }
    $js_branch_labels = json_encode($branches);
    $js_branch_values = json_encode($branchValuesOrdered);

    $js_dept_labels = json_encode(array_keys($deptTotals));
    $js_dept_values = json_encode(array_map(function($v) use ($pembagi){ return round($v / $pembagi, 2); }, array_values($deptTotals)));

    // JS for sales totals (used when get_Lvl starts with 'dtC')
    // keep a stable ordering: sort by value desc so chart is meaningful
    arsort($salesTotals);
    $salesLabels = array_keys($salesTotals);
    $salesValues = array_map(function($v) use ($pembagi){ return round($v / $pembagi, 2); }, array_values($salesTotals));
    $js_sales_labels = json_encode($salesLabels);
    $js_sales_values = json_encode($salesValues);

    // Top items by total quantity (sum across $data rows)
    $itemsAgg = []; // key = ItemCode
    foreach ($data as $row) {
        $code = isset($row['ItemCode']) ? trim($row['ItemCode']) : '-';
        if ($code === '') $code = '-';
        $qty = isset($row['OpenQty']) && is_numeric($row['OpenQty']) ? (float)$row['OpenQty'] : 0;
        $amt = isset($row['Total']) && is_numeric($row['Total']) ? (float)$row['Total'] : 0;
        $desc = isset($row['Dscription']) ? trim($row['Dscription']) : '';
        if (!isset($itemsAgg[$code])) {
            $itemsAgg[$code] = ['code'=>$code, 'desc'=>$desc, 'qty'=>0.0, 'amt'=>0.0];
        }
        $itemsAgg[$code]['qty'] += $qty;
        $itemsAgg[$code]['amt'] += $amt;
    }

    // convert to numeric indexed array for safe usort
    $itemsAggList = array_values($itemsAgg);

    // sort items by qty desc
    usort($itemsAggList, function($a, $b) {
        if ($a['qty'] == $b['qty']) return 0;
        return ($a['qty'] > $b['qty']) ? -1 : 1;
    });

    // $topN = 10;
    // $topItems = array_slice($itemsAggList, 0, $topN);
    $topItems = $itemsAggList;

    $js_item_labels = json_encode(array_map(function($i){ return $i['code']; }, $topItems));
    // item values: qty (numbers)
    $js_item_values = json_encode(array_map(function($i){ return round($i['qty'], 2); }, $topItems));

?>