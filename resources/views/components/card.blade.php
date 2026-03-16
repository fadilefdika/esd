<div class="card-main shadow-sm {{ $class ?? '' }}" style="border: 1px solid #e2e8f0; border-radius: 12px; background: #fff; overflow: hidden;">
    @isset($header)
        <div class="card-header-custom" style="padding: 1.25rem 1.5rem; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; background: #fafafa;">
            {{ $header }}
        </div>
    @endisset

    <div class="card-body px-4 py-4">
        {{ $slot }}
    </div>
    
    @isset($footer)
        <div class="card-footer bg-white border-top px-4 py-3 text-end">
            {{ $footer }}
        </div>
    @endisset
</div>
