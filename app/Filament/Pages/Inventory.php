<?php

namespace App\Filament\Pages;

use App\Models\RatePlan;
use App\Models\RoomType;
use App\Models\RoomTypeRate;
use Carbon\Carbon;
use Filament\Pages\Page;
use Filament\Notifications\Notification;

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
}
