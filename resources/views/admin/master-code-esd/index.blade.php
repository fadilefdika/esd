@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <style>
        :root { --primary-ems: #2563eb; --bg-body: #f8fafc; }
        .content-wrapper { padding: 1.5rem; }
        .card-main { border: 1px solid #e2e8f0; border-radius: 12px; background: #fff; }
        .card-header-custom { padding: 1.25rem 1.5rem; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; }
        .table thead th { background-color: #f8fafc; color: #64748b; font-size: 0.75rem; text-transform: uppercase; font-weight: 700; padding: 12px 16px; }
        .avatar-circle { width: 32px; height: 32px; background-color: #eff6ff; color: var(--primary-ems); display: flex; align-items: center; justify-content: center; border-radius: 8px; }
        .btn-action-group .btn { padding: 4px 8px; border-radius: 6px; color: #64748b; border: 1px solid #e2e8f0; background: #fff; }
        #esdCodeTable_wrapper .row { padding: 10px; }
    </style>

    <div class="content-wrapper">
        <div class="card-main"> 
            <div class="card-header-custom">
                <div>
                    <h5 class="fw-bold text-dark mb-0">Manajemen Kode ESD</h5>
                    <small class="text-muted">Ringkasan penggunaan kode asset per kategori</small>
                </div>
                <a href="{{ route('admin.code-esd.create') }}" class="btn btn-primary btn-sm px-3 fw-semibold">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Kode ESD
                </a>
            </div>

            <div class="table-responsive">
                <table id="esdCodeTable" class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th width="50">ID</th>
                            <th>Nama Kode</th>
                            <th class="text-center">Jumlah Karyawan</th>
                            {{-- <th>Terdaftar Pada</th> --}}
                            <th class="text-center" width="120">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($codeEsd as $code)
                            <tr>
                                <td class="text-muted">#{{ $code->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        {{-- <div class="avatar-circle me-3"><i class="bi bi-qr-code-scan"></i></div> --}}
                                        <div>
                                            <div class="fw-bold text-dark">{{ $code->name }}</div>
                                            <small class="text-muted">ESD Category</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge rounded-pill bg-primary px-3">
                                        {{ $code->jumlah_karyawan ?? 0 }} Orang
                                    </span>
                                </td>
                                {{-- <td>
                                    <div class="text-dark small">{{ $code->created_at->format('d M Y') }}</div>
                                    <small class="text-muted">Oleh ID: {{ $code->creator_id }}</small>
                                </td> --}}
                                <td class="text-center">
                                    <div class="btn-action-group">
                                        <a href="{{ route('admin.code-esd.edit', $code->id) }}" class="btn btn-sm" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.code-esd.destroy', $code->id) }}" method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm" onclick="return confirm('Hapus kode ESD ini?')">
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

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#esdCodeTable').DataTable({
                "pageLength": 10,
                "order": [], // Urutkan berdasarkan jumlah karyawan terbanyak secara default
                "language": { 
                    "search": "", 
                    "searchPlaceholder": "Cari kode (ATS, BJM...)" 
                }
            });
        });
    </script>
@endsection