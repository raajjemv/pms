<?php

namespace App\Livewire\Pms\Reservation;

use Carbon\Carbon;
use Filament\Forms;
use App\Models\Room;
use App\Enums\Status;
use App\Models\Booking;
use Livewire\Component;
use App\Models\RatePlan;
use App\Models\RoomType;
use Filament\Forms\Form;
use Livewire\Attributes\On;
use App\Models\BusinessSource;
use Filament\Facades\Filament;
use App\Models\BookingReservation;
use Illuminate\Support\Collection;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Forms\Concerns\InteractsWithForms;
use Illuminate\Support\Facades\Cache;

class NewBooking extends Component implements HasForms
{
    use InteractsWithForms;

    public $from, $to;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([]);
    }


    #[On('open-modal')]
    public function initNewBooking($id, $from = NULL, $to = NULL, $room_id = null)
    {
        if ($id == 'new-booking') {
            $room = Room::find($room_id);

            $this->from = Carbon::parse($from)->setTime(14, 0, 0);
            $this->to = Carbon::parse($to)->setTime(12, 0, 0);
            $this->form->fill([
                'from' => $from,
                'to' => $to,
                'bookingReservations' => [
                    ['room_type' => $room->room_type_id, 'rate_plan' => roomTypeDefaultPlan($room->room_type_id)->id, 'room' => $room_id, 'adults' => 2, 'children' => 0],

                ]
            ]);
        }
    }

    public function form(Form $form): Form
    {
        $roomTypes = RoomType::whereHas('rooms')->with('ratePlans')->get();

        // 
        //     $reservationsOnSelectedDates = BookingReservation::query()
        //         ->whereHas('room', function ($q) {
        //             $q->where('room_type_id', 2);
        //         })
        //         ->whereBetween('from', [$startOfMonth, $endOfMonth])
        //         ->orWhereBetween('to', [$startOfMonth, $endOfMonth])
        //         ->get();
        return $form
            ->schema([
                Forms\Components\Group::make([
                    Forms\Components\DatePicker::make('from')
                        ->label('Check-In')
                        ->format('d/m/Y')
                        ->live()
                        ->afterStateUpdated(fn($state) => $this->from = Carbon::parse($state)->setTime(14, 0, 0))
                        ->required(),
                    Forms\Components\DatePicker::make('to')
                        ->label('Check-Out')
                        ->format('d/m/Y')
                        ->live()
                        ->afterStateUpdated(fn($state) => $this->to = Carbon::parse($state)->setTime(12, 0, 0))
                        ->required(),
                    Forms\Components\Select::make('status')
                        ->options(Status::getAllValues())
                        ->required(),
                    Forms\Components\Select::make('business_source')
                        ->options(BusinessSource::pluck('name', 'id')),

                ])->columns(4),

                Forms\Components\Repeater::make('bookingReservations')
                    ->label('')
                    ->schema([
                        Forms\Components\Group::make([
                            Forms\Components\Select::make('room_type')
                                ->options($roomTypes->pluck('name', 'id'))
                                ->required()
                                ->searchable()
                                ->live(),

                            Forms\Components\Select::make('rate_plan')
                                ->options(fn($get) => $get('room_type') ? $roomTypes->where('id', $get('room_type'))->first()->ratePlans->pluck('name', 'id') : [])
                                ->required()
                                ->searchable()
                                ->afterStateUpdated(function ($get, $set) use ($roomTypes) {})
                                ->live(),

                            Forms\Components\Select::make('room')
                                ->options(function ($get, $set) use (&$rooms): Collection {
                                    $room_type_id = $get('room_type');
                                    $reservations = BookingReservation::query()
                                        ->whereHas('room', function ($q) use ($room_type_id) {
                                            $q->where('room_type_id', $room_type_id);
                                        })
                                        ->whereBetween('from', [$this->from, $this->to])
                                        ->orWhereBetween('to', [$this->from, $this->to])
                                        ->get();
                                    return Room::query()
                                        ->where('room_type_id', $get('room_type'))
                                        ->whereNotIn('id', $reservations->pluck('room_id'))
                                        ->pluck('room_number', 'id');
                                })
                                ->required()
                                // ->searchable()
                                ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                ->live(),

                            Forms\Components\TextInput::make('adults')
                                ->numeric()
                                ->required()
                                ->default(2)
                                ->live(),
                            Forms\Components\TextInput::make('children')
                                ->numeric()
                                ->required()
                                ->default(0)
                                ->live(),
                            Forms\Components\Placeholder::make('price')
                                ->content(function ($get) use ($roomTypes) {
                                    $selectedRoomType = $roomTypes->where('id', $get('room_type'))->first();


                                    $from = Carbon::parse($this->from);

                                    $to = Carbon::parse($this->to);


                                    $base_rate = roomTypeRate($selectedRoomType->id, $from->format('Y-m-d'), $get('rate_plan'));

                                    // return $rate_plan = $selectedRoomType?->ratePlans->where('id', $get('rate_plan'))->first();

                                    $nights = totolNights($from, $to);

                                    $total = $base_rate * $nights;

                                    return filled($total) ? number_format($total, 2) : 0;
                                })
                                ->live(),
                        ])->columns(6),
                    ])

                    ->required()
                    ->minItems(1)
                    ->itemLabel('Rooms')
                    ->addActionLabel('Add Room'),

                Forms\Components\Group::make([
                    Forms\Components\TextInput::make('guest_name')
                        ->required(),
                    Forms\Components\TextInput::make('phone'),
                    Forms\Components\TextInput::make('email')
                        ->required(),
                ])
                    ->columns(3)

            ])
            ->statePath('data')
            ->model(Booking::class);
    }


    public function createBooking()
    {
        $form = $this->form->getState();
        $from = Carbon::createFromFormat('d/m/Y H:i:s', $form['from'] . '14:00:00');
        $to = Carbon::createFromFormat('d/m/Y H:i:s', $form['to'] . '12:00:00');

        $booking = Booking::create([
            'booking_type' => 'direct',
            'tenant_id' => Filament::getTenant()->id,
            'booking_number' =>  strtoupper(str()->random()),
            'booking_customer' => $form['guest_name'],
            'booking_email' => $form['email'],
            'user_id' => auth()->id()
        ]);

        $reservation_count = 1;

        foreach (collect($form['bookingReservations']) as $key => $reservation) {

            $customer_iteration = collect($form['bookingReservations'])->count() == 1 ? NULL : ' - ' . $reservation_count;

            $booking_reservation = $booking->bookingReservations()->create([
                'tenant_id' => Filament::getTenant()->id,
                'room_id' => $reservation['room'],
                'adults' => $reservation['adults'],
                'children' => $reservation['children'],
                'rate_plan_id' => $reservation['rate_plan'],
                'booking_customer' => $booking->booking_customer . $customer_iteration,
                'from' => $from,
                'to' => $to,
                'master' => $key == 0 ? true : false
            ]);

            $reservation_count++;

            $nights = $booking_reservation->from->diffInDays($booking_reservation->to);

            for ($i = 0; $i < $nights; $i++) {
                $date = $booking_reservation->from->copy()->addDays($i);
                $booking->bookingTransactions()->create([
                    'booking_reservation_id' => $booking_reservation->id,
                    'rate' => roomTypeRate($booking_reservation->room->room_type_id, $from->format('Y-m-d'), $reservation['rate_plan']),
                    'date' => $date,
                    'transaction_type' => 'room_charge',
                    'user_id' => auth()->id()
                ]);
            }
        }

        Notification::make()
            ->title('Reservation made successfully')
            ->success()
            ->send();

        $this->dispatch('refresh-scheduler');

        $this->dispatch('close-modal', id: 'new-booking');
    }





    public function render()
    {
        return view('livewire.pms.reservation.new-booking');
    }
}
