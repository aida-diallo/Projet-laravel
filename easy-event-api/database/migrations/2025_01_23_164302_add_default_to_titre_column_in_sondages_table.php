<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sondages', function (Blueprint $table) {
            $table->string('titre')->default('Sans titre')->change(); // Ajoute une valeur par défaut
        });
    }

    public function down(): void
    {
        Schema::table('sondages', function (Blueprint $table) {
            $table->string('titre')->default(null)->change(); // Supprime la valeur par défaut
        });
    }
};
