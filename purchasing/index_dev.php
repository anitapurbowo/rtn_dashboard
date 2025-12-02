<?php
date_default_timezone_set('Asia/Jakarta');
$_dashpr = $_GET['dashpr'] ?? '0';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Daftar PR Terbuka yang Belum Terpenuhi">
    <link rel="icon" href="../logo.ico" type="image/x-icon">
    <title>Dashboard Purchase Request</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --color-primary: #1abc9c; --color-primary-dark: #16a085; --color-secondary: #2ecc71;
            --color-background: #ecf0f1; --color-surface: #ffffff; --color-text-dark: #2c3e50;
            --color-text-light: #7f8c8d; --color-border: #dfe6e9; --color-pending: #bdc3c7;
            --color-lunas: #27ae60; --color-belum-lunas: #f39c12; --color-menunggu: #3498db;
            --color-menunggu-po: #89AC46; --color-po-sebagian: #343131;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--color-background);
            color: var(--color-text-dark);
            padding: 8px; /* Mengurangi padding body lebih lanjut */
        }
        
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--color-surface);
            color: var(--color-text-dark);
            padding: 12px 25px; /* Mengurangi padding vertikal header */
            margin-bottom: 10px; /* Mengurangi margin bawah header lebih lanjut */
            border-radius: 12px;
            border-left: 5px solid var(--color-primary);
            box-shadow: 0 5px 25px rgba(0,0,0,0.07);
        }
        .header-left {
            display: flex;
            align-items: center;
        }
        .header-icon {
            width: 32px;
            height: 32px;
            margin-right: 15px;
            fill: var(--color-primary);
        }
        .dashboard-header h1 {
            margin: 0;
            font-size: 20px;
            font-weight: 600;
        }
        .header-right {
            display: flex;
            gap: 25px;
            text-align: right;
        }
        .stat-value {
            font-size: 18px;
            font-weight: 600;
            display: block;
        }
        .stat-label {
            font-size: 12px;
            color: var(--color-text-light);
            display: block;
        }
        
        .table-card {
            background: var(--color-surface); padding: 15px; border-radius: 12px; /* Mengurangi padding card tabel */
            box-shadow: 0 5px 25px rgba(0,0,0,0.08);
        }
        table.dataTable thead th {
            background-color: var(--color-primary) !important;
            color: white; font-weight: 400;
            border-bottom: 2px solid var(--color-primary-dark);
            font-size: 13px;
        }
        table.dataTable tbody td {
            font-size: 12px;
            vertical-align: middle;
        }
        td.details-control {
            background: url('https://datatables.net/examples/resources/details_open.png') no-repeat center center;
            cursor: pointer;
            width: 20px !important;
        }
        tr.dt-hasChild td.details-control {
            background: url('https://datatables.net/examples/resources/details_close.png') no-repeat center center;
        }
        .status-badge {
            padding: 5px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; color: white;
            display: inline-flex;   
            align-items: center;    
            justify-content: center; 
            min-height: 20px;     
            line-height: 1.4; 
            text-align: center;  
        }
        .status-lunas { background-color: var(--color-lunas);  }
        .status-belum-lunas { background-color: var(--color-belum-lunas);  font-size: 10px;}
        .status-menunggu { background-color: var(--color-menunggu); font-size: 10px;}
        .status-netral { background-color: var(--color-pending); }
        .status-menunggu-po { background-color: var(--color-menunggu-po); font-size: 10px;}
        .status-po-sebagian { background-color: var(--color-po-sebagian); font-size: 10px;}
        .timeline-wrapper { padding: 20px 15px; background-color: #f9fbfd; border-top: 1px solid var(--color-border); }
        .timeline-track { display: flex; justify-content: space-between; position: relative; padding: 0 10px; }
        .timeline-track::before {
            content: ''; position: absolute; top: 10px; left: 5%; right: 5%;
            height: 4px; background-color: var(--color-border); z-index: 1;
        }
        .timeline-milestone { position: relative; z-index: 2; text-align: center; width: 18%; }
        .milestone-icon {
            width: 24px; height: 24px; border-radius: 50%; background: var(--color-surface);
            border: 4px solid var(--color-pending); margin: 0 auto; color: var(--color-pending);
            font-weight: bold; line-height: 18px;
        }
        .timeline-milestone.completed .milestone-icon {
            border-color: var(--color-primary); background-color: var(--color-primary); color: white;
        }
        .milestone-info { margin-top: 10px; }
        .milestone-label { font-size: 13px; font-weight: 500; }
        .milestone-date { font-size: 12px; color: var(--color-text-light); }
        .milestone-doc { font-size: 11px; color: var(--color-primary); font-weight: 600; }
        .milestone-qty {
            font-size: 12px; font-weight: 500; color: #555; margin-top: 5px;
            padding: 3px 8px; background-color: #e9ecef; border-radius: 5px;
            display: inline-block;
        }

        .stat-card {
            padding: 15px;    border-radius: 8px;   color: white;   text-align: center;
            cursor: pointer;  transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
            height: 100%;  display: flex;  flex-direction: column;   justify-content: center;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.15);
        }

        .stat-card-title {
            font-size: 16px;
            font-weight: 600;
            opacity: 0.9;
        }

        .stat-card-title2 {
            font-size: 14px;
            font-weight: 400;
            opacity: 0.9;
        }

        .stat-card-value {
            font-size: 36px;
            font-weight: 700;
            line-height: 1.2;
        }

        .stat-card-subtitle {
            font-size: 12px;
            opacity: 0.8;
        }

        .card-header {
            font-size: 14px;
            font-weight: 500;
            padding: 8px 15px;
        }

        .bg-total { background-color: #27ae60; } /* Hijau */
        .bg-total-exp { background-color: #404B69; } /* Abu */
        .bg-age { background-color: #BDB76B; } /* Olive Drap */
        .bg-aai { background-color: #2980b9; } /* Biru */
        .bg-exp { background-color: #f1c40f; } /* Kuning */
        .bg-import { background-color: #4CAF50; } /* Hijau Material */
        .bg-lokal { background-color: #FFC107; } /* Kuning Amber */
        .bg-sis { background-color: #64B5F6; } /* Biru Muda */
    </style>
</head>
<body>

<div class="container-fluid">
    <header class="dashboard-header">
        <div class="header-left">
            <svg class="header-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20M8,12H16V14H8V12M8,16H16V18H8V16Z" /></svg>
            <h1>Dashboard Purchase Request</h1>
        </div>
        <div class="header-right">
            <div class="stat">
                <span id="waktu-update" class="stat-value"></span>
                <span class="stat-label">Waktu Update</span>
            </div>
        </div>
    </header>
    
    <div class="alert alert-info d-flex align-items-center mb-2" role="alert">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-info-circle-fill flex-shrink-0 me-3" viewBox="0 0 16 16" role="img" aria-label="Info:">
            <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
        </svg>
        <div>
            <strong>Tips:</strong> Klik pada salah satu kartu di bawah ini untuk memfilter data pada tabel.
        </div>
    </div>
    <div class="row g-2 mb-2">
        <div class="col-lg-2 col-md-4">
            <div class="stat-card bg-total" data-category="">
                <div class="stat-card-title" style="font-size: 20px">Jumlah Item</div>
                <div class="stat-card-title2" style="font-size: 15px">Barang Dagang</div>
                <div class="stat-card-value" id="total-pr-count" style="font-size: 45px">0</div>
                <div class="stat-card-subtitle">Klik Untuk Reset Filter</div>
            </div>
        </div>

        <div class="col-lg-4 col-md-8">
            <div class="card">
                <div class="card-header">
                    Jumlah Item - Berdasarkan Divisi
                </div>
                <div class="card-body p-2">
                    <div class="row g-2">
                        <div class="col">
                            <div class="stat-card bg-age" data-category="AGE" data-column-index="5">
                                <div class="stat-card-title">AGE</div>
                                <div class="stat-card-value" id="age-count">0</div>
                                <div class="stat-card-subtitle" id="age-pr-count">0 PR</div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="stat-card bg-aai" data-category="AAI" data-column-index="5">
                                <div class="stat-card-title">AAI</div>
                                <div class="stat-card-value" id="aai-count">0</div>
                                <div class="stat-card-subtitle" id="aai-pr-count">0 PR</div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="stat-card bg-exp" data-category="EXP" data-column-index="5">
                                <div class="stat-card-title">EXP</div>
                                <div class="stat-card-value" id="exp-count">0</div>
                                <div class="stat-card-subtitle" id="exp-pr-count">0 PR</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-12">
            <div class="card">
                <div class="card-header">
                    Jumlah Item - Berdasarkan Tipe Vendor
                </div>
                <div class="card-body p-2">
                    <div class="row g-2">
                        <div class="col">
                            <div class="stat-card bg-import" data-category="Import" data-column-index="10">
                                <div class="stat-card-title">Import</div>
                                <div class="stat-card-value" id="import-count">0</div>
                                <div class="stat-card-subtitle" id="import-pr-count">0 PR</div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="stat-card bg-lokal" data-category="Lokal" data-column-index="10">
                                <div class="stat-card-title">Lokal</div>
                                <div class="stat-card-value" id="lokal-count">0</div>
                                <div class="stat-card-subtitle" id="lokal-pr-count">0 PR</div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="stat-card bg-sis" data-category="Sisco" data-column-index="10">
                                <div class="stat-card-title">Sisco</div>
                                <div class="stat-card-value" id="sis-count">0</div>
                                <div class="stat-card-subtitle" id="sis-pr-count">0 PR</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-2 col-md-4">
            <div id="expense-card" class="stat-card bg-total-exp" data-category="">
                <div class="stat-card-title" style="font-size: 20px">Jumlah Item</div>
                <div class="stat-card-title2" style="font-size: 15px">Non Barang Dagang</div>
                <div class="stat-card-value" id="expense-count" style="font-size: 40px">0</div>
                <div class="stat-card-subtitle" id="expense-pr-count">0 PR</div>
                <div class="stat-card-subtitle">Klik Untuk Melihat Data</div>
            </div>
        </div>
    </div>
    <div class="warning alert-info align-items-center mb-1">
        <div>
            <strong>Purchase Request Open (dengan Item Produk)</strong>
        </div>
    </div>
    <main class="table-card">
        <div class="table-responsive">
            <table id="prTable" class="table table-striped table-hover" style="width:100%">
                <thead>
                    <tr>
                        <th></th>
                        <th>No PR</th>
                        <th>Tgl PR</th>
                        <th>Kode Produk</th>
                        <th>Deskripsi</th>
                        <th>Kat</th>
                        <th>PR</th>
                        <th>PO</th>
                        <th>GRPO</th>
                        <th>Status</th>
                        <th>Vendor</th>
                        <th>ETD</th>
                        <th>ETA</th>
                    </tr>
                </thead>
                <tbody>
                   
                </tbody>
            </table>
        </div>
    </main>
    <br/>
    <div class="warning alert-info align-items-center mb-1">
        <div>
            <strong>Purchase Request Open (Expense Item)</strong>
        </div>
    </div>
    <main id="expense-table-container" class="table-card">
        <div class="table-responsive">
            <table id="prExpTable" class="table table-striped table-hover" style="width:100%">
                <thead>
                    <tr>
                        <th></th>
                        <th>No PR</th>
                        <th>Tgl PR</th>
                        <th>Deskripsi</th>
                        <th>Dept/Div</th>
                        <th>Cabang</th>
                        <th>Jumlah PR</th>
                        <th>Sisa PR</th>
                        <th>Usulan Vendor</th>
                        <th>ETA</th>
                        <th>Peminta</th>
                    </tr>
                </thead>
                <tbody>
                   
                </tbody>
            </table>
        </div>
    </main>
</div>

<!-- <script src="./../js/jquery-3.7.1.js"></script> -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.js"></script>

<script>
const dashprParam = '<?php echo $_dashpr; ?>';

function formatDetails(d) {
    function createMilestone(label, date, docNumber = '', quantityInfo = '') {
        var isCompleted = date && date.trim() !== '';

        let statusClass = 'pending';
        let icon = '●';

        const moneyIconSvg = `<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="5 5 16 16"><path d="M11 15a4 4 0 1 0 0-8 4 4 0 0 0 0 8zm5-4a5 5 0 1 1-10 0 5 5 0 0 1 10 0z"/><path d="M9.438 11.944c.047.596.518 1.06 1.363 1.116v.44h.375v-.443c.875-.061 1.386-.529 1.386-1.207 0-.618-.39-.935-1.355-1.213l-.761-.256A1.5 1.5 0 0 1 9.5 8.5V8c0-.549.486-1.06 1.372-1.114v-.447h-.375v.443c-.875.06-1.366.53-1.366 1.207 0 .618.39.935 1.355 1.213l.761.256a1.5 1.5 0 0 1 .498 1.128l-.01.009zm-2.136.085A1.5 1.5 0 0 1 7.5 10.5V10c0-.549.486-1.06 1.372-1.114v-.447h-.375v.443c-.875.06-1.366.53-1.366 1.207 0 .618.39.935 1.355 1.213l.761.256a1.5 1.5 0 0 1 .498 1.128l-.01.009zM1.372 4.114A1.5 1.5 0 0 1 2.5 5.5V6c0 .549-.486 1.06-1.372 1.114v.447h.375v-.443c.875-.061 1.366-.53 1.366-1.207 0-.618-.39-.935-1.355-1.213l-.761-.256A1.5 1.5 0 0 1 1.5 3.5V3c0-.549.486-1.06 1.372-1.114v-.447h-.375v.443c-.875.06-1.366.53-1.366 1.207z"/></svg>`;

        if (label === 'Invoice Diterima') {
            const poQty = parseFloat(d['TotalQtyPO']) || 0;
            const apQty = parseFloat(d['TotalQtyAP']) || 0; 
            isCompleted = (date && date.trim() !== '') && (poQty > 0 && poQty === apQty);
            statusClass = isCompleted ? 'completed' : 'pending';
            icon = isCompleted ? '✓' : '●';
        } else if (label === 'Pembayaran') {
            const hasPayment = date && date.trim() !== '';
            if (hasPayment) {
                statusClass = 'completed'; 
                icon = moneyIconSvg;       
            } else {
                statusClass = 'pending';
                icon = '●';
            }
        } else { // Logic for all other milestones
            isCompleted = date && date.trim() !== '';
            statusClass = isCompleted ? 'completed' : 'pending';
            icon = isCompleted ? '✓' : '●';
        }

        let dateFormatted = 'Menunggu';
        //  if (label === 'Invoice Diterima') {
        //     const poQty = parseFloat(d['TotalQtyPO']) || 0;
        //     const apQty = parseFloat(d['TotalQtyAP']) || 0; 

        //     isCompleted = (date && date.trim() !== '') && (poQty > 0 && poQty === apQty);
        // }

        // const statusClass = isCompleted ? 'completed' : 'pending';
        // const icon = isCompleted ? '✓' : '●';
        // let dateFormatted = 'Menunggu';
        if(isCompleted) {
            const dateObj = new Date(date);
            const options = { day: 'numeric', month: 'short', year: 'numeric' };
            dateFormatted = dateObj.toLocaleDateString('id-ID', options);
        }
        const docNumberHtml = docNumber && docNumber.trim() !== '' ? `<div class="milestone-doc">${docNumber}</div>` : "";
        return `
        <div class="timeline-milestone ${statusClass}">
            <div class="milestone-icon">${icon}</div>
            <div class="milestone-info">
                <div class="milestone-label">${label}</div>
                <div class="milestone-date">${dateFormatted}</div>
                ${docNumberHtml}
                ${quantityInfo} 
            </div>
        </div>`;
    }
    const qtyPrHtml = d['QtyPR'] ? `<div class="milestone-qty">Qty: ${parseInt(d['QtyPR'])}</div>` : '';
    const qtyPoHtml = d['TotalQtyPO'] ? `<div class="milestone-qty">Qty: ${parseInt(d['TotalQtyPO'])}</div>` : '';
    const qtyGrpoHtml = d['TotalQtyGRPO'] ? `<div class="milestone-qty">Qty Diterima: ${parseInt(d['TotalQtyGRPO'])}</div>` : '';
    const qtyApHtml = d['TotalQtyAP'] ? `<div class="milestone-qty">Qty Invoice: ${parseInt(d['TotalQtyAP'])}</div>` : '';

    return `
    <div class="timeline-wrapper">
        <div><strong>Requestor:</strong> ${d['Requester']}</div>
        <br/>
        <div class="timeline-track">
            ${createMilestone('PR Dibuat', d['TglPR'], '', qtyPrHtml)}
            ${createMilestone('PO Dibuat', d['TglPO'], d['NoPO'], qtyPoHtml)}
            ${createMilestone('Barang Diterima', d['TglGRPO'], d['NoGRPO'], qtyGrpoHtml)}
            ${createMilestone('Invoice Diterima', d['TglAP_DP_Terakhir'], d['NoAP_dan_DP'], qtyApHtml )}
            ${createMilestone('Pembayaran', d['TglBayar_Terakhir'])}
        </div>
    </div>`;
}

$(function () {
    let initialPageLength = 10; 
    if (dashprParam === '1') {
        initialPageLength = 30; 
    }

    var table = $('#prTable').DataTable({
        "pageLength": initialPageLength,
        lengthMenu: [ [10, 30, 50, 100, -1], [ '10', '30', '50', '100', 'Semua' ] ],
        ajax: {
            url: 'getPrNotYetFullFill.php', 
            data: function(d) {
                d.timestamp = new Date().getTime(); 
            },
            dataSrc: 'data'
        },
        columns: [
            {
                className: 'details-control',
                orderable: false,
                data: null,
                defaultContent: '', // Kolom pertama untuk tombol, tidak ada data
                "width": "1%","searchable": false
            },
            { data: 'NoPR', "width": "8%", "className": "text-center"},
            { data: 'TglPR', "width": "9%", "className": "text-center" },
            { data: 'ItemCode', "width": "12%" },
            { data: 'Deskripsi', "width": "30%" },
            { data: 'Kategori', "width": "8%" },
            { data: 'QtyPR', "width": "6%", className: 'text-center' ,
                render: function(data, type, row) {
                    return data ? parseInt(data) : 0;
                }
            },
            { data: 'TotalQtyPO', "width": "6%", className: 'text-center',
                render: function(data, type, row) {
                    return data ? parseInt(data) : 0;
                }
            },
            { data: 'TotalQtyGRPO', "width": "6%", className: 'text-center' ,
                render: function(data, type, row) {
                    return data ? parseInt(data) : 0;
                }
            },
            { 
                data: 'StatusPelunasan',
                "width": "10%" ,
                // Render custom untuk membuat badge status
                render: function(data, type, row) {
                    let statusClass = '';
                    let statusClean = (data || '').toString().toUpperCase().trim();
                    if (statusClean == 'ADA PEMBAYARAN') statusClass = 'status-lunas';
                    else if (statusClean == 'BELUM LUNAS') statusClass = 'status-belum-lunas';
                    else if (statusClean == 'MENUNGGU BARANG') statusClass = 'status-menunggu';
                    else if (statusClean == 'MENUNGGU PO') statusClass = 'status-menunggu-po';
                    else if (statusClean == 'PO SEBAGIAN') statusClass = 'status-po-sebagian';
                    else statusClass = 'status-netral';
                    
                    return `<span class="status-badge ${statusClass}">${data || 'N/A'}</span>`;
                }
            },
            { data: 'Vendor', "width": "10%" },
            { data: 'ETD', "width": "10%" },
            { data: 'ETA', "width": "10%" }
        ],
        drawCallback: function(settings) {
            var api = this.api();
        
            const now = new Date();
            const timeString = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            const dateString = now.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
            $('#waktu-update').text(`${dateString}, ${timeString}`);

            let totalPr = 0;
            let countAGE = 0, countAAI = 0, countEXP = 0;
            let countImport = 0, countLokal = 0, countSisco = 0;

            let agePrSet = new Set();
            let aaiPrSet = new Set();
            let expPrSet = new Set();
            let importPrSet = new Set();
            let lokalPrSet = new Set();
            let siscoPrSet = new Set();
            
            api.data().each(function(row) {
                totalPr++; 
                const kategori = (row.Kategori || '').toUpperCase();
                const vendor = (row.Vendor || '').toUpperCase();
                
                 if (kategori === 'AGE') {
                    countAGE++;
                    agePrSet.add(row.NoPR); 
                }
                if (kategori === 'AAI') {
                    countAAI++;
                    aaiPrSet.add(row.NoPR);
                }
                if (kategori === 'EXP') {
                    countEXP++;
                    expPrSet.add(row.NoPR);
                }
                if (vendor === 'IMPORT') {
                    countImport++;
                    importPrSet.add(row.NoPR);
                }
                if (vendor === 'LOKAL') {
                    countLokal++;
                    lokalPrSet.add(row.NoPR);
                }
                if (vendor === 'SISCO') {
                    countSisco++;
                    siscoPrSet.add(row.NoPR);
                }
            });

            $('#total-pr-count').text(totalPr.toLocaleString('id-ID'));
            $('#age-count').text(countAGE.toLocaleString('id-ID'));
            $('#age-pr-count').text(agePrSet.size.toLocaleString('id-ID') + ' PR');
            $('#aai-count').text(countAAI.toLocaleString('id-ID'));
            $('#aai-pr-count').text(aaiPrSet.size.toLocaleString('id-ID') + ' PR');
            $('#exp-count').text(countEXP.toLocaleString('id-ID'));
            $('#exp-pr-count').text(expPrSet.size.toLocaleString('id-ID') + ' PR');
            $('#import-count').text(countImport.toLocaleString('id-ID'));
            $('#import-pr-count').text(importPrSet.size.toLocaleString('id-ID') + ' PR');
            $('#lokal-count').text(countLokal.toLocaleString('id-ID'));
            $('#lokal-pr-count').text(lokalPrSet.size.toLocaleString('id-ID') + ' PR');
            $('#sis-count').text(countSisco.toLocaleString('id-ID'));
            $('#sis-pr-count').text(siscoPrSet.size.toLocaleString('id-ID') + ' PR');
        },
        order: [[2, 'asc']], // Urutan default
        pagingType: 'full_numbers',
        language: {
            url: 'https://cdn.datatables.net/plug-ins/2.0.8/i18n/id.json',
            paginate: { first: '<<', previous: '<', next: '>', last: '>>' }
        },
        autoWidth: false, 
    });

    var Exptable = $('#prExpTable').DataTable({
        "pageLength": initialPageLength,
        lengthMenu: [ [10, 30, 50, 100, -1], [ '10', '30', '50', '100', 'Semua' ] ],
        ajax: {
            url: 'getPRExpOpen.php', 
            data: function(d) {
                d.timestamp = new Date().getTime(); 
            },
            dataSrc: 'data'
        },
        columns: [
            {
                className: 'details-control',
                orderable: false,
                data: null,
                defaultContent: '', 
                "width": "1%","searchable": false
            },
            { data: 'NoPR', "width": "8%", "className": "text-center"},
            { data: 'Tanggal', "width": "7%", "className": "text-center" },
            { data: 'Deskripsi', "width": "20%" },
            { data: 'OcrCode', "width": "8%", "className": "text-center" },
            { data: 'OcrCode2', "width": "8%", "className": "text-center" },
            { data: 'QtyPR', "width": "6%", className: 'text-center' ,
                render: function(data, type, row) {
                    return data ? parseInt(data) : 0;
                }
            },
            { data: 'OpenQty', "width": "6%", className: 'text-center' ,
                render: function(data, type, row) {
                    return data ? parseInt(data) : 0;
                }
            },
            { data: 'Vendor', "width": "20%" },
            { data: 'ETA', "width": "10%" },
            { data: 'ReqName', "width": "8%"}
        ],
        drawCallback: function(settings) {
            var api = this.api();
        
            const now = new Date();
            const timeString = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            const dateString = now.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
            $('#waktu-update').text(`${dateString}, ${timeString}`);

            let totalPrOpen = 0;
            let totalPrSet = new Set();
            
            api.data().each(function(row) {
                totalPrOpen++; 
                totalPrSet.add(row.NoPR)
            });

            $('#expense-count').text(totalPrOpen.toLocaleString('id-ID'));
            $('#expense-pr-count').text(totalPrSet.size.toLocaleString('id-ID') + ' PR');
        },
        order: [[2, 'asc']], // Urutan default
        pagingType: 'full_numbers',
        language: {
            url: 'https://cdn.datatables.net/plug-ins/2.0.8/i18n/id.json',
            paginate: { first: '<<', previous: '<', next: '>', last: '>>' }
        },
        autoWidth: false, 
    });
    
    setInterval(function() {
        table.ajax.reload(null, false);
    }, 300000); 

    $('#prTable tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = table.row(tr);
        if (row.child.isShown()) {
            row.child.hide();
        } else {
            row.child(formatDetails(row.data())).show(); 
        }
    });

    $('.stat-card').on('click', function() {
        const category = $(this).data('category');
        const columnIndex = $(this).data('column-index');

        const KATEGORI_COL = 5;
        const VENDOR_COL = 10;
        
        const searchTerm = category ? '^' + category + '$' : '';

        if (category === '') {
            table.columns([KATEGORI_COL, VENDOR_COL]).search('').draw();
            return;
        }

        const currentSearch = table.column(columnIndex).search();      
        table.columns([KATEGORI_COL, VENDOR_COL]).search('');
        if (currentSearch !== searchTerm) {
            table.column(columnIndex).search(searchTerm, true, false).draw();
        } else {
            table.draw();
        }
    });

    $('#expense-card').on('click', function(event) {
        event.preventDefault(); 

        const targetElement = document.getElementById('expense-table-container');
        
        if (targetElement) {
            targetElement.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});
</script>

</body>
</html>

