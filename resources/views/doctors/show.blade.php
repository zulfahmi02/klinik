@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4">Detail Dokter</h1>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    <div class="card p-4 mb-3">
        <h2 class="h4">{{ $doctor->nama }}</h2>
        <dl class="row mb-0">
            @foreach(['spesialisasi' => 'Spesialisasi', 'no_sip' => 'Nomor SIP', 'no_hp' => 'Nomor HP', 'status' => 'Status'] as $field => $label)
                <dt class="col-sm-4">{{ $label }}</dt><dd class="col-sm-8">{{ $doctor->$field ?: '-' }}</dd>
            @endforeach
            <dt class="col-sm-4">Tarif konsultasi</dt><dd class="col-sm-8">Rp {{ number_format($doctor->tarif_konsultasi, 0, ',', '.') }}</dd>
            <dt class="col-sm-4">Total kunjungan</dt><dd class="col-sm-8">{{ $doctor->visits_count }}</dd>
        </dl>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('doctors.index') }}" class="btn btn-outline-secondary">Kembali ke Data Dokter</a>
        <form method="POST" action="{{ route('doctors.destroy', $doctor) }}" onsubmit="return confirm('Hapus dokter ini? Riwayat kunjungan akan kehilangan referensi dokter.');">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-outline-danger">Hapus Dokter</button>
        </form>
    </div>
</div>
@endsection
