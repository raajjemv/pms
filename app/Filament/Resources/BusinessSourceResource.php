<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\BusinessSource;
use Filament\Resources\Resource;
use App\Filament\Clusters\Configurations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\BusinessSourceResource\Pages;
use App\Filament\Resources\BusinessSourceResource\RelationManagers;

class BusinessSourceResource extends Resource
{
    protected static ?string $model = BusinessSource::class;

    protected static ?string $cluster = Configurations::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('business_registration')
                    ->maxLength(255),
                Forms\Components\Select::make('type')
                    ->options([
                        'ota' => 'OTA',
                        'city_ledger' => 'City Ledger',
                        'local_travel_agent' => 'Local Travel Agent',
                        'foreign_travel_agent' => 'Foreign Travel Agent',
                        'corporate' => 'Coporate'
                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->description(fn($record) => $record->locked ? 'System Use' : NULL)
                    ->searchable(),
                Tables\Columns\TextColumn::make('business_registration')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
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
                Tables\Actions\ViewAction::make()
                    ->visible(fn($record) => !$record->locked),
                Tables\Actions\EditAction::make()
                    ->visible(fn($record) => !$record->locked),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'DESC');
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
            'index' => Pages\ListBusinessSources::route('/'),
            'create' => Pages\CreateBusinessSource::route('/create'),
            'view' => Pages\ViewBusinessSource::route('/{record}'),
            'edit' => Pages\EditBusinessSource::route('/{record}/edit'),
        ];
    }

    protected static bool $isScopedToTenant = false;
}
