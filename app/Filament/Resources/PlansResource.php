<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlansResource\Pages;
use App\Filament\Resources\PlansResource\RelationManagers;
use App\Models\Plans;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PlansResource extends Resource
{
    protected static ?string $model = Plans::class;

    protected static ?string $navigationGroup = 'Configuration';
    protected static ?string $navigationIcon = 'heroicon-o-fire';

    protected static ?string $navigationLabel = "Plan";
    protected static ?string $modelLabel = "Plan";
    protected static ?string $pluralModelLabel = "Plans";

    public static function form(Form $form): Form
    {


        return $form
            ->schema([
                Forms\Components\Section::make('Plan Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\DatePicker::make('duration')
                            ->required(),
                        Forms\Components\TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->prefix('$'),
                        Forms\Components\Select::make('status')
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                            ])
                            ->required(),
                    ])->columnSpan(2)
                    ->columns(2),

                Forms\Components\Section::make('Plan Details')
                    ->schema([
                        Forms\Components\MarkdownEditor::make('description')
                            ->required(),
                    ])->columnSpan(1),

            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->money()
                    ->sortable(),
                Tables\Columns\TextColumn::make('duration')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status'),
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
            'index' => Pages\ListPlans::route('/'),
            'create' => Pages\CreatePlans::route('/create'),
            'view' => Pages\ViewPlans::route('/{record}'),
            'edit' => Pages\EditPlans::route('/{record}/edit'),
        ];
    }
}
