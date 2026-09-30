<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Klinik App') }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        * {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        :root { --bs-primary-rgb: 8,127,117; --bs-link-color: #087f75; --bs-link-hover-color: #065e56; }
        body { background: #f4f7f5; color: #254440; font-size: 13px; }
        .app-main { min-width: 0; }
        .sidebar { width: 245px; flex-shrink: 0; background: #173f3d; padding: 22px 14px; position: sticky; top: 0; height: 100vh; overflow-y: auto; }
        .sidebar-brand { display: flex; align-items: center; gap: 11px; color: white; padding: 4px 9px 28px; font-weight: 750; font-size: 16px; }
        .sidebar-brand span { background: #ffffff16; border: 1px solid #ffffff20; border-radius: 12px; width: 38px; height: 38px; display: grid; place-items: center; font-size: 26px; }
        .nav-caption { color: #9abdb5; font-size: 9px; font-weight: 700; letter-spacing: 1.8px; padding: 22px 13px 9px; text-transform: uppercase; }
        .sidebar a { color: #c5d8d3; padding: 12px 14px; margin: 3px 0; display: flex; align-items: center; gap: 12px; text-decoration: none; border-radius: 10px; font-size: 12px; transition: background .15s; }
        .sidebar a i { width: 17px; text-align: center; opacity: .8; }
        .sidebar a:hover { background: #ffffff0e; color: white; }
        .sidebar a.active { background: #e5f3ec; color: #165e51; font-weight: 750; }
        .sidebar summary { font-size: 12px; color: #c5d8d3; padding: 12px 14px; cursor: pointer; }
        .sidebar details a { margin-left: 14px; font-size: 11px; }
        .sidebar-foot { margin: 28px 10px 8px; padding-top: 18px; border-top: 1px solid #ffffff16; color: #9abdb5; font-size: 10px; line-height: 1.8; }
        .topbar { background: #ffffffeb; border-bottom: 1px solid #e1e9e4; color: #3f5e57; padding: 18px 28px; gap: 16px; }
        .workspace-label { font-size: 10px; color: #80958f; letter-spacing: 1.3px; text-transform: uppercase; display: block; margin-bottom: 3px; }
        .app-content { padding: 30px !important; }
        .app-content > .container-fluid { padding: 0; }
        .card { border-radius: 18px !important; border-color: #e0e9e3 !important; box-shadow: 0 4px 20px #173f3d04 !important; }
        .card-header { border-radius: 18px 18px 0 0 !important; border-color: #edf1ed !important; padding: 20px !important; }
        .card-title { font-size: 15px; }
        .card-body { padding: 22px; }
        .table { --bs-table-color: #34534a; --bs-table-border-color: #edf1ed; --bs-table-hover-bg: #f4f9f6; font-size: 12px; }
        .table > :not(caption) > * > * { padding: 15px 14px; }
        .table thead th { font-size: 10px; color: #73877e; text-transform: uppercase; letter-spacing: .65px; font-weight: 650; background: #f7faf7; white-space: nowrap; }
        .table-responsive { border-radius: 0 0 18px 18px; }
        .badge { padding: 7px 9px; font-weight: 600; border-radius: 7px; }
        .btn { border-radius: 9px; font-size: 12px; padding: 10px 14px; }
        .btn-sm { font-size: 11px; padding: 6px 10px; }
        .btn-primary { --bs-btn-bg: #087f75; --bs-btn-border-color: #087f75; --bs-btn-hover-bg: #06665e; --bs-btn-hover-border-color: #06665e; --bs-btn-active-bg: #06594f; --bs-btn-active-border-color: #06594f; }
        .btn-outline-primary { --bs-btn-color: #087f75; --bs-btn-border-color: #c0dcd1; --bs-btn-hover-bg: #087f75; --bs-btn-hover-border-color: #087f75; --bs-btn-active-bg: #087f75; }
        .form-control, .form-select { border-radius: 9px; border-color: #dce7df; min-height: 41px; font-size: 13px; }
        .form-control:focus, .form-select:focus { border-color: #087f75; box-shadow: 0 0 0 3px #087f7514; }
        .form-label { font-size: 12px; }
        .text-muted { color: #788b81 !important; }
        .dashboard-welcome { position: relative; overflow: hidden; background: #e6efe4; padding: 30px; border: 1px solid #dbe6d9; border-radius: 22px; display: flex; align-items: center; justify-content: space-between; gap: 20px; margin-bottom: 24px; }
        .dashboard-welcome::after { content: '+'; position: absolute; right: 190px; top: -46px; font-size: 200px; line-height: 1; font-weight: 700; color: #173f3d05; pointer-events: none; }
        .dashboard-welcome h1 { font-size: clamp(23px, 2.7vw, 34px); letter-spacing: -1px; color: #193f36; font-weight: 750; margin: 9px 0; }
        .dashboard-welcome p { color: #5c7668; margin: 0; max-width: 500px; line-height: 1.8; }
        .dashboard-eyebrow { color: #5b7f6b; text-transform: uppercase; font-weight: 700; font-size: 9px; letter-spacing: 1.5px; }
        .metric-card { height: 100%; padding: 22px; }
        .metric-top { display: flex; justify-content: space-between; align-items: center; gap: 8px; color: #75897c; font-size: 11px; }
        .metric-icon { width: 34px; height: 34px; border-radius: 10px; display: grid; place-items: center; background: #eef5ef; color: #42755d; }
        .metric-value { font-size: clamp(23px, 2.2vw, 31px); font-weight: 750; letter-spacing: -1px; color: #25493c; margin: 12px 0 6px; overflow-wrap: anywhere; }
        .metric-note { font-size: 10px; color: #82948a; }
        .section-label { font-size: 15px; font-weight: 700; color: #345449; margin: 28px 0 14px; }
        .shortcut { display: flex; align-items: center; gap: 12px; padding: 18px; border-radius: 14px; background: white; border: 1px solid #e1e9e1; color: #345449; text-decoration: none; height: 100%; transition: border-color .15s; }
        .shortcut:hover { border-color: #83b39f; color: #087f75; }
        .shortcut > i { width: 34px; height: 34px; display: grid; place-items: center; flex-shrink: 0; background: #edf4ed; border-radius: 10px; color: #507f67; }
        .shortcut strong { font-size: 12px; display: block; margin-bottom: 3px; }
        .shortcut small { font-size: 10px; color: #7f9285; }
        .mobile-menu { display: none; }
        @media (max-width: 1100px) { .sidebar { width: 215px; } .app-content { padding: 22px !important; } }
        @media (max-width: 767px) {
            .sidebar { position: fixed; left: 0; top: 0; width: 265px; height: 100dvh; z-index: 1050; transform: translateX(-100%); transition: transform .2s; visibility: hidden; }
            .sidebar.is-open { transform: translateX(0); visibility: visible; }
            .mobile-menu { display: inline-flex; }
            .nav-backdrop { position: fixed; inset: 0; background: #122e2966; z-index: 1040; }
            .topbar { padding: 14px 16px; } .workspace-title { font-size: 11px; }
            .app-content { padding: 18px 14px !important; }
            .dashboard-welcome { padding: 23px; flex-direction: column; align-items: flex-start; }
            .app-content .container-fluid > .d-flex.justify-content-between { flex-wrap: wrap; gap: 14px; }
            .metric-card { padding: 16px; } .metric-value { font-size: 24px; }
            .table { min-width: 650px; } .shortcut { padding: 14px; }
        }
        @media (prefers-reduced-motion: reduce) { .sidebar { transition: none; } }
        @media print {
            @page { size: A4 landscape; margin: 12mm; }
            body { background: white !important; color: black; }
            .sidebar, .topbar, .no-print, .d-print-none, .btn:not(.tooth-btn), form, .pagination, .alert { display: none !important; }
            .app-shell, .app-main { display: block !important; width: 100% !important; min-height: 0 !important; }
            .app-content, .container-fluid { padding: 0 !important; margin: 0 !important; }
            .table-responsive { overflow: visible !important; }
            .card { box-shadow: none !important; }
            .table { min-width: 0 !important; }
            .nav-backdrop { display: none !important; }
            table { width: 100% !important; font-size: 9pt; }
            thead { display: table-header-group; }
            tr { break-inside: avoid; }
            .badge { color: black !important; background: transparent !important; white-space: normal; }
            a { color: inherit !important; text-decoration: none !important; }
        }
    </style>
</head>
<body>
    <div class="d-flex app-shell">
        <!-- Sidebar -->
        <div id="nav-backdrop" class="nav-backdrop" hidden></div>
        <nav class="sidebar" id="staff-navigation" aria-label="Navigasi petugas">
            <div class="sidebar-brand"><span aria-hidden="true">+</span>{{ config('app.name', 'Klinik') }}</div>
            <div class="nav-caption">Ruang pelayanan</div>
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-pie w-20"></i> Dashboard
            </a>
            <a href="{{ route('patients.index') }}" class="{{ request()->routeIs('patients.*') ? 'active' : '' }}">
                <i class="fa-solid fa-hospital-user w-20"></i> Data Pasien
            </a>
            <a href="{{ route('visits.index') }}" class="{{ request()->routeIs('visits.*') ? 'active' : '' }}">
                <i class="fa-solid fa-calendar-check w-20"></i> Kunjungan
            </a>
            <a href="{{ route('odontogram.index') }}" class="{{ request()->routeIs('odontogram.*') ? 'active' : '' }}">
                <i class="fa-solid fa-tooth w-20"></i> Odontogram
            </a>
            <a href="{{ route('medical_records.index') }}" class="{{ request()->routeIs('medical_records.*') ? 'active' : '' }}">
                <i class="fa-solid fa-clock-rotate-left w-20"></i> Riwayat Pasien
            </a>
            <details {{ request()->routeIs('doctors.*') ? 'open' : '' }}>
                <summary >Pengelolaan Dokter</summary>
                <a href="{{ route('doctors.index') }}" class="ps-4 {{ request()->routeIs('doctors.index', 'doctors.show') ? 'active' : '' }}">Data & Detail Dokter</a>
                <a href="{{ route('doctors.create') }}" class="ps-4 {{ request()->routeIs('doctors.create') ? 'active' : '' }}">Tambah Dokter</a>
            </details>
            <div class="nav-caption">Laporan & analitik</div>
            <a href="{{ route('reports.visits') }}" class="{{ request()->routeIs('reports.visits') ? 'active' : '' }}">
                <i class="fa-solid fa-file-waveform w-20"></i> Laporan Kunjungan
            </a>
            <a href="{{ route('reports.charts') }}" class="{{ request()->routeIs('reports.charts') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-line w-20"></i> Grafik Kunjungan
            </a>
            <a href="{{ route('reports.finance') }}" class="{{ request()->routeIs('reports.finance') ? 'active' : '' }}">
                <i class="fa-solid fa-file-invoice-dollar w-20"></i> Laporan Keuangan
            </a>
            <a href="{{ route('reports.treatments') }}" class="{{ request()->routeIs('reports.treatments') ? 'active' : '' }}">
                <i class="fa-solid fa-clipboard-check w-20"></i> Laporan Tindakan
            </a>
            <a href="{{ route('reports.doctors') }}" class="{{ request()->routeIs('reports.doctors') ? 'active' : '' }}">
                <i class="fa-solid fa-user-doctor w-20"></i> Laporan Dokter
            </a>
            <div class="sidebar-foot">Ruang kerja petugas<br>Pelayanan yang lebih terarah.</div>
        </nav>

        <!-- Main content -->
        <div class="flex-grow-1 d-flex flex-column min-vh-100 app-main">
            <div class="topbar d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <button type="button" class="btn btn-outline-primary mobile-menu" id="nav-toggle" aria-controls="staff-navigation" aria-expanded="false" aria-label="Buka navigasi"><i class="fa-solid fa-bars" aria-hidden="true"></i></button>
                    <div><span class="workspace-label">Ruang kerja klinik</span><strong class="workspace-title">{{ now()->locale('id')->translatedFormat('l, d F Y') }}</strong></div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    @auth
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light dropdown-toggle px-3 py-1 shadow-sm fw-semibold" type="button" data-bs-toggle="dropdown">
                                <i class="fa-solid fa-circle-user text-primary me-1"></i> {{ Auth::user()->name }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                                <li>
                                    <div class="px-3 py-2 small text-muted border-bottom">
                                        Masuk sebagai: <strong>{{ Auth::user()->name }}</strong>
                                    </div>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2" href="{{ route('profile.edit') }}">
                                        <i class="fa-solid fa-user-pen text-primary me-2"></i> Profil & Ganti Password
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider my-1"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item py-2 text-danger">
                                            <i class="fa-solid fa-arrow-right-from-bracket me-2"></i> Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-sm btn-light">
                            <i class="fa-solid fa-arrow-right-to-bracket me-1"></i> Login
                        </a>
                    @endauth
                </div>
            </div>

            <div class="p-4 flex-grow-1 app-content">
                {{ $slot ?? '' }}
                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (() => {
            const toggle = document.getElementById('nav-toggle');
            const nav = document.getElementById('staff-navigation');
            const backdrop = document.getElementById('nav-backdrop');
            function closeMenu() { nav.classList.remove('is-open'); backdrop.hidden = true; toggle.setAttribute('aria-expanded', 'false'); }
            toggle.addEventListener('click', () => { const open = !nav.classList.contains('is-open'); nav.classList.toggle('is-open', open); backdrop.hidden = !open; toggle.setAttribute('aria-expanded', String(open)); if (open) nav.querySelector('a').focus(); });
            backdrop.addEventListener('click', () => { closeMenu(); toggle.focus(); });
            document.addEventListener('keydown', event => { if (event.key === 'Escape') { closeMenu(); toggle.focus(); } });
            nav.addEventListener('focusout', () => setTimeout(() => { if (!nav.contains(document.activeElement) && document.activeElement !== toggle) closeMenu(); }, 0));
            window.matchMedia('(min-width: 768px)').addEventListener('change', closeMenu);
        })();
    </script>
    @stack('scripts')
</body>
</html>