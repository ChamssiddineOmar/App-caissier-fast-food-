<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Commande;
use App\Models\CommandeProduit; // On utilise ton modèle
use Illuminate\Support\Facades\DB;

class StatistiqueController extends Controller
{
    public function index()
    {
        $ca_du_jour = Commande::whereDate('created_at', today())->sum('total');
        
        $dernieres_ventes = Commande::whereDate('created_at', today())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.stats', compact('ca_du_jour', 'dernieres_ventes'));
    }

    public function details($id)
    {
        try {
            // On cherche les produits liés à la commande dans la table 'commande_produit'
            $details = CommandeProduit::where('commande_id', $id)->get();
            
            return response()->json($details);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}