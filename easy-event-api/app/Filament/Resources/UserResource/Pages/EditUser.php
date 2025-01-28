<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Hash;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function afterSave(): void
    {
        if (isset($this->data['admin_password'])) {
            $this->record->administrateur->update([
                'password' => Hash::make($this->data['admin_password']),
            ]);
        }
    }
}
