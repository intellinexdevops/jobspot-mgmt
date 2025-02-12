<?php

namespace App\Filament\Resources\EmploymentSizeResource\Pages;

use App\Filament\Resources\EmploymentSizeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEmploymentSizes extends ListRecords
{
    protected static string $resource = EmploymentSizeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
