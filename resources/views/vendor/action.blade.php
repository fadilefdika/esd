<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Vendor Action - EMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { background-color: #f8fafc; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; padding-bottom: 24px; }
        .app-navbar { background: white; padding: 16px; border-bottom: 1px solid #e2e8f0; position: sticky; top: 0; z-index: 10; display: flex; align-items: center; gap: 16px; }
        .back-btn { color: #64748b; font-size: 1.25rem; text-decoration: none; }
        .card-panel { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.03); border: 1px solid #f1f5f9; margin-bottom: 16px; }
        .status-options .btn-check:checked + .btn { background-color: #eff6ff; color: #2563eb; border-color: #bfdbfe; font-weight: 600; }
        .status-options .btn { border-color: #e2e8f0; color: #475569; border-radius: 8px; justify-content: start; text-align: left; padding: 12px 16px; margin-bottom: 8px; }
        .submit-btn { background-color: #2563eb; color: white; border-radius: 12px; padding: 14px; font-weight: 600; width: 100%; border: none; font-size: 1rem; box-shadow: 0 4px 6px -1px rgba(37,99,235,0.2); transition: all 0.2s; }
        .submit-btn:hover { background-color: #1d4ed8; transform: translateY(-1px); }
        .submit-btn:disabled { background-color: #94a3b8; transform: none; box-shadow: none; }
        .nav-tabs .nav-link { color: #64748b; font-weight: 600; border: none; border-bottom: 2px solid transparent; padding-bottom: 12px; }
        .nav-tabs .nav-link.active { color: #2563eb; border-bottom: 2px solid #2563eb; background: transparent; }
        .item-list { list-style: none; padding: 0; margin: 0; }
        .item-list li { padding: 8px 12px; border-bottom: 1px solid #f1f5f9; font-size: 0.85rem; display: flex; justify-content: space-between; align-items: center; }
        .item-list li:last-child { border-bottom: none; }
    </style>
</head>
<body>

<div class="app-navbar">
    <a href="{{ route('vendor.dashboard') }}" class="back-btn"><i class="bi bi-arrow-left"></i></a>
    <h6 class="mb-0 fw-bold">Detail Pekerjaan</h6>
</div>

<div class="container px-3 py-3">
    <!-- Info Singkat -->
    <div class="card-panel mb-3">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="fw-bold mb-0 text-dark">{{ $entity->code }}</h5>
            @if($activeTransaction)
                @if($activeTransaction->transaction_status === 'OPEN')
                    <span class="badge bg-warning text-dark px-2 py-1"><i class="bi bi-hourglass-split"></i> Diproses</span>
                @else
                    <span class="badge bg-success px-2 py-1"><i class="bi bi-check-circle"></i> Selesai</span>
                @endif
            @else
                <span class="badge bg-secondary px-2 py-1">Tidak Ada Cucian</span>
            @endif
        </div>
        <p class="text-muted mb-1" style="font-size: 0.85rem;">
            <i class="bi bi-person badge bg-light text-dark p-1 me-1"></i> 
            {{ $entity->employee_name ?? 'Available' }} ({{ $entity->dept_name ?? '-' }})
        </p>
        @if($activeTransaction)
            <p class="text-muted mb-0" style="font-size: 0.75rem;">
                <i class="bi bi-receipt me-1"></i> {{ $activeTransaction->transaction_code }} 
                &bull; Masuk: {{ $activeTransaction->created_at->format('d M Y, H:i') }}
            </p>
        @endif
    </div>

    @if($activeTransaction)
        <!-- Item yang di-laundry -->
        <div class="card-panel mb-3">
            <h6 class="fw-bold text-dark mb-2" style="font-size: 0.85rem;">
                <i class="bi bi-list-check text-primary me-1"></i> Item yang Dicuci
            </h6>
            @if($activeTransaction->items && $activeTransaction->items->count() > 0)
                <div class="bg-light rounded-3 overflow-hidden">
                    <ul class="item-list">
                        @foreach($activeTransaction->items as $item)
                            <li>
                                <span class="fw-medium">{{ $item->item_name }}</span>
                                <span class="badge bg-primary bg-opacity-10 text-primary">1x</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @else
                <p class="text-muted mb-0" style="font-size: 0.8rem;">Item tidak tersedia.</p>
            @endif
        </div>

        <!-- Tab Navigasi -->
        <ul class="nav nav-tabs mb-3 border-bottom-0" id="vendorTab" role="tablist">
            <li class="nav-item flex-fill text-center" role="presentation">
                <button class="nav-link w-100 active" id="status-tab" data-bs-toggle="tab" data-bs-target="#status" type="button" role="tab" aria-controls="status" aria-selected="true">
                    <i class="bi bi-arrow-repeat me-1"></i> Update Status
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="vendorTabContent">
            
            <!-- UPDATE STATUS TAB -->
            <div class="tab-pane fade show active" id="status" role="tabpanel" aria-labelledby="status-tab">
                
                <!-- Status saat ini -->
                <div class="d-flex align-items-center p-3 rounded-3 mb-4" style="background: #fef3c7; border: 1px solid #fcd34d;">
                    @if($activeTransaction->transaction_status === 'READY')
                        <i class="bi bi-check-circle fs-4 text-success me-3"></i>
                        <div>
                            <div class="fw-bold text-dark" style="font-size: 0.9rem;">Status Saat Ini: Siap Diambil</div>
                            <div class="text-muted" style="font-size: 0.75rem;">Selesai Dicuci</div>
                        </div>
                    @else
                        <i class="bi bi-arrow-repeat fs-4 text-warning me-3"></i>
                        <div>
                            <div class="fw-bold text-dark" style="font-size: 0.9rem;">Status Saat Ini: Sedang Diproses</div>
                            <div class="text-muted" style="font-size: 0.75rem;">Masuk: {{ $activeTransaction->created_at->format('d M Y, H:i') }}</div>
                        </div>
                    @endif
                </div>

                @if($activeTransaction->transaction_status !== 'READY')
                    <p class="text-muted mb-3" style="font-size: 0.8rem;">Tekan tombol di bawah jika cucian sudah selesai dan siap diambil oleh karyawan.</p>

                    <form id="formUpdateStatus">
                        <input type="hidden" name="status" value="READY">
                        <button type="submit" class="submit-btn" id="btnSaveStatus" style="background-color: #059669; box-shadow: 0 4px 6px -1px rgba(5,150,105,0.3);">
                            <i class="bi bi-check2-circle me-2"></i> Tandai Siap Diambil
                        </button>
                    </form>
                @else
                    <div class="alert alert-success border-0 text-center" style="border-radius: 10px; font-size: 0.85rem;">
                        <i class="bi bi-hand-thumbs-up me-2"></i> Menunggu Karyawan untuk mengonfirmasi penerimaan seragam.
                    </div>
                @endif
            </div>

        </div>
    @else
        <div class="card border-0 shadow-sm mb-3" style="border-radius: 12px;">
            <div class="card-body p-3">
                <h6 class="fw-bold text-dark mb-3" style="font-size: 0.85rem;">
                    <i class="bi bi-box-arrow-in-right text-primary me-1"></i> Form Penerimaan Laundry
                </h6>
                
                <form id="formTerimaLaundry">
                    @csrf
                    <input type="hidden" name="entity_id" value="{{ $entity->id }}">
                    <input type="hidden" name="jenis_transaksi" value="Serah ke laundry">
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary text-uppercase">Tanggal & Waktu</label>
                        <input type="datetime-local" class="form-control border-0 bg-light shadow-none" 
                            name="transaction_date" 
                            value="{{ now()->format('Y-m-d\TH:i') }}" 
                            required 
                            style="font-size: 0.85rem; border-radius: 8px;">
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label small fw-bold text-secondary text-uppercase mb-0">Item yang Diterima</label>
                            @if(isset($maxSelectableSets))
                                <span class="badge bg-primary rounded-pill mb-0 shadow-sm" style="font-size: 0.7rem;">Maks Laundry: {{ $maxSelectableSets }} Set</span>
                            @endif
                        </div>
                        
                        <div class="row g-2">
                            @if(isset($allSets) && count($allSets) > 0)
                                @foreach($allSets as $setNo => $setData)
                                    @php
                                        $itemsInSet = $setData['items'];
                                        $status = $setData['status'];
                                        $isAvailable = $setData['is_available'];
                                        
                                        $badgeClass = 'bg-success';
                                        if ($status === 'Hilang') $badgeClass = 'bg-dark';
                                        elseif ($status === 'Rusak') $badgeClass = 'bg-danger';
                                        elseif ($status === 'Sedang Laundry') $badgeClass = 'bg-warning text-dark';
                                    @endphp

                                    <div class="col-12 mb-2">
                                        <div class="p-2 border-start {{ $isAvailable ? 'border-primary bg-light' : 'border-secondary bg-light' }} border-4 rounded-3" style="{{ $isAvailable ? '' : 'opacity: 0.7;' }}">
                                            <div class="d-flex justify-content-between align-items-center mb-2 ps-1">
                                                <div class="fw-bold" style="font-size: 0.8rem; color: #475569;">SET {{ $setNo }}</div>
                                                <span class="badge {{ $badgeClass }}" style="font-size: 0.7rem;">{{ $status }}</span>
                                            </div>
                                            
                                            <div class="list-group list-group-flush bg-transparent">
                                                @foreach($itemsInSet as $itemName => $item)
                                                    <label class="list-group-item d-flex align-items-center border-0 bg-transparent px-1 py-1" style="cursor: {{ $isAvailable ? 'pointer' : 'not-allowed' }};">
                                                        <input class="form-check-input me-3 item-checkbox" 
                                                            type="checkbox" 
                                                            name="items[]" 
                                                            value="{{ $item->id }}_{{ $setNo }}"
                                                            data-set="{{ $setNo }}"
                                                            data-label="{{ $itemName }} (Set {{ $setNo }})"
                                                            {{ $isAvailable ? '' : 'disabled' }}
                                                            style="width: 1.1rem; height: 1.1rem;">
                                                        <span style="font-size: 0.85rem; color: #1e293b; {{ $isAvailable ? '' : 'text-decoration: line-through; opacity: 0.5;' }}">{{ $itemName }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="col-12">
                                    <div class="alert alert-light border text-center small py-3" style="border-radius: 10px;">
                                        Tidak ada item Set ESD yang tercatat.
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="{{ route('vendor.dashboard') }}" class="btn btn-light flex-grow-1" style="border-radius: 10px; font-weight: 600; font-size: 0.85rem;">Batal</a>
                        <button type="button" class="btn btn-primary flex-grow-1" id="btnSubmitLaundry" style="border-radius: 10px; font-weight: 600; font-size: 0.85rem;">
                            Proses Terima
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

@if($activeTransaction)
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('formUpdateStatus');
        const btn = document.getElementById('btnSaveStatus');

        form.addEventListener('submit', function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Tandai Selesai?',
                text: 'Cucian akan ditandai selesai dan siap diambil oleh karyawan.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#059669',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Selesai',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (!result.isConfirmed) return;

                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Menyimpan...';

                fetch("{{ route('vendor.update-status', $activeTransaction->id) }}", {
                    method: "PATCH",
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ status: 'READY' })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.message,
                            confirmButtonColor: '#2563eb',
                            allowOutsideClick: false
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Gagal', text: data.message || 'Terjadi kesalahan', confirmButtonColor: '#2563eb' });
                        btn.disabled = false;
                        btn.innerHTML = '<i class="bi bi-save me-2"></i> Simpan Status';
                    }
                })
                .catch(error => {
                    console.error(error);
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Gagal menghubungi server', confirmButtonColor: '#2563eb' });
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bi bi-save me-2"></i> Simpan Status';
                });
            });
        });

    });
</script>
@else
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Form Terima Laundry
        const btnSubmitLaundry = document.getElementById('btnSubmitLaundry');
        if (btnSubmitLaundry) {

            // Logika limit pemilihan set
            const maxSets = {{ $maxSelectableSets ?? 999 }};
            const checkboxes = document.querySelectorAll('.item-checkbox');
            
            checkboxes.forEach(cb => {
                cb.addEventListener('change', function() {
                    // Kumpulkan Set yang terpilih
                    let selectedSets = new Set();
                    document.querySelectorAll('.item-checkbox:checked').forEach(c => {
                        selectedSets.add(c.dataset.set);
                    });

                    // Jika jumlah set yang terpilih melebihi maksimal
                    if (selectedSets.size > maxSets) {
                        this.checked = false; // Batalkan centang yang baru saja diklik
                        Swal.fire({ 
                            icon: 'warning', 
                            title: 'Batas Maksimal', 
                            text: `Sistem mencatat batas minimum ketersediaan seragam. Anda hanya diizinkan mencuci maksimal ${maxSets} set.`, 
                            confirmButtonColor: '#2563eb' 
                        });
                    }
                });
            });

            btnSubmitLaundry.addEventListener('click', function() {
                const form = document.getElementById('formTerimaLaundry');
                
                let checkedItems = [];
                document.querySelectorAll('.item-checkbox:checked').forEach(cb => {
                    checkedItems.push(cb.dataset.label);
                });

                if(checkedItems.length === 0) {
                    Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Pilih minimal 1 item yang diterima!', confirmButtonColor: '#2563eb' });
                    return;
                }

                btnSubmitLaundry.disabled = true;
                btnSubmitLaundry.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Memproses...';

                const formData = new FormData(form);

                fetch("{{ route('transactions.store') }}", {
                    method: "POST",
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if(data.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Pakaian berhasil diterima masuk ke laundry.',
                            confirmButtonColor: '#2563eb',
                            allowOutsideClick: false
                        }).then(() => {
                            window.location.href = "{{ route('vendor.dashboard') }}";
                        });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Gagal', text: data.message || 'Terjadi kesalahan', confirmButtonColor: '#dc3545' });
                        btnSubmitLaundry.disabled = false;
                        btnSubmitLaundry.innerHTML = 'Proses Terima';
                    }
                })
                .catch(error => {
                    console.error(error);
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Gagal menghubungi server', confirmButtonColor: '#dc3545' });
                    btnSubmitLaundry.disabled = false;
                    btnSubmitLaundry.innerHTML = 'Proses Terima';
                });
            });
        }
    });
</script>
@endif

</body>
</html>
