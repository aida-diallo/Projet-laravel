<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReponseResource\Pages;
use App\Models\Reponse;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReponseResource extends Resource
{
    protected static ?string $model = Reponse::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-ellipsis';

    protected static ?string $modelLabel = 'Réponse';
    protected static ?string $pluralModelLabel = 'Réponses';
    protected static ?string $navigationGroup = 'Gestion des Sondages';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Sélection de la question
                Forms\Components\Select::make('question_id')
                    ->label('Question')
                    ->relationship('question', 'texte')
                    ->searchable() 
                    ->preload() 
                    ->required()
                    ->placeholder('Sélectionnez une question')
                    ->disabled(), 

                Forms\Components\TextInput::make('reponse')
                    ->label('Réponse')
                    ->required()
                    ->placeholder('Entrez la réponse')
                    ->maxLength(255) 
                    ->disabled(),

                // Sélection du participant
                Forms\Components\Select::make('participant_id')
                    ->label('Participant')
                    ->relationship('participant', 'id') 
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->user->name) 
                    ->searchable()
                    ->preload()
                    ->required()
                    ->placeholder('Sélectionnez un participant')
                    ->disabled(), 
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Colonne pour la question
                Tables\Columns\TextColumn::make('question.texte')
                    ->label('Question')
                    ->sortable()
                    ->searchable()
                    ->limit(50) 
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) > 50) {
                            return $state; 
                        }
                        return null;
                    }),

                
                Tables\Columns\TextColumn::make('reponse')
                    ->label('Réponse')
                    ->sortable()
                    ->searchable()
                    ->limit(50) 
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) > 50) {
                            return $state; 
                        }
                        return null;
                    }),

                Tables\Columns\TextColumn::make('question.sondage.evenement.nom')
                    ->label('Événement')
                    ->sortable()
                    ->searchable()
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) > 50) {
                            return $state;
                        }
                        return null;
                    }),

                // Colonne pour le participant
                Tables\Columns\TextColumn::make('participant.user.name')
                    ->label('Participant')
                    ->sortable()
                    ->searchable()
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) > 50) {
                            return $state;
                        }
                        return null;
                    }),
            ])
            ->filters([
            
                Tables\Filters\SelectFilter::make('question_id')
                    ->label('Question')
                    ->relationship('question', 'texte')
                    ->searchable()
                    ->preload(),

    
                Tables\Filters\SelectFilter::make('participant_id')
                    ->label('Participant')
                    ->relationship('participant', 'id') 
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->user->name) 
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('question.sondage.evenement_id')
                    ->label('Événement')
                    ->relationship('question.sondage.evenement', 'nom')
                    ->searchable()
                    ->preload(),
            ])
            ->defaultSort('created_at', 'desc') 
            ->paginated([10, 25, 50, 100]); 
    }

    public static function getRelations(): array
    {
        return [
            
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReponses::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}