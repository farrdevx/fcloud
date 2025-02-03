<?php

namespace App\Filament\Resources\ExternalResource\Pages;

use App\Filament\Resources\ExternalResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditExternal extends EditRecord
{
    protected static string $resource = ExternalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
