<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sondages', function (Blueprint $table) {
            $table->unsignedBigInteger('evenement_id')->nullable()->after('id'); // Place la colonne après 'id'
        });
    }

    public function down(): void
    {
        Schema::table('sondages', function (Blueprint $table) {
            $table->dropColumn('evenement_id');
        });
    }
};
