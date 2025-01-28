<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Participant;

class SurveyNotification extends Mailable
{
    use SerializesModels;

    public $participant;

    // Constructor pour l'initialisation
    public function __construct(Participant $participant)
    {
        $this->participant = $participant;
    }

    public function build()
    {
        return $this->view('emails.survey_notification')
                    ->subject('Votre avis sur notre événement')
                    ->with([
                        'participant' => $this->participant,
                        'evenement' => $this->participant->events->first(),
                        'token' => $this->participant->survey_token,
                    ]);
    }
}
