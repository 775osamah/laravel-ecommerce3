<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class TempAuthMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Temporarily authenticate as the first user for development
        if (!auth()->check()) {
            $user = User::first();
            if ($user) {
                auth()->setUser($user);
            }
        }
        
        return $next($request);
    }
}