@props([
    'src' => null,
    'alt' => '',
    'ratio' => '4 / 3',
    'label' => 'Image placeholder',
    'priority' => false,
])

@php
    $exists = $src && file_exists(public_path($src));
@endphp

<div {{ $attributes->merge(['class' => 'media']) }} style="aspect-ratio: {{ $ratio }};">
    @if ($exists)
        <img
            class="media__img"
            src="{{ asset($src) }}"
            alt="{{ $alt }}"
            decoding="async"
            @if ($priority) fetchpriority="high" @else loading="lazy" @endif
        >
    @else
        <div class="media__placeholder" role="img" aria-label="{{ $label }}">
            <span>{{ $label }}</span>
        </div>
    @endif
</div>