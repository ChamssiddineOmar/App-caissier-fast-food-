<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produit;
use App\Models\Commande;
use App\Models\Caissier; 
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    // --- PARTIE STATISTIQUES ---
    public function stats(Request $request)
    {
        $moisChoisi = $request->get('mois', date('Y-m'));
        $annee = substr($moisChoisi, 0, 4);
        $mois = substr($moisChoisi, 5, 2);

        $ventes = Commande::whereYear('created_at', $annee)
                            ->whereMonth('created_at', $mois)
                            ->latest()
                            ->get();

        $ca_mensuel = $ventes->sum('total');

        $top_produits = DB::table('commande_produit')
            ->select('nom_produit', DB::raw('SUM(quantite) as total_quantite'), DB::raw('SUM(prix_unitaire * quantite) as total_revenu'))
            ->whereYear('created_at', $annee)
            ->whereMonth('created_at', $mois)
            ->groupBy('nom_produit')
            ->orderBy('total_revenu', 'desc')
            ->limit(5)
            ->get();

        return view('admin.stats', compact('ventes', 'ca_mensuel', 'top_produits', 'moisChoisi'));
    }

    // --- GESTION DES CAISSIERS (MISE À JOUR) ---
    
    // 1. Affiche la page de gestion au patron
    public function gestionCaissiers()
    {
        $caissiers = Caissier::orderBy('created_at', 'desc')->get();
        return view('admin.caissiers', compact('caissiers'));
    }

    // 2. Le patron AJOUTE un caissier
    public function storeCaissier(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255|unique:caissiers,nom',
        ]);

        Caissier::create([
            'nom' => $request->nom,
            'actif' => true // Par défaut, il apparaît à la caisse
        ]);

        return back()->with('success', 'Nouveau caissier enregistré !');
    }

    // 3. Le patron SUPPRIME ou DÉSACTIVE un caissier
    public function destroyCaissier($id)
    {
        $caissier = Caissier::findOrFail($id);
        
        // On vérifie si ce caissier a déjà travaillé (pour ne pas corrompre les stats)
        // Si tu n'as pas encore lié les commandes aux caissiers, delete() suffit
        $caissier->delete();

        return back()->with('success', 'Caissier supprimé avec succès !');
    }

    // 4. Option : Activer/Désactiver (pour cacher un caissier sans le supprimer)
    public function toggleCaissier($id)
    {
        $caissier = Caissier::findOrFail($id);
        $caissier->actif = !$caissier->actif;
        $caissier->save();

        return back()->with('success', 'Disponibilité du caissier mise à jour');
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
        $produit->en_stock = !$produit->en_stock;
        $produit->save();

        return back()->with('success', 'État du stock mis à jour');
    }
}