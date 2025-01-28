<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sondages', function (Blueprint $table) {
            $table->string('titre')->nullable()->change(); // Permet des valeurs NULL
        });
    }

    public function down(): void
    {
        Schema::table('sondages', function (Blueprint $table) {
            $table->string('titre')->nullable(false)->change(); // Revenir à l'état initial
        });
    }
};
