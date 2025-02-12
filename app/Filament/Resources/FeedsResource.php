<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FeedsResource\Pages;
use App\Models\Feeds;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FeedsResource extends Resource
{
    protected static ?string $model = Feeds::class;

    protected static ?string $navigationIcon = 'heroicon-o-window';

    protected static ?string $navigationGroup = 'Content';

    protected static ?string $navigationLabel = 'Feeds';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('company_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('title')
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('assets')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('reaction_count')
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('comment_count')
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('company.company_name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('assets')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('reaction_count')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('comment_count')
                    ->numeric()
                    ->sortable(),
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
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListFeeds::route('/'),
            'create' => Pages\CreateFeeds::route('/create'),
            'view' => Pages\ViewFeeds::route('/{record}'),
            'edit' => Pages\EditFeeds::route('/{record}/edit'),
        ];
    }
}
