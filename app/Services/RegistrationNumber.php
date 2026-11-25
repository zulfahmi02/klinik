<?php

namespace App\Services;

use App\Models\Patient;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RegistrationNumber
{
    // Call inside the transaction which creates the patient or visit.
    public function next(string $type, string $date): string
    {
        // Retain the required legacy column using an internal, non-sequential reference.
        if ($type === 'visit') {
            return 'VIS-'.Str::uuid();
        }

        $key = $type.':'.$date;
        DB::table('registration_counters')->insertOrIgnore(['name' => $key, 'value' => 0]);
        $counter = DB::table('registration_counters')->where('name', $key)->lockForUpdate()->first();
        $sequence = $counter->value;

        // Include numbers created before the counter was introduced.
        $prefix = 'RM-'.str_replace('-', '', $date).'-';
        $numbers = Patient::where('no_rm', 'like', $prefix.'%')->pluck('no_rm');
        foreach ($numbers as $number) {
            if (str_starts_with($number, $prefix) && ctype_digit(substr($number, strlen($prefix)))) {
                $sequence = max($sequence, (int) substr($number, strlen($prefix)));
            }
        }

        DB::table('registration_counters')->where('name', $key)->update(['value' => ++$sequence]);

        return $prefix.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }
}
