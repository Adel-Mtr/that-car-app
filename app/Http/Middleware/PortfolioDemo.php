<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PortfolioDemo
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('demo.enabled')) {
            return $next($request);
        }

        abort_if($request->is('admin', 'admin/*'), 403, 'Admin access is disabled in the portfolio demo.');

        if ($request->isMethod('GET') && $request->is('register', 'forgot-password', 'reset-password/*')) {
            return redirect()->route('login');
        }

        abort_if($request->is('verify-email/*'), 403, 'Account changes are disabled in the portfolio demo.');

        if ($request->isMethod('POST') && $request->routeIs('login.store')) {
            abort_unless(
                $request->input('email') === 'demo@thatcarapp.test',
                403,
                'Use the sample member account shown on the sign-in page.',
            );
        } elseif (! $request->isMethodSafe() && ! ($request->isMethod('POST') && $request->routeIs('logout'))) {
            abort(403, 'This portfolio demo is read-only. Changes and uploads are disabled.');
        }

        return $next($request);
    }
}
