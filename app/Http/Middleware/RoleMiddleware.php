<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $roles): Response
    {

        if (!auth()->check()) {
            abort(401);
        }

        if (!Auth::user()->roles()->where('name', $roles)->exists()) {
            abort(403,"user doesn't have Authorization");
        }

        return $next($request);
    }
}
