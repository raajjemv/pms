@props(['options' => []])

<div wire:ignore>
    <input  x-data="{
        init() {
            flatpickr(this.$refs.input, {
                plugins: [
                    new monthSelectPlugin({
                        shorthand: true, //defaults to false
                        dateFormat: 'Y-m', //defaults to 'F Y'
                        altFormat: 'Y-m', //defaults to 'F Y'
                        theme: 'dark' // defaults to 'light'
                    })
                ]
    
            });
        }
    }"  x-ref="input" type="text"
        {{ $attributes->merge(['class' => 'form-input w-full rounded-md shadow-sm']) }} />
</div>
