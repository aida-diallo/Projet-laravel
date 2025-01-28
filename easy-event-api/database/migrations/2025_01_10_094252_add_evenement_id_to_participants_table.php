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
        $table->unsignedBigInteger('evenement_id')->nullable(); 
        $table->foreign('evenement_id')->references('id')->on('evenements')->onDelete('cascade'); 
    });
}

public function down()
{
    Schema::table('participants', function (Blueprint $table) {
        $table->dropForeign(['evenement_id']); 
        $table->dropColumn('evenement_id'); 
    });
}

};
