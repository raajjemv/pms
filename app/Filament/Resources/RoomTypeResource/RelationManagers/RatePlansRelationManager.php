<?php

namespace App\Filament\Resources\RoomTypeResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use App\Models\RatePlan;
use App\Models\RoomType;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Cache;
use Filament\Tables\Actions\AttachAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class RatePlansRelationManager extends RelationManager
{
    protected static string $relationship = 'ratePlans';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('rate')
                    ->numeric()
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('default')
                    ->disabled(fn($record) => $record->pivot->default)
                    ->afterStateUpdated(function ($state, $record) {
                        if ($state) {
                            RoomType::find($record->pivot_room_type_id)->ratePlans()->newPivotQuery()->update(['default' => false]);
                            Cache::forget('room_type_' . $record->pivot_room_type_id);
                        }
                    }),

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('pivot.rate')
                    ->label('Rate'),
                Tables\Columns\IconColumn::make('pivot.default')
                    ->boolean()
                    ->label('Default'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->preloadRecordSelect()
                    ->mutateFormDataUsing(function (array $data): array {
                        $hasDefault = RoomType::find($this->getOwnerRecord()->id)->ratePlans()->wherePivot('default', true)->exists();

                        $data['default'] = $hasDefault ? false : true;

                        return $data;
                    })
                    ->form(function ($action) {
                        return [
                            $action->getRecordSelect(),
                            Forms\Components\TextInput::make('rate')
                                ->numeric()
                                ->required(),
                            // Forms\Components\Toggle::make('default')
                            //     ->formatStateUsing(fn() => $hasDefault ? false : true)
                        ];
                    })
                    ->after(function ($data) {
                        Cache::forget('room_type_' . $this->getOwnerRecord()->id);
                        Cache::forget('room_types_' . Filament::getTenant()->id);

                        if ($data['default'] == true) {
                            // RoomType::find($this->getOwnerRecord()->id)->ratePlans()->wherePivot('rate_plan_id', '!=', $record->id)->newPivotQuery()->update(['default' => false]);
                        }
                    })

            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->after(function ($record) {
                        if ($record->pivot->default) {
                            RoomType::find($this->getOwnerRecord()->id)->ratePlans()->wherePivot('rate_plan_id', '!=', $record->id)->newPivotQuery()->update(['default' => false]);
                            Cache::forget('room_type_' . $this->getOwnerRecord()->id);
                            Cache::forget('room_types_' . Filament::getTenant()->id);
                        }
                    })
                    ->modalWidth('sm'),
                Tables\Actions\DetachAction::make()
                    ->after(function () {
                        Cache::forget('room_type_' . $this->getOwnerRecord()->id);
                        Cache::forget('room_types_' . Filament::getTenant()->id);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }
}
