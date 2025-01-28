<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Communication extends Model
{
    use HasFactory;
    protected $fillable = ['dateEnvoie', 'type', 'message', 'evenement_id'];

    protected $casts = [
        'dateEnvoie' => 'date'
    ];

    public function evenement()
    {
        return $this->belongsTo(Evenement::class);
    }
}
