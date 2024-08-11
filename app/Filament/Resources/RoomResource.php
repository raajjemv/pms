<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoomResource\Pages;
use App\Filament\Resources\RoomResource\RelationManagers;
use App\Models\Amenity;
use App\Models\Room;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RoomResource extends Resource
{
    protected static ?string $model = Room::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Rooms Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make([
                    Forms\Components\Select::make('room_type_id')
                        ->relationship(name: 'roomType', titleAttribute: 'name')
                        ->required(),
                    Forms\Components\Select::make('room_class_id')
                        ->relationship(name: 'roomClass', titleAttribute: 'name')
                        ->required(),
                    Forms\Components\TextInput::make('room_number')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('capacity')
                        ->required()
                        ->numeric()
                        ->helperText('Numeric value only')
                        ->default(2),
                    Forms\Components\TextInput::make('maximum_occupancy')
                        ->required()
                        ->numeric()
                        ->helperText('Numeric value only')
                        ->default(2),
                    Forms\Components\TextInput::make('room_size')
                        ->required()
                        ->helperText('eg: 18 m²/194 ft²')
                        ->maxLength(255),
                    Forms\Components\Select::make('bed_type_id')
                        ->relationship(name: 'bedType', titleAttribute: 'name')
                        ->required(),
                    Forms\Components\Select::make('bathroom_type_id')
                        ->relationship(name: 'bathroomType', titleAttribute: 'name')
                        ->required(),
                    Forms\Components\Select::make('room_view_id')
                        ->relationship(name: 'roomView', titleAttribute: 'name')
                        ->required(),
                    Forms\Components\Select::make('amenities')
                        ->multiple()
                        ->options(Amenity::pluck('name', 'id')),
                    Forms\Components\Toggle::make('smoking')
                        ->required(),
                    Forms\Components\TextInput::make('floor_number')
                        ->maxLength(255),
                    Forms\Components\Textarea::make('room_description')
                        ->required()
                        ->columnSpanFull(),
                  
                ])
                ->columns(3)


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('roomType.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('room_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('room_size')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('roomView.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('smoking')
                    ->boolean(),
                Tables\Columns\TextColumn::make('floor_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListRooms::route('/'),
            'create' => Pages\CreateRoom::route('/create'),
            'view' => Pages\ViewRoom::route('/{record}'),
            'edit' => Pages\EditRoom::route('/{record}/edit'),
        ];
    }
}
