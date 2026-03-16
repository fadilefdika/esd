@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<style>
    .info-label { font-size: 0.75rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 2px; }
    .info-value { font-size: 1.1rem; font-weight: 600; color: #0f172a; }
</style>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <!-- Informasi Code ESD -->
        <div class="col-md-4 mb-4 mb-md-0">
            <x-card class="h-100">
                <x-slot name="header">
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">Detail Master Code</h6>
                    </div>
                    <a href="{{ route('admin.code-esd.index') }}" class="btn btn-light btn-sm border">Kembali</a>
                </x-slot>
                
                <div class="d-flex align-items-center mb-4">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                        <i class="bi bi-upc-scan fs-4"></i>
                    </div>
                    <div>
                        <div class="info-label">KODE ESD</div>
                        <div class="fs-4 fw-bold text-dark">{{ $codeEsd->name }}</div>
                    </div>
                </div>
                
                <hr class="text-muted opacity-25">
                
                <div class="row mt-3">
                    <div class="col-6 mb-3">
                        <div class="info-label">Total Pengguna</div>
                        <div class="info-value text-primary">
                            <i class="bi bi-people-fill me-1"></i> {{ $codeEsd->entities->count() }} Orang
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="info-label">Total Set</div>
                        <div class="info-value">
                            {{ $codeEsd->jumlah_karyawan }} Orang
                        </div>
                    </div>
                    <div class="col-12 mt-2">
                        <a href="{{ route('admin.code-esd.edit', $codeEsd->id) }}" class="btn btn-outline-primary btn-sm w-100">
                            <i class="bi bi-pencil me-1"></i> Edit Kode
                        </a>
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Tabel Entity/Karyawan Pengguna -->
        <div class="col-md-8">
            <x-card class="h-100">
                <x-slot name="header">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="bi bi-table me-2 text-muted"></i>Daftar Karyawan (Dihubungkan)
                    </h6>
                </x-slot>
                
                <div class="table-responsive">
                    <table id="entityTable" class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                         <thead class="bg-light text-muted" style="text-transform: uppercase; font-size: 0.75rem;">
                            <tr>
                                <th>NPK</th>
                                <th>Nama Karyawan</th>
                                <th>Departemen</th>
                                <th>Status</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($codeEsd->entities as $entity)
                            <tr>
                                <td class="fw-bold">{{ $entity->npk ?? $entity->code }}</td>
                                <td>{{ $entity->employee_name ?? 'STOK SPARE (AVAILABLE)' }}</td>
                                <td>{{ $entity->dept_name ?? '-' }}</td>
                                <td>
                                    @php
                                        $bgClass = $entity->status == 'AKTIF' ? 'bg-success' : ($entity->status == 'AVAILABLE' ? 'bg-info' : 'bg-secondary');
                                    @endphp
                                    <span class="badge {{ $bgClass }}">{{ $entity->status }}</span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.entities.edit', $entity->id) }}" class="btn btn-sm btn-light border text-primary px-2 py-1" target="_blank" title="Buka Detail">
                                        <i class="bi bi-box-arrow-up-right"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i class="bi bi-folder-x fs-1 opacity-50 d-block mb-2"></i>
                                    Belum ada data asset/karyawan yang menggunakan kode ini.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#entityTable').DataTable({
            "pageLength": 10,
            "language": { "search": "", "searchPlaceholder": "Cari nama..." }
        });
    });
</script>
@endsection
