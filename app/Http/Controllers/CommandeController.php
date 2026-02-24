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
        // Validation des données entrantes
        $request->validate([
            'total' => 'required|numeric',
            'caissier' => 'required|string',
            'panier' => 'required|array'
        ]);

        try {
            // Utilisation d'une transaction pour la sécurité des données
            return DB::transaction(function () use ($request) {
                
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

                return response()->json(['message' => 'Vente enregistrée avec succès'], 201);
            });
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erreur: ' . $e->getMessage()], 500);
        }
    }
}