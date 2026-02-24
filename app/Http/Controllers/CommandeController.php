<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Commande;
use App\Models\LigneCommande;
use Illuminate\Support\Facades\DB;

class CommandeController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validation de base
        $request->validate([
            'total' => 'required|numeric',
            'panier' => 'required|array'
        ]);

        try {
            // 2. Utilisation d'une Transaction pour éviter les erreurs partielles
            return DB::transaction(function () use ($request) {
                
                // Création de la commande principale
                $commande = Commande::create([
                    'total' => $request->total,
                    'caissier' => 'OMAR', // On pourra lier cela à l'utilisateur connecté plus tard
                ]);

                // Création des lignes de détails
                foreach ($request->panier as $item) {
                    LigneCommande::create([
                        'commande_id' => $commande->id,
                        'produit_nom' => $item['nom'],
                        'quantite'    => $item['quantite'],
                        'prix_unitaire' => $item['prix'],
                    ]);
                }

                return response()->json([
                    'status' => 'success',
                    'message' => 'Commande enregistrée avec succès !',
                    'commande_id' => $commande->id
                ], 201);
            });

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de l\'enregistrement : ' . $e->getMessage()
            ], 500);
        }
    }
}