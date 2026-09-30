@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1"><i class="fa-solid fa-chart-pie text-primary me-2"></i>Dashboard Klinik</h3>
            <p class="text-muted mb-0">Selamat datang di Sistem Informasi Manajemen Pelayanan Pasien</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('visits.create') }}" class="btn btn-primary fw-semibold shadow-sm">
                <i class="fa-solid fa-calendar-plus me-1"></i> + Kunjungan Baru
            </a>
            <a href="{{ route('patients.create') }}" class="btn btn-outline-primary fw-semibold">
                <i class="fa-solid fa-user-plus me-1"></i> + Pasien Baru
            </a>
        </div>
    </div>

    <!-- KPI Metric Cards Row -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-3 me-3 fs-3">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Total Pasien</div>
                        <h3 class="fw-bold mb-0 text-primary">{{ $totalPasien }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-info bg-opacity-10 text-info p-3 rounded-3 me-3 fs-3">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Kunjungan Hari Ini</div>
                        <h3 class="fw-bold mb-0 text-info">{{ $kunjunganHariIni }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-3 me-3 fs-3">
                        <i class="fa-solid fa-clock"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Antrean Menunggu</div>
                        <h3 class="fw-bold mb-0 text-warning">{{ $antreanMenunggu }}</h3>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-success bg-opacity-10 text-success p-3 rounded-3 me-3 fs-3">
                        <i class="fa-solid fa-cash-register"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Pendapatan Hari Ini</div>
                        <h4 class="fw-bold mb-0 text-success">Rp {{ number_format($pendapatanHariIni, 0, ',', '.') }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Navigation Shortcuts -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <a href="{{ route('visits.index') }}" class="card border-0 shadow-sm rounded-3 text-decoration-none text-dark h-100 hover-card">
                <div class="card-body text-center py-3">
                    <i class="fa-solid fa-calendar-check text-primary fs-3 mb-2"></i>
                    <div class="fw-bold">Antrean Kunjungan</div>
                    <small class="text-muted">Kelola antrean poliklinik</small>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('odontogram.index') }}" class="card border-0 shadow-sm rounded-3 text-decoration-none text-dark h-100 hover-card">
                <div class="card-body text-center py-3">
                    <i class="fa-solid fa-tooth text-info fs-3 mb-2"></i>
                    <div class="fw-bold">Odontogram Gigi</div>
                    <small class="text-muted">Diagram rekam medis gigi</small>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('medical_records.index') }}" class="card border-0 shadow-sm rounded-3 text-decoration-none text-dark h-100 hover-card">
                <div class="card-body text-center py-3">
                    <i class="fa-solid fa-clock-rotate-left text-warning fs-3 mb-2"></i>
                    <div class="fw-bold">Riwayat Pasien</div>
                    <small class="text-muted">Rekam medis & timeline</small>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('reports.charts') }}" class="card border-0 shadow-sm rounded-3 text-decoration-none text-dark h-100 hover-card">
                <div class="card-body text-center py-3">
                    <i class="fa-solid fa-chart-line text-success fs-3 mb-2"></i>
                    <div class="fw-bold">Grafik Analitik</div>
                    <small class="text-muted">Statistik & tren pelayanan</small>
                </div>
            </a>
        </div>
    </div>

    <!-- Recent Visits Table -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0 fw-bold text-dark">
                <i class="fa-solid fa-history text-primary me-2"></i>Kunjungan Pasien Terbaru
            </h5>
            <a href="{{ route('visits.index') }}" class="btn btn-sm btn-outline-primary fw-semibold">
                Lihat Semua Antrean <i class="fa-solid fa-arrow-right ms-1"></i>
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Antrean</th>
                            <th>Tanggal & Waktu</th>
                            <th>Pasien</th>
                            <th>Poli</th>
                            <th>Dokter</th>
                            <th>Keluhan</th>
                            <th>Status</th>
                            <th class="text-end pe-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentVisits as $rv)
                            <tr>
                                <td class="ps-3">
                                    <span class="badge bg-primary fs-6">{{ $rv->no_antrean }}</span>
                                </td>
                                <td>
                                    <strong>{{ $rv->tanggal_kunjungan->format('d/m/Y') }}</strong>
                                    <div class="small text-muted">{{ $rv->jam_kunjungan ?? '-' }}</div>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $rv->patient ? $rv->patient->nama : '-' }}</div>
                                    <small class="text-muted">RM: {{ $rv->patient ? $rv->patient->no_rm : '-' }}</small>
                                </td>
                                <td><span class="badge bg-light text-dark border">{{ $rv->poli }}</span></td>
                                <td>{{ $rv->doctor ? $rv->doctor->nama : 'Belum Ditentukan' }}</td>
                                <td style="max-width: 180px;">
                                    <span class="text-truncate d-inline-block" style="max-width: 180px;">{{ $rv->keluhan ?: '-' }}</span>
                                </td>
                                <td>
                                    @if ($rv->status === 'Selesai')
                                        <span class="badge bg-success">Selesai</span>
                                    @elseif ($rv->status === 'Sedang Diperiksa')
                                        <span class="badge bg-info">Diperiksa</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Menunggu</span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    <a href="{{ route('visits.show', $rv) }}" class="btn btn-sm btn-outline-info">Detail</a>
                                    <a href="{{ route('visits.edit', $rv) }}" class="btn btn-sm btn-outline-primary">Periksa</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">Belum ada kunjungan tercatat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .hover-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .hover-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 20px rgba(37, 99, 235, 0.1) !important;
        border-color: #2563eb !important;
    }
</style>
@endsection