<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use App\Models\Caissier; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProduitController extends Controller
{
    /**
     * Affiche la page de la caisse avec les produits et les caissiers synchronisés.
     */
    public function index() {
        // Récupération de tous les produits pour la grille de vente
        $produits = Produit::all();

        // Récupération des caissiers actifs uniquement
        // On les trie par nom pour que la liste soit propre dans le menu déroulant
        $caissiers = Caissier::where('actif', true)->orderBy('nom', 'asc')->get();

        // Envoi des données à la vue 'produits.index'
        return view('produits.index', compact('produits', 'caissiers'));
    }

    /**
     * Ajoute un nouveau produit au menu.
     */
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

        return redirect()->back()->with('success', 'Produit ajouté avec succès !');
    }

    /**
     * Supprime un produit et son image associée.
     */
    public function destroy($id)
    {
        $produit = Produit::findOrFail($id);
        
        // Suppression physique du fichier image du stockage public
        if($produit->image) {
            Storage::disk('public')->delete($produit->image);
        }

        $produit->delete();

        return redirect()->back()->with('success', 'Produit retiré du menu');
    }

    /**
     * Met à jour les informations d'un produit.
     */
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

        // Si une nouvelle image est téléchargée, on remplace l'ancienne
        if ($request->hasFile('image')) {
            if($produit->image) { 
                Storage::disk('public')->delete($produit->image); 
            }
            $produit->image = $request->file('image')->store('produits', 'public');
        }

        $produit->save();

        return redirect()->back()->with('success', 'Article mis à jour !');
    }
}