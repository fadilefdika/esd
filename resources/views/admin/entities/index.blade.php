@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <style>
        :root {
            --primary-ems: #2563eb;
            --bg-body: #f8fafc;
        }

        /* Container & Card Styling */
        .content-wrapper { padding: 1.5rem; }
        
        .card-main {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
            background: #fff;
        }

        .card-header-custom {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Table Design */
        .table thead th {
            background-color: #f8fafc;
            color: #64748b;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 700;
            border-top: none;
            padding: 12px 16px;
        }

        .table td {
            padding: 10px 16px;
            font-size: 0.875rem;
            border-bottom: 1px solid #f1f5f9;
        }

        /* Avatar Soft */
        .avatar-circle {
            width: 32px;
            height: 32px;
            background-color: #eff6ff;
            color: var(--primary-ems);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            font-size: 1rem;
        }

        /* Status Badges */
        .badge-soft {
            padding: 4px 10px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.75rem;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .badge-soft-success { background: #dcfce7; color: #15803d; }
        .badge-soft-danger { background: #fee2e2; color: #b91c1c; }

        /* Action Buttons */
        .btn-action-group .btn {
            padding: 4px 8px;
            border-radius: 6px;
            color: #64748b;
            transition: all 0.2s;
            border: 1px solid #e2e8f0;
            background: #fff;
            margin: 0 2px;
        }
        .btn-action-group .btn:hover {
            background: #f8fafc;
            color: var(--primary-ems);
            border-color: var(--primary-ems);
        }

        /* QR Micro Style */
        .qr-mini-box {
            padding: 4px;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            display: inline-block;
            background: #fff;
            transition: transform 0.2s;
        }
        .qr-mini-box:hover { transform: scale(1.05); cursor: pointer; }

        /* Customizing DataTable Search */
        .dataTables_filter input {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 5px 12px;
            font-size: 0.85rem;
        }
    </style>

    <div class="content-wrapper">
        <div class="card-main">
            <div class="card-header-custom">
                <div>
                    <h5 class="fw-bold text-dark mb-0">Alokasi Asset ESD</h5>
                </div>
                
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-primary btn-sm px-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#importModal">
                        <i class="bi bi-file-earmark-excel me-1"></i>
                        Import Excel
                    </button>

                    <a href="{{ route('admin.entities.download-all-qr') }}"
                        class="btn btn-outline-success btn-sm px-3 fw-semibold btn-download-all">
                        <i class="bi bi-qr-code me-1"></i>
                        Download All QR
                    </a>

                    <a href="{{ route('admin.entities.create') }}"
                        class="btn btn-primary btn-sm px-3 fw-semibold">
                        <i class="bi bi-plus-lg me-1"></i>
                        Tambah Data
                    </a>
                </div>
            </div>

            <div class="table-responsive">
                <table id="entityTable" class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th width="40">No</th>
                            <th>Info Karyawan</th>
                            <th class="text-center">ID / QR</th>
                            <th>Departemen</th>
                            <th class="text-center">Status</th>
                            <th class="text-center" width="120">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($entities as $entity)
                            <tr>
                                <td class="text-muted small">{{ $loop->iteration }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle me-3">
                                            <i class="bi bi-person"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $entity->employee_name }}</div>
                                            <div class="text-muted" style="font-size: 0.75rem;">NPK: {{ $entity->npk }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="qr-mini-box btn-view-qr shadow-sm" 
                                         data-qr-code="{{ $entity->id }}" 
                                         data-name="{{ $entity->employee_name }}"
                                         data-npk="{{ $entity->npk }}">
                                        {!! QrCode::size(32)->generate(url('/preview/' . $entity->id)) !!}
                                    </div>
                                    <div class="mt-1">
                                        <a href="{{ route('admin.entities.download-qr', $entity->id) }}" 
                                           class="text-primary fw-bold text-decoration-none" style="font-size: 0.6rem;">
                                            <i class="bi bi-download"></i> DOWNLOAD
                                        </a>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-dark fw-medium">{{ $entity->dept_name ?? '-' }}</span>
                                </td>
                                <td class="text-center">
                                    @if($entity->status == 'AKTIF')
                                        <span class="badge-soft badge-soft-success">
                                            <i class="bi bi-check-circle-fill"></i> Aktif
                                        </span>
                                    @else
                                        <span class="badge-soft badge-soft-danger">
                                            <i class="bi bi-x-circle-fill"></i> Non-Aktif
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-action-group">
                                        <a href="{{ route('admin.entities.copy', $entity->id) }}" class="btn btn-sm" title="Copy">
                                            <i class="bi bi-copy"></i>
                                        </a>
                                        <a href="{{ route('admin.entities.edit', $entity->id) }}" class="btn btn-sm" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.entities.destroy', $entity->id) }}" method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-delete">
                                                <i class="bi bi-trash text-danger"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-3"></div>
        </div>
    </div>

    <div class="modal fade" id="qrViewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-body text-center p-4">
                    <div class="mb-3 p-2 bg-white d-inline-block border rounded-3" id="qrContainerLarge"></div>
                    <h6 class="fw-bold mb-1 mt-2" id="qrNameTitle"></h6>
                    <p class="text-muted small mb-4" id="qrNpkSubtitle"></p>
                    <button type="button" class="btn btn-primary w-100 rounded-pill" data-bs-dismiss="modal">Selesai</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.entities.importExcel') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" id="importModalLabel">Import Data Aset ESD</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info py-2 small">
                            <i class="bi bi-info-circle me-1"></i>
                            Pastikan nama tab sesuai: Manufacturing, Quality, HRGA & EHS, dll.
                        </div>
                        <div class="mb-3">
                            <label for="file" class="form-label small fw-bold">Pilih File Excel (.xlsx)</label>
                            <input class="form-control form-control-sm" type="file" id="file" name="file" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm px-4">Mulai Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#entityTable').DataTable({
                "pageLength": 10,
                "dom": '<"d-flex justify-content-between align-items-center p-3"f>rt<"d-flex justify-content-between align-items-center p-3"ip>',
                "language": {
                    "search": "",
                    "searchPlaceholder": "Cari Karyawan...",
                    "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data"
                }
            });

            $('.btn-view-qr').on('click', function() {
                const qrLink = $(this).data('qr-code');
                $('#qrNameTitle').text($(this).data('name'));
                $('#qrNpkSubtitle').text('NPK: ' + $(this).data('npk'));
                $('#qrContainerLarge').html('');
                new QRCode(document.getElementById("qrContainerLarge"), {
                    text: qrLink,
                    width: 180,
                    height: 180,
                    colorDark : "#0f172a",
                    correctLevel : QRCode.CorrectLevel.H
                });
                $('#qrViewModal').modal('show');
            });

            // SweetAlert for Download All QR
            $('.btn-download-all').on('click', function(e) {
                e.preventDefault();
                const url = $(this).attr('href');
                Swal.fire({
                    title: 'Download QR?',
                    text: 'Download semua QR ({{ $entities->count() }} data)?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#2563eb',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Ya, Download!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = url;
                    }
                });
            });

            // SweetAlert for Delete Action
            $('.btn-delete').on('click', function(e) {
                e.preventDefault();
                const form = $(this).closest('form');
                Swal.fire({
                    title: 'Hapus Data?',
                    text: 'Apakah Anda yakin ingin menghapus data ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
@endsection