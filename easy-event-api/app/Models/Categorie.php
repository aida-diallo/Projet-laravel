<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Categorie extends Model
{
    use HasFactory;
     
    protected $fillable = ['nom'];

    public static $rules = [
        'nom' => 'required|unique:categories|max:255',
    ];

    public function evenements()
    {
        return $this->hasMany(Evenement::class, 'categorie_id');
    }

    public function getNomAttribute($value)
    {
        return ucfirst($value);  
    }

    public function evenementsAVenir()
    {
        return $this->evenements()->where('date', '>', now()->toDateString());
    }

    public function evenementsPasses()
    {
        return $this->evenements()->where('date', '<', now()->toDateString());
    }

    public function logEvenements()
    {
        foreach ($this->evenements as $evenement) {
            Log::info("Événement dans la catégorie : ", ['id' => $evenement->id, 'nom' => $evenement->nom]);
        }
    }
}
