<div>
    
    <div class="flex items-center justify-between">
        <div>
            <x-filament::tabs label="Content tabs">
                <x-filament::tabs.item :active="$activeTab === 'tab1'" :disabled="$activeTab !== 'tab1'">
                    1. Selections
                </x-filament::tabs.item>

                <x-filament::tabs.item :active="$activeTab === 'tab2'" :disabled="$activeTab !== 'tab2'">
                    2. Date Range
                </x-filament::tabs.item>

                <x-filament::tabs.item :active="$activeTab === 'tab3'" :disabled="$activeTab !== 'tab3'">
                    3. Update
                </x-filament::tabs.item>
            </x-filament::tabs>
        </div>
        <div class="space-x-2">
            @if ($activeTab == 'tab1')
                <x-filament::button :disabled="count($roomTypeSelections) === 0" class="disabled:opacity-60" wire:click="$set('activeTab','tab2')">
                    Next
                </x-filament::button>
            @endif
            @if ($activeTab !== 'tab1')
                <x-filament::button color="gray"
                    wire:click="$set('activeTab','{{ $activeTab == 'tab2' ? 'tab1' : 'tab2' }}')">
                    Back
                </x-filament::button>
            @endif
            @if ($activeTab == 'tab2')
                <x-filament::button :disabled="!filled($dateOptionData['dates']) || count($dateOptionData['days']) === 0" wire:click="$set('activeTab','tab3')">
                    Next
                </x-filament::button>
            @endif

        </div>
    </div>
    <div>
        @if ($activeTab == 'tab1')
            <livewire:pms.inventory.wizard-room-type-selections wire:model.live="roomTypeSelections" />
        @endif
        @if ($activeTab == 'tab2')
            {{ $this->form }}
        @endif
        @if ($activeTab == 'tab3')
            @php

                $explode_date = explode(' - ', $this->form->getstate()['dates']);
                [$from, $to] = [
                    Carbon\Carbon::createFromFormat('d/m/Y', $explode_date[0]),
                    Carbon\Carbon::createFromFormat('d/m/Y', $explode_date[1]),
                ];
            @endphp
            <div class="p-3 text-sm text-gray-600">
                {{ $from->format('jS M Y') }} - {{ $to->format('jS M Y') }}
            </div>

            <form class="text-sm" wire:submit="updateNewRates">
                @foreach ($this->selectedRoomTypes as $pkey => $selectedRoomType)
                    <div class="p-2 ">
                        <div class="flex items-center p-2 bg-gray-200">
                            <div class="w-[60%] font-medium ">{{ $selectedRoomType->name }}</div>
                            <div class="w-[20%]">Base Rate</div>
                            <div class="w-[20%]">New Rate</div>
                        </div>
                        <div class="p-1 divide-y">
                            @foreach ($selectedRoomType->ratePlans as $key => $ratePlan)
                                <div class="flex items-center py-2 ">
                                    <div class="w-[60%]">{{ $selectedRoomType->name }} - {{ $ratePlan->name }}</div>
                                    <div class="w-[20%]">
                                        {{ roomTypeRate($selectedRoomType->id, $from, $ratePlan->id) }}
                                    </div>
                                    <div class="w-[20%]">
                                        <input type="number"
                                            wire:model="formData.{{ $loop->parent->index }}.rate_plans.{{ $loop->index }}.rate">

                                        @error('formData.' . $loop->parent->index . '.rate_plans.' . $loop->index .
                                            '.rate')
                                            <span class="error">{{ $message }}</span>
                                        @enderror

                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                <x-filament::button type="submit" class="mt-5"  wire:loading.attr="disabled">
                    <i class="fa-solid fa-circle-notch fa-spin" wire:loading></i> Submit
                </x-filament::button>
            </form>
        @endif
    </div>
</div>
