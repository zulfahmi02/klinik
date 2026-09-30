@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1"><i class="fa-solid fa-clock-rotate-left text-primary me-2"></i>Riwayat & Rekam Medis Pasien</h3>
            <p class="text-muted mb-0">Pencarian dan penelusuran riwayat kunjungan klinis, diagnosa, dan terapi pasien</p>
        </div>
    </div>

    <!-- Search Box -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('medical_records.index') }}" class="row g-2">
                <div class="col-md-7">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fa-solid fa-search text-muted"></i></span>
                        <input type="text" class="form-control" name="search" value="{{ $search }}" placeholder="Cari berdasarkan Nomor RM, Nama Pasien, NIK, atau No. HP...">
                        <button type="submit" class="btn btn-primary px-4">Cari Rekam Medis</button>
                    </div>
                </div>
                @if ($search)
                    <div class="col-md-2">
                        <a href="{{ route('medical_records.index') }}" class="btn btn-outline-secondary">Reset</a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Patient Table -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">No. Rekam Medis</th>
                            <th>Nama Pasien</th>
                            <th>NIK</th>
                            <th>L/P</th>
                            <th>No. Handphone</th>
                            <th>Penjamin</th>
                            <th>Total Kunjungan</th>
                            <th class="text-end pe-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($patients as $patient)
                            <tr>
                                <td class="ps-3">
                                    <span class="badge bg-primary fs-6">{{ $patient->no_rm }}</span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $patient->nama }}</div>
                                    <small class="text-muted">{{ $patient->alamat ?? '-' }}</small>
                                </td>
                                <td>{{ $patient->nik ?? '-' }}</td>
                                <td>
                                    @if ($patient->jenis_kelamin === 'L')
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle">Laki-laki</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Perempuan</span>
                                    @endif
                                </td>
                                <td>{{ $patient->no_hp ?? '-' }}</td>
                                <td><span class="badge bg-light text-secondary border">{{ $patient->penjamin ?? 'Umum' }}</span></td>
                                <td>
                                    <span class="badge bg-info text-white fs-6 px-2">
                                        <i class="fa-solid fa-notes-medical me-1"></i> {{ $patient->visits_count }}x Kunjungan
                                    </span>
                                </td>
                                <td class="text-end pe-3">
                                    <a href="{{ route('medical_records.show', $patient) }}" class="btn btn-primary btn-sm fw-semibold">
                                        <i class="fa-solid fa-folder-open me-1"></i> Buka Rekam Medis
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-folder-open fs-2 mb-2 d-block text-secondary"></i>
                                    Tidak ada pasien yang cocok dengan pencarian Anda.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($patients->hasPages())
                <div class="p-3 border-top">
                    {{ $patients->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
