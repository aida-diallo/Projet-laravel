<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = ['texte', 'type', 'sondages_id','options'];

    public function sondage()
    {
        return $this->belongsTo(Sondage::class, 'sondages_id');
    }
    public function reponses() 
    {
        return $this->hasMany(Reponse::class);
    }
     // Convertir les options en JSON
     public function setOptionsAttribute($value)
     {
         $this->attributes['options'] = json_encode($value);
     }
      // Convertir les options en tableau
    public function getOptionsAttribute($value)
    {
        return json_decode($value, true);
    }
    public function answers()
{
    return $this->hasMany(Answer::class); // Si vous avez un modèle Answer
}


public function event()
{
    return $this->belongsTo(Event::class);
}

 
    use HasFactory;
}
