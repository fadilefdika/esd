<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Vendor Dashboard - EMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { background-color: #f1f5f9; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; padding-bottom: 80px; }
        .header-mobile { background: #2563eb; color: white; padding: 16px 20px; border-bottom-left-radius: 20px; border-bottom-right-radius: 20px; box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2); }
        .stat-card { background: white; border-radius: 12px; padding: 16px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .fab-scan { position: fixed; bottom: 24px; left: 50%; transform: translateX(-50%); width: 64px; height: 64px; background: #2563eb; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 28px; box-shadow: 0 8px 16px rgba(37, 99, 235, 0.4); border: none; z-index: 1000; }
        .fab-scan:active { background: #1d4ed8; transform: translateX(-50%) scale(0.95); }
        
        .list-wrapper {
            max-height: 55vh;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 transparent;
        }
        .list-wrapper::-webkit-scrollbar { width: 4px; }
        .list-wrapper::-webkit-scrollbar-track { background: transparent; }
        .list-wrapper::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 10px; }

        .empty-state { text-align: center; padding: 40px 20px; }
        .empty-state i { font-size: 3rem; color: #cbd5e1; }
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
                    <a class="dropdown-item text-danger d-flex align-items-center" href="/login">
                        <i class="bi bi-box-arrow-right me-2"></i> Keluar (Logout)
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="row text-center mt-3 g-2">
        <div class="col-6">
            <div class="bg-white bg-opacity-25 rounded-3 py-2">
                <h4 class="mb-0 fw-bold">{{ $openCount }}</h4>
                <small style="font-size: 0.7rem;">DALAM PROSES</small>
            </div>
        </div>
        <div class="col-6">
            <div class="bg-white bg-opacity-25 rounded-3 py-2">
                <h4 class="mb-0 fw-bold">{{ $finishedCount }}</h4>
                <small style="font-size: 0.7rem;">SELESAI</small>
            </div>
        </div>
    </div>
</div>

<div class="container px-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.9rem;">Daftar Cucian Masuk</h6>
    </div>

    <!-- Filter & Limit -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="d-flex overflow-auto gap-2 pb-1" style="scrollbar-width: none;">
            <select id="filterStatus" class="form-select form-select-sm border border-light bg-white shadow-sm fw-bold text-dark flex-shrink-0" style="font-size: 0.75rem; width: auto; border-radius: 8px;">
                <option value="all">Semua Status</option>
                <option value="OPEN">Dalam Proses</option>
                <option value="FINISHED">Selesai</option>
            </select>
            
            <select id="limitSelect" class="form-select form-select-sm border border-light bg-white shadow-sm fw-bold text-primary flex-shrink-0" style="font-size: 0.75rem; width: auto; border-radius: 8px;">
                <option value="10" selected>10 Baris</option>
                <option value="20">20 Baris</option>
                <option value="50">50 Baris</option>
            </select>
        </div>
        <span class="badge bg-primary rounded-pill fw-normal ms-2 text-nowrap" id="totalDataBadge" style="font-size: 0.7rem;">Total: {{ $transactions->count() }}</span>
    </div>

    <!-- List Group -->
    <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden; margin-bottom: 20px;">
        <div class="list-group list-group-flush" id="laundryList">
            
            @forelse($transactions as $trx)
                @php
                    $entity = $trx->entity;
                    $isOpen = $trx->transaction_status === 'OPEN';
                    $statusLabel = $isOpen ? 'Dalam Proses' : 'Selesai';
                    $statusColor = $isOpen ? 'warning' : 'success';
                    $iconClass = $isOpen ? 'bi-arrow-repeat' : 'bi-check2-all';
                    $timeLabel = $trx->created_at ? $trx->created_at->diffForHumans() : '-';
                @endphp
                <a href="{{ route('vendor.action', $entity->code ?? '#') }}" 
                   class="list-group-item list-group-item-action py-3 px-3 vendor-item d-none" 
                   data-status="{{ $trx->transaction_status }}"
                   style="border-bottom: 1px solid #f1f5f9;">
                    <div class="d-flex w-100 justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="bg-{{ $statusColor }} bg-opacity-10 text-{{ $statusColor }} rounded-circle d-flex justify-content-center align-items-center me-3" style="width: 38px; height: 38px;">
                                <i class="bi {{ $iconClass }} fs-5"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.85rem;">{{ $entity->code ?? '-' }}</h6>
                                <small class="text-muted" style="font-size: 0.7rem;">{{ $entity->employee_name ?? 'Available' }} &bull; {{ $trx->transaction_code }}</small>
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-{{ $statusColor }} {{ $statusColor == 'warning' ? 'text-dark' : '' }} mb-1" style="font-size: 0.65rem;">{{ $statusLabel }}</span><br>
                            <small class="text-muted" style="font-size: 0.65rem;">{{ $timeLabel }}</small>
                        </div>
                    </div>
                </a>
            @empty
                <div class="empty-state py-5">
                    <i class="bi bi-inbox"></i>
                    <p class="text-muted mt-2 mb-0" style="font-size: 0.85rem;">Belum ada cucian yang masuk.</p>
                </div>
            @endforelse

        </div>
    </div>

    <!-- Pagination Controls -->
    <div class="d-flex justify-content-center align-items-center mb-5 pb-5">
        <nav aria-label="Page navigation">
            <ul class="pagination pagination-sm mb-0 shadow-sm" id="pagination" style="border-radius: 8px; overflow: hidden;">
            </ul>
        </nav>
    </div>

</div>

<!-- Scan Button -->
<button class="fab-scan" onclick="simulateScan()">
    <i class="bi bi-qr-code-scan"></i>
</button>

<!-- Scan Modal -->
<div class="modal fade" id="scanModal" tabindex="-1" aria-labelledby="scanModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius: 16px; overflow: hidden; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
      <div class="modal-header bg-light border-0 pb-2">
        <h6 class="modal-title fw-bold" id="scanModalLabel"><i class="bi bi-qr-code-scan me-2 text-primary"></i> Scan QR Code Seragam</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-0 bg-dark position-relative">
        <div id="reader" style="width: 100%; border: none;"></div>
      </div>
      <div class="modal-footer bg-light border-0 d-flex justify-content-between py-2">
        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="simulateScanFallback()" style="font-weight: 500; font-size: 0.8rem;">
            <i class="bi bi-keyboard"></i> Input Manual
        </button>
        <button type="button" class="btn btn-sm btn-danger px-3" data-bs-dismiss="modal" style="font-weight: 500; font-size: 0.8rem;">Tutup</button>
      </div>
    </div>
  </div>
</div>

<script src="https://unpkg.com/html5-qrcode"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<style>
    /* Styling for html5-qrcode UI overrides to look more integrated */
    #reader button {
        background-color: #2563eb;
        color: white;
        border: none;
        padding: 5px 15px;
        border-radius: 5px;
        margin: 5px;
        font-size: 0.85rem;
    }
    #reader select {
        padding: 5px;
        border-radius: 5px;
        font-size: 0.85rem;
        margin: 5px;
    }
    #reader a { color: #2563eb; }
</style>
<script>
    let html5QrcodeScanner = null;

    function simulateScan() {
        const modalEl = document.getElementById('scanModal');
        const bsModal = new bootstrap.Modal(modalEl);
        bsModal.show();

        if (!html5QrcodeScanner) {
            html5QrcodeScanner = new Html5QrcodeScanner(
                "reader",
                { fps: 10, qrbox: {width: 250, height: 250} },
                false
            );
        }
        
        // Delay render slightly so modal finishes transition
        setTimeout(() => {
            html5QrcodeScanner.render(onScanSuccess, onScanFailure);
        }, 200);
    }

    function onScanSuccess(decodedText, decodedResult) {
        if(html5QrcodeScanner) {
            html5QrcodeScanner.clear();
        }
        const modalEl = document.getElementById('scanModal');
        const bsModal = bootstrap.Modal.getInstance(modalEl);
        if(bsModal) bsModal.hide();
        
        let code = decodedText;
        if(code.includes('/')) {
            let parts = code.split('/');
            code = parts[parts.length - 1]; // Assume last URL segment is the entity code
        }
        
        Swal.fire({
            title: 'Memproses...',
            text: 'Membuka data cucian...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });
        
        window.location.href = `/vendor/scan/${code}`;
    }

    function onScanFailure(error) {
        // fail silently in background
    }

    function simulateScanFallback() {
        const modalEl = document.getElementById('scanModal');
        const bsModal = bootstrap.Modal.getInstance(modalEl);
        if(bsModal) bsModal.hide();
        if(html5QrcodeScanner) html5QrcodeScanner.clear();
        
        setTimeout(() => {
            Swal.fire({
                title: 'Input Kode Manual',
                text: 'Masukkan Kode Entity/Seragam:',
                input: 'text',
                inputPlaceholder: 'Contoh: ENT-2026-0001',
                showCancelButton: true,
                confirmButtonColor: '#2563eb',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Cari',
                cancelButtonText: 'Batal',
                inputValidator: (value) => {
                    if (!value || !value.trim()) return 'Kode tidak boleh kosong!';
                }
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    window.location.href = `/vendor/scan/${result.value.trim()}`;
                }
            });
        }, 500); // Wait for modal to hide
    }

    // Clear scanner when modal is closed
    document.addEventListener('DOMContentLoaded', function() {
        const modalEl = document.getElementById('scanModal');
        if(modalEl) {
            modalEl.addEventListener('hidden.bs.modal', function () {
                if(html5QrcodeScanner) {
                    html5QrcodeScanner.clear().catch(e => console.error(e));
                }
            });
        }
    });

    // Client-side Pagination & Filter
    document.addEventListener('DOMContentLoaded', function() {
        const allItems = Array.from(document.querySelectorAll('.vendor-item'));
        const limitSelect = document.getElementById('limitSelect');
        const filterStatus = document.getElementById('filterStatus');
        const pagination = document.getElementById('pagination');
        const totalBadge = document.getElementById('totalDataBadge');
        
        let currentPage = 1;
        let limit = parseInt(limitSelect.value);
        let filteredItems = [...allItems];

        function applyFilters() {
            const statusVal = filterStatus.value;

            filteredItems = allItems.filter(item => {
                const itemStatus = item.getAttribute('data-status');
                if (statusVal === 'all') return true;
                return itemStatus === statusVal;
            });

            totalBadge.innerText = 'Total: ' + filteredItems.length;
            currentPage = 1;
            renderList();
        }

        function renderList() {
            allItems.forEach(item => {
                item.classList.remove('d-flex');
                item.classList.add('d-none');
            });

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

            if(filteredItems.length === 0 || totalPages <= 1) return;

            pagination.innerHTML += `
                <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                    <a class="page-link border-0 ${currentPage === 1 ? 'text-muted' : 'text-dark fw-bold'}" href="#" data-page="${currentPage - 1}">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                </li>
            `;

            for (let i = 1; i <= totalPages; i++) {
                pagination.innerHTML += `
                    <li class="page-item ${currentPage === i ? 'active' : ''}">
                        <a class="page-link border-0 ${currentPage === i ? '' : 'text-dark fw-bold'}" href="#" data-page="${i}">${i}</a>
                    </li>
                `;
            }

            pagination.innerHTML += `
                <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                    <a class="page-link border-0 ${currentPage === totalPages ? 'text-muted' : 'text-dark fw-bold'}" href="#" data-page="${currentPage + 1}">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
            `;

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

        filterStatus.addEventListener('change', applyFilters);

        // Initial Render
        applyFilters(); 
    });
</script>
</body>
</html>
