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
  public function stats()
{
    $ca_du_jour = Commande::whereDate('created_at', today())->sum('total');
    
    // On récupère les dernières commandes avec leurs produits
    $dernieres_ventes = Commande::orderBy('created_at', 'desc')->take(10)->get();

    return view('admin.stats', compact('ca_du_jour', 'dernieres_ventes'));
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
}