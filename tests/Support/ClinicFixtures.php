<?php

use App\Models\Patient;
use App\Models\Visit;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

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

// Report content tests authenticate generated fixtures via the actual report challenge.
function confirmClinicReports(TestCase $test): void
{
    $user = auth()->user();
    $user->forceFill(['password' => Hash::make('fixture-report-password')])->save();
    $test->post(route('reports.login.store'), ['login' => $user->email, 'password' => 'fixture-report-password'])
        ->assertRedirect()->assertSessionHasNoErrors();
    $test->withCookie(config('session.cookie'), session()->getId())->withCredentials();
}

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
        'poli' => 'Poli Gigi',
        'keluhan' => 'Batuk sejak kemarin',
    ];
}
