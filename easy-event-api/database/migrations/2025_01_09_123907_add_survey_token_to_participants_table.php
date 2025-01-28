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
        Schema::table('participants', function (Blueprint $table) {
            $table->string('survey_token')->nullable();
            $table->timestamp('token_expiration')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('participants', function (Blueprint $table) {
            $table->dropColumn(['survey_token', 'token_expiration']);
        });
    }
    
    
};
