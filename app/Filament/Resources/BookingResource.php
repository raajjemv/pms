<?php

namespace App\Filament\Resources;

use App\Enums\BookingType;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use App\Models\Booking;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\BookingResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\BookingResource\RelationManagers;
use Malzariey\FilamentDaterangepickerFilter\Fields\DateRangePicker;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('booking_type')
                    ->options(BookingType::class)->required(),
                Forms\Components\TextInput::make('booking_number')
                    ->required()
                    ->formatStateUsing(fn() => strtoupper(Str::random())),
             
                // Forms\Components\DatePicker::make('from')
                //     ->live()
                //     ->required(),
                // Forms\Components\DatePicker::make('to')
                //     ->required(),
                // DateRangePicker::make('date')
                //     ->separator(' to ')
                //     ->live()
                //     ->displayFormat('Y-MM-D')
                //     ->format('Y-MM-D'),
                //     Forms\Components\TextInput::make('booking_customer')
                //         ->required(),
                //   
                //     Forms\Components\TextInput::make('adults')
                //         ->numeric()
                //         ->default(1)
                //         ->required(),
                //     Forms\Components\TextInput::make('children')
                //         ->numeric()
                //         ->default(0)
                //         ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('booking_number')
                    ->description(fn($record) => $record->booking_type->name)
                    ->searchable(),
                Tables\Columns\TextColumn::make('room.room_number')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('from')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('to')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
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
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListBookings::route('/'),
            'create' => Pages\CreateBooking::route('/create'),
            'view' => Pages\ViewBooking::route('/{record}'),
            'edit' => Pages\EditBooking::route('/{record}/edit'),
        ];
    }
}
