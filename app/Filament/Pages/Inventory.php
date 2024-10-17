<?php

namespace App\Filament\Pages;

use Carbon\Carbon;
use Filament\Forms;
use App\Models\RatePlan;
use App\Models\RoomType;
use Filament\Pages\Page;
use App\Models\ChannelGroup;
use App\Models\RoomTypeRate;
use Filament\Actions\Action;
use Livewire\Attributes\Url;
use Filament\Facades\Filament;
use Livewire\Attributes\Computed;
use Filament\Support\Enums\MaxWidth;
use Filament\Notifications\Notification;

class Inventory extends Page
{
    public $monthDays;

    #[Url(except: '')]
    public $selectedChannelGroup;

    public $channelGroups;

    public $startOfMonth, $endOfMonth;

    public $roomTypes;

    protected static ?string $navigationIcon = 'heroicon-o-circle-stack';

    protected static string $view = 'filament.pages.inventory';

    #[Computed(persist: true)]
    public function roomTypes()
    {
        return RoomType::whereHas('ratePlans')->with([
            'ratePlans',
            'rates' => function ($query) {
                $query->where(function ($query) {
                    $query->where('date', '>=', $this->startOfMonth)
                        ->where('date', '<=', $this->endOfMonth)
                        ->where('channel_group_id', $this->selectedChannelGroup);
                });
            }
        ])->get();
    }

    public function mount()
    {
        $this->channelGroups = Filament::getTenant()->channelGroups;

        $this->selectedChannelGroup = defaultChannelGroup()->id;

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

    public function updateRoomRate($value = 0,  $plan, $roomType,  $date)
    {
        try {
            $rate = RoomTypeRate::updateOrCreate(
                ['room_type_id' => $roomType, 'rate_plan_id' => $plan, 'date' => $date, 'channel_group_id' => $this->selectedChannelGroup],
                ['rate' => $value, 'tenant_id' => auth()->user()->current_tenant_id, 'user_id' => auth()->id()]
            );
            Notification::make()
                ->title('Saved successfully')
                ->success()
                ->send();
        } catch (\Throwable $th) {
            Notification::make()
                ->title('An error occured! Please try again!')
                ->danger()
                ->send();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('filter_date')
                ->color('gray')
                ->icon('heroicon-m-calendar')
                ->modalHeading('Filter Date')
                ->form([
                    Forms\Components\DatePicker::make('date')
                ])
                ->modalWidth('sm'),
            Action::make('select_channel_group')
                ->icon('heroicon-m-squares-2x2')
                ->color('gray')
                ->label($this->channelGroups->where('id', $this->selectedChannelGroup)->first()->name ?? 'Select Pool')
                ->modalWidth(MaxWidth::Small)
                ->form(function () {
                    return [
                        Forms\Components\Select::make('channel_group')
                            ->options($this->channelGroups->pluck('name', 'id'))

                    ];
                })
                ->action(function ($data) {
                    $this->selectedChannelGroup = $data['channel_group'];

                    Notification::make()
                        ->title('Pool Changed')
                        ->success()
                        ->send();
                }),

            Action::make('bulk update')
                ->icon('heroicon-m-clipboard-document-check')
                ->visible($this->selectedChannelGroup ? true : false)
                ->form([
                    Forms\Components\Select::make('days')
                        ->options([
                            'sunday' => 'Sunday',
                            'monday' => 'Monday',
                            'tuesday' => 'Tuesday',
                            'wednesday' => 'Wednesday',
                            'thursday' => 'Thursday',
                            'friday' => 'Friday',
                            'saturday' => 'Saturday',
                        ])
                        ->multiple()
                        ->required(),
                    Forms\Components\Select::make('room_type')
                        ->options($this->roomTypes()->pluck('name', 'id'))
                        ->required()
                        ->live(),
                    Forms\Components\Select::make('rate_plan')
                        ->required()
                        ->visible(fn($get) => $get('room_type'))
                        ->options(fn($get) => $this->roomTypes()->where('id', $get('room_type'))->first()->ratePlans->pluck('name', 'id')),
                    Forms\Components\TextInput::make('rate')
                        ->required()
                ])
                ->action(function ($data) {
                    try {


                        foreach (collect($data['days']) as $dayName) {
                            $days = $this->daysByName($dayName);
                            foreach ($days as $day) {
                                $rate = RoomTypeRate::updateOrCreate(
                                    ['room_type_id' => $data['room_type'], 'rate_plan_id' => $data['rate_plan'], 'date' => $day, 'channel_group_id' => $this->selectedChannelGroup],
                                    ['rate' => $data['rate'], 'tenant_id' => auth()->user()->current_tenant_id, 'user_id' => auth()->id()]
                                );
                            }
                        }

                        Notification::make()
                            ->title('Bulk updated room rates successfully')
                            ->success()
                            ->send();
                    } catch (\Throwable $th) {
                        Notification::make()
                            ->title('An error occured, ' . $th->getMessage())
                            ->danger()
                            ->send();
                    }
                })
                ->closeModalByClickingAway(false)
                ->requiresConfirmation(),

        ];
    }

    public function daysByName($dayName)
    {
        $days = [];

        // Clone to avoid modifying the original startOfMonth
        $startDate = clone $this->startOfMonth;
        $startDate->modify('this ' . $dayName);

        $endDate = $this->endOfMonth;

        while ($startDate <= $endDate) {
            $days[] = $startDate->format('Y-m-d');
            $startDate->modify('+1 week');
        }

        return $days;
    }

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('admin|tenant_owner|sales_manager');
    }
}
