@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1"><i class="fa-solid fa-tooth text-primary me-2"></i>Odontogram Pemeriksaan Gigi</h3>
            <p class="text-muted mb-0">Pilih pasien untuk melihat dan mengisi rekam odontogram gigi secara visual</p>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('odontogram.index') }}" class="row g-2">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="fa-solid fa-search text-muted"></i></span>
                        <input type="text" class="form-control" name="search" value="{{ $search }}" placeholder="Cari pasien berdasarkan nama, No. RM, atau No. HP...">
                        <button type="submit" class="btn btn-primary px-4">Cari Pasien</button>
                    </div>
                </div>
                @if ($search)
                    <div class="col-md-2">
                        <a href="{{ route('odontogram.index') }}" class="btn btn-outline-secondary">Reset</a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Patient Cards Grid -->
    <div class="row g-3">
        @forelse ($patients as $patient)
            <div class="col-md-4 col-lg-3">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle me-3 fs-4">
                                <i class="fa-solid fa-tooth"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0 text-dark">{{ $patient->nama }}</h6>
                                <small class="text-primary fw-semibold">{{ $patient->no_rm }}</small>
                            </div>
                        </div>

                        <div class="small text-muted mb-3 flex-grow-1">
                            <div><i class="fa-solid fa-venus-mars me-1"></i> {{ $patient->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</div>
                            <div><i class="fa-solid fa-phone me-1"></i> {{ $patient->no_hp ?? '-' }}</div>
                            <div class="mt-2">
                                <span class="badge bg-light text-dark border">
                                    <i class="fa-solid fa-notes-medical text-primary me-1"></i>
                                    <strong>{{ $patient->odontograms_count }}</strong> data gigi tercatat
                                </span>
                            </div>
                        </div>

                        <a href="{{ route('odontogram.show', $patient) }}" class="btn btn-outline-primary btn-sm w-100 fw-semibold">
                            <i class="fa-solid fa-chart-simple me-1"></i> Buka Peta Odontogram
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-3 text-center py-5 text-muted">
                    <i class="fa-solid fa-tooth fs-1 text-secondary mb-3"></i>
                    <h5>Tidak ada pasien ditemukan.</h5>
                    <p class="small mb-0">Silakan daftarkan pasien baru terlebih dahulu melalui menu Pendaftaran Pasien.</p>
                </div>
            </div>
        @endforelse
    </div>

    @if ($patients->hasPages())
        <div class="mt-4">
            {{ $patients->links() }}
        </div>
    @endif
</div>
@endsection
