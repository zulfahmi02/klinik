@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Detail Pasien</h2>
    <div class="card">
        <div class="card-body">
            <table class="table">
                <tr><th>No. RM</th><td>{{ $patient->no_rm }}</td></tr>
                <tr><th>Nama</th><td>{{ $patient->nama }}</td></tr>
                <tr><th>NIK</th><td>{{ $patient->nik ?? '-' }}</td></tr>
                <tr><th>Jenis Kelamin</th><td>{{ $patient->jenis_kelamin }}</td></tr>
                <tr><th>Tanggal Lahir</th><td>{{ $patient->tanggal_lahir?->format('d-m-Y') ?? '-' }}</td></tr>
                <tr><th>Alamat</th><td>{{ $patient->alamat ?? '-' }}</td></tr>
                <tr><th>No. HP</th><td>{{ $patient->no_hp ?? '-' }}</td></tr>
                <tr><th>Penjamin</th><td>{{ $patient->penjamin ?? '-' }}</td></tr>
                <tr><th>Alergi</th><td>{{ $patient->alergi ?? '-' }}</td></tr>
            </table>
            <a href="{{ route('patients.index') }}" class="btn btn-secondary">Kembali</a>
            <a href="{{ route('patients.edit', $patient) }}" class="btn btn-warning">Edit</a>
        </div>
    </div>
</div>
@endsection