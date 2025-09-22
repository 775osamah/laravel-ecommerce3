<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated and has admin role
        if (!auth()->check()) {
            return response()->json([
                'message' => 'غير مصرح بالدخول. يرجى تسجيل الدخول.'
            ], 401);
        }

        if (auth()->user()->role !== 'admin') {
            return response()->json([
                'message' => 'غير مصرح بالدخول. هذه الصفحة للمديرين فقط.'
            ], 403);
        }

        return $next($request);
    }
}