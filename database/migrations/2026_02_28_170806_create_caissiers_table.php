<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
    Schema::create('caissiers', function (Blueprint $table) {
        $table->id();
        $table->string('nom');
        $table->boolean('actif')->default(true); // Pour pouvoir désactiver un caissier sans le supprimer
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('caissiers');
    }
};
