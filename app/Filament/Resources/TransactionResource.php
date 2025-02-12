<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationGroup = 'Overview';
    protected static ?string $navigationLabel = 'Transaction';
    protected static ?string $pluralModelLabel = 'Transaction';
    protected static ?string $modelLabel = 'Transaction';
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('tran_id')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('currency')
                    ->maxLength(255)
                    ->default('USD'),
                Forms\Components\TextInput::make('status')
                    ->maxLength(255)
                    ->default('pending'),
                Forms\Components\TextInput::make('payment_option')
                    ->maxLength(255)
                    ->default('cards'),
                Forms\Components\TextInput::make('type')
                    ->maxLength(255)
                    ->default('purchase'),
                Forms\Components\TextInput::make('items')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.username')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tran_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->badge()
                    ->color('success')
                    ->numeric(2)
                    ->searchable(),
                Tables\Columns\TextColumn::make('currency')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'paid' => 'success',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'pending' => 'PENDING',
                        'paid' => 'PAID',
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('payment_option')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'cards' => 'CARD',
                        'khqr' => 'KHQR',
                    })
                    ->color('warning')
                    ->searchable(),
                // Tables\Columns\TextColumn::make('type')
                //     ->searchable(),
                Tables\Columns\TextColumn::make('items')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'view' => Pages\ViewTransaction::route('/{record}'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}
