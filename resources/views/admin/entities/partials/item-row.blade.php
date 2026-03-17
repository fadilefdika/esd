{{-- <div class="row g-2 mb-3 align-items-center item-row border-bottom pb-2">
    <div class="col-md-3">
        <label class="small text-muted d-block">Item</label>
        <select name="items[{{ $index }}][item_id]" class="form-select form-select-sm" required>
            <option value="" disabled {{ !$pivotItem ? 'selected' : '' }}>Pilih Item...</option>
            @foreach($items as $item)
                <option value="{{ $item->id }}" {{ ($pivotItem && $item->id == $pivotItem->id) ? 'selected' : '' }}>
                    {{ $item->item_name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-1">
        <label class="small text-muted d-block">Size</label>
        <input type="text" name="items[{{ $index }}][size]" class="form-control form-control-sm" placeholder="Size" value="{{ $pivotItem->pivot->size ?? '' }}">
    </div>
    <div class="col-md-1">
        <label class="small text-muted d-block">Status</label>
        <select name="items[{{ $index }}][status]" class="form-select form-select-sm">
            <option value="-" {{ ($pivotItem->pivot->status ?? '') == '-' ? 'selected' : '' }}>-</option>
            <option value="Diterima" {{ ($pivotItem->pivot->status ?? '') == 'Diterima' ? 'selected' : '' }}>Diterima</option>
            <option value="Dikembalikan" {{ ($pivotItem->pivot->status ?? '') == 'Dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
        </select>
    </div>
    <div class="col-md-2">
        <label class="small text-muted d-block">Tgl Terima</label>
        <input type="date" name="items[{{ $index }}][receive_date]" class="form-control form-control-sm" value="{{ $pivotItem->pivot->receive_date ?? '' }}">
    </div>
    <div class="col-md-2">
        <label class="small text-muted d-block">Tgl Kembali</label>
        <input type="date" name="items[{{ $index }}][return_date]" class="form-control form-control-sm" value="{{ $pivotItem->pivot->return_date ?? '' }}">
    </div>
    <div class="col-md-2">
        <label class="small text-muted d-block">Catatan Pengembalian</label>
        <input type="text" name="items[{{ $index }}][return_notes]" class="form-control form-control-sm" placeholder="Catatan Pengembalian" value="{{ $pivotItem->pivot->return_notes ?? '' }}">
    </div>
    <div class="col-md-1 text-end">
        <label class="small text-muted d-block">&nbsp;</label>
        <button type="button" class="btn btn-link text-danger remove-item p-0" title="Hapus Baris">
            <i class="bi bi-dash-circle-fill fs-5"></i>
        </button>
    </div>
</div> --}}

{{-- resources/views/admin/entities/partials/item-row.blade.php --}}
<div class="row g-2 mb-3 align-items-center item-row border-bottom pb-2">
    <div class="col-md-3">
        <label class="small text-muted d-block">Item</label>
        <select name="items[{{ $index }}][item_id]" class="form-select form-select-sm" required>
            <option value="" disabled {{ !$pivotItem ? 'selected' : '' }}>Pilih Item...</option>
            @foreach($items as $item)
                {{-- Cek ID dari pivot atau dari model item itu sendiri --}}
                @php 
                    $selectedId = $pivotItem ? ($pivotItem->id ?? $pivotItem->item_id) : null; 
                @endphp
                <option value="{{ $item->id }}" {{ $selectedId == $item->id ? 'selected' : '' }}>
                    {{ $item->item_name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-1">
        <label class="small text-muted d-block">Size</label>
        <input type="text" name="items[{{ $index }}][size]" class="form-control form-control-sm" 
               placeholder="Size" value="{{ $pivotItem->pivot->size ?? '' }}">
    </div>
    <div class="col-md-1">
        <label class="small text-muted d-block">Status</label>
        <select name="items[{{ $index }}][status]" class="form-select form-select-sm">
            @php $currentStatus = $pivotItem->pivot->status ?? '-'; @endphp
            <option value="-" {{ $currentStatus == '-' ? 'selected' : '' }}>-</option>
            <option value="Diterima" {{ $currentStatus == 'Diterima' ? 'selected' : '' }}>Diterima</option>
            <option value="Dikembalikan" {{ $currentStatus == 'Dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
        </select>
    </div>
    <div class="col-md-2">
        <label class="small text-muted d-block">Tgl Terima</label>
        <input type="date" name="items[{{ $index }}][receive_date]" class="form-control form-control-sm" 
               value="{{ isset($pivotItem->pivot->receive_date) ? \Carbon\Carbon::parse($pivotItem->pivot->receive_date)->format('Y-m-d') : '' }}">
    </div>
    <div class="col-md-2">
        <label class="small text-muted d-block">Tgl Kembali</label>
        <input type="date" name="items[{{ $index }}][return_date]" class="form-control form-control-sm" 
               value="{{ isset($pivotItem->pivot->return_date) ? \Carbon\Carbon::parse($pivotItem->pivot->return_date)->format('Y-m-d') : '' }}">
    </div>
    <div class="col-md-2">
        <label class="small text-muted d-block">Catatan Pengembalian</label>
        <input type="text" name="items[{{ $index }}][return_notes]" class="form-control form-control-sm" 
               placeholder="Catatan Pengembalian" value="{{ $pivotItem->pivot->return_notes ?? '' }}">
    </div>
    <div class="col-md-1 text-end">
        <label class="small text-muted d-block">&nbsp;</label>
        <button type="button" class="btn btn-link text-danger remove-item p-0" title="Hapus Baris">
            <i class="bi bi-dash-circle-fill fs-5"></i>
        </button>
    </div>
</div>