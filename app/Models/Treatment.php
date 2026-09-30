<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Treatment extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode',
        'nama',
        'kategori',
        'tarif',
        'jasa_dokter',
    ];

    public function visitTreatments()
    {
        return $this->hasMany(VisitTreatment::class);
    }
}
