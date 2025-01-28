<!DOCTYPE html>
<html>
<head>
    <title>Confirmation d'inscription</title>
</head>
<body>
<h1>Confirmation d'inscription</h1>

<p>Bonjour {{ $user->nom }},</p>

<p>Votre inscription à l'événement "{{ $evenement->nom }}" a été enregistrée avec succès.</p>

<h2>Détails de l'événement :</h2>
<ul>
    <li>Date : {{ $evenement->date }}</li>
    <li>Heure : {{ $evenement->heureDebut }}</li>
    <li>Lieu : {{ $evenement->lieu }}</li>
    @if($evenement->description)
        <li>Description : {{ $evenement->description }}</li>
    @endif
</ul>

<p>Voici votre QR Code d'accès :</p>
<img src="{{ $message->embed(public_path($qrCodePath)) }}" alt="QR Code">

<p>Conservez ce QR Code, il vous sera demandé lors de l'événement.</p>

<p>Cordialement,<br>L'équipe d'organisation</p>
</body>
</html>
