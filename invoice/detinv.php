<?php
session_start();

@require_once('./../global_func.php');

$get_Lvl = null;

if (!empty($_GET['t'])) {
    $get_Lvl = cek_credential($_GET['t']);
} else {
     echo "<h2>Server misconfiguration: TOKEN not found.</h2>";
}

if (in_array($get_Lvl, $allowedLevels)) {
    $pembagi = $pembagi_global; 
    @include_once('getInv.php');
    
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="refresh" content="3000">
    <title>Dashboard Penjualan — Realtime</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../resicon/linv.ico" type="image/x-icon">

    <!-- libs -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

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
    <link rel="stylesheet" href="../css/template.css">
    <!-- custom js -->
    <script defer src="../js/template_js.js"></script>

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
            <img src="../resicon/invoice-receipt-svgrepo-com.svg" alt="Icon" />
            <div>
                <h1>Dashboard Penjualan — <?= htmlspecialchars($getBln . ' ' . $getThn) ?></h1>
                <small class="muted">Ringkasan transaksi & realisasi penjualan</small>
            </div>
        </div>

        <div class="top-actions" role="toolbar" aria-label="toolbar actions">
            <button id="refreshBtn" class="btn">🔄 Refresh</button>
            <button id="themeToggle" class="btn">🌙 DARK</button>
        </div>

        <div class="toolbar-warning" id="toolbarWarning">
            <i class="bi bi-info-circle" style="margin-right:8px;"></i>
             ℹ️ Klik salah satu baris pada tabel untuk melihat detail Invoice
        </div>
    </div>

    <!-- SUMMARY / CARDS -->
    <div class="table-card">
        <div class="summary-grid" style="gap:16px;">
            <div class="summary-card" style="min-width:180px;">
                <div class="summary-title">Transaksi Hari Ini</div>
                <div id="cardTodayCount" class="summary-value">-</div>
                <div id="cardTodayAmount" class="summary-sub">Rp -</div>
            </div>

            <div class="summary-card" style="min-width:180px;">
                <div class="summary-title">Transaksi Bulan Ini</div>
                <div id="cardMonthCount" class="summary-value"><?= number_format($totalMonthCount) ?></div>
                <div id="cardMonthAmount" class="summary-sub"><?= htmlspecialchars('Rp ' . number_format($totalMonthReal / $pembagi, 2, ',', '.')) ?></div>
            </div>

            <div class="summary-card">
                <div class="summary-title">Penjualan AAI</div>
                <div class="summary-value"><?= number_format(round(($deptTotals['AAI'] / $pembagi),2), 2) ?></div>
                <div class="summary-sub">dalam juta</div>
            </div>

            <div class="summary-card">
                <div class="summary-title">Penjualan AGE</div>
                <div class="summary-value"><?= number_format(round(($deptTotals['AGE'] / $pembagi),2), 2) ?></div>
                <div class="summary-sub">dalam juta</div>
            </div>

            <div class="summary-card">
                <div class="summary-title">Penjualan NLU</div>
                <div class="summary-value"><?= number_format(round(($deptTotals['NLU'] / $pembagi),2), 2) ?></div>
                <div class="summary-sub">dalam juta</div>
            </div>

            <div class="summary-card">
                <div class="summary-title">Top Sales</div>
                <div id="topSalesmanName" class="summary-value">-</div>
                <div id="topSalesmanAmount" class="summary-sub">Rp -</div>
            </div>
        </div>
    </div>

    <!-- GRID: left = branch list + charts, right = today's sales -->
    <div class="dashboard-grid">

        <!-- Branch list (per-cabang) -->
        <div class="col-8">
            <div class="table-card">
                <div class="section-title">Daftar Penjualan per Cabang (<?= htmlspecialchars($getBln) ?>)</div>
                <table id="tbBranch" class="display nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th>Cabang</th>
                            <th class="text-right">Realisasi (Juta)</th>
                            <th class="text-right">Target (Juta)</th>
                            <th class="text-right">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($grouped as $cabang => $nilai): 
                            $real = is_numeric($nilai['real']) ? $nilai['real'] / $pembagi : 0;
                            $tgt = is_numeric($nilai['target']) ? $nilai['target'] / $pembagi : 0;
                            $pct = ($tgt == 0) ? 0 : ($real / $tgt * 100);
                        ?>
                        <tr class="clickable-row" data-cabang="<?= htmlspecialchars($cabang) ?>">
                            <td><?= htmlspecialchars($cabang) ?></td>
                            <td class="text-right"><?= number_format($real, 2, ',', '.') ?></td>
                            <td class="text-right"><?= number_format($tgt, 2, ',', '.') ?></td>
                            <td class="text-right"><?= number_format($pct, 2, ',', '.') ?>%</td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- small branch chart -->
            <div class="table-card" style="margin-top:10px;">
                <div class="section-title">Grafik Ringkasan Cabang (Top 10 berdasarkan Realisasi)</div>
                <canvas id="branchBar" style="height:240px;"></canvas>
            </div>
        </div>

        <!-- Today's sales & top salesman -->
        <div class="col-4">
            <div class="table-card">
                <div class="section-title">Penjualan Hari Ini (<?= date('Y-m-d') ?>)</div>
                <table id="tbToday" class="display nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th>No Inv</th>
                            <th>Sales</th>
                            <th class="text-right">Total (Juta)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- diisi via AJAX -->
                    </tbody>
                </table>
            </div>

            <div class="table-card" style="margin-top:12px;">
                <div class="section-title">Salesman Teratas (Hari Ini)</div>
                <div id="topSalesList">
                    <!-- generated via JS -->
                    <p class="muted">Memuat...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- detail area untuk branch / item -->
    <div id="modalOverlay"></div>
    <div id="modalBox" role="dialog" aria-modal="true">
        <button id="modalClose">&times;</button>
        <div id="modalHeader"><strong id="modalTitle">Detail</strong></div>
        <div id="modalBody"></div>
    </div>

</div> <!-- /wrap -->

<script>
    const pembagi = <?= json_encode($pembagi) ?>;
    const branchData = <?= json_encode(array_map(function($k,$v){ return ['cabang'=>$k, 'real'=>$v['real'], 'target'=>$v['target']]; }, array_keys($grouped), $grouped)) ?>;

    $(document).ready(function(){
        js_lvl = <?= json_encode($get_Lvl) ?>;
        js_post_data = <?= json_encode($postData) ?>;
        js_data = <?= json_encode($data) ?>;

        $('#tbBranch').DataTable({
            paging: true, searching: true, ordering: true, info: false, responsive: true,
            columnDefs: [{ targets: [1,2,3], className: 'dt-right' }],
            dom: 'frtip'
        });

        $('#tbToday').DataTable({
            paging: true, searching: true, ordering: true, info: false, responsive: true,
            columnDefs: [{ targets: [2], className: 'dt-right' }],
            dom: 'frtip'
        });

        // Refresh button
        $('#refreshBtn').on('click', function(){ location.reload(); });

        // Branch click -> open modal and load branch sales (ajax getinvoice.php)
        $('#tbBranch tbody').on('click', 'tr.clickable-row', function(){
            const cabang = $(this).data('cabang');
            openModal('Penjualan Cabang: ' + cabang);
            // call ajax to get detail; using existing getinvoice.php endpoint (as in original)
            $.ajax({
                url: 'getDetInv.php',
                method: 'POST',
                data: {
                    aksi: 'getInvoice',
                    nama: cabang,
                    bulan: '<?= $getBln ?>',
                    dir: <?= json_encode($getDir) ?>,
                    awal: '<?= addslashes($awal) ?>',
                    akhir: '<?= addslashes($akhir) ?>',
                    dev: <?= json_encode($getDev) ?>
                },
                success: function(resp) {
                    // assume resp is array of items
                    renderBranchDetail(cabang, resp);
                },
                error: function(xhr, status, err) {
                    $('#modalBody').html('<p class="muted">Error memuat data cabang.</p>');
                }
            });
        });

        // Load today's sales (AJAX)
        loadTodaySales();

        // modal close
        $('#modalClose, #modalOverlay').on('click', closeModal);
        $(document).on('keydown', function(e){ if (e.key === "Escape") closeModal(); });
    });

    (function setup() {
        // branch chart
        const arr = branchData.slice().sort((a,b)=> (b.real - a.real)).slice(0,10);
        const labels = arr.map(x=>x.cabang);
        const vals = arr.map(x=>Math.round((x.real/pembagi)*100)/100);

        const ctx = document.getElementById('branchBar').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Realisasi (Juta)',
                    data: vals,
                    backgroundColor: labels.map(()=> getComputedStyle(document.documentElement).getPropertyValue('--accent') || '#85A98F'),
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        ticks: {
                            callback: v => v.toLocaleString('id-ID') + ' jt'
                        }
                    }
                }
            }
        });
    })();

    function openModal(title){
        $('#modalTitle').text(title);
        $('#modalBody').html('<p class="muted">Memuat...</p>');
        $('#modalOverlay').show();
        $('#modalBox').show();
    }
    function closeModal(){
        $('#modalOverlay').hide();
        $('#modalBox').hide();
        $('#modalBody').html('');
    }

    function renderBranchDetail(cabang, data) {
        // defensive: if server returned JSON string, parse it
        let rows = data;
        try { if (typeof data === 'string') rows = JSON.parse(data); } catch(e){}
        if (!rows || !rows.length) {
            $('#modalBody').html('<p class="muted">Tidak ada transaksi untuk cabang ini.</p>');
            return;
        }
        // Build table
        let html = '<div class="table-card"><table class="display nowrap" style="width:100%"><thead><tr><th>No Inv</th><th>Sales</th><th>Customer</th><th class="text-right">Total (Juta)</th></tr></thead><tbody>';
        rows.forEach(item => {
            // fields: docnum, salesname, cardname, amountreal (assumed)
            const total = (parseFloat(item.amountreal || item.amount || 0) / pembagi).toLocaleString('id-ID', {minimumFractionDigits:2});
            html += `<tr data-item='${escapeHtml(JSON.stringify(item))}' class="branch-item clickable-row"><td>${item.docnum||'-'}</td><td>${item.salesname||'-'}</td><td>${item.cardname||'-'}</td><td class="text-right">${total}</td></tr>`;
        });
        html += '</tbody></table></div>';
        $('#modalBody').html(html);

        // attach click handler to show item detail
        $('#modalBody').find('tr.branch-item').on('click', function(){
            const payload = $(this).attr('data-item');
            let obj;
            try { obj = JSON.parse(unescapeHtml(payload)); } catch(e){ obj = null; }
            renderItemDetail(obj);
        });
    }

    function renderItemDetail(item){
        if (!item) {
            $('#modalBody').html('<p class="muted">Detail tidak tersedia.</p>');
            return;
        }
        let html = `<div class="table-card"><div class="section-title">Detail Invoice ${item.docnum || ''}</div>`;
        html += '<table style="width:100%"><tbody>';
        // render key fields defensively
        html += `<tr><td><strong>Invoice</strong></td><td>${item.docnum||'-'}</td></tr>`;
        html += `<tr><td><strong>Sales</strong></td><td>${item.salesname||'-'}</td></tr>`;
        html += `<tr><td><strong>Customer</strong></td><td>${item.cardname||item.cardcode||'-'}</td></tr>`;
        html += `<tr><td><strong>Tanggal</strong></td><td>${item.docdate || item.tgl || '-'}</td></tr>`;
        html += `<tr><td><strong>Total</strong></td><td>Rp ${( (parseFloat(item.amountreal||item.amount||0) / pembagi).toLocaleString('id-ID', {minimumFractionDigits:2}) )}</td></tr>`;
        html += '</tbody></table></div>';
        $('#modalBody').html(html);
    }

    function loadTodaySales(){
        const today = new Date();
        const yyyy = today.getFullYear();
        const mm = String(today.getMonth() + 1).padStart(2, '0'); 
        const dd = String(today.getDate()).padStart(2, '0');

        const formatted = `${yyyy}-${mm}-${dd}`;

        $.ajax({
            url: 'getDetInv.php',
            method: 'POST',
            data: { aksi: 'getInvoice', dir: <?= json_encode($getDir) ?>, dev: <?= json_encode($getDev) ?>, awal: formatted, akhir: formatted },
            success: function(resp) {
                let rows = resp;
                try { if (typeof resp === 'string') rows = JSON.parse(resp); } catch(e){}
                const dt = $('#tbToday').DataTable();
                dt.clear();
                let totalToday = 0;
                let salesAgg = {}; // salesName => total
                (rows || []).forEach(item => {
                    const amount = parseFloat(item.amountreal || item.amount || 0) || 0;
                    totalToday += amount;
                    const sales = item.salesname || item.SlpName || '-';
                    if (!salesAgg[sales]) salesAgg[sales] = 0;
                    salesAgg[sales] += amount;
                    dt.row.add([ item.docnum || '-', sales, (amount/pembagi).toFixed(2) ]);
                });
                dt.draw();

                // update cards: count and amount
                $('#cardTodayCount').text((rows && rows.length) ? rows.length : '0');
                $('#cardTodayAmount').text('Rp ' + (totalToday / pembagi).toLocaleString('id-ID', {minimumFractionDigits:2}));

                // compute top salesman
                const salesList = Object.keys(salesAgg).map(k => ({ name:k, amt: salesAgg[k] }));
                salesList.sort((a,b)=> b.amt - a.amt);
                if (salesList.length) {
                    $('#topSalesmanName').text(salesList[0].name);
                    $('#topSalesmanAmount').text('Rp ' + (salesList[0].amt / pembagi).toLocaleString('id-ID', {minimumFractionDigits:2}));
                } else {
                    $('#topSalesmanName').text('-');
                    $('#topSalesmanAmount').text('Rp -');
                }

                // render top sales list
                const topHtml = salesList.slice(0,5).map(s => `<div style="display:flex;justify-content:space-between;margin-bottom:6px;"><div>${escapeHtml(s.name)}</div><div>Rp ${(s.amt/pembagi).toLocaleString('id-ID',{minimumFractionDigits:2})}</div></div>`).join('');
                $('#topSalesList').html(topHtml || '<p class="muted">Tidak ada transaksi hari ini.</p>');

                // attach click handler on today rows to show detail (use client-side data)
                $('#tbToday tbody tr').off('click').on('click', function(e){
                    const idx = $(this).closest('tr').index();
                    const item = (rows || [])[idx] || null;
                    if (item) {
                        openModal('Detail Invoice ' + (item.docnum || '-'));
                        renderItemDetail(item);
                    }
                });
            },
            error: function() {
                $('#cardTodayCount').text('0');
                $('#cardTodayAmount').text('Rp 0');
                $('#topSalesList').html('<p class="muted">Gagal memuat data.</p>');
            }
        });
    }

    // helper escape functions for storing JSON into DOM attributes
    function escapeHtml(str) {
        return String(str).replace(/&/g, '&amp;').replace(/"/g,'&quot;').replace(/'/g,'&#39;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    }
    function unescapeHtml(s) {
        try {
            return s.replace(/&quot;/g,'"').replace(/&#39;/g,"'").replace(/&lt;/g,'<').replace(/&gt;/g,'>').replace(/&amp;/g,'&');
        } catch(e){ return s; }
    }

</script>
</body>
</html>

<?php
} else {
    header('Location: ../noaccess.php');
    exit();
}
?>