<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Visit;

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
        $menungguPemeriksaan = Visit::whereDate('tanggal_kunjungan', $today)->where('status', 'Menunggu')->count();
        $pendapatanHariIni = Visit::whereDate('tanggal_kunjungan', $today)->where('status_pembayaran', 'Lunas')->sum('total_biaya');

        $sedangDiperiksa = Visit::whereDate('tanggal_kunjungan', $today)->where('status', 'Sedang Diperiksa')->count();
        $selesaiHariIni = Visit::whereDate('tanggal_kunjungan', $today)->where('status', 'Selesai')->count();
        $dailyTrend = collect(range(6, 0))->map(function ($days) {
            $date = now()->subDays($days);

            return ['label' => $date->locale('id')->translatedFormat('D, d M'), 'count' => Visit::whereDate('tanggal_kunjungan', $date)->count()];
        });
        $monthlyTotal = Visit::whereYear('tanggal_kunjungan', now()->year)->whereMonth('tanggal_kunjungan', now()->month)->count();
        $previous = now()->startOfMonth()->subMonth();
        $previousTotal = Visit::whereYear('tanggal_kunjungan', $previous->year)->whereMonth('tanggal_kunjungan', $previous->month)->count();

        $recentVisits = Visit::with(['patient', 'doctor'])
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'totalPasien', 'pasienBaruBulanIni', 'pasienHariIni',
            'kunjunganHariIni', 'menungguPemeriksaan', 'pendapatanHariIni',
            'recentVisits', 'sedangDiperiksa', 'selesaiHariIni', 'dailyTrend', 'monthlyTotal', 'previousTotal'
        ));
    }
}
