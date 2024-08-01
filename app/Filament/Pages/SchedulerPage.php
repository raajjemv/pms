<?php

namespace App\Filament\Pages;

use App\Models\Room;
use App\Models\RoomType;
use Carbon\Carbon;
use Filament\Pages\Page;

class SchedulerPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.scheduler-page';

    public $rooms;

    public $monthDays;

    public $startOfMonth, $endOfMonth;

    public function mount()
    {



        $startOfMonth = request('date') ? Carbon::parse(request('date'))->startOfMonth() : Carbon::parse('2024-08-01');

        $endOfMonth = request('date') ?  Carbon::parse(request('date'))->endOfMonth() : Carbon::parse('2024-08-15');

        $this->startOfMonth = $startOfMonth;
        $this->endOfMonth = $endOfMonth;

        $this->rooms = Room::with(['roomType', 'bookings' => function ($query) use ($startOfMonth, $endOfMonth) {
            $query->where(function ($query) use ($startOfMonth, $endOfMonth) {
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
}
