<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Treatment;
use App\Models\Visit;
use App\Models\VisitTreatment;
use App\Services\RegistrationNumber;
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

        // Generate next queue number
        $todayCount = Visit::whereDate('tanggal_kunjungan', now()->toDateString())->count();
        $nextQueue = 'A-'.str_pad($todayCount + 1, 3, '0', STR_PAD_LEFT);

        return view('visits.create', compact('patients', 'doctors', 'nextQueue'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'nullable|exists:doctors,id',
            'poli' => 'required|string',
            'penjamin' => 'required|string',
            'keluhan' => 'nullable|string',
        ]);

        $doctor = Doctor::find($request->doctor_id);
        $biayaKonsultasi = $doctor ? $doctor->tarif_konsultasi : 50000;

        DB::transaction(function () use ($validated, $biayaKonsultasi) {
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
                'biaya_konsultasi' => $biayaKonsultasi,
                'total_biaya' => $biayaKonsultasi,
                'status_pembayaran' => 'Belum Lunas',
            ]);

        }, 3);

        return redirect()->route('visits.index')->with('success', 'Pendaftaran kunjungan berhasil dibuat!');
    }

    public function show(Visit $visit)
    {
        $visit->load(['patient', 'doctor', 'visitTreatments.treatment']);

        return view('visits.show', compact('visit'));
    }

    public function edit(Visit $visit)
    {
        $visit->load(['patient', 'doctor', 'visitTreatments.treatment']);
        $doctors = Doctor::where('status', 'Aktif')->orderBy('nama')->get();
        $treatments = Treatment::orderBy('nama')->get();

        return view('visits.edit', compact('visit', 'doctors', 'treatments'));
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
            'biaya_konsultasi' => 'nullable|numeric',
            'biaya_obat' => 'nullable|numeric',
            'status_pembayaran' => 'required|in:Lunas,Belum Lunas',
            'metode_pembayaran' => 'nullable|string',
        ]);

        $biayaKonsultasi = $validated['biaya_konsultasi'] ?? $visit->biaya_konsultasi;
        $biayaObat = $validated['biaya_obat'] ?? 0;

        // Process added treatments if any
        $biayaTindakan = 0;
        if ($request->has('treatment_ids') && is_array($request->treatment_ids)) {
            // Remove existing treatments and replace
            $visit->visitTreatments()->delete();
            foreach ($request->treatment_ids as $treatId) {
                $treatment = Treatment::find($treatId);
                if ($treatment) {
                    VisitTreatment::create([
                        'visit_id' => $visit->id,
                        'treatment_id' => $treatment->id,
                        'biaya' => $treatment->tarif,
                        'catatan' => $treatment->nama,
                    ]);
                    $biayaTindakan += $treatment->tarif;
                }
            }
        } else {
            $biayaTindakan = $visit->visitTreatments()->sum('biaya');
        }

        $totalBiaya = $biayaKonsultasi + $biayaTindakan + $biayaObat;

        $visit->update([
            'doctor_id' => $validated['doctor_id'] ?? $visit->doctor_id,
            'status' => $validated['status'],
            'diagnosa' => $validated['diagnosa'] ?? $visit->diagnosa,
            'tensi_darah' => $validated['tensi_darah'] ?? $visit->tensi_darah,
            'suhu_tubuh' => $validated['suhu_tubuh'] ?? $visit->suhu_tubuh,
            'berat_badan' => $validated['berat_badan'] ?? $visit->berat_badan,
            'catatan_dokter' => $validated['catatan_dokter'] ?? $visit->catatan_dokter,
            'biaya_konsultasi' => $biayaKonsultasi,
            'biaya_tindakan' => $biayaTindakan,
            'biaya_obat' => $biayaObat,
            'total_biaya' => $totalBiaya,
            'status_pembayaran' => $validated['status_pembayaran'],
            'metode_pembayaran' => $validated['metode_pembayaran'] ?? $visit->metode_pembayaran,
        ]);

        return redirect()->route('visits.show', $visit)->with('success', 'Data pemeriksaan kunjungan berhasil disimpan!');
    }

    public function destroy(Visit $visit)
    {
        $visit->delete();

        return redirect()->route('visits.index')->with('success', 'Kunjungan berhasil dihapus.');
    }
}
