<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Employee Dashboard - EMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { background-color: #f8fafc; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; }
        .header-app { background: #2563eb; color: white; padding: 24px 20px; border-bottom-left-radius: 20px; border-bottom-right-radius: 20px; box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2); }
        
        .pickup-banner {
            background: linear-gradient(135deg, #ecfdf5, #d1fae5);
            border: 1px solid #6ee7b7;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 16px;
        }
        .pickup-banner .btn-pickup {
            background: #059669;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 10px 16px;
            font-weight: 600;
            font-size: 0.85rem;
            width: 100%;
            transition: all 0.2s;
            box-shadow: 0 2px 4px rgba(5, 150, 105, 0.3);
        }
        .pickup-banner .btn-pickup:hover {
            background: #047857;
            transform: translateY(-1px);
        }
        
        .laundry-banner {
            background: #fef3c7;
            border-radius: 12px;
            font-size: 0.85rem;
            border: 1px solid #fcd34d;
        }
    </style>
</head>
<body>

<div class="header-app mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-1 fw-bold">Halo, {{ $user->fullname ?? 'Karyawan' }}!</h5>
            <small class="text-white-50">NPK: {{ $user->npk ?? '-' }}</small>
        </div>
        
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-light btn-sm rounded-pill text-danger px-3 shadow-sm fw-bold">
                <i class="bi bi-box-arrow-right me-1"></i> Keluar
            </button>
        </form>
    </div>
</div>

<div class="container px-3">

    @if($entity)
        {{-- Header: Paket & Badge --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold text-dark mb-0" style="font-size: 0.9rem;">Paket Saya: {{ $entity->package ?? 'Standar' }}</h6>
            <span class="badge bg-primary px-2 py-1" style="font-size: 0.75rem;">{{ $entity->code }}</span>
        </div>

        {{-- Banner: Sedang di Laundry --}}
        @if($isInLaundry)
            <div class="alert border-0 mb-3 px-3 py-3 shadow-sm" style="background: #fef3c7; border: 1px solid #fcd34d; border-radius: 14px;">
                <div class="d-flex align-items-start mb-2">
                    <span class="spinner-grow spinner-grow-sm text-warning me-2 mt-1" role="status"></span>
                    @if($activeTransaction && $activeTransaction->transaction_status === 'READY')
                        <span class="fw-semibold text-dark" style="font-size: 0.85rem; line-height: 1.3;">Laundry Anda sudah selesai dicuci! Silakan ambil.</span>
                    @else
                        <span class="fw-semibold text-dark" style="font-size: 0.85rem; line-height: 1.3;">Sebagian atau seluruh seragam Anda sedang dalam proses laundry.</span>
                    @endif
                </div>

                @if($activeTransaction && $activeTransaction->transaction_status === 'OPEN')
                    <p class="mb-0 text-muted mt-1" style="font-size: 0.75rem;">Harap tunggu hingga Vendor menyelesaikan proses penyucian. Anda hanya bisa konfirmasi apabila statusnya siap diambil.</p>
                @else
                    <p class="mb-3 text-muted" style="font-size: 0.75rem;">Setelah seragam diterima kembali, konfirmasikan di bawah agar status kembali tersedia.</p>
                    <form action="{{ route('employee.laundry.confirm') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm w-100 fw-bold py-2" style="border-radius: 10px; font-size: 0.8rem;">
                            <i class="bi bi-check2-circle me-1"></i> Saya Sudah Terima Laundry
                        </button>
                    </form>
                @endif
            </div>
        @endif

        @forelse($sets as $setNo => $items)
            @php
                $setStatus   = 'Tersedia';
                $badgeClass  = 'bg-success';
                $borderColor = '#10b981';

                foreach($items as $it) {
                    $status = strtolower($it->pivot->status ?? '');
                    if (in_array($status, ['laundry', 'diproses'])) {
                        $setStatus   = 'Sedang di Laundry';
                        $badgeClass  = 'bg-warning text-dark';
                        $borderColor = '#f59e0b';
                        break;
                    } elseif ($status === 'rusak') {
                        $setStatus   = 'Rusak';
                        $badgeClass  = 'bg-danger';
                        $borderColor = '#ef4444';
                        break;
                    } elseif ($status === 'hilang') {
                        $setStatus   = 'Hilang';
                        $badgeClass  = 'bg-dark';
                        $borderColor = '#6b7280';
                        break;
                    }
                }
            @endphp
            
            <div class="card border-0 shadow-sm mb-3" style="border-radius: 14px; border-left: 5px solid {{ $borderColor }} !important;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0" style="font-size: 0.9rem;">Set {{ $setNo }}</h6>
                        <span class="badge {{ $badgeClass }}" style="font-size: 0.7rem;">{{ $setStatus }}</span>
                    </div>
                    
                    {{-- Grid Item: Diubah ke d-grid untuk mobile agar lebih rapi --}}
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($items as $i)
                            @php
                                $itemStatus = strtolower($i->pivot->status ?? '');
                                $isReported = in_array($itemStatus, ['hilang', 'rusak']);
                            @endphp
                            
                            @if($isReported)
                                <div class="badge {{ $itemStatus === 'hilang' ? 'bg-dark' : 'bg-danger' }} text-start p-2 border-0 shadow-sm w-100" style="border-radius: 10px; max-width: 100%;">
                                    <div class="fw-bold mb-1" style="font-size: 0.8rem;">
                                        <i class="bi {{ $itemStatus === 'hilang' ? 'bi-slash-circle' : 'bi-exclamation-triangle' }} me-1"></i>{{ $i->item_name }}
                                    </div>
                                    <div class="fw-normal opacity-75" style="font-size: 0.65rem; line-height: 1.2;">
                                        Dilaporkan {{ ucfirst($itemStatus) }} pada:<br>
                                        {{ $i->pivot->updated_at ? \Carbon\Carbon::parse($i->pivot->updated_at)->format('d M Y, H:i') : '-' }} WIB
                                    </div>
                                </div>
                            @else
                                <span class="badge bg-light text-dark border d-inline-flex align-items-center px-2 py-2" style="border-radius: 8px; font-size: 0.75rem; font-weight: 500;">
                                    {{ $i->item_name }}
                                </span>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        @empty
            <div class="alert alert-warning border-0 text-center py-4" style="border-radius: 14px; font-size: 0.85rem;">
                <i class="bi bi-inbox fs-2 d-block mb-2 opacity-50"></i>
                Belum ada item ESD yang ditugaskan ke Anda.
            </div>
        @endforelse

        {{-- Tombol Utama: Dibuat Full Width untuk Mobile agar mudah dijangkau --}}
        <div class="mt-4 mb-5">
            <button type="button" class="btn btn-outline-danger w-100 py-3 shadow-sm d-flex align-items-center justify-content-center" style="border-radius: 14px; font-weight: 700; font-size: 0.9rem;" data-bs-toggle="modal" data-bs-target="#reportModal">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> Lapor Kendala (Rusak/Hilang)
            </button>
        </div>

    @else
        {{-- Empty State --}}
        <div class="card border-0 shadow-sm" style="border-radius: 20px;">
            <div class="card-body text-center py-5 px-4">
                <div class="mb-4 text-danger">
                    <i class="bi bi-person-x-fill" style="font-size: 3.5rem; opacity: 0.8;"></i>
                </div>
                <h5 class="fw-bold text-dark mb-2">Data Tidak Ditemukan</h5>
                <p class="text-muted small mb-0">
                    Sistem tidak menemukan alokasi pakaian ESD untuk NPK Anda. Silakan hubungi bagian Admin atau Laundry.
                </p>
            </div>
        </div>
    @endif
</div>
{{-- Cukup panggil sub-folder setelah folder components --}}
<x-forms.form-report-damage-lost :sets="$sets" />

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Flash messages as SweetAlert
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                confirmButtonColor: '#2563eb'
            });
        @endif
        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Oops!',
                text: '{{ session('error') }}',
                confirmButtonColor: '#2563eb'
            });
        @endif

        const reportTypeEl = document.getElementById('report_type');
        const chronologySectionEl = document.getElementById('chronology_section');
        const chronologyInputEl = document.getElementById('chronology_input');

        if(reportTypeEl) {
            reportTypeEl.addEventListener('change', function() {
                if (this.value === 'hilang') {
                    // Tampilkan section kronologi
                    chronologySectionEl.classList.remove('d-none');
                    // Wajib diisi
                    chronologyInputEl.setAttribute('required', 'required');
                } else {
                    // Sembunyikan section kronologi
                    chronologySectionEl.classList.add('d-none');
                    // Tidak wajib diisi
                    chronologyInputEl.removeAttribute('required');
                    chronologyInputEl.value = ''; // Reset isinya
                }
            });
        }
    });

    function submitDemo() {
        const form = document.getElementById('reportForm');
        if(!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const reportModal = bootstrap.Modal.getInstance(document.getElementById('reportModal'));
        reportModal.hide();

        Swal.fire(
            'Laporan Terkirim (Demo)', 
            'UI Form berhasil dibuat! Integrasi backend untuk upload dan simpan segera menyusul.', 
            'success'
        );
    }
</script>
</body>
</html>
