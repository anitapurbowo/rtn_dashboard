<?php

@require_once('./../../global_func.php');

$get_Lvl = null;

if (!empty($_GET['t'])) {
    $get_Lvl = cek_credential($_GET['t']);
} else {
     echo "<h2>Server misconfiguration: TOKEN not found.</h2>";
}

if (in_array($get_Lvl, $allowedLevels)) {
    $pembagi = $pembagi_global; // Pembagi untuk format angka

    @include_once('getsoopen.php'); 

    
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="refresh" content="3000">
    <title>Data Open SO</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../../resicon/lso.ico" type="image/x-icon">

    <!-- libs -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- DataTables CSS & core -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>

    <!-- Responsive (after core) -->
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.dataTables.min.css">
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>

    <!-- Buttons extension + dependencies (load after core/responsive) -->
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>

    <!-- Chart.js (after DataTables scripts) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <!-- icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- custom css -->
    <link rel="stylesheet" href="../../css/template.css">
    <!-- custom js -->
     <script src="../../js/template_js.js"></script>

    <style>   
        /*responsive*/
        @media (max-width: 480px) {
            .summary-grid.compact,
            .summary-grid {
                grid-template-columns: 1fr !important;
            }
        }

        @media (max-width:720px) {
            #TopItemsTable td, #TopItemsTable th { padding:6px 8px !important; font-size:12px; }
            #TopItemsTable td:nth-child(2), #TopItemsTable th:nth-child(2) { max-width: 240px; }
            .dataTables_info { font-size: 11px; }
            .dataTables_paginate .paginate_button { font-size: 11px; padding: 5px 8px; }
            .summary-grid.compact,
            .summary-grid {
                grid-template-columns: 2fr !important;
            }
        }

        @media (max-width:900px) {
            .charts-row { grid-template-columns: 1fr; }
            .summary-card { padding: 12px; min-height: 76px; }
            .summary-value { font-size: 22px; }
            #detailModal { width: 96%; max-width: 96%; }
            #modalContent { max-height: calc(80vh - 64px); }
            .summary-top-wrapper .right-panel { width: 100%; max-width: 100%; min-width: auto; }
        }

        @media (max-width: 1100px) {
            .summary-top-wrapper { flex-direction: column; }
            .summary-top-wrapper {
                flex-direction: column !important;
                gap: 12px !important;
            }
            .summary-top-wrapper .left-panel,
            .summary-top-wrapper .right-panel {
                flex-basis: 100% !important;
                flex: 1 1 100% !important;
                max-width: 100% !important;
                width: 100% !important;
                min-width: 0 !important;
            }
            .summary-top-wrapper .left-panel { order: 1 !important; }
            .summary-top-wrapper .right-panel { order: 2 !important; }
            #TopItemsTable.display.nowrap,
            #TopItemsTable.display.nowrap tbody,
            #TopItemsTable.display.nowrap td,
            #TopItemsTable.display.nowrap th,
            #TopItemsTable {
                white-space: normal !important;
                table-layout: auto !important;
                width: 100% !important;
            }
            #TopItemsTable col { width: auto !important; }
            .dataTables_info { font-size: 11px !important; }
            .dataTables_paginate .paginate_button { font-size: 11px !important; padding: 5px 6px !important; }
            #TopItemsTable td, #TopItemsTable th { font-size: 12px !important; padding: 6px 8px !important; }
        }

        /* Chart wrapper (height reduced) */
        .charts-row { display:grid; grid-template-columns: 1fr 1fr; gap:12px; margin-bottom:14px; align-items:start; }
        .chart-card { background: var(--card); padding:12px; border-radius:12px; box-shadow: var(--shadow-sm); border:1px solid var(--line); }
        .chart-title { font-size:13px; font-weight:700; color:var(--title-header); margin-bottom:8px; }
        .chart-wrapper { width:100%; height:260px; max-height:320px; } /* reduced height as requested */

        /* compact table spacing */
        table.dataTable, table { width:100% !important; border-collapse:separate; border-spacing:0; font-size:13px; }
        table.dataTable thead th { background: var(--table-head); color:#fff; font-size:11px; text-transform:uppercase; letter-spacing:.6px; padding:8px; position: sticky; top:0; z-index:2; border-bottom: 1px solid rgba(255,255,255,0.06); }
        table.dataTable th, table.dataTable td { padding:6px 8px !important; line-height:1.25 !important; font-size:13px !important; vertical-align:middle; }
        tbody td { background: transparent; border-bottom:1px solid var(--line); color:var(--text); }
        tr:nth-child(even) tbody td { background: rgba(0,0,0,0.01); }
        tbody tr:hover td { background: rgba(133,161,143,0.04); }

        td.text-center, th.text-center { text-align:center; }

        .wrap-col { white-space: normal !important; word-break: break-word; max-width:520px; }

        .dataTables_info {
            font-size: 11px;
            color: var(--muted);
        }
        .dataTables_paginate .paginate_button {
            font-size: 12px;
            padding: 6px 10px;
        }

        .dataTables_filter input[type="search"] {
            font-size: 11px;         /* ukuran teks di input */
            padding: 5px 8px;
            height: 30px;
            line-height: 1;
            border-radius: 6px;
            border: 1px solid var(--line);
            background: transparent;
            color: var(--text);
        }

        /* Summary cards (redesigned) */
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 10px;
            align-items: stretch;
        }
        .summary-card {
            background: linear-gradient(135deg, rgba(255,255,255,0.95), rgba(245,255,240,0.85));
            border-radius: 12px;
            padding: 14px 16px;
            box-shadow: 0 12px 30px rgba(34,50,30,0.06);
            border: 1px solid rgba(133,161,143,0.12);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 96px;
            position: relative;
            overflow: hidden;
            color: #083032;
        }
        .summary-card .icon {
            position: absolute;
            right: 12px;
            top: 12px;
            font-size:40px;
            opacity:0.12;
        }

        .summary-card .illustration {
            position: absolute;
            left: -6%;
            top: -18%;
            width: 160px;
            height: 160px;
            opacity: 0.08;
            pointer-events: none;
            transform: rotate(-12deg);
            filter: drop-shadow(0 6px 12px rgba(8,48,50,0.03));
        }

        .summary-card.total {
            background: linear-gradient(135deg, #E8F3FF 0%, #aedafeff 100%);
            border: 1px solid rgba(120,160,200,0.08);
        }
        .summary-card.kom {
            background: linear-gradient(135deg, #EFFCF6 0%, #bae5baff 100%);
            border: 1px solid rgba(120,200,170,0.07);
        }
        .summary-card.pro {
            background: linear-gradient(135deg, #FFF9ED 0%, #e8d7c3ff 100%);
            border: 1px solid rgba(200,170,120,0.06);
        }
        .summary-card.exim {
            background: linear-gradient(135deg, #FFF1F6 0%, #e4b7cdff 100%);
            border: 1px solid rgba(220,150,160,0.06);
        }
        .summary-card .icon { opacity: .2; }
        .summary-card .summary-value,
        .summary-card .summary-title { font-weight: 800; text-transform: uppercase; letter-spacing: 0.6px; margin-bottom: 8px; color: var(--summary-title); }

        .summary-value {
            font-size: 30px;
        }

        .summary-meta { font-size: 12px; color: var(--muted); margin-top: 8px; display:flex; gap:6px; align-items:center; }
        .badge-small {
            font-size:11px;
            padding:4px 8px;
            border-radius:999px;
            background: linear-gradient(90deg, rgba(61,141,122,0.12), rgba(61,141,122,0.18));
            color: var(--accent-dark);
            font-weight:700;
            display:inline-block;
        }

        /* adjust for dark theme */
        [data-theme="dark"] .summary-card {
            background: linear-gradient(90deg, rgba(61,141,122,0.12), rgba(61,141,122,0.18));
            border: 1px solid #EAEAEA;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        }

        [data-theme="dark"] .summary-card .icon { opacity:0.06; color: #043708ff; }

        /* MODAL CSS - added/important */
        #modalOverlay {
            display: none;
            position: fixed;
            inset: 0; /* top:0;right:0;bottom:0;left:0 */
            background: rgba(2,6,23,0.5);
            z-index: 9998;
            transition: opacity 180ms ease;
            opacity: 0;
            backdrop-filter: blur(2px);
        }
        #modalOverlay.active {
            display: block;
            opacity: 1;
        }

        #detailModal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%,-50%) scale(.98);
            width: 92%;
            max-width: 980px;
            max-height: 86vh;
            background: var(--card);
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(2,6,23,0.45);
            z-index: 9999;
            overflow: hidden;
            opacity: 0;
            transition: transform 160ms ease, opacity 160ms ease;
        }
        #detailModal.active {
            display: block;
            transform: translate(-50%,-50%) scale(1);
            opacity: 1;
        }

        #modalContent {
            padding: 0 0 18px 0;
            max-height: calc(86vh - 64px);
            overflow: auto;
            outline: none;
        }

        #closeModal {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 0;
            background: #eee;
            color: #222;
            font-size: 20px;
            cursor: pointer;
            z-index: 10001;
        }
        #closeModal:hover { transform: rotate(90deg); transition: transform 120ms ease; }

        .modal-header-section { padding:18px 22px; border-bottom:1px solid var(--line); background:var(--card); }
        .modal-main-header h2 { margin:0; font-size:20px; color:var(--text); }
        .modal-main-header .doc-type { font-size:13px; color:var(--muted); display:block; margin-bottom:6px; }
        .customer-name { font-weight:600; color:var(--title-header); margin-top:6px; }

        .details-grid { display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:12px; padding:16px 22px; }
        .detail-block { display:flex; gap:10px; align-items:center; font-size:14px; }
        .detail-block svg { width:22px; height:22px; fill:var(--accent); flex-shrink:0; }
        .detail-block-content span { display:block; font-size:12px; color:var(--muted); text-transform:uppercase; }

        .modal-table-section { padding:12px 22px 22px 22px; }
        .detail-table { width:100%; border-collapse:collapse; }
        .detail-table th, .detail-table td { padding:10px 12px; border-bottom:1px solid var(--line); font-size:13px; text-align:left; }
        .detail-table thead th { background:var(--accent); color:#fff; text-transform:uppercase; font-size:12px; }
        .detail-table tfoot td { font-weight:700; font-size:14px; text-align:right; }

        .summary-top-wrapper {
            display: flex;
            gap: 12px;
            align-items: stretch;
            margin-bottom: 12px;
            flex-wrap: wrap;
        }
        .summary-top-wrapper .left-panel {
            flex: 1 1 55%;
            min-width: 280px;
            display: flex;
            flex-direction: column;
        }
        .summary-top-wrapper .right-panel {
            flex: 0 1 480px; 
            max-width: 46%;  
            min-width: 300px; 
            display: flex;
            flex-direction: column;
            align-self: stretch;
            box-sizing: border-box;
            min-height: 240px;
        }

        .right-panel .section-title { margin-bottom:8px; }
        .right-panel table thead th { position: sticky; top:0; z-index:3; }

        .left-panel .table-card { display:flex; flex-direction:column; height:100%; }
        .left-panel .summary-grid { margin-bottom:12px; }
        .left-panel .chart-card { margin-top:0; flex:1 1 auto; display:flex; flex-direction:column; }
        .left-panel .chart-wrapper { flex:1 1 auto; }

        .right-panel .table-card { display:flex; flex-direction:column; height:100%; }

        .right-panel .dataTables_wrapper { display:flex; flex-direction:column; flex:1 1 auto; min-height:0; }
        
        .right-panel table.dataTable { flex:1 1 auto; width:100% !important; }
        .right-panel .dataTables_scrollBody { flex:1 1 auto; overflow:auto; }

        .col-sort-hidden { display:none; visibility:hidden; width:0; padding:0; margin:0; border:0; }

        /* TopItems: allow wrapping and remove forced fixed layout */
        #TopItemsTable {
            table-layout: auto;
            width: 100% !important;
            white-space: normal;
            overflow: visible;
        }

        /* Kolom deskripsi (kolom ke-2) harus bisa membungkus */
        #TopItemsTable td:nth-child(2),
        #TopItemsTable th:nth-child(2) {
            white-space: normal !important;
            overflow-wrap: anywhere;
            word-break: break-word;
            max-width: 420px;
        }

        /* Pastikan angka tetap rata kanan */
        #TopItemsTable td.text-right, #TopItemsTable th.text-right {
            white-space: nowrap;
            vertical-align: middle !important;
        }

        #TopItemsTable tbody td,
        #TopItemsTable thead th {
            vertical-align: middle !important;
        }

        #TopItemsTable col, 
        #TopItemsTable col:nth-child(2) { width: auto !important; }

        #TopItemsTable tbody tr { cursor: pointer; }
        #TopItemsTable tbody tr:hover { filter: brightness(0.98); transform: translateY(-1px); transition: .12s; }

        /* style kecil untuk tabel modal item -> SO list */
        .item-so-table { width:100%; border-collapse: collapse; margin-top:8px; }
        .item-so-table th, .item-so-table td { padding:8px 10px; border-bottom: 1px solid var(--line); font-size:13px; text-align:left; }
        .item-so-table thead th { background: var(--table-head); color:#fff; text-transform:uppercase; font-size:12px; }
        .item-so-table td.text-right { text-align: right; white-space: nowrap; }
        
        table.dataTable.dtr-inline.collapsed > tbody > tr[role="row"] > td:first-child:before {
            top: 50%;
            transform: translateY(-50%);
        }

        .summary-top-wrapper .right-panel .dataTables_scrollBody {
            flex:1 1 auto;
            overflow:auto;
            padding-bottom: 12px; /* ruang untuk pagination */
        }

        .right-panel .table-card { overflow: auto; }

        /* Controls for DIR chart */
        .chart-controls { display:flex; gap:8px; align-items:center; margin-bottom:8px; flex-wrap:wrap; }
        .chart-controls .control { display:flex; gap:6px; align-items:center; font-size:13px; color:var(--muted); }
        .chart-controls select { padding:6px 8px; border-radius:6px; border:1px solid var(--line); background:transparent; color:var(--text); }
        .chart-controls input[type="radio"] { transform:translateY(1px); }

        #SOOpenKom tbody tr {
            cursor: pointer;
        }

        #SOOpenPro tbody tr {
            cursor: pointer;
        }

        #SOOpenExim tbody tr {
            cursor: pointer;
        }
    </style>
</head>
<body>

<div class="wrap">
     <div class="topbar" role="banner">
        <div class="brand" aria-hidden="true">
            <img src="../../resicon/savings-svgrepo-com.svg" alt="Sales Order Icon" />
            <div>
                <h1>Daftar SO Open <?php  if ($get_Lvl=='dtC') echo $selectedBranchCode; else if ($get_Lvl!='ACCD' && $get_Lvl!='DIR') echo $get_Lvl; ?> (dalam Juta)</h1>
                <small>Open SO adalah SO yang masih terbuka belum dilanjutkan proses nya menjadi Invoice <?php if ($get_Lvl=='ACCD' || $get_Lvl=='DIR') echo "[".$get_Lvl."]"; ?> </small>
            </div>
        </div>

        <div class="top-actions" role="toolbar" aria-label="toolbar actions">
            <button id="refreshBtn" class="btn">🔄 Refresh</button>
            <button id="themeToggle" class="btn small" aria-pressed="false">🌙 Dark</button>
        </div>

        <div class="toolbar-warning" id="toolbarWarning">
            <i class="bi bi-info-circle" style="margin-right:8px;"></i>
             ℹ️ Klik salah satu baris pada tabel untuk melihat detail Sales Order.
        </div>
    </div>

    <!-- Modal (overlay + dialog) -->
    <div id="modalOverlay" aria-hidden="true"></div>
    <div id="detailModal" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
        <button id="closeModal" aria-label="Tutup modal">&times;</button>
        <div id="modalContent" tabindex="0">
            <!-- Dynamic content akan dimasukkan JS -->
        </div>
    </div>

    <?php if (isset($data[0]) && is_array($data[0])): ?>
        <!-- SUMMARY CARDS -->
        <div class="summary-top-wrapper" role="region" aria-label="Summary and Top Items">
            <div class="left-panel">
                <div class="table-card summary-cards">
                    <div class="summary-grid" role="region" aria-label="Summary Sales Orders">
                        <!-- Total SO -->
                        <div class="summary-card total" aria-hidden="false" role="article" aria-label="Total SO">
                            <div class="content">
                                <i class="fa-solid fa-layer-group icon" aria-hidden="true"></i>
                                <div class="summary-title">Total SO</div>
                                <div class="summary-value"><?= $countKom+$countProyek+$countExport ?></div>
                                <div class="summary-meta">
                                    <span class="badge-small"><?= $countItemKom+$countItemProyek+$countItemExport ?></span>
                                    <span><?= formatRupiah(($totalKom + $totalPro + $totalExport)/$pembagi, 2) ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- Komersil -->
                        <div class="summary-card kom" role="article" aria-label="SO Komersil">
                            <div class="content">
                                <i class="fa-solid fa-handshake icon" aria-hidden="true"></i>
                                <div class="summary-title">SO Komersil</div>
                                <div class="summary-value"><?= $countKom ?></div>
                                <div class="summary-meta">
                                    <span class="badge-small"><?= $countItemKom ?></span>
                                    <span><?= formatRupiah(($totalKom)/$pembagi, 2) ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- Proyek -->
                        <div class="summary-card pro" role="article" aria-label="SO Project">
                            <div class="content">
                                <i class="fa-solid fa-diagram-project icon" aria-hidden="true"></i>
                                <div class="summary-title">SO Project</div>
                                <div class="summary-value"><?= $countProyek ?></div>
                                <div class="summary-meta">
                                    <span class="badge-small"><?= $countItemProyek ?></span>
                                    <span><?= formatRupiah(($totalPro)/$pembagi, 2) ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- Exim -->
                        <div class="summary-card exim" role="article" aria-label="SO Exim">
                            <div class="content">
                                <i class="fa-solid fa-ship icon" aria-hidden="true"></i>
                                <div class="summary-title">SO Exim</div>
                                <div class="summary-value"><?= $countExport ?></div>
                                <div class="summary-meta">
                                    <span class="badge-small"><?= $countItemExport ?></span>
                                    <span><?= formatRupiah(($totalExport)/$pembagi, 2) ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="chart-card" aria-hidden="false" style="margin-top:6px;">
                        <div class="chart-title">Total Open SO per Cabang (dalam Juta)</div>

                        <?php if (($get_Lvl === 'DIR') || ($get_Lvl === 'ACCD') ): ?>
                            <!-- Controls visible only to DIR -->
                            <div class="chart-controls" role="toolbar" aria-label="chart controls">
                                <label class="control"><input type="radio" name="chartMode" value="dept" checked> By Dept</label>
                                <label class="control"><input type="radio" name="chartMode" value="branch"> By Branch</label>

                                <div id="branchSelectorWrap" style="display:none;">
                                    <label class="control">Cabang:
                                        <select id="branchSelector">
                                            <option value="__ALL__">All Branches</option>
                                            <?php foreach ($branches as $b): ?>
                                                <option value="<?= htmlspecialchars($b) ?>"><?= htmlspecialchars($b) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </label>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="chart-wrapper">
                            <canvas id="branchChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="right-panel table-card" aria-label="Top Items">
                <div class="section-title">
                    Qty Barang Terbanyak dari seluruh SO
                    <small style="display:block; font-size:11px; color:var(--bg-toolbar-warning); margin-top:6px;">
                        (klik item untuk melihat detail)
                    </small>
                </div>
                <table id="TopItemsTable" class="display nowrap table-hover" cellspacing="0" width="100%">
                    <colgroup>
                        <col style="width:25%">   <!-- Item Code -->
                        <col>                     <!-- Deskripsi -->
                        <col style="width:8%">    <!-- Total Qty -->
                        <col style="width:8%">    <!-- Total Nilai (Juta) -->
                    </colgroup>
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Deskripsi</th>
                            <th class="text-right">Qty</th>
                            <th class="text-right">Amnt</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $rnk = 1; foreach ($topItems as $it): ?>
                            <tr>
                                <td style="text-align:left; font-size:11px !important;"><?= htmlspecialchars($it['code']) ?></td>
                                <td style="text-align:left; font-size:11px !important;" class="tdItemTop"><?= htmlspecialchars($it['desc']) ?></td>
                                <td class="text-right"
                                    style="font-size:11px !important;"
                                    data-export="<?= is_numeric($it['qty']) ? (float)$it['qty'] : 0 ?>"
                                    data-order="<?= is_numeric($it['qty']) ? (float)$it['qty'] : 0 ?>">
                                    <?= number_format(is_numeric($it['qty']) ? (float)$it['qty'] : 0) ?>
                                </td>
                                <td class="text-right"
                                    style="font-size:11px !important;"
                                    data-export="<?= is_numeric($it['amt']) ? (float)$it['amt'] / $pembagi : 0 ?>"
                                    data-order="<?= is_numeric($it['amt']) ? (float)$it['amt'] / $pembagi : 0 ?>"
                                    data-total="<?= is_numeric($it['amt']) ? ((float)$it['amt'] / $pembagi) : 0 ?>">
                                    <?= number_format(is_numeric($it['amt']) ? (float)$it['amt'] / $pembagi : 0) ?>
                                </td>
                            </tr>
                        <?php $rnk++; endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ORIGINAL TABLES (Kom/Pro/Exim)  -->
        <div class="table-card">
            <div class="section-title">
                DATA SO KOMERSIL (Juta)
                <small style="display:block; font-size:11px; color:var(--bg-toolbar-warning); margin-top:6px;">
                    (klik item untuk melihat detail)
                </small>
            </div>
            <table id="SOOpenKom" class="display nowrap table-hover" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th class="text-center">No SO</th>
                        <th class="text-center">Cabang</th>
                        <th class="text-center">Divisi</th>
                        <th class="text-center">Fokus</th>
                        <th>Nama Customer</th>
                        <th>Kode Produk</th>
                        <th>Open Qty</th>
                        <th class="text-right">Total</th>
                        <th class="text-center">Hari</th>
                        <th>Nama Salesman</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th colspan="6"></th>
                        <th style="text-align:right"> </th>
                        <th style="text-align:right">Total Nilai:</th>
                        <th></th><th></th><th></th>
                    </tr>
                </tfoot>
                <tbody>
                    <?php foreach ($dataKom as $row):
                        $tgl = new DateTime(date('d-m-Y',strtotime(substr(isset($row['DocDate']) ? $row['DocDate'] : '-',0,10))));
                        $tgl_now = new DateTime();
                        $selisih = $tgl->diff($tgl_now)->format("%a") ;
                    ?>
                    <tr data-docnum="<?= htmlspecialchars($row['DocNum'] ?? '-') ?>" class="grouped-row">
                        <td><?= $tgl->format('d-m-Y') ?></td>
                        <td class="text-center"><?= htmlspecialchars($row['DocNum'] ?? '-') ?></td>
                        <td class="text-center"><?= htmlspecialchars($row['CabCode'] ?? '-') ?></td>
                        <td class="text-center"><?= htmlspecialchars($row['Divisi'] ?? '-') ?></td>
                        <td class="text-center"><?= htmlspecialchars($row['Fokus'] ?? '-') ?></td>
                        <td class="wrap-col"><?= htmlspecialchars($row['CardName'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row['ItemCode'] ?? '-') ?></td>
                        <td class="text-right""><?= number_format($row['OpenQty'], 2, ',', '.') ?></td>
                        <td class="text-right" data-total="<?= is_numeric($row['Total']) ? $row['Total'] / $pembagi : 0 ?>"><?= number_format((is_numeric($row['Total']) ? (float)$row['Total'] : 0) / $pembagi, 2, ',', '.') ?></td>
                        <td class="text-center"><?= $selisih ?></td>
                        <td><?= htmlspecialchars($row['SlpName'] ?? '-') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if ($get_Lvl != 'accd'): ?>
        <div class="table-card">
            <div class="section-title">
                DATA SO PROYEK (Juta)
                <small style="display:block; font-size:11px; color:var(--bg-toolbar-warning); margin-top:6px;">
                    (klik item untuk melihat detail)
                </small>
            </div>
            <table id="SOOpenPro" class="display nowrap table-hover" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th class="text-center">No SO</th>
                        <th class="text-center">Cabang</th>
                        <th class="text-center">Divisi</th>
                        <th class="text-center">Fokus</th>
                        <th>Nama Customer</th>
                        <th>Kode Produk</th>
                        <th>Open Qty</th>
                        <th class="text-right">Total</th>
                        <th class="text-center">Hari</th>
                        <th>Nama Salesman</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th colspan="6"></th>
                        <th style="text-align:right"> </th>
                        <th style="text-align:right">Total Nilai:</th>
                        <th></th><th></th><th></th>
                    </tr>
                </tfoot>
                <tbody>
                    <?php foreach ($dataProyek as $row):
                        $tgl = new DateTime(date('d-m-Y',strtotime(substr(isset($row['DocDate']) ? $row['DocDate'] : '-',0,10))));
                        $tgl_now = new DateTime();
                        $selisih = $tgl->diff($tgl_now)->format("%a") ;
                    ?>
                    <tr data-docnum="<?= htmlspecialchars($row['DocNum'] ?? '-') ?>" class="grouped-row">
                        <td><?= $tgl->format('d-m-Y') ?></td>
                        <td class="text-center"><?= htmlspecialchars($row['DocNum'] ?? '-') ?></td>
                        <td class="text-center"><?= htmlspecialchars($row['CabCode'] ?? '') ?></td>
                        <td class="text-center"><?= htmlspecialchars($row['Divisi'] ?? '-') ?></td>
                        <td class="text-center"><?= htmlspecialchars($row['Fokus'] ?? '-') ?></td>
                        <td class="wrap-col"><?= htmlspecialchars($row['CardName'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row['ItemCode'] ?? '-') ?></td>
                        <td class="text-right""><?= number_format($row['OpenQty'], 2, ',', '.') ?></td>
                        <td class="text-right" data-total="<?= is_numeric($row['Total']) ? $row['Total'] / $pembagi : 0 ?>"><?= number_format((is_numeric($row['Total']) ? (float)$row['Total'] : 0) / $pembagi, 2, ',', '.') ?></td>
                        <td class="text-center"><?= $selisih ?></td>
                        <td><?= htmlspecialchars($row['SlpName'] ?? '-') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="table-card">
            <div class="section-title">
                DATA SO EXIM (Juta)
                <small style="display:block; font-size:11px; color:var(--bg-toolbar-warning); margin-top:6px;">
                    (klik item untuk melihat detail)
                </small>
            </div>
            <table id="SOOpenExim" class="display nowrap table-hover" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th class="text-center">No SO</th>
                        <th class="text-center">Cabang</th>
                        <th class="text-center">Divisi</th>
                        <th class="text-center">Fokus</th>
                        <th>Nama Customer</th>
                        <th>Kode Produk</th>
                        <th>Open Qty</th>
                        <th class="text-right">Total</th>
                        <th class="text-center">Hari</th>
                        <th>Nama Salesman</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th colspan="6"></th>
                        <th style="text-align:right"> </th>
                        <th style="text-align:right">Total Nilai:</th>
                        <th></th><th></th><th></th>
                    </tr>
                </tfoot>
                <tbody>
                    <?php foreach ($dataExport as $row):
                        $tgl = new DateTime(date('d-m-Y',strtotime(substr(isset($row['DocDate']) ? $row['DocDate'] : '-',0,10))));
                        $tgl_now = new DateTime();
                        $selisih = $tgl->diff($tgl_now)->format("%a") ;
                    ?>
                    <tr data-docnum="<?= htmlspecialchars($row['DocNum'] ?? '-') ?>" class="grouped-row">
                        <td><?= $tgl->format('d-m-Y') ?></td>
                        <td class="text-center"><?= htmlspecialchars($row['DocNum'] ?? '-') ?></td>
                        <td class="text-center"><?= htmlspecialchars($row['CabCode'] ?? '') ?></td>
                        <td class="text-center"><?= htmlspecialchars($row['Divisi'] ?? '-') ?></td>
                        <td class="text-center"><?= htmlspecialchars($row['Fokus'] ?? '-') ?></td>
                        <td class="wrap-col"><?= htmlspecialchars($row['CardName'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row['ItemCode'] ?? '-') ?></td>
                        <td class="text-right""><?= number_format($row['OpenQty'], 2, ',', '.') ?></td>
                        <td class="text-right" data-total="<?= is_numeric($row['Total']) ? $row['Total'] / $pembagi : 0 ?>"><?= number_format((is_numeric($row['Total']) ? (float)$row['Total'] : 0) / $pembagi, 2, ',', '.') ?></td>
                        <td class="text-center"><?= $selisih ?></td>
                        <td><?= htmlspecialchars($row['SlpName'] ?? '-') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    <?php else: ?>
        <p>Data kosong atau format tidak sesuai.</p>
    <?php endif; ?>
</div>

<script>
    
    var js_so = <?= json_encode($data) ?>;
    
    var branchLabels = <?= $js_branch_labels ?>;
    var branchValues = <?= $js_branch_values ?>;
    var deptLabels = <?= $js_dept_labels ?>;
    var deptValues = <?= $js_dept_values ?>;

    var salesLabels = <?= $js_sales_labels ?>;
    var salesValues = <?= $js_sales_values ?>;

    $(document).ready(function() {
        js_so = <?= json_encode($data) ?>;
        js_lvl = <?= json_encode($get_Lvl) ?>;
        post_data = <?= json_encode($postData) ?>;

        branchLabels = <?= $js_branch_labels ?>;
        branchValues = <?= $js_branch_values ?>;
        deptLabels = <?= $js_dept_labels ?>;
        deptValues = <?= $js_dept_values ?>;

        // Salesman arrays (for dtC users)
        salesLabels = <?= $js_sales_labels ?>;
        salesValues = <?= $js_sales_values ?>;

        $('#refreshBtn').on('click', function(){
            // simple page reload to refresh all server-side data
            location.reload();
        });

        branchValues = (branchValues || []).map(function(n){
            var v = Number(n);
            return (isNaN(v) || !isFinite(v)) ? 0 : v;
        });
        deptValues = (deptValues || []).map(function(n){
            var v = Number(n);
            return (isNaN(v) || !isFinite(v)) ? 0 : v;
        });
        salesValues = (salesValues || []).map(function(n){
            var v = Number(n);
            return (isNaN(v) || !isFinite(v)) ? 0 : v;
        });

        var itemLabels = <?= $js_item_labels ?>;
        var itemValues = <?= $js_item_values ?>; 
        itemValues = (itemValues || []).map(function(n){
            var v = Number(n);
            return (isNaN(v) || !isFinite(v)) ? 0 : v;
        });

        var initialLabels = branchLabels;
        var initialData = branchValues;

        <?php if (substr($get_Lvl, 0, 3) === 'dtC'): ?>
            initialLabels = salesLabels;
            initialData = salesValues;
        <?php elseif (($get_Lvl === 'DIR') || ($get_Lvl === 'ACCD')): ?>
            initialLabels = deptLabels;
            initialData = deptValues;
        <?php endif; ?>

        var branchChart = null;
        if (document.getElementById('branchChart')) {
            var ctx = document.getElementById('branchChart').getContext('2d');
            branchChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: initialLabels,
                    datasets: [{
                        label: 'Total (Juta)',
                        data: initialData,
                        backgroundColor: (function(){
                            var base = getComputedStyle(document.documentElement).getPropertyValue('--accent') || '#85A98F';
                            base = base.trim();
                            var arr = [];
                            for (var i=0;i<initialLabels.length;i++){
                                arr.push(base);
                            }
                            return arr;
                        })(),
                        borderRadius: 6,
                        barThickness: 36,
                        maxBarThickness: 48
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    var v = context.raw;
                                    var num = Number(v);
                                    if (isNaN(num)) num = 0;
                                    return 'Rp ' + num.toLocaleString('id-ID') + ' jt';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return Number(value).toLocaleString('id-ID') + ' jt';
                                }
                            },
                            grid: { color: 'rgba(0,0,0,0.04)' }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { autoSkip: false }
                        }
                    }
                }
            });
        }

        // If DIR: wire up controls
        <?php if (($get_Lvl === 'DIR') || ($get_Lvl === 'ACCD')): ?>
        // show/hide branch selector based on radio
        function setChartMode(mode) {
            if (!branchChart) return;
            if (mode === 'dept') {
                $('#branchSelectorWrap').hide();
                branchChart.data.labels = deptLabels;
                branchChart.data.datasets[0].data = deptValues;
                branchChart.options.scales.x.ticks.autoSkip = false;
                branchChart.update();
            } else if (mode === 'branch') {
                $('#branchSelectorWrap').show();
                // currently selected option
                var sel = $('#branchSelector').val() || '__ALL__';
                if (sel === '__ALL__') {
                    branchChart.data.labels = branchLabels;
                    branchChart.data.datasets[0].data = branchValues;
                } else {
                    branchChart.data.labels = [sel];
                    // find index
                    var idx = branchLabels.indexOf(sel);
                    var val = (idx >= 0 ? branchValues[idx] : 0);
                    branchChart.data.datasets[0].data = [val];
                }
                branchChart.options.scales.x.ticks.autoSkip = false;
                branchChart.update();
            }
        }

        $('input[name="chartMode"]').on('change', function(){
            setChartMode($(this).val());
        });

        $('#branchSelector').on('change', function(){
            setChartMode('branch');
        });

        // initial state: dept (we set earlier)
        $('#branchSelectorWrap').hide();
        <?php endif; ?>

        // PREP: ensure TopItems will allow wrapping and col widths reset
        $('#TopItemsTable').removeClass('nowrap');
        try {
            $('#TopItemsTable col').each(function(i, el){
                try { el.style.setProperty('width', 'auto', 'important'); } catch(e) { try { el.style.width='auto'; } catch(e2){} }
            });
        } catch(e){ /* ignore */ }

        // wrap single-word long tokens (fallback)
        $('#TopItemsTable tbody td:nth-child(2)').each(function(){
            var $td = $(this);
            var txt = $td.html();
            if (txt && !/\s/.test($td.text())) {
                $td.html('<span style="word-break:break-word;overflow-wrap:anywhere;">'+txt+'</span>');
            }
        });

        // init datatables if exist
        initializeDataTable('#TopItemsTable');
        initializeDataTable('#SOOpenKom');
        initializeDataTable('#SOOpenPro');
        initializeDataTable('#SOOpenExim');

        // small recalc helper to force datatables responsive recalc after layout changes
        (function(){
            function recalcTopItems() {
                $('#TopItemsTable').removeClass('nowrap');
                try {
                    $('#TopItemsTable col').each(function(i, el){ try{ el.style.setProperty('width','auto','important'); }catch(e){} });
                } catch(e){}
                if ($.fn.dataTable && $.fn.dataTable.isDataTable('#TopItemsTable')) {
                    try {
                        var dt = $('#TopItemsTable').DataTable();
                        dt.columns.adjust();
                        if (dt.responsive) dt.responsive.recalc();
                    } catch(e){ console.warn('recalc TopItems failed', e); }
                }
            }
            setTimeout(recalcTopItems, 180);
            var _t;
            $(window).on('resize', function(){
                clearTimeout(_t);
                _t = setTimeout(recalcTopItems, 220);
            });
        })();

        var $overlay = $('#modalOverlay'), $modal = $('#detailModal'), $content = $('#modalContent');

        // delegated click handler (works with DataTables re-rendering)
        var data = <?= json_encode($data) ?>;
        $(document).on('click', '.grouped-row', function(e){
            // jika klik pada link/button, lewati (agar tombol export/aksi tetap bisa dipakai)
            if ($(e.target).closest('a,button').length) return;

            var $row = $(this);
            var docNum = $row.data('docnum') || $row.attr('data-docnum');
            if (!docNum) return;

            var filteredData = (data || []).filter(function(item){ return item.DocNum == docNum; });
            if (filteredData.length === 0) return;

            var firstItem = filteredData[0];
            var totalQty = 0, totalOpenQty = 0, totalAmount = 0;
            var rowsHtml = '';

            filteredData.forEach(function(row){
                var q = parseFloat(row.Quantity) || 0;
                var oq = parseFloat(row.OpenQty) || 0;
                var t = parseFloat(row.Total) || 0;
                totalQty += q; totalOpenQty += oq; totalAmount += t;
                rowsHtml += '<tr>'+
                    '<td><strong>' + (row.ItemCode||'-') + '</strong><br><span style="font-size:12px;color:var(--color-text-light)">' + (row.Dscription||'-') + '</span></td>'+
                    '<td class="text-center">' + (row.Divisi||'-') + '</td>'+
                    '<td class="text-center">' + (row.Fokus||'-') + '</td>'+
                    '<td class="text-right">' + q.toLocaleString('id-ID') + '</td>'+
                    '<td class="text-right">' + oq.toLocaleString('id-ID') + '</td>'+
                    '<td class="text-right"><strong>' + ( (t/1000000).toLocaleString('id-ID', { style: 'currency', currency: 'IDR' }) ) + '</strong></td>'+
                    '</tr>';
            });

            var modalHtml = '<div class="modal-header-section"><div class="modal-main-header"><span class="doc-type">Sales Order Detail</span><h2 id="modalTitle">'+docNum+'</h2><div class="customer-name">'+(firstItem.CardName||'-')+'</div></div></div>';
            modalHtml += '<div class="details-grid">'+
                '<div class="detail-block"><svg viewBox="0 0 24 24"><path d="M19,19H5V8H19M16,1V3H8V1H6V3H5C3.89,3 3,3.89 3,5V19C3,20.1 3.9,21 5,21H19C20.1,21 21,20.1 21,19V5C21,3.89 20.1,3 19,3H18V1M17,12H12V17H17V12Z"/></svg><div class="detail-block-content"><span>Tanggal SO</span><strong>'+ new Date(firstItem.DocDate).toLocaleDateString('id-ID', {day:'2-digit', month:'long', year:'numeric'}) +'</strong></div></div>'+
                '<div class="detail-block"><svg viewBox="0 0 24 24"><path d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"/></svg><div class="detail-block-content"><span>Salesman</span><strong>'+(firstItem.SlpName||'-')+'</strong></div></div>'+
                '<div class="detail-block"><svg viewBox="0 0 24 24"><path d="M10,20V14H14V20H19V12H22L12,3L2,12H5V20H10Z"/></svg><div class="detail-block-content"><span>Cabang</span><strong>'+(firstItem.CabCode||'-')+'</strong></div></div>'+
                '<div class="detail-block"><svg viewBox="0 0 24 24"><path d="M4,15V9H12V4.16L19.84,12L12,19.84V15H4Z"/></svg><div class="detail-block-content"><span>Divisi</span><strong>'+(firstItem.Divisi||'-')+'</strong></div></div>'+
                '</div>';

            modalHtml += '<div class="modal-table-section"><table class="detail-table"><thead><tr><th>Produk</th><th class="text-center">Div</th><th class="text-center">Fokus</th><th class="text-right">Qty</th><th class="text-right">Open Qty</th><th class="text-right">Jumlah</th></tr></thead><tbody>'+rowsHtml+'</tbody><tfoot><tr><td colspan="3" class="total-label text-right">Total</td><td class="text-right">'+ totalQty.toLocaleString('id-ID') +'</td><td class="text-right">'+ totalOpenQty.toLocaleString('id-ID') +'</td><td class="text-right total-amount">'+ ( (totalAmount/1000000).toLocaleString('id-ID', { style:'currency', currency:'IDR'}) ) +'</td></tr></tfoot></table></div>';

            // show modal + overlay and lock body scroll
            $content.html(modalHtml);
            $overlay.addClass('active').show();
            $modal.addClass('active').show();
            $('body').addClass('modal-open');
            setTimeout(function(){ $content.focus(); }, 120);
        });

        function closeModal() {
            $('#modalOverlay').removeClass('active');
            $('#detailModal').removeClass('active');
            $('body').removeClass('modal-open');
            setTimeout(function(){
                $('#modalOverlay').hide();
                $('#detailModal').hide();
                $('#modalContent').html('');
            }, 180);
        }

        $('#closeModal').on('click', function(e){ e.stopPropagation(); closeModal(); });
        $('#modalOverlay').on('click', function(e){ closeModal(); });

        $(document).on('keydown', function(e){ if (e.key === "Escape") closeModal(); });

        var root = document.documentElement;
        $('#themeToggle').on('click', function(){
            var current = root.getAttribute('data-theme');
            var next = current === 'dark' ? null : 'dark';
            if (next) root.setAttribute('data-theme','dark'); else root.removeAttribute('data-theme');
            $(this).text(next ? '☀️ Light' : '🌙 Dark');
        });

        // --- Click handler for TopItems rows: show list of Open SO that contain the clicked Item ---
        $(document).on('click', '#TopItemsTable tbody tr', function(e){
            if ($(e.target).closest('a,button').length) return;

            var $row = $(this);
            var itemCode = $row.find('td:first').text().trim();
            if (!itemCode) return;

            var matched = (data || []).filter(function(r){
                if (!r) return false;
                var rc = (r.ItemCode || '').toString().trim();
                return rc.toLowerCase() === itemCode.toLowerCase();
            });

            if (!matched || matched.length === 0) {
                // fallback: tampilkan pesan singkat
                var modalHtml = '<div class="modal-header-section"><div class="modal-main-header"><span class="doc-type">Item</span><h2 id="modalTitle">'+itemCode+'</h2></div></div>';
                modalHtml += '<div class="modal-table-section"><div style="padding:18px 22px;">Tidak ada SO untuk item ini.</div></div>';
                $('#modalContent').html(modalHtml);
                $('#modalOverlay').addClass('active').show();
                $('#detailModal').addClass('active').show();
                $('body').addClass('modal-open');
                return;
            }

            // Build table rows
            var rowsHtml = '';
            matched.forEach(function(r){
                var cab = r.CabCode || '-';
                var doc = r.DocNum || '-';
                var cust = r.CardName || '-';
                // choose visible qty: prefer OpenQty (if present) else Quantity
                var qty = (typeof r.OpenQty !== 'undefined' && r.OpenQty !== null && r.OpenQty !== '') ? parseFloat(r.OpenQty) : (parseFloat(r.Quantity) || 0);
                if (!isFinite(qty)) qty = 0;
                // amount: r.Total is in IDR (not divided). convert to juta for display
                var amt = parseFloat(r.Total) || 0;
                var salesman = r.SlpName || '-';

                rowsHtml += '<tr>'+
                    '<td>'+ $('<div>').text(cab).html() +'</td>'+
                    '<td>'+ $('<div>').text(doc).html() +'</td>'+
                    '<td>'+ $('<div>').text(cust).html() +'</td>'+
                    '<td class="text-right">'+ (qty ? qty.toLocaleString('id-ID') : '0') +'</td>'+
                    '<td class="text-right">'+ ( (amt/1000000).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) ) +'</td>'+
                    '<td>'+ $('<div>').text(salesman).html() +'</td>'+
                    '</tr>';
            });

            var modalHtml = '<div class="modal-header-section"><div class="modal-main-header"><span class="doc-type">Daftar SO untuk Item</span><h2 id="modalTitle">'+ itemCode +'</h2></div></div>';
            modalHtml += '<div class="modal-table-section"><table class="item-so-table"><thead><tr><th>Cabang</th><th>No SO</th><th>Customer</th><th class="text-right">Qty</th><th class="text-right">Amount (jt)</th><th>Salesman</th></tr></thead><tbody>'+ rowsHtml +'</tbody></table></div>';

            // show modal + overlay and lock body scroll
            $('#modalContent').html(modalHtml);
            $('#modalOverlay').addClass('active').show();
            $('#detailModal').addClass('active').show();
            $('body').addClass('modal-open');
            setTimeout(function(){ $('#modalContent').focus(); }, 120);
        });

        // ensure table redraw on small delay to fix responsive columns
        setTimeout(function(){
            $('#SOOpenKom').trigger('resize');
            $('#SOOpenPro').trigger('resize');
            $('#SOOpenExim').trigger('resize');
            $('#TopItemsTable').trigger('resize');
        }, 250);
    });

    function initializeDataTable(selector) {
        if ($(selector).length === 0) return;

        var colCount = $(selector).find('thead th').length;

        var centerCandidates = [0,1,2,3,4];
        var rightCandidates  = [7,8]; // keep flexibility
        var centerTargets = centerCandidates.filter(function(i){ return i < colCount; });
        var rightTargets  = rightCandidates.filter(function(i){ return i < colCount; });

        var defaultOrder = [];
        if (colCount > 1) {
            if (colCount > 8) defaultOrder = [[9,'desc'], [1,'asc']];
            else if (colCount <5) defaultOrder = [[2,'desc']];
            else if (colCount > 1) defaultOrder = [[Math.max(0, colCount-1),'desc'], [1,'asc']];
            else defaultOrder = [[0,'asc']];
        } else {
            defaultOrder = [[0,'asc']];
        }

        var buttonsAvailable = !!($.fn.dataTable && $.fn.dataTable.Buttons);
        var domSetting = buttonsAvailable ? 'Bfrtip' : 'frtip';
        var buttonsConfig = [];

        if (buttonsAvailable && selector !== '#TopItemsTable') {
            buttonsConfig.push({
                extend: 'excelHtml5',
                text: '<i class="bi bi-file-earmark-arrow-down"></i> Excel',
                filename: 'DATA SO',
                className: 'dt-button',
                footer: false,
                exportOptions: {
                    // exclude hidden helper columns (class col-sort-hidden)
                    columns: function(idx, data, node) {
                        try {
                            return !$(node).hasClass('col-sort-hidden');
                        } catch (e) { return true; }
                    },
                    // exclude rows that are inside tfoot (so footer won't be exported)
                    rows: function(idx, data, node) {
                        // node is TR; return false if inside tfoot
                        try {
                            return $(node).closest('tfoot').length === 0;
                        } catch (e) {
                            return true;
                        }
                    },
                    format: {
                        body: function(data, row, column, node) {
                            try {
                                var $node = $(node);

                                var rawExport = $node.attr('data-export');
                                if (typeof rawExport !== 'undefined' && rawExport !== null && rawExport !== '') {
                                    return rawExport;
                                }
                                var rawTotal = $node.attr('data-total');
                                if (typeof rawTotal !== 'undefined' && rawTotal !== null && rawTotal !== '') {
                                    // rawTotal currently in juta -> convert back to full IDR
                                    var num = parseFloat(String(rawTotal).replace(',', '.')) || 0;
                                    return String(num * 1000000);
                                }

                                // use visible text
                                var visible = $node.text();
                                if (typeof visible !== 'string') visible = (visible === null || visible === undefined) ? '' : visible.toString();
                                var txt = visible.trim();
                                if (txt === '') return '';

                                // if contains letters -> keep as text (item codes / descriptions)
                                if (/[A-Za-z]/.test(txt)) return visible;

                                // if date-like -> keep as text (you asked tanggal tetap string)
                                var dateLike1 = /^(\d{1,2})[\/\-\.](\d{1,2})[\/\-\.](\d{4})$/;
                                var dateLike2 = /^(\d{4})[\/\-](\d{1,2})[\/\-](\d{1,2})$/;
                                if (dateLike1.test(txt) || dateLike2.test(txt)) return visible;

                                // keep leading-zero codes as text
                                if (/^0\d+$/.test(txt)) return visible;

                                // numeric normalization (ID format)
                                var cleaned = txt.replace(/^Rp\.?\s*/i, '').replace(/\s+/g, '');
                                if (cleaned.indexOf('.') !== -1 && cleaned.indexOf(',') !== -1) {
                                    cleaned = cleaned.replace(/\./g, '').replace(/,/g, '.');
                                } else if (cleaned.indexOf(',') !== -1 && cleaned.indexOf('.') === -1) {
                                    cleaned = cleaned.replace(/,/g, '.');
                                } else {
                                    cleaned = cleaned.replace(/[^0-9\.\-]/g, '');
                                }
                                var num = parseFloat(cleaned);
                                if (!isNaN(num) && isFinite(num)) return String(num);

                                return visible;
                            } catch (e) {
                                try { return $(node).text() || data; } catch (ee) { return data; }
                            }
                        }
                    }
                },
                customize: function (xlsx, row) {
                    var sheet = xlsx.xl.worksheets['sheet1.xml'];

                    var colWidths = [15, 13, 10, 5, 5, 30, 40, 12];
                    var colsNode = sheet.getElementsByTagName('cols')[0];
                    if (colsNode) {
                        // ensure <col> exists for each index
                        for (var i = 0; i < colWidths.length; i++) {
                            var col = colsNode.getElementsByTagName('col')[i];
                            if (!col) {
                                col = sheet.createElement('col');
                                col.setAttribute('min', i + 1);
                                col.setAttribute('max', i + 1);
                                colsNode.appendChild(col);
                            }
                            col.setAttribute('width', colWidths[i]);
                            col.setAttribute('customWidth', '1');
                        }
                        // cap very large widths
                        var allCols = colsNode.getElementsByTagName('col');
                        for (var j = 0; j < allCols.length; j++) {
                            var w = parseFloat(allCols[j].getAttribute('width') || 0);
                            if (w > 80) allCols[j].setAttribute('width', 80);
                        }
                    }
                    $('row c[r^="H"], row c[r^="I"]', sheet).attr( 's', 64);

                    // --- ensure header row cells get a bold style (optional) ---
                    try {
                        // cell might contain inlineStr or v
                        var vNode = headerCells[h].getElementsByTagName('v')[0];
                        if (vNode) {
                            var textVal = vNode.textContent || '';
                            if (textVal.indexOf('Total') !== -1 || textVal.indexOf('Juta') !== -1) {
                                // set to plain "Total"
                                vNode.textContent = 'Total';
                            }
                        } else {
                            var isNode = headerCells[h].getElementsByTagName('is')[0];
                            if (isNode) {
                                var tNode = isNode.getElementsByTagName('t')[0];
                                if (tNode) {
                                    var txt = tNode.textContent || '';
                                    if (txt.indexOf('Total') !== -1 || txt.indexOf('Juta') !== -1) {
                                        tNode.textContent = 'Total';
                                    }
                                }
                            }
                        }
                    } catch(e){}

                    // --- FORCE WRAP TEXT: update styles.xml cellXfs entries ---
                    try {
                        var styles = xlsx.xl['styles.xml'];
                        var cellXfs = styles.getElementsByTagName('cellXfs')[0];
                        if (cellXfs) {
                            for (var xi = 0; xi < cellXfs.childNodes.length; xi++) {
                                var xf = cellXfs.childNodes[xi];
                                // mark applyAlignment
                                xf.setAttribute('applyAlignment', '1');
                                // ensure alignment node exists and set wrapText=1
                                var alignment = xf.getElementsByTagName('alignment')[0];
                                if (!alignment) {
                                    alignment = styles.createElement('alignment');
                                    xf.appendChild(alignment);
                                }
                                alignment.setAttribute('wrapText', '1');
                            }
                        }
                    } catch (e) {
                        console.warn('wrap-text customize failed', e);
                    }
                }
            });
        }


        try {
            $(selector).DataTable({
                paging: true,
                searching: true,
                ordering: true,
                info: true,
                responsive: {
                    details: {
                        type: 'inline' // inline detail row saat kolom tersembunyi
                    }
                },
                order: defaultOrder,
                columnDefs: (function(){
                    var defs = [];
                    if (centerTargets.length) defs.push({ className: 'dt-center', targets: centerTargets });
                    if (rightTargets.length)  defs.push({ className: 'dt-right', targets: rightTargets });
                    return defs;
                })(),
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api();
                    if (colCount > 8) {
                        var total = api.column(8).nodes().reduce(function(a, b) {
                            return a + (parseFloat($(b).data('total')) || 0);
                        }, 0);
                        try { $(api.column(8).footer()).html(total.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 })); } catch(e) {}
                    }
                    try {
                        var distinctCount = new Set(api.column(1).data().toArray()).size;
                        var itemCount = api.column(1).data().toArray().length;
                        $(api.column(5).footer()).html('Total SO = ' + distinctCount + '  (' + itemCount +' item)');
                    } catch(e){}
                },
                dom: domSetting,
                buttons: buttonsConfig
            });
        } catch (err) {
            console.error('DataTable init error for', selector, err);
            // fallback: try minimal init without buttons
            try {
                $(selector).DataTable({
                    paging: true, searching: true, ordering: true, info: true, responsive: true, order: defaultOrder
                });
            } catch (err2) {
                console.error('Fallback DataTable init failed for', selector, err2);
            }
        }
    }
</script>

</body>
</html>

<?php
} else {
    header('Location: ../../noaccess.php');
    exit();
}
?>
