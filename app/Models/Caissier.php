<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Caissier extends Model
{
    // Ajoute ces lignes :
    protected $fillable = [
        'nom',
        'actif',
    ];
}
