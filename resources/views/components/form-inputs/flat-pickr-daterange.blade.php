@props(['options' => []])

<div wire:ignore>
    <input x-data="{
        init() {
            flatpickr(this.$refs.input, {
                mode: 'range',
                dateFormat: 'Y-m-d'
            });
        }
    }" x-ref="input" type="text"
        {{ $attributes->merge(['class' => 'border-gray-300 w-full rounded-md shadow-sm text-gray-600']) }} />
</div>
