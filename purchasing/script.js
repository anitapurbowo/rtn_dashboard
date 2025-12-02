document.addEventListener('DOMContentLoaded', () => {
    // ================== SUMBER DATA ==================
    // 1. URL API untuk Tabel 1
    const apiUrl = 'https://jsonplaceholder.typicode.com/todos';

    // 2. Data Lokal untuk Tabel 2 (dari file "PR to AP")
    const prToApData = [
        { "pr_number": "PR-2025-001", "vendor": "PT Sinar Jaya", "description": "Pengadaan ATK Kantor", "amount": 1500000, "status": "Approved" },
        { "pr_number": "PR-2025-002", "vendor": "CV Mandiri Tech", "description": "Service 5 Unit AC", "amount": 2500000, "status": "Paid" },
        { "pr_number": "PR-2025-003", "vendor": "Toko Komputer ABC", "description": "Pembelian 2 unit Mouse Wireless", "amount": 450000, "status": "Pending" },
        { "pr_number": "PR-2025-004", "vendor": "PT Sinar Jaya", "description": "Kertas HVS A4 10 rim", "amount": 550000, "status": "Paid" },
        { "pr_number": "PR-2025-005", "vendor": "CV Maju Mundur", "description": "Jasa Catering Rapat", "amount": 1200000, "status": "Approved" }
    ];

    let apiData = [];

    // Elemen-elemen DOM
    const tableBody1 = document.getElementById('table-body-1');
    const tableBody2 = document.getElementById('table-body-2');
    const showClosed1 = document.getElementById('show-closed-1');
    const showClosed2 = document.getElementById('show-closed-2');
    const searchInput1 = document.getElementById('search-input-1');
    const searchInput2 = document.getElementById('search-input-2');
    const modal = document.getElementById('detail-modal');
    const modalBody = document.getElementById('modal-body');
    const closeButton = document.querySelector('.close-button');

    // --- Logika untuk Tabel 1 (Data API Tugas) ---
    function updateTable1() {
        const dataForTable1 = apiData.slice(0, 15);
        renderApiTable(tableBody1, dataForTable1, showClosed1.checked, searchInput1.value);
    }

    function renderApiTable(tableBody, data, showClosed, searchTerm) {
        tableBody.innerHTML = '';
        let filteredData = showClosed ? data : data.filter(item => !item.completed);
        if (searchTerm) {
            filteredData = filteredData.filter(item => item.title.toLowerCase().includes(searchTerm.toLowerCase()));
        }
        if (filteredData.length === 0) {
            tableBody.innerHTML = `<tr><td colspan="4">Tidak ada data.</td></tr>`;
            return;
        }
        filteredData.forEach(item => {
            const row = document.createElement('tr');
            if (item.completed) row.classList.add('row-closed');
            row.innerHTML = `
                <td>${item.id}</td>
                <td>${item.title}</td>
                <td><span class="${item.completed ? 'status-closed' : 'status-open'}">${item.completed ? 'Selesai' : 'Berjalan'}</span></td>
                <td><button class="detail-button" data-id="${item.id}" data-type="api">Detail</button></td>`;
            tableBody.appendChild(row);
        });
    }

    // --- Logika untuk Tabel 2 (Data Lokal PR ke AP) ---
    function updateTable2() {
        renderPrTable(tableBody2, prToApData, showClosed2.checked, searchInput2.value);
    }

    function renderPrTable(tableBody, data, showClosed, searchTerm) {
        tableBody.innerHTML = '';
        let filteredData = data;

        // Filter status "Paid" (jika checkbox tidak dicentang)
        if (!showClosed) {
            filteredData = data.filter(item => item.status !== 'Paid');
        }

        // Filter berdasarkan pencarian
        if (searchTerm) {
            const lowerCaseSearchTerm = searchTerm.toLowerCase();
            filteredData = filteredData.filter(item =>
                item.vendor.toLowerCase().includes(lowerCaseSearchTerm) ||
                item.description.toLowerCase().includes(lowerCaseSearchTerm)
            );
        }

        if (filteredData.length === 0) {
            tableBody.innerHTML = `<tr><td colspan="4">Tidak ada data yang cocok.</td></tr>`;
            return;
        }

        filteredData.forEach(item => {
            const row = document.createElement('tr');
            const isClosed = item.status === 'Paid';
            if (isClosed) row.classList.add('row-closed');
            row.innerHTML = `
                <td>${item.pr_number}</td>
                <td>${item.vendor}</td>
                <td><span class="${isClosed ? 'status-closed' : 'status-open'}">${item.status}</span></td>
                <td><button class="detail-button" data-id="${item.pr_number}" data-type="pr">Detail</button></td>`;
            tableBody.appendChild(row);
        });
    }

    // --- Logika untuk Modal Detail ---
    function showDetails(id, type) {
        let item;
        let content = '';

        if (type === 'api') {
            item = apiData.find(d => d.id == id);
            if (item) content = `<p><strong>ID Tugas:</strong> ${item.id}</p><p><strong>Judul:</strong> ${item.title}</p><p><strong>Status:</strong> ${item.completed ? 'Selesai' : 'Berjalan'}</p>`;
        } else if (type === 'pr') {
            item = prToApData.find(d => d.pr_number == id);
            if (item) content = `
                <p><strong>No. PR:</strong> ${item.pr_number}</p>
                <p><strong>Vendor:</strong> ${item.vendor}</p>
                <p><strong>Deskripsi:</strong> ${item.description}</p>
                <p><strong>Jumlah:</strong> Rp ${item.amount.toLocaleString('id-ID')}</p>
                <p><strong>Status:</strong> ${item.status}</p>`;
        }

        if (item) {
            modalBody.innerHTML = content;
            modal.style.display = 'block';
        }
    }

    // --- Fungsi Utama untuk Inisialisasi Dashboard ---
    async function initializeDashboard() {
        // 1. Ambil data API dan render Tabel 1
        try {
            const response = await fetch(apiUrl);
            apiData = await response.json();
            updateTable1();
        } catch (error) {
            console.error("Gagal mengambil data API:", error);
            tableBody1.innerHTML = `<tr><td colspan="4">Gagal memuat data.</td></tr>`;
        }

        // 2. Langsung render Tabel 2 dari data lokal
        updateTable2();
    }

    // --- Pendaftaran Event Listeners ---
    showClosed1.addEventListener('change', updateTable1);
    searchInput1.addEventListener('input', updateTable1);
    showClosed2.addEventListener('change', updateTable2);
    searchInput2.addEventListener('input', updateTable2);

    document.querySelector('.dashboard-container').addEventListener('click', (event) => {
        if (event.target.classList.contains('detail-button')) {
            showDetails(event.target.getAttribute('data-id'), event.target.getAttribute('data-type'));
        }
    });

    closeButton.addEventListener('click', () => modal.style.display = 'none');
    window.addEventListener('click', (event) => {
        if (event.target === modal) modal.style.display = 'none';
    });

    // --- Menjalankan Dashboard ---
    initializeDashboard();
});