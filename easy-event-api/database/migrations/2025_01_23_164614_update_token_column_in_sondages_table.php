<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sondages', function (Blueprint $table) {
            $table->text('token')->change(); // Change la colonne token en type TEXT
        });
    }

    public function down(): void
    {
        Schema::table('sondages', function (Blueprint $table) {
            $table->string('token', 255)->change(); // Revenir à VARCHAR si nécessaire
        });
    }
};
