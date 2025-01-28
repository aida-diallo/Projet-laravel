<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'reponse',
        'question_id',
        // 'participant_id',
        'evenement_id'
    ];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }

    public function evenement()
    {
        return $this->belongsTo(Evenement::class);
    }

public function sondage()
{
    return $this->belongsTo(Sondage::class);
}
}