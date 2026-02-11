<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  string  ...$roles  Allowed roles (e.g. 'admin', 'personnel')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Support comma-separated roles (e.g. role:admin,sub-admin)
        $allowed = collect($roles)->flatMap(function ($r) {
            return array_map('trim', explode(',', $r));
        })->filter()->values()->all();

        if (! in_array($user->role, $allowed)) {
            abort(403, 'Unauthorized. You do not have access to this area.');
        }

        return $next($request);
    }
}
