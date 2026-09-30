@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1"><i class="fa-solid fa-clipboard-check text-primary me-2"></i>Laporan Tindakan Medis</h3>
            <p class="text-muted mb-0">Rekapitulasi prosedur medis, tindakan gigi, bedah minor, dan laboratorium</p>
        </div>
        <button onclick="window.print()" class="btn btn-primary px-3 shadow-sm fw-semibold">
            <i class="fa-solid fa-print me-1"></i> Cetak Laporan Tindakan
        </button>
    </div>

    <!-- Filter Bar -->
    <div class="card border-0 shadow-sm rounded-3 mb-4 d-print-none">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('reports.treatments') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold text-secondary">Tanggal Mulai</label>
                    <input type="date" class="form-control" name="start_date" value="{{ $startDate }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold text-secondary">Tanggal Sampai</label>
                    <input type="date" class="form-control" name="end_date" value="{{ $endDate }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold text-secondary">Kategori Tindakan</label>
                    <select class="form-select" name="kategori">
                        <option value="">-- Semua Kategori --</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat }}" {{ $kategori === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary w-100 fw-semibold">
                        <i class="fa-solid fa-filter me-1"></i> Filter
                    </button>
                    <a href="{{ route('reports.treatments') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Aggregated Procedures Summary -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white py-3 border-bottom">
            <h5 class="card-title mb-0 fw-bold text-dark">
                <i class="fa-solid fa-chart-simple text-primary me-2"></i>Rekapitulasi Frekuensi & Pendapatan per Jenis Tindakan
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Kode</th>
                            <th>Nama Tindakan Medis</th>
                            <th>Kategori</th>
                            <th class="text-end">Tarif Satuan</th>
                            <th class="text-center">Jumlah Dilakukan</th>
                            <th class="text-end pe-3">Total Penerimaan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($treatmentSummary as $item)
                            <tr>
                                <td class="ps-3 fw-bold text-primary">{{ $item['kode'] }}</td>
                                <td class="fw-semibold">{{ $item['nama'] }}</td>
                                <td><span class="badge bg-light text-dark border">{{ $item['kategori'] }}</span></td>
                                <td class="text-end">Rp {{ number_format($item['tarif'], 0, ',', '.') }}</td>
                                <td class="text-center">
                                    <span class="badge bg-primary fs-6 px-3 py-1">{{ $item['jumlah'] }}x</span>
                                </td>
                                <td class="text-end pe-3 fw-bold text-success fs-6">
                                    Rp {{ number_format($item['total_biaya'], 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    Tidak ada data tindakan pada periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if ($treatmentSummary->count() > 0)
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td colspan="4" class="text-end">TOTAL KESELURUHAN:</td>
                                <td class="text-center">{{ $treatmentSummary->sum('jumlah') }}x Tindakan</td>
                                <td class="text-end pe-3 text-success fs-6">
                                    Rp {{ number_format($treatmentSummary->sum('total_biaya'), 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

    <!-- Detailed Action Log Table -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3 border-bottom">
            <h6 class="card-title mb-0 fw-bold text-dark">
                <i class="fa-solid fa-list text-secondary me-2"></i>Rincian Log Pelaksanaan Tindakan
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 50px;">No.</th>
                            <th>Tanggal</th>
                            <th>No. RM & Pasien</th>
                            <th>Tindakan Dilakukan</th>
                            <th>Dokter Pelaksana</th>
                            <th>Catatan Medis</th>
                            <th class="text-end">Biaya</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($records as $idx => $r)
                            <tr>
                                <td class="text-center">{{ $idx + 1 }}</td>
                                <td>{{ $r->visit->tanggal_kunjungan->format('d/m/Y') }}</td>
                                <td>
                                    <span class="text-primary fw-semibold">{{ $r->visit->patient ? $r->visit->patient->no_rm : '-' }}</span>
                                    <div class="fw-bold">{{ $r->visit->patient ? $r->visit->patient->nama : '-' }}</div>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $r->treatment ? $r->treatment->nama : '-' }}</div>
                                    <small class="badge bg-light text-secondary border">{{ $r->treatment ? $r->treatment->kategori : '' }}</small>
                                </td>
                                <td>{{ $r->visit->doctor ? $r->visit->doctor->nama : 'Belum Ditentukan' }}</td>
                                <td class="small text-muted">{{ $r->catatan ?: '-' }}</td>
                                <td class="text-end fw-semibold text-primary">Rp {{ number_format($r->biaya, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    Tidak ada log pelaksanaan tindakan medis pada periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
