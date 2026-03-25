<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Transaksi ESD - {{ $entity->code }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <!-- Using Google Fonts (Inter) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-ems: #2563eb;
            --primary-hover: #1d4ed8;
            --bg-body: #f8fafc;
        }

        body { 
            background-color: var(--bg-body); 
            font-family: 'Inter', sans-serif;
            color: #0f172a;
        }

        .card-main {
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            background: #fff;
            overflow: hidden;
            padding: 24px;
        }

        .header-box { 
            background-color: #eff6ff; 
            color: var(--primary-ems);
            display: inline-block; 
            padding: 4px 12px; 
            font-weight: 700; 
            border-radius: 6px;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
        }

        .form-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: #475569;
            margin-bottom: 0.4rem;
        }

        .form-control, .form-select {
            font-size: 0.9rem;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            padding: 10px 14px;
            color: #1e293b;
            box-shadow: none;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-ems);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .form-control[readonly] {
            background-color: #f1f5f9;
            color: #64748b;
        }

        .btn-submit { 
            background-color: var(--primary-ems); 
            color: white; 
            border: none; 
            padding: 10px 16px; 
            width: 100%; 
            font-weight: 600; 
            border-radius: 8px; 
            transition: all 0.2s ease; 
            box-shadow: 0 2px 4px -1px rgba(37, 99, 235, 0.2);
            font-size: 0.9rem;
            margin-top: 10px;
        }

        .btn-submit:hover { 
            background-color: var(--primary-hover); 
            color: white; 
            transform: translateY(-1px);
            box-shadow: 0 6px 8px -1px rgba(37, 99, 235, 0.3);
        }
        
        .btn-back {
            color: #64748b;
            font-size: 0.85rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            font-weight: 500;
            transition: color 0.2s;
            background: #f8fafc;
            padding: 6px 12px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
        }
        .btn-back:hover {
            color: #0f172a;
            background: #f1f5f9;
        }

    </style>
</head>
<body>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            
            <div class="d-flex justify-content-between align-items-center mb-4 px-1">
                <a href="{{ route('public.preview', $entity->code) }}" class="btn-back">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
                <div class="header-box">
                    <i class="bi bi-arrow-left-right me-1"></i> TRANSAKSI ESD
                </div>
            </div>

            <div class="card-main mb-4">
                <div class="text-center mb-4 pb-3 border-bottom" style="border-color: #f1f5f9 !important;">
                    <div class="d-inline-flex justify-content-center align-items-center bg-primary bg-opacity-10 text-primary rounded-circle mb-3" style="width: 56px; height: 56px;">
                        <i class="bi bi-clipboard2-check fs-3"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">Form Transaksi</h5>
                    <p class="text-muted mb-0" style="font-size: 0.8rem;">Silakan isi detail transaksi seragam Anda.</p>
                </div>

                <!-- Form UI Dummy -->
                <form action="#" method="POST" id="formTransaksi">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label">Code ESD</label>
                        <input type="text" class="form-control" name="code_esd" value="{{ $entity->code }}" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jenis Transaksi</label>
                        <select class="form-select" name="jenis_transaksi" required>
                            <option value="" selected disabled>Pilih jenis transaksi...</option>
                            <option value="Serah ke laundry">Serah ke laundry</option>
                            <option value="Ambil dari laundry">Ambil dari laundry</option>
                            <option value="Ganti rusak">Ganti rusak</option>
                            <option value="Kehilangan">Kehilangan</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Set / Paket ESD</label>
                        <select class="form-select" name="set_paket" required>
                            <option value="" selected disabled>Pilih Set...</option>
                            @php
                                $sets = [];
                                if($entity->items && $entity->items->count() > 0) {
                                    foreach($entity->items as $item) {
                                        if(!in_array($item->pivot->set_no, $sets)) {
                                            $sets[] = $item->pivot->set_no;
                                        }
                                    }
                                }
                                sort($sets);
                            @endphp
                            
                            @if(count($sets) > 0)
                                @foreach($sets as $setNo)
                                    <option value="{{ $setNo }}">Set ke-{{ $setNo }} (Paket {{ $entity->package }})</option>
                                @endforeach
                            @else
                                <option value="1">Set ke-1</option>
                            @endif
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Jumlah</label>
                        <input type="number" class="form-control" name="jumlah" value="1" min="1" required>
                    </div>

                    <button type="button" class="btn btn-submit" id="btnProses">
                        <i class="bi bi-send-check me-2"></i> Proses Transaksi
                    </button>
                </form>
            </div>

            <div class="text-center mt-4">
                <p class="text-muted mb-0" style="font-size: 0.75rem;">Astra Visteon Indonesia &copy; {{ date('Y') }}</p>
                <p class="text-muted" style="font-size: 0.7rem;">ESD Management System</p>
            </div>

        </div>
    </div>
</div>

<script>
    document.getElementById('btnProses').addEventListener('click', function() {
        const jenis = document.querySelector('select[name="jenis_transaksi"]').value;
        const set = document.querySelector('select[name="set_paket"]').value;
        const qty = document.querySelector('input[name="jumlah"]').value;
        
        if(!jenis || !set || !qty) {
            alert('Harap lengkapi semua field terlebih dahulu!');
            return;
        }

        alert(`[UI ONLY] Form disubmit:\nTransaksi: ${jenis}\nSet: ke-${set}\nJumlah: ${qty}\n\nFitur ini akan diintegrasikan dengan backend nanti.`);
    });
</script>

</body>
</html>
