<?php

namespace App\Filament\Resources\SubIndustryResource\Pages;

use App\Filament\Resources\SubIndustryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSubIndustries extends ListRecords
{
    protected static string $resource = SubIndustryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
