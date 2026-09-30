@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Pendaftaran Pasien Baru</h2>

    <form action="{{ route('patients.store') }}" method="POST">
        @csrf

        <div class="row">
            <div class="col-md-6 mb-3">
                <label>Nama Lengkap <span class="text-danger">*</span></label>
                <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama') }}" required>
                @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label>NIK</label>
                <input type="text" name="nik" class="form-control @error('nik') is-invalid @enderror" value="{{ old('nik') }}">
                @error('nik') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4 mb-3">
                <label>Jenis Kelamin <span class="text-danger">*</span></label>
                <select name="jenis_kelamin" class="form-control" required>
                    <option value="">-- Pilih --</option>
                    <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                </select>
            </div>

            <div class="col-md-4 mb-3">
                <label>Tanggal Lahir</label>
                <input type="date" name="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir') }}">
            </div>

            <div class="col-md-4 mb-3">
                <label>Tempat Lahir</label>
                <input type="text" name="tempat_lahir" class="form-control" value="{{ old('tempat_lahir') }}">
            </div>

            <div class="col-md-12 mb-3">
                <label>Alamat</label>
                <textarea name="alamat" class="form-control" rows="2">{{ old('alamat') }}</textarea>
            </div>

            <div class="col-md-4 mb-3">
                <label>No. HP</label>
                <input type="text" name="no_hp" class="form-control" value="{{ old('no_hp') }}">
            </div>

            <div class="col-md-4 mb-3">
                <label>Pekerjaan</label>
                <input type="text" name="pekerjaan" class="form-control" value="{{ old('pekerjaan') }}">
            </div>

            <div class="col-md-4 mb-3">
                <label>Status Pernikahan</label>
                <select name="status_pernikahan" class="form-control">
                    <option value="">-- Pilih --</option>
                    <option value="Belum Menikah">Belum Menikah</option>
                    <option value="Menikah">Menikah</option>
                    <option value="Cerai">Cerai</option>
                </select>
            </div>

            <div class="col-md-4 mb-3">
                <label>Golongan Darah</label>
                <select name="golongan_darah" class="form-control">
                    <option value="">-- Pilih --</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="AB">AB</option>
                    <option value="O">O</option>
                </select>
            </div>

            <div class="col-md-4 mb-3">
                <label>Agama</label>
                <input type="text" name="agama" class="form-control" value="{{ old('agama') }}">
            </div>

            <div class="col-md-4 mb-3">
                <label>Penjamin</label>
                <select name="penjamin" class="form-control">
                    <option value="">-- Pilih --</option>
                    <option value="Umum">Umum</option>
                    <option value="BPJS">BPJS</option>
                    <option value="Asuransi">Asuransi</option>
                </select>
            </div>

            <div class="col-md-6 mb-3">
                <label>No. Penjamin / No. Kartu</label>
                <input type="text" name="no_penjamin" class="form-control" value="{{ old('no_penjamin') }}">
            </div>

            <div class="col-md-12 mb-3">
                <label>Alergi</label>
                <textarea name="alergi" class="form-control" rows="2">{{ old('alergi') }}</textarea>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Simpan Pasien</button>
        <a href="{{ route('patients.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection