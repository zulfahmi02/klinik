@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1"><i class="fa-solid fa-calendar-check text-primary me-2"></i>Kunjungan & Antrean Pasien</h3>
            <p class="text-muted mb-0">Kelola registrasi kunjungan, antrean poliklinik, dan pelayanan medis</p>
        </div>
        <a href="{{ route('visits.create') }}" class="btn btn-primary px-3 shadow-sm fw-semibold">
            <i class="fa-solid fa-plus me-1"></i> Daftarkan Kunjungan Baru
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Statistic Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-3 me-3 fs-3">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Total Kunjungan Hari Ini</div>
                        <h4 class="fw-bold mb-0 text-dark">{{ $totalHariIni }}</h4>
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
                        <div class="text-muted small">Menunggu Antrean</div>
                        <h4 class="fw-bold mb-0 text-warning">{{ $menungguCount }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-info bg-opacity-10 text-info p-3 rounded-3 me-3 fs-3">
                        <i class="fa-solid fa-stethoscope"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Sedang Diperiksa</div>
                        <h4 class="fw-bold mb-0 text-info">{{ $diperiksaCount }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-success bg-opacity-10 text-success p-3 rounded-3 me-3 fs-3">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Pelayanan Selesai</div>
                        <h4 class="fw-bold mb-0 text-success">{{ $selesaiCount }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('visits.index') }}" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-secondary">Tanggal Kunjungan</label>
                    <input type="date" class="form-control form-control-sm" name="tanggal" value="{{ $date }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-secondary">Status Pelayanan</label>
                    <select class="form-select form-select-sm" name="status">
                        <option value="">-- Semua Status --</option>
                        <option value="Menunggu" {{ $status === 'Menunggu' ? 'selected' : '' }}>Menunggu</option>
                        <option value="Sedang Diperiksa" {{ $status === 'Sedang Diperiksa' ? 'selected' : '' }}>Sedang Diperiksa</option>
                        <option value="Selesai" {{ $status === 'Selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="Batal" {{ $status === 'Batal' ? 'selected' : '' }}>Batal</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold text-secondary">Cari Pasien (Nama / No. RM)</label>
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control" name="search" value="{{ $search }}" placeholder="Ketik nama atau No. RM...">
                        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-search"></i></button>
                    </div>
                </div>
                <div class="col-md-2 d-flex gap-1">
                    <a href="{{ route('visits.index') }}" class="btn btn-sm btn-outline-secondary w-100">
                        <i class="fa-solid fa-rotate-left"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Visits Table -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Antrean</th>
                            <th>Waktu</th>
                            <th>Pasien</th>
                            <th>Poliklinik</th>
                            <th>Dokter Pemeriksa</th>
                            <th>Keluhan</th>
                            <th>Penjamin</th>
                            <th>Status</th>
                            <th>Pembayaran</th>
                            <th class="text-end pe-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($visits as $visit)
                            <tr>
                                <td class="ps-3">
                                    <span class="badge bg-primary fs-6 px-2 py-1">{{ $visit->no_antrean }}</span>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $visit->jam_kunjungan ?? '-' }}</div>
                                    <small class="text-muted">{{ $visit->tanggal_kunjungan->format('d/m/Y') }}</small>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $visit->patient ? $visit->patient->nama : '-' }}</div>
                                    <small class="text-muted">RM: {{ $visit->patient ? $visit->patient->no_rm : '-' }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ $visit->poli }}</span>
                                </td>
                                <td>
                                    <div class="small fw-semibold">{{ $visit->doctor ? $visit->doctor->nama : 'Belum Ditentukan' }}</div>
                                    <small class="text-muted">{{ $visit->doctor ? $visit->doctor->spesialisasi : '-' }}</small>
                                </td>
                                <td style="max-width: 180px;">
                                    <span class="text-truncate d-inline-block" style="max-width: 180px;" title="{{ $visit->keluhan }}">
                                        {{ $visit->keluhan ?? '-' }}
                                    </span>
                                </td>
                                <td>
                                    @if ($visit->penjamin === 'BPJS')
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success">BPJS</span>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border">{{ $visit->penjamin }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($visit->status === 'Menunggu')
                                        <span class="badge bg-warning text-dark"><i class="fa-solid fa-clock me-1"></i> Menunggu</span>
                                    @elseif ($visit->status === 'Sedang Diperiksa')
                                        <span class="badge bg-info text-white"><i class="fa-solid fa-stethoscope me-1"></i> Diperiksa</span>
                                    @elseif ($visit->status === 'Selesai')
                                        <span class="badge bg-success"><i class="fa-solid fa-check me-1"></i> Selesai</span>
                                    @else
                                        <span class="badge bg-danger">Batal</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($visit->status_pembayaran === 'Lunas')
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">
                                            <i class="fa-solid fa-check-circle me-1"></i> Lunas
                                        </span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                            <i class="fa-solid fa-circle-exclamation me-1"></i> Belum Lunas
                                        </span>
                                    @endif
                                    <div class="small text-muted">Rp {{ number_format($visit->total_biaya, 0, ',', '.') }}</div>
                                </td>
                                <td class="text-end pe-3">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('visits.show', $visit) }}" class="btn btn-outline-info" title="Detail Kunjungan">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        <a href="{{ route('visits.edit', $visit) }}" class="btn btn-outline-primary" title="Periksa / Edit Medis">
                                            <i class="fa-solid fa-user-doctor"></i> Periksa
                                        </a>
                                        <form action="{{ route('visits.destroy', $visit) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus kunjungan antrean ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Hapus">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-calendar-xmark fs-2 mb-2 d-block text-secondary"></i>
                                    Belum ada data kunjungan pada filter yang dipilih.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($visits->hasPages())
                <div class="p-3 border-top">
                    {{ $visits->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
