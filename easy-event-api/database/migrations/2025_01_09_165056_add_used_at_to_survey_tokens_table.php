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
            $table->timestamp('used_at')->nullable()->after('updated_at'); 
        });
    }
    
    public function down()
    {
        Schema::table('survey_tokens', function (Blueprint $table) {
            $table->dropColumn('used_at');
        });
    }
    
};
