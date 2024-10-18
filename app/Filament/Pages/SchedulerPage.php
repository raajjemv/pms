<?php

namespace App\Filament\Pages;

use Carbon\Carbon;
use App\Models\Room;
use App\Models\Booking;
use App\Models\RoomType;
use Filament\Pages\Page;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;

class SchedulerPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Scheduler';

    protected static string $view = 'filament.pages.scheduler-page';

    protected ?string $heading = 'Scheduler';

    // public $monthDays;

    #[Url(keep: true, except: '')]
    public $date;

    #[On('refresh-scheduler')]
    public function refreshComponent() {}

    #[Computed]
    public function startOfMonth()
    {
        return Carbon::parse($this->date)->startOfMonth();
    }

    #[Computed]
    public function endOfMonth()
    {
        return Carbon::parse($this->date)->endOfMonth();
    }

    #[Computed]
    public function rooms()
    {
        $startOfMonth = $this->startOfMonth();

        $endOfMonth = $this->endOfMonth();

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
        return $rooms;
    }

    #[Computed]
    public function monthDays()
    {
        $this->date ?? today()->format('Y-m');

        $startOfMonth = $this->startOfMonth();

        $endOfMonth = $this->endOfMonth();

        $days = $startOfMonth->diffInDays($endOfMonth);
        $monthDays = [];
        for ($i = 0; $i < $days; $i++) {
            $monthDays[] = $startOfMonth->copy()->addDays($i);
        }
        // sleep(2);
        return $monthDays;
    }


    public function mount()
    {
        $this->date = $this->date ? $this->date : today()->format('Y-m');
    }


    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('admin|tenant_owner|sales_manager|front_desk');
    }
}
