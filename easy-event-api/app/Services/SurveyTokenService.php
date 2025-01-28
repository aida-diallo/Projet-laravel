<?php

namespace App\Services;

use App\Models\SurveyToken;
use Illuminate\Support\Str;

class SurveyTokenService
{
    public function generateToken($evenementId, $userId, $expirationDays = 7)
    {
        // Invalider les tokens existants pour cette combinaison
        SurveyToken::where('evenement_id', $evenementId)
            ->where('user_id', $userId)
            ->whereNull('used_at')
            ->update(['used_at' => now()]);

        // Créer un nouveau token
        return SurveyToken::create([
            'token' => Str::random(64),
            'evenement_id' => $evenementId,
            'user_id' => $userId,
            'expires_at' => now()->addDays($expirationDays),
        ]);
    }

    public function validateToken($token)
    {
        return SurveyToken::where('token', $token)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->first();
    }

    public function markAsUsed(SurveyToken $token)
    {
        $token->update(['used_at' => now()]);
    }
}