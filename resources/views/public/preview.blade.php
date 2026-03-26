<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EMS</title>
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

        .info-table { 
            width: 100%; 
            background: white; 
            border-collapse: collapse; 
        }

        .info-table td { 
            padding: 8px 12px; 
            border-bottom: 1px solid #f1f5f9; 
            font-size: 0.8rem;
        }
        
        .info-table tr:last-child td {
            border-bottom: none;
        }

        .label-col { 
            width: 35%; 
            font-weight: 600; 
            color: #64748b; 
        }

        .value-col { 
            width: 65%; 
            font-weight: 500;
            color: #1e293b;
        }

        .badge-code { 
            background-color: #eff6ff; 
            color: var(--primary-ems); 
            font-weight: 700; 
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            border: 1px solid #bfdbfe;
            display: inline-block;
        }

        .badge-status {
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: 600;
            font-size: 0.7rem;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .badge-status-active { background: #dcfce7; color: #15803d; }
        .badge-status-inactive { background: #fee2e2; color: #b91c1c; }
        .badge-status-available { background: #fef9c3; color: #a16207; }

        .btn-submit { 
            background-color: var(--primary-ems); 
            color: white; 
            border: none; 
            padding: 8px 12px; 
            width: 100%; 
            font-weight: 600; 
            border-radius: 8px; 
            transition: all 0.2s ease; 
            box-shadow: 0 2px 4px -1px rgba(37, 99, 235, 0.2);
            font-size: 0.85rem;
        }

        .btn-submit:hover { 
            background-color: var(--primary-hover); 
            color: white; 
            transform: translateY(-1px);
            box-shadow: 0 6px 8px -1px rgba(37, 99, 235, 0.3);
        }

        .avatar-circle-lg {
            width: 48px;
            height: 48px;
            background-color: #eff6ff;
            color: var(--primary-ems);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            font-size: 1.4rem;
            margin: 0 auto 10px;
        }

        .timeline-wrapper {
            max-height: 250px;
            overflow-y: auto;
            padding-right: 8px;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 transparent;
        }

        .timeline-wrapper::-webkit-scrollbar {
            width: 5px;
        }

        .timeline-wrapper::-webkit-scrollbar-track {
            background: transparent;
        }

        .timeline-wrapper::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 10px;
        }
    </style>
</head>
<body>

<div class="container py-3">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            
            <div class="d-flex justify-content-between align-items-center mb-3 px-2">
                <div class="d-flex align-items-center">
                    <div class="header-box me-2">
                        <i class="bi bi-shield-check me-1"></i> ESD INFO
                    </div>
                </div>
                <div>
                    @if($entity->status == 'AKTIF')
                        <span class="badge-status badge-status-active">
                            <i class="bi bi-check-circle-fill"></i> Aktif
                        </span>
                    @elseif($entity->status == 'AVAILABLE')
                        <span class="badge-status badge-status-available">
                            <i class="bi bi-box-seam-fill"></i> Available
                        </span>
                    @else
                        <span class="badge-status badge-status-inactive">
                            <i class="bi bi-x-circle-fill"></i> Non-Aktif
                        </span>
                    @endif
                </div>
            </div>
            
            <div class="card-main mb-3">
                <div class="text-center pt-3 pb-2 border-bottom" style="border-color: #f1f5f9 !important;">
                    <div class="avatar-circle-lg">
                        <i class="bi bi-person-badge"></i>
                    </div>
                    @if($entity->employee_name && $entity->npk)
                        <h6 class="fw-bold text-dark mb-1">{{ $entity->employee_name }}</h6>
                        <p class="text-muted mb-2" style="font-size: 0.70rem;">NPK: {{ $entity->npk }}</p>
                    @else
                        <h6 class="fw-bold text-success fst-italic mb-2">Tersedia (Available)</h6>
                    @endif
                </div>

                <div class="text-center mb-3">
                    @if($status === 'IN LAUNDRY')
                        <div class="d-inline-flex align-items-center px-3 py-1 rounded-pill bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25">
                            <span class="spinner-grow spinner-grow-sm me-2" role="status"></span>
                            <span class="fw-bold" style="font-size: 0.75rem;">SEDANG DI LAUNDRY</span>
                        </div>
                    @else
                        <div class="d-inline-flex align-items-center px-3 py-1 rounded-pill bg-success bg-opacity-10 text-success border border-success border-opacity-25">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            <span class="fw-bold" style="font-size: 0.75rem;">AVAILABLE / READY</span>
                        </div>
                    @endif
                </div>

                <table class="info-table">
                    <tr>
                        <td class="label-col">System Code</td>
                        <td class="value-col">
                            <span class="badge-code">{{ $entity->code }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="label-col">Kategori</td>
                        <td class="value-col">{{ $entity->category ?? 'Staff' }}</td>
                    </tr>
                    <tr>
                        <td class="label-col">Departemen</td>
                        <td class="value-col">{{ $entity->dept_name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label-col">No. Loker</td>
                        <td class="value-col">{{ $entity->no_loker ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label-col">Paket ESD</td>
                        <td class="value-col">
                            @if($entity->package)
                                <span class="badge bg-secondary px-2 py-1">{{ $entity->package }}</span>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="label-col">Ukuran Setup</td>
                        <td class="value-col">
                            @php 
                                // Mengambil size dari item pertama (biasanya Baju/Celana)
                                $itemSize = '-';
                                if($entity->items && $entity->items->count() > 0) {
                                    $pivot = $entity->items->first()->pivot;
                                    $itemSize = $pivot->size ?? '-';
                                }
                            @endphp
                            <span class="fw-bold">{{ $itemSize }}</span>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Timeline Log -->
            <div class="card-main p-3 mt-4 mb-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold text-dark mb-0" style="font-size: 0.85rem;"><i class="bi bi-clock-history me-2 text-primary"></i>Riwayat Transaksi</h6>
                    <span class="badge bg-light text-secondary border" style="font-size: 0.7rem;">{{ $histories->count() }}</span>
                </div>
                
                <div class="timeline-wrapper">
                    <div class="timeline-container ps-3" style="border-left: 2px solid #e2e8f0; margin-left: 8px;">
                        
                        @forelse($histories as $log)
                            <div class="timeline-item mb-3" style="position: relative;">
                                <div style="position: absolute; left: -18px; top: 5px; width: 10px; height: 10px; border-radius: 50%; 
                                    background: {{ $log->transaction_status == 'OPEN' ? '#f59e0b' : '#10b981' }}; border: 2px solid #fff;">
                                </div>
                                
                                <div class="ps-2">
                                    <div class="fw-bold text-dark" style="font-size: 0.8rem;">
                                        {{ $log->transaction_type }}
                                        @if($log->transaction_status == 'OPEN')
                                            <small class="text-warning fw-normal">(Proses)</small>
                                        @endif
                                    </div>
                                    <div class="text-muted" style="font-size: 0.7rem;">
                                        {{ $log->transaction_start_date->format('d M Y, H:i') }} - 
                                        <span class="text-primary">{{ $log->creator->name ?? 'System' }}</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-3 text-muted" style="font-size: 0.75rem;">
                                Belum ada riwayat transaksi.
                            </div>
                        @endforelse

                    </div>
                </div>
            </div>

            <!-- Authenticaton / Check Auth  -->
            <div class="mt-4 border-top pt-3 text-center">
                @auth
                    @if(auth()->user()->role === 'admin' || auth()->user()->role === 'employee' || Auth::guard('web')->check() || Auth::guard('admin')->check())
                        <a href="{{ route('public.laundry.form', $entity->code) }}" class="btn btn-submit">
                            <i class="bi bi-file-earmark-text me-2"></i> Form Transaksi ESD
                        </a>
                    @else
                        <p class="text-muted" style="font-size: 0.8rem;">Role Anda tidak memiliki akses transaksi.</p>
                    @endif
                @else
                    <p class="text-muted mb-2" style="font-size: 0.75rem;">Harap login untuk memproses transaksi laundry baju Anda.</p>
                    <a href="{{ route('public.laundry.form', $entity->code) }}" class="btn btn-outline-primary w-100" style="border-radius: 8px; font-size: 0.85rem; font-weight: 600;">
                        <i class="bi bi-box-arrow-in-right me-2"></i> Lanjut Login & Form Transaksi
                    </a>
                @endauth
            </div>

            <div class="text-center mt-5">
                <p class="text-muted mb-0" style="font-size: 0.75rem;">Astra Visteon Indonesia &copy; {{ date('Y') }}</p>
                <p class="text-muted" style="font-size: 0.7rem;">ESD Management System</p>
            </div>

        </div>
    </div>
</div>

</body>
</html>