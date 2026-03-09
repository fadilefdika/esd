@push('styles')
<style>
    /* Paksa variabel warna baru */
    :root {
        --primary-ems: #2563eb !important; 
        --bg-active: #eff6ff !important;
    }

    .sidebar {
        background-color: #ffffff !important;
        border-right: 1px solid #e2e8f0 !important;
        height: 100vh !important;
        width: 260px !important;
        position: fixed !important;
        left: 0 !important;
        top: 0 !important;
        display: flex !important;
        flex-direction: column !important;
        z-index: 1000;
    }

    .sidebar-header {
        padding: 25px 20px !important;
        border-bottom: 1px solid #f1f5f9 !important;
    }

    .ems-brand {
        font-size: 1.5rem !important;
        font-weight: 800 !important;
        color: #0f172a !important;
        margin: 0 !important;
    }
    .ems-brand span { color: var(--primary-ems) !important; }

    .nav-custom {
        padding: 20px 15px !important; /* Memberi jarak dari tepi kiri */
    }

    .menu-label {
        font-size: 0.65rem !important;
        font-weight: 700 !important;
        color: #94a3b8 !important;
        text-transform: uppercase !important;
        letter-spacing: 1px !important;
        margin: 20px 0 10px 15px !important;
        display: block !important;
    }

    .nav-custom .nav-link {
        color: #475569 !important;
        padding: 12px 15px !important;
        border-radius: 8px !important;
        display: flex !important;
        align-items: center !important;
        gap: 12px !important;
        text-decoration: none !important;
        margin-bottom: 5px !important;
    }

    /* Override warna merah pada tombol aktif */
    .nav-custom .nav-link.active {
        background-color: var(--bg-active) !important;
        color: var(--primary-ems) !important;
        border: none !important; /* Hapus border merah jika ada */
    }

    .nav-custom .nav-link i {
        font-size: 1.2rem !important;
        color: #94a3b8 !important;
    }

    .nav-custom .nav-link.active i {
        color: var(--primary-ems) !important;
    }

    /* Animasi panah dropdown */
    .nav-custom .nav-link .bi-chevron-down {
        transition: transform 0.3s ease;
    }
    
    .nav-custom .nav-link[aria-expanded="true"] .bi-chevron-down {
        transform: rotate(180deg);
    }
    
    /* Hover effects yang lebih halus */
    .nav-custom .nav-link:hover {
        background-color: #f8fafc !important;
        transform: translateX(3px);
        transition: all 0.2s ease;
    }
    .nav-custom .nav-link.active:hover {
        transform: none; /* Jangan geser kalau lagi active */
    }

    /* Styling khusus sub-menu */
    .submenu-item .nav-link {
        padding: 10px 15px 10px 35px !important; /* Indentasi disesuaikan untuk icon */
        font-size: 0.85rem !important;
        color: #64748b !important;
        display: flex !important;
        align-items: center !important;
        gap: 10px !important;
    }
    
    .submenu-item .nav-link:hover {
        color: var(--primary-ems) !important;
        background-color: transparent !important;
    }
    
    .submenu-item .nav-link.active {
        background-color: transparent !important;
        color: var(--primary-ems) !important;
        font-weight: 700 !important;
    }
    
    .submenu-item .nav-link i {
        font-size: 1rem !important;
    }
</style>
@endpush

<nav class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <h1 class="ems-brand">EMS<span>.</span></h1>
        <small class="text-muted fw-bold uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">ESD & Laundry Tracking</small>
    </div>

    <div class="nav-custom">
        <span class="menu-label">Main Menu</span>
        <ul class="nav flex-column p-0 m-0">
            <li class="nav-item">
                <a href="{{ route('admin.entities.index') }}" 
                   class="nav-link {{ request()->routeIs('admin.entities.*') ? 'active' : '' }}">
                    <i class="bi bi-shield-check"></i>
                    <span>ESD Assets</span>
                </a>
            </li>
        </ul>

        <span class="menu-label">Administration</span>
        <ul class="nav flex-column p-0 m-0">
            <li class="nav-item">
                @php 
                    $isMasterDataActive = request()->is('admin/packages*') || request()->is('admin/code-esd*');
                @endphp
                <a class="nav-link justify-content-between {{ $isMasterDataActive ? 'active' : '' }}" 
                   data-bs-toggle="collapse" 
                   href="#masterDataMenu" 
                   aria-expanded="{{ $isMasterDataActive ? 'true' : 'false' }}">
                    <div class="d-flex align-items-center gap-3">
                        <i class="bi bi-database-gear"></i>
                        <span>Master Data</span>
                    </div>
                    <i class="bi bi-chevron-down" style="font-size: 0.7rem;"></i>
                </a>

                <div class="collapse {{ $isMasterDataActive ? 'show' : '' }}" id="masterDataMenu">
                    <ul class="nav flex-column p-0 m-0 position-relative">
                        <li class="nav-item submenu-item position-relative">
                            <a href="{{ route('admin.packages.index') }}" 
                               class="nav-link {{ request()->routeIs('admin.packages.*') ? 'active' : '' }}">
                                <i class="bi bi-box-seam"></i> <span>Master Package</span>
                            </a>
                        </li>
                        <li class="nav-item submenu-item position-relative">
                            <!-- Update routeIs agar persis membaca admin.code-esd karena controllernya code-esd -->
                            <a href="{{ route('admin.code-esd.index') }}" 
                               class="nav-link {{ request()->routeIs('admin.code-esd.*') || request()->is('admin/code-esd*') ? 'active' : '' }}">
                                <i class="bi bi-upc-scan"></i> <span>Master Code ESD</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
        </ul>
    </div>
</nav>