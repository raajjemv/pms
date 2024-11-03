<?php

namespace App\Livewire\Pms\Inventory;

use Carbon\Carbon;
use Filament\Forms;
use Livewire\Component;
use App\Models\RoomType;
use Carbon\CarbonPeriod;
use Filament\Forms\Form;
use Livewire\Attributes\On;
use App\Models\RoomTypeRate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Livewire;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Concerns\InteractsWithForms;
use Malzariey\FilamentDaterangepickerFilter\Fields\DateRangePicker;

class Wizard extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $dateOptionData = [];

    public $channel;

    public string $modalId;


    public $activeTab = 'tab1';

    public function mount(): void
    {

        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
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
                    ->live()
                    ->multiple()
                    ->required(),

            ])
            ->statePath('dateOptionData');
    }

    public $formData = [];

    public $date;

    public $roomTypeSelections = [];

    #[Computed]
    public function selectedRoomTypes()
    {
        $roomTypeSlc = [];
        foreach ($this->roomTypeSelections as $roomTypeSelection) {
            $roomTypeId = explode('_', $roomTypeSelection)[0];
            $roomTypeSlc[][$roomTypeId] = explode('_', $roomTypeSelection)[1];
        }

        $result = collect($roomTypeSlc)
            ->groupBy(function ($item) {
                return key($item);
            })
            ->map(function ($items) {
                return RoomType::with(['ratePlans' => function ($q) use ($items) {
                    return $q->wherePivotIn('rate_plan_id', $items->pluck(key($items[0])));
                }])->find(key($items[0]));
                return $items
                    ->pluck(key($items[0]))
                    ->flatten()
                    ->unique()
                    ->values();
            })
            ->flatten();
        $this->formData = $result->toArray();
        return $result;
    }

    public function updateNewRates()
    {
        $this->validate([
            'formData.*.rate_plans.*.rate' => 'required|numeric',
        ], [
            'required' => 'Rate is required',
            'numeric' => 'Rate should be a number'
        ]);

        $explode_date = explode(' - ', $this->form->getstate()['dates']);
        [$from, $to] = [
            Carbon::createFromFormat('d/m/Y', $explode_date[0]),
            Carbon::createFromFormat('d/m/Y', $explode_date[1]),
        ];

        $period = CarbonPeriod::create($from, $to);

        foreach ($this->formData as $rt) {
            foreach ($rt['rate_plans'] as $rp) {
                foreach ($period as $dt) {
                    $dayOfWeek = strtolower($dt->format('l'));
                    if (in_array($dayOfWeek, $this->form->getstate()['days'])) {
                        $rate = RoomTypeRate::updateOrCreate(
                            ['room_type_id' => $rt['id'], 'rate_plan_id' => $rp['id'], 'date' => $dt->format('Y-m-d'), 'channel_group_id' => $this->channel],
                            ['rate' => $rp['rate'], 'tenant_id' => auth()->user()->current_tenant_id, 'user_id' => auth()->id()]
                        );
                    }
                }
            }
        }

        $this->dispatch('close-modal', id: $this->modalId);

        $this->dispatch('refresh-inventory');

        Notification::make()
            ->title('Bulk updated room rates successfully')
            ->success()
            ->send();

    }


    public function render()
    {
        return view('livewire.pms.inventory.wizard', []);
    }
}
