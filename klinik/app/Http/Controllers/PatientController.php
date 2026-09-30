<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function index()
    {
        $patients = Patient::latest()->paginate(10);
        return view('patients.index', compact('patients'));
    }

    public function create()
    {
        return view('patients.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'            => 'required|string|max:255',
            'nik'             => 'nullable|string|max:20|unique:patients,nik',
            'jenis_kelamin'   => 'required|in:L,P',
            'tanggal_lahir'   => 'nullable|date',
            'tempat_lahir'    => 'nullable|string|max:100',
            'alamat'          => 'nullable|string',
            'no_hp'           => 'nullable|string|max:20',
            'pekerjaan'       => 'nullable|string|max:100',
            'status_pernikahan' => 'nullable|string|max:50',
            'golongan_darah'  => 'nullable|string|max:5',
            'agama'           => 'nullable|string|max:50',
            'penjamin'        => 'nullable|string|max:50',
            'no_penjamin'     => 'nullable|string|max:50',
            'alergi'          => 'nullable|string',
        ]);

        // Generate No. RM otomatis (format: RM-YYYYMMDD-XXXX)
        $today = now()->format('Ymd');
        $lastPatient = Patient::whereDate('created_at', today())->latest('id')->first();
        $sequence = $lastPatient ? (int)substr($lastPatient->no_rm, -4) + 1 : 1;
        $no_rm = 'RM-' . $today . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);

        Patient::create([
            'no_rm'             => $no_rm,
            'nik'               => $request->nik,
            'nama'              => $request->nama,
            'jenis_kelamin'     => $request->jenis_kelamin,
            'tanggal_lahir'     => $request->tanggal_lahir,
            'tempat_lahir'      => $request->tempat_lahir,
            'alamat'            => $request->alamat,
            'no_hp'             => $request->no_hp,
            'pekerjaan'         => $request->pekerjaan,
            'status_pernikahan' => $request->status_pernikahan,
            'golongan_darah'    => $request->golongan_darah,
            'agama'             => $request->agama,
            'penjamin'          => $request->penjamin,
            'no_penjamin'       => $request->no_penjamin,
            'alergi'            => $request->alergi,
        ]);

        return redirect()->route('patients.index')
            ->with('success', 'Pasien berhasil didaftarkan dengan No. RM: ' . $no_rm);
    }

    public function show(Patient $patient)
    {
        return view('patients.show', compact('patient'));
    }

    public function edit(Patient $patient)
    {
        return view('patients.edit', compact('patient'));
    }

    public function update(Request $request, Patient $patient)
    {
        $request->validate([
            'nama'            => 'required|string|max:255',
            'nik'             => 'nullable|string|max:20|unique:patients,nik,' . $patient->id,
            'jenis_kelamin'   => 'required|in:L,P',
            'tanggal_lahir'   => 'nullable|date',
            'tempat_lahir'    => 'nullable|string|max:100',
            'alamat'          => 'nullable|string',
            'no_hp'           => 'nullable|string|max:20',
            'pekerjaan'       => 'nullable|string|max:100',
            'status_pernikahan' => 'nullable|string|max:50',
            'golongan_darah'  => 'nullable|string|max:5',
            'agama'           => 'nullable|string|max:50',
            'penjamin'        => 'nullable|string|max:50',
            'no_penjamin'     => 'nullable|string|max:50',
            'alergi'          => 'nullable|string',
        ]);

        $patient->update($request->only([
            'nik', 'nama', 'jenis_kelamin', 'tanggal_lahir', 'tempat_lahir',
            'alamat', 'no_hp', 'pekerjaan', 'status_pernikahan',
            'golongan_darah', 'agama', 'penjamin', 'no_penjamin', 'alergi'
        ]));

        return redirect()->route('patients.index')
            ->with('success', 'Data pasien berhasil diperbarui.');
    }

    public function destroy(Patient $patient)
    {
        $patient->delete();
        return redirect()->route('patients.index')
            ->with('success', 'Data pasien berhasil dihapus.');
    }
}