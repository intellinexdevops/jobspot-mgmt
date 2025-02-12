<?php

namespace App\Filament\Resources\EmploymentSizeResource\Pages;

use App\Filament\Resources\EmploymentSizeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEmploymentSize extends EditRecord
{
    protected static string $resource = EmploymentSizeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
