<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Mail\SurveyNotification;
use Illuminate\Support\Facades\Mail;
use App\Models\Participant;
use App\Models\Question;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SendSurveyEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:survey-emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envoyer les emails du sondage à tous les participants.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
           
            $recentQuestions = Question::where('created_at', '>=', Carbon::now()->subMinutes(5))->count();

            if ($recentQuestions === 0) {
                $this->info('Aucune nouvelle question créée récemment. Aucun email envoyé.');
                return;
            }

            $participants = Participant::with(['user', 'events'])->get();

            if ($participants->isEmpty()) {
                $this->warn('Aucun participant trouvé dans la base de données.');
                return;
            }

            $this->info('Début de l\'envoi des emails...');
            $successCount = 0;
            $errorCount = 0;

            foreach ($participants as $participant) {
                $email = $participant->user->email ?? null;

                if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errorCount++;
                    Log::warning('Email invalide ou manquant', ['participant_id' => $participant->id]);
                    continue;
                }

                try {
                    $participant->generateToken();
                    Mail::to($email)->send(new SurveyNotification($participant));
                    $successCount++;
                    Log::info('Email de sondage envoyé', ['participant_id' => $participant->id, 'email' => $email]);
                } catch (\Exception $e) {
                    $errorCount++;
                    Log::error('Erreur d\'envoi d\'email', [
                        'participant_id' => $participant->id,
                        'email' => $email,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            $this->info("✅ Emails envoyés avec succès : {$successCount}");
            $this->error("❌ Erreurs : {$errorCount}");
        } catch (\Exception $e) {
            $this->error('Erreur critique : ' . $e->getMessage());
            Log::error('Erreur critique dans send:survey-emails', ['error' => $e->getMessage()]);
        }
    }
}
