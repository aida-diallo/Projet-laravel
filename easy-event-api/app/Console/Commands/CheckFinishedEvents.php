<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Evenement;
use Carbon\Carbon;

class CheckFinishedEvents extends Command
{
    
    protected $signature = 'events:check-finished';

    protected $description = 'Vérifie si un événement est terminé.';

    public function handle()
    {
        // Récupération des événements terminés
        $finishedEvents = Evenement::where('heureFin', '<', Carbon::now())->get();

        if ($finishedEvents->isEmpty()) {
            $this->info('Aucun événement terminé trouvé.');
        } else {
            $this->info('Liste des événements terminés :');
            foreach ($finishedEvents as $evenement) {
                $this->line(" - {$evenement->name} (Terminé le : {$evenement->heureFin})");
            }
        }
    }
}
