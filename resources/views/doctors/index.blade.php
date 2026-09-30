@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Data Dokter</h1>
        <a href="{{ route('doctors.create') }}" class="btn btn-primary">Tambah Dokter</a>
    </div>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    <div class="card"><div class="table-responsive">
        <table class="table table-striped align-middle mb-0">
            <thead><tr><th>Nama</th><th>Spesialisasi</th><th>Nomor SIP</th><th>Status</th><th>Kunjungan</th><th>Aksi</th></tr></thead>
            <tbody>
                @forelse($doctors as $doctor)
                    <tr>
                        <td>{{ $doctor->nama }}</td><td>{{ $doctor->spesialisasi }}</td><td>{{ $doctor->no_sip ?: '-' }}</td>
                        <td>{{ $doctor->status }}</td><td>{{ $doctor->visits_count }}</td>
                        <td><a class="btn btn-sm btn-outline-primary" href="{{ route('doctors.show', $doctor) }}">Detail</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center p-4">Belum ada data dokter.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div></div>
    <div class="mt-3">{{ $doctors->links() }}</div>
</div>
@endsection
