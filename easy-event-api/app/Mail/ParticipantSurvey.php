<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Evenement;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ParticipantSurvey extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $evenement;
    public $surveyLink;

    public function __construct(User $user, Evenement $evenement, string $surveyLink)
    {
        $this->user = $user;
        $this->evenement = $evenement;
        $this->surveyLink = $surveyLink;
    }

    public function build()
    {
        Log::info('Construction du mail pour l\'événement : ' . $this->evenement->nom);
        Log::info('Destiné à : ' . $this->user->name);
        Log::info('Lien du sondage : ' . $this->surveyLink);

        return $this->subject('Donnez votre avis sur ' . $this->evenement->nom)
                    ->view('emails.survey')
                    ->text('emails.survey_plain');
    }
}