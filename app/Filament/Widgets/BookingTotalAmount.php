<?php

namespace App\Filament\Widgets;

use Illuminate\Support\Number;
use App\Filament\Pages\EditReservation;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class BookingTotalAmount extends BaseWidget
{
    use ExposesTableToWidgets;

    public $total, $paid;

    protected function getTablePage(): string
    {
        return EditReservation::class;
    }

    protected function getStats(): array
    {
        return [
            Stat::make('', Number::currency($this->total))
                ->description('Total Payable')
                ->color('danger'),
            Stat::make('', Number::currency($this->total - $this->paid))
                ->description('Balance')
                ->color('success'),
        ];
    }
}
