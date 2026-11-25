<?php

namespace App\Http\Controllers;

use App\Models\Odontogram;
use App\Models\Patient;
use App\Models\Visit;
use App\Services\VisitOdontogram;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class VisitOdontogramController extends Controller
{
    public function show(Visit $visit, VisitOdontogram $odontogram)
    {
        abort_unless($visit->poli === 'Poli Gigi', 404);

        return view('visits.odontogram', ['visit' => $visit, ...$odontogram->data($visit)]);
    }

    public function update(Request $request, Visit $visit, VisitOdontogram $odontogram)
    {
        abort_unless($visit->poli === 'Poli Gigi' && $visit->status !== 'Batal', 404);
        abort_unless(Schema::hasTable('visit_odontogram_snapshots'), 503, 'Penyimpanan odontogram kunjungan belum tersedia.');
        $data = $request->validate([
            'nomor_gigi' => ['required', 'integer', Rule::in(VisitOdontogram::allTeeth())],
            'kondisi' => ['required', Rule::in(VisitOdontogram::CONDITIONS)],
            'tindakan' => ['nullable', 'string', 'max:255'],
            'catatan' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($visit, $data, $odontogram) {
            // Serialize edits for this patient and retain every other tooth in the snapshot.
            Patient::whereKey($visit->patient_id)->lockForUpdate()->firstOrFail();
            $locked = Visit::whereKey($visit->id)->lockForUpdate()->firstOrFail();
            abort_if($locked->status === 'Batal', 409);
            $teeth = $odontogram->data($locked)['teeth'];
            $tooth = collect($data)->only(['kondisi', 'tindakan', 'catatan'])->all();
            $teeth[$data['nomor_gigi']] = $tooth;
            $existing = DB::table('visit_odontogram_snapshots')->where('visit_id', $visit->id)->first();
            DB::table('visit_odontogram_snapshots')->updateOrInsert(['visit_id' => $visit->id], [
                'teeth' => json_encode($teeth),
                'created_at' => $existing->created_at ?? now(),
                'updated_at' => now(),
            ]);

            // Editing a historical visit must never replace the patient's latest condition.
            $latest = $locked->patient->dentalVisits()->orderByDesc('tanggal_kunjungan')->orderByDesc('id')->first();
            if ($latest?->id === $locked->id) {
                Odontogram::updateOrCreate([
                    'patient_id' => $locked->patient_id,
                    'visit_id' => null,
                    'nomor_gigi' => $data['nomor_gigi'],
                ], $tooth);
            }
        });

        return redirect()->to($request->boolean('return_to_examination') ? route('visits.edit', $visit).'#odontogram' : route('visits.odontogram.show', $visit))->with('success', 'Odontogram kunjungan tersimpan.');
    }
}
