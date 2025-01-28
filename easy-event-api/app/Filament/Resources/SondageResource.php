<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SondageResource\Pages;
use App\Models\Sondage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Pages\AnalyseSondage;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SondageResource extends Resource
{
    protected static ?string $model = Sondage::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Gestion des Sondages';
    protected static ?string $modelLabel = 'Sondage';
    protected static ?string $pluralModelLabel = 'Sondages';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Sélection de l'événement
                Forms\Components\Select::make('evenement_id')
                    ->label('Événement')
                    ->relationship('evenement', 'nom')
                    ->required()
                    ->placeholder('Sélectionnez un événement')
                    ->searchable()
                    ->preload()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                       
                        if ($state) {
                            $evenement = \App\Models\Evenement::find($state);
                            if ($evenement && $evenement->date) {
                                $set('dateEnvoie', $evenement->date);
                            }
                        }
                    }),

              
                Forms\Components\DatePicker::make('dateEnvoie')
                    ->label('Date d\'envoi')
                    ->required()
                    ->placeholder('Sélectionnez une date'),

                // Répéteur pour les questions
                Forms\Components\Repeater::make('questions')
                    ->relationship('questions')
                    ->schema([
                        // Texte de la question
                        Forms\Components\TextInput::make('texte')
                            ->label('Question')
                            ->required()
                            ->placeholder('Entrez la question')
                            ->maxLength(255),

                        // Type de question
                        Forms\Components\Select::make('type')
                            ->label('Type de question')
                            ->options([
                                'texte' => 'Texte',
                                'choix_multiple' => 'Choix multiple',
                            ])
                            ->required()
                            ->reactive(),

                        // Répéteur pour les options (visible uniquement pour les questions à choix multiple)
                        Forms\Components\Repeater::make('options')
                            ->label('Options')
                            ->visible(fn ($get) => $get('type') === 'choix_multiple')
                            ->schema([
                                Forms\Components\TextInput::make('option')
                                    ->label('Option')
                                    ->required()
                                    ->placeholder('Entrez une option'),
                            ])
                            ->defaultItems(2)
                            ->minItems(2)
                            ->required(fn ($get) => $get('type') === 'choix_multiple'),
                    ])
                   
                    ->defaultItems(1) 
                    ->minItems(1) 
                    ->columnSpanFull(), 
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Colonne pour l'événement
                Tables\Columns\TextColumn::make('evenement.nom')
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

                // Colonne pour la date d'envoi
                Tables\Columns\TextColumn::make('dateEnvoie')
                    ->label('Date d\'envoi')
                    ->sortable()
                    ->searchable()
                    ->date(),

                // Colonne pour les questions
                Tables\Columns\TextColumn::make('questions_text')
                    ->label('Questions')
                    ->getStateUsing(function ($record) {
                        return $record->questions->pluck('texte')->implode(', ');
                    })
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) > 50) {
                            return $state;
                        }
                        return null;
                    }),

                // Colonne pour le nombre de questions
                Tables\Columns\TextColumn::make('questions_count')
                    ->label('Nombre de questions')
                    ->counts('questions'),

                // Colonne pour la date de création
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->sortable()
                    ->date(),

                // Colonne pour la date de mise à jour
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Mis à jour le')
                    ->sortable()
                    ->date(),
            ])
            ->filters([
                // Filtre par événement
                Tables\Filters\SelectFilter::make('evenement_id')
                    ->label('Événement')
                    ->relationship('evenement', 'nom')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('dateEnvoie')
                    ->form([
                        Forms\Components\DatePicker::make('dateEnvoie')
                            ->label('Date d\'envoi'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if ($data['dateEnvoie']) {
                            $query->whereDate('dateEnvoie', $data['dateEnvoie']);
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('analyse')
                ->label('Analyse')
                ->icon('heroicon-o-eye') 
                ->color('secondary') 
                ->url(fn (Sondage $record) => route('filament.pages.analyse--sondage', ['sondageId' => $record->id]))
                // ->openUrlInNewTab(), 
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListSondages::route('/'),
            'create' => Pages\CreateSondage::route('/create'),
            'edit' => Pages\EditSondage::route('/{record}/edit'),
            //  'analyse' => Pages\AnalyseSondage::route('/{record}/analyse'),
        ];
    }
}