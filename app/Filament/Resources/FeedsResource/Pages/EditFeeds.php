<?php

namespace App\Filament\Resources\FeedsResource\Pages;

use App\Filament\Resources\FeedsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFeeds extends EditRecord
{
    protected static string $resource = FeedsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
