<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sondages', function (Blueprint $table) {
            if (Schema::hasColumn('sondages', 'questionnaire')) {
                $table->string('questionnaire')->nullable()->change(); // Autorise NULL
            } else {
                // Ajout de la colonne si elle n'existe pas
                $table->string('questionnaire')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('sondages', function (Blueprint $table) {
            if (Schema::hasColumn('sondages', 'questionnaire')) {
                $table->string('questionnaire')->nullable(false)->change(); // Revient à obligatoire
            }
        });
    }
};
