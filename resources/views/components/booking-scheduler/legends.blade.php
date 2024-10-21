<div class="relative" x-data="{ showLegend: false }">
    <x-filament::icon-button x-show="!showLegend" size="xl" color="gray" icon="heroicon-m-information-circle"
        @click="showLegend = !showLegend" />
    <x-filament::icon-button x-show="showLegend" size="xl" color="gray" icon="heroicon-m-x-circle"
        @click="showLegend = !showLegend" />
    <div @click.away="showLegend = false" x-cloak x-show="showLegend"
        class="absolute z-30 w-56 p-3 rounded shadow-lg bg-black/60 backdrop-blur top-7 right-2">
        <div class="grid grid-cols-2 gap-5">
            @foreach (\App\Enums\Status::cases() as $statusCase)
                <div class="flex items-center space-x-2">
                    <div>
                        <div class="{{ $statusCase->getColor() }} size-2"></div>
                    </div>
                    <div class="text-sm text-white">
                        {{ $statusCase->name }}
                    </div>

                </div>
            @endforeach
        </div>
    </div>
</div>
