@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1"><i class="fa-solid fa-tooth text-primary me-2"></i>Peta Odontogram Gigi</h3>
            <p class="text-muted mb-0">Pasien: <strong>{{ $patient->nama }}</strong> &bull; No. RM: <span class="badge bg-primary">{{ $patient->no_rm }}</span> &bull; Penjamin: {{ $patient->penjamin ?? 'Umum' }}</p>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-outline-secondary">
                <i class="fa-solid fa-print me-1"></i> Cetak Odontogram
            </button>
            <a href="{{ route('odontogram.index') }}" class="btn btn-outline-primary">
                <i class="fa-solid fa-arrow-left me-1"></i> Pilih Pasien Lain
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Interactive FDI Odontogram Chart -->
        <div class="col-lg-8 odontogram-chart">
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold text-dark">
                        <i class="fa-solid fa-teeth-open text-primary me-2"></i>Diagram Odontogram Standar FDI (32 Gigi)
                    </h5>
                    <small class="text-muted"><i class="fa-solid fa-hand-pointer me-1"></i> Klik nomor gigi untuk mengubah kondisi</small>
                </div>
                <div class="card-body p-4">
                    <!-- Legend -->
                    <div class="d-flex flex-wrap gap-2 mb-4 pb-3 border-bottom" style="font-size: 12px;">
                        @foreach ($conditions as $cKey => $cVal)
                            <div class="d-flex align-items-center me-2">
                                <span class="d-inline-block rounded-circle me-1" style="width: 12px; height: 12px; background-color: {{ $cVal['color'] }};"></span>
                                <span>{{ $cVal['label'] }}</span>
                            </div>
                        @endforeach
                    </div>

                    <!-- Rahang Atas (Upper Jaw) -->
                    <div class="text-center mb-2">
                        <span class="badge bg-light text-secondary px-3 py-1 border">RAHANG ATAS (MAXILLA)</span>
                    </div>

                    <div class="d-flex justify-content-center align-items-center gap-3 mb-4 overflow-auto py-2">
                        <!-- Kuadran 1 (Kanan Atas / 18 - 11) -->
                        <div class="d-flex gap-1">
                            @foreach ($upperRight as $tooth)
                                @php
                                    $record = $odontograms->get($tooth);
                                    $cond = $record ? $record->kondisi : 'Sehat';
                                    $color = $conditions[$cond]['color'] ?? '#10b981';
                                @endphp
                                <div class="text-center">
                                    <button 
                                        type="button" 
                                        class="btn p-0 tooth-btn" 
                                        onclick="selectTooth({{ $tooth }}, '{{ $cond }}', '{{ $record ? addslashes($record->tindakan) : '' }}', '{{ $record ? addslashes($record->catatan) : '' }}')"
                                        title="Gigi {{ $tooth }} ({{ $cond }})"
                                    >
                                        <div class="tooth-box shadow-sm" style="border-top: 5px solid {{ $color }};">
                                            <i class="fa-solid fa-tooth fs-5" style="color: {{ $color }};"></i>
                                            <div class="fw-bold" style="font-size: 11px;">{{ $tooth }}</div>
                                        </div>
                                    </button>
                                </div>
                            @endforeach
                        </div>

                        <!-- Garis Tengah Medial -->
                        <div class="border-start border-2 border-primary h-100 mx-1" style="min-height: 55px;" title="Garis Median"></div>

                        <!-- Kuadran 2 (Kiri Atas / 21 - 28) -->
                        <div class="d-flex gap-1">
                            @foreach ($upperLeft as $tooth)
                                @php
                                    $record = $odontograms->get($tooth);
                                    $cond = $record ? $record->kondisi : 'Sehat';
                                    $color = $conditions[$cond]['color'] ?? '#10b981';
                                @endphp
                                <div class="text-center">
                                    <button 
                                        type="button" 
                                        class="btn p-0 tooth-btn" 
                                        onclick="selectTooth({{ $tooth }}, '{{ $cond }}', '{{ $record ? addslashes($record->tindakan) : '' }}', '{{ $record ? addslashes($record->catatan) : '' }}')"
                                        title="Gigi {{ $tooth }} ({{ $cond }})"
                                    >
                                        <div class="tooth-box shadow-sm" style="border-top: 5px solid {{ $color }};">
                                            <i class="fa-solid fa-tooth fs-5" style="color: {{ $color }};"></i>
                                            <div class="fw-bold" style="font-size: 11px;">{{ $tooth }}</div>
                                        </div>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="border-bottom my-3"></div>

                    <!-- Rahang Bawah (Lower Jaw) -->
                    <div class="d-flex justify-content-center align-items-center gap-3 mb-2 overflow-auto py-2">
                        <!-- Kuadran 4 (Kanan Bawah / 48 - 41) -->
                        <div class="d-flex gap-1">
                            @foreach ($lowerRight as $tooth)
                                @php
                                    $record = $odontograms->get($tooth);
                                    $cond = $record ? $record->kondisi : 'Sehat';
                                    $color = $conditions[$cond]['color'] ?? '#10b981';
                                @endphp
                                <div class="text-center">
                                    <button 
                                        type="button" 
                                        class="btn p-0 tooth-btn" 
                                        onclick="selectTooth({{ $tooth }}, '{{ $cond }}', '{{ $record ? addslashes($record->tindakan) : '' }}', '{{ $record ? addslashes($record->catatan) : '' }}')"
                                        title="Gigi {{ $tooth }} ({{ $cond }})"
                                    >
                                        <div class="tooth-box shadow-sm" style="border-bottom: 5px solid {{ $color }};">
                                            <div class="fw-bold" style="font-size: 11px;">{{ $tooth }}</div>
                                            <i class="fa-solid fa-tooth fs-5" style="color: {{ $color }};"></i>
                                        </div>
                                    </button>
                                </div>
                            @endforeach
                        </div>

                        <!-- Garis Tengah Medial -->
                        <div class="border-start border-2 border-primary h-100 mx-1" style="min-height: 55px;" title="Garis Median"></div>

                        <!-- Kuadran 3 (Kiri Bawah / 31 - 38) -->
                        <div class="d-flex gap-1">
                            @foreach ($lowerLeft as $tooth)
                                @php
                                    $record = $odontograms->get($tooth);
                                    $cond = $record ? $record->kondisi : 'Sehat';
                                    $color = $conditions[$cond]['color'] ?? '#10b981';
                                @endphp
                                <div class="text-center">
                                    <button 
                                        type="button" 
                                        class="btn p-0 tooth-btn" 
                                        onclick="selectTooth({{ $tooth }}, '{{ $cond }}', '{{ $record ? addslashes($record->tindakan) : '' }}', '{{ $record ? addslashes($record->catatan) : '' }}')"
                                        title="Gigi {{ $tooth }} ({{ $cond }})"
                                    >
                                        <div class="tooth-box shadow-sm" style="border-bottom: 5px solid {{ $color }};">
                                            <div class="fw-bold" style="font-size: 11px;">{{ $tooth }}</div>
                                            <i class="fa-solid fa-tooth fs-5" style="color: {{ $color }};"></i>
                                        </div>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="text-center mt-2">
                        <span class="badge bg-light text-secondary px-3 py-1 border">RAHANG BAWAH (MANDIBULA)</span>
                    </div>
                </div>
            </div>

            <!-- Table of Diagnosed Teeth -->
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="card-title mb-0 fw-bold text-dark">
                        <i class="fa-solid fa-list-check text-primary me-2"></i>Daftar Temuan Kondisi Gigi Pasien
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">No. Gigi</th>
                                    <th>Kondisi</th>
                                    <th>Rencana Tindakan</th>
                                    <th>Catatan / Lokasi</th>
                                    <th class="text-end pe-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($odontograms as $item)
                                    @php
                                        $badgeColor = $conditions[$item->kondisi]['badge'] ?? 'secondary';
                                    @endphp
                                    <tr>
                                        <td class="ps-3">
                                            <span class="badge bg-dark fs-6 px-2">Gigi #{{ $item->nomor_gigi }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $badgeColor }}">{{ $item->kondisi }}</span>
                                        </td>
                                        <td class="fw-semibold">{{ $item->tindakan ?: '-' }}</td>
                                        <td class="text-muted small">{{ $item->catatan ?: '-' }}</td>
                                        <td class="text-end pe-3">
                                            <button 
                                                type="button" 
                                                class="btn btn-sm btn-outline-primary"
                                                onclick="selectTooth({{ $item->nomor_gigi }}, '{{ $item->kondisi }}', '{{ addslashes($item->tindakan) }}', '{{ addslashes($item->catatan) }}')"
                                            >
                                                <i class="fa-solid fa-pen-to-square"></i> Edit
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            Semua gigi dalam kondisi normal atau belum ada catatan kelainan gigi.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Update Tooth Form Card -->
        <div class="col-lg-4 d-print-none">
            <div class="card border-0 shadow-sm rounded-3 sticky-top" style="top: 20px;">
                <div class="card-header bg-primary text-white py-3">
                    <h6 class="card-title mb-0 fw-bold">
                        <i class="fa-solid fa-tooth me-2"></i>Input / Update Kondisi Gigi
                    </h6>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('odontogram.update_tooth', $patient) }}" id="toothForm">
                        @csrf

                        <div class="mb-3">
                            <label for="toothNumber" class="form-label fw-semibold">Nomor Gigi (FDI)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-hashtag"></i></span>
                                <input type="number" class="form-control fw-bold text-primary fs-5" id="toothNumber" name="nomor_gigi" value="11" min="11" max="48" required>
                            </div>
                            <small class="text-muted">Pilih atau klik nomor gigi pada diagram di samping.</small>
                        </div>

                        <div class="mb-3">
                            <label for="toothCondition" class="form-label fw-semibold">Kondisi Gigi</label>
                            <select class="form-select fw-semibold" id="toothCondition" name="kondisi" required>
                                @foreach ($conditions as $cKey => $cVal)
                                    <option value="{{ $cKey }}">{{ $cVal['label'] }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Pilih 'Sehat / Normal' untuk menghapus temuan kelainan.</small>
                        </div>

                        <div class="mb-3">
                            <label for="toothTreatment" class="form-label fw-semibold">Rencana Tindakan Medis</label>
                            <input type="text" class="form-control" id="toothTreatment" name="tindakan" placeholder="Contoh: Tambal komposit / Cabut gigi">
                        </div>

                        <div class="mb-4">
                            <label for="toothNotes" class="form-label fw-semibold">Catatan Tambahan</label>
                            <textarea class="form-control" id="toothNotes" name="catatan" rows="2" placeholder="Contoh: Karies oklusal, ngilu pada rangsang dingin"></textarea>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary py-2 fw-semibold">
                                <i class="fa-solid fa-save me-1"></i> Simpan Status Gigi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print { .odontogram-chart { width: 100% !important; } }
    .tooth-box {
        width: 44px;
        height: 60px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 3px;
        transition: all 0.2s ease;
    }
    .tooth-box:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 12px rgba(37, 99, 235, 0.15) !important;
        border-color: #2563eb;
    }
    .tooth-btn:focus .tooth-box {
        border: 2px solid #2563eb !important;
    }
</style>

@push('scripts')
<script>
    function selectTooth(number, condition, treatment, notes) {
        document.getElementById('toothNumber').value = number;
        document.getElementById('toothCondition').value = condition || 'Sehat';
        document.getElementById('toothTreatment').value = treatment || '';
        document.getElementById('toothNotes').value = notes || '';

        // Flash visual highlight
        const formCard = document.getElementById('toothForm');
        formCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
</script>
@endpush
@endsection
