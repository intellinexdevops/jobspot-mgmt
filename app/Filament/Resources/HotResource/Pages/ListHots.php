<?php

namespace App\Filament\Resources\HotResource\Pages;

use App\Filament\Resources\HotResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHots extends ListRecords
{
    protected static string $resource = HotResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
