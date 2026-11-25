<?php

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\Process\Process;

test('clinic recap PDF includes every filtered visit and protects patient identifiers', function () {
    $this->get('/laporan/reports/visits/pdf')->assertRedirect(route('reports.login'));
    for ($i = 0; $i < 17; $i++) {
        $p = clinicPatient('Recap'.$i);
        clinicVisit($p, 'Poli Gigi', ['tanggal_kunjungan' => '2020-01-01', 'biaya_tindakan' => 10000, 'biaya_obat' => 2000]);
    }
    clinicVisit(clinicPatient('Outside'), 'Poli Gigi', ['tanggal_kunjungan' => '2022-01-01']);
    $this->actingAs(User::factory()->create());
    confirmClinicReports($this);
    $params = ['start_date' => '2020-01-01', 'end_date' => '2020-01-01'];
    $response = $this->get(route('reports.visits.pdf', $params))->assertOk()->assertHeader('Content-Type', 'application/pdf');
    expect(substr($response->getContent(), 0, 5))->toBe('%PDF-');
    $path = tempnam(sys_get_temp_dir(), 'recap');
    file_put_contents($path, $response->getContent());
    try {
        $process = new Process(['pdftotext', $path, '-']);
        $process->mustRun();
        $text = $process->getOutput();
        for ($i = 0; $i < 17; $i++) {
            expect($text)->toContain('Pasien Recap'.$i);
        }
        expect($text)->toContain('204.000')->not->toContain('Outside')->not->toContain('NIK-')->not->toContain('HP-')->not->toContain('Konsultasi')->not->toContain('Antrean');
    } finally {
        unlink($path);
    }
    $this->get(route('reports.visits', $params))->assertOk()->assertSee('href="'.e(route('reports.visits.pdf', $params)).'"', false);
});

test('recap pagination retains filters and summary covers all matching records', function () {
    for ($i = 0; $i < 17; $i++) {
        clinicVisit(clinicPatient('Paging'.$i), 'Poli Gigi', ['tanggal_kunjungan' => '2020-01-01']);
    }
    $this->actingAs(User::factory()->create());
    confirmClinicReports($this);
    $this->get(route('reports.visits', ['start_date' => '2020-01-01']))->assertOk()
        ->assertViewHas('totalVisits', 17)
        ->assertViewHas('visits', fn ($rows) => $rows instanceof LengthAwarePaginator && $rows->count() === 15)
        ->assertSee('start_date=2020-01-01', false)->assertDontSee('name="diagnosa"', false);
});

test('report landing is authenticated and separates reporting from operations', function () {
    $this->get('/laporan/reports')->assertRedirect(route('reports.login'));
    $this->actingAs(User::factory()->create());
    confirmClinicReports($this);
    $this->get('/laporan')->assertOk()->assertSee('Laporan Klinik')->assertSee('Laporan Kunjungan')
        ->assertSee('href="'.route('reports.visits').'"', false)->assertDontSee('name="diagnosa"', false);
    $this->get(route('dashboard'))->assertOk()->assertSee('href="'.route('reports.entry').'"', false)
        ->assertSee('Kunjungan / Pemeriksaan')->assertSee('Laporan &amp; analitik', false);
    confirmClinicReports($this);
    $this->get(route('reports.visits'))->assertOk();
});

test('clinic recap defaults to all dates and validates its period', function () {
    $patient = clinicPatient('Period');
    clinicVisit($patient, 'Poli Gigi', ['tanggal_kunjungan' => '2020-01-01']);
    clinicVisit($patient, 'Poli Gigi', ['tanggal_kunjungan' => now()->toDateString()]);
    $this->actingAs(User::factory()->create());
    confirmClinicReports($this);
    $this->get(route('reports.visits'))->assertOk()->assertViewHas('totalVisits', 2);
    confirmClinicReports($this);
    $this->get(route('reports.visits', ['start_date' => '2020-01-01', 'end_date' => '2020-01-01']))->assertOk()->assertViewHas('totalVisits', 1);
    $this->getJson(route('reports.visits', ['start_date' => '2025-01-01', 'end_date' => '2020-01-01']))->assertUnprocessable()->assertJsonValidationErrors('end_date');
    $this->getJson(route('reports.visits', ['start_date' => 'invalid']))->assertUnprocessable()->assertJsonValidationErrors('start_date');
});
