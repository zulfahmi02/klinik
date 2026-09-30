@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1"><i class="fa-solid fa-calendar-plus text-primary me-2"></i>Pendaftaran Kunjungan Baru</h3>
            <p class="text-muted mb-0">Daftarkan pasien ke dalam antrean poliklinik</p>
        </div>
        <a href="{{ route('visits.index') }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Antrean
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title mb-0 fw-bold text-dark">
                        <i class="fa-solid fa-id-card-clip text-primary me-2"></i>Form Registrasi Kunjungan Poliklinik
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('visits.store') }}">
                        @csrf

                        <!-- Nomor Antrean -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="no_antrean" class="form-label fw-semibold">Nomor Antrean</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fa-solid fa-ticket"></i></span>
                                    <input type="text" class="form-control fw-bold text-primary" id="no_antrean" name="no_antrean" value="{{ old('no_antrean', $nextQueue) }}" required>
                                </div>
                                <small class="text-muted">Nomor urut antrean hari ini.</small>
                            </div>
                            <div class="col-md-8">
                                <label for="patient_id" class="form-label fw-semibold">Pilih Pasien Terdaftar <span class="text-danger">*</span></label>
                                <select class="form-select @error('patient_id') is-invalid @enderror" id="patient_id" name="patient_id" required>
                                    <option value="">-- Pilih Pasien (No. RM / Nama) --</option>
                                    @foreach ($patients as $p)
                                        <option value="{{ $p->id }}" {{ old('patient_id') == $p->id ? 'selected' : '' }}>
                                            {{ $p->no_rm }} - {{ $p->nama }} ({{ $p->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}) - {{ $p->penjamin ?? 'Umum' }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Belum ada pasien? <a href="{{ route('patients.create') }}" target="_blank">Daftar Pasien Baru</a></small>
                            </div>
                        </div>

                        <!-- Poliklinik & Dokter -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="poli" class="form-label fw-semibold">Tujuan Poliklinik <span class="text-danger">*</span></label>
                                <select class="form-select" id="poli" name="poli" required>
                                    <option value="Poli Umum" {{ old('poli') == 'Poli Umum' ? 'selected' : '' }}>Poli Umum</option>
                                    <option value="Poli Gigi" {{ old('poli') == 'Poli Gigi' ? 'selected' : '' }}>Poli Gigi</option>
                                    <option value="KIA / KB" {{ old('poli') == 'KIA / KB' ? 'selected' : '' }}>KIA / KB (Kesehatan Ibu & Anak)</option>
                                    <option value="Poli Anak" {{ old('poli') == 'Poli Anak' ? 'selected' : '' }}>Poli Spesialis Anak</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="doctor_id" class="form-label fw-semibold">Dokter Pemeriksa</label>
                                <select class="form-select" id="doctor_id" name="doctor_id">
                                    <option value="">-- Pilih Dokter (Bisa Menyusul) --</option>
                                    @foreach ($doctors as $doc)
                                        <option value="{{ $doc->id }}" {{ old('doctor_id') == $doc->id ? 'selected' : '' }}>
                                            {{ $doc->nama }} ({{ $doc->spesialisasi }}) - Rp {{ number_format($doc->tarif_konsultasi, 0, ',', '.') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Penjamin / Pembayaran -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="penjamin" class="form-label fw-semibold">Jenis Penjamin <span class="text-danger">*</span></label>
                                <select class="form-select" id="penjamin" name="penjamin" required>
                                    <option value="Umum" {{ old('penjamin') == 'Umum' ? 'selected' : '' }}>Pasien Umum / Mandiri</option>
                                    <option value="BPJS" {{ old('penjamin') == 'BPJS' ? 'selected' : '' }}>BPJS Kesehatan</option>
                                    <option value="Asuransi Swasta" {{ old('penjamin') == 'Asuransi Swasta' ? 'selected' : '' }}>Asuransi Swasta / Perusahaan</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Waktu Pendaftaran</label>
                                <input type="text" class="form-control bg-light" value="{{ now()->format('d F Y, H:i') }} WIB" disabled>
                            </div>
                        </div>

                        <!-- Keluhan Utama Pasien -->
                        <div class="mb-4">
                            <label for="keluhan" class="form-label fw-semibold">Keluhan Utama / Alasan Kunjungan</label>
                            <textarea class="form-control" id="keluhan" name="keluhan" rows="3" placeholder="Contoh: Demam, pusing, batuk sejak 2 hari yang lalu...">{{ old('keluhan') }}</textarea>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('visits.index') }}" class="btn btn-light px-4">Batal</a>
                            <button type="submit" class="btn btn-primary px-4 fw-semibold">
                                <i class="fa-solid fa-check me-1"></i> Simpan Pendaftaran
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
