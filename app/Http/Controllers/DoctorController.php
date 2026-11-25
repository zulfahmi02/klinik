<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    public function index()
    {
        $doctors = Doctor::withCount('visits')->orderBy('nama')->paginate(15);

        return view('doctors.index', compact('doctors'));
    }

    public function create()
    {
        return view('doctors.create');
    }

    public function show(Doctor $doctor)
    {
        $doctor->loadCount('visits');

        return view('doctors.show', compact('doctor'));
    }

    public function edit(Doctor $doctor)
    {
        return view('doctors.edit', compact('doctor'));
    }

    public function update(Request $request, Doctor $doctor): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'spesialisasi' => ['required', 'string', 'max:255'],
            'no_sip' => ['nullable', 'string', 'max:255'],
            'no_hp' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:Aktif,Nonaktif'],
        ]);
        $doctor->update($validated);

        return redirect()->route('doctors.show', $doctor)->with('success', 'Data dokter berhasil diperbarui.');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'spesialisasi' => ['required', 'string', 'max:255'],
            'no_sip' => ['nullable', 'string', 'max:255'],
            'no_hp' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:Aktif,Nonaktif'],
        ]);

        $doctor = Doctor::create(array_merge($validated, ['tarif_konsultasi' => 0]));

        return redirect()->route('doctors.show', $doctor)->with('success', 'Dokter berhasil ditambahkan.');
    }

    public function destroy(Doctor $doctor): RedirectResponse
    {
        $doctor->delete();

        return redirect()->route('doctors.index')->with('success', 'Dokter berhasil dihapus.');
    }
}
