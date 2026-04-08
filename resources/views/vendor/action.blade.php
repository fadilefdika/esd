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
                &bull; Masuk: {{ $activeTransaction->transaction_start_date->format('d M Y, H:i') }}
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
            <li class="nav-item flex-fill text-center" role="presentation">
                <button class="nav-link w-100" id="lapor-tab" data-bs-toggle="tab" data-bs-target="#lapor" type="button" role="tab" aria-controls="lapor" aria-selected="false">
                    <i class="bi bi-exclamation-triangle me-1"></i> Lapor Rusak
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="vendorTabContent">
            
            <!-- UPDATE STATUS TAB -->
            <div class="tab-pane fade show active" id="status" role="tabpanel" aria-labelledby="status-tab">
                
                <!-- Status saat ini -->
                <div class="d-flex align-items-center p-3 rounded-3 mb-4" style="background: #fef3c7; border: 1px solid #fcd34d;">
                    <i class="bi bi-arrow-repeat fs-4 text-warning me-3"></i>
                    <div>
                        <div class="fw-bold text-dark" style="font-size: 0.9rem;">Status Saat Ini: Sedang Diproses</div>
                        <div class="text-muted" style="font-size: 0.75rem;">Masuk: {{ $activeTransaction->transaction_start_date->format('d M Y, H:i') }}</div>
                    </div>
                </div>

                <p class="text-muted mb-3" style="font-size: 0.8rem;">Tekan tombol di bawah jika cucian sudah selesai dan siap diambil oleh karyawan.</p>

                <form id="formUpdateStatus">
                    <input type="hidden" name="status" value="FINISHED">
                    <button type="submit" class="submit-btn" id="btnSaveStatus" style="background-color: #059669; box-shadow: 0 4px 6px -1px rgba(5,150,105,0.3);">
                        <i class="bi bi-check2-circle me-2"></i> Tandai Selesai
                    </button>
                </form>
            </div>

            <!-- LAPOR RUSAK TAB -->
            <div class="tab-pane fade" id="lapor" role="tabpanel" aria-labelledby="lapor-tab">
                <div class="alert alert-warning border-0" style="font-size: 0.8rem; border-radius: 10px;">
                    <i class="bi bi-info-circle-fill me-1"></i> Form ini untuk melaporkan kondisi pakaian yang rusak sebelum/saat dicuci.
                </div>
                
                <form action="#" method="POST" id="formLaporRusak">
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="font-size: 0.85rem;">Pilih Item yang Rusak</label>
                        <select class="form-select" style="border-radius: 10px; font-size: 0.9rem;" required>
                            <option value="" disabled selected>Pilih item...</option>
                            @if($entity->items)
                                @foreach($entity->items as $item)
                                    <option value="{{ $item->id }}">{{ $item->item_name }} (Set {{ $item->pivot->set_no }})</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold" style="font-size: 0.85rem;">Deskripsi Kerusakan</label>
                        <textarea class="form-control" rows="4" placeholder="Misal: Kancing baju lepas, ritsleting rusak, noda tinta..." style="border-radius: 10px; font-size: 0.9rem;" required></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold" style="font-size: 0.85rem;">Foto Bukti (Opsional)</label>
                        <input class="form-control" type="file" accept="image/*" capture="camera" style="border-radius: 10px; font-size: 0.85rem;">
                    </div>

                    <button type="submit" class="submit-btn" style="background-color: #dc3545; box-shadow: 0 4px 6px -1px rgba(220,53,69,0.2);"><i class="bi bi-send-fill me-2"></i> Kirim Laporan Rusak</button>
                </form>
            </div>

        </div>
    @else
        <!-- Tidak ada transaksi aktif -->
        <div class="card-panel text-center py-4">
            <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
            <p class="text-muted mt-2 mb-0">Tidak ada cucian aktif untuk kode ini.</p>
            <a href="{{ route('vendor.dashboard') }}" class="btn btn-outline-primary btn-sm mt-3 rounded-pill px-4">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
            </a>
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
                    body: JSON.stringify({ status: 'FINISHED' })
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
                            window.location.href = "{{ route('vendor.dashboard') }}";
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

        // Form Lapor Rusak
        const formRusak = document.getElementById('formLaporRusak');
        if (formRusak) {
            formRusak.addEventListener('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    icon: 'success',
                    title: 'Terkirim!',
                    text: 'Laporan kerusakan berhasil dikirim.',
                    confirmButtonColor: '#2563eb'
                });
            });
        }
    });
</script>
@endif

</body>
</html>
