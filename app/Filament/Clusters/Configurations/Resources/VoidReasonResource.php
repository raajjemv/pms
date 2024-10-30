<?php

namespace App\Filament\Clusters\Configurations\Resources;

use App\Filament\Clusters\Configurations;
use App\Filament\Clusters\Configurations\Resources\VoidReasonResource\Pages;
use App\Filament\Clusters\Configurations\Resources\VoidReasonResource\RelationManagers;
use App\Models\VoidReason;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VoidReasonResource extends Resource
{
    protected static ?string $model = VoidReason::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = Configurations::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\TextInput::make('reason')
                    ->required()
                    ->maxLength(255),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reason')
                    ->description(fn($record) => $record->locked ? 'System Use' : NULL)
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
            'index' => Pages\ListVoidReasons::route('/'),
            'create' => Pages\CreateVoidReason::route('/create'),
            'view' => Pages\ViewVoidReason::route('/{record}'),
            'edit' => Pages\EditVoidReason::route('/{record}/edit'),
        ];
    }

    protected static bool $isScopedToTenant = false;

}
