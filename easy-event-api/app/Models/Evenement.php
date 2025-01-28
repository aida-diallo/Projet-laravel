<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Jobs\SendSurveyEmail;
use Carbon\Carbon;

class Evenement extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'evenements';

    protected $fillable = [
        'nom',
        'description',
        'date',
        'heureDebut',
        'heureFin',
        'lieu',
        'image',
        'categorie_id',
        'user_id',
        'emails_sent',
        'emails_sent_at',
        'emails_count',
        'statut',
        'max_participants',
        'tarif',
    ];

    protected $casts = [
        'date' => 'date',
        'heureDebut' => 'datetime:H:i:s',
        'heureFin' => 'datetime:H:i:s',
        'emails_sent' => 'boolean',
        'emails_sent_at' => 'datetime',
        'tarif' => 'float',
        'max_participants' => 'integer',
        'emails_count' => 'integer',
    ];

    protected $attributes = [
        'emails_sent' => false,
        'emails_count' => 0,
    ];

    // Relations
    public function categorie()
    {
        return $this->belongsTo(Categorie::class, 'categorie_id');
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function participants()
    {
        return $this->belongsToMany(Participant::class, 'evenement_participant')
            ->withPivot('dateInscription', 'status')
            ->withTimestamps();
    }

    public function commentaires()
    {
        return $this->hasMany(Commentaire::class);
    }

    public function communications()
    {
        return $this->hasMany(Communication::class);
    }

    public function sondage()
{
    return $this->hasMany(Sondage::class);
}

    public function rapports()
    {
        return $this->hasMany(Rapport::class);
    }

    // Accessors
    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    public function getIsFinishedAttribute()
    {
        if (!$this->date || !$this->heureFin) {
            return false;
        }

        try {
            $heureFinEvenement = Carbon::createFromFormat('Y-m-d H:i:s', 
                $this->date->format('Y-m-d') . ' ' . $this->heureFin
            );
            return $heureFinEvenement->isPast();
        } catch (\Exception $e) {
            Log::error('Erreur lors du parsing de la date de fin', [
                'evenement_id' => $this->id,
                'date' => $this->date,
                'heureFin' => $this->heureFin,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

 

    public function getIsStartedAttribute()
    {
        if (!$this->date || !$this->heureDebut) {
            return false;
        }

        try {
            $heureDebutEvenement = Carbon::createFromFormat('Y-m-d H:i:s', 
                $this->date->format('Y-m-d') . ' ' . $this->heureDebut
            );
            return $heureDebutEvenement->isPast();
        } catch (\Exception $e) {
            Log::error('Erreur lors du parsing de la date de début', [
                'evenement_id' => $this->id,
                'date' => $this->date,
                'heureDebut' => $this->heureDebut,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    // Scopes
    public function scopeTermines($query)
    {
        return $query->where(function($q) {
            $q->where('date', '<', now()->toDateString())
                ->orWhere(function ($subQ) {
                    $subQ->where('date', '=', now()->toDateString())
                         ->where('heureFin', '<', now()->toTimeString());
                });
        });
    }

    public function scopeAVenir($query)
    {
        return $query->where(function($q) {
            $q->where('date', '>', now()->toDateString())
                ->orWhere(function ($subQ) {
                    $subQ->where('date', '=', now()->toDateString())
                         ->where('heureDebut', '>', now()->toTimeString());
                });
        });
    }

    public function scopeEnCours($query)
    {
        return $query->where('date', '=', now()->toDateString())
            ->where('heureDebut', '<=', now()->toTimeString())
            ->where('heureFin', '>=', now()->toTimeString());
    }

    // Observers
    protected static function booted()
    {
        static::created(function ($evenement) {
            Log::info("Nouvel événement créé", [
                'id' => $evenement->id, 
                'nom' => $evenement->nom
            ]);
        });

        static::updated(function ($evenement) {
            if (!$evenement->date || !$evenement->heureFin) {
                return;
            }

            try {
                $heureFinEvenement = Carbon::createFromFormat('Y-m-d H:i:s', 
                    $evenement->date->format('Y-m-d') . ' ' . $evenement->heureFin
                );

                if ($heureFinEvenement->isPast() &&
                    $heureFinEvenement->diffInMinutes(now()) <= 2 &&
                    !$evenement->emails_sent) {
                    Log::info("Démarrage de l'envoi des emails pour l'événement", [
                        'id' => $evenement->id
                    ]);
                    
                    $evenement->envoyerEmailsSondage();
                }
            } catch (\Exception $e) {
                Log::error('Erreur lors du traitement de la mise à jour', [
                    'evenement_id' => $evenement->id,
                    'error' => $e->getMessage()
                ]);
            }
        });

        static::deleting(function ($evenement) {
            Log::info("Suppression de l'événement", [
                'id' => $evenement->id,
                'nom' => $evenement->nom
            ]);

            if ($evenement->image) {
                Storage::delete('public/' . $evenement->image);
            }

            $evenement->participants()->detach();
            $evenement->commentaires()->delete();
            $evenement->communications()->delete();
            $evenement->sondages()->delete();
            $evenement->rapports()->delete();
        });
    }

    public function envoyerEmailsSondage()
    {
        $emailsSentCount = 0;
        $failedEmails = [];

        foreach ($this->participants as $participant) {
            try {
                SendSurveyEmail::dispatch($participant, $this);
                $emailsSentCount++;
                Log::info("Email envoyé avec succès", [
                    'email' => $participant->user->email,
                    'evenement_id' => $this->id
                ]);
            } catch (\Exception $e) {
                $failedEmails[] = $participant->user->email;
                Log::error("Erreur lors de l'envoi de l'email", [
                    'email' => $participant->user->email,
                    'evenement_id' => $this->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $this->update([
            'emails_sent' => true,
            'emails_sent_at' => now(),
            'emails_count' => $emailsSentCount,
        ]);

        if (count($failedEmails) > 0) {
            Log::warning("Certains emails n'ont pas pu être envoyés", [
                'evenement_id' => $this->id,
                'failed_emails' => $failedEmails
            ]);
        }

        
        

        return $emailsSentCount;
    }
}