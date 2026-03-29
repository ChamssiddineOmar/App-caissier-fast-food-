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
        // On ajoute la colonne 'type' pour stocker "Sur Place" ou "À Emporter"
        $table->string('type')->default('Sur Place')->after('total');
    });
}

public function down(): void
{
    Schema::table('commandes', function (Blueprint $table) {
        $table->dropColumn('type');
    });
}
};
