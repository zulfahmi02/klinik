<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Treatment;
use App\Models\Visit;
use App\Models\VisitTreatment;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    private function downloadReport(string $view, array $data, string $filename)
    {
        return Pdf::loadView($view, $data)
            ->setPaper('a4', 'landscape')
            ->setOptions(['isRemoteEnabled' => false, 'isPhpEnabled' => false, 'isJavascriptEnabled' => false])
            ->download($filename, ['Cache-Control' => 'private, no-store']);
    }

    private function validatePeriod(Request $request, array $extra = []): void
    {
        $request->merge([
            'start_date' => $request->input('start_date') ?: now()->startOfMonth()->toDateString(),
            'end_date' => $request->input('end_date') ?: now()->toDateString(),
        ]);
        $request->validate(array_merge([
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d|after_or_equal:start_date',
        ], $extra));
    }

    public function financePdf(Request $request)
    {
        return $this->downloadReport('reports.finance-pdf', $this->finance($request)->getData(), 'laporan-finance.pdf');
    }

    public function treatmentsPdf(Request $request)
    {
        return $this->downloadReport('reports.treatments-pdf', $this->treatments($request)->getData(), 'laporan-treatments.pdf');
    }

    public function doctorsPdf(Request $request)
    {
        return $this->downloadReport('reports.doctors-pdf', $this->doctors($request)->getData(), 'laporan-doctors.pdf');
    }

    public function chartsPdf(Request $request)
    {
        return $this->downloadReport('reports.charts-pdf', $this->charts($request)->getData(), 'laporan-charts.pdf');
    }

    /**
     * Laporan Kunjungan
     */
    public function visits(Request $request)
    {
        $rules = ['search' => 'nullable|string|max:255', 'poli' => 'nullable|in:Poli Gigi',
            'start_date' => 'nullable|date_format:Y-m-d', 'end_date' => 'nullable|date_format:Y-m-d',
            'doctor_id' => 'nullable|integer|exists:doctors,id', 'penjamin' => 'nullable|in:Umum,BPJS,Asuransi Swasta',
            'status' => 'nullable|in:Menunggu,Sedang Diperiksa,Selesai,Batal'];
        if ($request->filled('start_date')) {
            $rules['end_date'] .= '|after_or_equal:start_date';
        }
        $request->validate($rules);
        $search = trim((string) $request->input('search', ''));
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $poli = $request->input('poli');
        $doctorId = $request->input('doctor_id');
        $penjamin = $request->input('penjamin');
        $status = $request->input('status');

        $query = Visit::with(['patient', 'doctor', 'visitTreatments'])
            ->when($startDate, fn ($q) => $q->whereDate('tanggal_kunjungan', '>=', $startDate))
            ->when($endDate, fn ($q) => $q->whereDate('tanggal_kunjungan', '<=', $endDate))
            ->orderBy('tanggal_kunjungan', 'desc')->orderByDesc('id');

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
                    ->orWhere('keluhan', 'like', $term)->orWhere('diagnosa', 'like', $term);
            });
        }

        $allVisits = $query->get();
        $visits = $request->routeIs('reports.visits.pdf') ? $allVisits : $query->paginate(15)->withQueryString();

        // Summary stats cover all matching records, not just the displayed page.
        $totalVisits = $allVisits->count();
        $totalSelesai = $allVisits->where('status', 'Selesai')->count();
        $totalBpjs = $allVisits->where('penjamin', 'BPJS')->count();
        $totalUmum = $allVisits->where('penjamin', 'Umum')->count();

        $doctors = Doctor::orderBy('nama')->get();

        return view('reports.visits', compact(
            'visits', 'startDate', 'endDate', 'poli', 'doctorId', 'penjamin', 'status',
            'totalVisits', 'totalSelesai', 'totalBpjs', 'totalUmum', 'doctors', 'search'
        ));
    }

    public function visitsPdf(Request $request)
    {
        $data = $this->visits($request)->getData();

        return Pdf::loadView('reports.visits-pdf', $data)
            ->setPaper('a4', 'landscape')
            ->setOptions(['isRemoteEnabled' => false, 'isPhpEnabled' => false, 'isJavascriptEnabled' => false])
            ->download('rekap-kunjungan-klinik.pdf');
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
            'penjaminVisits', 'doctorVisits',
            'dailyLabels', 'dailyTrend'
        ));
    }

    /**
     * Laporan Keuangan
     */
    public function finance(Request $request)
    {
        $this->validatePeriod($request, ['status_pembayaran' => 'nullable|in:Lunas,Belum Lunas', 'metode_pembayaran' => 'nullable|in:Tunai,QRIS,Transfer,BPJS']);
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());
        $statusPembayaran = $request->input('status_pembayaran');
        $metodePembayaran = $request->input('metode_pembayaran');

        $query = Visit::with(['patient', 'doctor'])
            ->whereDate('tanggal_kunjungan', '>=', $startDate)->whereDate('tanggal_kunjungan', '<=', $endDate)
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
        $totalTindakan = $visits->where('status_pembayaran', 'Lunas')->sum('biaya_tindakan');
        $totalObat = $visits->where('status_pembayaran', 'Lunas')->sum('biaya_obat');
        $totalPiutang = $visits->where('status_pembayaran', 'Belum Lunas')->sum('total_biaya');

        return view('reports.finance', compact(
            'visits', 'startDate', 'endDate', 'statusPembayaran', 'metodePembayaran',
            'totalPendapatan', 'totalTindakan', 'totalObat', 'totalPiutang'
        ));
    }

    /**
     * Laporan Tindakan Medis
     */
    public function treatments(Request $request)
    {
        $this->validatePeriod($request, ['kategori' => 'nullable|string|max:255']);
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());
        $kategori = $request->input('kategori');

        $query = VisitTreatment::with(['treatment', 'visit.patient', 'visit.doctor'])
            ->whereHas('visit', function ($q) use ($startDate, $endDate) {
                $q->whereDate('tanggal_kunjungan', '>=', $startDate)->whereDate('tanggal_kunjungan', '<=', $endDate);
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
        $this->validatePeriod($request, []);
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());

        $doctors = Doctor::with(['visits' => function ($q) use ($startDate, $endDate) {
            $q->whereDate('tanggal_kunjungan', '>=', $startDate)->whereDate('tanggal_kunjungan', '<=', $endDate);
        }, 'visits.visitTreatments'])->get();

        $doctorStats = $doctors->map(function ($doctor) {
            $visits = $doctor->visits;
            $totalPasien = $visits->count();
            $pasienSelesai = $visits->where('status', 'Selesai')->count();

            $totalTindakanCount = 0;
            $totalBiayaTindakan = 0;
            foreach ($visits as $v) {
                $totalTindakanCount += $v->visitTreatments->count();
                $totalBiayaTindakan += $v->visitTreatments->sum('biaya');
            }

            $totalPendapatan = $totalBiayaTindakan + $visits->sum('biaya_obat');

            return [
                'id' => $doctor->id,
                'nama' => $doctor->nama,
                'spesialisasi' => $doctor->spesialisasi,
                'no_sip' => $doctor->no_sip,
                'total_pasien' => $totalPasien,
                'pasien_selesai' => $pasienSelesai,
                'total_tindakan' => $totalTindakanCount,
                'total_tindakan_rp' => $totalBiayaTindakan,
                'total_pendapatan' => $totalPendapatan,
            ];
        });

        return view('reports.doctors', compact('doctorStats', 'startDate', 'endDate'));
    }
}
