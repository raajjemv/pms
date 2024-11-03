<div class="grid grid-cols-3 gap-5 my-3">
    @foreach ($roomTypes as $key => $roomType)
        <div class="border rounded-lg oveflow-hidden">
            <div class="p-2 bg-gray-200">
                {{ $roomType->name }}
            </div>
            <div class="py-2 pl-3 text-sm">
                @foreach ($roomType->ratePlans as $ratePlan)
                    <div class="py-1">
                        <label class="space-x-2">
                            <input wire:key="rt-{{ $loop->parent->index }}-rp-{{ $loop->index }}" type="checkbox" value="{{ $roomType->id }}_{{ $ratePlan->id }}"
                                wire:model="value" />
                            <span>{{ $ratePlan->name }}</span>
                        </label>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
</div>
