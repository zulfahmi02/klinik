@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1"><i class="fa-solid fa-folder-open text-primary me-2"></i>Berkas Rekam Medis Pasien</h3>
            <p class="text-muted mb-0">No. RM: <strong>{{ $patient->no_rm }}</strong> &bull; Nama: <strong>{{ $patient->nama }}</strong></p>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-outline-secondary">
                <i class="fa-solid fa-print me-1"></i> Cetak Rekam Medis
            </button>
            <a href="{{ route('medical_records.index') }}" class="btn btn-outline-primary">
                <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Pencarian
            </a>
        </div>
    </div>

    <!-- Patient Identity Header Card -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-primary text-white py-3">
            <h5 class="card-title mb-0 fs-6 fw-bold">
                <i class="fa-solid fa-address-card me-2"></i>Identitas Pasien
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="text-muted small">Nomor Rekam Medis:</div>
                    <div class="fs-5 fw-bold text-primary">{{ $patient->no_rm }}</div>
                </div>
                <div class="col-md-3">
                    <div class="text-muted small">Nama Lengkap:</div>
                    <div class="fs-5 fw-bold text-dark">{{ $patient->nama }}</div>
                </div>
                <div class="col-md-3">
                    <div class="text-muted small">NIK (KTP):</div>
                    <div class="fw-semibold">{{ $patient->nik ?? '-' }}</div>
                </div>
                <div class="col-md-3">
                    <div class="text-muted small">Jenis Kelamin:</div>
                    <div class="fw-semibold">{{ $patient->jenis_kelamin === 'L' ? 'Laki-laki (L)' : 'Perempuan (P)' }}</div>
                </div>
                <div class="col-md-3">
                    <div class="text-muted small">Tanggal Lahir / Umur:</div>
                    <div class="fw-semibold">
                        {{ $patient->tanggal_lahir ? $patient->tanggal_lahir->format('d F Y') : '-' }}
                        @if ($patient->tanggal_lahir)
                            ({{ $patient->tanggal_lahir->age }} Tahun)
                        @endif
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-muted small">No. Handphone / WhatsApp:</div>
                    <div class="fw-semibold">{{ $patient->no_hp ?? '-' }}</div>
                </div>
                <div class="col-md-3">
                    <div class="text-muted small">Penjamin / No. BPJS:</div>
                    <div class="fw-semibold">
                        <span class="badge bg-light text-dark border">{{ $patient->penjamin ?? 'Umum' }}</span>
                        {{ $patient->no_penjamin ? "({$patient->no_penjamin})" : '' }}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-muted small">Riwayat Alergi Obat / Makanan:</div>
                    <div>
                        @if ($patient->alergi)
                            <span class="badge bg-danger fs-6">{{ $patient->alergi }}</span>
                        @else
                            <span class="badge bg-success-subtle text-success border border-success-subtle">Tidak Ada Alergi</span>
                        @endif
                    </div>
                </div>
                <div class="col-12 border-top pt-2">
                    <div class="text-muted small">Alamat Domisili:</div>
                    <div class="fw-semibold">{{ $patient->alamat ?? '-' }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Dental Odontogram Summary (If any) -->
    @if ($odontograms->count() > 0)
        <div class="card border-0 shadow-sm rounded-3 mb-4 border-start border-warning border-4">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0 fw-bold text-dark">
                    <i class="fa-solid fa-tooth text-warning me-2"></i>Catatan Odontogram Gigi Terakhir
                </h6>
                <a href="{{ route('odontogram.show', $patient) }}" class="btn btn-sm btn-outline-warning text-dark fw-semibold">
                    <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Buka Odontogram Penuh
                </a>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    @foreach ($odontograms as $od)
                        <div class="col-md-3">
                            <div class="p-2 border rounded-3 bg-light">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="badge bg-dark">Gigi #{{ $od->nomor_gigi }}</span>
                                    <span class="badge bg-danger">{{ $od->kondisi }}</span>
                                </div>
                                <div class="small fw-semibold text-primary">{{ $od->tindakan ?: 'Perlu evaluasi' }}</div>
                                <div class="text-muted" style="font-size: 11px;">{{ $od->catatan ?: '-' }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Medical Visits Timeline -->
    <h5 class="fw-bold mb-3 text-dark">
        <i class="fa-solid fa-timeline text-primary me-2"></i>Riwayat Kronologis Kunjungan & Pemeriksaan ({{ $visits->count() }} Kunjungan)
    </h5>

    @forelse ($visits as $visit)
        <div class="card border-0 shadow-sm rounded-3 mb-3">
            <div class="card-header bg-light py-3 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary fs-6">{{ $visit->no_antrean }}</span>
                    <span class="fw-bold text-dark fs-6">{{ $visit->tanggal_kunjungan->format('d F Y') }}</span>
                    <span class="text-muted small">({{ $visit->jam_kunjungan ?? '-' }} WIB)</span>
                    <span class="badge bg-white text-dark border ms-2">{{ $visit->poli }}</span>
                </div>
                <div>
                    @if ($visit->status === 'Selesai')
                        <span class="badge bg-success"><i class="fa-solid fa-check me-1"></i> Selesai</span>
                    @elseif ($visit->status === 'Sedang Diperiksa')
                        <span class="badge bg-info text-white">Sedang Diperiksa</span>
                    @else
                        <span class="badge bg-warning text-dark">Menunggu</span>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4 border-end">
                        <div class="small text-muted mb-1">Dokter Pemeriksa:</div>
                        <div class="fw-bold text-dark">{{ $visit->doctor ? $visit->doctor->nama : 'Tidak tercatat' }}</div>
                        <div class="small text-muted mb-3">{{ $visit->doctor ? $visit->doctor->spesialisasi : '' }}</div>

                        <div class="small text-muted mb-1">Tanda-Tanda Vital:</div>
                        <div class="d-flex gap-2 flex-wrap">
                            <span class="badge bg-light text-dark border">TD: {{ $visit->tensi_darah ?: '-' }}</span>
                            <span class="badge bg-light text-dark border">Suhu: {{ $visit->suhu_tubuh ?: '-' }}</span>
                            <span class="badge bg-light text-dark border">BB: {{ $visit->berat_badan ?: '-' }}</span>
                        </div>
                    </div>

                    <div class="col-md-8">
                        <div class="mb-2">
                            <span class="text-muted small fw-bold">Keluhan Pasien:</span>
                            <div class="text-dark">{{ $visit->keluhan ?: '-' }}</div>
                        </div>

                        <div class="mb-2">
                            <span class="text-muted small fw-bold">Diagnosa Medis:</span>
                            <div class="fw-bold text-primary fs-6">{{ $visit->diagnosa ?: 'Belum diisi' }}</div>
                        </div>

                        @if ($visit->visitTreatments->count() > 0)
                            <div class="mb-2">
                                <span class="text-muted small fw-bold">Tindakan Medis yang Diberikan:</span>
                                <div>
                                    @foreach ($visit->visitTreatments as $vt)
                                        <span class="badge bg-warning-subtle text-dark border border-warning-subtle me-1">
                                            {{ $vt->treatment ? $vt->treatment->nama : '' }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if ($visit->catatan_dokter)
                            <div class="mb-0">
                                <span class="text-muted small fw-bold">Instruksi Terapi / Resep:</span>
                                <div class="p-2 bg-light rounded-2 small text-dark">{{ $visit->catatan_dokter }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="card-footer bg-white border-top text-end py-2">
                <a href="{{ route('visits.show', $visit) }}" class="btn btn-sm btn-outline-info">
                    <i class="fa-solid fa-eye me-1"></i> Rincian Kunjungan & Tagihan
                </a>
            </div>
        </div>
    @empty
        <div class="card border-0 shadow-sm rounded-3 py-5 text-center text-muted">
            <i class="fa-solid fa-calendar-xmark fs-2 mb-2 text-secondary"></i>
            <h5>Belum ada riwayat kunjungan medis untuk pasien ini.</h5>
            <p class="small mb-0">Pasien dapat didaftarkan pada menu Kunjungan.</p>
        </div>
    @endforelse
</div>
@endsection
