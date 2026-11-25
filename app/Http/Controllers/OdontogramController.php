<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Services\VisitOdontogram;
use Illuminate\Http\Request;

class OdontogramController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = Patient::whereHas('dentalVisits')->withCount('odontograms')->orderBy('nama');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('no_rm', 'like', "%{$search}%")
                    ->orWhere('no_hp', 'like', "%{$search}%");
            });
        }

        $patients = $query->paginate(12)->withQueryString();

        return view('odontogram.index', compact('patients', 'search'));
    }

    public function show(Patient $patient)
    {
        $visit = $patient->dentalVisits()->orderByDesc('tanggal_kunjungan')->orderByDesc('id')->firstOrFail();

        return redirect()->route('visits.odontogram.show', $visit);
    }

    public function updateTooth(Request $request, Patient $patient)
    {
        $visit = $patient->dentalVisits()->orderByDesc('tanggal_kunjungan')->orderByDesc('id')->firstOrFail();

        // Keep old bookmarks/forms working without bypassing visit snapshots.
        return app(VisitOdontogramController::class)->update($request, $visit, app(VisitOdontogram::class));
    }
}
