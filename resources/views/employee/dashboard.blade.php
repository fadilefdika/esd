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
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold text-dark mb-0">Paket Saya: {{ $entity->package ?? 'Standar' }}</h6>
            <span class="badge bg-primary px-2 py-1">{{ $entity->code }}</span>
        </div>

        {{-- Banner: Sedang di Laundry --}}
        @if($isInLaundry)
            <div class="alert laundry-banner border-0 mb-3 d-flex align-items-center px-3 py-3">
                <span class="spinner-grow spinner-grow-sm text-warning me-2" role="status"></span>
                <span class="fw-semibold text-dark" style="font-size: 0.85rem;">Sebagian atau seluruh seragam Anda sedang dalam proses laundry.</span>
            </div>
        @endif

        @forelse($sets as $setNo => $items)
            @php
                $setStatus = 'Tersedia';
                $badgeClass = 'bg-success';
                $borderColor = '#10b981';
                
                foreach($items as $it) {
                    $status = strtoupper($it->pivot->status ?? '');
                    if(in_array($status, ['LAUNDRY', 'DIPROSES'])) {
                        $setStatus = 'Sedang di Laundry';
                        $badgeClass = 'bg-warning text-dark';
                        $borderColor = '#f59e0b';
                        break;
                    }
                }

            @endphp
            <div class="card border-0 shadow-sm mb-3" style="border-radius: 12px; border-left: 4px solid {{ $borderColor }} !important;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="fw-bold mb-0" style="font-size: 0.95rem;">Set {{ $setNo }}</h6>
                        <span class="badge {{ $badgeClass }}">{{ $setStatus }}</span>
                    </div>
                    
                    <div class="text-muted d-flex flex-wrap gap-1" style="font-size: 0.8rem;">
                        @foreach($items as $i)
                            <span class="badge bg-light text-dark border">{{ $i->item_name }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        @empty
            <div class="alert alert-warning border-0" style="border-radius: 12px;">
                Belum ada item ESD yang ditugaskan ke Anda.
            </div>
        @endforelse

        <div class="d-flex gap-2 mt-3">
            <a href="#" class="btn btn-outline-danger w-50 py-2 shadow-sm" style="border-radius: 12px; font-weight: 600; font-size: 0.85rem;" onclick="Swal.fire('Fitur Segera Hadir', 'Form Pelaporan sedang dikembangkan oleh Admin.', 'info')">
                <i class="bi bi-exclamation-triangle-fill me-1"></i> Lapor Rusak/Hilang
            </a>
            <a href="#" class="btn btn-primary w-50 py-2 shadow-sm" style="border-radius: 12px; font-weight: 600; font-size: 0.85rem;" onclick="Swal.fire('Fitur Segera Hadir', 'Form Penggantian Baru sedang dikembangkan oleh Admin.', 'info')">
                <i class="bi bi-arrow-repeat me-1"></i> Pengajuan Baru
            </a>
        </div>

    @else
        <div class="card border-0 shadow-sm" style="border-radius: 16px;">
            <div class="card-body text-center py-5 px-3">
                <div class="mb-4 text-danger">
                    <i class="bi bi-exclamation-triangle-fill" style="font-size: 4rem; opacity: 0.9;"></i>
                </div>
                <h5 class="fw-bold text-dark mb-3">Data ESD Tidak Ditemukan</h5>
                <p class="text-muted mb-0" style="font-size: 0.9rem;">
                    Sistem tidak dapat menemukan alokasi pakaian ESD yang terhubung dengan NPK Anda. Silakan hubungi Admin.
                </p>
            </div>
        </div>
    @endif
</div>

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

        // Pickup confirmation with SweetAlert
        const formPickup = document.getElementById('formPickup');
        if (formPickup) {
            formPickup.addEventListener('submit', function(e) {
                e.preventDefault();
                const form = this;
                Swal.fire({
                    title: 'Konfirmasi Pengambilan',
                    text: 'Apakah Anda sudah mengambil seragam dari laundry?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#059669',
                    cancelButtonColor: '#94a3b8',
                    confirmButtonText: 'Ya, Sudah Diambil',
                    cancelButtonText: 'Belum'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        }
    });
</script>
</body>
</html>
