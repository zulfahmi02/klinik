@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Daftar Pasien</h2>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="GET" class="card p-3 mb-4"><label for="patient-query" class="form-label">Cari pasien</label><div class="d-flex gap-2"><input id="patient-query" name="search" class="form-control" value="{{ $search }}" placeholder="Nama, nomor RM, NIK, atau nomor HP"><button class="btn btn-primary">Cari</button><a class="btn btn-outline-secondary" href="{{ route('patients.index') }}">Reset</a></div></form>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive"><table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No. RM</th>
                        <th>Nama</th>
                        <th>NIK</th>
                        <th>L/P</th>
                        <th>No. HP</th>
                        <th>Penjamin</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($patients as $patient)
                        <tr>
                            <td>{{ $patient->no_rm }}</td>
                            <td>{{ $patient->nama }}</td>
                            <td>{{ $patient->nik ?? '-' }}</td>
                            <td>{{ $patient->jenis_kelamin }}</td>
                            <td>{{ $patient->no_hp ?? '-' }}</td>
                            <td>{{ $patient->penjamin ?? '-' }}</td>
                            <td>
                                <a href="{{ route('patients.show', $patient) }}" class="btn btn-sm btn-info">Detail</a>
                                <a href="{{ route('patients.edit', $patient) }}" class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ route('patients.destroy', $patient) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">Belum ada data pasien</td>
                        </tr>
                    @endforelse
                </tbody>
            </table></div>

            {{ $patients->links() }}
        </div>
    </div>
</div>
@endsection