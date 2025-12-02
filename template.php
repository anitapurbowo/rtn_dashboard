<?php
session_start();

require_once('./../../global_func.php');

$get_Lvl = null;

if (!empty($_GET['t'])) {
    $get_Lvl = cek_credential($_GET['t']);
} else {
     echo "<h2>Server misconfiguration: TOKEN not found.</h2>";
     exit;
}

if (in_array($get_Lvl, $allowedLevels)) {
    $pembagi = $pembagi_global; 

?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="refresh" content="3000">
    <title>Dashboard Penjualan — Realtime</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../../resicon/lho.png" type="image/x-icon">

    <!-- DataTables CSS & core -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
    <script defer src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>

    <!-- Responsive (after core) -->
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.dataTables.min.css">
    <script defer src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>

    <!-- Buttons extension + dependencies (load after core/responsive) -->
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
    <script defer  src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script defer src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>

    <!-- Chart.js (after DataTables scripts) -->
    <script defer src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <!-- icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- custom css -->
    <link rel="stylesheet" href="../../css/template.css">
    <!-- custom js -->
    <script defer src="../../js/template_js.js"></script>

    <style>
        .top-actions .btn { padding:6px 10px; border-radius:8px; }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            gap: 12px;
            margin-bottom: 14px;
            align-items: start;
        }
        .col-4 { grid-column: span 4; }
        .col-8 { grid-column: span 8; }
        .col-12 { grid-column: span 12; }

        /* reuse summary card styling names */
        .summary-grid { display:flex; gap:12px; align-items:stretch; }
        .summary-card { flex:1 1 0; padding:12px; border-radius:10px; background:var(--card); box-shadow:var(--shadow-sm); border:1px solid var(--line); position:relative; min-height:84px; }
        .summary-title { font-size:12px; font-weight:700; color:var(--muted); text-transform:uppercase; }
        .summary-value { font-size:22px; font-weight:800; margin-top:8px; color:var(--accent-dark); }
        .summary-sub { font-size:12px; color:var(--muted); margin-top:6px; }
        

        .table-card { background: var(--card); padding:12px; border-radius:12px; box-shadow: var(--shadow-sm); border:1px solid var(--line); margin-bottom:12px; }

        /* tiny helpers */
        .clickable-row { cursor:pointer; }
        .muted { color: var(--muted); font-size:13px; }

        /* make modals similar to SO */
        #modalOverlay { display:none; position:fixed; inset:0; background: rgba(2,6,23,0.5); z-index:9998; }
        #modalBox { display:none; position:fixed; z-index:9999; left:50%; top:50%; transform: translate(-50%,-50%); max-width:920px; width:94%; background:var(--card); border-radius:12px; box-shadow:0 20px 60px rgba(0,0,0,0.35); }
        #modalHeader { padding:14px 18px; border-bottom: 1px solid var(--line); }
        #modalBody { padding:12px 18px; max-height:66vh; overflow:auto; }
        #modalClose { position:absolute; right:12px; top:12px; border:0; background:transparent; font-size:20px; cursor:pointer; }
        
    </style>
</head>
<body>
<div class="wrap">

    <div class="topbar" role="banner">
        <div class="brand" aria-hidden="true">
            <img src="../../resicon/HotOffer.png" alt="Icon" />
            <div>
                <h1>Data HOT OFFER </h1>
                <small class="muted">Ringkasan transaksi & realisasi penjualan</small>
            </div>
        </div>

        <div class="top-actions" role="toolbar" aria-label="toolbar actions">
            <button id="refreshBtn" class="btn">🔄 Refresh</button>
            <button id="themeToggle" class="btn" aria-pressed="false">🌙 DARK</button>
        </div>

        <div class="toolbar-warning" id="toolbarWarning">
            <i class="bi bi-info-circle" style="margin-right:8px;"></i>
             ℹ️ Klik salah satu baris pada tabel untuk melihat detail Invoice
        </div>
    </div>
</div>

<!-- libs -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>

    $(document).ready(function() {
        var root = document.documentElement;
        js_data = <?= $data; ?>;

        $('#refreshBtn').on('click', function(){
            location.reload();
        });

        $('#themeToggle').on('click', function(){
            var current = root.getAttribute('data-theme');
            var next = current === 'dark' ? null : 'dark';
            if (next) root.setAttribute('data-theme','dark'); else root.removeAttribute('data-theme');
            $(this).text(next ? '☀️ Light' : '🌙 Dark');
        });
    });
</script>
</body>
</html>

<?php
} else {
    header('Location: ../noaccess.php');
    exit();
}
?>