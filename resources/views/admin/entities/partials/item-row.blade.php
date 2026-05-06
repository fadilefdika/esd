<div class="row g-2 mb-3 align-items-center item-row border-bottom pb-2">
    <div class="col-md-3">
        <label class="small text-muted d-block">Item</label>
        <select {!! $isEdit ? 'style="pointer-events: none; background: #e9ecef;" tabindex="-1"' : 'name="items['.$index.'][item_id]"' !!} class="form-select form-select-sm" required>
            <option value="" disabled {{ !$pivotItem ? 'selected' : '' }}>Pilih Item...</option>
            @foreach($items as $item)
                <option value="{{ $item->id }}" {{ ($pivotItem && $item->id == $pivotItem->id) ? 'selected' : '' }}>
                    {{ $item->item_name }}
                </option>
            @endforeach
        </select>
        @if($isEdit)
            <input type="hidden" name="items[{{ $index }}][item_id]" value="{{ $pivotItem->id ?? '' }}">
        @endif
    </div>
    <div class="col-md-1">
        <label class="small text-muted d-block">Size</label>
        <input type="text" name="items[{{ $index }}][size]" class="form-control form-control-sm {!! $isEdit ? 'bg-light' : '' !!}" placeholder="Size" value="{{ $pivotItem->pivot->size ?? '' }}" {!! $isEdit ? 'readonly' : '' !!}>
    </div>
    <div class="col-md-1">
        <label class="small text-muted d-block">Status</label>
        <select name="items[{{ $index }}][status]" class="form-select form-select-sm">
            <option value="IN_USE"   {{ ($pivotItem->pivot->status ?? 'IN_USE') == 'IN_USE'   ? 'selected' : '' }}>In Use (Dipakai)</option>
            <option value="RETURNED" {{ ($pivotItem->pivot->status ?? '') == 'RETURNED' ? 'selected' : '' }}>Returned (Dikembalikan)</option>
            <option value="DAMAGED"  {{ ($pivotItem->pivot->status ?? '') == 'DAMAGED'  ? 'selected' : '' }}>Damaged (Rusak)</option>
            <option value="LOST"     {{ ($pivotItem->pivot->status ?? '') == 'LOST'     ? 'selected' : '' }}>Lost (Hilang)</option>
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
        @if(!$isEdit)
        <button type="button" class="btn btn-link text-danger remove-item p-0" title="Hapus Baris">
            <i class="bi bi-dash-circle-fill fs-5"></i>
        </button>
        @endif
    </div>

    <!-- Temporary Status Row -->
    <div class="col-12 mt-1">
        <div class="d-flex align-items-center gap-2 p-2 bg-light rounded" style="border: 1px dashed #cbd5e1;">
            <div class="form-check mb-0">
                <input class="form-check-input temp-checkbox" type="checkbox" name="items[{{ $index }}][is_temporary]" value="1" id="temp_{{ $index }}" {{ ($pivotItem->pivot->is_temporary ?? false) ? 'checked' : '' }}>
                <label class="form-check-label small fw-bold text-secondary" for="temp_{{ $index }}">
                    Notes
                </label>
            </div>
            <div class="flex-grow-1 ms-3 temp-note-container" style="display: {{ ($pivotItem->pivot->is_temporary ?? false) ? 'block' : 'none' }};">
                <input type="text" name="items[{{ $index }}][temporary_note]" class="form-control form-control-sm" placeholder="Catatan (Misal: Stok L kosong, pakai XL sementara)" value="{{ $pivotItem->pivot->temporary_note ?? '' }}">
            </div>
        </div>
    </div>
</div>