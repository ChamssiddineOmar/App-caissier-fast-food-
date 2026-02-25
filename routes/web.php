<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProduitController;
use App\Http\Controllers\CommandeController;
use App\Http\Controllers\StatistiqueController;
use App\Http\Controllers\AdminController;


// --- INTERFACE CAISSE (ACCÈS CAISSIER) ---
Route::get('/', [ProduitController::class, 'index'])->name('accueil');
Route::post('/commandes/store', [CommandeController::class, 'store'])->name('commandes.store');

// --- ESPACE PATRON (ACCÈS SÉCURISÉ) ---
Route::prefix('admin')->group(function () {
    
    Route::get('/produits', [AdminController::class, 'index'])->name('admin.produits');
    Route::get('/stats', [StatistiqueController::class, 'index'])->name('admin.stats');

    Route::post('/produits', [AdminController::class, 'store'])->name('produits.store');
    Route::put('/produits/{id}', [AdminController::class, 'update'])->name('produits.update');
    Route::delete('/produits/{id}', [AdminController::class, 'destroy'])->name('produits.destroy');

    // CORRECTION ICI : Pas besoin de répéter /admin car il est déjà dans le préfixe du groupe
    Route::get('/commandes/{id}/details', [StatistiqueController::class, 'details']);
    
});