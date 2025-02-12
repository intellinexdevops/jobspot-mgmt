<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Transaction;

class Transactions extends BaseWidget
{
    protected static ?string $heading = 'Transactions';
    protected static bool $isLazy = false;
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Transaction::query()
            )
            ->columns([
                Tables\Columns\TextColumn::make('user.nickname')->label('Customer'),
                Tables\Columns\TextColumn::make('plan_id')->label('Plan'),
                Tables\Columns\TextColumn::make('amount')
                ->label('Amount')
                ->numeric()
                ->badge()
                ->money(fn ($record) => $record->currency == 'USD' ? 'USD' : 'KHR'),
                Tables\Columns\TextColumn::make('status')
                ->label('Status')
                ->color(fn ($state) => $state == 'pending' ? 'warning' : 'success')
                ->badge()
                ->alignCenter()
                ->formatStateUsing(fn ($state) => $state == 'pending' ? 'PENDING' : 'PAID'),
                Tables\Columns\TextColumn::make('created_at')->label('Created At'),
            ]);
    }
}
