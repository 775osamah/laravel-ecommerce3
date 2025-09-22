<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Auth;

class EnsureSanctumAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        // Get the token from the request
        $token = $request->bearerToken();
        
        if (!$token) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }
        
        // Find the token using Sanctum
        $accessToken = PersonalAccessToken::findToken($token);
        
        if (!$accessToken) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }
        
        // Get the user and authenticate them
        $user = $accessToken->tokenable;
        
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }
        
        // Authenticate the user - FIXED METHOD
        Auth::login($user);
        
        return $next($request);
    }
}