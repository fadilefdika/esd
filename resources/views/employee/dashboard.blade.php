<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Employee Dashboard - EMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8fafc; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; }
        .header-app { background: #2563eb; color: white; padding: 24px 20px; border-bottom-left-radius: 20px; border-bottom-right-radius: 20px; box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2); }
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

        @forelse($sets as $setNo => $items)
            @php
                // Cek status sederhana: Jika ada item yang punya status LAUNDRY/DIPROSES, anggap setnya di laundry.
                $setStatus = 'Tersedia';
                $badgeClass = 'bg-success';
                
                foreach($items as $it) {
                    $status = strtoupper($it->pivot->status ?? '');
                    if(in_array($status, ['LAUNDRY', 'DIPROSES'])) {
                        $setStatus = 'Sedang di Laundry';
                        $badgeClass = 'bg-warning text-dark';
                        break;
                    }
                }
            @endphp
            <div class="card border-0 shadow-sm mb-3" style="border-radius: 12px; border-left: 4px solid {{ $setStatus === 'Tersedia' ? '#10b981' : '#f59e0b' }} !important;">
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

        <a href="{{ route('public.laundry.form', $entity->code) }}" class="btn btn-primary w-100 py-3 mt-2 shadow-sm" style="border-radius: 12px; font-weight: 600;">
            <i class="bi bi-basket-fill me-2"></i> Ajukan Laundry Baru
        </a>
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
</body>
</html>
