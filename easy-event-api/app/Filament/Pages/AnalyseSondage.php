<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Sondage;
use App\Models\Question;
use App\Models\Reponse;


class AnalyseSondage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar'; 
    protected static string $view = 'filament.pages.analyse--sondage'; 
    protected static bool $shouldRegisterNavigation = false;

    public $sondage ; 
    public $question;
    public $reponse; 

  

    // Fonction mount pour initialiser la page
    public function mount($sondageId): void
    {
        // Recherche du sondage et des questions liées
        $this->sondage = Sondage::with('questions.reponses')->find($sondageId);
        $this->question = Question::where('sondage_id', $this->sondage->id)->get();
        

        // Si le sondage n'est pas trouvé, on retourne une erreur 404
        if (!$this->sondage) {
            abort(404, 'Sondage introuvable');
        }

      
    }

   
}
