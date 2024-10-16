<?php

namespace App\Filament\Pages;

use Filament\Tables;
use App\Models\Customer;
use Filament\Pages\Page;
use Filament\Tables\Table;
use App\Models\BookingReservation;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Contracts\HasTable;
use Spatie\Activitylog\Models\Activity;
use Filament\Tables\Concerns\InteractsWithTable;

class ActivityLog extends Page implements HasForms, HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.activity-log';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $slug = 'activity-log/{reservation_id}';

    public BookingReservation $record;

    public function mount($reservation_id): void
    {
        $this->record = (new BookingReservation)->FindOrFail(decrypt($reservation_id));
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Activity::query()->with('causer')->forSubject($this->record))
            ->columns([
                Tables\Columns\TextColumn::make('description'),
                Tables\Columns\TextColumn::make('log_name'),
                Tables\Columns\TextColumn::make('causer.name')
                ->label('Action By'),
                Tables\Columns\TextColumn::make('created_at'),
            ])
            ->filters([
                // ...
            ])
            ->actions([
                // ...
            ])
            ->bulkActions([
                // ...
            ]);
    }
}
