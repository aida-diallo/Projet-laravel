<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Sondage extends Model
{
    use HasFactory;
    protected $fillable = ['dateEnvoie', 'questionnaire', 'evenement_id', 'token'];

    protected $casts = [  
        'dateEnvoie' => 'datetime',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($sondage) {
            if (!$sondage->token) {
                $sondage->token = Str::random(60);
                
            }
        });
    }

    public function evenement()
    {
        return $this->belongsTo(Evenement::class, 'evenement_id'); 
    }
    

    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}