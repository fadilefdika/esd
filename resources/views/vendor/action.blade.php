<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Vendor Action - EMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8fafc; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; padding-bottom: 24px; }
        .app-navbar { background: white; padding: 16px; border-bottom: 1px solid #e2e8f0; position: sticky; top: 0; z-index: 10; display: flex; align-items: center; gap: 16px; }
        .back-btn { color: #64748b; font-size: 1.25rem; text-decoration: none; }
        .card-panel { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.03); border: 1px solid #f1f5f9; margin-bottom: 16px; }
        .status-options .btn-check:checked + .btn { background-color: #eff6ff; color: #2563eb; border-color: #bfdbfe; font-weight: 600; }
        .status-options .btn { border-color: #e2e8f0; color: #475569; border-radius: 8px; justify-content: start; text-align: left; padding: 12px 16px; margin-bottom: 8px; }
        .submit-btn { background-color: #2563eb; color: white; border-radius: 12px; padding: 14px; font-weight: 600; width: 100%; border: none; font-size: 1rem; box-shadow: 0 4px 6px -1px rgba(37,99,235,0.2); }
        .nav-tabs .nav-link { color: #64748b; font-weight: 600; border: none; border-bottom: 2px solid transparent; padding-bottom: 12px; }
        .nav-tabs .nav-link.active { color: #2563eb; border-bottom: 2px solid #2563eb; background: transparent; }
    </style>
</head>
<body>

<div class="app-navbar">
    <a href="/vendor/dashboard" class="back-btn"><i class="bi bi-arrow-left"></i></a>
    <h6 class="mb-0 fw-bold">Detail Pekerjaan</h6>
</div>

<div class="container px-3 py-3">
    <!-- Info Singkat -->
    <div class="card-panel mb-3">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h5 class="fw-bold mb-0 text-dark">{{ $entity->code }}</h5>
            <span class="badge bg-warning text-dark px-2 py-1"><i class="bi bi-hourglass-split"></i> Diproses</span>
        </div>
        <p class="text-muted mb-0" style="font-size: 0.85rem;"><i class="bi bi-person badge bg-light text-dark p-1 me-1"></i> {{ $entity->employee_name ?? 'Available' }} ({{ $entity->dept_name ?? '-' }})</p>
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
            <h6 class="fw-bold text-dark mb-3">Ubah Status Cucian:</h6>
            <form action="#" method="POST" onsubmit="event.preventDefault(); alert('Status diperbarui!'); window.location.href='/vendor/dashboard';">
                <div class="d-flex flex-column status-options mb-4">
                    <input type="radio" class="btn-check" name="status" id="stat-proses" value="proses" checked>
                    <label class="btn btn-outline-secondary w-100" for="stat-proses">
                        <i class="bi bi-washing-machine fs-5 me-2 align-middle text-warning"></i> Sedang Dicuci / Diproses
                    </label>

                    <input type="radio" class="btn-check" name="status" id="stat-ready" value="ready">
                    <label class="btn btn-outline-secondary w-100" for="stat-ready">
                        <i class="bi bi-check2-circle fs-5 me-2 align-middle text-success"></i> Selesai (Siap Diambil)
                    </label>
                </div>
                <button type="submit" class="submit-btn"><i class="bi bi-save me-2"></i> Simpan Status</button>
            </form>
        </div>

        <!-- LAPOR RUSAK TAB -->
        <div class="tab-pane fade" id="lapor" role="tabpanel" aria-labelledby="lapor-tab">
            <div class="alert alert-warning border-0" style="font-size: 0.8rem; border-radius: 10px;">
                <i class="bi bi-info-circle-fill me-1"></i> Form ini untuk melaporkan kondisi pakaian yang rusak sebelum/saat dicuci.
            </div>
            
            <form action="#" method="POST" onsubmit="event.preventDefault(); alert('Laporan kerusakan berhasil dikirim!');">
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
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
