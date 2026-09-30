<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitTreatment extends Model
{
    use HasFactory;

    protected $fillable = [
        'visit_id',
        'treatment_id',
        'biaya',
        'catatan',
    ];

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }

    public function treatment()
    {
        return $this->belongsTo(Treatment::class);
    }
}
