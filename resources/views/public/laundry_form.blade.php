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

        /* Chrome, Safari, Edge, Opera */
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        
        /* Firefox */
        input[type=number] {
            -moz-appearance: textfield;
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
                    <h5 class="fw-bold text-dark mb-1">LAUNDRY SERAGAM ESD</h5>
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

                    <div class="mb-4">
                        <label class="form-label mb-3">Pilih Item yang Ditransaksikan</label>
                        @php
                            $groupedSets = [];
                            $uniqueItems = [];
                            if($entity->items && $entity->items->count() > 0) {
                                foreach($entity->items as $item) {
                                    $setNo = $item->pivot->set_no;
                                    if(!isset($groupedSets[$setNo])) {
                                        $groupedSets[$setNo] = [];
                                    }
                                    $groupedSets[$setNo][$item->item_name] = $item;
                                    
                                    if(!in_array($item->item_name, $uniqueItems)) {
                                        $uniqueItems[] = $item->item_name;
                                    }
                                }
                            }
                            ksort($groupedSets);
                        @endphp

                        <div class="table-responsive border rounded-3 p-2 bg-white shadow-sm">
                            <table class="table table-borderless align-middle text-center mb-0">
                                <thead style="border-bottom: 2px solid #f1f5f9;">
                                    <tr>
                                        <th class="text-start text-muted pb-3" style="font-weight: 600; font-size: 0.8rem; width: 35%;">SET EST</th>
                                        @foreach($uniqueItems as $itemName)
                                            <th class="text-muted pb-3" style="font-weight: 600; font-size: 0.8rem;">{{ strtoupper($itemName) }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($groupedSets) > 0)
                                        @foreach($groupedSets as $setNo => $itemsInSet)
                                            <tr style="border-bottom: 1px solid #f8fafc;">
                                                <td class="text-start py-3">
                                                    <div class="fw-bold text-dark" style="font-size: 0.9rem;">Set ke-{{ $setNo }}</div>
                                                    <div class="text-muted" style="font-size: 0.75rem;">Paket {{ $entity->package }}</div>
                                                </td>
                                                @foreach($uniqueItems as $itemName)
                                                    <td class="py-3">
                                                        @if(isset($itemsInSet[$itemName]))
                                                            @php $item = $itemsInSet[$itemName]; @endphp
                                                            <div class="form-check d-flex justify-content-center m-0">
                                                                <input class="form-check-input item-checkbox shadow-none" 
                                                                    type="checkbox" 
                                                                    name="items[]" 
                                                                    value="{{ $item->id }}" 
                                                                    data-label="{{ $item->item_name }} (Set {{ $setNo }})" 
                                                                    id="item-{{ $setNo }}-{{ $item->id }}"
                                                                    style="width: 22px; height: 22px; cursor: pointer; border-color: #cbd5e1;">
                                                            </div>
                                                        @else
                                                            <span class="text-muted opacity-50">-</span>
                                                        @endif
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="{{ count($uniqueItems) + 1 }}" class="text-center text-muted py-4" style="font-size: 0.85rem;">Tidak ada item terdaftar.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="mb-4 d-none" id="jumlahContainer">
                        <label class="form-label">Jumlah</label>
                        <input type="number" 
                            class="form-control" 
                            name="jumlah" 
                            value="1" 
                            min="1" 
                            oninput="if(this.value < 1) this.value = 1" 
                            required>
                    </div>

                    <button type="button" class="btn btn-submit" id="btnProses">
                        <i class="bi bi-send-check me-2"></i> Proses Transaksi
                    </button>
                </form>
            </div>

            <div class="text-center mt-4 mb-5">
                <p class="text-muted mb-0" style="font-size: 0.75rem;">Astra Visteon Indonesia &copy; {{ date('Y') }}</p>
                <p class="text-muted" style="font-size: 0.7rem;">ESD Management System</p>
            </div>

        </div>
    </div>
</div>

<!-- Modal Konfirmasi / Struk -->
<div class="modal fade" id="strukModal" tabindex="-1" aria-labelledby="strukModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow" style="border-radius: 16px;">
      <div class="modal-header border-bottom-0 bg-light" style="border-radius: 16px 16px 0 0;">
        <h5 class="modal-title fw-bold" id="strukModalLabel">
            <i class="bi bi-receipt me-2 text-primary"></i>Struk Transaksi
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <div class="struk-header text-center mb-4 border-bottom pb-3">
            <h6 class="fw-bold mb-1">ESD MANAGEMENT SYSTEM</h6>
            <p class="text-muted mb-0" style="font-size: 0.8rem;">Astra Visteon Indonesia</p>
        </div>
        
        <table class="table table-borderless table-sm mb-4" style="font-size: 0.9rem;">
            <tr>
                <td class="text-muted" width="40%">Code ESD</td>
                <td class="fw-semibold text-end" id="struk-code">{{ $entity->code }}</td>
            </tr>
            <tr>
                <td class="text-muted">Nama Karyawan</td>
                <td class="fw-semibold text-end text-truncate" style="max-width: 150px;">{{ $entity->employee_name ?? 'Available' }}</td>
            </tr>
            <tr>
                <td class="text-muted">Departemen</td>
                <td class="fw-semibold text-end">{{ $entity->dept_name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="text-muted">Jenis Transaksi</td>
                <td class="fw-semibold text-end text-primary" id="struk-jenis"></td>
            </tr>
            <tr>
                <td class="text-muted">Tanggal</td>
                <td class="fw-semibold text-end">
                    {{ now()->setTimezone('Asia/Jakarta')->format('d M Y H:i') }} WIB
                </td>
            </tr>
        </table>

        <p class="fw-bold mb-2" style="font-size: 0.85rem; color: #64748b;">DETAIL ITEM:</p>
        <div class="bg-light p-3 rounded-3 mb-4">
            <ul class="list-unstyled mb-0" id="struk-items" style="font-size: 0.9rem;">
                <!-- Items will be injected here via JS -->
            </ul>
        </div>

        <div class="d-flex justify-content-between align-items-center fw-bold px-2 mb-2">
            <span>TOTAL ITEM:</span>
            <span id="struk-total" class="fs-5">0</span>
        </div>
      </div>
      <div class="modal-footer border-top-0 pt-0 px-4 pb-4 flex-column">
        <button type="button" class="btn btn-primary w-100 mb-2" id="btnSubmitFinal" style="border-radius: 8px;">
            <i class="bi bi-check-circle me-2"></i> Konfirmasi & Submit
        </button>
        <button type="button" class="btn btn-light w-100 m-0" data-bs-dismiss="modal" style="border-radius: 8px; color: #64748b;">Kembali Edit</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        let strukModal;
        if(typeof bootstrap !== 'undefined') {
            strukModal = new bootstrap.Modal(document.getElementById('strukModal'));
        }

        document.getElementById('btnProses').addEventListener('click', function() {
            const jenis = document.querySelector('select[name="jenis_transaksi"]').value;
            
            let checkedItems = [];
            document.querySelectorAll('.item-checkbox:checked').forEach(cb => {
                checkedItems.push(cb.dataset.label);
            });
            
            if(!jenis) {
                alert('Harap pilih Jenis Transaksi terlebih dahulu!');
                return;
            }

            if(checkedItems.length === 0) {
                alert('Pilih minimal 1 item yang ingin ditransaksikan!');
                return;
            }

            // Populate Modal / Struk
            document.getElementById('struk-jenis').innerText = jenis;
            
            const ul = document.getElementById('struk-items');
            ul.innerHTML = '';
            checkedItems.forEach(item => {
                ul.innerHTML += `<li class="mb-2 d-flex justify-content-between align-items-center border-bottom pb-1 border-light">
                                    <span class="text-dark">${item}</span> 
                                    <span class="badge bg-secondary rounded-pill">1x</span>
                                 </li>`;
            });
            
            document.getElementById('struk-total').innerText = checkedItems.length;

            if(strukModal) strukModal.show();
            else alert('Error memuat bootstrap javascript untuk struk modal.');
        });

        document.getElementById('btnSubmitFinal').addEventListener('click', function() {
            alert('Sukses! Data (bersama detail checkbox item) berhasil di-submit ke backend.');
            if(strukModal) strukModal.hide();
            // document.getElementById('formTransaksi').submit(); // Tunggu backend siap
        });
    });
</script>

</body>
</html>
