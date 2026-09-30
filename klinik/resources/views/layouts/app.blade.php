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
        body { background-color: #f4f6f9; }
        .sidebar {
            min-height: 100vh;
            background-color: #1e2a3a;
        }
        .sidebar a {
            color: #cfd8e3;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            transition: background-color 0.2s ease, color 0.2s ease;
        }
        .sidebar a:hover, .sidebar a.active {
            background-color: #2d3e50;
            color: #fff;
            border-left: 4px solid #2563eb;
        }
        .topbar {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: #fff;
            padding: 14px 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }
    </style>
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <nav class="sidebar" style="width: 260px;">
            <div class="p-3 text-white fw-bold fs-5 d-flex align-items-center gap-2 border-bottom border-secondary border-opacity-25">
                <i class="fa-solid fa-notes-medical text-primary"></i> Klinik App
            </div>
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-pie w-20"></i> Dashboard
            </a>
            <a href="{{ route('patients.index') }}" class="{{ request()->routeIs('patients.*') ? 'active' : '' }}">
                <i class="fa-solid fa-hospital-user w-20"></i> Pendaftaran Pasien
            </a>
            <a href="{{ route('visits.index') }}" class="{{ request()->routeIs('visits.*') ? 'active' : '' }}">
                <i class="fa-solid fa-calendar-check w-20"></i> Kunjungan
            </a>
            <a href="{{ route('reports.visits') }}" class="{{ request()->routeIs('reports.visits') ? 'active' : '' }}">
                <i class="fa-solid fa-file-waveform w-20"></i> Laporan Kunjungan
            </a>
            <a href="{{ route('reports.charts') }}" class="{{ request()->routeIs('reports.charts') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-line w-20"></i> Grafik Kunjungan
            </a>
            <a href="{{ route('odontogram.index') }}" class="{{ request()->routeIs('odontogram.*') ? 'active' : '' }}">
                <i class="fa-solid fa-tooth w-20"></i> Odontogram
            </a>
            <a href="{{ route('medical_records.index') }}" class="{{ request()->routeIs('medical_records.*') ? 'active' : '' }}">
                <i class="fa-solid fa-clock-rotate-left w-20"></i> Riwayat Pasien
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
        </nav>

        <!-- Main content -->
        <div class="flex-grow-1 d-flex flex-column min-vh-100">
            <div class="topbar d-flex justify-content-between align-items-center">
                <div class="fw-semibold">
                    <i class="fa-solid fa-hospital me-2"></i>Sistem Informasi Pelayanan Pasien
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

            <div class="p-4 flex-grow-1">
                {{ $slot ?? '' }}
                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>