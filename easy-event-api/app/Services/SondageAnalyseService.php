<?php
// Nouveau service pour la logique métier
namespace App\Services;

class SondageAnalyseService
{
    public function analyserResultatsParQuestion(Evenement $evenement): array
    {
        $resultats = [];
        
        foreach ($evenement->questions as $question) {
            $reponses = $question->reponses;
            $totalReponses = $reponses->count();
            
            $reponsesGroupees = $this->grouperEtCalculerPourcentages($reponses, $totalReponses);

            $resultats[] = [
                'question' => $this->formatQuestionData($question),
                'statistiques' => [
                    'total_reponses' => $totalReponses,
                    'details' => $reponsesGroupees
                ]
            ];
        }

        return $resultats;
    }

    protected function grouperEtCalculerPourcentages($reponses, $totalReponses): array
    {
        return $reponses->groupBy('reponse')
            ->map(function ($groupe) use ($totalReponses) {
                $count = $groupe->count();
                return [
                    'count' => $count,
                    'pourcentage' => $this->calculerPourcentage($count, $totalReponses)
                ];
            })->toArray();
    }

    protected function calculerPourcentage($count, $total): float
    {
        return $total > 0 ? round(($count / $total) * 100, 2) : 0;
    }

    protected function formatQuestionData(Question $question): array
    {
        return [
            'id' => $question->id,
            'texte' => $question->texte,
            'type' => $question->type
        ];
    }

    public function calculerStatistiquesGenerales(Evenement $evenement): array
    {
        $totalQuestions = $evenement->questions->count();
        $totalParticipants = $this->compterParticipantsUniques($evenement);
        
        return [
            'total_questions' => $totalQuestions,
            'total_participants' => $totalParticipants,
            'taux_participation' => $this->calculerTauxParticipation(
                $evenement, 
                $totalQuestions, 
                $totalParticipants
            )
        ];
    }

    protected function compterParticipantsUniques(Evenement $evenement): int
    {
        return Reponse::where('sondage_id', $evenement->sondage->id)
            ->select('participant_id')
            ->distinct()
            ->count();
    }

    protected function calculerTauxParticipation(
        Evenement $evenement,
        int $totalQuestions,
        int $totalParticipants
    ): float {
        $totalReponsesRecues = Reponse::where('sondage_id', $evenement->sondage->id)->count();
        $totalPotentiel = $totalParticipants * $totalQuestions;
        
        return $totalPotentiel > 0 ? round(($totalReponsesRecues / $totalPotentiel) * 100, 2) : 0;
    }

    public function exporterResultatsCSV(Evenement $evenement): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="resultats_sondage_' . $evenement->id . '.csv"',
        ];
        
        return response()->stream(
            function() use ($evenement) {
                $file = fopen('php://output', 'w');
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                
                fputcsv($file, ['Question', 'Réponse', 'Nombre de réponses', 'Pourcentage']);
                
                foreach ($evenement->questions as $question) {
                    $this->ecrireResultatsQuestion($file, $question);
                }
                
                fclose($file);
            },
            200,
            $headers
        );
    }

    protected function ecrireResultatsQuestion($file, Question $question): void
    {
        $reponses = $question->reponses;
        $totalReponses = $reponses->count();
        
        foreach ($reponses->groupBy('reponse') as $reponse => $groupe) {
            $count = $groupe->count();
            $pourcentage = $this->calculerPourcentage($count, $totalReponses);
            
            fputcsv($file, [
                $question->texte,
                $reponse,
                $count,
                $pourcentage . '%'
            ]);
        }

        fputcsv($file, []);
    }
}