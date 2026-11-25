<?php

use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Treatment;
use App\Models\User;
use App\Models\Visit;

// Optional HTML snapshots use synthetic records and the isolated test database only.
test('all redesigned screens render with real route data', function () {
    $capture = function ($name, $response) {
        $response->assertOk();
        if ($directory = getenv('CLINIC_SCREENSHOTS_DIR')) {
            if (! is_dir($directory)) {
                mkdir($directory, 0700, true);
            }
            $html = $response->getContent();
            $html = preg_replace_callback('~https?://[^"\s]+/(css/clinic\.css|js/clinic\.js)(?:\?[^"\s]*)?~', fn ($m) => 'file://'.public_path($m[1]), $html);
            file_put_contents($directory.'/'.$name.'.html', $html);
        }
    };
    $capture('registration', $this->get('/'));
    $capture('login', $this->get(route('login')));
    $capture('forgot-password', $this->get(route('password.request')));
    $capture('reset-password', $this->get(route('password.reset', ['token' => 'demo-token'])));
    $this->withSession(['registration_receipt' => ['nama' => 'Pasien Contoh', 'visit_id' => 1, 'no_rm' => 'RM-DEMO', 'poli' => 'Poli Gigi', 'tanggal' => now()->format('d/m/Y')]]);
    $capture('receipt', $this->get(route('registration.success')));
    $user = User::factory()->create(['name' => 'Petugas Klinik']);
    $this->actingAs($user);
    $doctor = Doctor::create(['nama' => 'drg. Senja Contoh', 'spesialisasi' => 'Dokter Gigi', 'status' => 'Aktif', 'no_sip' => 'SIP-DEMO', 'no_hp' => '080000000000']);
    $patient = Patient::create(['no_rm' => 'RM-DEMO-001', 'nama' => 'Siti Contoh', 'jenis_kelamin' => 'P', 'tanggal_lahir' => '1997-10-14', 'alamat' => 'Alamat pasien contoh', 'penjamin' => 'Umum', 'alergi' => 'Alergi tercatat: penisilin']);
    $visit = Visit::create(['patient_id' => $patient->id, 'doctor_id' => $doctor->id, 'no_antrean' => 'LEGACY-DEMO', 'tanggal_kunjungan' => now()->toDateString(), 'poli' => 'Poli Gigi', 'keluhan' => 'Gigi geraham bawah terasa ngilu', 'diagnosa' => 'Catatan diagnosis dokter', 'status' => 'Sedang Diperiksa', 'biaya_tindakan' => 200000, 'biaya_obat' => 35000, 'total_biaya' => 235000]);
    $treatment = Treatment::create(['kode' => 'GG-DEMO', 'nama' => 'Penambalan gigi', 'kategori' => 'Gigi', 'tarif' => 200000]);
    $visit->visitTreatments()->create(['treatment_id' => $treatment->id, 'biaya' => 200000]);
    $this->post(route('visits.odontogram.update', $visit), ['nomor_gigi' => 46, 'kondisi' => 'Karies', 'catatan' => 'Temuan contoh']);
    foreach (['dashboard' => [], 'patients.index' => [], 'patients.show' => [$patient], 'patients.edit' => [$patient], 'visits.index' => [], 'visits.create' => [], 'visits.show' => [$visit], 'visits.edit' => [$visit], 'visits.odontogram.show' => [$visit], 'medical_records.index' => [], 'medical_records.show' => [$patient], 'doctors.index' => [], 'doctors.create' => [], 'doctors.show' => [$doctor], 'doctors.edit' => [$doctor], 'profile.edit' => []] as $route => $args) {
        $capture($route, $this->get(route($route, $args)));
    }
    $capture('report-login', $this->get(route('reports.login')));
    $capture('visit-print', $this->get(route('visits.print', $visit)));
    foreach (['visits.pdf' => [$visit], 'visits.list.pdf' => []] as $route => $args) {
        $document = $this->get(route($route, $args))->assertOk()->assertHeader('Content-Type', 'application/pdf');
        if ($directory = getenv('CLINIC_SCREENSHOTS_DIR')) {
            file_put_contents($directory.'/'.$route.'.pdf', $document->getContent());
        }
    }
    confirmClinicReports($this);
    foreach (['reports.index' => [], 'reports.visits' => [], 'reports.charts' => [], 'reports.treatments' => [], 'reports.finance' => [], 'reports.doctors' => [], 'reports.history.show' => [$patient], 'reports.examinations.show' => [$visit], 'reports.odontogram.show' => [$visit]] as $route => $args) {
        $capture($route, $this->get(route($route, $args)));
    }
});
