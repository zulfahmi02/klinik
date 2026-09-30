<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_rm',
        'nik',
        'nama',
        'jenis_kelamin',
        'tanggal_lahir',
        'tempat_lahir',
        'alamat',
        'no_hp',
        'pekerjaan',
        'status_pernikahan',
        'golongan_darah',
        'agama',
        'penjamin',
        'no_penjamin',
        'alergi',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    public function visits()
    {
        return $this->hasMany(Visit::class);
    }

    public function odontograms()
    {
        return $this->hasMany(Odontogram::class);
    }
}