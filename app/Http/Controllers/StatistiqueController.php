<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Commande;
use Illuminate\Support\Facades\DB;

class StatistiqueController extends Controller
{
    public function index()
    {
        // 1. CA Aujourd'hui (Fonctionne déjà)
        $ca_du_jour = Commande::whereDate('created_at', today())->sum('total');

        // 2. On crée des collections vides pour éviter que la vue ne plante
        // Nous les remplirons quand tu auras une table "commande_items"
        $stats_categories = collect(); 
        $top_produits = collect();

        return view('admin.stats', compact('ca_du_jour', 'stats_categories', 'top_produits'));
    }
}