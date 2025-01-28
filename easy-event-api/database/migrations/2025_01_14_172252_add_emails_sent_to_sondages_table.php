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
        Schema::table('sondages', function (Blueprint $table) {
            $table->boolean('emails_sent')->default(false); // Par défaut, les emails ne sont pas envoyés
        });
    }
    
    public function down()
    {
        Schema::table('sondages', function (Blueprint $table) {
            $table->dropColumn('emails_sent');
        });
    }
    
};
