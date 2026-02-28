<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commande extends Model
{
    use HasFactory;

    // Ajout de 'type' pour permettre l'enregistrement (Sur Place / Emporter)
    protected $fillable = ['total', 'caissier', 'type', 'statut'];

    /**
     * Les attributs qui doivent être convertis.
     * Utile pour manipuler les dates proprement dans les statistiques.
     */
    protected $casts = [
        'created_at' => 'datetime',
    ];

    // Relation : Une commande contient plusieurs produits
    public function produits()
    {
        return $this->hasMany(CommandeProduit::class, 'commande_id');
    }
}