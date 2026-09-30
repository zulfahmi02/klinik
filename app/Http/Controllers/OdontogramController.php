<?php

namespace App\Http\Controllers;

use App\Models\Odontogram;
use App\Models\Patient;
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
        abort_unless($patient->dentalVisits()->exists(), 404);
        $odontograms = $patient->odontograms->keyBy('nomor_gigi');

        // FDI 32 adult teeth numbering
        $upperRight = [18, 17, 16, 15, 14, 13, 12, 11];
        $upperLeft = [21, 22, 23, 24, 25, 26, 27, 28];
        $lowerRight = [48, 47, 46, 45, 44, 43, 42, 41];
        $lowerLeft = [31, 32, 33, 34, 35, 36, 37, 38];

        $conditions = [
            'Sehat' => ['label' => 'Sehat / Normal', 'color' => '#10b981', 'badge' => 'success'],
            'Karies' => ['label' => 'Karies (Berlubang)', 'color' => '#ef4444', 'badge' => 'danger'],
            'Tambalan Komposit' => ['label' => 'Tambalan Komposit', 'color' => '#2563eb', 'badge' => 'primary'],
            'Tambalan Sementara' => ['label' => 'Tambalan Sementara', 'color' => '#f59e0b', 'badge' => 'warning'],
            'Hilang' => ['label' => 'Hilang / Missing', 'color' => '#6b7280', 'badge' => 'secondary'],
            'Sisa Akar' => ['label' => 'Sisa Akar (Radix)', 'color' => '#78350f', 'badge' => 'dark'],
            'Mahkota' => ['label' => 'Mahkota / Crown', 'color' => '#d97706', 'badge' => 'warning'],
            'Gigi Tiruan' => ['label' => 'Gigi Tiruan', 'color' => '#8b5cf6', 'badge' => 'info'],
            'Impaksi' => ['label' => 'Gigi Impaksi', 'color' => '#ec4899', 'badge' => 'danger'],
        ];

        return view('odontogram.show', compact(
            'patient', 'odontograms',
            'upperRight', 'upperLeft', 'lowerRight', 'lowerLeft',
            'conditions'
        ));
    }

    public function updateTooth(Request $request, Patient $patient)
    {
        abort_unless($patient->dentalVisits()->exists(), 404);
        $validated = $request->validate([
            'nomor_gigi' => 'required|integer',
            'kondisi' => 'required|string',
            'tindakan' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        if ($validated['kondisi'] === 'Sehat') {
            Odontogram::where('patient_id', $patient->id)
                ->where('nomor_gigi', $validated['nomor_gigi'])
                ->delete();
        } else {
            Odontogram::updateOrCreate(
                [
                    'patient_id' => $patient->id,
                    'nomor_gigi' => $validated['nomor_gigi'],
                ],
                [
                    'kondisi' => $validated['kondisi'],
                    'tindakan' => $validated['tindakan'] ?? null,
                    'catatan' => $validated['catatan'] ?? null,
                ]
            );
        }

        return redirect()->route('odontogram.show', $patient)
            ->with('success', "Kondisi gigi nomor {$validated['nomor_gigi']} berhasil diperbarui!");
    }
}
