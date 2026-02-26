<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProduitController;
use App\Http\Controllers\CommandeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\StatistiqueController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- INTERFACE CAISSE (ACCÈS PUBLIC / CAISSIER) ---
Route::get('/', [ProduitController::class, 'index'])->name('accueil');
Route::post('/commandes/store', [CommandeController::class, 'store'])->name('commandes.store');


// --- ESPACE PATRON (ACCÈS ADMIN) ---
// On utilise le préfixe 'admin' pour regrouper toutes les pages de gestion
Route::prefix('admin')->group(function () {

    // 1. GESTION DES PRODUITS (Liste, Ajout, Modif, Suppression, Stock)
    Route::get('/produits', [AdminController::class, 'index'])->name('admin.produits');
    Route::post('/produits/store', [AdminController::class, 'store'])->name('produits.store');
    Route::put('/produits/{id}', [AdminController::class, 'update'])->name('produits.update');
    Route::delete('/produits/{id}', [AdminController::class, 'destroy'])->name('produits.destroy');
    Route::post('/produits/{id}/toggle-stock', [AdminController::class, 'toggleStock'])->name('admin.produits.stock');

    // 2. BILAN FINANCIER & STATISTIQUES
    // Une SEULE route pour les stats pour éviter les conflits de filtrage
    Route::get('/stats', [AdminController::class, 'stats'])->name('admin.stats');

    // 3. ACTIONS TECHNIQUES (AJAX)
    // Récupère les produits d'une commande pour la fenêtre surgissante (Modal)
    Route::get('/commandes/{id}/details', [StatistiqueController::class, 'details'])->name('admin.commandes.details');

});