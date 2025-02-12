<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Location;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static ?string $navigationLabel = "User";
    protected static ?string $modelLabel = "User";
    protected static ?string $pluralModelLabel = "Users";
    protected static ?string $slug = "user";

    protected static ?int $navigationSort = 1;
    protected static ?string $navigationGroup = "Authentication";

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Section::make("User Information")
                    ->schema([
                        Forms\Components\TextInput::make('username')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('nickname')
                            ->maxLength(100),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->required()
                            ->maxLength(255),


                        Forms\Components\Select::make('gender')
                            ->options([
                                'male' => 'Male',
                                'female' => 'Female',
                            ]),

                        Forms\Components\DatePicker::make('birthday'),

                        Forms\Components\TextInput::make('mobile')
                            ->numeric()
                            ->maxLength(50),


                    ])
                    ->columnSpan(2)
                    ->columns(2)
                    ->description("Enter user credentials"),


                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make("Additional Information")
                            ->collapsible()
                            ->schema([
                                Forms\Components\FileUpload::make('avatar')
                                    ->disk("public")
                                    ->directory("avatars"),

                                Forms\Components\Select::make('location_id')->label("Location")
                                    ->options(Location::all()->pluck('name', 'id'))
                                    ->required()
                                    ->searchable()
                                    ->preload(),

                                Forms\Components\Textarea::make('bio')
                                    ->columnSpanFull(),
                            ])
                            ->columnSpan(1),
                    ]),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make("id")->label("ID"),
                Tables\Columns\TextColumn::make('username')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('nickname')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Email Verified')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\ImageColumn::make('avatar')
                    ->circular()
                    ->size(30),
                Tables\Columns\TextColumn::make('gender')
                    ->label('Gender'),
                Tables\Columns\TextColumn::make('birthday')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('mobile')
                    ->searchable(),
                Tables\Columns\TextColumn::make('verification')
                    ->label('Verification')
                    ->badge()
                    ->color(fn($state) => $state == 'verified' ? 'success' : 'danger')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('location.name')
                    ->label('Location')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn($state) => $state == 'active' ? 'success' : 'danger'),
                Tables\Columns\TextColumn::make("push_token")->label('FMC'),
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
