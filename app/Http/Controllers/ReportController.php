<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Treatment;
use App\Models\Visit;
use App\Models\VisitTreatment;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Laporan Kunjungan
     */
    public function visits(Request $request)
    {
        $request->validate(['search' => 'nullable|string|max:255']);
        $search = trim((string) $request->input('search', ''));
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());
        $poli = $request->input('poli');
        $doctorId = $request->input('doctor_id');
        $penjamin = $request->input('penjamin');
        $status = $request->input('status');

        $query = Visit::with(['patient', 'doctor', 'visitTreatments'])
            ->whereDate('tanggal_kunjungan', '>=', $startDate)
            ->whereDate('tanggal_kunjungan', '<=', $endDate)
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

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $term = "%{$search}%";
                $q->whereHas('patient', function ($patient) use ($term) {
                    $patient->where('nama', 'like', $term)->orWhere('no_rm', 'like', $term)
                        ->orWhere('nik', 'like', $term)->orWhere('no_hp', 'like', $term);
                })->orWhereHas('doctor', fn ($doctor) => $doctor->where('nama', 'like', $term))
                    ->orWhere('no_antrean', 'like', $term)
                    ->orWhere('keluhan', 'like', $term)->orWhere('diagnosa', 'like', $term);
            });
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
            'totalVisits', 'totalSelesai', 'totalBpjs', 'totalUmum', 'doctors', 'search'
        ));
    }

    /**
     * Grafik Kunjungan
     */
    public function charts(Request $request)
    {
        $validated = $request->validate([
            'year' => ['nullable', 'integer', 'min:2000', 'max:'.now()->year],
        ]);
        $year = (int) ($validated['year'] ?? now()->year);
        $start = CarbonImmutable::create($year, 1, 1)->startOfDay();
        $yearQuery = Visit::where('tanggal_kunjungan', '>=', $start->toDateString())
            ->where('tanggal_kunjungan', '<', $start->addYear()->toDateString());

        // Conditional aggregates work on both MySQL and SQLite.
        $monthlyQuery = clone $yearQuery;
        for ($m = 1; $m <= 12; $m++) {
            $monthStart = $start->addMonths($m - 1);
            $monthlyQuery->selectRaw(
                "SUM(CASE WHEN tanggal_kunjungan >= ? AND tanggal_kunjungan < ? THEN 1 ELSE 0 END) AS month_{$m}",
                [$monthStart->toDateString(), $monthStart->addMonth()->toDateString()]
            );
        }
        $totals = $monthlyQuery->first();
        $monthsLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $monthlyData = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthlyData[] = (int) $totals->{'month_'.$m};
        }

        $poliVisits = (clone $yearQuery)->select('poli', DB::raw('count(*) as count'))
            ->groupBy('poli')->orderBy('poli')->pluck('count', 'poli')->toArray();
        $penjaminVisits = (clone $yearQuery)->select('penjamin', DB::raw('count(*) as count'))
            ->groupBy('penjamin')->orderBy('penjamin')->pluck('count', 'penjamin')->toArray();
        $doctorVisits = (clone $yearQuery)->with('doctor')
            ->select('doctor_id', DB::raw('count(*) as count'))->groupBy('doctor_id')->orderBy('doctor_id')->get()
            ->mapWithKeys(function ($item) {
                $label = $item->doctor ? $item->doctor->nama.' (#'.$item->doctor_id.')' : 'Belum ditentukan';

                return [$label => (int) $item->count];
            })->toArray();

        // The operational daily chart always shows the current seven days, independently of the annual filter.
        $today = CarbonImmutable::today();
        $dailyCounts = Visit::where('tanggal_kunjungan', '>=', $today->subDays(6)->toDateString())
            ->where('tanggal_kunjungan', '<', $today->addDay()->toDateString())
            ->selectRaw('DATE(tanggal_kunjungan) as day, COUNT(*) as count')
            ->groupBy('day')->pluck('count', 'day');
        $dailyTrend = [];
        $dailyLabels = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = $today->subDays($i);
            $dailyLabels[] = $date->format('d M');
            $dailyTrend[] = (int) ($dailyCounts[$date->toDateString()] ?? 0);
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
