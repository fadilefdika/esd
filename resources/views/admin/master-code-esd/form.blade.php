@extends('layouts.app')

@section('content')
@php
    $isEdit = isset($codeEsd);
    $title = $isEdit ? 'Edit Code ESD' : 'Tambah Code ESD';
    $action = $isEdit ? route('admin.code-esd.update', $codeEsd->id) : route('admin.code-esd.store');
@endphp

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <x-card>
                <x-slot name="header">
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">{{ $title }}</h5>
                        <small class="text-muted">Manajemen Master Data / {{ $title }}</small>
                    </div>
                    <a href="{{ route('admin.code-esd.index') }}" class="btn btn-light btn-sm border">Kembali</a>
                </x-slot>

                <form action="{{ $action }}" method="POST">
                    @csrf
                    @if($isEdit) @method('PUT') @endif

                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small text-uppercase">Nama Code (Kode Paket)</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                               value="{{ old('name', $codeEsd->name ?? '') }}" 
                               placeholder="Contoh: CTXL, BJM, ASP" required autofocus style="text-transform: uppercase;">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @else
                            <div class="form-text mt-1">Gunakan kombinasi huruf, misal awalan C untuk celana dan M untuk Medium.</div>
                        @enderror
                    </div>

                    @if($isEdit)
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small text-uppercase">Jumlah Karyawan Pengguna</label>
                        <input type="text" class="form-control bg-light text-muted" value="{{ $codeEsd->jumlah_karyawan }} Orang" disabled>
                        <div class="form-text mt-1">Dihitung otomatis oleh sistem berdasarkan jumlah pengguna aktif.</div>
                    </div>
                    @endif

                    <div class="mt-4 pt-3 border-top text-end">
                        <button type="submit" class="btn btn-primary px-4 shadow-sm" style="background-color: #2563eb; border: none; font-weight: 600;">
                            <i class="bi bi-save me-1"></i> {{ $isEdit ? 'Update Data' : 'Simpan Data' }}
                        </button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</div>
@endsection
