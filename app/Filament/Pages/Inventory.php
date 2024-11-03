<?php

namespace App\Filament\Pages;

use Carbon\Carbon;
use Filament\Forms;
use App\Models\RatePlan;
use App\Models\RoomType;
use Carbon\CarbonPeriod;
use Filament\Pages\Page;
use Livewire\Attributes\On;
use App\Models\ChannelGroup;
use App\Models\RoomTypeRate;
use Filament\Actions\Action;
use Livewire\Attributes\Url;
use Filament\Facades\Filament;
use Livewire\Attributes\Computed;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\View;
use Filament\Notifications\Notification;
use Coolsam\FilamentFlatpickr\Forms\Components\Flatpickr;
use Malzariey\FilamentDaterangepickerFilter\Fields\DateRangePicker;

class Inventory extends Page
{

    #[Url(except: '')]
    public $selectedChannelGroup;

    public $channelGroups;

    protected static ?string $navigationIcon = 'heroicon-o-circle-stack';

    protected static string $view = 'filament.pages.inventory';

    #[Url(keep: true, except: '')]
    public $date;

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
        $this->date = $this->date ? $this->date : today()->format('Y-m');

        $this->channelGroups = Filament::getTenant()->channelGroups;

        $this->selectedChannelGroup = defaultChannelGroup()->id;
    }

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

    #[On('refresh-inventory')]
    public function reloadComponent()
    {
        unset($this->roomTypes);
    }


    protected function getHeaderActions(): array
    {
        return [
            Action::make('filter_date')
                ->color('gray')
                ->icon('heroicon-m-calendar')
                ->modalHeading('Filter Date')
                ->form([
                    Flatpickr::make('date')
                        ->monthSelect()
                        ->theme(\Coolsam\FilamentFlatpickr\Enums\FlatpickrTheme::DARK)
                ])
                ->action(function ($data) {
                    $this->date = $data['date'];
                })
                ->modalWidth('sm'),
            Action::make('select_channel_group')
                ->icon('heroicon-m-squares-2x2')
                ->color('gray')
                ->label($this->channelGroups->where('id', $this->selectedChannelGroup)->first()->name ?? 'Select Pool')
                ->modalWidth(MaxWidth::Small)
                ->fillForm(fn() => [
                    'channel_group' => $this->selectedChannelGroup
                ])
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
            Action::make('update wizard')
                ->modalWidth('7xl')
                ->modalFooterActions(fn() => [])
                ->modalHeading('Inventory Wizard')
                ->modalContent(static function ($livewire) {
                    return View::make('pms.inventory.wizard-action', [
                        'channelGroup' => $livewire->selectedChannelGroup,
                        'modalId' => $livewire->getId() . '-action',

                    ]);
                }),

            Action::make('bulk update')
                ->icon('heroicon-m-clipboard-document-check')
                ->visible($this->selectedChannelGroup ? true : false)
                ->form([
                    DateRangePicker::make('dates')
                        ->displayFormat('DD/MM/YYYY')
                        ->format('DD/MM/YYYY'),
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


                        $dates = explode(' - ', $data['dates']);
                        $from = Carbon::createFromFormat('d/m/Y', $dates[0]);
                        $to = Carbon::createFromFormat('d/m/Y', $dates[1]) ?? $from;

                        $period = CarbonPeriod::create($from, $to);


                        foreach ($period as $dt) {
                            $dayOfWeek = strtolower($dt->format('l'));
                            if (in_array($dayOfWeek, $data['days'])) {
                                $rate = RoomTypeRate::updateOrCreate(
                                    ['room_type_id' => $data['room_type'], 'rate_plan_id' => $data['rate_plan'], 'date' => $dt->format('Y-m-d'), 'channel_group_id' => $this->selectedChannelGroup],
                                    ['rate' => $data['rate'], 'tenant_id' => auth()->user()->current_tenant_id, 'user_id' => auth()->id()]
                                );
                            }
                        }



                        unset($this->roomTypes);

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


    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('admin|tenant_owner|sales_manager');
    }
}
