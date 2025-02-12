<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EducationLevelResource\Pages;
use App\Filament\Resources\EducationLevelResource\RelationManagers;
use App\Models\EducationLevel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EducationLevelResource extends Resource
{
    protected static ?string $model = EducationLevel::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationGroup = 'Configuration';
    protected static ?string $navigationLabel = 'Education Level';
    protected static ?string $modelLabel = 'Education Level';
    protected static ?string $pluralModelLabel = 'Education Levels';
    protected static ?int $navigationSort = 5;

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
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d-m-Y H:i:s'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('d-m-Y H:i:s'),
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
            'index' => Pages\ListEducationLevels::route('/'),
            'create' => Pages\CreateEducationLevel::route('/create'),
            'view' => Pages\ViewEducationLevel::route('/{record}'),
            'edit' => Pages\EditEducationLevel::route('/{record}/edit'),
        ];
    }
}
