<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reponses', function (Blueprint $table) {
            if (!Schema::hasColumn('reponses', 'evenement_id')) {
                $table->unsignedBigInteger('evenement_id')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('reponses', function (Blueprint $table) {
            if (Schema::hasColumn('reponses', 'evenement_id')) {
                $table->dropColumn('evenement_id');
            }
        });
    }
};
