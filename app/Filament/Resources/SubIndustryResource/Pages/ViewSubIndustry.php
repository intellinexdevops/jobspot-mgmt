<?php

namespace App\Filament\Resources\SubIndustryResource\Pages;

use App\Filament\Resources\SubIndustryResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSubIndustry extends ViewRecord
{
    protected static string $resource = SubIndustryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
