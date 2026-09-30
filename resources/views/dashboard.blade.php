@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <section class="dashboard-welcome">
        <div><span class="dashboard-eyebrow">Ringkasan pelayanan hari ini</span><h1>Selamat datang, {{ Auth::user()->name }}.</h1><p>Pantau kunjungan, kelola antrean, dan siapkan pelayanan terbaik untuk setiap pasien.</p></div>
        <a href="{{ route('visits.create') }}" class="btn btn-primary text-nowrap"><i class="fa-solid fa-plus me-2"></i>Kunjungan Baru</a>
    </section>
    <div class="row g-3">
        @foreach ([
            ['Total pasien', number_format($totalPasien, 0, ',', '.'), 'users', 'Seluruh pasien terdaftar'],
            ['Kunjungan hari ini', $kunjunganHariIni, 'calendar-check', 'Kunjungan tercatat hari ini'],
            ['Antrean menunggu', $antreanMenunggu, 'clock', 'Menunggu pemeriksaan'],
            ['Pendapatan hari ini', 'Rp '.number_format($pendapatanHariIni, 0, ',', '.'), 'wallet', 'Kunjungan hari ini yang lunas'],
        ] as [$label, $value, $icon, $note])
        <div class="col-6 col-xl-3"><div class="card metric-card"><div class="metric-top"><span>{{ $label }}</span><span class="metric-icon"><i class="fa-solid fa-{{ $icon }}" aria-hidden="true"></i></span></div><div class="metric-value">{{ $value }}</div><div class="metric-note">{{ $note }}</div></div></div>
        @endforeach
    </div>
    <h2 class="section-label">Mulai dari sini</h2>
    <div class="row g-3 mb-4">
        @foreach ([
            ['visits.index', 'calendar-check', 'Antrean kunjungan', 'Kelola pelayanan pasien'],
            ['odontogram.index', 'tooth', 'Odontogram', 'Pemeriksaan Poli Gigi'],
            ['medical_records.index', 'notes-medical', 'Rekam medis', 'Lihat riwayat pasien'],
            ['reports.charts', 'chart-line', 'Grafik kunjungan', 'Kenali tren pelayanan'],
        ] as [$route, $icon, $label, $description])
        <div class="col-6 col-xl-3"><a class="shortcut" href="{{ route($route) }}"><i class="fa-solid fa-{{ $icon }}" aria-hidden="true"></i><div><strong>{{ $label }}</strong><small>{{ $description }}</small></div></a></div>
        @endforeach
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

@endsection
