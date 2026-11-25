<?php

use App\Http\Controllers\Auth\ReportSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\MedicalRecordController;
use App\Http\Controllers\OdontogramController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicRegistrationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\VisitController;
use App\Http\Controllers\VisitOdontogramController;
use App\Http\Middleware\LeaveReportArea;
use App\Http\Middleware\RequireReportAuthentication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicRegistrationController::class, 'create'])->name('registration.create');
Route::post('/pendaftaran', [PublicRegistrationController::class, 'store'])->middleware(['throttle:10,1'])->block(10, 10)->name('registration.store');
Route::get('/pendaftaran/berhasil', [PublicRegistrationController::class, 'success'])->name('registration.success');
Route::redirect('/dashboard', '/pemeriksaan');
Route::get('/laporan/login', [ReportSessionController::class, 'create'])->name('reports.login');
Route::post('/laporan/login', [ReportSessionController::class, 'store'])->name('reports.login.store');
Route::get('/laporan/masuk', [ReportSessionController::class, 'entry'])->name('reports.entry');

Route::prefix('pemeriksaan')->middleware(['auth', LeaveReportArea::class])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('patients', PatientController::class)->except(['create', 'store']);

    // Kunjungan Pasien
    Route::get('visits/pdf', [VisitController::class, 'index'])->name('visits.list.pdf');
    Route::get('visits/{visit}/pdf', [VisitController::class, 'pdf'])->name('visits.pdf');
    Route::get('visits/{visit}/print', [VisitController::class, 'print'])->name('visits.print');
    Route::resource('visits', VisitController::class);

    Route::get('visits/{visit}/odontogram', [VisitOdontogramController::class, 'show'])->name('visits.odontogram.show');
    Route::post('visits/{visit}/odontogram', [VisitOdontogramController::class, 'update'])->name('visits.odontogram.update');

    // Odontogram Gigi
    Route::get('/odontogram', [OdontogramController::class, 'index'])->name('odontogram.index');
    Route::get('/odontogram/{patient}', [OdontogramController::class, 'show'])->name('odontogram.show');
    Route::post('/odontogram/{patient}/tooth', [OdontogramController::class, 'updateTooth'])->name('odontogram.update_tooth');

    // Riwayat Pasien (Rekam Medis)
    Route::get('/medical-records', [MedicalRecordController::class, 'index'])->name('medical_records.index');
    Route::get('/medical-records/{patient}', [MedicalRecordController::class, 'show'])->name('medical_records.show');

    Route::resource('doctors', DoctorController::class)->only(['index', 'create', 'show', 'store', 'edit', 'update', 'destroy']);

    // Profile & Password
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::prefix('laporan')->middleware(RequireReportAuthentication::class)->group(function () {
    Route::get('/', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/kunjungan/pdf', [ReportController::class, 'visitsPdf'])->name('reports.visits.pdf');
    Route::get('/kunjungan', [ReportController::class, 'visits'])->name('reports.visits');
    Route::get('/keuangan/pdf', [ReportController::class, 'financePdf'])->name('reports.finance.pdf');
    Route::get('/tindakan/pdf', [ReportController::class, 'treatmentsPdf'])->name('reports.treatments.pdf');
    Route::get('/dokter/pdf', [ReportController::class, 'doctorsPdf'])->name('reports.doctors.pdf');
    Route::get('/grafik/pdf', [ReportController::class, 'chartsPdf'])->name('reports.charts.pdf');
    Route::get('/grafik', [ReportController::class, 'charts'])->name('reports.charts');
    Route::get('/keuangan', [ReportController::class, 'finance'])->name('reports.finance');
    Route::get('/tindakan', [ReportController::class, 'treatments'])->name('reports.treatments');
    Route::get('/dokter', [ReportController::class, 'doctors'])->name('reports.doctors');
    Route::get('/riwayat/{patient}', [MedicalRecordController::class, 'show'])->name('reports.history.show');
    Route::get('/pemeriksaan/{visit}/pdf', [VisitController::class, 'pdf'])->name('reports.examinations.pdf');
    Route::get('/pemeriksaan/{visit}/cetak', [VisitController::class, 'print'])->name('reports.examinations.print');
    Route::get('/pemeriksaan/{visit}', [VisitController::class, 'show'])->name('reports.examinations.show');
    Route::get('/pemeriksaan/{visit}/odontogram', [VisitOdontogramController::class, 'show'])->name('reports.odontogram.show');
});

// Explicit legacy report URLs only; no prefix-wide redirect or write-route alias.
foreach (['' => '', '/visits' => '/kunjungan', '/visits/pdf' => '/kunjungan/pdf', '/charts' => '/grafik', '/finance' => '/keuangan', '/treatments' => '/tindakan', '/doctors' => '/dokter'] as $old => $new) {
    Route::get('/laporan/reports'.$old, function (Request $request) use ($new) {
        $query = $request->getQueryString();

        return redirect('/laporan'.$new.($query ? '?'.$query : ''));
    })->middleware(RequireReportAuthentication::class);
}

require __DIR__.'/auth.php';
