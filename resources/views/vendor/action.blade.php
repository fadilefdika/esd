<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Vendor Scan - EMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { background-color: #f8fafc; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; padding-bottom: 24px; }
        .app-navbar { background: white; padding: 16px; border-bottom: 1px solid #e2e8f0; position: sticky; top: 0; z-index: 10; display: flex; align-items: center; gap: 16px; }
        .back-btn { color: #64748b; font-size: 1.25rem; text-decoration: none; }
        .card-panel { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.03); border: 1px solid #f1f5f9; margin-bottom: 16px; }
        .item-list { list-style: none; padding: 0; margin: 0; }
        .item-list li { padding: 8px 12px; border-bottom: 1px solid #f1f5f9; font-size: 0.85rem; display: flex; justify-content: space-between; align-items: center; }
        .item-list li:last-child { border-bottom: none; }
        .result-icon { font-size: 3.5rem; margin-bottom: 12px; }
    </style>
</head>
<body>

<div class="app-navbar">
    <a href="{{ route('vendor.dashboard') }}" class="back-btn"><i class="bi bi-arrow-left"></i></a>
    <h6 class="mb-0 fw-bold">Hasil Scan</h6>
</div>

<div class="container px-3 py-4">

    {{-- Info Singkat Entity --}}
    <div class="card-panel mb-3">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <h5 class="fw-bold mb-0 text-dark">{{ $entity->code }}</h5>
            @if($activeTransaction && $activeTransaction->transaction_status === 'OPEN')
                <span class="badge bg-warning text-dark px-2 py-1"><i class="bi bi-hourglass-split"></i> Di Laundry</span>
            @else
                <span class="badge bg-secondary px-2 py-1">Tidak Ada Cucian</span>
            @endif
        </div>
        <p class="text-muted mb-0" style="font-size: 0.82rem;">
            <i class="bi bi-person badge bg-light text-dark p-1 me-1"></i>
            {{ $entity->employee_name ?? 'Available' }} ({{ $entity->dept_name ?? '-' }})
        </p>
    </div>

    {{-- Hasil Proses Scan --}}
    @if($scanResult === 'processed')
        {{-- Sukses: Baru saja diproses --}}
        <div class="card-panel text-center py-4">
            <div class="result-icon text-success">✅</div>
            <h5 class="fw-bold text-success mb-1">Laundry Dicatat!</h5>
            <p class="text-muted mb-3" style="font-size: 0.85rem;">
                Seragam berhasil dicatat masuk ke laundry. Kode transaksi:
                <span class="fw-bold text-dark">{{ $activeTransaction->transaction_code }}</span>
            </p>

            {{-- Daftar set yang dicatat --}}
            @if(!empty($availableSets))
                <div class="bg-light rounded-3 overflow-hidden text-start mb-3">
                    <ul class="item-list">
                        @foreach($availableSets as $setNo => $items)
                            <li>
                                <span class="fw-semibold">Set {{ $setNo }}</span>
                                <span class="badge bg-warning text-dark">{{ count($items) }} item → LAUNDRY</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <a href="{{ route('vendor.dashboard') }}" class="btn btn-primary w-100 fw-semibold" style="border-radius: 10px;">
                <i class="bi bi-arrow-left me-2"></i> Kembali ke Dashboard
            </a>
        </div>

    @elseif($scanResult === 'already_in_laundry')
        {{-- Sudah di laundry sebelumnya --}}
        <div class="card-panel text-center py-4">
            <div class="result-icon text-warning">🧺</div>
            <h5 class="fw-bold text-warning mb-1">Sudah di Laundry</h5>
            <p class="text-muted mb-3" style="font-size: 0.85rem;">
                Seragam karyawan ini sedang dalam proses laundry sejak
                <strong>{{ $activeTransaction?->created_at?->format('d M Y, H:i') ?? '-' }}</strong>.
            </p>

            @if($activeTransaction && $activeTransaction->items && $activeTransaction->items->count() > 0)
                <div class="bg-light rounded-3 overflow-hidden text-start mb-3">
                    <ul class="item-list">
                        @foreach($activeTransaction->items as $item)
                            <li>
                                <span>{{ $item->item_name }}</span>
                                <span class="badge bg-warning text-dark">Di Laundry</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <a href="{{ route('vendor.dashboard') }}" class="btn btn-secondary w-100 fw-semibold" style="border-radius: 10px;">
                <i class="bi bi-arrow-left me-2"></i> Kembali ke Dashboard
            </a>
        </div>

    @elseif($scanResult === 'error')
        {{-- Error saat proses --}}
        <div class="card-panel text-center py-4">
            <div class="result-icon text-danger">❌</div>
            <h5 class="fw-bold text-danger mb-1">Gagal Memproses</h5>
            <p class="text-muted mb-3" style="font-size: 0.85rem;">Terjadi kesalahan saat mencatat transaksi. Coba scan ulang atau hubungi Admin.</p>
            <a href="{{ route('vendor.dashboard') }}" class="btn btn-danger w-100 fw-semibold" style="border-radius: 10px;">
                <i class="bi bi-arrow-left me-2"></i> Kembali
            </a>
        </div>

    @else
        {{-- Tidak ada item yang bisa diproses (semua rusak/hilang) --}}
        <div class="card-panel text-center py-4">
            <div class="result-icon text-secondary">📦</div>
            <h5 class="fw-bold text-secondary mb-1">Tidak Ada Item</h5>
            <p class="text-muted mb-3" style="font-size: 0.85rem;">Semua item karyawan ini berstatus rusak/hilang dan tidak dapat dilaundry.</p>
            <a href="{{ route('vendor.dashboard') }}" class="btn btn-secondary w-100 fw-semibold" style="border-radius: 10px;">
                <i class="bi bi-arrow-left me-2"></i> Kembali
            </a>
        </div>
    @endif

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
