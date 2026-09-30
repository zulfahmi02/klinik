@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1"><i class="fa-solid fa-user-doctor text-primary me-2"></i>Laporan Kinerja & Pelayanan Dokter</h3>
            <p class="text-muted mb-0">Rekapitulasi aktivitas dokter, volume pasien yang ditangani, dan kontribusi layanan</p>
        </div>
        <button onclick="window.print()" class="btn btn-primary px-3 shadow-sm fw-semibold">
            <i class="fa-solid fa-print me-1"></i> Cetak Laporan Dokter
        </button>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif

    <!-- Filter Bar -->
    <div class="card border-0 shadow-sm rounded-3 mb-4 d-print-none">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('reports.doctors') }}" class="row g-3">
                <div class="col-md-5">
                    <label class="form-label small fw-semibold text-secondary">Tanggal Mulai</label>
                    <input type="date" class="form-control" name="start_date" value="{{ $startDate }}">
                </div>
                <div class="col-md-5">
                    <label class="form-label small fw-semibold text-secondary">Tanggal Sampai</label>
                    <input type="date" class="form-control" name="end_date" value="{{ $endDate }}">
                </div>
                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary w-100 fw-semibold">
                        <i class="fa-solid fa-filter me-1"></i> Filter
                    </button>
                    <a href="{{ route('reports.doctors') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Doctor Performance Summary Table -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white py-3 border-bottom">
            <h5 class="card-title mb-0 fw-bold text-dark">
                <i class="fa-solid fa-ranking-star text-warning me-2"></i>Rekapitulasi Kinerja Dokter
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 50px;">No.</th>
                            <th>Nama Dokter & Spesialisasi</th>
                            <th>Nomor SIP</th>
                            <th class="text-center">Total Pasien</th>
                            <th class="text-center">Pelayanan Selesai</th>
                            <th class="text-center">Tindakan Dilakukan</th>
                            <th class="text-end">Jasa Konsultasi</th>
                            <th class="text-end">Biaya Tindakan</th>
                            <th class="text-end pe-3">Total Nilai Layanan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($doctorStats as $idx => $doc)
                            <tr>
                                <td class="text-center">{{ $idx + 1 }}</td>
                                <td>
                                    <div class="fw-bold text-dark fs-6">{{ $doc['nama'] }}</div>
                                    <span class="badge bg-light text-primary border">{{ $doc['spesialisasi'] }}</span>
                                </td>
                                <td class="small text-muted">{{ $doc['no_sip'] ?: '-' }}</td>
                                <td class="text-center">
                                    <span class="badge bg-primary fs-6 px-3 py-1">{{ $doc['total_pasien'] }} Pasien</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-success fs-6 px-3 py-1">{{ $doc['pasien_selesai'] }} Selesai</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info text-white fs-6 px-3 py-1">{{ $doc['total_tindakan'] }}x</span>
                                </td>
                                <td class="text-end fw-semibold">
                                    Rp {{ number_format($doc['total_konsultasi'], 0, ',', '.') }}
                                </td>
                                <td class="text-end fw-semibold">
                                    Rp {{ number_format($doc['total_tindakan_rp'], 0, ',', '.') }}
                                </td>
                                <td class="text-end pe-3 fw-bold text-success fs-6">
                                    Rp {{ number_format($doc['total_pendapatan'], 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">
                                    Tidak ada data dokter tercatat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if ($doctorStats->count() > 0)
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td colspan="3" class="text-end">TOTAL KESELURUHAN:</td>
                                <td class="text-center">{{ $doctorStats->sum('total_pasien') }} Pasien</td>
                                <td class="text-center">{{ $doctorStats->sum('pasien_selesai') }} Selesai</td>
                                <td class="text-center">{{ $doctorStats->sum('total_tindakan') }}x</td>
                                <td class="text-end">Rp {{ number_format($doctorStats->sum('total_konsultasi'), 0, ',', '.') }}</td>
                                <td class="text-end">Rp {{ number_format($doctorStats->sum('total_tindakan_rp'), 0, ',', '.') }}</td>
                                <td class="text-end pe-3 text-success fs-6">
                                    Rp {{ number_format($doctorStats->sum('total_pendapatan'), 0, ',', '.') }}
                                </td>
                                
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

    <!-- Doctor Cards Grid for Quick Overview -->
    <div class="row g-3 d-print-none">
        @foreach ($doctorStats as $doc)
            <div class="col-md-6 col-lg-3">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-body text-center p-4">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex p-3 fs-3 mb-3">
                            <i class="fa-solid fa-user-doctor"></i>
                        </div>
                        <h6 class="fw-bold mb-1 text-dark">{{ $doc['nama'] }}</h6>
                        <div class="small text-primary mb-3">{{ $doc['spesialisasi'] }}</div>
                        <div class="border-top pt-3">
                            <div class="d-flex justify-content-between text-muted small mb-1">
                                <span>Pasien Ditangani:</span>
                                <strong class="text-dark">{{ $doc['total_pasien'] }}</strong>
                            </div>
                            <div class="d-flex justify-content-between text-muted small mb-1">
                                <span>Tindakan:</span>
                                <strong class="text-dark">{{ $doc['total_tindakan'] }}</strong>
                            </div>
                            <div class="d-flex justify-content-between text-muted small">
                                <span>Total Kontribusi:</span>
                                <strong class="text-success">Rp {{ number_format($doc['total_pendapatan'], 0, ',', '.') }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
