<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProduitController;
use App\Http\Controllers\CommandeController;

// La page d'accueil affiche maintenant notre interface de caisse
Route::get('/', [ProduitController::class, 'index'])->name('accueil');

// Toutes les autres routes pour les produits
Route::resource('produits', ProduitController::class);

Route::post('/commandes', [CommandeController::class, 'store'])->name('commandes.store');

Route::delete('/produits/{id}', [ProduitController::class, 'destroy'])->name('produits.destroy');

Route::put('/produits/{id}', [ProduitController::class, 'update'])->name('produits.update');