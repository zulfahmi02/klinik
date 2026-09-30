<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Visit;
use App\Models\Doctor;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();
        $totalPasien = Patient::count();
        $pasienBaruBulanIni = Patient::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $pasienHariIni = Patient::whereDate('created_at', today())->count();

        $kunjunganHariIni = Visit::whereDate('tanggal_kunjungan', $today)->count();
        $antreanMenunggu = Visit::whereDate('tanggal_kunjungan', $today)->where('status', 'Menunggu')->count();
        $pendapatanHariIni = Visit::whereDate('tanggal_kunjungan', $today)->where('status_pembayaran', 'Lunas')->sum('total_biaya');

        $recentVisits = Visit::with(['patient', 'doctor'])
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'totalPasien', 'pasienBaruBulanIni', 'pasienHariIni',
            'kunjunganHariIni', 'antreanMenunggu', 'pendapatanHariIni',
            'recentVisits'
        ));
    }
}