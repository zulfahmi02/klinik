@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1"><i class="fa-solid fa-calendar-plus text-primary me-2"></i>Pendaftaran Kunjungan Baru</h3>
            <p class="text-muted mb-0">Daftarkan pasien untuk pemeriksaan gigi</p>
        </div>
        <a href="{{ route('visits.index') }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Kunjungan
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
                        <i class="fa-solid fa-id-card-clip text-primary me-2"></i>Pendaftaran Kunjungan Poli Gigi
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('visits.store') }}">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="patient_id" class="form-label fw-semibold">Pilih Pasien Terdaftar <span class="text-danger">*</span></label>
                                <input type="search" class="form-control mb-2" id="patient-search" placeholder="Cari nama atau nomor RM" aria-label="Cari pasien terdaftar">
                                <select class="form-select @error('patient_id') is-invalid @enderror" id="patient_id" name="patient_id" required>
                                    <option value="">-- Pilih Pasien (No. RM / Nama) --</option>
                                    @foreach ($patients as $p)
                                        <option value="{{ $p->id }}" {{ old('patient_id') == $p->id ? 'selected' : '' }}>
                                            {{ $p->no_rm }} - {{ $p->nama }} ({{ $p->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}) - {{ $p->penjamin ?? 'Umum' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Poliklinik & Dokter -->
                        <div class="row mb-3">
                            <input type="hidden" name="poli" value="Poli Gigi">
                            <div class="col-md-6">
                                <label for="doctor_id" class="form-label fw-semibold">Dokter Pemeriksa</label>
                                <select class="form-select" id="doctor_id" name="doctor_id">
                                    <option value="">-- Pilih Dokter (Bisa Menyusul) --</option>
                                    @foreach ($doctors as $doc)
                                        <option value="{{ $doc->id }}" {{ old('doctor_id') == $doc->id ? 'selected' : '' }}>
                                            {{ $doc->nama }} ({{ $doc->spesialisasi }})
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
                            <textarea class="form-control" id="keluhan" name="keluhan" rows="3" placeholder="Ceritakan keluhan gigi atau mulut pasien...">{{ old('keluhan') }}</textarea>
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
@push('scripts')
<script>document.getElementById('patient-search').addEventListener('input', function () { const query = this.value.toLocaleLowerCase('id'); document.querySelectorAll('#patient_id option').forEach(option => { option.hidden = !!option.value && !option.textContent.toLocaleLowerCase('id').includes(query); }); });</script>
@endpush
