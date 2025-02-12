<?php

namespace App\Filament\Resources\PlansResource\Pages;

use App\Filament\Resources\PlansResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPlans extends ViewRecord
{
    protected static string $resource = PlansResource::class;

    protected static ?string $title = 'Plans';

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\CreateAction::make()->label('Create new')
        ];
    }
}
