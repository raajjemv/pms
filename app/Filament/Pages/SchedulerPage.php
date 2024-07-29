<?php

namespace App\Filament\Pages;

use App\Models\RoomType;
use Carbon\Carbon;
use Filament\Pages\Page;

class SchedulerPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.scheduler-page';

    public $selectedMonth;
    public $totalDaysInMonth;
    public $rooms;

    public function mount()
    {

        $selectedMonth = Carbon::createFromDate(request('year', date('Y')), request('month', date('m')));
        $this->selectedMonth = $selectedMonth;
        $this->totalDaysInMonth =  $selectedMonth->daysInMonth();
        $this->rooms = RoomType::all();
    }
}
