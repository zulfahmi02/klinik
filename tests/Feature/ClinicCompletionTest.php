<?php

use App\Models\Doctor;
use App\Models\Treatment;
use App\Models\User;
use App\Services\VisitOdontogram;
use Symfony\Component\Process\Process;

function completionPdfText($response): string
{
    $response->assertOk()->assertHeader('Content-Type', 'application/pdf');
    $path = tempnam(sys_get_temp_dir(), 'completion-pdf-');
    try {
        file_put_contents($path, $response->getContent());
        $process = new Process(['pdftotext', '-layout', $path, '-']);
        $process->mustRun();

        return $process->getOutput();
    } finally {
        unlink($path);
    }
}

test('all report PDFs respect report access and date filters without navigation', function () {
    $doctor = Doctor::create(['nama' => 'drg. Laporan', 'status' => 'Aktif']);
    $visit = clinicVisit(clinicPatient('Included'), 'Poli Gigi', ['doctor_id' => $doctor->id, 'status_pembayaran' => 'Lunas', 'biaya_tindakan' => 120000, 'total_biaya' => 120000]);
    clinicVisit(clinicPatient('Excluded'), 'Poli Gigi', ['tanggal_kunjungan' => now()->subMonths(2)->toDateString(), 'doctor_id' => $doctor->id]);
    $treatment = Treatment::create(['kode' => 'COMPLETION', 'nama' => 'Tambal laporan', 'kategori' => 'Gigi', 'tarif' => 150000]);
    $visit->visitTreatments()->create(['treatment_id' => $treatment->id, 'biaya' => 120000]);
    $this->actingAs(User::factory()->create());
    foreach (['finance', 'treatments', 'doctors', 'charts'] as $report) {
        $this->get(route("reports.$report.pdf"))->assertRedirect(route('reports.login'));
    }
    confirmClinicReports($this);
    foreach (['finance', 'treatments', 'doctors', 'charts'] as $report) {
        $response = $this->get(route("reports.$report.pdf", ['start_date' => now()->toDateString(), 'end_date' => now()->toDateString()]));
        $response->assertHeader('Cache-Control', 'no-store, private');
        $text = completionPdfText($response);
        expect($text)->toContain('drg. Laporan')->not->toContain('Pasien Excluded', 'Navigasi petugas', 'Ruang kerja petugas', 'Logout', 'Unduh PDF');
        if (in_array($report, ['finance', 'treatments'])) {
            expect($text)->toContain('Pasien Included', '120.000');
        }
        if ($directory = getenv('CLINIC_SCREENSHOTS_DIR')) {
            file_put_contents($directory.'/'.$report.'.pdf', $response->getContent());
        }
    }
    foreach (['finance', 'treatments', 'doctors'] as $report) {
        $this->getJson(route("reports.$report.pdf", ['start_date' => '2026-03-02', 'end_date' => '2026-03-01']))->assertUnprocessable()->assertJsonValidationErrors('end_date');
        $this->get(route("reports.$report.pdf", ['start_date' => '2000-01-01', 'end_date' => '2000-01-02']))->assertOk();
    }
});

test('primary and permanent teeth coexist in visit history and PDF without altering earlier snapshots', function () {
    $patient = clinicPatient('Primary');
    $old = clinicVisit($patient, 'Poli Gigi', ['tanggal_kunjungan' => now()->subDay()->toDateString()]);
    $current = clinicVisit($patient, 'Poli Gigi');
    $this->actingAs(User::factory()->create());
    foreach ([[$old, 55, 'Karies'], [$current, 55, 'Sehat'], [$current, 16, 'Karies']] as [$visit, $number, $condition]) {
        $this->post(route('visits.odontogram.update', $visit), ['nomor_gigi' => $number, 'kondisi' => $condition])->assertRedirect()->assertSessionHasNoErrors();
    }
    expect(app(VisitOdontogram::class)->data($old)['teeth'][55]['kondisi'])->toBe('Karies');
    expect(app(VisitOdontogram::class)->data($current)['teeth'][55]['kondisi'])->toBe('Sehat');
    expect(app(VisitOdontogram::class)->data($current)['teeth'][16]['kondisi'])->toBe('Karies');
    $this->get(route('visits.edit', $current))->assertOk()->assertSee('data-tooth="55"', false)->assertSee('Gigi susu');
    $text = completionPdfText($this->get(route('visits.pdf', $current)));
    expect($text)->toContain('Gigi susu', '55', 'Sehat', '16', 'Karies');
    foreach ([50, 56, 66, 76, 86] as $invalid) {
        $this->postJson(route('visits.odontogram.update', $current), ['nomor_gigi' => $invalid, 'kondisi' => 'Sehat'])->assertUnprocessable()->assertJsonValidationErrors('nomor_gigi');
    }
});
