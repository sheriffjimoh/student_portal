<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Check if user is not logged in
        if (!$request->user()) {
            return redirect()->route('login');
        }

        // Check if user's role matches the required role
        if ($request->user()->role->name !== $role) {
            // If user is logged in but wrong role, show 403 error
            abort(403, 'You do not have permission to access this resource.');
        }

        // Role check passed, continue with the request
        return $next($request);
    }
    
}
