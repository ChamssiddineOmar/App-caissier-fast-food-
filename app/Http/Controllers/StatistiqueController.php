<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Commande;
use App\Models\CommandeProduit;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatistiqueController extends Controller
{
   public function index()
{
    // 1. Chiffre d'affaires total (Aujourd'hui)
    $ca_du_jour = Commande::whereDate('created_at', now())->sum('total');

    // 2. Nombre de commandes (Aujourd'hui)
    $nb_commandes = Commande::whereDate('created_at', now())->count();

    // 3. Panier moyen (Aujourd'hui)
    $panier_moyen = $nb_commandes > 0 ? $ca_du_jour / $nb_commandes : 0;

    // 4. Top 5 des produits les plus vendus
    $top_produits = CommandeProduit::select('nom_produit', DB::raw('SUM(quantite) as total_vendu'))
        ->groupBy('nom_produit')
        ->orderBy('total_vendu', 'desc')
        ->take(5)
        ->get();

    // 5. Ventes par catégorie pour le graphique
    $stats_categories = DB::table('commande_produit')
        ->join('produits', 'commande_produit.nom_produit', '=', 'produits.nom')
        ->select('produits.categorie', DB::raw('SUM(commande_produit.quantite) as total'))
        ->groupBy('produits.categorie')
        ->get(); // <-- Corrigé ici

    return view('admin.stats', compact('ca_du_jour', 'nb_commandes', 'panier_moyen', 'top_produits', 'stats_categories'));
}
}