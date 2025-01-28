<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reponses', function (Blueprint $table) {
            $table->unsignedBigInteger('evenement_id')->nullable()->change(); // Permet d'accepter NULL
        });
    }

    public function down(): void
    {
        Schema::table('reponses', function (Blueprint $table) {
            $table->unsignedBigInteger('evenement_id')->nullable(false)->change(); // Revenir à NOT NULL
        });
    }
};
