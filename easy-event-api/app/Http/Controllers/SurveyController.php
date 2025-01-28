<?php

// app/Http/Controllers/SurveyController.php
namespace App\Http\Controllers;

use App\Models\Participant;
use Illuminate\Http\Request;

class SurveyController extends Controller
{
    public function show(Request $request)
    {
        $token = $request->query('token');

     
        $participant = Participant::where('token', $token)
            ->where('token_expires_at', '>', now())
            ->first();

        if (!$participant) {
            return response()->json(['message' => 'Lien invalide ou expiré.'], 403);
        }

        
        return view('survey.page', ['participant' => $participant]);
    }
}

