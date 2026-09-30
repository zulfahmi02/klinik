<?php

use App\Models\Doctor;
use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;
use Carbon\Carbon;

function clinicPatient(string $suffix): Patient
{
    return Patient::create(['no_rm' => 'RM-'.$suffix, 'nama' => 'Pasien '.$suffix, 'jenis_kelamin' => 'L', 'nik' => 'NIK-'.$suffix, 'no_hp' => 'HP-'.$suffix]);
}

function clinicVisit(Patient $patient, string $poli, array $attributes = []): Visit
{
    return Visit::create(array_merge([
        'patient_id' => $patient->id, 'no_antrean' => 'A-'.$patient->id, 'poli' => $poli,
        'tanggal_kunjungan' => now()->toDateString(), 'status' => 'Menunggu',
    ], $attributes));
}

test('doctor management is separate from the performance report', function () {
    $this->actingAs(User::factory()->create());
    $this->get(route('doctors.create'))->assertOk()->assertSee('name="nama"', false);
    $this->post(route('doctors.store'), [
        'nama' => 'dr. Baru', 'spesialisasi' => 'Dokter Gigi', 'no_sip' => 'SIP-123',
        'no_hp' => '08123456789', 'tarif_konsultasi' => 70000, 'status' => 'Aktif',
    ])->assertRedirect(route('doctors.show', Doctor::first()));
    $this->get(route('doctors.index'))->assertOk()->assertSee('dr. Baru');
    $this->get(route('doctors.show', Doctor::first()))->assertOk()->assertSee('SIP-123')->assertSee('70.000');
    $this->get(route('reports.doctors'))->assertOk()->assertDontSee('name="nama"', false)->assertDontSee('Hapus</button>', false);
});

test('visit search matches multiple fields while respecting date and clinic filters', function () {
    $this->actingAs(User::factory()->create());
    $target = clinicPatient('Target');
    $other = clinicPatient('Other');
    $doctor = Doctor::create(['nama' => 'dr. Pencarian', 'spesialisasi' => 'Gigi']);
    $visit = clinicVisit($target, 'Poli Gigi', ['doctor_id' => $doctor->id, 'keluhan' => 'Ngilu', 'diagnosa' => 'Karies']);
    clinicVisit($other, 'Poli Umum');
    foreach (['Target', 'RM-Target', 'NIK-Target', 'HP-Target', 'Pencarian', $visit->no_antrean, 'Ngilu', 'Karies'] as $search) {
        $this->get(route('reports.visits', ['search' => $search]))->assertOk()
            ->assertViewHas('visits', fn ($visits) => $visits->modelKeys() === [$visit->id])
            ->assertViewHas('totalVisits', 1);
    }
    $this->get(route('reports.visits', ['search' => 'Target', 'poli' => 'Poli Umum']))
        ->assertViewHas('totalVisits', 0);
    $this->get(route('reports.visits', ['search' => 'Target', 'start_date' => now()->subDays(5)->toDateString(), 'end_date' => now()->subDay()->toDateString()]))
        ->assertViewHas('totalVisits', 0);
});

test('odontogram includes only patients with noncancelled dental visits', function () {
    $this->actingAs(User::factory()->create());
    $dental = clinicPatient('Dental');
    $general = clinicPatient('General');
    $cancelled = clinicPatient('Cancelled');
    clinicVisit($dental, 'Poli Gigi');
    clinicVisit($dental, 'Poli Umum');
    clinicVisit($general, 'Poli Umum');
    clinicVisit($cancelled, 'Poli Gigi', ['status' => 'Batal']);
    $this->get(route('odontogram.index'))->assertOk()
        ->assertViewHas('patients', fn ($patients) => $patients->getCollection()->modelKeys() === [$dental->id]);
    $this->get(route('odontogram.index', ['search' => $general->no_rm]))->assertViewHas('patients', fn ($patients) => $patients->isEmpty());
    $this->get(route('odontogram.show', $dental))->assertOk()->assertSee('function selectTooth', false);
    $this->get(route('odontogram.show', $general))->assertNotFound();
    $this->post(route('odontogram.update_tooth', $general), ['nomor_gigi' => 11, 'kondisi' => 'Karies'])->assertNotFound();
    $this->post(route('odontogram.update_tooth', $dental), ['nomor_gigi' => 11, 'kondisi' => 'Karies'])->assertRedirect();
    $this->assertDatabaseHas('odontograms', ['patient_id' => $dental->id, 'nomor_gigi' => 11]);
});

test('charts use the selected year consistently and retain doctors with matching names', function () {
    $this->travelTo(Carbon::parse('2026-09-30 10:00:00'));
    $this->actingAs(User::factory()->create());
    $patient = clinicPatient('Charts');
    $first = Doctor::create(['nama' => 'dr. Sama']);
    $second = Doctor::create(['nama' => 'dr. Sama']);
    clinicVisit($patient, 'Poli Gigi', ['tanggal_kunjungan' => '2025-01-31', 'doctor_id' => $first->id, 'penjamin' => 'BPJS']);
    clinicVisit($patient, 'Poli Gigi', ['tanggal_kunjungan' => '2025-12-31', 'doctor_id' => $second->id, 'penjamin' => 'Umum']);
    clinicVisit($patient, 'Poli Umum', ['tanggal_kunjungan' => '2026-09-30']);
    $this->get(route('reports.charts', ['year' => 2025]))->assertOk()
        ->assertViewHas('monthlyData', [1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1])
        ->assertViewHas('poliVisits', ['Poli Gigi' => 2])
        ->assertViewHas('penjaminVisits', ['BPJS' => 1, 'Umum' => 1])
        ->assertViewHas('doctorVisits', fn ($values) => count($values) === 2 && array_sum($values) === 2)
        ->assertViewHas('dailyTrend', [0, 0, 0, 0, 0, 0, 1]);
    $this->get(route('reports.charts', ['year' => 2024]))->assertOk()
        ->assertViewHas('monthlyData', array_fill(0, 12, 0))->assertSee('Belum ada kunjungan');
    $this->getJson(route('reports.charts', ['year' => 'invalid']))->assertUnprocessable();
    $this->getJson(route('reports.charts', ['year' => 2027]))->assertUnprocessable();
});
