@extends('layouts.public')

@section('content')
<div class="card registration-card receipt-card p-4 p-md-5 text-center">
    <div class="success-mark" aria-hidden="true">✓</div>
    <p class="text-success fw-semibold">Pendaftaran berhasil</p>
    <h1 class="h3">Nomor antrean Anda</h1>
    <div class="queue-number">{{ $receipt['no_antrean'] }}</div>
    <p class="fs-5">{{ $receipt['poli'] }} · {{ $receipt['tanggal'] }}</p>
    <p>Nomor rekam medis: <strong>{{ $receipt['no_rm'] }}</strong></p>
    <hr>
    <p class="text-secondary">Simpan bukti ini dan tunjukkan kepada petugas saat tiba di klinik. Bawa identitas serta kartu penjamin jika digunakan. Nomor antrean bukan jaminan jam pemeriksaan.</p>
    <div class="d-flex flex-wrap justify-content-center gap-2 no-print">
        <button class="btn btn-primary" type="button" onclick="window.print()">Cetak bukti pendaftaran</button>
        <a class="btn btn-outline-secondary" href="{{ route('registration.create') }}">Daftarkan pasien lain</a>
    </div>
</div>
@endsection
