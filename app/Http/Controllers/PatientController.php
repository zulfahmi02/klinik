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
            'nama' => 'required|string|max:255',
            'nik' => 'nullable|string|max:20|unique:patients,nik,'.$patient->id,
            'jenis_kelamin' => 'required|in:L,P',
            'tanggal_lahir' => 'nullable|date',
            'tempat_lahir' => 'nullable|string|max:100',
            'alamat' => 'nullable|string',
            'no_hp' => 'nullable|string|max:20',
            'pekerjaan' => 'nullable|string|max:100',
            'status_pernikahan' => 'nullable|string|max:50',
            'golongan_darah' => 'nullable|string|max:5',
            'agama' => 'nullable|string|max:50',
            'penjamin' => 'nullable|string|max:50',
            'no_penjamin' => 'nullable|string|max:50',
            'alergi' => 'nullable|string',
        ]);

        $patient->update($request->only([
            'nik', 'nama', 'jenis_kelamin', 'tanggal_lahir', 'tempat_lahir',
            'alamat', 'no_hp', 'pekerjaan', 'status_pernikahan',
            'golongan_darah', 'agama', 'penjamin', 'no_penjamin', 'alergi',
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
