@extends('layouts.app')

@section('content')
@php
    $isEdit = isset($package) && $package->exists;
    $title = $isEdit ? 'Perbarui Package' : 'Tambah Package Baru';
    $formAction = $isEdit ? route('admin.packages.update', $package->id) : route('admin.packages.store');
@endphp

<style>
    .card-custom { border-radius: 8px; border: 1px solid #e2e8f0; }
    .input-label { font-size: 0.7rem; font-weight: 700; color: #64748b; margin-bottom: 3px; text-transform: uppercase; }
    .form-section-title { font-size: 0.7rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; display: flex; align-items: center; gap: 10px; margin-top: 1.5rem; }
    .form-section-title::after { content: ""; flex: 1; height: 1px; background: #f1f5f9; }
</style>

<div class="container-fluid py-4">
    <div class="card card-custom shadow-sm">
        <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0">{{ $title }}</h6>
            <a href="{{ route('admin.packages.index') }}" class="btn btn-light btn-sm border">Kembali</a>
        </div>

        <div class="card-body px-4">
            <form action="{{ $formAction }}" method="POST">
                @csrf
                @if($isEdit) @method('PUT') @endif

                <div class="form-section-title mb-3">Informasi Package</div>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="input-label">Nama Package</label>
                        <input type="text" name="package_name" class="form-control form-control-sm" 
                               value="{{ old('package_name', $package->package_name ?? '') }}" 
                               placeholder="Contoh: Paket Induction Karyawan Baru" required>
                    </div>
                </div>

                <div class="form-section-title mb-3">Item dalam Package</div>
                <div id="item-wrapper">
                    @php 
                        $loopItems = (isset($package) && $package->items->count() > 0) ? $package->items : [null]; 
                    @endphp
                    
                    @foreach($loopItems as $index => $pivotItem)
                        @include('admin.master-package.partials.item-row', ['index' => $index, 'pivotItem' => $pivotItem])
                    @endforeach
                </div>

                <button type="button" id="add-item-btn" class="btn btn-sm btn-outline-primary mt-2">
                    <i class="bi bi-plus-lg me-1"></i> TAMBAH ITEM
                </button>

                <div class="mt-5 pt-3 border-top d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary px-5 shadow-sm">
                        {{ $isEdit ? 'Simpan Perubahan' : 'Buat Package' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Template untuk baris baru via JS --}}
<template id="item-row-template">
    @include('admin.master-package.partials.item-row', ['index' => 'ID_PLACEHOLDER', 'pivotItem' => null])
</template>

@push('scripts')
<script>
$(document).ready(function() {
    let rowIdx = {{ count($loopItems) }};

    $('#add-item-btn').on('click', function() {
        const template = $('#item-row-template').html();
        const html = template.replace(/ID_PLACEHOLDER/g, rowIdx);
        $('#item-wrapper').append(html);
        rowIdx++;
    });

    $(document).on('click', '.remove-item', function() {
        if ($('.item-row').length > 1) {
            $(this).closest('.item-row').remove();
        }
    });
});
</script>
@endpush
@endsection