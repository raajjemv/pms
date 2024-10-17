<?php

namespace App\Filament\App\Resources\TenantResource\RelationManagers;

use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;
use Spatie\Permission\Models\Role;

class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';



    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('email')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        setPermissionsTeamId($this->getOwnerRecord()->id);

        return $table
            ->recordTitleAttribute('email')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('email'),
                Tables\Columns\TextColumn::make('roles.name')
                    ->badge(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->preloadRecordSelect(),
            ])
            ->actions([
                Tables\Actions\Action::make('edit_roles')
                    ->fillForm(function ($record) {
                        setPermissionsTeamId($this->getOwnerRecord()->id);
                        return [
                            'roles' => $record->roles->pluck('name')
                        ];
                    })
                    ->form([
                        Forms\Components\Select::make('roles')
                            ->options(function ($record) {
                                setPermissionsTeamId($this->getOwnerRecord()->id);
                                return Role::query()->pluck('name', 'name');
                            })
                            ->multiple()
                            ->preload()
                            ->saveRelationshipsBeforeChildrenUsing(function (Model $record, $state) {
                                setPermissionsTeamId($this->getOwnerRecord()->id);
                                $record->syncRoles($state);
                            })
                            ->dehydrated(true)


                    ]),
                Tables\Actions\DetachAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('id', 'DESC');
    }
    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return true;
    }
    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }


    protected function canAttach(): bool
    {
        return true;
    }

    protected function canCreate(): bool
    {
        return true;
    }

    protected function canDelete(Model $record): bool
    {
        return true;
    }

    protected function canDeleteAny(): bool
    {
        return true;
    }

    protected function canDetach(Model $record): bool
    {
        return true;
    }

    protected function canDetachAny(): bool
    {
        return true;
    }


    protected function canEdit(Model $record): bool
    {
        return true;
    }

    protected function canForceDelete(Model $record): bool
    {
        return true;
    }

    protected function canForceDeleteAny(): bool
    {
        return true;
    }

    protected function canReorder(): bool
    {
        return true;
    }

    protected function canReplicate(Model $record): bool
    {
        return true;
    }

    protected function canRestore(Model $record): bool
    {
        return true;
    }

    protected function canRestoreAny(): bool
    {
        return true;
    }

    protected function canView(Model $record): bool
    {
        return $this->can('view', $record);
    }
}
