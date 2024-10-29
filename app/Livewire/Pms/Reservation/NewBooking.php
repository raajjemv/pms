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
use App\Enums\BookingType;
use Livewire\Attributes\On;
use App\Models\BusinessSource;
use Filament\Facades\Filament;
use App\Services\BookingService;
use Livewire\Attributes\Computed;
use App\Http\Traits\CachedQueries;
use App\Models\BookingReservation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Services\ReservationService;
use Illuminate\Support\Facades\Cache;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Forms\Concerns\InteractsWithForms;

class NewBooking extends Component implements HasForms
{
    use InteractsWithForms;
    use CachedQueries;

    public $from, $to;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([]);
    }


    #[On('open-modal')]
    public function initNewBooking($id, $from = NULL, $to = NULL, $room_id = null, $room_type_id = null)
    {
        if ($id == 'new-booking') {
            // $room = Room::find($room_id);

            $this->from = Carbon::parse($from)->setTimeFromTimeString(tenant()->check_in_time);
            $this->to = Carbon::parse($to)->setTimeFromTimeString(tenant()->check_out_time);
            $this->form->fill([
                'from' => $from,
                'to' => $to,
                'status' => 'reserved',
                'booking_type' => 'direct',
                'bookingReservations' => [
                    ['room_type' => $room_type_id, 'rate_plan' => roomTypeDefaultPlan($room_type_id)->id, 'room' => $room_id, 'adults' => 2, 'children' => 0],

                ]
            ]);
        }
    }


    public function form(Form $form): Form
    {
        $roomTypes = static::roomTypes();

        return $form
            ->schema([
                Forms\Components\Group::make([
                    Forms\Components\DatePicker::make('from')
                        ->label('Check-In')
                        ->format('d/m/Y')
                        ->live()
                        ->afterStateUpdated(fn($state) => $this->from = Carbon::parse($state)->setTimeFromTimeString(tenant()->check_in_time))
                        ->required(),
                    Forms\Components\DatePicker::make('to')
                        ->label('Check-Out')
                        ->format('d/m/Y')
                        ->live()
                        ->afterStateUpdated(fn($state) => $this->to = Carbon::parse($state)->setTimeFromTimeString(tenant()->check_out_time))
                        ->required(),

                    Forms\Components\Select::make('status')
                        ->options(Status::class)
                        ->required(),


                    Forms\Components\Select::make('booking_type')
                        ->options(BookingType::class)
                        ->required()
                        ->live(),

                    Forms\Components\Select::make('business_source')
                        ->options(static::businessSources()->pluck('name', 'id'))
                        ->searchable()
                        ->preload()
                        ->required()
                        ->visible(fn($get) => $get('booking_type') !== 'direct'),

                    Forms\Components\Select::make('booking_type_reference')
                        ->options([
                            'walk-in' => 'Walk-In',
                            'phone' => 'Phone',
                            'website' => 'Website',
                            'email' => 'Email'
                        ])
                        ->visible(fn($get) => $get('booking_type') == 'direct'),

                ])->columns(5),

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

                                    $base_rate = $selectedRoomType?->id ? roomTypeRate($selectedRoomType->id, $from->format('Y-m-d'), $get('rate_plan')) : 0;

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
        $data = $this->form->getState();



        DB::transaction(function () use ($data) {
            $bookingService = new BookingService;
            $reservationService = new ReservationService;


            $booking = $bookingService->create($data);


            $reservation_count = 1;

            $from = Carbon::createFromFormat('d/m/Y H:i', $data['from'] . ' ' . tenant()->check_in_time);
            $to = Carbon::createFromFormat('d/m/Y H:i', $data['to'] . ' ' . tenant()->check_out_time);

            foreach (collect($data['bookingReservations']) as $key => $reservation) {

                $customer_iteration = collect($data['bookingReservations'])->count() == 1 ? NULL : ' - ' . $reservation_count;

                $reservation['master'] = $key == 0 ? true : false;
                $reservation['from'] = $from;
                $reservation['to'] = $to;
                $reservation['guest_name'] = $data['guest_name'] . $customer_iteration;
                $reservation['status'] = $data['status'];

                $booking_reservation = $reservationService->create($booking, $reservation);

                $blockConnectingRooms = $reservationService->blockConnectingRooms($booking_reservation);


                $reservation_count++;
            }
        });

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
