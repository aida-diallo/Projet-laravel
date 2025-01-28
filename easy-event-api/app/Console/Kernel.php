<?php
// app/Console/Kernel.php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\SendSurveyEmails;

class Kernel extends ConsoleKernel
{
    /**
     * Les commandes artisanales de l'application.
     *
     * @var array
     */
    protected $commands = [
        SendSurveyEmails::class, 
    ];

    /**
     * Définir les programmes de planification des tâches.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('send:survey-emails'); 
    }

    /**
     * Définir les commandes qui sont exécutées au démarrage du programme.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
