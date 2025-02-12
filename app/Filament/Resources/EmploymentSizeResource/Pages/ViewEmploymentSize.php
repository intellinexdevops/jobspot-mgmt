<?php

namespace App\Filament\Resources\EmploymentSizeResource\Pages;

use App\Filament\Resources\EmploymentSizeResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewEmploymentSize extends ViewRecord
{
    protected static string $resource = EmploymentSizeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
