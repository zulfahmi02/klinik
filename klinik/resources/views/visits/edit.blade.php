@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1"><i class="fa-solid fa-user-doctor text-primary me-2"></i>Pemeriksaan & Rekam Medis Kunjungan</h3>
            <p class="text-muted mb-0">Pasien: <strong>{{ $visit->patient->nama }}</strong> ({{ $visit->patient->no_rm }}) &bull; No. Antrean: <span class="badge bg-primary">{{ $visit->no_antrean }}</span></p>
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

    <form method="POST" action="{{ route('visits.update', $visit) }}">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <!-- Left Column: Medical Examination -->
            <div class="col-lg-8">
                <!-- Vital Signs Card -->
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="card-title mb-0 fw-bold text-dark">
                            <i class="fa-solid fa-heart-pulse text-danger me-2"></i>Tanda-Tanda Vital (TTV)
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="tensi_darah" class="form-label fw-semibold">Tekanan Darah</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="tensi_darah" name="tensi_darah" value="{{ old('tensi_darah', $visit->tensi_darah) }}" placeholder="120/80">
                                    <span class="input-group-text small">mmHg</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="suhu_tubuh" class="form-label fw-semibold">Suhu Tubuh</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="suhu_tubuh" name="suhu_tubuh" value="{{ old('suhu_tubuh', $visit->suhu_tubuh) }}" placeholder="36.5">
                                    <span class="input-group-text small">°C</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="berat_badan" class="form-label fw-semibold">Berat Badan</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="berat_badan" name="berat_badan" value="{{ old('berat_badan', $visit->berat_badan) }}" placeholder="65">
                                    <span class="input-group-text small">kg</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

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
                                                    id="treat_{{ $treatment->id }}"
                                                    {{ in_array($treatment->id, old('treatment_ids', $existingTreatmentIds)) ? 'checked' : '' }}
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
                            <textarea class="form-control" id="catatan_dokter" name="catatan_dokter" rows="3" placeholder="Contoh: Paracetamol 500mg 3x1, Amoxicillin 500mg 3x1 setelah makan...">{{ old('catatan_dokter', $visit->catatan_dokter) }}</textarea>
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
                                <option value="Menunggu" {{ old('status', $visit->status) === 'Menunggu' ? 'selected' : '' }}>Menunggu</option>
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
                            <label for="biaya_konsultasi" class="form-label fw-semibold">Biaya Konsultasi (Rp)</label>
                            <input type="number" class="form-control" id="biaya_konsultasi" name="biaya_konsultasi" value="{{ old('biaya_konsultasi', $visit->biaya_konsultasi) }}">
                        </div>

                        <div class="mb-3">
                            <label for="biaya_obat" class="form-label fw-semibold">Biaya Obat / Resep (Rp)</label>
                            <input type="number" class="form-control" id="biaya_obat" name="biaya_obat" value="{{ old('biaya_obat', $visit->biaya_obat) }}">
                        </div>

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
