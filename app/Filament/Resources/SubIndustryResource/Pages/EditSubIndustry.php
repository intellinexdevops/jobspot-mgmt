<?php

namespace App\Filament\Resources\SubIndustryResource\Pages;

use App\Filament\Resources\SubIndustryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSubIndustry extends EditRecord
{
    protected static string $resource = SubIndustryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
