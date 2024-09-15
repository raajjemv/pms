@props([])
<button  {{ $attributes->merge(['class' => 'border border-blue-400 text-black px-3 py-1.5 rounded outline-none hover:bg-gray-100 text-sm']) }}>
    {{ $slot }}
</button>
