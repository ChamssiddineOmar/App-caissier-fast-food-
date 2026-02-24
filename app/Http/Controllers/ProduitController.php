<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use Illuminate\Http\Request;

class ProduitController extends Controller
{
    public function index() {
        $produits = Produit::all();
        return view('produits.index', compact('produits'));
    }

    public function store(Request $request) {
        $request->validate([
            'nom' => 'required|string',
            'prix' => 'required|numeric',
            'categorie' => 'required',
            'image' => 'image|nullable|max:2048'
        ]);

        $path = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('produits', 'public');
        }

        Produit::create([
            'nom' => $request->nom,
            'prix' => $request->prix,
            'categorie' => $request->categorie,
            'image' => $path,
            'disponible' => true
        ]);

        return redirect()->back()->with('success', 'Produit ajouté !');
    }

    public function destroy($id)
{
    $produit = Produit::findOrFail($id);
    
    // Optionnel : Supprimer l'image du stockage si elle existe
    if($produit->image) {
        \Storage::disk('public')->delete($produit->image);
    }

    $produit->delete();

    return redirect()->back()->with('success', 'Produit retiré du menu');
}

public function update(Request $request, $id)
{
    $produit = Produit::findOrFail($id);
    
    $request->validate([
        'nom' => 'required|string|max:255',
        'prix' => 'required|numeric',
        'categorie' => 'required',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
    ]);

    $produit->nom = $request->nom;
    $produit->prix = $request->prix;
    $produit->categorie = $request->categorie;

    if ($request->hasFile('image')) {
        // Supprimer l'ancienne image si elle existe
        if($produit->image) { \Storage::disk('public')->delete($produit->image); }
        $produit->image = $request->file('image')->store('produits', 'public');
    }

    $produit->save();

    return redirect()->back()->with('success', 'Article mis à jour !');
}
}