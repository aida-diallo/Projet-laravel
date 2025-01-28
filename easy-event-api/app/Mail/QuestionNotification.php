<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class QuestionNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $participant;
    public $question;

    public function __construct($participant, $question)
    {
        $this->participant = $participant;
        $this->question = $question;
    }

    public function build()
    {
        return $this->subject('Nouvelle Question de Sondage')
            ->markdown('emails.questions.notification')
            ->with([
                'participantName' => $this->participant->name,
                'questionText' => $this->question->texte,
                'surveyLink' => url('/survey/' . $this->participant->survey_token),
            ]);
    }
}
