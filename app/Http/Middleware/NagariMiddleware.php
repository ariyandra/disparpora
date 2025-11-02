<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NagariMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->role == 3) {
            if (!auth()->user()->nagari_id) {
                return redirect('/')->with('error', 'Nagari belum dipilih.');
            }
            return $next($request);
        }
        
        return redirect('/')->with('error', 'Unauthorized access.');
    }
}
