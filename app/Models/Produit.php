<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produit extends Model
{
    // Ajoute ces lignes :
    protected $fillable = ['nom', 'prix', 'categorie', 'image', 'description', 'categorie_id'];
}