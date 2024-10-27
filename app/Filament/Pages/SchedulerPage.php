<?php

namespace App\Filament\Pages;

use Closure;
use Carbon\Carbon;
use Filament\Forms;
use App\Models\Room;
use App\Models\Booking;
use App\Models\RoomType;
use Filament\Pages\Page;
use Livewire\Attributes\On;
use Filament\Actions\Action;
use Livewire\Attributes\Url;
use Filament\Facades\Filament;
use Livewire\Attributes\Computed;

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

    public function quickReservationActions()
    {
        return Action::make('quickReservationActions')
            ->modalWidth('md')
            ->fillForm(fn($arguments) => [
                'from' => $arguments['from'],
                'to' => $arguments['to'],
            ])
            ->form(function ($arguments) {
                return [
                    Forms\Components\Radio::make('action')
                        ->options([
                            'reservation' => 'Reservation',
                            'maintenance' => 'Maintenance',
                        ])
                        ->descriptions([
                            'reservation' => 'Walking / Direct Reservations',
                            'maintenance' => 'Maintenance / Housekeeping',
                        ])
                        ->required()
                        ->live(),

                    Forms\Components\Group::make([
                        Forms\Components\DatePicker::make('from')
                            ->label('Maintenance start date')
                            ->format('d/m/Y')
                            ->live()
                            ->disabled()
                            ->required(),
                        Forms\Components\DatePicker::make('to')
                            ->label('Maintenance end date')
                            ->format('Y-m-d')
                            ->live()
                            ->native(false)
                            ->closeOnDateSelection()
                            ->disabledDates(fn($state) => roomReservationsByMonth(
                                $arguments['room_id'],
                                Carbon::parse($state)->startOfMonth(),
                                Carbon::parse($state)->endOfMonth()
                            )->toArray())
                            ->required()
                            ->rules([
                                fn(): Closure => function (string $attribute,  $value, Closure $fail) use ($arguments) {
                                    $nights = totolNights($arguments['from'], $value);

                                    if ($nights == 0) {
                                        $fail('Invalid date chosen!');
                                    }
                                    $roomReservationsByMonth = roomReservationsByMonth(
                                        $arguments['room_id'],
                                        Carbon::parse($arguments['from'])->startOfMonth(),
                                        Carbon::parse($value)->endOfMonth()
                                    );

                                    for ($i = 0; $i < $nights; $i++) {
                                        $date = Carbon::parse($arguments['from'])->copy()->addDays($i);
                                        if ($roomReservationsByMonth->contains($date->format('Y-m-d'))) {
                                            $fail('Pick another day. Your reservation falls to a booked date');
                                        }
                                    }
                                },
                            ]),
                    ])
                        ->columns(2)
                        ->visible(fn($get) => $get('action') == 'maintenance'),

                    Forms\Components\TextInput::make('remarks')
                        ->visible(fn($get) => $get('action') == 'maintenance')
                        ->required()
                ];
            })
            ->action(function ($arguments, $data) {
                if ($data['action'] == 'reservation') {
                    $this->dispatch(
                        'open-modal',
                        id: 'new-booking',
                        from: $arguments['from'],
                        to: $arguments['to'],
                        room_id: $arguments['room_id'],
                        room_type_id: $arguments['room_type_id'],
                    );
                    return;
                }
                $booking = Booking::create([
                    'booking_type' => 'maintenance',
                    'tenant_id' => Filament::getTenant()->id,
                    'booking_number' =>  strtoupper(str()->random()),
                    'booking_customer' => $data['remarks'],
                    'user_id' => auth()->id()
                ]);


                $from = Carbon::parse($arguments['from'])->setTimeFromTimeString(tenant()->check_in_time);
                $to = Carbon::parse($data['to'])->setTimeFromTimeString(tenant()->check_out_time);

                $booking_reservation = $booking->bookingReservations()->create([
                    'tenant_id' => Filament::getTenant()->id,
                    'room_id' => $arguments['room_id'],
                    'adults' => 0,
                    'children' => 0,
                    'rate_plan_id' => 1,
                    'booking_customer' => $data['remarks'],
                    'from' => $from,
                    'to' => $to,
                    'master' => true,
                    'status' => 'maintenance'
                ]);

                $nights = $booking_reservation->from->diffInDays($booking_reservation->to);

                for ($i = 0; $i < $nights; $i++) {
                    $date = $booking_reservation->from->copy()->addDays($i);
                    $booking->bookingTransactions()->create([
                        'booking_reservation_id' => $booking_reservation->id,
                        'rate' => 0,
                        'date' => $date,
                        'transaction_type' => 'room_charge',
                        'user_id' => auth()->id(),
                        'maintenance' => true
                    ]);
                }
            });
    }


    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('admin|tenant_owner|sales_manager|front_desk');
    }
}
