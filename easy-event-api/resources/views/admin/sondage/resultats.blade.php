<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Analyse Resultats</title>
</head>
<body>

    @extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold">Résultats du sondage : {{ $evenement->name }}</h1>

    <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="font-semibold text-gray-700">Total Questions</h3>
            <p class="text-2xl font-bold">{{ $statistiques_generales['total_questions'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="font-semibold text-gray-700">Total Participants</h3>
            <p class="text-2xl font-bold">{{ $statistiques_generales['total_participants'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="font-semibold text-gray-700">Taux de participation</h3>
            <p class="text-2xl font-bold">{{ $statistiques_generales['taux_participation'] }}%</p>
        </div>
    </div>

    <div class="space-y-6 mt-6">
        @foreach($resultats_detailles as $resultat)
        <div class="bg-white shadow-lg rounded-lg p-6">
            <h2 class="text-xl font-semibold mb-4">{{ $resultat['question']['texte'] }}</h2>
            <p class="text-gray-600 mb-4">Total des réponses : {{ $resultat['statistiques']['total_reponses'] }}</p>

            <div class="space-y-4">
                @foreach($resultat['statistiques']['details'] as $reponse => $stats)
                <div class="relative">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-sm font-medium text-gray-700">{{ $reponse }}</span>
                        <span class="text-sm font-medium text-gray-700">{{ $stats['pourcentage'] }}%</span>
                    </div>
                    <div class="overflow-hidden h-4 bg-gray-200 rounded">
                        <div 
                            class="h-full bg-blue-600 transition-all duration-300" 
                            style="width: {{ $stats['pourcentage'] }}%">
                        </div>
                    </div>
                    <div class="mt-1 text-sm text-gray-500">
                        {{ $stats['count'] }} réponses
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-6">
        <a 
            href="{{ route('admin.sondage.export', ['evenementId' => $evenement->id]) }}"
            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
        >
            Exporter en CSV
        </a>
    </div>
</div>
@endsection

</body>
</html>