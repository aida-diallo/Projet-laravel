<x-filament-panels::page>
    <div class="space-y-6">
        <h1 class="text-2xl font-bold">Analyse du sondage : {{ $sondage->titre }}</h1>

        @foreach ($sondage->questions as $question)
            <div class="p-4 rounded-lg shadow-sm">
                <h2 class="text-lg font-semibold">{{ $question->texte }}</h2>

                @php
                    $totalReponses = $question->reponses->count();
                    $maxPossibleReponses = 100;
                @endphp

                @if ($totalReponses > 0)
                    <ul class="mt-4 space-y-2">
                        @foreach ($question->reponses->groupBy('choix') as $choix => $reponses)
                            @php
                                $barWidth = min(count($reponses), 100);
                                $pourcentage = ($totalReponses > 0) ? (count($reponses) / $totalReponses) * 100 : 0;
                            @endphp
                            <li>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="font-medium">{{ $choix }}</span>
                                    <span class="font-medium">{{ $barWidth }}%</span>
                                </div>

                                <div class="progress-bar relative w-full bg-gray-800 rounded-full h-2">
                                    <div class="progress-bar-filled absolute top-0 left-0 h-full rounded-full"
                                         style="width: {{ $barWidth }}%"></div>
                                </div>

                                <ul class="ml-4 text-gray-600 py-3">
                                    @foreach ($reponses as $reponse)
                                        <li>- {{ $reponse->reponse }}</li>
                                    @endforeach
                                </ul>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-gray-500">Aucune réponse enregistrée pour cette question.</p>
                @endif
            </div>
        @endforeach
    </div>

    {{-- Debug section reste inchangée --}}
    <div class="debug mt-4">
        <h3>Débogage : Affichage des réponses</h3>
        @foreach ($sondage->questions as $question)
            <h4>{{ $question->texte }}</h4>
            <ul>
                @foreach ($question->reponses as $reponse)
                    <li>{{ $reponse->reponse }}</li>
                @endforeach
            </ul>
        @endforeach
    </div>

    <style>
        .progress-bar {
            height: 15px;
            background-color: #1a1a1a;
            border-radius: 9999px;
            position: relative;
            overflow: hidden;
        }

        .progress-bar-filled {
            height: 100%;
            background: linear-gradient(90deg, #2563eb 0%, #ec4899 50%, #10b981 100%);
            border-radius: 9999px;
            transition: width 0.5s ease-in-out;
        }
    </style>
</x-filament-panels::page>