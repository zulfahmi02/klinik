@extends('layouts.app')
@section('content')
<div class="container-fluid"><h1 class="h3 mb-4">Tambah Dokter</h1>
    <div class="card border-0 shadow-sm rounded-3 mb-4 d-print-none">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold"><i class="fa-solid fa-user-plus text-primary me-2"></i>Tambah Dokter</h5>
        </div>
        <div class="card-body p-4">
            <form method="POST" action="{{ route('doctors.store') }}" class="row g-3">
                @csrf
                <div class="col-md-6">
                    <label for="nama" class="form-label">Nama dokter</label>
                    <input id="nama" name="nama" value="{{ old('nama') }}" class="form-control @error('nama') is-invalid @enderror" required maxlength="255">
                    @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="spesialisasi" class="form-label">Spesialisasi</label>
                    <input id="spesialisasi" name="spesialisasi" value="{{ old('spesialisasi', 'Dokter Umum') }}" class="form-control @error('spesialisasi') is-invalid @enderror" required maxlength="255">
                    @error('spesialisasi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label for="no_sip" class="form-label">Nomor SIP</label>
                    <input id="no_sip" name="no_sip" value="{{ old('no_sip') }}" class="form-control @error('no_sip') is-invalid @enderror" maxlength="255">
                    @error('no_sip')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label for="no_hp" class="form-label">Nomor HP</label>
                    <input id="no_hp" name="no_hp" value="{{ old('no_hp') }}" class="form-control @error('no_hp') is-invalid @enderror" maxlength="255">
                    @error('no_hp')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-2">
                    <label for="tarif_konsultasi" class="form-label">Tarif konsultasi (Rp)</label>
                    <input id="tarif_konsultasi" type="number" name="tarif_konsultasi" value="{{ old('tarif_konsultasi', 50000) }}" class="form-control @error('tarif_konsultasi') is-invalid @enderror" min="0" required>
                    @error('tarif_konsultasi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label">Status</label>
                    <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                        <option value="Aktif" @selected(old('status', 'Aktif') === 'Aktif')>Aktif</option>
                        <option value="Nonaktif" @selected(old('status') === 'Nonaktif')>Nonaktif</option>
                    </select>
                    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12"><button type="submit" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i>Tambah Dokter</button></div>
            </form>
        </div>
    </div>

<a href="{{ route('doctors.index') }}" class="btn btn-outline-secondary">Kembali ke Data Dokter</a></div>
@endsection
