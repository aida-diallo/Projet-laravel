<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSurveySentToEvenementsTable extends Migration
{
    public function up()
    {
        Schema::table('evenements', function (Blueprint $table) {
            $table->boolean('survey_sent')->default(false)->after('heureFin'); // Ajoute la colonne
        });
    }

    public function down()
    {
        Schema::table('evenements', function (Blueprint $table) {
            $table->dropColumn('survey_sent'); // Supprime la colonne si on rollback
        });
    }
}
