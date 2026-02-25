<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Commande;
use App\Models\CommandeProduit;
use Illuminate\Support\Facades\DB;

class CommandeController extends Controller
{
    public function store(Request $request)
{
    $request->validate([
        'total' => 'required|numeric',
        'caissier' => 'required|string',
        'panier' => 'required|array'
    ]);

    try {
        DB::transaction(function () use ($request) {
            // 1. Création de la commande
            $commande = Commande::create([
                'total' => $request->total,
                'caissier' => $request->caissier,
                'statut' => 'payé'
            ]);

            // 2. Enregistrement des produits détaillés
            foreach ($request->panier as $item) {
                CommandeProduit::create([
                    'commande_id' => $commande->id,
                    'nom_produit' => $item['nom'],
                    'quantite' => $item['qte'],
                    'prix_unitaire' => $item['prix']
                ]);
            }
        });

        return response()->json(['success' => true, 'message' => 'Vente enregistrée'], 201);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}
}