@extends('layouts.public')

@section('content')
<div class="registration-overview">
    <div><span class="eyebrow">Pendaftaran kunjungan · Poli Gigi</span><h1>Mulai perawatan senyum Anda.</h1><p>Lengkapi data pasien dan keluhan gigi. Pendaftaran berlaku untuk kunjungan hari ini, tanpa membuat akun.</p></div>
    <div class="registration-date"><span>Hari kunjungan</span><strong>{{ now('Asia/Jakarta')->translatedFormat('d F Y') }}</strong><small>Waktu Indonesia Barat</small></div>
</div>
<ol class="registration-stages" aria-label="Langkah pendaftaran">
    <li><span>01</span><div><strong>Isi data diri</strong><small>Identitas dan kontak pasien</small></div></li>
    <li><span>02</span><div><strong>Sampaikan keluhan gigi</strong><small>Keluhan dan informasi penjamin</small></div></li>
    <li><span>03</span><div><strong>Simpan bukti pendaftaran</strong><small>Tunjukkan saat datang ke klinik</small></div></li>
</ol>
<div class="registration-grid registration-redesign">
    <div>
@if ($errors->any())
    <div class="alert alert-danger" role="alert">
        <strong>Pendaftaran belum tersimpan. Periksa kembali isian berikut:</strong>
        <ul class="mb-0 mt-2">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif
<form action="{{ route('registration.store') }}" method="POST" class="registration-card registration-form">
    @csrf
    <input type="hidden" name="registration_token" value="{{ session('registration_token') }}">
    <div class="form-intro visually-hidden"><div><strong>Formulir pendaftaran</strong><p>Isian bertanda * wajib diisi.</p></div><span class="today-pill">{{ now('Asia/Jakarta')->format('d M Y') }} · WIB</span></div>
    <div class="form-body">
    <div class="section-heading"><span class="section-icon">01</span><div><h2>Data pribadi pasien</h2><p>Lengkapi identitas dan informasi kesehatan Anda.</p></div></div>
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
    <input type="hidden" name="poli" value="Poli Gigi">
    </div>
    <div class="form-body registration-visit-section">
    <div class="section-heading"><span class="section-icon">02</span><div><h2>Informasi kunjungan Poli Gigi</h2><p>Pemeriksaan khusus kesehatan gigi dan mulut.</p></div></div>
    <div class="row g-3">
        @foreach (['penjamin' => ['Jenis pembayaran / penjamin', ['Umum', 'BPJS', 'Asuransi Swasta']]] as $field => [$label, $options])
            <div class="col-md-6">
                <label for="{{ $field }}" class="form-label">{{ $label }} *</label>
                <select id="{{ $field }}" name="{{ $field }}" class="form-select" required>
                    <option value="">Pilih {{ strtolower($label) }}</option>
                    @foreach ($options as $option)<option value="{{ $option }}" @selected(old($field) === $option)>{{ $option }}</option>@endforeach
                </select>
            </div>
        @endforeach
        <div class="col-12">
            <label for="no_penjamin" class="form-label">Nomor penjamin</label>
            <input id="no_penjamin" name="no_penjamin" class="form-control" maxlength="50" value="{{ old('no_penjamin') }}" aria-describedby="penjamin-help">
            <div id="penjamin-help" class="form-text">Nomor penjamin wajib diisi untuk BPJS dan asuransi swasta.</div>
        </div>
        <div class="col-12">
            <label for="keluhan" class="form-label">Keluhan utama *</label>
            <textarea id="keluhan" name="keluhan" class="form-control" rows="3" maxlength="2000" required placeholder="Ceritakan keluhan yang ingin diperiksakan">{{ old('keluhan') }}</textarea>
        </div>
    </div>
    <div class="registration-notice">Pendaftaran berlaku untuk hari ini (WIB). Dokter, jadwal pelayanan, dan biaya akhir dikonfirmasi petugas saat kedatangan.</div>
    <button class="btn btn-primary btn-lg w-100" type="submit">Daftar kunjungan <span aria-hidden="true" class="ms-2">→</span></button>
    <p class="submit-note">Periksa kembali data Anda sebelum mengirim pendaftaran.</p>
    </div>
</form>
    </div>
    <aside class="registration-help" aria-label="Informasi pendaftaran">
        <section class="registration-help-card"><span class="registration-help-icon" aria-hidden="true">✓</span><h2>Siapkan sebelum mendaftar</h2><ul><li>Data identitas pasien yang akan diperiksa.</li><li>Nomor HP yang dapat dihubungi.</li><li>Nomor penjamin jika menggunakan BPJS atau asuransi.</li></ul></section>
        <section class="registration-help-card"><h2>Sudah pernah berobat?</h2><p>Hubungi petugas untuk mendaftarkan kunjungan menggunakan nomor rekam medis yang sudah ada.</p><p class="mb-0">Dengan begitu, riwayat pemeriksaan gigi Anda tetap terhubung.</p></section>
        <section class="registration-help-card"><h2>Setelah pendaftaran</h2><p>Simpan bukti yang muncul setelah formulir berhasil dikirim, lalu tunjukkan kepada petugas saat tiba di klinik.</p><div class="registration-help-note">Dokter, jadwal pelayanan, dan biaya tindakan dikonfirmasi oleh petugas saat kedatangan.</div></section>
        <div class="registration-support"><strong>Butuh bantuan?</strong><p class="mb-0">Jika mengalami kendala pengisian, hubungi petugas klinik.</p></div>
    </aside>
</div>
@endsection
