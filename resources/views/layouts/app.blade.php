<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>EMS.</title>
    {{-- <link rel="icon" type="image/png" href="{{ asset('assets/images/ams.png') }}?v={{ time() }}"> --}}
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">
    <!-- Favicon -->
    {{-- <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/ams.png') }}"> --}}

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

    <style>
        :root {
            --primary-ems: #2563eb; 
            --primary-hover: #1d4ed8;
            --bg-active: #eff6ff;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --sidebar-width: 260px; /* Variabel lebar tetap */
        }

        body {
            background-color: #f8fafc;
            font-family: 'Inter', sans-serif;
            margin: 0;
        }

        /* Sidebar Container */
        .sidebar {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            height: 100vh !important;
            width: var(--sidebar-width) !important;
            background-color: #ffffff !important;
            border-right: 1px solid #e2e8f0 !important;
            display: flex !important;
            flex-direction: column !important;
            z-index: 1050 !important;
            padding: 0 !important; /* Reset padding kontainer luar */
        }

        /* Header Brand */
        .sidebar-header {
            padding: 30px 24px 20px !important; /* Memberi ruang di atas dan samping */
        }

        .ems-brand {
            font-size: 1.5rem !important;
            font-weight: 800 !important;
            letter-spacing: -1px !important;
            color: var(--text-main) !important;
            margin: 0 !important;
            line-height: 1 !important;
        }

        .ems-brand span { color: var(--primary-ems) !important; }

        .brand-subtitle {
            display: block !important;
            font-size: 0.65rem !important;
            color: var(--text-muted) !important;
            font-weight: 600 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
            margin-top: 6px !important;
        }

        /* Menu & Navigation */
        .nav-custom {
            padding: 10px 14px !important; /* Ruang agar menu tidak mepet sidebar */
        }

        .menu-label {
            display: block !important;
            font-size: 0.65rem !important;
            font-weight: 700 !important;
            color: #94a3b8 !important;
            text-transform: uppercase !important;
            letter-spacing: 1px !important;
            padding: 20px 12px 8px !important;
        }

        .nav-custom .nav-link {
            color: var(--text-muted) !important;
            font-size: 0.875rem !important;
            font-weight: 500 !important;
            padding: 10px 16px !important;
            border-radius: 8px !important;
            display: flex !important;
            align-items: center !important;
            gap: 12px !important;
            text-decoration: none !important;
            margin-bottom: 4px !important;
            transition: all 0.2s ease !important;
        }

        /* Hover & Active State */
        .nav-custom .nav-link:hover {
            background-color: #f8fafc !important;
            color: var(--primary-ems) !important;
        }

        .nav-custom .nav-link.active {
            background-color: var(--bg-active) !important;
            color: var(--primary-ems) !important;
            font-weight: 600 !important;
        }

        .nav-custom .nav-link i {
            font-size: 1.1rem !important;
            color: #94a3b8 !important;
        }

        .nav-custom .nav-link.active i {
            color: var(--primary-ems) !important;
        }

        /* Layout Content Adjustment */
        .navbar-custom, .main-content {
            margin-left: var(--sidebar-width) !important;
            transition: margin-left 0.3s ease;
        }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .navbar-custom, .main-content { margin-left: 0 !important; }
        }
        /* Chrome, Safari, Edge, Opera */
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
        }
        /* Firefox */
        input[type=number] {
        -moz-appearance: textfield;
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body>

<!-- Sidebar -->
@include('layouts.partials.sidebar')

<!-- Navbar -->
@include('layouts.partials.navbar')


<!-- Main Content -->
<div class="main-content" id="main-content">
    @yield('content')
</div>

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
    $('#toggleSidebar').on('click', function () {
        $('#sidebar').addClass('show');
    });

    $('#closeSidebar').on('click', function () {
        $('#sidebar').removeClass('show');
    });
</script>

<script>
    @if (session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: '{{ session('success') }}',
            timer: 2500,
            showConfirmButton: false
        });
    @elseif (session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: '{{ session('error') }}',
            timer: 3000,
            showConfirmButton: true
        });
    @endif
</script>




<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>