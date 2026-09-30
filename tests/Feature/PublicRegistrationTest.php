<?php

use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;

function registrationData(): array
{
    return [
        'registration_token' => 'test-token',
        'nama' => 'Pasien Mandiri',
        'nik' => '3273010101900001',
        'jenis_kelamin' => 'L',
        'tanggal_lahir' => '1990-01-01',
        'no_hp' => '081234567890',
        'alamat' => 'Jalan Melati 1',
        'penjamin' => 'Umum',
        'poli' => 'Poli Umum',
        'keluhan' => 'Batuk sejak kemarin',
    ];
}

test('public registration creates patient and queued visit without a staff account', function () {
    $this->withSession(['registration_token' => 'test-token'])
        ->post(route('registration.store'), registrationData())
        ->assertRedirect(route('registration.success'));

    $this->assertGuest();
    $this->assertDatabaseCount('users', 0);
    $this->assertDatabaseCount('patients', 1);
    $this->assertDatabaseHas('visits', ['patient_id' => Patient::first()->id, 'no_antrean' => 'A-001', 'status' => 'Menunggu', 'status_pembayaran' => 'Belum Lunas']);
    $this->get(route('registration.success'))->assertOk()->assertSee('A-001')->assertDontSee('3273010101900001');
});

test('resubmitting the completed form does not duplicate the visit', function () {
    $this->withSession(['registration_token' => 'test-token'])->post(route('registration.store'), registrationData())->assertRedirect();
    $this->post(route('registration.store'), registrationData())->assertRedirect(route('registration.success'));
    $this->assertDatabaseCount('patients', 1);
    $this->assertDatabaseCount('visits', 1);
});

test('invalid submissions create neither patient nor visit', function () {
    $this->withSession(['registration_token' => 'test-token'])
        ->post(route('registration.store'), array_merge(registrationData(), ['poli' => 'Unknown', 'tanggal_lahir' => '2999-01-01']))
        ->assertSessionHasErrors(['poli', 'tanggal_lahir']);
    $this->assertDatabaseCount('patients', 0);
    $this->assertDatabaseCount('visits', 0);
});

test('existing identity is not overwritten or returned publicly', function () {
    $this->withSession(['registration_token' => 'test-token'])->post(route('registration.store'), registrationData());
    $this->withSession(['registration_token' => 'another-token'])
        ->post(route('registration.store'), array_merge(registrationData(), ['registration_token' => 'another-token', 'nama' => 'Changed']))
        ->assertSessionHasErrors('nik');
    $this->assertDatabaseCount('patients', 1);
    $this->assertDatabaseCount('visits', 1);
    expect(Patient::first()->nama)->toBe('Pasien Mandiri');
});

test('public registration rejects expired forms and missing insurer number', function () {
    $this->withSession(['registration_token' => 'current-token'])->post(route('registration.store'), registrationData())->assertSessionHasErrors('registration_token');
    $this->withSession(['registration_token' => 'test-token'])->post(route('registration.store'), array_merge(registrationData(), ['penjamin' => 'BPJS']))->assertSessionHasErrors('no_penjamin');
    $this->assertDatabaseCount('patients', 0);
});

test('staff and public registrations share queue allocation', function () {
    $this->withSession(['registration_token' => 'test-token'])->post(route('registration.store'), registrationData());
    $this->actingAs(User::factory()->create())->post(route('visits.store'), [
        'patient_id' => Patient::first()->id,
        'poli' => 'Poli Umum',
        'penjamin' => 'Umum',
        'no_antrean' => 'A-001',
    ])->assertRedirect(route('visits.index'));
    expect(Visit::orderBy('id')->pluck('no_antrean')->all())->toBe(['A-001', 'A-002']);
});

test('reports and patient lists require staff login', function () {
    $this->get('/laporan')->assertRedirect(route('login'));
    $this->get(route('patients.index'))->assertRedirect(route('login'));
    $this->get(route('reports.finance'))->assertRedirect(route('login'));
    $this->get(route('registration.success'))->assertRedirect(route('registration.create'));
    $this->actingAs(User::factory()->create())->get('/laporan')->assertOk();
});
