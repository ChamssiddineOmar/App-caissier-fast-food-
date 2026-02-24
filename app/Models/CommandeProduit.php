<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommandeProduit extends Model
{
    use HasFactory;

    // IMPORTANT : On dit à Laravel d'utiliser le nom exact de ta table
    protected $table = 'commande_produit';

    // Champs que l'on autorise à remplir via le formulaire/panier
    protected $fillable = [
        'commande_id', 
        'nom_produit', 
        'quantite', 
        'prix_unitaire'
    ];

    public function commande()
    {
        return $this->belongsTo(Commande::class);
    }
}