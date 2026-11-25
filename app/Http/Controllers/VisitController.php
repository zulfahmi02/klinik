<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Treatment;
use App\Models\Visit;
use App\Services\RegistrationNumber;
use App\Services\VisitOdontogram;
use Dompdf\Dompdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VisitController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->input('tanggal', now()->toDateString());
        $status = $request->input('status');
        $search = $request->input('search');

        $query = Visit::with(['patient', 'doctor', 'visitTreatments'])
            ->orderBy('created_at', 'desc');

        if ($date) {
            $query->whereDate('tanggal_kunjungan', $date);
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->whereHas('patient', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('no_rm', 'like', "%{$search}%");
            });
        }

        if ($request->routeIs('visits.list.pdf')) {
            $visits = $query->get();
            $renderer = new Dompdf(['isRemoteEnabled' => false, 'isPhpEnabled' => false]);
            $renderer->loadHtml(view('visits.list-pdf', compact('visits', 'date', 'status', 'search'))->render());
            $renderer->setPaper('A4', 'landscape');
            $renderer->render();

            return response($renderer->output(), 200, ['Content-Type' => 'application/pdf', 'Content-Disposition' => 'attachment; filename="daftar-kunjungan.pdf"', 'Cache-Control' => 'no-store, private']);
        }

        $visits = $query->paginate(15)->withQueryString();

        // Summary counts for today
        $today = now()->toDateString();
        $totalHariIni = Visit::whereDate('tanggal_kunjungan', $today)->count();
        $menungguCount = Visit::whereDate('tanggal_kunjungan', $today)->where('status', 'Menunggu')->count();
        $diperiksaCount = Visit::whereDate('tanggal_kunjungan', $today)->where('status', 'Sedang Diperiksa')->count();
        $selesaiCount = Visit::whereDate('tanggal_kunjungan', $today)->where('status', 'Selesai')->count();

        return view('visits.index', compact(
            'visits', 'date', 'status', 'search',
            'totalHariIni', 'menungguCount', 'diperiksaCount', 'selesaiCount'
        ));
    }

    public function create()
    {
        $patients = Patient::orderBy('nama')->get();
        $doctors = Doctor::where('status', 'Aktif')->orderBy('nama')->get();

        return view('visits.create', compact('patients', 'doctors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'nullable|exists:doctors,id',
            'poli' => 'required|in:Poli Gigi',
            'penjamin' => 'required|string',
            'keluhan' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated) {
            $date = now('Asia/Jakarta');
            Visit::create([
                'no_antrean' => app(RegistrationNumber::class)->next('visit', $date->toDateString()),
                'patient_id' => $validated['patient_id'],
                'doctor_id' => $validated['doctor_id'] ?? null,
                'poli' => $validated['poli'],
                'tanggal_kunjungan' => $date->toDateString(),
                'jam_kunjungan' => $date->format('H:i'),
                'penjamin' => $validated['penjamin'],
                'keluhan' => $validated['keluhan'] ?? null,
                'status' => 'Menunggu',
                'biaya_konsultasi' => 0,
                'total_biaya' => 0,
                'status_pembayaran' => 'Belum Lunas',
            ]);

        }, 3);

        return redirect()->route('visits.index')->with('success', 'Pendaftaran kunjungan berhasil dibuat!');
    }

    public function show(Visit $visit)
    {
        $visit->load(['patient', 'doctor', 'visitTreatments.treatment']);

        return view('visits.show', ['visit' => $visit, ...app(VisitOdontogram::class)->data($visit)]);
    }

    public function pdf(Visit $visit)
    {
        $visit->load(['patient', 'doctor', 'visitTreatments.treatment']);
        $renderer = new Dompdf([
            'isRemoteEnabled' => false,
            'isPhpEnabled' => false,
            'isJavascriptEnabled' => false,
            'chroot' => resource_path('views'),
        ]);
        $renderer->loadHtml(view('visits.print', [
            'visit' => $visit,
            'pdf' => true,
            ...app(VisitOdontogram::class)->data($visit),
        ])->render());
        $renderer->setPaper('A4');
        $renderer->render();

        return response($renderer->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="kunjungan-'.$visit->id.'.pdf"',
            'Cache-Control' => 'no-store, private',
        ]);
    }

    public function print(Visit $visit)
    {
        $visit->load(['patient', 'doctor', 'visitTreatments.treatment']);

        return response()->view('visits.print', ['visit' => $visit, ...app(VisitOdontogram::class)->data($visit)])
            ->header('Cache-Control', 'no-store, private');
    }

    public function edit(Visit $visit)
    {
        $visit->load(['patient', 'doctor', 'visitTreatments.treatment']);
        $doctors = Doctor::where('status', 'Aktif')->orderBy('nama')->get();
        $treatments = Treatment::orderBy('nama')->get();

        return view('visits.edit', [...compact('visit', 'doctors', 'treatments'), ...app(VisitOdontogram::class)->data($visit)]);
    }

    public function update(Request $request, Visit $visit)
    {
        $validated = $request->validate([
            'doctor_id' => 'nullable|exists:doctors,id',
            'status' => 'required|in:Menunggu,Sedang Diperiksa,Selesai,Batal',
            'diagnosa' => 'nullable|string',
            'tensi_darah' => 'nullable|string',
            'suhu_tubuh' => 'nullable|string',
            'berat_badan' => 'nullable|string',
            'catatan_dokter' => 'nullable|string',
            'biaya_obat' => 'nullable|integer|min:0',
            'treatments_submitted' => 'sometimes|boolean',
            'treatment_ids' => 'sometimes|array',
            'treatment_ids.*' => 'integer|distinct|exists:treatments,id',
            'status_pembayaran' => 'required|in:Lunas,Belum Lunas',
            'metode_pembayaran' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated, $visit) {
            $visit = Visit::whereKey($visit->id)->lockForUpdate()->firstOrFail();
            $biayaObat = $validated['biaya_obat'] ?? $visit->biaya_obat;

            if (array_key_exists('treatment_ids', $validated) || ($validated['treatments_submitted'] ?? false)) {
                $ids = $validated['treatment_ids'] ?? [];
                $visit->visitTreatments()->whereNotIn('treatment_id', $ids)->delete();
                $retained = $visit->visitTreatments()->pluck('treatment_id')->all();
                foreach (Treatment::whereIn('id', array_diff($ids, $retained))->get() as $treatment) {
                    $visit->visitTreatments()->create([
                        'treatment_id' => $treatment->id,
                        'biaya' => $treatment->tarif,
                        'catatan' => $treatment->nama,
                    ]);
                }
            }
            $biayaTindakan = $visit->visitTreatments()->sum('biaya');

            $totalBiaya = $biayaTindakan + $biayaObat;

            $visit->update([
                'doctor_id' => $validated['doctor_id'] ?? $visit->doctor_id,
                'status' => $validated['status'],
                'diagnosa' => $validated['diagnosa'] ?? $visit->diagnosa,
                'tensi_darah' => $validated['tensi_darah'] ?? $visit->tensi_darah,
                'suhu_tubuh' => $validated['suhu_tubuh'] ?? $visit->suhu_tubuh,
                'berat_badan' => $validated['berat_badan'] ?? $visit->berat_badan,
                'catatan_dokter' => $validated['catatan_dokter'] ?? $visit->catatan_dokter,
                'biaya_konsultasi' => 0,
                'biaya_tindakan' => $biayaTindakan,
                'biaya_obat' => $biayaObat,
                'total_biaya' => $totalBiaya,
                'status_pembayaran' => $validated['status_pembayaran'],
                'metode_pembayaran' => $validated['metode_pembayaran'] ?? $visit->metode_pembayaran,
            ]);

        });

        return redirect()->route('visits.show', $visit)->with('success', 'Data pemeriksaan kunjungan berhasil disimpan!');
    }

    public function destroy(Visit $visit)
    {
        $visit->delete();

        return redirect()->route('visits.index')->with('success', 'Kunjungan berhasil dihapus.');
    }
}
