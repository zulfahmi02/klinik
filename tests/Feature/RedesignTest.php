<?php

use App\Models\Doctor;
use App\Models\User;

test('patient editing and search remain usable after redesign', function () {
    $patient = clinicPatient('Redesign');
    $this->actingAs(User::factory()->create());
    $this->get(route('patients.edit', $patient))->assertOk()->assertSee('Edit Data Pasien');
    $this->put(route('patients.update', $patient), ['nama' => 'Nama Diperbarui', 'jenis_kelamin' => 'L'])->assertRedirect();
    $this->get(route('patients.index', ['search' => 'Diperbarui']))->assertOk()->assertViewHas('patients', fn ($patients) => $patients->count() === 1);
    $this->get(route('patients.index', ['search' => 'Tidak Ada']))->assertOk()->assertViewHas('patients', fn ($patients) => $patients->isEmpty());
});

test('doctor can be edited without consultation fees', function () {
    $doctor = Doctor::create(['nama' => 'drg. Contoh', 'spesialisasi' => 'Dokter Gigi', 'status' => 'Aktif']);
    $this->actingAs(User::factory()->create());
    $this->get(route('doctors.edit', $doctor))->assertOk()->assertDontSee('tarif_konsultasi');
    $this->put(route('doctors.update', $doctor), ['nama' => 'drg. Baru', 'spesialisasi' => 'Dokter Gigi', 'status' => 'Nonaktif'])->assertRedirect(route('doctors.show', $doctor));
    expect($doctor->fresh()->status)->toBe('Nonaktif');
});

test('examination contains selectable dental diagram and no general examination fields', function () {
    $visit = clinicVisit(clinicPatient('Diagram'), 'Poli Gigi');
    $this->actingAs(User::factory()->create());
    $response = $this->get(route('visits.edit', $visit))->assertOk()->assertSee('data-tooth="46"', false)
        ->assertDontSee('name="suhu_tubuh"', false)->assertDontSee('name="berat_badan"', false);
    expect(substr_count($response->getContent(), 'data-tooth="'))->toBe(52);
    $this->post(route('visits.odontogram.update', $visit), ['nomor_gigi' => 46, 'kondisi' => 'Karies', 'return_to_examination' => '1'])->assertRedirect(route('visits.edit', $visit).'#odontogram');
});

test('visit list PDF is gated and includes filtered results', function () {
    $visit = clinicVisit(clinicPatient('PDFDesign'), 'Poli Gigi');
    $this->get(route('visits.list.pdf'))->assertRedirect(route('login'));
    $this->actingAs(User::factory()->create());
    $response = $this->get(route('visits.list.pdf', ['search' => 'PDFDesign']))->assertOk()->assertHeader('Content-Type', 'application/pdf');
    expect($response->getContent())->toStartWith('%PDF');
});
