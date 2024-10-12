<?php

namespace App\Livewire\Pms\Reservation;

use App\Enums\Status;
use App\Models\Booking;
use App\Models\BusinessSource;
use App\Models\RatePlan;
use App\Models\Room;
use App\Models\RoomType;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Livewire\Component;
use Livewire\Attributes\On;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms;
use Filament\Forms\Form;

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
            $room = Room::with('roomType')->find($room_id);
            $this->from = $from;
            $this->to = $to;
            $this->form->fill([
                'from' => $from,
                'to' => $to,
                'bookingReservations' => [
                    ['room_type' => $room->roomType->id, 'room' => $room_id, 'adults' => 2, 'children' => 0],

                ]
            ]);
        }
    }

    public function form(Form $form): Form
    {
        $rooms = Room::query()->orderBy('room_number', 'ASC')->get();
        $roomTypes = RoomType::whereHas('rooms')->with('ratePlans')->get();

        return $form
            ->schema([
                Forms\Components\Group::make([
                    Forms\Components\DatePicker::make('from')
                        ->label('Check-In')
                        ->format('d/m/Y')
                        ->live()
                        ->afterStateUpdated(fn($state) => $this->from = $state)
                        ->required(),
                    Forms\Components\DatePicker::make('to')
                        ->label('Check-Out')
                        ->format('d/m/Y')
                        ->live()
                        ->afterStateUpdated(fn($state) => $this->to = $state)
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
                                ->afterStateUpdated(function ($get, $set) use ($roomTypes) {
                                })
                                ->live(),

                            Forms\Components\Select::make('room')
                                ->options(fn($get) => $get('room_type') ? $rooms->where('room_type_id', $get('room_type'))->pluck('room_number', 'id') : [])
                                ->required()
                                ->searchable()
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

                                    $base_rate = $selectedRoomType?->id ? roomTypeBaseRate($selectedRoomType->id, $from) : 0;

                                    $rate_plan = $selectedRoomType?->ratePlans->where('id', $get('rate_plan'))->first()?->rate;
                                    $nights = $from->diffInDays($to);
                                    $total = ($base_rate + $rate_plan) * $nights;
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
                'from' => Carbon::createFromFormat('d/m/Y', $form['from'])->format('Y-m-d'),
                'to' => Carbon::createFromFormat('d/m/Y', $form['to'])->format('Y-m-d'),
                'master' => $key == 0 ? true : false
            ]);

            $reservation_count++;

            $nights = $booking_reservation->from->diffInDays($booking_reservation->to);

            for ($i = 0; $i < $nights; $i++) {
                $date = $booking_reservation->from->copy()->addDays($i);
                $booking->bookingTransactions()->create([
                    'booking_reservation_id' => $booking_reservation->id,
                    'rate' => $booking_reservation->room->roomType->base_rate,
                    'date' => $date,
                    'transaction_type' => 'room_charge',
                    'user_id' => auth()->id()
                ]);
            }
        }
    }





    public function render()
    {
        return view('livewire.pms.reservation.new-booking');
    }
}
