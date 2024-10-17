<?php

namespace App\Filament\Pages;

use Carbon\Carbon;
use App\Models\Room;
use App\Models\Booking;
use App\Models\RoomType;
use Filament\Pages\Page;
use Livewire\Attributes\On;

class SchedulerPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Scheduler';

    protected static string $view = 'filament.pages.scheduler-page';

    protected ?string $heading = 'Scheduler';

    // public $rooms;

    public $monthDays;

    public $startOfMonth, $endOfMonth;

    #[On('refresh-scheduler')]
    public function refreshComponent() {}


    protected function getViewData(): array
    {
        $startOfMonth = $this->startOfMonth;
        $endOfMonth = $this->endOfMonth;

        $rooms = Room::with(['roomType' => function ($q) use ($startOfMonth, $endOfMonth) {
            $q->with(['rates' => function ($qq) use ($startOfMonth, $endOfMonth) {
                $qq->whereBetween('date', [$startOfMonth, $endOfMonth]);
            }]);
        }, 'bookingReservations' => function ($query) use ($startOfMonth, $endOfMonth) {
            $query->with('customer', 'booking')->where(function ($query) use ($startOfMonth, $endOfMonth) {
                $query->where('from', '>=', $startOfMonth)
                    ->where('from', '<=', $endOfMonth);
            })->orWhere(function ($query) use ($startOfMonth, $endOfMonth) {
                $query->where('to', '>=', $startOfMonth)
                    ->where('to', '<=', $endOfMonth);
            })->orWhere(function ($query) use ($startOfMonth, $endOfMonth) {
                $query->where('from', '<', $startOfMonth)
                    ->where('to', '>', $endOfMonth);
            });
        }])
            ->get();
        return compact('rooms');
    }

    public function mount()
    {

        $startOfMonth = request('date') ? Carbon::parse(request('date'))->startOfMonth() : Carbon::now()->startOfMonth();

        $endOfMonth = request('date') ?  Carbon::parse(request('date'))->endOfMonth() : Carbon::now()->endOfMonth();

        $this->startOfMonth = $startOfMonth;
        $this->endOfMonth = $endOfMonth;

        $days = $startOfMonth->diffInDays($endOfMonth);
        $monthDays = [];
        for ($i = 0; $i < $days; $i++) {
            $monthDays[] = $startOfMonth->copy()->addDays($i);
        }

        $this->monthDays = $monthDays;
    }

   
    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('admin|tenant_owner|sales_manager|front_desk');
    }
}
