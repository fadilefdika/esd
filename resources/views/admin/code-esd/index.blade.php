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
        #packageTable_wrapper .row {
            padding: 10px;
        }
    </style>

    

    <div class="content-wrapper">
        <div class="card-main">
            <div class="card-header-custom">
                <div>
                    <h5 class="fw-bold text-dark mb-0">Manajemen Package</h5>
                    <small class="text-muted">Daftar paket dan item terkait</small>
                </div>
                <a href="{{ route('admin.packages.create') }}" class="btn btn-primary btn-sm px-3 fw-semibold">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Package
                </a>
            </div>

            <div class="table-responsive">
                <table id="packageTable" class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th width="50">ID</th>
                            <th>Nama Package</th>
                            <th>Daftar Item (Size)</th>
                            <th class="text-center">Total Item</th>
                            <th class="text-center" width="120">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($packages as $package)
                            <tr>
                                <td class="text-muted">#{{ $package->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle me-3"><i class="bi bi-box-seam"></i></div>
                                        <div class="fw-bold text-dark">{{ $package->package_name }}</div>
                                    </div>
                                </td>
                                <td>
                                    @foreach($package->items as $item)
                                        <span class="badge bg-light text-dark border mr-1">
                                            {{ $item->item_name }} ({{ $item->pivot->size ?? '-' }})
                                        </span>
                                    @endforeach
                                </td>
                                <td class="text-center">
                                    <span class="badge rounded-pill bg-primary">{{ $package->items->count() }}</span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-action-group">
                                        <a href="{{ route('admin.packages.edit', $package->id) }}" class="btn btn-sm" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.packages.destroy', $package->id) }}" method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm" onclick="return confirm('Hapus package ini?')">
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
            $('#packageTable').DataTable({
                "pageLength": 10,
                "language": { "search": "", "searchPlaceholder": "Cari package..." }
            });
        });
    </script>
@endsection