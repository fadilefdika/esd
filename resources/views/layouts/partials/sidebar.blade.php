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
                <a class="nav-link justify-content-between" data-bs-toggle="collapse" href="#masterDataMenu">
                    <div class="d-flex align-items-center gap-3">
                        <i class="bi bi-database-gear"></i>
                        <span>Master Data</span>
                    </div>
                    <i class="bi bi-chevron-down" style="font-size: 0.7rem;"></i>
                </a>

                <div class="collapse {{ request()->is('admin/packages*') ? 'show' : '' }}" id="masterDataMenu">
                    <ul class="nav flex-column ps-4"> <li class="nav-item">
                            <a href="{{ route('admin.packages.index') }}" 
                            class="nav-link {{ request()->routeIs('admin.packages.*') ? 'active' : '' }}" 
                            style="font-size: 0.9rem;">
                                <i class="bi bi-box-seam" style="font-size: 1rem !important;"></i>
                                <span>Master Package</span>
                            </a>
                        </li>
                        </ul>
                </div>
            </li>
        </ul>
    </div>
</nav>