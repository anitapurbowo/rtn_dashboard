<?php
    $getTampil = isset($_GET['tampil']) ? strtolower($_GET['tampil']) : 'all';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Analisa Penjualan</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/rowgroup/1.4.0/css/rowGroup.dataTables.min.css">

    <style>
        :root {
            --color-primary: #4f46e5; --color-background: #E9EFEC; --color-surface: #ffffff;
            --color-text-primary: #1f2937; --color-text-secondary: #6b7280; --color-border: #e5e7eb;
            --color-success: #10b981; --color-warning: #f59e0b; --color-danger: #ef4444;
            --color-card-subtitle: #EE4E4E; --color-gray: #f3f4f6;
        }
        body { font-family: 'Inter', sans-serif; background-color: var(--color-background); color: var(--color-text-primary); margin: 0; padding: 24px; }
        .dashboard-container { max-width: 1600px; margin: auto; }
        .dashboard-grid { display: grid; grid-template-columns: repeat(12, 1fr); gap: 16px; }
        .card { background-color: var(--color-surface); border-radius: 12px; padding: 16px; border: 1px solid var(--color-border); box-shadow: 0 1px 3px 0 rgba(0,0,0,0.07), 0 1px 2px -1px rgba(0,0,0,0.07); }
        .chart-card { grid-column: span 12; }
        .table-card { grid-column: span 12; }
        @media (min-width: 1280px) {
            .table-divisi { grid-column: span 5; }
            .table-cabang { grid-column: span 7; }
        }
        h1 { font-size: 28px; font-weight: 800; margin: 0 0 8px 0; }

        /* [BAGIAN 1] CSS PENTING UNTUK LAYOUT KIRI-KANAN */
        .chart-wrapper {
            display: flex;
            flex-direction: column; /* Default untuk mobile */
            gap: 24px;
        }
        @media (min-width: 1024px) {
            .chart-wrapper {
                flex-direction: row; /* Berdampingan di desktop */
            }
        }
        #mainChart {
            flex-grow: 1;
            min-width: 0; /* Penting agar chart tidak 'mendorong' layout */
        }
        .chart-info-panel {
            flex-basis: 300px; /* Lebar tetap untuk panel info */
            flex-shrink: 0;
            border-left: 1px solid var(--color-border);
            padding-left: 24px;
        }
        .chart-info-panel h4 { margin: 0 0 16px 0; font-size: 18px; font-weight: 600; }
        .info-item { display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid var(--color-border); }
        .info-item span { color: var(--color-text-secondary); font-size: 14px; }
        .info-item strong { font-size: 20px; font-weight: 700; color: var(--color-primary); }
        .info-item:first-of-type strong { color: var(--color-text-secondary); }
        .chart-info-panel h5 { font-size: 12px; text-transform: uppercase; color: var(--color-text-secondary); margin: 16px 0 8px 0; }
        .info-item-sm { display: flex; justify-content: space-between; font-size: 13px; padding: 6px 0; }
        .info-item-sm span { color: var(--color-text-secondary); }
        .info-item-sm strong { font-weight: 600; }

        .card-header { display: flex; justify-content: space-between; align-items: center; padding-bottom: 8px; margin-bottom: 4px; border-bottom: 1px solid var(--color-border); }
        .card-header h3 { margin: 0; padding: 0; font-size: 18px; font-weight: 600; }
        .card-subtitle { display: inline-flex; align-items: center; gap: 6px; background-color: var(--color-gray); color: var(--color-text-subtitle); padding: 4px 10px; border-radius: 999px; font-size: 12px; font-weight: 500; margin: 0; }
        .card-subtitle svg { flex-shrink: 0; }
        
        table.dataTable thead th, table.dataTable tfoot th { background-color: #AEC8A4; color: var(--color-text-primary); font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--color-border) !important; text-align: left; padding: 12px 10px; }
        table.dataTable tbody td { padding: 6px 6px; vertical-align: middle; border: none; border-bottom: 1px solid var(--color-border); }
        table.dataTable tbody tr { border: none; font-size: 13px; }
        table.dataTable tbody tr:nth-child(even) { background-color: transparent; }
        table.dataTable tbody tr:last-child td { border-bottom: none; }
        table.dataTable tbody tr:not(.dtrg-group):hover { background-color: #eff6ff; cursor: pointer; }
        table.dataTable td.text-right, table.dataTable th.text-right, table.dataTable tfoot th.text-right { text-align: right; font-feature-settings: 'tnum'; font-weight: 500; }
        table.dataTable td:first-child, table.dataTable th:first-child { font-weight: 600; }

        .progress-bar-container { width: 100%; height: 22px; background-color: #e2e8f0; border-radius: 50px; position: relative; overflow: hidden; }
        .progress-bar-fill { height: 100%; border-radius: 50px; transition: width 0.5s ease-in-out; }
        .progress-bar-label { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); font-size: 11px; font-weight: 700; color: white; text-shadow: 1px 1px 2px rgba(0,0,0,0.4); }

        /* Modal Styles */
        .modal-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background-color: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transition: opacity 0.3s ease, visibility 0s 0.3s;
        }
        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
            transition: opacity 0.3s ease;
        }
        .modal-content { background-color: var(--color-surface); border-radius: 16px; padding: 24px; width: 90%; max-width: 1000px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.1); max-height: 90vh; overflow-y: auto; }
        .modal-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--color-border); padding-bottom: 16px; margin-bottom: 16px; }
        .modal-header h2 { margin: 0; }
        .modal-close { font-size: 28px; line-height: 1; font-weight: 300; cursor: pointer; border: none; background: none; color: var(--color-text-secondary); }
        .modal-body { display: grid; grid-template-columns: 1fr; gap: 24px; }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h1>Dashboard Analisa Penjualan</h1>

        <div class="dashboard-grid">
            <div class="card chart-card">
                <div class="card-header">
                    <h3 id="mainChartTitle">Performa Bulanan</h3>
                    <div class="card-subtitle">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" width="16" height="16"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                        <span>Klik bar/titik di grafik untuk detail</span>
                    </div>
                </div>
                <div class="chart-wrapper">
                    <div id="mainChart"></div>
                    <div class="chart-info-panel" id="chartInfoPanel">
                        <h4 id="infoPanelTitle">Memuat...</h4>
                        <div class="info-item">
                            <span>Target</span>
                            <strong id="infoPanelTarget">-</strong>
                        </div>
                        <div class="info-item">
                            <span>Realisasi</span>
                            <strong id="infoPanelRealisasi">-</strong>
                        </div>
                        <hr style="border: none; border-top: 1px solid var(--color-border); margin: 8px 0;">
                        <h5>Rincian Divisi</h5>
                        <div id="infoPanelBreakdown">
                            <p style="font-size: 13px; color: var(--color-text-secondary);">Klik bulan pada grafik untuk melihat rincian.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card table-card table-divisi">
                <div class="card-header">
                    <h3>Pencapaian per Divisi</h3>
                    <small class="card-subtitle">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" width="16" height="16"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                        <span>Klik pada baris untuk melihat detail</span>
                    </small>
                </div>
                <table id="divisiTable" class="display" style="width:100%">
                    <thead><tr><th>Divisi</th><th>Target</th><th>Realisasi</th><th>Pencapaian</th></tr></thead>
                    <tfoot><tr><th>Total</th><th></th><th></th><th></th></tr></tfoot>
                </table>
            </div>
            <div class="card table-card table-cabang">
                 <div class="card-header">
                    <h3>Pencapaian Seluruh Cabang</h3>
                </div>
                <table id="cabangTable" class="display" style="width:100%">
                    <thead><tr><th>Cabang</th><th>Target</th><th>Realisasi</th><th>Pencapaian</th></tr></thead>
                    <tfoot><tr><th>Total</th><th></th><th></th><th></th></tr></tfoot>
                </table>
            </div>
        </div>
    </div>

        <div class="modal-overlay" id="detailModal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 id="modalTitle">Detail Divisi</h2>
                    <button class="modal-close" id="closeModal">&times;</button>
                </div>
                <div class="modal-body">
                    <div id="detailChart"></div>
                    <div id="detailTableContainer">
                        <table id="detailTable" class="display" style="width:100%">
                            <thead>
                                <tr><th>Cabang</th><th>Target</th><th>Realisasi</th><th>Pencapaian</th></tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/rowgroup/1.4.0/js/dataTables.rowGroup.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

        <script>
            let rawData = [];
            let monthlyBreakdown = []; 
            let monthlyDataGlobal = [];

            let mainChart, detailChart = null;
            let detailTable;
            var getTampil = '';

            const monthNames = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Ags", "Sep", "Okt", "Nov", "Des"];

            $(document).ready(function() {
                getTampil = '<?= $getTampil ?>';

                mainChart = new ApexCharts(document.querySelector("#mainChart"), getChartOptions('main'));
                mainChart.render();
                detailChart = new ApexCharts(document.querySelector("#detailChart"), getChartOptions('detail'));
                detailChart.render();
                
                const divisiTable = $('#divisiTable').DataTable({
                    responsive: true,
                    paging: false, 
                    info: false, 
                    columns: [
                        { data: 'divisi' },
                        { 
                            data: 'totalTarget', 
                            className: 'text-right', 
                            render: (d) => formatNumber(d) 
                        },
                        { 
                            data: 'totalRealisasi', 
                            className: 'text-right', 
                            render: (d) => formatNumber(d) 
                        },
                        { data: 'achievement', render: renderProgressBar }
                    ],
                    order: [[3, 'desc']],
                    footerCallback: footerCallbackSum(2, 1, 3)
                    });

                const cabangTable = $('#cabangTable').DataTable({
                    responsive: true,
                    paging: false, 
                    info: false, 
                    columns: [
                        { data: 'cabang' },
                        { 
                            data: 'totalTarget', 
                            className: 'text-right',
                            render: (d) => formatNumber(d) 
                        },
                        { 
                            data: 'totalRealisasi', 
                            className: 'text-right', 
                            render: (d) => formatNumber(d)
                        },
                        { data: 'achievement', render: renderProgressBar }
                    ],
                    order: [[3, 'desc']],
                    footerCallback: footerCallbackSum(2, 1, 3) 
                });

                let detailTable; 

                async function fetchDataAndUpdate() {
                    try {
                        const response = await fetch('getdetailRealisasiTarget.php');
                        const data = await response.json();
                        rawData = data.result || [];

                        if (getTampil !== 'all') {
                            rawData = rawData.filter(item => item.hasil !== 'PRYHO' && item.hasil !== 'EXP' && item.hasil !== 'OTH');
                        }
                        updateDashboard();
                    } catch (error) { console.error("Gagal mengambil data:", error); }
                }

                function updateDashboard() {
                    const divisiData = processDataBy(rawData, 'hasil');
                    divisiTable.clear().rows.add(divisiData).draw();

                    const cabangData = processDataBy(rawData, 'descript');
                    cabangTable.clear().rows.add(cabangData).draw();

                    const monthlyData = processDataBy(rawData, 'bulan');
                    monthlyData.sort((a,b) => a.bulan - b.bulan);
                    const monthNames = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Ags", "Sep", "Okt", "Nov", "Des"];

                    monthlyBreakdown = monthlyData.map(monthEntry => {
                        const dataForThisMonth = rawData.filter(item => parseInt(item.bulan) === monthEntry.bulan);
                        return processDataBy(dataForThisMonth, 'hasil');
                    });

                    const targetSeriesData = monthlyData.map(d => ({
                        x: monthNames[d.bulan - 1],
                        y: parseFloat(d.totalTarget.toFixed(2))
                    }));
                    const realisasiSeriesData = monthlyData.map(monthEntry => {
                        const dataForThisMonth = rawData.filter(item => parseInt(item.bulan) === monthEntry.bulan);
                        const breakdown = processDataBy(dataForThisMonth, 'hasil');
                        
                        return {
                            x: monthNames[monthEntry.bulan - 1],
                            y: parseFloat(monthEntry.totalRealisasi.toFixed(2)),
                            breakdown: breakdown
                        };
                    });

                    mainChart.updateSeries([
                        { name: 'Target', type: 'column', data: targetSeriesData },
                        { name: 'Realisasi', type: 'line', data: realisasiSeriesData } 
                    ]);

                    const currentMonthIndex = new Date().getMonth(); // 0 = Jan, 1 = Feb, dst.
                    const currentMonthData = monthlyData.find(d => d.bulan === (currentMonthIndex + 1));
                    
                    if (currentMonthData) {
                        const currentMonthName = monthNames[currentMonthIndex];
                        const currentBreakdown = monthlyBreakdown[monthlyData.indexOf(currentMonthData)];
                        updateInfoPanel(currentMonthName, currentMonthData.totalTarget, currentMonthData.totalRealisasi, currentBreakdown);
                    } else if (monthlyData.length > 0) {
                        const lastDataPoint = monthlyData.length - 1;
                        const lastMonthData = monthlyData[lastDataPoint];
                        const lastMonthName = monthNames[lastMonthData.bulan - 1];
                        const lastBreakdown = monthlyBreakdown[lastDataPoint];
                        updateInfoPanel(lastMonthName, lastMonthData.totalTarget, lastMonthData.totalRealisasi, lastBreakdown);
                    }
                }

                $('#divisiTable tbody').on('click', 'tr', function() {
                    const data = divisiTable.row(this).data();
                    if (data) showDetailModal(data.divisi);
                });

                $('#closeModal, .modal-overlay').on('click', function(e) {
                    if (e.target.id === 'closeModal' || e.target.classList.contains('modal-overlay')) {
                        $('#detailModal').removeClass('active');
                    }
                });
                
                function showDetailModal(selectedDivisi) {
                    $('#modalTitle').text(`Detail Divisi: ${selectedDivisi}`);
                    const filteredByDivisi = rawData.filter(item => item.hasil.trim() === selectedDivisi);
                    
                    const monthlyData = processDataBy(filteredByDivisi, 'bulan');
                    monthlyData.sort((a,b) => a.bulan - b.bulan); 

                    const targetSeriesData = monthlyData.map(d => ({
                        x: monthNames[d.bulan - 1],
                        y: parseFloat(d.totalTarget.toFixed(2))
                    }));
                    const realisasiSeriesData = monthlyData.map(d => ({
                        x: monthNames[d.bulan - 1],
                        y: parseFloat(d.totalRealisasi.toFixed(2))
                    }));

                    detailChart.updateSeries([
                        { name: 'Target', type: 'column', data: targetSeriesData },
                        { name: 'Realisasi', type: 'line', data: realisasiSeriesData }
                    ]);

                    const cabangData = processDataBy(filteredByDivisi, 'descript');
                    if (detailTable) detailTable.destroy(); 
                    detailTable = $('#detailTable').DataTable({
                        data: cabangData,
                        destroy: true,
                        paging: false, info: false, searching: false,
                        columns: [
                            { data: 'cabang' },
                            { data: 'totalTarget', render: (d) => formatNumber(d) },
                            { data: 'totalRealisasi', render: (d) => formatNumber(d) },                        
                            { data: 'achievement', render: renderProgressBar }
                        ],
                        order: [[3, 'desc']]
                    });

                    $('#detailModal').addClass('active');
                }
                fetchDataAndUpdate();
            });

            // --- FUNGSI-FUNGSI BANTUAN ---
            const pembagi = 1000000;
            function processDataBy(data, groupKey) {
                const summary = data.reduce((acc, item) => {
                    const key = item[groupKey].toString().trim();
                    if (!acc[key]) {
                        acc[key] = { [groupKey]: key, totalRealisasi: 0, totalTarget: 0};
                    }
                    acc[key].totalRealisasi += parseFloat(item.amountreal) || 0;
                    acc[key].totalTarget += parseFloat(item.amounttarget) || 0;
                    return acc;
                }, {});

                return Object.values(summary).map(item => {
                    item.totalRealisasi /= pembagi;
                    item.totalTarget /= pembagi;
                    item.achievement = item.totalTarget > 0 ? (item.totalRealisasi / item.totalTarget) * 100 : (item.totalRealisasi > 0 ? 100 : 0);
                    
                    if (groupKey === 'descript') {
                        item.cabang = item.descript;
                        delete item.descript;
                    } else if(groupKey === 'hasil') {
                        item.divisi = item.hasil;
                        delete item.hasil;
                    } else if(groupKey === 'bulan') {
                        item.bulan = parseInt(item.bulan);
                    }
                    return item;
                });
            }
            
            function updateInfoPanel(monthName, target, realisasi, breakdown) {
                // console.log(monthName);
                $('#infoPanelTitle').text(`Detail: ${monthName}`);
                $('#infoPanelTarget').text(formatNumber(target));
                $('#infoPanelRealisasi').text(formatNumber(realisasi));

                let breakdownHtml = '';
                if(breakdown && breakdown.length > 0){
                    breakdown.sort((a, b) => b.totalRealisasi - a.totalRealisasi); // Urutkan divisi dari realisasi terbesar
                    breakdown.forEach(div => {
                        breakdownHtml += `<div class="info-item-sm"><span>${div.divisi}</span><strong>${formatNumber(div.totalRealisasi)}</strong></div>`;
                    });
                } else {
                    breakdownHtml = '<p style="font-size: 13px; color: var(--color-text-secondary);">Tidak ada data rincian.</p>';
                }
                $('#infoPanelBreakdown').html(breakdownHtml);
            }

            function formatNumber(num) { 
                return num.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); 
            }
            
            function renderProgressBar(data, type, row) {
                let color = 'var(--color-danger)';
                if (data >= 90) color = 'var(--color-success)';
                else if (data >= 50) color = 'var(--color-warning)';
                const percentage = data.toFixed(2);
                return `<div class="progress-bar-container"><div class="progress-bar-fill" style="width: ${Math.min(data, 100)}%; background-color: ${color};"></div><span class="progress-bar-label">${percentage}%</span></div>`;
            }

            function footerCallbackSum(realisasiIndex, targetIndex, achievementIndex) {
                return function (row, data, start, end, display) {
                    var api = this.api();
                    const totalRealisasi = api.column(realisasiIndex, { page: 'current' }).data().reduce((a, b) => a + b, 0);
                    const totalTarget = api.column(targetIndex, { page: 'current' }).data().reduce((a, b) => a + b, 0);
                    const totalAchievement = totalTarget > 0 ? (totalRealisasi / totalTarget) * 100 : 0;

                    $(api.column(realisasiIndex).footer()).html(formatNumber(totalRealisasi));
                    $(api.column(targetIndex).footer()).html(formatNumber(totalTarget));
                    if(api.column(achievementIndex).footer()) {
                        $(api.column(achievementIndex).footer()).html(renderProgressBar(totalAchievement));
                    }
                }
            }

            function getChartOptions(type) {
                const isDetail = type === 'detail';
                return {
                    series: [],
                    chart: { 
                        height: isDetail ? 200 : 400, 
                        type: 'line', 
                        toolbar: { show: false }, 
                        fontFamily: 'Inter, sans-serif',
                        events: {
                            dataPointSelection: function(event, chartContext, config) {
                                const dataPointIndex = config.dataPointIndex;
                                if (monthlyBreakdown.length > 0 && dataPointIndex >= 0) {
                                    const month = config.w.globals.labels[dataPointIndex];
                                    const target = config.w.globals.series[0][dataPointIndex];
                                    const realisasi = config.w.globals.series[1][dataPointIndex];
                                    const breakdown = monthlyBreakdown[dataPointIndex];
                                    
                                    updateInfoPanel(monthNames[month-1], target, realisasi, breakdown);
                                }
                            }
                        }
                    },
                    plotOptions: { bar: { horizontal: false, columnWidth: '60%', borderRadius: 6, borderRadiusApplication: 'end' } },
                    colors: ['#AEC8A4', '#F97A00'], 
                    dataLabels: { enabled: false },
                    stroke: { width: [0, 4], curve: 'smooth' },
                    markers: { size: 5, strokeWidth: 2, strokeColors: '#fff', hover: { size: 7 }},
                    states: {
                        hover: { filter: { type: 'darken', value: 0.2 } },
                        active: { filter: { type: 'darken', value: 0.85 }, allowMultipleDataPointsSelection: false }
                    },
                    xaxis: { categories: [], labels: { style: { colors: '#64748b', fontSize: '12px' } } },
                    yaxis: { 
                        title: { text: 'Amount (dalam Juta)', style: { color: '#64748b', fontWeight: 500 } },
                        labels: {
                            style: { colors: '#64748b', fontSize: '12px' },
                            formatter: (val) => { return val.toLocaleString('id-ID'); }
                        }
                    },
                    grid: { borderColor: '#e2e8f0', strokeDashArray: 4 },
                    fill: { opacity: 1 },
                    // tooltip: {
                    //     enabled: true,
                    //     shared: true,
                    //     intersect: false,
                    //     custom: function({ series, seriesIndex, dataPointIndex, w }) {
                    //         // [DIUBAH] Ambil nilai pada indeks yang benar
                    //         const totalTarget = series[0][dataPointIndex];
                    //         const totalRealisasi = series[1][dataPointIndex];
                            
                    //         const realisasiDataPoint = w.config.series[1].data[dataPointIndex];
                    //         if (!realisasiDataPoint) return '';
                    //         const breakdownData = realisasiDataPoint.breakdown || [];
                            
                    //         let breakdownHtml = '';
                    //         breakdownData.forEach(div => {
                    //             breakdownHtml += `
                    //                 <div style="display: flex; align-items: center; padding: 2px 8px;">
                    //                     <span style="background-color: #a0aec0; width: 10px; height: 10px; border-radius: 50%; margin-right: 8px;"></span>
                    //                     <div style="font-size: 12px; display: flex; justify-content: space-between; flex-grow: 1;">
                    //                         <span>${div.divisi}: </span>
                    //                         <span style="font-weight: 600; margin-left: 12px;">${formatNumber(div.totalRealisasi)}</span>
                    //                     </div>
                    //                 </div>
                    //             `;
                    //         });

                    //         return `
                    //             <div style="background: #262626; color: #fff; padding: 8px; border-radius: 6px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    //                 <div style="font-weight: 600; margin-bottom: 8px; padding-bottom: 6px; border-bottom: 1px solid #555;">
                    //                     ${w.globals.labels[dataPointIndex]}
                    //                 </div>
                    //                 <div style="display: flex; align-items: center; padding: 2px 8px;">
                    //                     <span style="background-color: ${w.globals.colors[0]}; width: 10px; height: 10px; border-radius: 50%; margin-right: 8px;"></span>
                    //                     <div style="font-size: 12px; display: flex; justify-content: space-between; flex-grow: 1;">
                    //                         <span>Target: </span>
                    //                         <span style="font-weight: 600; margin-left: 12px;">${formatNumber(totalTarget)}</span>
                    //                     </div>
                    //                 </div>
                    //                 <div style="display: flex; align-items: center; padding: 2px 8px; margin-bottom: 6px;">
                    //                     <span style="background-color: ${w.globals.colors[1]}; width: 10px; height: 10px; border-radius: 50%; margin-right: 8px;"></span>
                    //                     <div style="font-size: 12px; display: flex; justify-content: space-between; flex-grow: 1;">
                    //                         <span>Realisasi: </span>
                    //                         <span style="font-weight: 600; margin-left: 12px;">${formatNumber(totalRealisasi)}</span>
                    //                     </div>
                    //                 </div>
                    //                 <div style="border-top: 1px solid #555; margin: 4px 8px;"></div>
                    //                 <div style="padding-top: 5px;">${breakdownHtml}</div>
                    //             </div>
                    //         `;
                    //     }
                    // },
                    noData: { text: 'Sedang Memuat data...' }
                };
            }
        </script>
    </div>
</body>
</html>