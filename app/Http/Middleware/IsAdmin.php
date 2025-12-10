<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    /**
     * Gère une requête entrante.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Vérifie si l'utilisateur est connecté
        if (!Auth::check()) {
            // S'il n'est pas connecté, redirigez-le vers la page de connexion
            return redirect()->route('login'); 
        }

        // 2. Vérifie si l'utilisateur connecté est un administrateur
        if (Auth::user()->is_admin) {
            // Si oui, la requête continue son chemin vers la route demandée
            return $next($request);
        }

        // 3. Si l'utilisateur est connecté mais n'est pas admin
        // Retourne une erreur 403 (Accès interdit) ou redirige
        return response()->view('errors.403', [], 403);
        
        // Ou pour rediriger vers la page d'accueil :
        // return redirect('/');
    }
}