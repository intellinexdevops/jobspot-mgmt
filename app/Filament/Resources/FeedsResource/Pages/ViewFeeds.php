<?php

namespace App\Filament\Resources\FeedsResource\Pages;

use App\Filament\Resources\FeedsResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewFeeds extends ViewRecord
{
    protected static string $resource = FeedsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\EditAction::make(),
        ];
    }
}
