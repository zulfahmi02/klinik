<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\MedicalRecordController;
use App\Http\Controllers\OdontogramController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicRegistrationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\VisitController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicRegistrationController::class, 'create'])->name('registration.create');
Route::post('/pendaftaran', [PublicRegistrationController::class, 'store'])->middleware(['throttle:10,1'])->block(10, 10)->name('registration.store');
Route::get('/pendaftaran/berhasil', [PublicRegistrationController::class, 'success'])->name('registration.success');
Route::redirect('/dashboard', '/laporan');

Route::prefix('laporan')->middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('patients', PatientController::class)->except(['create', 'store']);

    // Kunjungan Pasien
    Route::resource('visits', VisitController::class);

    // Odontogram Gigi
    Route::get('/odontogram', [OdontogramController::class, 'index'])->name('odontogram.index');
    Route::get('/odontogram/{patient}', [OdontogramController::class, 'show'])->name('odontogram.show');
    Route::post('/odontogram/{patient}/tooth', [OdontogramController::class, 'updateTooth'])->name('odontogram.update_tooth');

    // Riwayat Pasien (Rekam Medis)
    Route::get('/medical-records', [MedicalRecordController::class, 'index'])->name('medical_records.index');
    Route::get('/medical-records/{patient}', [MedicalRecordController::class, 'show'])->name('medical_records.show');

    // Laporan & Grafik
    Route::get('/reports/visits', [ReportController::class, 'visits'])->name('reports.visits');
    Route::get('/reports/charts', [ReportController::class, 'charts'])->name('reports.charts');
    Route::get('/reports/finance', [ReportController::class, 'finance'])->name('reports.finance');
    Route::get('/reports/treatments', [ReportController::class, 'treatments'])->name('reports.treatments');
    Route::get('/reports/doctors', [ReportController::class, 'doctors'])->name('reports.doctors');
    Route::resource('doctors', DoctorController::class)->only(['index', 'create', 'show', 'store', 'destroy']);

    // Profile & Password
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
