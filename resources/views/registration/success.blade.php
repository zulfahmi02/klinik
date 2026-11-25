@extends('layouts.public')

@section('content')
<div class="card registration-card receipt-card p-4 p-md-5 text-center">
    <div class="success-mark" aria-hidden="true">✓</div>
    <p class="text-success fw-semibold">Pendaftaran berhasil</p>
    <h1 class="h3">Bukti pendaftaran</h1>
    <p class="fs-5">{{ $receipt['poli'] }} · {{ $receipt['tanggal'] }}</p>
    <p>Nomor rekam medis: <strong>{{ $receipt['no_rm'] }}</strong></p>
    @if(isset($receipt['visit_id']))<p>Nomor kunjungan: <strong>KJ-{{ $receipt['visit_id'] }}</strong></p>@endif
    @if(isset($receipt['nama']))<p>Nama pasien: <strong>{{ $receipt['nama'] }}</strong></p>@endif
    @if(isset($receipt['penjamin']))<p>Penjamin: {{ $receipt['penjamin'] }} · Nomor penjamin: {{ $receipt['no_penjamin'] ?? '-' }}</p>@endif
    <hr>
    <p class="text-secondary">Simpan bukti ini dan tunjukkan kepada petugas saat tiba di klinik. Bawa identitas serta nomor penjamin jika digunakan.</p>
    <p class="small text-muted">Bukti ini merupakan bukti pendaftaran, bukan bukti pembayaran.</p>
    <div class="d-flex flex-wrap justify-content-center gap-2 no-print">
        <button class="btn btn-primary" type="button" onclick="window.print()">Cetak / Simpan PDF</button>
        <a class="btn btn-outline-secondary" href="{{ route('registration.create') }}">Daftarkan pasien lain</a>
    </div>
</div>
@endsection
