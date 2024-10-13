<x-filament-panels::page>
    @if (!$selectedChannelGroup)
        <div class="font-medium"> Select a Channel Pool </div>
    @else
        <div class="overflow-x-scroll text-sm text-black rounded-lg bg-gray-50" x-data="rateUpdater">
            <div class="flex ">
                <div
                    class="bg-gray-50 sticky top-0 left-0 w-[200px] flex items-center font-semibold flex-none  px-1 border-[0.8px] border-gray-200">
                    Rooms
                </div>
                @foreach ($monthDays as $day)
                    <div @class([
                        'flex-none border-[0.8px] border-gray-200 flex items-center justify-center w-[90px] px-1 py-1',
                        'bg-amber-100' => $day->isFriday() || $day->isSaturday(),
                    ])>
                        <div class="text-center">
                            <div>{{ $day->format('D') }}</div>
                            <div class="font-semibold">{{ $day->format('d') }}</div>
                            <div>{{ $day->format('M') }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="">
                @foreach ($this->roomTypes() as $roomTypeKey => $roomType)
                    <div class=" w-max">
                        <div class="relative flex bg-zinc-200">
                            <div
                                class="sticky top-0 left-0 flex-none w-[200px] px-2 flex items-center border-[0.8px] bg-zinc-200 border-gray-300 font-semibold">
                                {{ $roomType->name }}
                            </div>
                            <div class="flex">
                                @foreach ($monthDays as $day)
                                    <div class="flex-none  w-[90px] border-[0.8px] border-gray-300">
                                        <div class="p-3">
                                        </div>

                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @foreach ($roomType->ratePlans as $ratePlan)
                            <div class="flex ">
                                <div
                                    class="sticky top-0 left-0 z-20 bg-gray-50 flex-none w-[200px] flex items-center px-1 border-[0.8px] border-gray-200 font-medium pl-3 py-1">
                                    {{ $ratePlan->name }}</div>
                                <div class="relative flex">
                                    @foreach ($monthDays as $day)
                                        <div key="day-{{ $day }}"
                                            class="flex-none  flex items-center w-[90px] p-0.5  border-[0.8px] border-gray-200">
                                            @php
                                                $rate = 20;
                                                $rateQ = $roomType->rates
                                                    ->where('rate_plan_id', $ratePlan->id)
                                                    ->where('date', $day->format('Y-m-d'))
                                                    ->first();
                                                $rate = $rateQ ? $rateQ->rate : $ratePlan->pivot->rate;
                                            @endphp
                                            <input
                                                x-on:change="handleRateUpdate({
                                            event: $event,
                                            ratePlan: '{{ $ratePlan->id }}',
                                            date: '{{ $day }}',
                                            roomType: '{{ $roomType->id }}'
                                        })"
                                                type="text" step="0.01" value="{{ $rate }}"
                                                class="w-full border border-gray-200 appearance-none">

                                        </div>
                                    @endforeach

                                </div>

                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>

        </div>
    @endif
    {{-- <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('rateUpdater', () => ({
                loading: false,
                handleRateUpdate(event) {
                    @this.updateRoomRate(
                        event.event.target.value,
                        event.ratePlan,
                        event.roomType,
                        event.date,
                    );
                    this.$wire.$refresh

                },

            }))
        })
    </script> --}}
</x-filament-panels::page>
@script
    <script>
        Alpine.data('rateUpdater', () => ({
            handleRateUpdate(event) {
                $wire.updateRoomRate(
                    event.event.target.value,
                    event.ratePlan,
                    event.roomType,
                    event.date,
                )
                // console.log(v.promise.value)
            },
        }))
    </script>
@endscript
