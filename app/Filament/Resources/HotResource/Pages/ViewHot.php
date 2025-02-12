<?php

namespace App\Filament\Resources\HotResource\Pages;

use App\Filament\Resources\HotResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewHot extends ViewRecord
{
    protected static string $resource = HotResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
