<?php

use App\Http\Controllers\EvenementController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SondageController;
use App\Http\Controllers\ReponseController;
use App\Http\Controllers\SondageAnalyseController;
use App\Filament\Pages\AnalyseSondage;

/*
|---------------------------------------------------------------------------
| API Routes
|---------------------------------------------------------------------------
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/evenements', [EvenementController::class, 'listEvents']);
Route::post('/evenements/{id_evenement}/inscription', [EvenementController::class, 'inscrire']);
Route::post('/verifier-participant', [EvenementController::class, 'verifierParticipant']);

Route::get('/sondages/token/{token}/questions-with-event', [SondageController::class, 'getQuestionsByToken']);
Route::post('/sondages/{id}/questions', [SondageController::class, 'addQuestion']);
// Route::post('/sondages/token/{token}/submit', [SondageController::class, 'submitAnswers']);  

Route::get('/sondages/token', [SondageController::class, 'getToken']);



Route::post('/evenements/{evenementId}/reponses', [ReponseController::class, 'store']);
Route::post('/sondages/token/{token}/submit', [SondageController::class, 'submitAnswers']);
// Route::get('/sondages/{evenementId}/resultats', [SondageController::class, 'getSondageResultsByEvent'])
// ->name('admin.sondage.resultats');
Route::get('/events/{id}/details', [sondageController::class, 'getEventDetails']);
Route::get('/sondages/{id}/reponses', [SondageController::class, 'getAnswers']);
// Route::get('/analyse-sondage/{id}', [SondageController::class, 'analyseSondage'])->name('analyse.sondage');

