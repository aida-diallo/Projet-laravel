<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Sondage;
use App\Models\Question;
use App\Models\Participant; 
use App\Models\Reponse; 
use App\Mail\QuestionNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Models\Evenement;
use Illuminate\Support\Facades\Validator;


class SondageController extends Controller
{
    /**
     *
     *
     * @param Request 
     * @param int 
     * @return \Illuminate\Http\JsonResponse
     */
    public function addQuestion(Request $request, $id)
    {
        $validated = $request->validate([
            'text' => 'required|string|max:255', 
            'participant_id' => 'required|exists:participants,id', 
        ]);

        $sondage = Sondage::findOrFail($id);

        if ($sondage->emails_sent) {
            return response()->json([
                'message' => 'Les emails ont déjà été envoyés pour ce sondage.',
            ], 422);
        }

        $question = new Question([
            'texte' => $validated['text'],
        ]);

        $sondage->questions()->save($question);

        $participant = Participant::findOrFail($validated['participant_id']);

        if (filter_var($participant->email, FILTER_VALIDATE_EMAIL)) {
            try {
                Log::info('Envoi de l\'email à : ' . $participant->email);
                Mail::to($participant->email)->send(new QuestionNotification($participant, $question));

                $sondage->emails_sent = true;
                $sondage->save();

                return response()->json([
                    'message' => 'Question ajoutée avec succès et notification envoyée au participant.',
                    'question' => $question,
                ]);
            } catch (\Exception $e) {
                Log::error('Erreur lors de l\'envoi de l\'email : ' . $e->getMessage(), [
                    'participant_id' => $participant->id,
                    'email' => $participant->email,
                ]);

                return response()->json([
                    'message' => 'La question a été ajoutée, mais une erreur est survenue lors de l\'envoi de l\'email.',
                    'error' => $e->getMessage(),
                ], 500);
            }
        } else {
            return response()->json([
                'message' => 'La question a été ajoutée, mais l\'email du participant est invalide.',
            ], 422);
        }
    }

    /**
     * 
     *
     * @param string 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getQuestionsByToken($token)
    {
        if (strlen($token) !== 60 || !preg_match('/^[A-Za-z0-9]+$/', $token)) {
            return response()->json([
                'message' => 'Token mal formé.',
                'debug_info' => [
                    'token_reçu' => $token,
                    'format_attendu' => '60 caractères alphanumériques',
                ],
            ], 400); 
        }

        $sondage = Sondage::where('token', $token)->first();

        if (!$sondage) {
            $similarTokens = $this->findSimilarTokens($token);

            if (is_array($similarTokens) && count($similarTokens) > 0) {
                return response()->json([
                    'message' => 'Token introuvable. Voici des suggestions de tokens similaires.',
                    'suggestions' => $similarTokens,
                ], 404); 
            }

            return response()->json([
                'message' => 'Token introuvable et aucune suggestion similaire trouvée.',
                'debug_info' => [
                    'token_reçu' => $token,
                    'tokens_dans_db' => Sondage::pluck('token')->toArray(),
                ],
            ], 404); 
        }

        $evenement = Evenement::where('id', $sondage->evenement_id)->first();

        return response()->json([
            'evenement' => $evenement,
            'questions' => $sondage->questions,
        ]);
    }

    /**
     * 
     *
     * @param  string  
     * @return array
     */
    public function findSimilarTokens($token)
    {
        $tokens = DB::table('sondages')->pluck('token')->toArray();

        $similarTokens = [];
        foreach ($tokens as $dbToken) {
            $distance = levenshtein($token, $dbToken);

            if ($distance <= 5) {
                $similarTokens[] = $dbToken;
            }
        }

        return $similarTokens;
    }

    public function getToken()
    {
        try {
           
            $sondage = Sondage::latest()->first();
            
            if (!$sondage || !$sondage->token) {
                return response()->json([
                    'message' => 'Aucun token trouvé',
                    'debug_info' => [
                        'table' => 'sondages',
                        'sql' => 'SELECT * FROM sondages ORDER BY created_at DESC LIMIT 1'
                    ]
                ], 404);
            }
    
            return response()->json([
                'token' => $sondage->token,
                'message' => 'Token récupéré avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la récupération du token',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function getEventDetails($id)
{
    $evenement = Evenement::with(['questions' => function($query) {
        $query->with('reponses'); 
    }])->find($id);

    if (!$evenement) {
        return response()->json(['message' => 'Événement introuvable'], 404);
    }

    return response()->json([
        'success' => true,
        'data' => [
            'event' => [
                'id' => $evenement->id,
                'name' => $evenement->name,
                'image_url' => asset('storage/' . $evenement->image),
                'image_name' => $evenement->image,
            ],
            'questions' => $evenement->questions->map(function($question) {
                return [
                    'id' => $question->id,
                    'texte' => $question->texte,
                    'type' => $question->type,
                    'reponses' => $question->reponses->map(function($reponse) {
                        return [
                            'id' => $reponse->id,
                            'texte' => $reponse->texte,
                            'is_correct' => $reponse->is_correct
                        ];
                    })
                ];
            })
        ]
    ]);
}

    public function submitAnswers(Request $request, $token)
    {
        $validator = Validator::make($request->all(), [
            'answers' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Données invalides.'], 400);
        }

        $sondage = Sondage::where('token', $token)->first();
        if (!$sondage) {
            return response()->json(['message' => 'Sondage non trouvé.'], 404);
        }

        foreach ($request->answers as $questionId => $reponse) {
            Reponse::create([
                'sondage_id' => $sondage->id,
                'question_id' => $questionId,
                'reponse' => is_array($reponse) ? json_encode($reponse) : $reponse,
                // 'participant_id' => $request->participant_id,
            ]);
        }

        return response()->json(['message' => 'Réponses enregistrées avec succès.'], 200);
    }
    public function getAnswers($id)
{
    $sondage = Sondage::with('questions.reponses')->find($id);

    if (!$sondage) {
        return response()->json(['message' => 'Sondage non trouvé.'], 404);
    }

    return response()->json($sondage);
}

public function getSondageResultsByEvent($evenementId)
{
    $sondages = Sondage::whereHas('evenement', function($query) use ($evenementId) {
        $query->where('evenement_id', $evenementId);  
    })->get();
    
    

    $resultats = [];

    foreach ($sondages as $sondage) {
        $questions = $sondage->questions;
        
        foreach ($questions as $question) {
           
            $reponses = Reponse::where('evenement_id', $evenementId)
                ->where('question_id', $question->id)
                ->get();
            
           
            $totalReponses = $reponses->count();

            
            $reponsesCount = $reponses->groupBy('reponse')->map(function ($group) {
                return $group->count();
            });

            $pourcentages = [];

            foreach ($reponsesCount as $reponse => $count) {
                $pourcentages[$reponse] = $totalReponses > 0 
                    ? ($count / $totalReponses) * 100 
                    : 0;
            }

            $resultats[] = [
                'sondage' => $sondage->nom,
                'question' => $question->texte,
                'pourcentages' => $pourcentages,
            ];
        }
    }

    return response()->json($resultats);

}

//     public function analyseSondage($id)
//     {
//         $sondage = Sondage::with('questions.reponses')->find($id);

//         if (!$sondage) {
//             return response()->json(['message' => 'Sondage non trouvé.'], 404);
//         }

//         Log::info('Sondage trouvé : ' . $sondage->id);

//         $resultats = [];

//         foreach ($sondage->questions as $question) {
//             $totalReponses = $question->reponses->count();

//             $reponsesCount = $question->reponses->groupBy('texte')->map(function ($group) {
//                 return $group->count();
//             });

//             $pourcentages = [];
//             foreach ($reponsesCount as $texte => $count) {
//                 $pourcentages[$texte] = $totalReponses > 0 ? ($count / $totalReponses) * 100 : 0;
//             }

//             $resultats[] = [
//                 'question' => $question->texte,
//                 'pourcentages' => $pourcentages,
//             ];
//         }

//         Log::info('Résultats générés : ', $resultats);

//         return response()->json([
//             'sondage' => $sondage->nom,
//             'resultats' => $resultats,
//         ]);
//     }
//     public function mount($id)
// {
//     $this->resultats = app(SondageController::class)->analyseSondage($id)->original;
// }
}





