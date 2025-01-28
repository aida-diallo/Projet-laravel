<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sondages', function (Blueprint $table) {
            if (!Schema::hasColumn('sondages', 'token')) {
                $table->string('token')->after('questionnaire')->nullable(false);
            }
        });
    }

    public function down(): void
    {
        Schema::table('sondages', function (Blueprint $table) {
            if (Schema::hasColumn('sondages', 'token')) {
                $table->dropColumn('token');
            }
        });
    }
};
