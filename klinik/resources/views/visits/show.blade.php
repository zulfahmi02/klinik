@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1"><i class="fa-solid fa-file-medical text-primary me-2"></i>Detail Kunjungan Pasien</h3>
            <p class="text-muted mb-0">Nomor Antrean: <strong>{{ $visit->no_antrean }}</strong> &bull; Waktu: {{ $visit->tanggal_kunjungan->format('d/m/Y') }} {{ $visit->jam_kunjungan }}</p>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-outline-secondary">
                <i class="fa-solid fa-print me-1"></i> Cetak Resume
            </button>
            <a href="{{ route('visits.edit', $visit) }}" class="btn btn-warning fw-semibold">
                <i class="fa-solid fa-user-doctor me-1"></i> Periksa / Input Medis
            </a>
            <a href="{{ route('visits.index') }}" class="btn btn-outline-primary">
                <i class="fa-solid fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Patient Identity Card -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="card-title mb-0 fs-6 fw-bold">
                        <i class="fa-solid fa-hospital-user me-2"></i>Data Pasien
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center pb-3 border-bottom mb-3">
                        <div class="bg-light rounded-circle d-inline-flex p-3 text-primary fs-2 mb-2">
                            <i class="fa-solid fa-user"></i>
                        </div>
                        <h5 class="fw-bold mb-0 text-dark">{{ $visit->patient->nama }}</h5>
                        <div class="text-primary fw-semibold">{{ $visit->patient->no_rm }}</div>
                        <span class="badge bg-secondary mt-1">{{ $visit->penjamin }}</span>
                    </div>

                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted" style="width: 40%;">Jenis Kelamin:</td>
                            <td class="fw-semibold">{{ $visit->patient->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tanggal Lahir:</td>
                            <td class="fw-semibold">{{ $visit->patient->tanggal_lahir ? $visit->patient->tanggal_lahir->format('d/m/Y') : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">No. Handphone:</td>
                            <td class="fw-semibold">{{ $visit->patient->no_hp ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Alamat:</td>
                            <td class="fw-semibold">{{ $visit->patient->alamat ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Riwayat Alergi:</td>
                            <td>
                                @if ($visit->patient->alergi)
                                    <span class="badge bg-danger">{{ $visit->patient->alergi }}</span>
                                @else
                                    <span class="text-muted">Tidak Ada</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Polyclinic & Status Card -->
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="card-title mb-0 fw-bold">
                        <i class="fa-solid fa-clipboard-user text-primary me-2"></i>Status Kunjungan
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Poliklinik Tujuan:</span>
                        <span class="badge bg-light text-dark border fs-6">{{ $visit->poli }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Dokter:</span>
                        <span class="fw-bold">{{ $visit->doctor ? $visit->doctor->nama : 'Belum Ditentukan' }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted">Status Pelayanan:</span>
                        @if ($visit->status === 'Menunggu')
                            <span class="badge bg-warning text-dark">Menunggu</span>
                        @elseif ($visit->status === 'Sedang Diperiksa')
                            <span class="badge bg-info text-white">Sedang Diperiksa</span>
                        @elseif ($visit->status === 'Selesai')
                            <span class="badge bg-success">Pelayanan Selesai</span>
                        @else
                            <span class="badge bg-danger">Batal</span>
                        @endif
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Status Kasir:</span>
                        @if ($visit->status_pembayaran === 'Lunas')
                            <span class="badge bg-success"><i class="fa-solid fa-check-circle me-1"></i> Lunas</span>
                        @else
                            <span class="badge bg-danger">Belum Lunas</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Examination Details -->
        <div class="col-lg-8">
            <!-- Vital Signs -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="card-title mb-0 fw-bold text-dark">
                        <i class="fa-solid fa-heart-pulse text-danger me-2"></i>Tanda-Tanda Vital (TTV)
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3 text-center">
                        <div class="col-md-4">
                            <div class="border rounded-3 p-3 bg-light">
                                <div class="text-muted small">Tekanan Darah</div>
                                <h4 class="fw-bold text-primary mb-0">{{ $visit->tensi_darah ?? '-' }}</h4>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded-3 p-3 bg-light">
                                <div class="text-muted small">Suhu Tubuh</div>
                                <h4 class="fw-bold text-danger mb-0">{{ $visit->suhu_tubuh ?? '-' }}</h4>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded-3 p-3 bg-light">
                                <div class="text-muted small">Berat Badan</div>
                                <h4 class="fw-bold text-success mb-0">{{ $visit->berat_badan ?? '-' }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Medical Diagnosis & Notes -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="card-title mb-0 fw-bold text-dark">
                        <i class="fa-solid fa-stethoscope text-primary me-2"></i>Hasil Pemeriksaan & Diagnosa Dokter
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small fw-bold">Keluhan Pasien:</label>
                        <div class="p-3 bg-light rounded-3">{{ $visit->keluhan ?: 'Tidak ada catatan keluhan.' }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small fw-bold">Diagnosa Medis:</label>
                        <div class="p-3 bg-light rounded-3 fw-semibold text-dark">
                            {{ $visit->diagnosa ?: 'Belum diisi. Silakan klik "Periksa / Input Medis" di atas.' }}
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="text-muted small fw-bold">Catatan Dokter / Resep / Saran Terapi:</label>
                        <div class="p-3 bg-light rounded-3">{{ $visit->catatan_dokter ?: 'Belum ada instruksi tambahan.' }}</div>
                    </div>
                </div>
            </div>

            <!-- Treatments Given -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0 fw-bold text-dark">
                        <i class="fa-solid fa-syringe text-warning me-2"></i>Tindakan Medis yang Diberikan
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Nama Tindakan</th>
                                    <th>Keterangan</th>
                                    <th class="text-end pe-3">Biaya Tindakan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($visit->visitTreatments as $vt)
                                    <tr>
                                        <td class="ps-3 fw-semibold">{{ $vt->treatment ? $vt->treatment->nama : '-' }}</td>
                                        <td><span class="badge bg-light text-secondary border">{{ $vt->catatan ?? '-' }}</span></td>
                                        <td class="text-end pe-3 fw-bold">Rp {{ number_format($vt->biaya, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-3 text-muted">Belum ada tindakan medis tambahan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Billing Summary -->
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="card-title mb-0 fw-bold text-dark">
                        <i class="fa-solid fa-file-invoice-dollar text-success me-2"></i>Rincian Biaya & Pembayaran
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Biaya Konsultasi Dokter:</span>
                                <span class="fw-semibold">Rp {{ number_format($visit->biaya_konsultasi, 0, ',', '.') }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Biaya Tindakan Medis:</span>
                                <span class="fw-semibold">Rp {{ number_format($visit->biaya_tindakan, 0, ',', '.') }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Biaya Obat / Resep:</span>
                                <span class="fw-semibold">Rp {{ number_format($visit->biaya_obat, 0, ',', '.') }}</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between fs-5 fw-bold text-primary">
                                <span>Total Tagihan:</span>
                                <span>Rp {{ number_format($visit->total_biaya, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <div class="col-md-6 border-start ps-md-4">
                            <div class="mb-3">
                                <label class="text-muted small">Metode Pembayaran:</label>
                                <div class="fw-bold fs-6">{{ $visit->metode_pembayaran ?: 'Belum Dipilih' }}</div>
                            </div>
                            <div>
                                <label class="text-muted small">Status Pembayaran:</label>
                                <div>
                                    @if ($visit->status_pembayaran === 'Lunas')
                                        <span class="badge bg-success fs-6 px-3 py-2"><i class="fa-solid fa-check-circle me-1"></i> LUNAS</span>
                                    @else
                                        <span class="badge bg-danger fs-6 px-3 py-2"><i class="fa-solid fa-circle-exclamation me-1"></i> BELUM LUNAS</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
