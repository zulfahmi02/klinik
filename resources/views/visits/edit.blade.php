@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1"><i class="fa-solid fa-user-doctor text-primary me-2"></i>Pemeriksaan & Rekam Medis Kunjungan</h3>
            <p class="text-muted mb-0">Pasien: <strong>{{ $visit->patient->nama }}</strong> ({{ $visit->patient->no_rm }})</p>
        </div>
        <a href="{{ route('visits.show', $visit) }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Detail
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="clinical-banner"><div class="patient-avatar">{{ mb_substr($visit->patient->nama, 0, 1) }}</div><div><strong class="fs-5">{{ $visit->patient->nama }}</strong><div class="clinical-meta">{{ $visit->patient->no_rm }} · Kunjungan #{{ $visit->id }} · {{ $visit->tanggal_kunjungan->format('d M Y') }}</div></div><span class="badge bg-primary ms-auto">Poli Gigi</span></div>
    @if ($visit->patient->alergi)<div class="clinical-note"><strong>Alergi pasien:</strong> {{ $visit->patient->alergi }}</div>@endif
    <nav class="clinical-links no-print" aria-label="Bagian pemeriksaan"><a href="#examination">Pemeriksaan</a><a href="#odontogram">Odontogram</a><a href="#billing">Tindakan & Biaya</a><a href="{{ route('medical_records.show', $visit->patient) }}">Riwayat pasien</a><a href="{{ route('visits.pdf', $visit) }}">Unduh PDF</a></nav>
    @if ($visit->poli === 'Poli Gigi')
        @include('odontogram.partials.visit')
        @if ($snapshotAvailable && $visit->status !== 'Batal')
            @include('odontogram.partials.editor')
        @endif
    @endif
    <form id="examination" method="POST" action="{{ route('visits.update', $visit) }}" class="mt-4">
        @csrf
        <input type="hidden" name="treatments_submitted" value="1">
        @method('PUT')

        <div class="row g-4">
            <!-- Left Column: Medical Examination -->
            <div class="col-lg-8">
                <!-- Diagnosis & Treatment Notes -->
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="card-title mb-0 fw-bold text-dark">
                            <i class="fa-solid fa-stethoscope text-primary me-2"></i>Diagnosa Medis & Tindakan
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Keluhan Pasien (Anamnesis Awal):</label>
                            <div class="p-3 bg-light rounded-3 text-muted">{{ $visit->keluhan ?: 'Tidak ada keluhan tertulis.' }}</div>
                        </div>

                        <div class="mb-3">
                            <label for="diagnosa" class="form-label fw-semibold">Diagnosa Klinis Dokter <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="diagnosa" name="diagnosa" rows="2" placeholder="Masukkan diagnosa dokter...">{{ old('diagnosa', $visit->diagnosa) }}</textarea>
                        </div>

                        <!-- Checkbox Treatments -->
                        <div id="billing"></div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Pilih Tindakan Medis yang Diberikan:</label>
                            @php
                                $existingTreatmentIds = $visit->visitTreatments->pluck('treatment_id')->toArray();
                            @endphp
                            <div class="border rounded-3 p-3 bg-light" style="max-height: 220px; overflow-y: auto;">
                                <div class="row g-2">
                                    @foreach ($treatments as $treatment)
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input 
                                                    class="form-check-input" 
                                                    type="checkbox" 
                                                    name="treatment_ids[]" 
                                                    value="{{ $treatment->id }}" 
                                                    id="treat_{{ $treatment->id }}" data-cost="{{ $visit->visitTreatments->firstWhere('treatment_id', $treatment->id)?->biaya ?? $treatment->tarif }}"
                                                    {{ in_array($treatment->id, old('treatment_ids', old('treatments_submitted') ? [] : $existingTreatmentIds)) ? 'checked' : '' }}
                                                >
                                                <label class="form-check-label small" for="treat_{{ $treatment->id }}">
                                                    <strong>{{ $treatment->nama }}</strong>
                                                    <div class="text-muted" style="font-size: 11px;">
                                                        <span class="badge bg-secondary" style="font-size: 10px;">{{ $treatment->kategori }}</span> 
                                                        Rp {{ number_format($treatment->tarif, 0, ',', '.') }}
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="mb-0">
                            <label for="catatan_dokter" class="form-label fw-semibold">Catatan Terapi / Resep Obat / Instruksi</label>
                            <textarea class="form-control" id="catatan_dokter" name="catatan_dokter" rows="3" placeholder="Tuliskan hasil perawatan, obat bila diberikan, dan instruksi kontrol...">{{ old('catatan_dokter', $visit->catatan_dokter) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Status & Billing -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="card-title mb-0 fw-bold text-dark">
                            <i class="fa-solid fa-sliders text-primary me-2"></i>Status & Dokter Pemeriksa
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="doctor_id" class="form-label fw-semibold">Dokter Pemeriksa</label>
                            <select class="form-select" id="doctor_id" name="doctor_id">
                                <option value="">-- Pilih Dokter --</option>
                                @foreach ($doctors as $doc)
                                    <option value="{{ $doc->id }}" {{ old('doctor_id', $visit->doctor_id) == $doc->id ? 'selected' : '' }}>
                                        {{ $doc->nama }} ({{ $doc->spesialisasi }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label fw-semibold">Status Pelayanan <span class="text-danger">*</span></label>
                            <select class="form-select fw-semibold" id="status" name="status" required>
                                <option value="Menunggu" {{ old('status', $visit->status) === 'Menunggu' ? 'selected' : '' }}>Terdaftar</option>
                                <option value="Sedang Diperiksa" {{ old('status', $visit->status) === 'Sedang Diperiksa' ? 'selected' : '' }}>Sedang Diperiksa</option>
                                <option value="Selesai" {{ old('status', $visit->status) === 'Selesai' ? 'selected' : '' }}>Selesai</option>
                                <option value="Batal" {{ old('status', $visit->status) === 'Batal' ? 'selected' : '' }}>Batal</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="card-title mb-0 fw-bold text-dark">
                            <i class="fa-solid fa-cash-register text-success me-2"></i>Biaya & Pembayaran
                        </h6>
                    </div>
                    <div class="card-body">


                        <div class="mb-3">
                            <label for="biaya_obat" class="form-label fw-semibold">Biaya Obat / Resep (Rp)</label>
                            <input type="number" class="form-control" id="biaya_obat" name="biaya_obat" min="0" value="{{ old('biaya_obat', $visit->biaya_obat) }}">
                        </div>

                        <div class="rounded-3 p-3 mb-3" style="background:var(--mint)"><div class="small">Perkiraan total tindakan + obat</div><strong class="fs-4 text-primary" id="billing-total" aria-live="polite">Rp {{ number_format($visit->biaya_tindakan + $visit->biaya_obat, 0, ',', '.') }}</strong><div class="small text-muted">Diperbarui mengikuti tindakan yang dipilih.</div></div>
                        <div class="mb-3">
                            <label for="status_pembayaran" class="form-label fw-semibold">Status Kasir</label>
                            <select class="form-select" id="status_pembayaran" name="status_pembayaran">
                                <option value="Belum Lunas" {{ old('status_pembayaran', $visit->status_pembayaran) === 'Belum Lunas' ? 'selected' : '' }}>Belum Lunas</option>
                                <option value="Lunas" {{ old('status_pembayaran', $visit->status_pembayaran) === 'Lunas' ? 'selected' : '' }}>Lunas</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="metode_pembayaran" class="form-label fw-semibold">Metode Pembayaran</label>
                            <select class="form-select" id="metode_pembayaran" name="metode_pembayaran">
                                <option value="Tunai" {{ old('metode_pembayaran', $visit->metode_pembayaran) === 'Tunai' ? 'selected' : '' }}>Tunai / Cash</option>
                                <option value="QRIS" {{ old('metode_pembayaran', $visit->metode_pembayaran) === 'QRIS' ? 'selected' : '' }}>QRIS / E-Wallet</option>
                                <option value="Transfer" {{ old('metode_pembayaran', $visit->metode_pembayaran) === 'Transfer' ? 'selected' : '' }}>Transfer Bank</option>
                                <option value="BPJS" {{ old('metode_pembayaran', $visit->metode_pembayaran) === 'BPJS' ? 'selected' : '' }}>Klaim BPJS</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary py-2 fw-semibold shadow-sm">
                        <i class="fa-solid fa-save me-1"></i> Simpan Pemeriksaan Medis
                    </button>
                    <a href="{{ route('visits.show', $visit) }}" class="btn btn-light py-2">Batal</a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
(() => {
 const inputs = document.querySelectorAll('[data-cost]'); const medicine = document.getElementById('biaya_obat');
 function updateTotal() { let total = Math.max(0, Number(medicine.value) || 0); inputs.forEach(input => { if (input.checked) total += Number(input.dataset.cost); }); document.getElementById('billing-total').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(total); }
 inputs.forEach(input => input.addEventListener('change', updateTotal)); medicine.addEventListener('input', updateTotal); updateTotal();
})();
</script>
@endpush
