<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'spesialisasi' => ['required', 'string', 'max:255'],
            'no_sip' => ['nullable', 'string', 'max:255'],
            'no_hp' => ['nullable', 'string', 'max:255'],
            'tarif_konsultasi' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'in:Aktif,Nonaktif'],
        ]);

        Doctor::create($validated);

        return redirect()->route('reports.doctors')->with('success', 'Dokter berhasil ditambahkan.');
    }

    public function destroy(Doctor $doctor): RedirectResponse
    {
        $doctor->delete();

        return redirect()->route('reports.doctors')->with('success', 'Dokter berhasil dihapus.');
    }
}
