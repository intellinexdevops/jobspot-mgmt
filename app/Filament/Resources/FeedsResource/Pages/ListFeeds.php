<?php

namespace App\Filament\Resources\FeedsResource\Pages;

use App\Filament\Resources\FeedsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFeeds extends ListRecords
{
    protected static string $resource = FeedsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
