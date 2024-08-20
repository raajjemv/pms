<?php

namespace App\Filament\Pages;

use App\Models\RatePlan;
use App\Models\RoomType;
use App\Models\RoomTypeRate;
use Carbon\Carbon;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Filament\Forms;

class Inventory extends Page
{
    public $monthDays;

    public $startOfMonth, $endOfMonth;

    public $roomTypes;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.inventory';

    public function mount()
    {
        $startOfMonth = request('date') ? Carbon::parse(request('date'))->startOfMonth() : Carbon::now();

        $endOfMonth = request('date') ?  Carbon::parse(request('date'))->endOfMonth() : Carbon::now()->endOfMonth();

        $this->startOfMonth = $startOfMonth;

        $this->endOfMonth = $endOfMonth;

        $days = $startOfMonth->diffInDays($endOfMonth);

        $monthDays = [];

        for ($i = 0; $i < $days; $i++) {
            $monthDays[] = $startOfMonth->copy()->addDays($i);
        }

        $this->monthDays = $monthDays;

        $this->roomTypes = RoomType::whereHas('ratePlans')->with([
            'ratePlans',
            'rates' => function ($query) use ($startOfMonth, $endOfMonth) {
                $query->where(function ($query) use ($startOfMonth, $endOfMonth) {
                    $query->where('date', '>=', $startOfMonth)
                        ->where('date', '<=', $endOfMonth);
                });
            }
        ])->get();
    }

    public function updateRoomRate($value = 0,  $plan, $roomType,  $date)
    {
        try {
            $rate = RoomTypeRate::updateOrCreate(
                ['room_type_id' => $roomType, 'rate_plan_id' => $plan, 'date' => $date],
                ['rate' => $value, 'tenant_id' => auth()->user()->current_tenant_id, 'user_id' => auth()->id()]
            );
            Notification::make()
                ->title('Saved successfully')
                ->success()
                ->send();
        } catch (\Throwable $th) {
            Notification::make()
                ->title('An error occured! Please try again!')
                ->danger()
                ->send();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('bulk update')
                ->form([
                    Forms\Components\Select::make('day')
                        ->options([
                            'sunday' => 'Sunday',
                            'monday' => 'Monday',
                            'tuesday' => 'Tuesday',
                            'wednesday' => 'Wednesday',
                            'thursday' => 'Thursday',
                            'friday' => 'Friday',
                            'saturday' => 'Saturday',
                        ])
                        ->required(),
                    Forms\Components\Select::make('room_type')
                        ->options($this->roomTypes->pluck('name', 'id'))
                        ->required()
                        ->live(),
                    Forms\Components\Select::make('rate_plan')
                        ->required()
                        ->visible(fn($get) => $get('room_type'))
                        ->options(fn($get) => $this->roomTypes->where('id', $get('room_type'))->first()->ratePlans->pluck('name', 'id')),
                    Forms\Components\TextInput::make('rate')
                        ->required()
                ])
                ->action(function ($data) {
                    try {
                        $days = $this->daysByName($data['day']);
                        foreach ($days as $day) {
                            $rate = RoomTypeRate::updateOrCreate(
                                ['room_type_id' => $data['room_type'], 'rate_plan_id' => $data['rate_plan'], 'date' => $day],
                                ['rate' => $data['rate'], 'tenant_id' => auth()->user()->current_tenant_id, 'user_id' => auth()->id()]
                            );
                        }
                        Notification::make()
                            ->title('Bulk updated room rates successfully')
                            ->success()
                            ->send();
                    } catch (\Throwable $th) {
                        Notification::make()
                            ->title('An error occured, please try again!')
                            ->danger()
                            ->send();
                    }
                })
                ->requiresConfirmation(),

        ];
    }

    public function daysByName($dayName)
    {
        $fridays = [];
        $startDate = $this->startOfMonth->modify('this ' . $dayName);
        $endDate = $this->endOfMonth;

        for ($date = $startDate; $date->lte($endDate); $date->addWeek()) {
            $fridays[] = $date->format('Y-m-d');
        }
        return $fridays;
    }
}
