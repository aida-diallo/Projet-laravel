<?php

namespace App\Filament\Resources\QuestionResource\Pages;

use App\Filament\Resources\QuestionResource;
use App\Models\Participant;
use App\Mail\QuestionNotification;
use Illuminate\Support\Facades\Mail;
use Filament\Resources\Pages\CreateRecord;

class CreateQuestion extends CreateRecord
{
    protected static string $resource = QuestionResource::class;

    protected function afterCreate(): void
    {
        $question = $this->record; 

        $participants = Participant::all();

        foreach ($participants as $participant) {
            $participant->generateToken();

            Mail::to($participant->email)->send(new QuestionNotification($participant, $question));
        }
    }
}
