@extends('layouts.app')

@section('title', $release->title)
@section('meta_description', \Illuminate\Support\Str::limit(
    $release->description ?: $release->title.' by '.$profile->artist_name.'.',
    160
))

@section('og_image', $release->cover_url ?? '')
@section('og_image_alt', 'Cover artwork for ' . $release->title)

@push('head')
    <link rel="canonical" href="{{ route('music.show', $release->slug) }}">

    <meta property="og:type" content="website">
    <meta property="og:title"
          content="{{ $release->title }} | {{ $profile->artist_name }}">
    <meta property="og:url"
          content="{{ route('music.show', $release->slug) }}">
    <meta property="og:description"
          content="{{ \Illuminate\Support\Str::limit(
              $release->description ?: $release->title.' by '.$profile->artist_name.'.',
              160
          ) }}">

    @if ($release->cover_url)
        <meta property="og:image" content="{{ $release->cover_url }}">
        <meta property="og:image:alt"
              content="Cover artwork for {{ $release->title }}">
    @endif
@endpush

@section('content')
    <section class="section">
        <div class="container">
            <a class="d-inline-block mb-4" href="{{ route('music.index') }}">
                <span aria-hidden="true">←</span> All music
            </a>

            <div class="row g-4 g-lg-5">
                <div class="col-md-5 col-lg-4">
                    @if ($release->cover_url)
                        <img class="live-cover"
                             src="{{ $release->cover_url }}"
                             alt="Cover artwork for {{ $release->title }}"
                             fetchpriority="high"
                             decoding="async">
                    @else
                        <div class="live-cover live-cover--empty"
                             aria-label="No cover artwork">
                            {{ $release->title }}
                        </div>
                    @endif
                </div>

                <div class="col-md-7 col-lg-8">
                    <p class="eyebrow eyebrow--accent">
                        {{ $release->type->label() }}
                    </p>

                    <h1 class="statement">{{ $release->title }}</h1>

                    @if ($release->release_date)
                        <p class="text-body-secondary mt-3">
                            <time datetime="{{ $release->release_date->format('Y-m-d') }}">
                                {{ $release->release_date->format('j F Y') }}
                            </time>
                        </p>
                    @endif

                    @unless ($release->type->isOfficial())
                        <p class="small text-body-secondary">
                            Unofficial bootleg. Original composition and recording
                            rights belong to their respective owners.
                        </p>
                    @endunless

                    @if ($release->description)
                        <div class="lead-text live-copy my-4">{{ $release->description }}</div>
                    @endif

                    @if ($release->audio_url)
    <div
        class="js-audio-player my-4"
        data-audio-player
    >
        <audio
            data-audio
            preload="metadata"
            src="{{ $release->audio_url }}"
        ></audio>

        <button
            type="button"
            class="js-audio-player__play"
            data-audio-play
            aria-label="Play {{ $release->title }}"
        >
            <span data-play-icon>▶</span>
            <span data-pause-icon hidden>Ⅱ</span>
        </button>

        <div class="js-audio-player__body">
            <div class="js-audio-player__top">
                <div class="js-audio-player__info">
                    <span class="js-audio-player__label">
                        Now playing
                    </span>

                    <strong class="js-audio-player__title">
                        {{ $release->title }}
                    </strong>
                </div>

                <span class="js-audio-player__time">
                    <span data-current-time>0:00</span>
                    /
                    <span data-duration>0:00</span>
                </span>
            </div>

            <input
                type="range"
                class="js-audio-player__progress"
                data-audio-progress
                min="0"
                max="100"
                value="0"
                step="0.1"
                aria-label="Audio progress"
            >
        </div>

        <button
            type="button"
            class="js-audio-player__volume"
            data-audio-mute
            aria-label="Mute audio"
        >
            <span data-volume-icon>♪</span>
            <span data-muted-icon hidden>×</span>
        </button>
    </div>
@endif

                    @if ($release->links->isNotEmpty())
                        <div class="d-flex flex-wrap gap-2 my-4">
                            @foreach ($release->links as $link)
                                <x-arrow-button
                                    :href="$link->url"
                                    :external="true">
                                    {{ $link->label }}
                                </x-arrow-button>
                            @endforeach
                        </div>
                    @endif

                    @if ($release->embed_url)
                        <x-media-player
                            :url="$release->embed_url"
                            :title="$release->title"
                            class="my-4"
                        />
                    @endif

                    @if ($release->credits)
                        <div class="mt-4">
                            <h2 class="h5">Credits</h2>
                            <div class="live-copy text-body-secondary">{{ $release->credits }}</div>
                        </div>
                    @endif
                </div>
            </div>

            @if ($release->mediaItems->isNotEmpty())
                <section class="mt-5" aria-labelledby="related-media-title">
                    <h2 id="related-media-title" class="h3 mb-4">
                        Videos & Mixes
                    </h2>

                    <div class="row g-4">
                        @foreach ($release->mediaItems as $item)
                            <div class="col-lg-6">
                                <article class="featured-card h-100">
                                    <p class="eyebrow">{{ $item->type->label() }}</p>
                                    <h3 class="h5">{{ $item->title }}</h3>

                                    <x-media-player
                                        :url="$item->source_url"
                                        :title="$item->title"
                                        class="my-3"
                                    />

                                    @if ($item->description)
                                        <p class="live-copy text-body-secondary">{{ $item->description }}</p>
                                    @endif

                                    <x-arrow-button
                                        :href="$item->source_url"
                                        :external="true">
                                        Open original
                                    </x-arrow-button>
                                </article>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>
    </section>
@endsection