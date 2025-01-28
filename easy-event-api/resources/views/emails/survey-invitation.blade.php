@component('mail::message')
# Bonjour {{ $participantName }},

Une nouvelle question de sondage a été ajoutée : 

**{{ $questionText }}**

Cliquez sur le lien ci-dessous pour répondre au sondage :

@component('mail::button', ['url' => $surveyLink])
Répondre au Sondage
@endcomponent

Merci,  
{{ config('app.name') }}
@endcomponent
