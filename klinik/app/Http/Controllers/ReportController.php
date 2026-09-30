<?php

namespace App\Http\Controllers;

use App\Models\Visit;
use App\Models\Doctor;
use App\Models\Treatment;
use App\Models\VisitTreatment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Laporan Kunjungan
     */
    public function visits(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());
        $poli = $request->input('poli');
        $doctorId = $request->input('doctor_id');
        $penjamin = $request->input('penjamin');
        $status = $request->input('status');

        $query = Visit::with(['patient', 'doctor', 'visitTreatments'])
            ->whereBetween('tanggal_kunjungan', [$startDate, $endDate])
            ->orderBy('tanggal_kunjungan', 'desc');

        if ($poli) {
            $query->where('poli', $poli);
        }
        if ($doctorId) {
            $query->where('doctor_id', $doctorId);
        }
        if ($penjamin) {
            $query->where('penjamin', $penjamin);
        }
        if ($status) {
            $query->where('status', $status);
        }

        $visits = $query->get();

        // Summary stats
        $totalVisits = $visits->count();
        $totalSelesai = $visits->where('status', 'Selesai')->count();
        $totalBpjs = $visits->where('penjamin', 'BPJS')->count();
        $totalUmum = $visits->where('penjamin', 'Umum')->count();

        $doctors = Doctor::orderBy('nama')->get();

        return view('reports.visits', compact(
            'visits', 'startDate', 'endDate', 'poli', 'doctorId', 'penjamin', 'status',
            'totalVisits', 'totalSelesai', 'totalBpjs', 'totalUmum', 'doctors'
        ));
    }

    /**
     * Grafik Kunjungan
     */
    public function charts(Request $request)
    {
        $year = $request->input('year', now()->year);

        // 1. Monthly visits trend for the year
        $monthlyVisits = Visit::selectRaw("strftime('%m', tanggal_kunjungan) as month, count(*) as count")
            ->whereYear('tanggal_kunjungan', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();

        $monthsLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $monthlyData = [];
        for ($m = 1; $m <= 12; $m++) {
            $key = str_pad($m, 2, '0', STR_PAD_LEFT);
            $monthlyData[] = $monthlyVisits[$key] ?? 0;
        }

        // 2. Visits by Polyclinic
        $poliVisits = Visit::select('poli', DB::raw('count(*) as count'))
            ->groupBy('poli')
            ->pluck('count', 'poli')
            ->toArray();

        // 3. Visits by Penjamin (BPJS vs Umum vs Asuransi)
        $penjaminVisits = Visit::select('penjamin', DB::raw('count(*) as count'))
            ->groupBy('penjamin')
            ->pluck('count', 'penjamin')
            ->toArray();

        // 4. Visits by Doctor
        $doctorVisits = Visit::with('doctor')
            ->select('doctor_id', DB::raw('count(*) as count'))
            ->whereNotNull('doctor_id')
            ->groupBy('doctor_id')
            ->get()
            ->mapWithKeys(function ($item) {
                $docName = $item->doctor ? $item->doctor->nama : 'Lainnya';
                return [$docName => $item->count];
            })
            ->toArray();

        // Recent 7 days trend
        $dailyTrend = [];
        $dailyLabels = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $dailyLabels[] = now()->subDays($i)->format('d M');
            $dailyTrend[] = Visit::whereDate('tanggal_kunjungan', $date)->count();
        }

        return view('reports.charts', compact(
            'year', 'monthsLabels', 'monthlyData',
            'poliVisits', 'penjaminVisits', 'doctorVisits',
            'dailyLabels', 'dailyTrend'
        ));
    }

    /**
     * Laporan Keuangan
     */
    public function finance(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());
        $statusPembayaran = $request->input('status_pembayaran');
        $metodePembayaran = $request->input('metode_pembayaran');

        $query = Visit::with(['patient', 'doctor'])
            ->whereBetween('tanggal_kunjungan', [$startDate, $endDate])
            ->orderBy('tanggal_kunjungan', 'desc');

        if ($statusPembayaran) {
            $query->where('status_pembayaran', $statusPembayaran);
        }
        if ($metodePembayaran) {
            $query->where('metode_pembayaran', $metodePembayaran);
        }

        $visits = $query->get();

        // Totals
        $totalPendapatan = $visits->where('status_pembayaran', 'Lunas')->sum('total_biaya');
        $totalKonsultasi = $visits->where('status_pembayaran', 'Lunas')->sum('biaya_konsultasi');
        $totalTindakan = $visits->where('status_pembayaran', 'Lunas')->sum('biaya_tindakan');
        $totalObat = $visits->where('status_pembayaran', 'Lunas')->sum('biaya_obat');
        $totalPiutang = $visits->where('status_pembayaran', 'Belum Lunas')->sum('total_biaya');

        return view('reports.finance', compact(
            'visits', 'startDate', 'endDate', 'statusPembayaran', 'metodePembayaran',
            'totalPendapatan', 'totalKonsultasi', 'totalTindakan', 'totalObat', 'totalPiutang'
        ));
    }

    /**
     * Laporan Tindakan Medis
     */
    public function treatments(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());
        $kategori = $request->input('kategori');

        $query = VisitTreatment::with(['treatment', 'visit.patient', 'visit.doctor'])
            ->whereHas('visit', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('tanggal_kunjungan', [$startDate, $endDate]);
            });

        if ($kategori) {
            $query->whereHas('treatment', function ($q) use ($kategori) {
                $q->where('kategori', $kategori);
            });
        }

        $records = $query->get();

        // Aggregated summary per treatment
        $treatmentSummary = $records->groupBy('treatment_id')->map(function ($group) {
            $first = $group->first();
            return [
                'nama' => $first->treatment ? $first->treatment->nama : 'N/A',
                'kode' => $first->treatment ? $first->treatment->kode : 'N/A',
                'kategori' => $first->treatment ? $first->treatment->kategori : 'N/A',
                'tarif' => $first->treatment ? $first->treatment->tarif : 0,
                'jumlah' => $group->count(),
                'total_biaya' => $group->sum('biaya'),
            ];
        })->sortByDesc('total_biaya');

        $categories = Treatment::select('kategori')->distinct()->pluck('kategori');

        return view('reports.treatments', compact(
            'records', 'treatmentSummary', 'startDate', 'endDate', 'kategori', 'categories'
        ));
    }

    /**
     * Laporan Kinerja Dokter
     */
    public function doctors(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());

        $doctors = Doctor::with(['visits' => function ($q) use ($startDate, $endDate) {
            $q->whereBetween('tanggal_kunjungan', [$startDate, $endDate]);
        }, 'visits.visitTreatments'])->get();

        $doctorStats = $doctors->map(function ($doctor) {
            $visits = $doctor->visits;
            $totalPasien = $visits->count();
            $pasienSelesai = $visits->where('status', 'Selesai')->count();
            $totalKonsultasi = $visits->sum('biaya_konsultasi');
            
            $totalTindakanCount = 0;
            $totalBiayaTindakan = 0;
            foreach ($visits as $v) {
                $totalTindakanCount += $v->visitTreatments->count();
                $totalBiayaTindakan += $v->visitTreatments->sum('biaya');
            }

            $totalPendapatan = $totalKonsultasi + $totalBiayaTindakan;

            return [
                'id' => $doctor->id,
                'nama' => $doctor->nama,
                'spesialisasi' => $doctor->spesialisasi,
                'no_sip' => $doctor->no_sip,
                'total_pasien' => $totalPasien,
                'pasien_selesai' => $pasienSelesai,
                'total_tindakan' => $totalTindakanCount,
                'total_konsultasi' => $totalKonsultasi,
                'total_tindakan_rp' => $totalBiayaTindakan,
                'total_pendapatan' => $totalPendapatan,
            ];
        });

        return view('reports.doctors', compact('doctorStats', 'startDate', 'endDate'));
    }
}
