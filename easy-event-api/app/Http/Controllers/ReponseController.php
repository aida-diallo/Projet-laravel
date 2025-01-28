<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reponse;
use App\Models\Question;
use App\Models\Evenement;
use App\Models\Participant;
use Illuminate\Support\Facades\DB;

class ReponseController extends Controller
{
    public function store(Request $request, $token)
    {
        try {
            DB::beginTransaction();

            // Récupérer le participant à partir du token
            $participant = Participant::where('survey_token', $token)->firstOrFail();
            
            // Récupérer l'événement associé au participant
            $evenement = $participant->events()->first();
            if (!$evenement) {
                throw new \Exception('Aucun événement trouvé pour ce participant.');
            }

            // Récupérer le sondage de l'événement
            $sondage = $evenement->sondage;
            if (!$sondage) {
                throw new \Exception('Aucun sondage trouvé pour cet événement.');
            }

            // Valider les données
            $validated = $request->validate([
                'answers' => 'required|array',
                'answers.*.question_id' => 'required|exists:questions,id',
                'answers.*.reponse' => 'required',
            ]);

            $reponses = [];
            foreach ($validated['answers'] as $answer) {
                // Vérifier que la question appartient au bon sondage
                $question = Question::where('id', $answer['question_id'])
                    ->where('sondage_id', $sondage->id)
                    ->firstOrFail();

                // Créer la réponse
                $reponse = Reponse::create([
                    // 'participant_id' => $participant->id,
                    'question_id' => $answer['question_id'],
                    'reponse' => $answer['reponse'],
                    'evenement_id' => $evenement->id
                ]);

                $reponses[] = $reponse;
            }

            DB::commit();

            return response()->json([
                'message' => 'Réponses enregistrées avec succès',
                'data' => $reponses
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Erreur lors de l\'enregistrement des réponses',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}