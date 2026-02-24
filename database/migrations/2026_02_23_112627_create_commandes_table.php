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
    // Table principale des ventes
    Schema::create('commandes', function (Blueprint $table) {
        $table->id();
        $table->decimal('total', 10, 2);
        $table->string('caissier');
        $table->string('statut')->default('payé'); // payé, annulé
        $table->timestamps(); // Cela créera created_at (la date et l'heure de vente)
    });

    // Table de détail (pour savoir ce qu'il y avait dans la commande)
    Schema::create('commande_produit', function (Blueprint $table) {
        $table->id();
        $table->foreignId('commande_id')->constrained()->onDelete('cascade');
        $table->string('nom_produit');
        $table->integer('quantite');
        $table->decimal('prix_unitaire', 10, 2);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commandes');
    }
};
