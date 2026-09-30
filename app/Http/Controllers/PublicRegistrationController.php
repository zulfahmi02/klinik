<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Visit;
use App\Services\RegistrationNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PublicRegistrationController extends Controller
{
    public function create(Request $request)
    {
        $request->session()->put('registration_token', $request->session()->get('registration_token', (string) Str::uuid()));

        return view('registration.create');
    }

    public function store(Request $request, RegistrationNumber $numbers)
    {
        // A completed form can be retried without creating a second visit.
        if ($request->input('registration_token') && $request->input('registration_token') === $request->session()->get('registration_receipt.token')) {
            return redirect()->route('registration.success');
        }

        $data = $request->validate([
            'registration_token' => ['required', 'string'],
            'nama' => ['required', 'string', 'max:255'],
            'nik' => ['nullable', 'digits:16'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'tanggal_lahir' => ['required', 'date', 'before_or_equal:today'],
            'alamat' => ['required', 'string', 'max:2000'],
            'no_hp' => ['required', 'string', 'max:20', 'regex:/^[0-9+()\s-]{8,20}$/'],
            'penjamin' => ['required', 'in:Umum,BPJS,Asuransi Swasta'],
            'no_penjamin' => ['nullable', 'required_unless:penjamin,Umum', 'string', 'max:50'],
            'alergi' => ['nullable', 'string', 'max:2000'],
            'poli' => ['required', 'in:Poli Umum,Poli Gigi,KIA / KB,Poli Anak'],
            'keluhan' => ['required', 'string', 'max:2000'],
        ]);

        if ($data['registration_token'] !== $request->session()->get('registration_token')) {
            throw ValidationException::withMessages(['registration_token' => 'Form sudah kedaluwarsa. Muat ulang halaman dan coba kembali.']);
        }

        $receipt = DB::transaction(function () use ($data, $numbers) {
            $now = now('Asia/Jakarta');
            $date = $now->toDateString();
            // Allocate first to serialize submissions, including repeat registrations by NIK.
            $noRm = $numbers->next('patient', $date);
            $patient = ! empty($data['nik']) ? Patient::where('nik', $data['nik'])->first() : null;
            if ($patient) {
                // Never expose or overwrite an existing patient's identity from a public form.
                throw ValidationException::withMessages(['nik' => 'Pendaftaran dengan data ini perlu dibantu petugas. Silakan hubungi bagian pendaftaran.']);
            }
            $patient = Patient::create(array_merge(
                collect($data)->only(['nama', 'nik', 'jenis_kelamin', 'tanggal_lahir', 'alamat', 'no_hp', 'penjamin', 'no_penjamin', 'alergi'])->all(),
                ['no_rm' => $noRm]
            ));
            $queue = $numbers->next('visit', $date);
            Visit::create([
                'patient_id' => $patient->id,
                'no_antrean' => $queue,
                'poli' => $data['poli'],
                'tanggal_kunjungan' => $date,
                'jam_kunjungan' => $now->format('H:i'),
                'penjamin' => $data['penjamin'],
                'keluhan' => $data['keluhan'],
                'status' => 'Menunggu',
                'status_pembayaran' => 'Belum Lunas',
                // Match the existing consultation default; staff confirms final fees.
                'biaya_konsultasi' => 50000,
                'total_biaya' => 50000,
            ]);

            return ['token' => $data['registration_token'], 'no_rm' => $noRm, 'no_antrean' => $queue, 'poli' => $data['poli'], 'tanggal' => $now->format('d/m/Y')];
        }, 3);

        $request->session()->put('registration_receipt', $receipt);
        $request->session()->forget('registration_token');

        return redirect()->route('registration.success');
    }

    public function success(Request $request)
    {
        if (! $request->session()->has('registration_receipt')) {
            return redirect()->route('registration.create');
        }

        return response()->view('registration.success', ['receipt' => $request->session()->get('registration_receipt')])
            ->header('Cache-Control', 'no-store, private');
    }
}
