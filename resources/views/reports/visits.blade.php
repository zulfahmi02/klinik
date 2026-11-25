@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1"><i class="fa-solid fa-file-waveform text-primary me-2"></i>Laporan Kunjungan Pasien</h3>
            <p class="text-muted mb-0">Rekapitulasi data kunjungan poliklinik dan riwayat pelayanan</p>
        </div>
        <a href="{{ route('reports.visits.pdf', request()->except('page')) }}" class="btn btn-primary px-3 shadow-sm fw-semibold">Unduh PDF rekap</a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">@foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>
    @endif
    <p class="text-muted">Tanggal kosong menampilkan seluruh riwayat. Laporan ini hanya untuk melihat data; pemeriksaan diisi melalui Kunjungan / Pemeriksaan.</p>
    <!-- Filter Card -->
    <div class="card border-0 shadow-sm rounded-3 mb-4 d-print-none">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('reports.visits') }}" class="row g-3">
                <div class="col-12">
                    <label for="search" class="form-label small fw-semibold text-secondary">Cari kunjungan</label>
                    <input id="search" name="search" type="search" class="form-control" maxlength="255" value="{{ $search }}" placeholder="Nama pasien, No. RM, NIK, No. HP, dokter, keluhan, atau diagnosa">
                    <div class="form-text">Pencarian mengikuti periode dan filter yang dipilih.</div>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-secondary">Tanggal Mulai</label>
                    <input type="date" class="form-control" name="start_date" value="{{ $startDate }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-secondary">Tanggal Sampai</label>
                    <input type="date" class="form-control" name="end_date" value="{{ $endDate }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold text-secondary">Dokter</label>
                    <select class="form-select" name="doctor_id">
                        <option value="">-- Semua Dokter --</option>
                        @foreach ($doctors as $doc)
                            <option value="{{ $doc->id }}" {{ $doctorId == $doc->id ? 'selected' : '' }}>{{ $doc->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary w-100 fw-semibold">
                        <i class="fa-solid fa-filter me-1"></i> Filter
                    </button>
                    <a href="{{ route('reports.visits') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    @if ($search !== '')
        <p class="text-muted">Hasil pencarian: <strong>{{ $search }}</strong></p>
    @endif
    <!-- Summary KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body">
                    <div class="text-muted small">Total Kunjungan</div>
                    <h3 class="fw-bold text-primary mb-0">{{ $totalVisits }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body">
                    <div class="text-muted small">Pelayanan Selesai</div>
                    <h3 class="fw-bold text-success mb-0">{{ $totalSelesai }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body">
                    <div class="text-muted small">Pasien BPJS Kesehatan</div>
                    <h3 class="fw-bold text-info mb-0">{{ $totalBpjs }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body">
                    <div class="text-muted small">Pasien Mandiri / Umum</div>
                    <h3 class="fw-bold text-warning mb-0">{{ $totalUmum }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Print Header Only for Paper Print -->
    <div class="d-none d-print-block text-center mb-4 pb-2 border-bottom">
        <h3 class="fw-bold mb-1">KLINIK PRATAMA MEDIKA</h3>
        <p class="mb-1 text-muted">LAPORAN KUNJUNGAN PASIEN</p>
        <small class="text-muted">Periode: {{ $startDate ?: 'Awal data' }} s/d {{ $endDate ?: 'Seluruh tanggal' }}</small>
    </div>

    <!-- Data Table -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;" class="text-center">No.</th>
                            <th>Tanggal & Jam</th>
                            <th>No. RM</th>
                            <th>Nama Pasien</th>
                            <th>Poli</th>
                            <th>Dokter Pemeriksa</th>
                            <th>Keluhan & Diagnosa</th>
                            <th>Penjamin</th>
                            <th>Status</th>
                            <th>Total Tercatat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($visits as $idx => $v)
                            <tr>
                                <td class="text-center">{{ ($visits->firstItem() ?? 1) + $idx }}</td>
                                <td>
                                    <strong>{{ $v->tanggal_kunjungan->format('d/m/Y') }}</strong>
                                    <div class="small text-muted">{{ $v->jam_kunjungan ?? '-' }}</div>
                                </td>
                                <td class="fw-semibold text-primary">{{ $v->patient ? $v->patient->no_rm : '-' }}</td>
                                <td class="fw-bold text-dark">{{ $v->patient ? $v->patient->nama : '-' }}</td>
                                <td><span class="badge bg-light text-dark border">{{ $v->poli }}</span></td>
                                <td>{{ $v->doctor ? $v->doctor->nama : 'Belum ditentukan' }}</td>
                                <td>
                                    <div><strong>Diagnosa:</strong> {{ $v->diagnosa ?: '-' }}</div>
                                    <small class="text-muted">Keluhan: {{ $v->keluhan ?: '-' }}</small>
                                </td>
                                <td><span class="badge bg-secondary">{{ $v->penjamin }}</span></td>
                                <td>
                                    @if ($v->status === 'Selesai')
                                        <span class="badge bg-success">Selesai</span>
                                    @elseif ($v->status === 'Sedang Diperiksa')
                                        <span class="badge bg-info">Diperiksa</span>
                                    @elseif ($v->status === 'Batal')
                                        <span class="badge bg-danger">Batal</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Terdaftar</span>
                                    @endif
                                </td>
                                <td class="text-end fw-semibold">
                                    Rp {{ number_format($v->total_biaya, 0, ',', '.') }}
                                    <div class="small {{ $v->status_pembayaran === 'Lunas' ? 'text-success' : 'text-danger' }}">
                                        {{ $v->status_pembayaran }}
                                    </div>
                                </td>
                                <td>
                                    <a href="{{ route('reports.examinations.show', $v) }}" class="btn btn-sm btn-outline-info">Detail pemeriksaan</a>
                                    <a href="{{ route('reports.history.show', $v->patient) }}" class="btn btn-sm btn-outline-primary">Riwayat pemeriksaan</a>
                                    @if ($v->poli === 'Poli Gigi')
                                        <a href="{{ route('reports.odontogram.show', $v) }}" class="btn btn-sm btn-outline-secondary">Odontogram kunjungan</a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center py-5 text-muted">
                                    Tidak ada data kunjungan pada rentang tanggal yang dipilih.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="mt-3 d-print-none">{{ $visits->links() }}</div>
</div>
@endsection
