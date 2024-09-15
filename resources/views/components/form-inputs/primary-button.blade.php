@props([])
<button  {{ $attributes->merge(['class' => 'bg-blue-700 text-white px-3 py-1.5 rounded outline-none hover:bg-blue-600 text-sm']) }}>
    {{ $slot }}
</button>
