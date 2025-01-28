<?php

namespace App\Listeners;

use App\Events\EventFinished;
use App\Mail\SurveyEmail;
use Illuminate\Support\Facades\Mail;

class SendSurveyEmails
{
    public function handle(EventFinished $event)
    {
        $participants = $event->event->participants;
        $surveyLink = route('survey.form', ['event' => $event->event->id]);

        foreach ($participants as $participant) {
            Mail::to($participant->email)->send(new SurveyEmail($surveyLink));
        }
    }
}
