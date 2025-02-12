<?php

namespace App\Filament\Resources\HotResource\Pages;

use App\Filament\Resources\HotResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHot extends EditRecord
{
    protected static string $resource = HotResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
