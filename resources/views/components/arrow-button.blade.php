@props([
    'href',
    'variant' => 'accent', // accent | light | dark
    'external' => false,
])

<a
    href="{{ $href }}"
    {{ $attributes->merge(['class' => 'btn-arrow btn-arrow--' . $variant]) }}
    @if ($external) target="_blank" rel="noopener" @endif
>
    <span class="btn-arrow__label">{{ $slot }}</span>
    <span class="btn-arrow__icon" aria-hidden="true">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" focusable="false">
            <path d="M5 12h14M13 6l6 6-6 6"/>
        </svg>
    </span>
</a>