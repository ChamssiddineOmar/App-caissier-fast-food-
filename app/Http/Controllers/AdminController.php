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
    // --- GESTION DES PRODUITS (ACCUEIL ADMIN) ---
    public function index()
    {
        $produits = Produit::all(); 

        // Calcul des données pour les 3 widgets du haut
        $ventes_totales = Commande::sum('total');
        $total_sur_place = Commande::where('type', 'Sur Place')->count();
        $total_emporter = Commande::where('type', 'À Emporter')->count();

        return view('admin.produits', compact(
            'produits', 
            'ventes_totales', 
            'total_sur_place', 
            'total_emporter'
        ));
    }

    // --- GESTION DES CAISSIERS ---
    
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
            'actif' => true 
        ]);

        return back()->with('success', 'Nouveau caissier enregistré !');
    }

    // 3. Le patron SUPPRIME un caissier
    public function destroyCaissier($id)
    {
        $caissier = Caissier::findOrFail($id);
        $caissier->delete();
        return back()->with('success', 'Caissier supprimé avec succès !');
    }

    // 4. Activer/Désactiver un caissier
    public function toggleCaissier($id)
    {
        $caissier = Caissier::findOrFail($id);
        $caissier->actif = !$caissier->actif;
        $caissier->save();

        return back()->with('success', 'Disponibilité du caissier mise à jour');
    }

    // --- ACTIONS SUR LES PRODUITS ---

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

    /**
     * Note: La méthode stats() a été retirée d'ici car elle est désormais 
     * gérée par StatistiqueController pour plus de clarté.
     */
}