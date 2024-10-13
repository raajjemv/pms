@props([
    'alias' => null,
    'class' => '',
    'icon' => null,
    'title' => 'hello'
])

@php
    $icon = ($alias ? \Filament\Support\Facades\FilamentIcon::resolve($alias) : null) ?: ($icon ?? $slot);
@endphp

@if ($icon instanceof \Illuminate\Contracts\Support\Htmlable)
    <span title="{{ $title }}" {{ $attributes->class($class) }}>
        {{ $icon }}
    </span>
@elseif (str_contains($icon, '/'))
    <img title="{{ $title }}"
        {{
            $attributes
                ->merge(['src' => $icon])
                ->class($class)
        }}
    />
@else
    @svg(
        $icon,
        $class,
        array_filter($attributes->merge(['title' => 'hello'])->getAttributes()),
    )
@endif
