<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Vendor Dashboard - EMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #f1f5f9; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; padding-bottom: 80px; }
        .header-mobile { background: #2563eb; color: white; padding: 16px 20px; border-bottom-left-radius: 20px; border-bottom-right-radius: 20px; box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2); }
        .stat-card { background: white; border-radius: 12px; padding: 16px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .item-card { background: white; border-radius: 12px; padding: 16px; margin-bottom: 12px; border-left: 4px solid #f59e0b; box-shadow: 0 2px 4px rgba(0,0,0,0.04); }
        .item-card.ready { border-left-color: #10b981; }
        .fab-scan { position: fixed; bottom: 24px; left: 50%; transform: translateX(-50%); width: 64px; height: 64px; background: #2563eb; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 28px; box-shadow: 0 8px 16px rgba(37, 99, 235, 0.4); border: none; z-index: 1000; }
        .fab-scan:active { background: #1d4ed8; transform: translateX(-50%) scale(0.95); }
        
        /* Custom Scrollbar for massive list */
        .list-wrapper {
            max-height: 55vh; /* Membatasi tinggi maksimum */
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 transparent;
        }
        .list-wrapper::-webkit-scrollbar { width: 4px; }
        .list-wrapper::-webkit-scrollbar-track { background: transparent; }
        .list-wrapper::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 10px; }
    </style>
</head>
<body>

<div class="header-mobile mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="mb-0 fw-bold">KlinClean Laundry</h5>
            <small class="text-white-50">Vendor ID: V-001</small>
        </div>
        <div class="dropdown">
            <a href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" class="text-decoration-none">
                <img src="https://ui-avatars.com/api/?name=Klin+Clean&background=eff6ff&color=2563eb" class="rounded-circle shadow-sm border border-light" width="42" height="42" alt="Avatar">
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="border-radius: 12px; margin-top: 10px;">
                <li><h6 class="dropdown-header text-dark fw-bold">Vendor Menu</h6></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item text-danger d-flex align-items-center" href="/login" onclick="alert('Berhasil Logout!');">
                        <i class="bi bi-box-arrow-right me-2"></i> Keluar (Logout)
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="row text-center mt-3 g-2">
        <div class="col-6">
            <div class="bg-white bg-opacity-25 rounded-3 py-2">
                <h4 class="mb-0 fw-bold">24</h4>
                <small style="font-size: 0.7rem;">DALAM PROSES</small>
            </div>
        </div>
        <div class="col-6">
            <div class="bg-white bg-opacity-25 rounded-3 py-2">
                <h4 class="mb-0 fw-bold">12</h4>
                <small style="font-size: 0.7rem;">SIAP DIAMBIL</small>
            </div>
        </div>
    </div>
</div>

<div class="container px-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.9rem;">Daftar Pakaian Terkini</h6>
    </div>

    <!-- Filter & Pilihan Limit (Di Atas) -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="d-flex overflow-auto gap-2 pb-1" style="scrollbar-width: none;">
            <select id="filterStatus" class="form-select form-select-sm border border-light bg-white shadow-sm fw-bold text-dark flex-shrink-0" style="font-size: 0.75rem; width: auto; border-radius: 8px;">
                <option value="all">Semua Status</option>
                <option value="pending">Belum (Proses)</option>
                <option value="done">Selesai (Siap)</option>
            </select>
            
            <select id="filterPaket" class="form-select form-select-sm border border-light bg-white shadow-sm fw-bold text-dark flex-shrink-0" style="font-size: 0.75rem; width: auto; border-radius: 8px;">
                <option value="all">Semua Paket</option>
                <option value="set 1">Set 1</option>
                <option value="set 2">Set 2</option>
                <option value="set 3">Set 3</option>
            </select>

            <select id="limitSelect" class="form-select form-select-sm border border-light bg-white shadow-sm fw-bold text-primary flex-shrink-0" style="font-size: 0.75rem; width: auto; border-radius: 8px;">
                <option value="5" selected>5 Baris</option>
                <option value="10">10 Baris</option>
                <option value="20">20 Baris</option>
            </select>
        </div>
        <span class="badge bg-primary rounded-pill fw-normal ms-2 text-nowrap" id="totalDataBadge" style="font-size: 0.7rem;">Total: 15</span>
    </div>

    <!-- High-Density List Group -->
    <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden; margin-bottom: 20px;">
        <div class="list-group list-group-flush" id="laundryList">
            
            @php
                // Dummy data array (15 baris)
                $dummyList = [
                    ['code' => 'ENT-2503-001A', 'name' => 'Budi Santoso', 'set' => 'Set 1', 'status' => 'Diproses', 'time' => '09:30', 'color' => 'warning'],
                    ['code' => 'ENT-2503-002B', 'name' => 'Andi Wijaya', 'set' => 'Set 2', 'status' => 'Siap Diambil', 'time' => '10:15', 'color' => 'success'],
                    ['code' => 'ENT-2503-005C', 'name' => 'Siti Aminah', 'set' => 'Set 1', 'status' => 'Diproses', 'time' => '11:00', 'color' => 'warning'],
                    ['code' => 'ENT-2503-008D', 'name' => 'Rina Suparni', 'set' => 'Set 3', 'status' => 'Baru Masuk', 'time' => '13:45', 'color' => 'danger'],
                    ['code' => 'ENT-2503-009A', 'name' => 'Ahmad Faisal', 'set' => 'Set 1', 'status' => 'Diproses', 'time' => '14:20', 'color' => 'warning'],
                    ['code' => 'ENT-2503-010C', 'name' => 'Dewi Lestari', 'set' => 'Set 2', 'status' => 'Siap Diambil', 'time' => 'Kemarin', 'color' => 'success'],
                    ['code' => 'ENT-2503-011X', 'name' => 'Tono Hartono', 'set' => 'Set 1', 'status' => 'Diproses', 'time' => 'Kemarin', 'color' => 'warning'],
                    ['code' => 'ENT-2503-012Y', 'name' => 'Yudi Pratama', 'set' => 'Set 2', 'status' => 'Baru Masuk', 'time' => 'Kemarin', 'color' => 'danger'],
                    ['code' => 'ENT-2503-013Z', 'name' => 'Sisca Melati', 'set' => 'Set 1', 'status' => 'Siap Diambil', 'time' => 'Kemarin', 'color' => 'success'],
                    ['code' => 'ENT-2503-014A', 'name' => 'Hendra Gunawan', 'set' => 'Set 3', 'status' => 'Diproses', 'time' => 'Kemarin', 'color' => 'warning'],
                    ['code' => 'ENT-2503-015B', 'name' => 'Nia Ramadhani', 'set' => 'Set 1', 'status' => 'Diproses', 'time' => '2 Hari Lalu', 'color' => 'warning'],
                    ['code' => 'ENT-2503-016C', 'name' => 'Lukman Hakim', 'set' => 'Set 2', 'status' => 'Siap Diambil', 'time' => '2 Hari Lalu', 'color' => 'success'],
                    ['code' => 'ENT-2503-017D', 'name' => 'Rika Anjani', 'set' => 'Set 1', 'status' => 'Diproses', 'time' => '2 Hari Lalu', 'color' => 'warning'],
                    ['code' => 'ENT-2503-018E', 'name' => 'Fajar Sidik', 'set' => 'Set 3', 'status' => 'Baru Masuk', 'time' => '3 Hari Lalu', 'color' => 'danger'],
                    ['code' => 'ENT-2503-019F', 'name' => 'Mega Puspita', 'set' => 'Set 1', 'status' => 'Siap Diambil', 'time' => '3 Hari Lalu', 'color' => 'success'],
                ];
            @endphp

            @foreach($dummyList as $item)
            <a href="#" class="list-group-item list-group-item-action py-3 px-3 vendor-item d-none" 
               data-status="{{ $item['status'] }}" 
               data-paket="{{ $item['set'] }}"
               style="border-bottom: 1px solid #f1f5f9;">
                <div class="d-flex w-100 justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="bg-{{ $item['color'] == 'warning' ? 'warning bg-opacity-10 text-warning' : ($item['color'] == 'success' ? 'success bg-opacity-10 text-success' : 'danger bg-opacity-10 text-danger') }} rounded-circle d-flex justify-content-center align-items-center me-3" style="width: 38px; height: 38px;">
                            <i class="bi {{ $item['status'] == 'Siap Diambil' ? 'bi-check2-all' : ($item['status'] == 'Diproses' ? 'bi-arrow-repeat' : 'bi-box-arrow-in-right') }} fs-5"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.85rem;">{{ $item['code'] }}</h6>
                            <small class="text-muted" style="font-size: 0.7rem;">{{ $item['set'] }} &bull; {{ $item['name'] }}</small>
                        </div>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-{{ $item['color'] }} {{ $item['color'] == 'warning' ? 'text-dark' : '' }} mb-1" style="font-size: 0.65rem;">{{ $item['status'] }}</span><br>
                        <small class="text-muted" style="font-size: 0.65rem;">{{ $item['time'] }}</small>
                    </div>
                </div>
            </a>
            @endforeach

        </div>
    </div>

    <!-- Pagination Controls -->
    <div class="d-flex justify-content-center align-items-center mb-5 pb-5">
        <nav aria-label="Page navigation">
            <ul class="pagination pagination-sm mb-0 shadow-sm" id="pagination" style="border-radius: 8px; overflow: hidden;">
                <!-- Diisi via JS -->
            </ul>
        </nav>
    </div>

</div>

<!-- Simulated Scan Button (Usually opens native Camera or JS QR Scanner) -->
<button class="fab-scan" onclick="simulateScan()">
    <i class="bi bi-qr-code-scan"></i>
</button>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function simulateScan() {
        const codes = ['ENT25032509172605', 'ENT-TEST-002'];
        const randomCode = codes[0]; 
        alert('Memulai Kamera Scanner (Simulasi)...');
        window.location.href = `/vendor/scan/${randomCode}`;
    }

    // Client-side Dummy Pagination & Filter Logic
    document.addEventListener('DOMContentLoaded', function() {
        const allItems = Array.from(document.querySelectorAll('.vendor-item'));
        const limitSelect = document.getElementById('limitSelect');
        const filterStatus = document.getElementById('filterStatus');
        const filterPaket = document.getElementById('filterPaket');
        const pagination = document.getElementById('pagination');
        const totalBadge = document.getElementById('totalDataBadge');
        
        let currentPage = 1;
        let limit = parseInt(limitSelect.value);
        let filteredItems = [...allItems];

        function applyFilters() {
            const statusVal = filterStatus.value;
            const paketVal = filterPaket.value;

            filteredItems = allItems.filter(item => {
                const itemStatus = item.getAttribute('data-status');
                const itemPaket = item.getAttribute('data-paket').toLowerCase();

                let matchStatus = true;
                if (statusVal === 'pending') {
                    matchStatus = ['Diproses', 'Baru Masuk'].includes(itemStatus);
                } else if (statusVal === 'done') {
                    matchStatus = itemStatus === 'Siap Diambil'; /* Selesai */
                }

                let matchPaket = true;
                if (paketVal !== 'all') {
                    matchPaket = itemPaket === paketVal;
                }

                return matchStatus && matchPaket;
            });

            totalBadge.innerText = 'Total: ' + filteredItems.length;
            currentPage = 1; // reset ke halaman 1
            renderList();
        }

        function renderList() {
            // Sembunyikan semua item di DOM awal
            allItems.forEach(item => {
                item.classList.remove('d-flex');
                item.classList.add('d-none');
            });

            // Tampilkan hanya filtered items yang masuk range
            filteredItems.forEach((item, index) => {
                if (index >= (currentPage - 1) * limit && index < currentPage * limit) {
                    item.classList.remove('d-none');
                    item.classList.add('d-flex');
                }
            });
            renderPagination();
        }

        function renderPagination() {
            const totalPages = Math.ceil(filteredItems.length / limit);
            pagination.innerHTML = '';

            // Jika kosong data
            if(filteredItems.length === 0) {
                pagination.innerHTML = '<li class="page-item disabled"><span class="page-link border-0 text-muted">Data Kosong</span></li>';
                return;
            }

            if(totalPages <= 1) return;

            // Tombol Prev
            pagination.innerHTML += `
                <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                    <a class="page-link border-0 ${currentPage === 1 ? 'text-muted' : 'text-dark fw-bold'}" href="#" data-page="${currentPage - 1}">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                </li>
            `;

            // Tombol Angka Halaman
            for (let i = 1; i <= totalPages; i++) {
                pagination.innerHTML += `
                    <li class="page-item ${currentPage === i ? 'active' : ''}">
                        <a class="page-link border-0 ${currentPage === i ? '' : 'text-dark fw-bold'}" href="#" data-page="${i}">${i}</a>
                    </li>
                `;
            }

            // Tombol Next
            pagination.innerHTML += `
                <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                    <a class="page-link border-0 ${currentPage === totalPages ? 'text-muted' : 'text-dark fw-bold'}" href="#" data-page="${currentPage + 1}">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
            `;

            // Setup events pergantian halaman
            pagination.querySelectorAll('.page-link[data-page]').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    let page = parseInt(this.getAttribute('data-page'));
                    if (page >= 1 && page <= totalPages) {
                        currentPage = page;
                        renderList();
                    }
                });
            });
        }

        limitSelect.addEventListener('change', function() {
            limit = parseInt(this.value);
            currentPage = 1; 
            renderList();
        });

        // Trigger filter kapan pun status dropdown ganti
        filterStatus.addEventListener('change', applyFilters);
        filterPaket.addEventListener('change', applyFilters);

        // Initial Render
        applyFilters(); 
    });
</script>
</body>
</html>
