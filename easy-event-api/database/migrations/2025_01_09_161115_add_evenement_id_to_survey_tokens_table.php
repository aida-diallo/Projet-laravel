<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('survey_tokens', function (Blueprint $table) {
        $table->unsignedBigInteger('evenement_id')->after('id'); // Ajoutez l'emplacement approprié
        $table->foreign('evenement_id')->references('id')->on('evenements')->onDelete('cascade');
    });
}

public function down()
{
    Schema::table('survey_tokens', function (Blueprint $table) {
        $table->dropForeign(['evenement_id']);
        $table->dropColumn('evenement_id');
    });
}

};
