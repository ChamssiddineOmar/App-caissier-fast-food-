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
Route::prefix('admin')->group(function () {

    // 1. GESTION DES PRODUITS
    Route::get('/produits', [AdminController::class, 'index'])->name('admin.produits');
    Route::post('/produits/store', [AdminController::class, 'store'])->name('produits.store');
    Route::put('/produits/{id}', [AdminController::class, 'update'])->name('produits.update');
    Route::delete('/produits/{id}', [AdminController::class, 'destroy'])->name('produits.destroy');
    Route::post('/produits/{id}/toggle-stock', [AdminController::class, 'toggleStock'])->name('admin.produits.stock');

    // 2. GESTION DES CAISSIERS
    Route::get('/caissiers', [AdminController::class, 'gestionCaissiers'])->name('admin.caissiers');
    Route::post('/caissiers/store', [AdminController::class, 'storeCaissier'])->name('admin.caissiers.store');
    
    // --- LA LIGNE CORRIGÉE POUR L'ERREUR TOGGLE ---
    Route::post('/caissiers/{id}/toggle', [AdminController::class, 'toggleCaissier'])->name('admin.caissiers.toggle');
    
    Route::delete('/caissiers/{id}', [AdminController::class, 'destroyCaissier'])->name('admin.caissiers.destroy');

    // 3. BILAN FINANCIER & STATISTIQUES
    Route::get('/stats', [AdminController::class, 'stats'])->name('admin.stats');
    Route::get('/stats/export', [AdminController::class, 'exportStats'])->name('admin.stats.export');

    // 4. ACTIONS TECHNIQUES (AJAX)
    Route::get('/commandes/{id}/details', [StatistiqueController::class, 'details'])->name('admin.commandes.details');

});