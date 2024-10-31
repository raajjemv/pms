@props(['bookingType'])
<span title="{{ $bookingType }}">
    <x-filament::icon icon="{{ $bookingType->getIcon() }}"
        {{ $attributes->merge(['class' => 'text-white size-5 dark:text-gray-400']) }}/>
</span>
