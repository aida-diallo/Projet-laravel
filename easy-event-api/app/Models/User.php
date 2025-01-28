<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel; 

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password', // Added password to fillable attributes
        'role',
    ];

    protected $hidden = [
        'password', // Ensure the password is hidden
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    const ROLE_ADMIN = 'admin';
    const ROLE_PARTICIPANT = 'participant';

    public function participant()
    {
        return $this->hasOne(Participant::class);
    }

    public function administrateur()
    {
        return $this->hasOne(Administrateur::class);
    }

    /**
     * Vérifie si l'utilisateur peut accéder au panneau Filament
     * 
     * @param  \Filament\Panel  $panel
     * @return bool
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isAdmin(): bool // Added return type hint
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isParticipant(): bool // Added return type hint
    {
        return $this->role === self::ROLE_PARTICIPANT;
    }

    public function evenements()
    {
        return $this->belongsToMany(Evenement::class, 'evenement_user', 'user_id', 'evenement_id');
    }
}
