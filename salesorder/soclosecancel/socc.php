<?php
session_start();
@include_once('./../../token_tool.php');
$secret ='RUTAN_DASHBOARD';
$get_Lvl = null;

if (!empty($_GET['t'])) {
    if (empty($secret)) {
        http_response_code(500);
        echo "<h2>Server misconfiguration: TOKEN_SECRET not found.</h2>";
        error_log("[token] TOKEN_SECRET kosong saat decode request dari " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
        exit;
    }

    $token = $_GET['t'];
    try {
        $decoded = text_token_decode($token, $secret);
        if ($decoded=='DIR' || $decoded=='AAI' || $decoded=='AGE' || $decoded=='ACCD') {
            $bln = isset($_GET['bln']) ? $_GET['bln'] : null;
            $thn = isset($_GET['thn']) ? $_GET['thn'] : null;
            $pilih = isset($_GET['pilih']) ? $_GET['pilih'] : '--';
            @include_once('getsocc.php'); // expects $data_close, $data_cancel, $datatampil, optionally $branch_stats
        }
    } catch (Exception $e) {
        http_response_code(403);
        echo "<h2>Invalid token: " . htmlspecialchars($e->getMessage()) . "</h2>";
        error_log("[token] decode failed: " . $e->getMessage() . " from " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
        exit;
    }
} else {
     echo "<h2>Server misconfiguration: TOKEN_SECRET not found.</h2>";
     exit;
}

// Example fallback for branch stats (if not provided by backend)
// expected format: array of arrays [['CAB A', 12], ['CAB B', 8], ...]
if (!isset($branch_stats)) {
    // build from $data_close or $data_cancel as simple fallback
    $branch_counts = [];
    if (isset($data_close) && is_array($data_close)) {
        foreach ($data_close as $r) {
            $cab = isset($r['Cab']) ? $r['Cab'] : 'Unknown';
            if (!isset($branch_counts[$cab])) $branch_counts[$cab] = 0;
            $branch_counts[$cab] += 1;
        }
    }
    $branch_stats = [];
    foreach ($branch_counts as $k=>$v) $branch_stats[] = [$k, $v];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="refresh" content="3000">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Open SO</title>
    <link rel="icon" href="../../resicon/lsoc.png" type="image/x-icon">

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
    
    <!-- Responsive -->
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.dataTables.min.css">
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>

        <!-- custom css -->
    <link rel="stylesheet" href="../../css/template.css">

    <style>

    .chip { border-radius:999px; padding:6px 10px; font-size:13px; border:1px solid var(--line); background:transparent; color:var(--muted); }
    .btn { display:inline-flex; align-items:center; gap:8px; padding:8px 12px; border-radius:10px; cursor:pointer; background:var(--accent-dark); color:white; font-weight:600; }
    .btn.small{ padding:6px 9px; font-size:13px; }

    .table-card{ background:var(--card); border-radius:10px; padding:8px; margin-bottom:12px; box-shadow:var(--shadow-sm); transition: transform .12s ease; }
    .table-card:hover{ transform: translateY(-3px); box-shadow: 0 18px 40px rgba(12,30,40,0.06); }

    .section-note{ font-size:12px; color:var(--muted); padding:2px 10px 6px 10px; }

    /* ===========================
       COMPACT + FONT + BUTTON LEFT
       =========================== */

    /* baseline: paksa semua sel header & body lebih kecil dan font +1pt ≈ set ke 12px */
    table.dataTable thead th,
    table.dataTable tbody td,
    table.dataTable th,
    table.dataTable td {
      padding: 6px 8px !important;  
      line-height: 1.3 !important;
      font-size: 14px !important; 
    }

    .wrap-col { white-space: normal !important; word-break: break-word; max-width:520px; }

    .dataTables_wrapper .dt-buttons {
      float: left !important;                /* dipindahkan ke kiri */
      margin-bottom: 6px;
      display: inline-flex;
      gap: 8px;
    }
    .dataTables_wrapper .dt-button {
      border-radius:8px !important;
      padding:6px 12px !important;
      border:0 !important;
      background: linear-gradient(90deg, #1f7a5b, #2e8f66) !important;
      color:white !important;
      font-weight:700 !important;
      box-shadow: 0 8px 20px rgba(20,90,60,0.12) !important;
      display:inline-flex;
      align-items:center;
      gap:8px;
    }

    /* header visuals */
    thead th{ background:var(--table-head); color:#fff; text-transform:uppercase; letter-spacing:.6px; position: sticky; top:0; z-index:2; border-bottom: 1px solid rgba(255,255,255,0.06); }

    /* tubuh tabel: minimal rules (padding sudah di atas) */
    tbody td { background:transparent; border-bottom:1px solid var(--line); color:var(--text); vertical-align: middle; }

    /* buat cell alasan tetap kompak: turunkan max-height sedikit dan font sedikit lebih besar */
    .alasan-cell-content { position:relative; overflow:hidden; padding-right:36px; }
    .truncate-text {
      display:block !important;
      max-height: 36px !important;   /* sedikit lebih tinggi (karena font sedikit lebih besar) */
      margin:0 !important;
      overflow:hidden !important;
      line-height:1.15 !important;
      transition:max-height .18s ease !important;
      color:var(--text) !important;
      font-size:12px !important;
    }
    .truncate-text.is-expanded { max-height:420px !important; }

    .has-batal td { background: linear-gradient(90deg, rgba(217,83,79,0.06), rgba(255,255,255,0)) !important; border-left: none !important; box-shadow: none !important; }

    .text-center{ text-align:center; }
    .text-right{ text-align:right; }

    .grouped-row:hover { cursor:pointer; background-color:#ECFDF5 !important; transform:scale(1.01); transition:transform .12s ease, background-color .12s ease; }

    /* modal */
    .modal-overlay { display:none; position:fixed; inset:0; background:rgba(2,6,23,0.45); z-index:9999; align-items:center; justify-content:center; padding:18px; -webkit-overflow-scrolling:touch; }
    .modal-overlay.show { display:flex; animation: modalFade .14s ease; }
    @keyframes modalFade { from{opacity:0} to{opacity:1} }
    .modal { width: min(980px,98%); max-height: calc(100vh - 64px); background:var(--card); border-radius:12px; padding:14px; box-shadow:0 20px 60px rgba(2,6,23,0.45); overflow:auto; border:1px solid var(--line); }
    .modal-header { display:flex; align-items:center; justify-content:space-between; gap:12px; }
    .modal-body { margin-top:10px; }
    .modal iframe, .modal img { width:100%; height:62vh; border:0; border-radius:8px; display:block; object-fit:contain; background:transparent; }
    .modal .close-inline { margin:0; }

    /* small screens: tombol center, font sedikit turun agar muat */
    @media (max-width:900px){
      .brand h1{ font-size:15px; }
      .toolbar-warning{ font-size:13px; padding:10px; }
      thead th{ font-size:11px; padding:6px; }
      tbody td{ font-size:11px; padding:4px !important; white-space: normal !important; }
      .dataTables_wrapper .dt-buttons { float:none !important; justify-content:center; margin-bottom:10px; }
      .modal iframe, .modal img { height:48vh; }
      .wrap-col { white-space: normal !important; }
      .truncate-text { max-height: 48px !important; }
    }

    /* detail modal internal table */
    .detail-table { width: 100%; border-collapse: collapse; margin-top:12px; }
    .detail-table th, .detail-table td { padding:6px 8px; border-bottom:1px solid var(--line); font-size:13px; }
    .detail-table thead th { background: #f4f7f6; text-transform:none; color:var(--muted); font-weight:700; font-size:12px; }
    .detail-table tfoot td { font-weight:700; padding-top:8px; }
    .total-label { text-align:right; padding-right:12px; }

    /* chart card */
    .chart-card { display:flex; gap:14px; align-items:center; flex-wrap:wrap; max-height: 260px; min-height: 140px; overflow: hidden; padding: 12px; }
    .chart-canvas-wrap { flex: 1 1 60%; min-width: 240px; max-width: 100%; max-height: 220px; height: 160px; overflow: hidden; position: relative; }
    .chart-canvas-wrap canvas { width: 100% !important; height: 100% !important; display: block; object-fit: contain; }

    </style>
</head>
<body>

<div class="wrap">
  <!-- TOPBAR -->
  <div class="topbar" role="banner">
    <div class="brand" aria-hidden="true">
      <img src="../../resicon/briefcase-svgrepo-com.svg" alt="Icon Briefcase" width="48" height="42" style="flex-shrink:0;"/>
      <div>
        <h1>Open SO - Close & Cancel</h1>
        <small>Daftar SO yang telah di Close / Cancel dalam periode tertentu</small>
      </div>
    </div>

    <div class="top-actions" role="toolbar" aria-label="toolbar actions">
      <button id="refreshBtn" class="btn">🔄 Refresh</button>
      <button id="themeToggle" class="btn small" aria-pressed="false">🌙 Dark</button>
    </div>

    <div class="toolbar-warning" id="toolbarWarning">
      <i class="bi bi-info-circle" style="margin-right:8px;"></i>
      ℹ️ Klik salah satu baris tabel <strong>SO Close / SO Cancel</strong> untuk melihat dokumen atau detail.
    </div>
  </div>

  <!-- SO CLOSE -->
  <div class="table-card">
    <div class="section-title">DAFTAR SO CLOSE</div>
    <div class="section-note">SO yang dibatalkan dengan kondisi sudah terpenuhi sebagian</div>
    <table id="SOClose" class="display nowrap table-hover table-striped table-bordered" cellspacing="0" width="100%">
      <thead>
        <tr>
          <th class="text-center">Status</th>
          <th class="text-center">Cab</th>
          <th class="text-center">Tanggal</th>
          <th class="text-center">No SO</th>
          <th class="text-center">Customer</th>
          <th class="text-center">Kode Produk</th>
          <th class="text-center">Qty Pesan</th>
          <th class="text-center">Qty Batal</th>
          <th class="text-center">Tanggal<br/>Close/Cancel</th>
          <th class="text-center">Kategori</th>
          <th class="text-center">Alasan</th>
          <th class="text-center">DOK</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($data_close as $row):
            $tgldoc = new DateTime(date('d-m-Y',strtotime(substr(isset($row['TglDoc']) ? $row['TglDoc'] : '-',0,10))));
            $tglclose = new DateTime(date('d-m-Y',strtotime(substr(isset($row['TglClose']) ? $row['TglClose'] : '-',0,10))));
        ?>
          <tr data-docnum="<?= htmlspecialchars($row['DocNum'] ?? '') ?>" class="grouped-row">
              <td class="text-center"><?= htmlspecialchars($row['SttSO'] ?? '-') ?></td>
              <td class="text-center"><?= htmlspecialchars($row['Cab'] ?? '-') ?></td>
              <td class="text-center"><?= $tgldoc->format('d-m-Y') ?></td>
              <td class="text-center"><?= htmlspecialchars($row['NoDoc'] ?? '-') ?></td>
              <td class="text-center"><?= htmlspecialchars($row['KodeCust'] ?? '-') ?></td>
              <td><?= htmlspecialchars($row['KodeProd'] ?? '-') ?></td>
              <td class="text-right"><?= isset($row['QtyPesan']) ? number_format($row['QtyPesan'],2,',','.') : '0' ?></td>
              <td class="text-right"><?= isset($row['QtyBatal']) ? number_format($row['QtyBatal'],2,',','.') : '0' ?></td>
              <td class="text-center"><?= $tglclose->format('d-m-Y') ?></td>
              <td class="text-center"><?= htmlspecialchars($row['Kategori'] ?? '-') ?></td>
              <td class="wrap-col">
                <div class="alasan-cell-content">
                  <p class="truncate-text"><?= isset($row['Alasan']) ? htmlspecialchars($row['Alasan']) : '-' ?></p>
                </div>
              </td>
              <td class="text-center">
                <?php 
                  $url = '#';
                  if (isset($row['URL']) && !empty($row['URL'])) {
                      $url = $row['URL'];
                      $embed_url = str_replace('/view?usp=sharing', '/preview', $url);
                ?>
                  <a href="<?= htmlspecialchars($embed_url); ?>" class="view-document-btn" title="Lihat Dokumen" aria-label="Lihat dokumen"  style="color:var(--text);">
                    <i class="bi bi-file-earmark-text" style="font-size:18px;"></i>
                  </a>
                <?php } ?>
              </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div style="height:8px"></div>

  <!-- SO CANCEL -->
  <div class="table-card">
    <div class="section-title">DAFTAR SO CANCEL</div>
    <div class="section-note">SO yang dibatalkan dengan kondisi belum diproses lebih lanjut keseluruhan</div>
    <table id="SOCancel" class="display nowrap table-hover table-striped table-bordered" cellspacing="0" width="100%">
      <thead>
        <tr>
          <th class="text-center">Status</th>
          <th class="text-center">Cab</th>
          <th class="text-center">Tanggal</th>
          <th class="text-center">No SO</th>
          <th class="text-center">Customer</th>
          <th class="text-center">Kode Produk</th>
          <th class="text-center">Qty Pesan</th>
          <th class="text-center">Qty Batal</th>
          <th class="text-center">Tanggal<br/>Close/Cancel</th>
          <th class="text-center">Kategori</th>
          <th class="text-center">Alasan</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($data_cancel as $row):
            $tgldoc = new DateTime(date('d-m-Y',strtotime(substr(isset($row['TglDoc']) ? $row['TglDoc'] : '-',0,10))));
            $tglclose = new DateTime(date('d-m-Y',strtotime(substr(isset($row['TglClose']) ? $row['TglClose'] : '-',0,10))));
        ?>
          <tr data-docnum="<?= htmlspecialchars($row['DocNum'] ?? '') ?>" class="grouped-row">
              <td class="text-center"><?= htmlspecialchars($row['SttSO'] ?? '-') ?></td>
              <td class="text-center"><?= htmlspecialchars($row['Cab'] ?? '-') ?></td>
              <td class="text-center"><?= $tgldoc->format('d-m-Y') ?></td>
              <td class="text-center"><?= htmlspecialchars($row['NoDoc'] ?? '-') ?></td>
              <td class="text-center"><?= htmlspecialchars($row['KodeCust'] ?? '-') ?></td>
              <td><?= htmlspecialchars($row['KodeProd'] ?? '-') ?></td>
              <td><?= isset($row['QtyPesan']) ? number_format($row['QtyPesan'],2,',','.') : '0' ?></td>
              <td><?= isset($row['QtyBatal']) ? number_format($row['QtyBatal'],2,',','.') : '0' ?></td>
              <td><?= $tglclose->format('d-m-Y') ?></td>
              <td class="text-center"><?= htmlspecialchars($row['Kategori'] ?? '-') ?></td>
              <td class="wrap-col">
                <div class="alasan-cell-content">
                  <p class="truncate-text"><?= isset($row['Alasan']) ? htmlspecialchars($row['Alasan']) : '-' ?></p>
                </div>
              </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

</div> <!-- wrap -->

<!-- MODAL (single markup, controlled through JS) -->
<div id="modalOverlay" class="modal-overlay" aria-hidden="true" role="dialog" aria-modal="true" aria-label="Preview / Detail">
  <div id="detailModal" class="modal" role="document" aria-labelledby="modalTitle">
    <div class="modal-header">
      <div>
        <h2 id="modalTitle" style="margin:0; font-size:18px;">Dokumen / Preview</h2>
        <div id="modalSubtitle" style="font-size:13px;color:var(--muted); margin-top:4px;"></div>
      </div>
      <button id="closeModal" class="close-inline" aria-label="Tutup modal">Tutup ✕</button>
    </div>
    <div id="modalContent" class="modal-body" style="margin-top:12px;">&nbsp;</div>
  </div>
</div>

<script>
/* ====== Prepare JS data from PHP ====== */
var datatampil = <?php echo json_encode($datatampil ?? []); ?>;
var branchStats = <?php echo json_encode($branch_stats ?? []); ?>;

$(document).ready(function(){

  /* ===== Chart: percabang ===== */
  (function renderBranchChart(){
    try {
      var labels = branchStats.map(function(x){ return x[0]; });
      var values = branchStats.map(function(x){ return x[1]*1; });
      var ctx = document.getElementById('branchChart') ? document.getElementById('branchChart').getContext('2d') : null;
      if(!ctx) return;
      // create simple pie chart
      var palette = [
        '#2E8F66','#85A98F','#0EA5E9','#F59E0B','#E11D48','#7C3AED','#06B6D4','#F97316','#10B981','#EF4444'
      ];
      var bg = values.map(function(_,i){ return palette[i % palette.length]; });
      new Chart(ctx, {
        type: 'pie',
        data: {
          labels: labels,
          datasets: [{
            data: values,
            backgroundColor: bg,
            borderWidth: 0
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { position: 'bottom', labels: { boxWidth:10, padding:8 } },
            tooltip: { callbacks: { label: function(ctx){ return ctx.label + ': ' + ctx.parsed + ' SO'; } } }
          }
        }
      });
    } catch (e) {
      console.warn('branch chart failed', e);
    }
  })();

  /* ===== Modal helpers (centralized) ===== */
  function openModal(htmlContent, options) {
    options = options || {};
    $('#modalContent').html(htmlContent || '');
    if (options.title) $('#modalTitle').text(options.title);
    if (options.subtitle) $('#modalSubtitle').text(options.subtitle);
    $('#modalOverlay').addClass('show').attr('aria-hidden','false');
    $('#closeModal').focus();
    $('body').css('overflow','hidden');
  }

  function openIframeModal(url, options) {
    options = options || {};
    if (!url) return;
    var embed = url;
    // transform common google drive share link if needed
    if (embed.indexOf('/view?usp=sharing') !== -1) embed = embed.replace('/view?usp=sharing','/preview');
    var content = '';
    if (embed.match(/\.(jpeg|jpg|gif|png|webp)$/i)) {
      content = '<img src="'+embed+'" alt="dokumen">';
    } else {
      content = '<iframe src="'+embed+'" title="Dokumen preview"></iframe>';
    }
    openModal(content, options);
  }

  function closeModal() {
    $('#modalOverlay').removeClass('show').attr('aria-hidden','true');
    setTimeout(function(){
      $('#modalContent').html('');
      $('#modalSubtitle').text('');
      $('body').css('overflow','');
    }, 200);
  }

  $('#closeModal').off('click').on('click', function(e){ e.preventDefault(); closeModal(); });
  $('#modalOverlay').off('click').on('click', function(e){ if (e.target === this) closeModal(); });
  $(document).off('keydown.modal').on('keydown.modal', function(e){ if (e.key === 'Escape' && $('#modalOverlay').hasClass('show')) closeModal(); });

  /* ===== read-more logic for alasan cells ===== */
  function ensureReadMore(tableSelector){
    $(tableSelector + ' tbody tr td .alasan-cell-content').each(function(){
      var $p = $(this).find('.truncate-text');
      if ($(this).find('.read-more-btn').length) return;
      if ($p[0] && $p[0].scrollHeight > $p[0].clientHeight + 2) {
        $(this).append('<a href="#" class="read-more-btn">>>></a>');
      }
    });
  }
  $(document).on('click', '.read-more-btn', function(e){
    e.preventDefault();
    var $p = $(this).siblings('.truncate-text');
    $p.toggleClass('is-expanded');
    $(this).text($p.hasClass('is-expanded') ? '<<<' : '>>>' );
  });

  /* ===== mark rows with QtyBatal > 0 ===== */
  function markQtyBatal(tableSelector, colIndex){
    $(tableSelector).find('tbody tr').each(function(){
      var $tr = $(this);
      var valRaw = $tr.find('td').eq(colIndex).text().replace(/\./g,'').replace(',','.').trim();
      var val = parseFloat(valRaw) || 0;
      if (val > 0) $tr.addClass('has-batal'); else $tr.removeClass('has-batal');
    });
  }

  /* ===== DataTables init ===== */
  var SOClose = $('#SOClose').DataTable({
    responsive: true,
    dom: 'Bfrtip',
    buttons: [{ extend:'excelHtml5', text: '<i class="bi bi-file-earmark-arrow-down"></i> Excel', className:'dt-button', filename: 'Daftar_SO_Status_Close - <?php echo addslashes($periode_file ?? 'periode'); ?>' }],
    order: [[8, "desc"]],
    columnDefs: [
      { orderable: true, targets: [1,3,4,5,6,8] },
      { responsivePriority: 1, targets: [1,8,9,10] },
      { responsivePriority: 2, targets: [2,4,6] },
      { responsivePriority: 3, targets: [0,3,5,7] }
    ],
    columns: [{width:"5%"},{width:"3%"},{width:"5%"},{width:"5%"},{width:"8%"},{width:"7%"},{width:"7%"},{width:"7%"},{width:"5%"},{width:"8%"},{width:"40%", className:"wrap-col"},{width:"2%"}],
    autoWidth:false,
    drawCallback: function(){
      ensureReadMore('#SOClose');
      markQtyBatal('#SOClose', 7);
      /* pastikan tombol tetap berada di kiri setelah draw */
      $('.dataTables_wrapper .dt-buttons').css({'display':'inline-flex','float':'left'});
    }
  });

  var SOCancel = $('#SOCancel').DataTable({
    responsive: true,
    dom: 'Bfrtip',
    buttons: [{ extend:'excelHtml5', text: '<i class="bi bi-file-earmark-arrow-down"></i> Excel', className:'dt-button', filename: 'Lap_SO_Status_Cancel - <?php echo addslashes($periode_file ?? 'periode'); ?>' }],
    order: [[8, "desc"]],
    columnDefs: [
      { orderable: false, targets: [1,3,4,5,6,8] },
      { responsivePriority: 1, targets: [1,8,9,10] },
      { responsivePriority: 2, targets: [2,4] },
      { responsivePriority: 3, targets: [0,3,5,7,6] }
    ],
    columns: [{width:"5%"},{width:"3%"},{width:"5%"},{width:"5%"},{width:"8%"},{width:"7%"},{width:"7%"},{width:"7%"},{width:"5%"},{width:"8%"},{width:"30%", className:"wrap-col"}],
    autoWidth:false,
    drawCallback: function(){
      ensureReadMore('#SOCancel');
      markQtyBatal('#SOCancel', 7);
      $('.dataTables_wrapper .dt-buttons').css({'display':'inline-flex','float':'left'});
    }
  });

  /* ===== view document click handler (uses modal helpers) ===== */
  $('#SOClose tbody').on('click', '.view-document-btn', function(e){
    e.preventDefault();
    var url = $(this).attr('href');
    if (!url || url === '#') return;
    $('#modalContent').html('<div style="padding:16px;text-align:center;color:var(--muted)">Memuat dokumen...</div>');
    // Prefer embed preview
    var embed = url;
    if (embed.indexOf('/view?usp=sharing') !== -1) embed = embed.replace('/view?usp=sharing','/preview');
    openIframeModal(embed, { title: 'Preview Dokumen' });
  });

  /* ===== grouped-row click: show detail modal ===== */
  $('.grouped-row').on('click', function(e){
    // ignore clicks coming from the doc button (so opening doc doesn't open details too)
    if ($(e.target).closest('.view-document-btn').length) return;
    var docNum = $(this).data('docnum') || $(this).data('DocNum') || $(this).attr('data-docnum');
    if (!docNum) return;
    var filtered = datatampil.filter(function(x){ return (x.DocNum+'' )=== (docNum+''); });
    if (!filtered.length) {
      // minimal fallback: show docnum only
      openModal('<div style="padding:12px;">Tidak ada detail lebih lanjut.<div style="margin-top:8px;color:var(--muted)">DocNum: '+docNum+'</div></div>', { title: docNum });
      return;
    }

    var first = filtered[0];
    var totalQty = 0, totalOpenQty = 0, totalAmount = 0;
    var rowsHtml = '';
    filtered.forEach(function(r){
      var q = parseFloat(r.Quantity) || 0;
      var oq = parseFloat(r.OpenQty) || 0;
      var t = parseFloat(r.Total) || 0;
      totalQty += q; totalOpenQty += oq; totalAmount += t;
      rowsHtml += '<tr><td><strong>'+ (r.ItemCode || '-') +'</strong><br><span style="color:var(--muted);font-size:12px;">'+ (r.Dscription || '-') +'</span></td>'
               + '<td class="text-center">'+ (r.Divisi || '-') +'</td>'
               + '<td class="text-center">'+ (r.Fokus || '-') +'</td>'
               + '<td class="text-right">'+ q.toLocaleString('id-ID') +'</td>'
               + '<td class="text-right">'+ oq.toLocaleString('id-ID') +'</td>'
               + '<td class="text-right"><strong>'+ (t>0? (t/1000000).toLocaleString('id-ID',{style:'currency',currency:'IDR'}) : '-') +'</strong></td></tr>';
    });

    var detailContent = '<div><div style="margin-bottom:8px;"><div style="font-weight:700;">Sales Order Detail</div><div style="color:var(--muted);">'+first.CardName+'</div></div>'
      + '<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:8px;margin-bottom:8px;">'
      + '<div style="font-size:12px;color:var(--muted)"><small>Tanggal SO</small><div>'+ (first.DocDate ? new Date(first.DocDate).toLocaleDateString('id-ID') : '-') +'</div></div>'
      + '<div style="font-size:12px;color:var(--muted)"><small>Salesman</small><div>'+ (first.SlpName || '-') +'</div></div>'
      + '<div style="font-size:12px;color:var(--muted)"><small>Cabang</small><div>'+ (first.CabCode || '-') +'</div></div>'
      + '<div style="font-size:12px;color:var(--muted)"><small>Divisi</small><div>'+ (first.Divisi || '-') +'</div></div>'
      + '</div>'
      + '<table class="detail-table"><thead><tr><th>Produk</th><th class="text-center">Div</th><th class="text-center">Fokus</th><th class="text-right">Qty</th><th class="text-right">Open Qty</th><th class="text-right">Jumlah</th></tr></thead>'
      + '<tbody>' + rowsHtml + '</tbody>'
      + '<tfoot><tr><td colspan="3" class="total-label">Total</td>'
      + '<td class="text-right">'+ totalQty.toLocaleString('id-ID') +'</td>'
      + '<td class="text-right">'+ totalOpenQty.toLocaleString('id-ID') +'</td>'
      + '<td class="text-right total-amount">'+ (totalAmount>0? (totalAmount/1000000).toLocaleString('id-ID',{style:'currency',currency:'IDR'}) : '-') +'</td></tr></tfoot></table></div>';

    openModal(detailContent, { title: docNum, subtitle: first.CardName || '' });
  });

  /* ===== density chip ===== */
  (function(){
    var chip = $('#densityChip');
    var states = [
      {name:'Compact', padding:'4px 6px'},
      {name:'Medium', padding:'6px 8px'},
      {name:'Comfort', padding:'8px 10px'}
    ];
    var i = 1;
    chip.on('click', function(){
      i = (i+1) % states.length;
      var s = states[i];
      $('thead th').css('padding', s.padding);
      $('tbody td').css('padding', s.padding);
      chip.text('Density: ' + s.name);
      SOClose.columns.adjust();
      SOCancel.columns.adjust();
    });
  })();

  /* ===== theme toggle ===== */
  (function(){
    var key = 'uitemp';
    var root = document.documentElement;
    var saved = localStorage.getItem(key);
    if (saved) root.setAttribute('data-theme', saved);
    $('#themeToggle').on('click', function(){
      var current = root.getAttribute('data-theme');
      var next = current === 'dark' ? 'light' : 'dark';
      root.setAttribute('data-theme', next);
      localStorage.setItem(key, next);
      $(this).text(next === 'dark' ? '☀️ Light' : '🌙 Dark');
    });
    $('#themeToggle').text(root.getAttribute('data-theme') === 'dark' ? '☀️ Light' : '🌙 Dark');
  })();

  // initial draw tweaks
  setTimeout(function(){ $('#SOClose').trigger('draw.dt'); $('#SOCancel').trigger('draw.dt'); }, 250);
});
</script>

</body>
</html>
