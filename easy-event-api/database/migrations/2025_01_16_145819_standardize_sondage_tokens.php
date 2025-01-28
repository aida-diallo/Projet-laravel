<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;
use App\Models\Sondage;

return new class extends Migration
{
    public function up()
    {
        // Convertir tous les tokens UUID en chaînes aléatoires de 60 caractères
        $sondages = Sondage::whereRaw('LENGTH(token) = 36')->get();
        
        foreach ($sondages as $sondage) {
            $sondage->update([
                'token' => Str::random(60)
            ]);
        }
    }

    public function down()
    {
        // Pas de down car on ne peut pas restaurer les anciens UUIDs
    }
};