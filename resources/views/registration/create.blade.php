@extends('layouts.public')

@section('content')
<div class="registration-hero">
    <div class="eyebrow">Pendaftaran pasien baru</div>
    <h1>Langkah pertama untuk<br><span>merasa lebih baik.</span></h1>
    <p class="mb-0">Daftarkan kunjungan Anda tanpa perlu membuat akun. Isi data, pilih poli, dan simpan nomor antrean untuk dibawa ke klinik.</p>
</div>
<div class="registration-grid">
    <aside class="registration-guide" aria-label="Panduan pendaftaran">
        <div class="guide-panel">
            <span class="guide-caption">Sebelum ke klinik</span>
            <h2>Tiga langkah.<br>Siap untuk berobat.</h2>
            <ol class="guide-steps">
                <li><span class="step-number">1</span><div><strong>Lengkapi data pasien</strong><p>Gunakan data sesuai identitas agar pendaftaran lebih lancar.</p></div></li>
                <li><span class="step-number">2</span><div><strong>Pilih tujuan kunjungan</strong><p>Tentukan poli dan ceritakan keluhan yang ingin diperiksakan.</p></div></li>
                <li><span class="step-number">3</span><div><strong>Simpan nomor antrean</strong><p>Tunjukkan bukti pendaftaran kepada petugas saat tiba di klinik.</p></div></li>
            </ol>
            <div class="guide-tip">Siapkan identitas dan kartu BPJS atau asuransi jika Anda menggunakannya.</div>
        </div>
        <div class="returning-patient"><h3>Sudah pernah berobat?</h3><p>Hubungi petugas untuk mendaftarkan kunjungan dengan nomor rekam medis yang sudah ada.</p></div>
    </aside>
    <div>
@if ($errors->any())
    <div class="alert alert-danger" role="alert">
        <strong>Pendaftaran belum tersimpan. Periksa kembali isian berikut:</strong>
        <ul class="mb-0 mt-2">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif
<form action="{{ route('registration.store') }}" method="POST" class="card registration-card">
    @csrf
    <input type="hidden" name="registration_token" value="{{ session('registration_token') }}">
    <div class="form-intro"><div><strong>Formulir pendaftaran</strong><p>Isian bertanda * wajib diisi.</p></div><span class="today-pill">{{ now('Asia/Jakarta')->format('d M Y') }} · WIB</span></div>
    <div class="form-body">
    <div class="section-heading"><span class="section-icon">01</span><div><h2>Kenali pasien lebih dekat</h2><p>Lengkapi identitas dan informasi kesehatan Anda.</p></div></div>
    <div class="row g-3">
        @foreach (['nama' => ['Nama lengkap', 'text'], 'nik' => ['NIK (16 digit, opsional)', 'text'], 'tanggal_lahir' => ['Tanggal lahir', 'date'], 'no_hp' => ['Nomor HP', 'tel']] as $field => [$label, $type])
            <div class="col-md-6">
                <label class="form-label" for="{{ $field }}">{{ $label }} {{ $field !== 'nik' ? '*' : '' }}</label>
                <input class="form-control @error($field) is-invalid @enderror" id="{{ $field }}" name="{{ $field }}" type="{{ $type }}" value="{{ old($field) }}" @required($field !== 'nik') @if($field === 'tanggal_lahir') max="{{ now('Asia/Jakarta')->toDateString() }}" @elseif($field === 'nik') inputmode="numeric" pattern="[0-9]{16}" maxlength="16" @elseif($field === 'nama') maxlength="255" autocomplete="name" @else maxlength="20" autocomplete="tel" @endif>
                @error($field)<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        @endforeach
        <div class="col-md-6">
            <label for="jenis_kelamin" class="form-label">Jenis kelamin *</label>
            <select id="jenis_kelamin" name="jenis_kelamin" class="form-select" required>
                <option value="">Pilih jenis kelamin</option>
                <option value="L" @selected(old('jenis_kelamin') === 'L')>Laki-laki</option>
                <option value="P" @selected(old('jenis_kelamin') === 'P')>Perempuan</option>
            </select>
        </div>
        <div class="col-12">
            <label for="alamat" class="form-label">Alamat *</label>
            <textarea id="alamat" name="alamat" class="form-control" rows="2" maxlength="2000" required>{{ old('alamat') }}</textarea>
        </div>
        <div class="col-12">
            <label for="alergi" class="form-label">Alergi (opsional)</label>
            <textarea id="alergi" name="alergi" class="form-control" rows="2" maxlength="2000" placeholder="Tuliskan alergi obat atau makanan jika ada">{{ old('alergi') }}</textarea>
        </div>
    </div>
    <hr class="section-divider">
    <div class="section-heading"><span class="section-icon">02</span><div><h2>Rencanakan kunjungan Anda</h2><p>Pilih layanan yang sesuai dengan kebutuhan Anda.</p></div></div>
    <div class="row g-3">
        @foreach (['poli' => ['Poli tujuan', ['Poli Umum', 'Poli Gigi', 'KIA / KB', 'Poli Anak']], 'penjamin' => ['Jenis pembayaran / penjamin', ['Umum', 'BPJS', 'Asuransi Swasta']]] as $field => [$label, $options])
            <div class="col-md-6">
                <label for="{{ $field }}" class="form-label">{{ $label }} *</label>
                <select id="{{ $field }}" name="{{ $field }}" class="form-select" required>
                    <option value="">Pilih {{ strtolower($label) }}</option>
                    @foreach ($options as $option)<option value="{{ $option }}" @selected(old($field) === $option)>{{ $option }}</option>@endforeach
                </select>
            </div>
        @endforeach
        <div class="col-12">
            <label for="no_penjamin" class="form-label">Nomor kartu penjamin</label>
            <input id="no_penjamin" name="no_penjamin" class="form-control" maxlength="50" value="{{ old('no_penjamin') }}" aria-describedby="penjamin-help">
            <div id="penjamin-help" class="form-text">Wajib untuk BPJS dan asuransi swasta.</div>
        </div>
        <div class="col-12">
            <label for="keluhan" class="form-label">Keluhan utama *</label>
            <textarea id="keluhan" name="keluhan" class="form-control" rows="3" maxlength="2000" required placeholder="Ceritakan keluhan yang ingin diperiksakan">{{ old('keluhan') }}</textarea>
        </div>
    </div>
    <div class="registration-notice">Pendaftaran berlaku untuk hari ini (WIB). Dokter, jadwal pelayanan, dan biaya akhir dikonfirmasi petugas saat kedatangan.</div>
    <button class="btn btn-primary btn-lg w-100" type="submit">Daftar dan ambil nomor antrean <span aria-hidden="true" class="ms-2">→</span></button>
    <p class="submit-note">Periksa kembali data Anda sebelum mengirim pendaftaran.</p>
    </div>
</form>
    </div>
</div>
@endsection
