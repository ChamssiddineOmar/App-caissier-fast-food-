<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Commande;
use App\Models\CommandeProduit;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatistiqueController extends Controller
{
    public function index(Request $request)
    {
        // 1. Récupération du mois (format Y-m) depuis la requête ou mois actuel
        $moisFiltre = $request->get('mois', date('Y-m'));
        
        // On crée un objet Carbon pour manipuler facilement l'année et le mois
        $date = Carbon::parse($moisFiltre);

        // 2. Chiffre d'affaires du jour (indépendant du filtre pour garder un œil sur aujourd'hui)
        $ca_du_jour = Commande::whereDate('created_at', Carbon::today())->sum('total');

        // 3. Récupération des ventes du mois sélectionné
        $ventes = Commande::whereYear('created_at', $date->year)
            ->whereMonth('created_at', $date->month)
            ->orderBy('created_at', 'desc')
            ->get();

        // 4. Calcul du Chiffre d'Affaires Mensuel
        $ca_mensuel = $ventes->sum('total');

        // 5. Statistiques des Top Produits pour ce mois précis
        $top_produits = CommandeProduit::select(
                'nom_produit', 
                DB::raw('SUM(quantite) as total_quantite'), 
                DB::raw('SUM(prix_unitaire * quantite) as total_revenu')
            )
            ->whereYear('created_at', $date->year)
            ->whereMonth('created_at', $date->month)
            ->groupBy('nom_produit')
            ->orderBy('total_revenu', 'desc')
            ->limit(5)
            ->get();

        // 6. Envoi des données à la vue
        return view('admin.stats', compact(
            'ventes', 
            'ca_mensuel', 
            'top_produits', 
            'ca_du_jour'
        ));
    }

    /**
     * Récupère les détails d'une commande via AJAX pour la Modal
     */
    public function details($id)
    {
        try {
            $details = CommandeProduit::where('commande_id', $id)->get();
            return response()->json($details);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors du chargement : ' . $e->getMessage()], 500);
        }
    }
}