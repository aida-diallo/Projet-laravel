<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rapport extends Model
{
    use HasFactory;
    protected $fillable = [
        'nb_participants',
        'tauxSatisfaction',
        'revenue',
        'format',
        'evenement_id'
    ];



    public function event()
    {
        return $this->belongsTo(Evenement::class);
    }
}
