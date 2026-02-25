<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produit;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    // Affiche la liste des produits avec les options de gestion (Nouveau, Modifier, Supprimer)
    public function index()
    {
        $produits = Produit::all(); 
        return view('admin.produits', compact('produits'));
    }

    // Gère l'ajout d'un nouveau produit (déplacé depuis ProduitController pour la sécurité)
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

    // Gère la mise à jour d'un produit (Changement de prix, nom, etc.)
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
            // On supprime l'ancienne image si elle existe
            if ($produit->image) {
                Storage::disk('public')->delete($produit->image);
            }
            $data['image'] = $request->file('image')->store('produits', 'public');
        }

        $produit->update($data);

        return redirect()->route('admin.produits')->with('success', 'Produit mis à jour !');
    }

    // Supprime définitivement un produit
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