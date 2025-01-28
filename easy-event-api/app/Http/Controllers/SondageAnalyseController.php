<?php

namespace App\Http\Controllers;

use App\Models\Sondage;
use App\Models\Question;
use App\Models\Reponse;
use App\Models\Evenement;
use App\Services\SondageAnalyseService;
use App\Exports\SondageResultatsExport;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SondageAnalyseController extends Controller
{
    protected $analyseService;

    public function __construct(SondageAnalyseService $analyseService)
    {
        $this->analyseService = $analyseService;
    }

    public function analyseResultats($evenementId): JsonResponse
    {
        try {
            $evenement = Evenement::with(['questions.reponses', 'sondage'])
                ->findOrFail($evenementId);

            $resultats = $this->analyseService->analyserResultatsParQuestion($evenement);
            $statsGenerales = $this->analyseService->calculerStatistiquesGenerales($evenement);

            return response()->json([
                'evenement' => [
                    'id' => $evenement->id,
                    'name' => $evenement->name
                ],
                'statistiques_generales' => $statsGenerales,
                'resultats_detailles' => $resultats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Une erreur est survenue lors de l\'analyse des résultats',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function exporterResultatsCSV($evenementId): StreamedResponse
    {
        try {
            $evenement = Evenement::with(['questions.reponses'])->findOrFail($evenementId);
            
            return $this->analyseService->exporterResultatsCSV($evenement);

        } catch (\Exception $e) {
            abort(500, 'Erreur lors de l\'export des résultats');
        }
    }
}

