<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_antrean',
        'patient_id',
        'doctor_id',
        'poli',
        'tanggal_kunjungan',
        'jam_kunjungan',
        'keluhan',
        'diagnosa',
        'tensi_darah',
        'suhu_tubuh',
        'berat_badan',
        'penjamin',
        'status',
        'catatan_dokter',
        'biaya_konsultasi',
        'biaya_tindakan',
        'biaya_obat',
        'total_biaya',
        'status_pembayaran',
        'metode_pembayaran',
    ];

    protected $casts = [
        'tanggal_kunjungan' => 'date',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function visitTreatments()
    {
        return $this->hasMany(VisitTreatment::class);
    }

    public function odontograms()
    {
        return $this->hasMany(Odontogram::class);
    }
}
