<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProduitController;
use App\Http\Controllers\CommandeController;
use App\Http\Controllers\StatistiqueController;
use App\Http\Controllers\AdminController;


// --- INTERFACE CAISSE (ACCÈS CAISSIER) ---
// La page d'accueil affiche uniquement l'interface de vente épurée
Route::get('/', [ProduitController::class, 'index'])->name('accueil');

// Route pour enregistrer une vente (utilisée par le bouton "Payer")
Route::post('/commandes/store', [CommandeController::class, 'store'])->name('commandes.store');

/// --- ESPACE PATRON (ACCÈS SÉCURISÉ) ---
Route::prefix('admin')->group(function () {
    
    Route::get('/produits', [AdminController::class, 'index'])->name('admin.produits');
    Route::get('/stats', [StatistiqueController::class, 'index'])->name('admin.stats');

    // Utilise AdminController ici aussi pour que tout soit centralisé
    Route::post('/produits', [AdminController::class, 'store'])->name('produits.store');
    Route::put('/produits/{id}', [AdminController::class, 'update'])->name('produits.update');
    Route::delete('/produits/{id}', [AdminController::class, 'destroy'])->name('produits.destroy');

    Route::post('/commandes/store', [CommandeController::class, 'store']);
});