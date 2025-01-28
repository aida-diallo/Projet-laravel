<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sondage Événement</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h1 style="color: #2c3e50;">Bonjour {{ $participant->user->name }},</h1>
        
        <p>Nous espérons que vous avez apprécié l'événement <strong>{{ $evenement->nom }}</strong>.</p>
        
        <p>Nous aimerions connaître votre avis pour améliorer nos prochains événements. Cela ne prendra que quelques minutes !</p>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ url('http://localhost:5173/form/' . $token) }}" 
               style="background-color: rgb(0, 179, 137); 
                      color: white; 
                      padding: 12px 25px; 
                      text-decoration: none; 
                      border-radius: 5px;
                      display: inline-block;">
                Donner mon avis
            </a>
        </div>
        
        <!-- <p>Si le bouton ne fonctionne pas, vous pouvez copier et coller ce lien 
            dans votre navigateur :</p>
        <p style="word-break: break-all;">{{ url('http://localhost:5173/form/' . $token) }}</p> -->
        
        <p>Merci pour votre participation,<br>
        L'équipe 
        <!-- {{ config('app.name') }} -->
        <span style="text-xl font-medium">
                    <span style="relative inline-block">
                    <span style="absolute inset-0 bg-[#ff9900] transform -skew-y-3"></span>
                    <span style="relative text-white px-2">Easy</span>
                    </span>
                    <span style="text-green">&nbsp;Events</span>
                    </span>
                    </p>
    </div>
</body>
</html>
