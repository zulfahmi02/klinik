<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ReportLoginRequest;
use Illuminate\Http\Request;

class ReportSessionController extends Controller
{
    public function create(Request $request)
    {
        $request->session()->forget('reports.confirmation');

        return response()->view('auth.report-login')->header('Cache-Control', 'no-store, private');
    }

    public function entry(Request $request)
    {
        $request->session()->forget('reports.confirmation');
        $request->session()->put('reports.intended', '/laporan');

        return redirect()->route('reports.login');
    }

    public function store(ReportLoginRequest $request)
    {
        $request->session()->forget('reports.confirmation');
        $request->authenticate();
        $request->session()->regenerate();
        $request->session()->put('reports.confirmation', [
            'user_id' => $request->user()->getAuthIdentifier(),
            'session_id' => $request->session()->getId(),
            'expires_at' => now()->addMinutes(30)->timestamp,
        ]);
        $intended = $request->session()->pull('reports.intended', '/laporan');
        // Only local report destinations are allowed; never trust a supplied redirect URL.
        if (! is_string($intended) || ! preg_match('#^/laporan(?:/|\?|$)#', $intended)
            || str_contains($intended, '\\') || str_starts_with($intended, '/laporan/login')
            || str_starts_with($intended, '/laporan/masuk')) {
            $intended = '/laporan';
        }

        return redirect($intended);
    }
}
