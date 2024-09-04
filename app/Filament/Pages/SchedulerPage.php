<?php

namespace App\Filament\Pages;

use App\Models\Booking;
use App\Models\Room;
use App\Models\RoomType;
use Carbon\Carbon;
use Filament\Pages\Page;

class SchedulerPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Scheduler';

    protected static string $view = 'filament.pages.scheduler-page';

    public $rooms;

    public $monthDays;

    public $startOfMonth, $endOfMonth;

    public  $bookingSummary;

    public function mount()
    {

        $startOfMonth = request('date') ? Carbon::parse(request('date'))->startOfMonth() : Carbon::now()->startOfMonth();

        $endOfMonth = request('date') ?  Carbon::parse(request('date'))->endOfMonth() : Carbon::now()->endOfMonth();

        $this->startOfMonth = $startOfMonth;
        $this->endOfMonth = $endOfMonth;

        $this->rooms = Room::with(['roomType' => function ($q) use ($startOfMonth, $endOfMonth) {
            $q->with(['rates' => function ($qq) use ($startOfMonth, $endOfMonth) {
                $qq->whereBetween('date', [$startOfMonth, $endOfMonth]);
            }]);
        }, 'bookings' => function ($query) use ($startOfMonth, $endOfMonth) {
            $query->with('customer')->where(function ($query) use ($startOfMonth, $endOfMonth) {
                $query->where('from', '>=', $startOfMonth)
                    ->where('from', '<=', $endOfMonth);
            })->orWhere(function ($query) use ($startOfMonth, $endOfMonth) {
                $query->where('to', '>=', $startOfMonth)
                    ->where('to', '<=', $endOfMonth);
            })->orWhere(function ($query) use ($startOfMonth, $endOfMonth) {
                $query->where('from', '<', $startOfMonth)
                    ->where('to', '>', $endOfMonth);
            });
        }])->get();

        $days = $startOfMonth->diffInDays($endOfMonth);
        $monthDays = [];
        for ($i = 0; $i < $days; $i++) {
            $monthDays[] = $startOfMonth->copy()->addDays($i);
        }

        $this->monthDays = $monthDays;
    }

    public function viewBookingSummary(Booking $booking)
    {
        $this->bookingSummary = $booking;
        $this->dispatch('open-modal', id: 'booking-summary');
    }
}
