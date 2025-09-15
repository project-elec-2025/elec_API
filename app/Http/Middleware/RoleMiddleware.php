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
    public function handle(Request $request, Closure $next, string ...$roles)
    {

        if (!Auth::check() || !in_array(Auth::user()->role, $roles)) {
            //  abort(403, 'Unauthorized access');
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access،You are not authorized to do this',

            ], 403);
        }

        return $next($request);
    }
}
