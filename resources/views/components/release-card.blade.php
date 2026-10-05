@props(['release'])

@php
    $isOfficial = in_array($release['type'], ['Official release', 'Remix', 'Mix'], true);
@endphp

<article class="release-card">
    <x-media
        class="release-card__cover"
        :src="'images/covers/' . $release['slug'] . '.jpg'"
        :alt="'Cover artwork for ' . $release['title']"
        ratio="1 / 1"
        label="Placeholder: cover artwork"
    />
    <p class="release-card__type">
        {{ $release['type'] }}
        @unless ($isOfficial)
            <span class="release-tag">Unofficial</span>
        @endunless
    </p>
    <h3 class="release-card__title">{{ $release['title'] }}</h3>
</article>