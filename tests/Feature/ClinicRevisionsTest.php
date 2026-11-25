<?php

use App\Models\Odontogram;
use App\Models\Treatment;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Process\Process;

test('only dentistry can be registered or selected without rewriting legacy visits', function () {
    $patient = clinicPatient('Legacy');
    $legacy = clinicVisit($patient, 'Poli Umum');
    $this->get('/')->assertOk()->assertSee('Poli Gigi')->assertDontSee('Poli Umum')->assertDontSee('Poli Anak');
    $this->withSession(['registration_token' => 'test-token'])->post(route('registration.store'), array_merge(registrationData(), ['poli' => 'Poli Umum']))->assertSessionHasErrors('poli');
    $this->actingAs(User::factory()->create());
    $this->get(route('visits.create'))->assertOk()->assertDontSee('Poli Umum')->assertDontSee('KIA / KB');
    $this->post(route('visits.store'), ['patient_id' => $patient->id, 'poli' => 'Poli Anak', 'penjamin' => 'Umum'])->assertSessionHasErrors('poli');
    confirmClinicReports($this);
    $this->get(route('reports.visits'))->assertOk()->assertDontSee('value="Poli Umum"', false);
    $this->getJson(route('reports.visits', ['poli' => 'Poli Umum']))->assertUnprocessable();
    expect($legacy->fresh()->poli)->toBe('Poli Umum');
    expect(Visit::count())->toBe(1);
});

test('queue UI is absent while legacy references are preserved', function () {
    $patient = clinicPatient('Queue');
    $visit = clinicVisit($patient, 'Poli Gigi', ['no_antrean' => 'A-987']);
    $this->get('/')->assertOk()->assertDontSee('antrean');
    $this->actingAs(User::factory()->create());
    foreach (['dashboard', 'visits.index', 'visits.create', 'reports.visits', 'reports.finance'] as $route) {
        if (str_starts_with($route, 'reports.')) {
            confirmClinicReports($this);
        }
        $this->get(route($route))->assertOk()->assertDontSee('Antrean')->assertDontSee('antrean')->assertDontSee('A-987');
    }
    foreach (['visits.show', 'visits.edit'] as $route) {
        $this->get(route($route, $visit))->assertOk()->assertDontSee('Antrean')->assertDontSee('A-987');
    }
    $this->get(route('medical_records.show', $patient))->assertOk()->assertDontSee('A-987');
    expect($visit->fresh()->no_antrean)->toBe('A-987');
});

test('insurer number uses the approved label in forms errors and patient details', function () {
    $this->get('/')->assertOk()->assertSee('Nomor penjamin')->assertDontSee('Nomor kartu penjamin');
    $this->withSession(['registration_token' => 'test-token'])->post(route('registration.store'), array_merge(registrationData(), ['penjamin' => 'BPJS']))
        ->assertSessionHasErrors(['no_penjamin' => 'Nomor penjamin wajib diisi untuk BPJS dan asuransi swasta.']);
    $patient = clinicPatient('Insurer');
    $patient->update(['no_penjamin' => 'INS-123']);
    $this->actingAs(User::factory()->create())->get(route('patients.show', $patient))->assertOk()->assertSee('Nomor penjamin')->assertSee('INS-123');
});

test('new registrations and examination billing exclude consultation and preserve other history', function () {
    $this->withSession(['registration_token' => 'test-token'])->post(route('registration.store'), registrationData())->assertRedirect();
    expect(Visit::first()->total_biaya)->toBe(0);
    $patient = clinicPatient('Billing');
    $legacy = clinicVisit($patient, 'Poli Gigi', ['biaya_konsultasi' => 50000, 'total_biaya' => 50000]);
    $visit = clinicVisit($patient, 'Poli Gigi', ['biaya_konsultasi' => 50000, 'biaya_obat' => 12000]);
    $treatment = Treatment::create(['kode' => 'T-1', 'nama' => 'Tambal', 'tarif' => 90000]);
    $this->actingAs(User::factory()->create());
    $this->post(route('visits.store'), ['patient_id' => $patient->id, 'poli' => 'Poli Gigi', 'penjamin' => 'Umum', 'biaya_konsultasi' => 99999])->assertRedirect();
    expect(Visit::latest('id')->first()->total_biaya)->toBe(0);
    $this->put(route('visits.update', $visit), ['status' => 'Selesai', 'status_pembayaran' => 'Lunas', 'biaya_konsultasi' => 99999, 'treatment_ids' => [$treatment->id]])->assertRedirect();
    expect($visit->fresh()->total_biaya)->toBe(102000);
    expect($visit->fresh()->biaya_konsultasi)->toBe(0);
    expect($legacy->fresh()->total_biaya)->toBe(50000);
    expect($legacy->fresh()->biaya_konsultasi)->toBe(50000);
    foreach (['visits.show', 'visits.edit'] as $route) {
        $this->get(route($route, $visit))->assertOk()->assertDontSee('Konsultasi')->assertDontSee('biaya_konsultasi');
    }
    foreach (['doctors.create', 'doctors.index', 'reports.finance', 'reports.doctors'] as $route) {
        if (str_starts_with($route, 'reports.')) {
            confirmClinicReports($this);
        }
        $this->get(route($route))->assertOk()->assertDontSee('konsultasi')->assertDontSee('Konsultasi');
    }
});

test('visit odontogram snapshots stay historical and use authenticated visit context', function () {
    $patient = clinicPatient('Teeth');
    $old = clinicVisit($patient, 'Poli Gigi', ['tanggal_kunjungan' => now()->subDay()->toDateString()]);
    $current = clinicVisit($patient, 'Poli Gigi');
    $tooth = Odontogram::create(['patient_id' => $patient->id, 'nomor_gigi' => 11, 'kondisi' => 'Karies', 'catatan' => 'Kondisi terbaru']);
    $this->get('/pemeriksaan/visits/'.$old->id.'/odontogram')->assertRedirect(route('login'));
    $this->post('/pemeriksaan/visits/'.$old->id.'/odontogram', ['nomor_gigi' => 11, 'kondisi' => 'Hilang'])->assertRedirect(route('login'));
    $this->actingAs(User::factory()->create());
    $this->get(route('visits.show', $old))->assertOk()->assertSee('Belum ada hasil odontogram yang disimpan untuk kunjungan ini')->assertSee('Kondisi terbaru pasien (referensi)')->assertDontSee('href="'.route('odontogram.index').'"', false);
    $this->get(route('visits.edit', $old))->assertOk()->assertSee('Odontogram kunjungan');
    $this->get('/pemeriksaan/visits/'.$old->id.'/odontogram')->assertOk();
    $this->post('/pemeriksaan/visits/'.$old->id.'/odontogram', ['nomor_gigi' => 11, 'kondisi' => 'Tambalan Komposit', 'catatan' => 'Catatan lama'])->assertRedirect();
    expect($tooth->fresh()->kondisi)->toBe('Karies');
    $this->post('/pemeriksaan/visits/'.$current->id.'/odontogram', ['nomor_gigi' => 11, 'kondisi' => 'Hilang'])->assertRedirect();
    expect($tooth->fresh()->kondisi)->toBe('Hilang');
    $snapshot = DB::table('visit_odontogram_snapshots')->where('visit_id', $old->id)->first();
    expect(json_decode($snapshot->teeth, true)['11']['kondisi'])->toBe('Tambalan Komposit');
    $this->get(route('visits.show', $old))->assertOk()->assertSee('Catatan lama')->assertSee('Belum dicatat');
    $this->postJson('/pemeriksaan/visits/'.$current->id.'/odontogram', ['nomor_gigi' => 99, 'kondisi' => 'Unknown'])->assertUnprocessable()->assertJsonValidationErrors(['nomor_gigi', 'kondisi']);
    $this->post('/pemeriksaan/visits/'.$current->id.'/odontogram', ['nomor_gigi' => 11, 'kondisi' => 'Sehat', 'catatan' => 'Sudah diperiksa'])->assertRedirect();
    $this->assertDatabaseHas('odontograms', ['patient_id' => $patient->id, 'nomor_gigi' => 11, 'kondisi' => 'Sehat', 'catatan' => 'Sudah diperiksa']);
    $general = clinicVisit($patient, 'Poli Umum');
    $this->post('/pemeriksaan/visits/'.$general->id.'/odontogram', ['nomor_gigi' => 11, 'kondisi' => 'Karies'])->assertNotFound();
});

test('visit reports link the correct patient history and visit odontogram with eager relations', function () {
    clinicPatient('Unrelated');
    $patient = clinicPatient('Report');
    $visit = clinicVisit($patient, 'Poli Gigi');
    $this->actingAs(User::factory()->create());
    confirmClinicReports($this);
    $this->get(route('reports.visits'))->assertOk()
        ->assertSee('href="'.route('reports.history.show', $patient).'"', false)
        ->assertSee('href="'.route('reports.odontogram.show', $visit).'"', false)
        ->assertViewHas('visits', fn ($visits) => $visits->every(fn ($row) => $row->relationLoaded('patient') && $row->relationLoaded('doctor') && $row->relationLoaded('visitTreatments')));
    $this->get(route('medical_records.show', $patient))->assertOk()->assertSee('Pasien Report');
    $this->get(route('visits.odontogram.show', $visit))->assertOk()->assertSee('Pasien Report');
});

test('visit print resume includes examination odontogram and treatment medicine billing only', function () {
    $patient = clinicPatient('Print');
    $visit = clinicVisit($patient, 'Poli Gigi', ['diagnosa' => 'Karies cetak', 'catatan_dokter' => 'Resep uji', 'biaya_konsultasi' => 50000, 'biaya_obat' => 15000, 'biaya_tindakan' => 80000, 'total_biaya' => 145000]);
    $treatment = Treatment::create(['kode' => 'PRINT', 'nama' => 'Tambal cetak', 'tarif' => 80000]);
    $visit->visitTreatments()->create(['treatment_id' => $treatment->id, 'biaya' => 80000]);
    $this->get('/pemeriksaan/visits/'.$visit->id.'/print')->assertRedirect(route('login'));
    $this->actingAs(User::factory()->create());
    $this->get('/pemeriksaan/visits/'.$visit->id.'/print')->assertOk()->assertSee('Belum ada snapshot');
    $this->post(route('visits.odontogram.update', $visit), ['nomor_gigi' => 11, 'kondisi' => 'Karies', 'catatan' => 'Gigi cetak']);
    $this->get('/pemeriksaan/visits/'.$visit->id.'/print')->assertOk()
        ->assertHeader('Cache-Control', 'no-store, private')
        ->assertSee('Pasien Print')->assertSee('Karies cetak')->assertSee('Resep uji')->assertSee('Gigi cetak')
        ->assertSee('Tambal cetak')->assertSee('15.000')->assertSee('95.000')
        ->assertDontSee('145.000')->assertDontSee($visit->no_antrean)->assertDontSee('konsultasi')->assertDontSee('antrean');
    expect($visit->fresh()->total_biaya)->toBe(145000);
});

test('visit pdf is an authenticated real downloadable document with the correct clinical content', function () {
    $patient = clinicPatient('PDF');
    $patient->update(['no_penjamin' => 'PENJAMIN-TEST']);
    $visit = clinicVisit($patient, 'Poli Gigi', ['diagnosa' => 'Diagnosa PDF', 'keluhan' => 'Nyeri gigi', 'catatan_dokter' => 'Resep PDF', 'biaya_konsultasi' => 50000, 'biaya_tindakan' => 80000, 'biaya_obat' => 15000, 'total_biaya' => 145000]);
    $treatment = Treatment::create(['kode' => 'PDF', 'nama' => 'Tambal PDF', 'tarif' => 80000]);
    $visit->visitTreatments()->create(['treatment_id' => $treatment->id, 'biaya' => 80000]);
    $this->get('/pemeriksaan/visits/'.$visit->id.'/pdf')->assertRedirect(route('login'));
    $this->actingAs(User::factory()->create());
    $this->post(route('visits.odontogram.update', $visit), ['nomor_gigi' => 11, 'kondisi' => 'Karies', 'catatan' => 'Odontogram PDF'])->assertRedirect();
    $response = $this->get('/pemeriksaan/visits/'.$visit->id.'/pdf')->assertOk()
        ->assertHeader('Content-Type', 'application/pdf')
        ->assertHeader('Cache-Control', 'no-store, private')
        ->assertDownload('kunjungan-'.$visit->id.'.pdf');
    expect($response->getContent())->toStartWith('%PDF-');
    $path = tempnam(sys_get_temp_dir(), 'clinic-pdf-');
    try {
        file_put_contents($path, $response->getContent());
        $process = new Process(['pdftotext', '-layout', $path, '-']);
        $process->mustRun();
        $text = $process->getOutput();
        foreach (['Pasien PDF', 'RM-PDF', 'PENJAMIN-TEST', 'Diagnosa PDF', 'Nyeri gigi', 'Resep PDF', 'Odontogram PDF', 'Tambal PDF', '15.000', '95.000', 'Belum dicatat'] as $expected) {
            expect($text)->toContain($expected);
        }
        foreach (['145.000', '50.000', 'antrean', 'konsultasi', $visit->no_antrean, 'password', 'APP_KEY'] as $excluded) {
            expect($text)->not->toContain($excluded);
        }
    } finally {
        unlink($path);
    }
    $this->get(route('visits.show', $visit))->assertSee('href="'.route('visits.pdf', $visit).'"', false);
    $this->get('/pemeriksaan/visits/999999/pdf')->assertNotFound();
});

test('billing edits validate before writing preserve retained prices and can clear treatment selection', function () {
    $patient = clinicPatient('BillingEdit');
    $visit = clinicVisit($patient, 'Poli Gigi', ['biaya_tindakan' => 75000, 'biaya_obat' => 10000, 'total_biaya' => 85000]);
    $treatment = Treatment::create(['kode' => 'RETAIN', 'nama' => 'Harga berubah', 'tarif' => 90000]);
    $visit->visitTreatments()->create(['treatment_id' => $treatment->id, 'biaya' => 75000]);
    $this->actingAs(User::factory()->create());
    $base = ['status' => 'Selesai', 'status_pembayaran' => 'Lunas'];
    $this->putJson(route('visits.update', $visit), $base + ['treatment_ids' => [999999]])->assertUnprocessable();
    expect($visit->fresh()->total_biaya)->toBe(85000);
    expect($visit->visitTreatments()->count())->toBe(1);
    $this->put(route('visits.update', $visit), $base + ['treatment_ids' => [$treatment->id]])->assertRedirect();
    expect($visit->fresh()->total_biaya)->toBe(85000);
    $this->get(route('visits.edit', $visit))->assertSee('name="treatments_submitted"', false);
    $this->put(route('visits.update', $visit), $base + ['treatments_submitted' => 1])->assertRedirect();
    expect($visit->fresh()->total_biaya)->toBe(10000);
    expect($visit->visitTreatments()->count())->toBe(0);
});

test('additive snapshot migration preserves legacy data and pending migration leaves visits readable', function () {
    $patient = clinicPatient('Migration');
    $visit = clinicVisit($patient, 'Poli Gigi', ['biaya_konsultasi' => 50000, 'total_biaya' => 50000]);
    $legacy = Odontogram::create(['patient_id' => $patient->id, 'visit_id' => $visit->id, 'nomor_gigi' => 12, 'kondisi' => 'Karies', 'catatan' => 'Legacy linked']);
    $current = Odontogram::create(['patient_id' => $patient->id, 'nomor_gigi' => 13, 'kondisi' => 'Hilang', 'catatan' => 'Latest only']);
    $before = [$visit->fresh()->getAttributes(), $legacy->fresh()->getAttributes(), $current->fresh()->getAttributes()];
    Schema::drop('visit_odontogram_snapshots');
    $this->actingAs(User::factory()->create());
    $this->get(route('visits.show', $visit))->assertOk();
    $this->get(route('visits.odontogram.show', $visit))->assertOk()->assertSee('Penyimpanan odontogram kunjungan belum tersedia');
    $this->post(route('visits.odontogram.update', $visit), ['nomor_gigi' => 11, 'kondisi' => 'Karies'])->assertStatus(503);
    $migration = require database_path('migrations/2026_10_08_000001_create_visit_odontogram_snapshots_table.php');
    $migration->up();
    expect([$visit->fresh()->getAttributes(), $legacy->fresh()->getAttributes(), $current->fresh()->getAttributes()])->toBe($before);
    $this->get(route('visits.print', $visit))->assertOk()->assertSee('Legacy linked')->assertDontSee('Latest only');
    $this->post(route('visits.odontogram.update', $visit), ['nomor_gigi' => 11, 'kondisi' => 'Tambalan Komposit'])->assertRedirect();
    $this->get(route('visits.print', $visit))->assertOk()->assertSee('Legacy linked')->assertSee('Tambalan Komposit')->assertDontSee('Latest only');
    expect($legacy->fresh()->getAttributes())->toBe($before[1]);
});
