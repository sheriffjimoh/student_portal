<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle($request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        $role = $roles[0];
        
        if ($user->role->name == $role) {
            return $next($request);
        }

        abort(403, 'Unauthorized action.');
    }
}