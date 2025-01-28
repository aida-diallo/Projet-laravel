<?php

namespace App\Filament\Resources\SondageResource\Pages;

use App\Filament\Resources\SondageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSondages extends ListRecords
{
    protected static string $resource = SondageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
