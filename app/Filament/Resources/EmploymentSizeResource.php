<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmploymentSizeResource\Pages;
use App\Filament\Resources\EmploymentSizeResource\RelationManagers;
use App\Models\EmploymentSize;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmploymentSizeResource extends Resource
{
    protected static ?string $model = EmploymentSize::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Configuration';
    protected static ?string $navigationLabel = 'Employment Size';
    protected static ?string $pluralModelLabel = 'Employment Sizes';
    protected static ?string $modelLabel = 'Employment Size';
    protected static ?int $navigationSort = 6;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListEmploymentSizes::route('/'),
            'create' => Pages\CreateEmploymentSize::route('/create'),
            'view' => Pages\ViewEmploymentSize::route('/{record}'),
            'edit' => Pages\EditEmploymentSize::route('/{record}/edit'),
        ];
    }
}
