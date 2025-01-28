<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('evenements', function (Blueprint $table) {
            $table->integer('emails_count')->default(0);
            // Si ces colonnes n'existent pas non plus, ajoutez-les aussi
            if (!Schema::hasColumn('evenements', 'emails_sent')) {
                $table->boolean('emails_sent')->default(false);
            }
            if (!Schema::hasColumn('evenements', 'emails_sent_at')) {
                $table->timestamp('emails_sent_at')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('evenements', function (Blueprint $table) {
            $table->dropColumn('emails_count');
            // Si vous voulez pouvoir supprimer les autres colonnes aussi
            // $table->dropColumn(['emails_sent', 'emails_sent_at']);
        });
    }
};