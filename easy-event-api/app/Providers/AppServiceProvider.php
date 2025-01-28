<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use App\Models\Sondage;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        try {
            $sondage = Sondage::where('emails_sent', false)->first();

            if ($sondage) {
                Log::info('Démarrage de l\'envoi automatique des sondages.');

                Artisan::call('send:survey-emails');

                $sondage->emails_sent = true;
                $sondage->save();
                
                Log::info('Envoi des emails effectué avec succès.');
            } else {
                Log::info('Tous les sondages ont déjà envoyé leurs emails.');
            }

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi automatique des sondages : ' . $e->getMessage());
        }
    }
}
