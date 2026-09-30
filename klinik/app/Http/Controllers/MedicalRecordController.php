<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;

class MedicalRecordController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = Patient::withCount('visits')
            ->with(['visits' => function ($q) {
                $q->latest('tanggal_kunjungan')->limit(1);
            }])
            ->orderBy('nama');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('no_rm', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('no_hp', 'like', "%{$search}%");
            });
        }

        $patients = $query->paginate(15)->withQueryString();

        return view('medical_records.index', compact('patients', 'search'));
    }

    public function show(Patient $patient)
    {
        $patient->load([
            'visits.doctor',
            'visits.visitTreatments.treatment',
            'odontograms',
        ]);

        $visits = $patient->visits()->with(['doctor', 'visitTreatments.treatment'])->latest('tanggal_kunjungan')->get();
        $odontograms = $patient->odontograms->keyBy('nomor_gigi');

        return view('medical_records.show', compact('patient', 'visits', 'odontograms'));
    }
}
