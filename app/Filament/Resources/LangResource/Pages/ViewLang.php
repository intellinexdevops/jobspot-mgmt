<?php

namespace App\Filament\Resources\LangResource\Pages;

use App\Filament\Resources\LangResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewLang extends ViewRecord
{
    protected static string $resource = LangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
