<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Participant extends Model
{
    use HasFactory;

    protected $fillable = [
        'telephone',
        'user_id',
        'badge',
        'survey_token',
        'token_expiration',
    ];

    // Relation avec l'utilisateur
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function events()
    {
        return $this->belongsToMany(
            Evenement::class,
            'evenement_participant',
            'participant_id',
            'evenement_id'
        )
        ->withPivot('dateInscription', 'status')
        ->withTimestamps();
    }

    public function generateToken()
    {
        $this->survey_token = Str::random(60);
        $this->token_expiration = now()->addDays(7); 
        $this->save();
    }

    public function reponses()
    {
        return $this->hasMany(Reponse::class);
    }
}
