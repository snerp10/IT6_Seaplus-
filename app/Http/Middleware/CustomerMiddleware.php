<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CustomerMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        
        if (!$user || !$user->hasRole('customer')) {
            return redirect()->route('dashboard.index')
                ->with('error', 'Access denied. Customer privileges required.');
        }

        return $next($request);
    }
}
