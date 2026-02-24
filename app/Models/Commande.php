<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commande extends Model
{
    use HasFactory;

    protected $fillable = ['total', 'caissier', 'statut'];

    // Relation : Une commande contient plusieurs produits
    public function produits()
    {
        return $this->hasMany(CommandeProduit::class, 'commande_id');
    }
}