<?php

namespace App\Filament\Resources\UserResource\Pages;

use Filament\Forms;
use App\Models\User;
use Filament\Actions;
use Filament\Facades\Filament;
use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Database\Eloquent\Builder;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('new')
                ->form([
                    Forms\Components\Select::make('action_type')
                        ->options([
                            'new-user' => 'New User',
                            'existing-user' => 'Exisiting User'
                        ])
                        ->required()
                        ->live(),
                    Forms\Components\Select::make('user')
                        ->searchable()
                        ->preload()
                        ->visible(fn ($get) => $get('action_type') == 'existing-user')
                        ->required()
                        ->live()
                        ->options(auth()->user()->tenants->load('users')->pluck('users')->flatten()->unique('id')->pluck('email', 'id')),
                    Forms\Components\Select::make('role')
                        ->visible(fn ($get) => $get('user'))
                        ->required()
                        ->relationship(
                            name: 'roles',
                            titleAttribute: 'name',
                            modifyQueryUsing: fn (Builder $query) => auth()->id() === 1 ? $query :  $query->whereNotIn('name', ['admin', 'tenant_owner']),
                        )
                ])
                ->action(function ($data) {
                    $resource = static::getResource();
                    if ($data['action_type'] == 'new-user') {
                        return redirect($resource::getUrl('create'));
                    } else {
                        $user = User::find($data['user']);
                        $user->tenants()->attach(Filament::getTenant()->id);
                    }
                }),
        ];
    }
}
