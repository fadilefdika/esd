@extends('layouts.app')

@section('content')
@php
    $isCopy = $isCopy ?? false;
    $isEdit = isset($entity) && $entity->exists && !$isCopy;
    $isCreate = !$isEdit && !$isCopy;

    $title = $isCreate ? 'Tambah Data Asset' : ($isCopy ? 'Duplikasi Asset' : 'Perbarui Asset');
    $formAction = $isEdit ? route('admin.entities.update', $entity->id) : route('admin.entities.store');
    $brandColor = '#2563eb';
@endphp

<style>
    /* Compact Styling */
    .card-custom { border-radius: 8px; border: 1px solid #e2e8f0; overflow: hidden; }
    .card-header-custom { background: #ffffff; border-bottom: 1px solid #e2e8f0; padding: 1rem 1.25rem; }
    .form-section-title { font-size: 0.7rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; display: flex; align-items: center; gap: 10px; margin-top: 1.5rem; }
    .form-section-title::after { content: ""; flex: 1; height: 1px; background: #f1f5f9; }
    .input-label { font-size: 0.7rem; font-weight: 700; color: #64748b; margin-bottom: 3px; text-transform: uppercase; }
    
    /* Smaller Form Elements */
    .form-control-sm, .form-select-sm { border-radius: 6px; }
    .select2-container--bootstrap-5 .select2-selection { font-size: 0.875rem !important; min-height: 31px !important; }
</style>

<div class="container-fluid py-4">
    <div class="card card-custom shadow-sm">
        <div class="card-header-custom d-flex justify-content-between align-items-center bg-light/50">
            <div>
                <h6 class="fw-bold mb-0 text-dark">{{ $title }}</h6>
                <small class="text-muted" style="font-size: 0.7rem;">Asset Management / {{ $title }}</small>
            </div>
            <div class="d-flex gap-2">
                @if($isEdit)
                    <a href="{{ route('admin.entities.download-qr', $entity->id) }}" class="btn btn-white btn-sm border shadow-sm">
                        <i class="bi bi-qr-code me-1"></i> QR Code
                    </a>
                @endif
                <a href="{{ route('admin.entities.index') }}" class="btn btn-light btn-sm border">Kembali</a>
            </div>
        </div>

        <div class="card-body px-4 pb-4">
            <form action="{{ $formAction }}" method="POST" id="entity-form">
                @csrf
                @if($isEdit) @method('PUT') @endif

                <div class="form-section-title mb-3">Identitas Utama</div>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="input-label">Cari Karyawan (Awork)</label>
                        <select id="employee_search" name="npk" class="form-select form-select-sm select2-remote" >
                            @if(isset($entity))
                                <option value="{{ $entity->npk }}" selected>{{ $entity->employee_name }} ({{ $entity->npk }})</option>
                            @endif
                        </select>
                        <input type="hidden" name="employee_name" id="employee_name" value="{{ $entity->employee_name ?? '' }}">
                    </div>
                    <div class="col-md-2">
                        <label class="input-label">NPK</label>
                        <input type="text" id="npk_display" class="form-control form-control-sm bg-light" value="{{ old('npk', $entity->npk ?? '') }}" readonly>
                    </div>
                    <div class="col-md-2">
                        <label class="input-label">Status</label>
                        <select name="status" class="form-select form-select-sm fw-bold">
                            <option value="AKTIF" {{ (old('status', $entity->status ?? 'AKTIF') == 'AKTIF') ? 'selected' : '' }}>AKTIF</option>
                            <option value="NON-AKTIF" {{ (old('status', $entity->status ?? '') == 'NON-AKTIF') ? 'selected' : '' }}>NON-AKTIF</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="input-label">No. Loker</label>
                        <input type="text" name="no_loker" class="form-control form-control-sm" value="{{ old('no_loker', $entity->no_loker ?? '') }}" placeholder="Ex: A-01">
                    </div>
                    <div class="col-md-2">
                        <label class="input-label">System Code</label>
                        <input type="text" class="form-control form-control-sm bg-light text-muted italic" value="{{ $isCreate || $isCopy ? 'AUTO' : $entity->code }}" readonly style="font-size: 0.75rem;">
                    </div>
                    <div class="col-md-2">
                        <label class="input-label">Kategori</label>
                        <select name="category" class="form-select form-select-sm">
                            <option value="">Pilih Kategori</option>
                            <option value="Karyawan" {{ old('category', $entity->category ?? '') === 'Karyawan' ? 'selected' : '' }}>Karyawan</option>
                            <option value="Tamu" {{ old('category', $entity->category ?? '') === 'Tamu' ? 'selected' : '' }}>Tamu</option>
                            <option value="Supplier" {{ old('category', $entity->category ?? '') === 'Supplier' ? 'selected' : '' }}>Supplier</option>
                            <option value="Pemagangan" {{ old('category', $entity->category ?? '') === 'Pemagangan' ? 'selected' : '' }}>Pemagangan</option>
                            <option value="OB" {{ old('category', $entity->category ?? '') === 'OB' ? 'selected' : '' }}>OB</option>
                            <option value="PKL" {{ old('category', $entity->category ?? '') === 'PKL' ? 'selected' : '' }}>PKL</option>
                            <option value="Backup" {{ old('category', $entity->category ?? '') === 'Backup' ? 'selected' : '' }}>Backup</option>
                        </select>
                    </div>
                     <div class="col-md-2">
                        <label class="input-label">Keterangan (Paket)</label>
                        {{-- <select name="package" id="package_select" class="form-select form-select-sm">
                            <option value="">Pilih Paket</option>
                            @foreach($package as $pkg)
                                <option value="{{ $pkg->package_name }}" 
                                    {{ trim(old('package', $entity->package ?? '')) == trim($pkg->package_name) ? 'selected' : '' }}>
                                    {{ $pkg->package_name }}
                                </option>
                            @endforeach
                        </select> --}}
                        <select name="package" id="package_select" class="form-select form-select-sm">
                            <option value="">Pilih Paket</option>
                            @foreach($package as $pkg)
                                <option value="{{ $pkg->package_name }}" 
                                        {{-- Simpan data items paket di sini --}}
                                        data-items='@json($pkg->items)'
                                        {{ trim(old('package', $entity->package ?? '')) == trim($pkg->package_name) ? 'selected' : '' }}>
                                    {{ $pkg->package_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-section-title mb-3">Departemen & Penempatan</div>
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label class="input-label">Dept Name</label>
                        <input type="text" name="dept_name" id="dept_name" class="form-control form-control-sm bg-light" value="{{ old('dept_name', $entity->dept_name ?? '') }}" readonly>
                        <input type="hidden" name="dept_id" id="dept_id" value="{{ old('dept_id', $entity->dept_id ?? '') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="input-label">Line Name</label>
                        <input type="text" name="line_name" id="line_name" class="form-control form-control-sm bg-light" value="{{ old('line_name', $entity->line_name ?? '') }}" readonly>
                        <input type="hidden" name="line_id" id="line_id" value="{{ old('line_id', $entity->line_id ?? '') }}">
                    </div>
                </div>

                <div class="form-section-title mb-3">Daftar Inventaris Item</div>
                <div id="item-wrapper">
                    @php $loopItems = (isset($entity) && $entity->items->count() > 0) ? $entity->items : [null]; @endphp
                    @foreach($loopItems as $index => $pivotItem)
                        @include('admin.entities.partials.item-row', ['index' => $index, 'pivotItem' => $pivotItem])
                    @endforeach
                </div>
                
                <button type="button" id="add-item-btn" class="btn btn-xs btn-outline-primary mt-2" style="font-size: 0.7rem; font-weight: 700; padding: 4px 12px;">
                    <i class="bi bi-plus-lg me-1"></i>TAMBAH BARIS
                </button>

                <div class="mt-5 pt-3 border-top d-flex justify-content-end align-items-center gap-3">
                    <span class="text-muted small italic">Pastikan data sudah benar sebelum menyimpan.</span>
                    <button type="submit" class="btn btn-primary px-4 shadow-sm" style="background-color: {{ $brandColor }}; border: none; font-size: 0.85rem; font-weight: 600;">
                        {{ $isCreate ? 'Simpan Asset' : ($isCopy ? 'Duplikasi' : 'Update Data') }}
                    </button>
                </div>
            </form>
        </div> 
    </div>
</div>

<template id="item-row-template">
    @include('admin.entities.partials.item-row', ['index' => 'ID_PLACEHOLDER', 'pivotItem' => null])
</template>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // 1. Inisialisasi baris item (Gunakan satu variabel saja)
    let rowIdx = {{ (isset($entity) ? $entity->items->count() : 1) }};

    // Handler Tambah Baris
    $('#add-item-btn').on('click', function() {
        const template = $('#item-row-template').html();
        const html = template.replace(/ID_PLACEHOLDER/g, rowIdx);
        $('#item-wrapper').append(html);
        rowIdx++;
    });

    // Handler Hapus Baris
    $(document).on('click', '.remove-item', function() {
        if ($('.item-row').length > 1) {
            $(this).closest('.item-row').fadeOut(200, function() { $(this).remove(); });
        }
    });

    // 2. Optimasi Select2 (Search Karyawan)
    $('#employee_search').select2({
        theme: 'bootstrap-5',
        minimumInputLength: 3, // OPTIMASI: Cari hanya setelah 3 karakter
        placeholder: 'Ketik NPK atau Nama...',
        allowClear: true,
        ajax: {
            url: "{{ route('admin.proxy.awork') }}",
            dataType: 'json',
            delay: 500, // OPTIMASI: Tunggu user selesai mengetik (500ms) baru kirim request
            cache: true, // OPTIMASI: Simpan hasil search sementara di browser
            data: params => ({ 
                search: params.term 
            }),
            processResults: data => ({
                results: $.map(data.data, item => ({
                    id: item.npk,
                    text: `${item.fullname} (${item.npk})`,
                    full_data: item
                }))
            })
        }
    });

    // Auto-fill Logic
    $('#employee_search').on('select2:select', function (e) {
        const d = e.params.data.full_data;
        $('#employee_name').val(d.fullname);
        $('#dept_id').val(d.department_id);
        $('#dept_name').val(d.department);
        $('#line_id').val(d.line_id);
        $('#line_name').val(d.line);
        $('#npk_display').val(d.npk);
    });

    // Auto-fill Items by Package
    $('#package_select').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const itemsData = selectedOption.data('items'); // Ini akan mengambil array item

        if (itemsData && itemsData.length > 0) {
            // 1. Kosongkan baris item yang ada sekarang
            $('#item-wrapper').empty();
            rowIdx = 0;

            // 2. Loop setiap item dari paket
            itemsData.forEach(function(item) {
                const template = $('#item-row-template').html();
                // Ganti placeholder ID dengan index saat ini
                let html = template.replace(/ID_PLACEHOLDER/g, rowIdx);
                
                // 3. Tambahkan ke wrapper
                $('#item-wrapper').append(html);
                
                // 4. Isi nilainya secara otomatis
                const currentRow = $('#item-wrapper .item-row').last();
                
                // Set value Item ID
                currentRow.find('select[name^="items"][name$="[item_id]"]').val(item.id);
                
                // Set default status (misal: Diterima)
                currentRow.find('select[name^="items"][name$="[status]"]').val('Diterima');

                // Opsional: Set Tanggal Terima ke hari ini
                const today = new Date().toISOString().split('T')[0];
                currentRow.find('input[name^="items"][name$="[receive_date]"]').val(today);
                
                rowIdx++;
            });
        }
    });


    });
</script>
@endpush