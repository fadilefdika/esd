<div class="row g-2 mb-2 align-items-end item-row">
    <div class="col-md-7">
        <label class="input-label d-block">Pilih Item</label>
        <select name="items[{{ $index }}][item_id]" class="form-select form-select-sm" required>
            <option value="" disabled {{ !$pivotItem ? 'selected' : '' }}>-- Pilih Item --</option>
            @foreach($availableItems as $item) {{-- Pastikan variabel $availableItems dikirim dari Controller --}}
                <option value="{{ $item->id }}" 
                    {{ ($pivotItem && $item->id == $pivotItem->id) ? 'selected' : '' }}>
                    {{ $item->item_name }}
                </option>
            @endforeach
        </select>
    </div>
    
    <div class="col-md-4">
        <label class="input-label d-block">Default Size (Optional)</label>
        <input type="text" name="items[{{ $index }}][size]" 
               class="form-control form-control-sm" 
               placeholder="Ex: XL, 42, dsb" 
               value="{{ $pivotItem->pivot->size ?? '' }}">
    </div>

    <div class="col-md-1 text-center">
        <button type="button" class="btn btn-link text-danger remove-item p-0 mb-1" title="Hapus">
            <i class="bi bi-trash fs-5"></i>
        </button>
    </div>
</div>