<?php

namespace App\Services;

use App\Models\Visit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class VisitOdontogram
{
    public const TEETH = [18, 17, 16, 15, 14, 13, 12, 11, 21, 22, 23, 24, 25, 26, 27, 28, 48, 47, 46, 45, 44, 43, 42, 41, 31, 32, 33, 34, 35, 36, 37, 38];

    public const PRIMARY_TEETH = [55, 54, 53, 52, 51, 61, 62, 63, 64, 65, 85, 84, 83, 82, 81, 71, 72, 73, 74, 75];

    public static function allTeeth(): array
    {
        return [...self::TEETH, ...self::PRIMARY_TEETH];
    }

    public static function displayTeeth(array $teeth): array
    {
        return array_intersect(self::PRIMARY_TEETH, array_keys($teeth))
            ? self::allTeeth() : self::TEETH;
    }

    public const CONDITIONS = ['Sehat', 'Karies', 'Tambalan Komposit', 'Tambalan Sementara', 'Hilang', 'Sisa Akar', 'Mahkota', 'Gigi Tiruan', 'Impaksi'];

    public function data(Visit $visit): array
    {
        // The watched application can still display visits before the additive migration is applied.
        $available = Schema::hasTable('visit_odontogram_snapshots');
        $snapshot = $available ? DB::table('visit_odontogram_snapshots')->where('visit_id', $visit->id)->first() : null;
        $visit->loadMissing(['odontograms', 'patient.odontograms']);
        $legacy = $visit->odontograms->keyBy('nomor_gigi')->map->only(['kondisi', 'tindakan', 'catatan'])->all();

        return [
            'snapshotAvailable' => $available,
            'snapshot' => $snapshot,
            'teeth' => $snapshot ? json_decode($snapshot->teeth, true) : $legacy,
            'currentTeeth' => $visit->patient->odontograms->sortBy('updated_at')->keyBy('nomor_gigi')->map->only(['kondisi', 'tindakan', 'catatan'])->all(),
        ];
    }
}
