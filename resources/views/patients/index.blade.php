@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Daftar Pasien</h2>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-striped">
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
            </table>

            {{ $patients->links() }}
        </div>
    </div>
</div>
@endsection