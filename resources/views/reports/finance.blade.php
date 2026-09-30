@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1"><i class="fa-solid fa-file-invoice-dollar text-primary me-2"></i>Laporan Keuangan & Pendapatan Klinik</h3>
            <p class="text-muted mb-0">Rekapitulasi pendapatan kasir poliklinik, biaya tindakan, konsultasi, dan obat</p>
        </div>
        <button onclick="window.print()" class="btn btn-primary px-3 shadow-sm fw-semibold">
            <i class="fa-solid fa-print me-1"></i> Cetak Laporan Keuangan
        </button>
    </div>

    <!-- Filter Bar -->
    <div class="card border-0 shadow-sm rounded-3 mb-4 d-print-none">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('reports.finance') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-secondary">Tanggal Mulai</label>
                    <input type="date" class="form-control" name="start_date" value="{{ $startDate }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-secondary">Tanggal Sampai</label>
                    <input type="date" class="form-control" name="end_date" value="{{ $endDate }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold text-secondary">Status Kasir</label>
                    <select class="form-select" name="status_pembayaran">
                        <option value="">-- Semua Status --</option>
                        <option value="Lunas" {{ $statusPembayaran === 'Lunas' ? 'selected' : '' }}>Lunas</option>
                        <option value="Belum Lunas" {{ $statusPembayaran === 'Belum Lunas' ? 'selected' : '' }}>Belum Lunas</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold text-secondary">Metode Bayar</label>
                    <select class="form-select" name="metode_pembayaran">
                        <option value="">-- Semua Metode --</option>
                        <option value="Tunai" {{ $metodePembayaran === 'Tunai' ? 'selected' : '' }}>Tunai</option>
                        <option value="QRIS" {{ $metodePembayaran === 'QRIS' ? 'selected' : '' }}>QRIS</option>
                        <option value="Transfer" {{ $metodePembayaran === 'Transfer' ? 'selected' : '' }}>Transfer Bank</option>
                        <option value="BPJS" {{ $metodePembayaran === 'BPJS' ? 'selected' : '' }}>Klaim BPJS</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary w-100 fw-semibold">
                        <i class="fa-solid fa-filter me-1"></i> Filter
                    </button>
                    <a href="{{ route('reports.finance') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Financial KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body">
                    <div class="text-muted small">Total Penerimaan (Lunas)</div>
                    <h3 class="fw-bold text-success mb-0">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body">
                    <div class="text-muted small">Jasa Konsultasi</div>
                    <h5 class="fw-bold text-primary mb-0">Rp {{ number_format($totalKonsultasi, 0, ',', '.') }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body">
                    <div class="text-muted small">Biaya Tindakan</div>
                    <h5 class="fw-bold text-info mb-0">Rp {{ number_format($totalTindakan, 0, ',', '.') }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body">
                    <div class="text-muted small">Biaya Obat</div>
                    <h5 class="fw-bold text-secondary mb-0">Rp {{ number_format($totalObat, 0, ',', '.') }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body">
                    <div class="text-muted small">Piutang / Belum Lunas</div>
                    <h4 class="fw-bold text-danger mb-0">Rp {{ number_format($totalPiutang, 0, ',', '.') }}</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Print Header -->
    <div class="d-none d-print-block text-center mb-4 pb-2 border-bottom">
        <h3 class="fw-bold mb-1">KLINIK PRATAMA MEDIKA</h3>
        <p class="mb-1 text-muted">LAPORAN KEUANGAN & PENDAPATAN KLINIK</p>
        <small class="text-muted">Periode: {{ date('d/m/Y', strtotime($startDate)) }} s/d {{ date('d/m/Y', strtotime($endDate)) }}</small>
    </div>

    <!-- Financial Table -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 50px;">No.</th>
                            <th>Tanggal</th>
                            <th>No. Antrean & RM</th>
                            <th>Nama Pasien</th>
                            <th>Poli & Dokter</th>
                            <th class="text-end">Konsultasi</th>
                            <th class="text-end">Tindakan</th>
                            <th class="text-end">Obat</th>
                            <th class="text-end">Total Biaya</th>
                            <th>Status & Metode</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($visits as $idx => $v)
                            <tr>
                                <td class="text-center">{{ $idx + 1 }}</td>
                                <td>{{ $v->tanggal_kunjungan->format('d/m/Y') }}</td>
                                <td>
                                    <span class="badge bg-primary">{{ $v->no_antrean }}</span>
                                    <div class="small text-muted">{{ $v->patient ? $v->patient->no_rm : '-' }}</div>
                                </td>
                                <td class="fw-bold text-dark">{{ $v->patient ? $v->patient->nama : '-' }}</td>
                                <td>
                                    <div class="small fw-semibold">{{ $v->poli }}</div>
                                    <small class="text-muted">{{ $v->doctor ? $v->doctor->nama : '-' }}</small>
                                </td>
                                <td class="text-end">Rp {{ number_format($v->biaya_konsultasi, 0, ',', '.') }}</td>
                                <td class="text-end">Rp {{ number_format($v->biaya_tindakan, 0, ',', '.') }}</td>
                                <td class="text-end">Rp {{ number_format($v->biaya_obat, 0, ',', '.') }}</td>
                                <td class="text-end fw-bold text-primary">Rp {{ number_format($v->total_biaya, 0, ',', '.') }}</td>
                                <td>
                                    @if ($v->status_pembayaran === 'Lunas')
                                        <span class="badge bg-success">Lunas</span>
                                    @else
                                        <span class="badge bg-danger">Belum Lunas</span>
                                    @endif
                                    <div class="small text-muted">{{ $v->metode_pembayaran ?: '-' }}</div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-5 text-muted">
                                    Tidak ada transaksi keuangan pada periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if ($visits->count() > 0)
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td colspan="5" class="text-end">TOTAL KESELURUHAN:</td>
                                <td class="text-end">Rp {{ number_format($visits->sum('biaya_konsultasi'), 0, ',', '.') }}</td>
                                <td class="text-end">Rp {{ number_format($visits->sum('biaya_tindakan'), 0, ',', '.') }}</td>
                                <td class="text-end">Rp {{ number_format($visits->sum('biaya_obat'), 0, ',', '.') }}</td>
                                <td class="text-end text-primary fs-6">Rp {{ number_format($visits->sum('total_biaya'), 0, ',', '.') }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
