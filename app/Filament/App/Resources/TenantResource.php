<?php

namespace App\Filament\App\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Tenant;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\App\AppTenant;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\App\Resources\TenantResource\Pages;
use App\Filament\App\Resources\TenantResource\RelationManagers;

class TenantResource extends Resource
{
    protected static ?string $model = Tenant::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone_number')
                    ->tel()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('logo')
                    ->maxLength(255),
                Forms\Components\TextInput::make('bill_initials')
                    ->maxLength(255),
                Forms\Components\Textarea::make('address')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('tin')
                    ->maxLength(255),
                Forms\Components\Select::make('owner_id')
                    ->relationship(name: 'owner', titleAttribute: 'name'),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone_number')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('logo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('bill_initials')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tin')
                    ->searchable(),
                Tables\Columns\TextColumn::make('owner.name')
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
            RelationManagers\UsersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTenants::route('/'),
            'create' => Pages\CreateTenant::route('/create'),
            'view' => Pages\ViewTenant::route('/{record}'),
            'edit' => Pages\EditTenant::route('/{record}/edit'),
            'channel-groups' => Pages\ManageChannelgroups::route('/{record}/channel-groups')
        ];
    }

    public static function canAccess(): bool
    {
        return true;
    }

    public static function canViewAny(): bool
    {
        return true;
    }

    public static function canCreate(): bool
    {
        return true;
    }

    public static function canEdit(Model $record): bool
    {
        return true;
    }

    public static function canDelete(Model $record): bool
    {
        return true;
    }

    public static function canDeleteAny(): bool
    {
        return true;
    }

    public static function canForceDelete(Model $record): bool
    {
        return true;
    }

    public static function canForceDeleteAny(): bool
    {
        return true;
    }

    public static function canReorder(): bool
    {
        return true;
    }

    public static function canReplicate(Model $record): bool
    {
        return true;
    }

    public static function canRestore(Model $record): bool
    {
        return true;
    }

    public static function canRestoreAny(): bool
    {
        return true;
    }

    public static function canView(Model $record): bool
    {
        return true;
    }
}
