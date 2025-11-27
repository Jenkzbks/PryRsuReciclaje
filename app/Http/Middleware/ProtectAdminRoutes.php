<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProtectAdminRoutes
{
    /**
     * Handle an incoming request.
     * If the path starts with admin and the user is not authenticated,
     * redirect to login.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->is('admin') || $request->is('admin/*')) {
            if (! Auth::check()) {
                return redirect()->guest(route('login'));
            }
        }

        return $next($request);
    }
}
