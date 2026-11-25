<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireReportAuthentication
{
    public function handle(Request $request, Closure $next): Response
    {
        $confirmation = $request->session()->get('reports.confirmation', []);
        if (! $request->user()
            || ($confirmation['user_id'] ?? null) !== $request->user()->getAuthIdentifier()
            || ($confirmation['session_id'] ?? null) !== $request->session()->getId()
            || ($confirmation['expires_at'] ?? 0) <= now()->timestamp) {
            $request->session()->forget('reports.confirmation');
            $request->session()->put('reports.intended', $request->getRequestUri());

            return redirect()->route('reports.login');
        }

        $response = $next($request);
        $response->headers->set('Cache-Control', 'no-store, private');

        return $response;
    }
}
