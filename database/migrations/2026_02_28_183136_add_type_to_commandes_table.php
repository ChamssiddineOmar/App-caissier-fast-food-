<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('commandes', function (Blueprint $table) {
            // On ajoute la colonne 'type' juste après l'ID ou le caissier
            // On met 'Sur Place' par défaut pour ne pas casser les anciennes données
            $table->string('type')->default('Sur Place')->after('caissier');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('commandes', function (Blueprint $table) {
            // On supprime la colonne si on fait un rollback
            $table->dropColumn('type');
        });
    }
};