<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'spesialisasi',
        'no_sip',
        'no_hp',
        'tarif_konsultasi',
        'status',
    ];

    public function visits()
    {
        return $this->hasMany(Visit::class);
    }
}
