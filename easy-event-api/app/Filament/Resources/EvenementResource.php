<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EvenementResource\Pages;
use App\Models\Evenement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EvenementResource extends Resource
{
    protected static ?string $model = Evenement::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $label = 'Événement';
    protected static ?string $pluralLabel = 'Événements';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('nom')
                            ->label('Nom de l\'événement')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->required(),

                        Forms\Components\Select::make('categorie_id')
                            ->label('Catégorie')
                            ->relationship('categorie', 'nom')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\FileUpload::make('image')
                            ->label('Image')
                            ->image()
                            ->disk('public')
                            ->directory('events')
                            ->required()
                            ->imageEditor()
                            ->imageResizeMode('cover')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/svg+xml']),
                    ]),

                Forms\Components\Section::make('Détails temporels')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('date')
                                    ->label('Date de l\'événement')
                                    ->required(),

                                Forms\Components\TimePicker::make('heureDebut')
                                    ->label('Heure de début')
                                    ->required(),

                                Forms\Components\TimePicker::make('heureFin')
                                    ->label('Heure de fin')
                                    ->required()
                                    ->after('heureDebut'),

                                Forms\Components\TextInput::make('lieu')
                                    ->label('Lieu')
                                    ->required()
                                    ->maxLength(255),
                            ]),
                    ]),

                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('max_participants')
                            ->label('Nombre maximum de participants')
                            ->numeric()
                            ->required()
                            ->minValue(1),

                        Forms\Components\TextInput::make('tarif')
                            ->label('Tarif')
                            ->numeric()
                            ->required()
                            ->prefix('€')
                            ->minValue(0),
                    ]),
            ]);
    }
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nom')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('date')
                    ->label('Date')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('heureDebut')
                    ->label('Heure de début')
                    ->time('H:i'),

                Tables\Columns\TextColumn::make('lieu')
                    ->label('Lieu')
                    ->searchable(),

                Tables\Columns\TextColumn::make('categorie.nom')
                    ->label('Catégorie')
                    ->searchable(),

                Tables\Columns\ImageColumn::make('image')
                    ->label('Image')
                    ->disk('public')
                    ->circular(),

                Tables\Columns\TextColumn::make('max_participants')
                    ->label('Max Participants')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('participants_count')
                    ->label('Participants')
                    ->counts('participants')
                    ->sortable(),

                Tables\Columns\TextColumn::make('tarif')
                    ->label('Tarif')
                    ->money('EUR')
                    ->sortable(),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('categorie')
                    ->relationship('categorie', 'nom')
                    ->label('Filtrer par Catégorie'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEvenements::route('/'),
            'create' => Pages\CreateEvenement::route('/create'),
            'edit' => Pages\EditEvenement::route('/{record}/edit'),
        ];
    }
}