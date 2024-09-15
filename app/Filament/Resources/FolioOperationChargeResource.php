<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\FolioOperationCharge;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Clusters\RoomConfigurations;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\FolioOperationChargeResource\Pages;
use App\Filament\Resources\FolioOperationChargeResource\RelationManagers;

class FolioOperationChargeResource extends Resource
{
    protected static ?string $model = FolioOperationCharge::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = RoomConfigurations::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
              
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('rate')
                    ->required()
                    ->numeric(),
                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
              
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('rate')
                    ->numeric()
                    ->sortable(),
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
            'index' => Pages\ListFolioOperationCharges::route('/'),
            'create' => Pages\CreateFolioOperationCharge::route('/create'),
            'view' => Pages\ViewFolioOperationCharge::route('/{record}'),
            'edit' => Pages\EditFolioOperationCharge::route('/{record}/edit'),
        ];
    }
}
