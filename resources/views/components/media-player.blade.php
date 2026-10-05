@props([
    'url' => null,
    'title' => 'Media player',
])

@php
    $embed = \App\Support\MediaEmbed::resolve($url);
@endphp

@if ($embed)
    <div {{ $attributes->class(['media-player']) }}>
        <div @class([
            'media-player__frame',
            'ratio ratio-16x9' => $embed['video'],
        ])>
            <iframe
                src="{{ $embed['src'] }}"
                title="{{ $embed['provider'] }} player: {{ $title }}"
                width="100%"
                @if ($embed['height'])
                    height="{{ $embed['height'] }}"
                @endif
                loading="lazy"
                allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture"
                referrerpolicy="strict-origin-when-cross-origin"
                allowfullscreen
            ></iframe>
        </div>
    </div>
@endif