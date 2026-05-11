<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DirecteurMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Vérifie si connecté
        if (!auth()->check()) {
            return redirect('/login');
        }

        // Vérifie rôle
        if (auth()->user()->role !== 'directeur') {
            abort(403);
        }

        return $next($request);
    }
}