<?php

namespace App\Filament\Resources\SondageResource\Pages;

use App\Filament\Resources\SondageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSondage extends EditRecord
{
    protected static string $resource = SondageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
