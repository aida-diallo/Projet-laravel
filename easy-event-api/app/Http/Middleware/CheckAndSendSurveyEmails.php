<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class CheckAndSendSurveys
{
    public function handle($request, Closure $next)
    {
        try {
            Log::info('Vérification des sondages en attente.');
            // Exécuter la commande automatiquement
            Artisan::call('send:survey-emails');
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi automatique des sondages : ' . $e->getMessage());
        }

        return $next($request);
    }
}
