<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class LeaveReportArea
{
    public function handle(Request $request, Closure $next)
    {
        $request->session()->forget(['reports.confirmation', 'reports.intended']);

        return $next($request);
    }
}
