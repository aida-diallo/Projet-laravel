<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuestionResource\Pages;
use App\Models\Question;
use App\Models\Participant;
use App\Mail\QuestionNotification;
use Illuminate\Support\Facades\Mail;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class QuestionResource extends Resource
{
    protected static ?string $model = Question::class;

    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';

    protected static ?string $modelLabel = 'Question';
    protected static ?string $pluralModelLabel = 'Questions';
    protected static ?string $navigationGroup = 'Gestion des Sondages';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('texte')
                    ->label('Question')
                    ->required()
                    ->placeholder('Entrez la question')
                    ->maxLength(255),

                Forms\Components\TextInput::make('type')
                    ->label('Type')
                    ->required()
                    ->placeholder('Entrez le type de question'),
                    
                    Forms\Components\Select::make('sondage_id')
                    ->label('Sondage')
                    ->relationship('sondage', 'titre', fn ($query) => $query->whereNotNull('titre'))
                    ->searchable()
                    ->preload()
                    ->required()
                    ->placeholder('Sélectionnez un sondage'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('texte')
                    ->label('Question')
                    ->sortable()
                    ->searchable()
                    ->limit(50),

                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('sondage.evenement.nom')
                    ->label('Événement')
                    ->sortable()
                    ->searchable()
                    ->limit(50),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuestions::route('/'),
            'create' => Pages\CreateQuestion::route('/create'),
        ];
    }

    public static function canCreate(): bool
    {
        return true;
    }
}
