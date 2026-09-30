<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\DashboardController;

use App\Http\Controllers\VisitController;
use App\Http\Controllers\OdontogramController;
use App\Http\Controllers\MedicalRecordController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\DoctorController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('patients', PatientController::class);

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
    Route::post('/doctors', [DoctorController::class, 'store'])->name('doctors.store');
    Route::delete('/doctors/{doctor}', [DoctorController::class, 'destroy'])->name('doctors.destroy');

    // Profile & Password
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
