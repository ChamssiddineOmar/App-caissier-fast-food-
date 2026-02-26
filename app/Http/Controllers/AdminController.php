<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produit;
use App\Models\Commande; // Ajouté pour les stats
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB; // Ajouté pour les requêtes complexes

class AdminController extends Controller
{
    // --- PARTIE STATISTIQUES (NOUVEAU) ---
 // --- PARTIE STATISTIQUES (RÉACTIVÉE) ---
public function stats(Request $request)
{
    // 1. Récupérer le mois choisi (par défaut le mois actuel)
    $moisChoisi = $request->get('mois', date('Y-m'));
    $annee = substr($moisChoisi, 0, 4);
    $mois = substr($moisChoisi, 5, 2);

    // 2. Récupérer les vraies commandes depuis la table 'commandes'
    $ventes = Commande::whereYear('created_at', $annee)
                        ->whereMonth('created_at', $mois)
                        ->latest()
                        ->get();

    // 3. Calcul du CA Mensuel réel
    $ca_mensuel = $ventes->sum('total');

    // 4. Top produits vendus (en utilisant la table 'commande_produits')
   // Remplace cette partie dans ton AdminController.php
$top_produits = DB::table('commande_produit') // Essaie sans le 's'
    ->select('nom_produit', DB::raw('SUM(quantite) as total_quantite'), DB::raw('SUM(prix_unitaire * quantite) as total_revenu'))
    ->whereYear('created_at', $annee)
    ->whereMonth('created_at', $mois)
    ->groupBy('nom_produit')
    ->orderBy('total_revenu', 'desc')
    ->limit(5)
    ->get();

    return view('admin.stats', compact('ventes', 'ca_mensuel', 'top_produits', 'moisChoisi'));
}
    // --- GESTION DES PRODUITS ---

    public function index()
    {
        $produits = Produit::all(); 
        return view('admin.produits', compact('produits'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required',
            'prix' => 'required|numeric',
            'categorie' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('produits', 'public');
        }

        Produit::create($data);

        return redirect()->route('admin.produits')->with('success', 'Produit ajouté avec succès !');
    }

    public function update(Request $request, $id)
    {
        $produit = Produit::findOrFail($id);
        
        $request->validate([
            'nom' => 'required',
            'prix' => 'required|numeric',
            'categorie' => 'required'
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            if ($produit->image) {
                Storage::disk('public')->delete($produit->image);
            }
            $data['image'] = $request->file('image')->store('produits', 'public');
        }

        $produit->update($data);

        return redirect()->route('admin.produits')->with('success', 'Produit mis à jour !');
    }

    public function destroy($id)
    {
        $produit = Produit::findOrFail($id);
        
        if ($produit->image) {
            Storage::disk('public')->delete($produit->image);
        }
        
        $produit->delete();

        return redirect()->route('admin.produits')->with('success', 'Produit supprimé !');
    }

    public function toggleStock($id)
{
    $produit = Produit::findOrFail($id);
    $produit->en_stock = !$produit->en_stock; // On inverse l'état
    $produit->save();

    return back()->with('success', 'État du stock mis à jour');
}


}