<?php

namespace App\Http\Controllers;

use App\Mail\InscriptionConfirmation;
use App\Models\Evenement;
use App\Mail\SurveyEmail;
use App\Models\Participant;
use App\Models\User;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use DateTime;
use Illuminate\Http\JsonResponse;

class EvenementController extends Controller
{
    public function listEvents(Request $request)
{
    $query = Evenement::with('categorie');

    if ($request->has('search')) {
        $query->where(function($q) use ($request) {
            $q->where('nom', 'like', '%' . $request->search . '%')
              ->orWhere('description', 'like', '%' . $request->search . '%');
        });
    }

    if ($request->has('categorie')) {
        $query->whereHas('categorie', function($q) use ($request) {
            $q->where('nom', $request->categorie);
        });
    }

    if ($request->has('period')) {
        $now = now();
        switch ($request->period) {
            case 'comming':
                $query->where('date', '>=', $now);
                break;
            case 'past':
                $query->where('date', '<', $now);
                break;
            case 'month':
                $query->whereBetween('date', [$now, $now->copy()->addMonth()]);
                break;
        }
    }

    // Filtre par localisation
    // if ($request->has('location')) {
    //     $query->where('lieu', $request->location);
    // }

    $perPage = $request->input('per_page', 4);
    $events = $query->paginate($perPage);

    return response()->json($events);
}

    public function inscrire(Request $request, $evenementId)
    {
        $evenement = Evenement::findOrFail($evenementId);
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'telephone' => 'required|string|max:20',
        ]);

        if (!$this->verifierDisponibilite($evenement)) {
            return response()->json([
                'message' => 'Désolé, plus de places disponibles'
            ], 400);
        }

        try {
            return DB::transaction(function () use ($evenement, $validatedData) {
              
                $user = User::where('email', $validatedData['email'])->first();

                if (!$user) {
                 
                    $user = User::create([
                        'name' => $validatedData['name'],
                        'email' => $validatedData['email'],
                        'role' => 'participant',
                        'email_verified_at' => now()
                    ]);
                }

                $participant = Participant::where('user_id', $user->id)
                    ->whereHas('events', function ($query) use ($evenement) {
                        $query->where('evenement_id', $evenement->id);
                    })
                    ->first();

                if ($participant) {
                    return response()->json([
                        'message' => 'Vous êtes déjà inscrit à cet événement',
                        'participant' => $participant
                    ], 400);
                }

                $participant = Participant::create([
                    'user_id' => $user->id,
                    'telephone' => $validatedData['telephone'],
                    'badge' => 'temp' 
                ]);

                $qrcodeDirectory = public_path('qrcodes');
                if (!File::exists($qrcodeDirectory)) {
                    File::makeDirectory($qrcodeDirectory, 0755, true);
                }

                $qrCodePath = 'qrcodes/participant_' . $participant->id . '.png';
                $fullPath = public_path($qrCodePath);

                $qrCode = QrCode::create(json_encode([
                    'participant_id' => $participant->id,
                    'evenement_id' => $evenement->id,
                    'name' => $user->name,
                    'email' => $user->email
                ]))
                    ->setSize(300)
                    ->setMargin(10);

                $writer = new PngWriter();
                $result = $writer->write($qrCode);
                $result->saveToFile($fullPath);

                $participant->update(['badge' => $qrCodePath]);

                $evenement->participants()->attach($participant->id, [
                    'dateInscription' => now(),
                    'status' => 'en_attente'
                ]);

                try {
                    Mail::to($user->email)
                        ->send(new InscriptionConfirmation($user, $evenement, $qrCodePath));
                } catch (\Exception $e) {
                    \Log::error('Erreur lors de l\'envoi de l\'email:', [
                        'error' => $e->getMessage(),
                        'user' => $user->id
                    ]);
                   
                }

                return response()->json([
                    'message' => 'Inscription réussie',
                    'participant' => $participant,
                    'qr_code' => $qrCodePath
                ], 201);
            });
        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'inscription:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Erreur lors de l\'inscription',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    private function verifierDisponibilite(Evenement $evenement)
    {
        $participantsActuels = $evenement->participants()->count();

        $participantsActuels = $evenement->participants()
            ->wherePivot('status', '!=', 'rejete')
            ->count();

        \Log::info('Vérification disponibilité', [
            'evenement_id' => $evenement->id,
            'max_participants' => $evenement->max_participants,
            'participants_actuels' => $participantsActuels
        ]);

        return $participantsActuels < $evenement->max_participants;
    }


    public function verifierParticipant(Request $request)
    {
        $validatedData = $request->validate([
            'participant_id' => 'required|integer',
            'evenement_id' => 'required|integer',
        ]);
    
        $participant = Participant::where('id', $validatedData['participant_id'])
            ->whereHas('events', function ($query) use ($validatedData) {
                $query->where('evenement_id', $validatedData['evenement_id']);
            })->first();
    
        if (!$participant) {
            return response()->json(['message' => 'Participant introuvable ou non inscrit à cet événement.'], 404);
        }
    
        return response()->json([
            'message' => 'Participant vérifié avec succès.',
            'participant' => $participant,
        ]);
    }

    public function getEventDetails($id)
    {
        $event = Event::find($id);

        if (!$event) {
            return response()->json(['message' => 'Événement introuvable'], 404);
        }

        return response()->json([
            'message' => 'Sondage trouvé.',
            'data' => $event
        ]);
    }
    

    public function sendSurveyEmails($eventId): JsonResponse
    {

        $event = Event::find($eventId);

        if ($event && $event->is_finished) {
         
            $participants = $event->participants;

           
            $surveyLink = route('survey.form', ['event' => $eventId]);

            foreach ($participants as $participant) {
                Mail::to($participant->email)->send(new SurveyEmail($surveyLink));
            }

            return response()->json(['message' => 'Sondages envoyés avec succès !']);
        }

        return response()->json(['message' => 'L\'événement n\'est pas terminé ou introuvable.'], 400);
    }


}


