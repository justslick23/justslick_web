@extends('layouts.app')

@section('title', $profile->artist_name)

@section('meta_description', $profile->short_bio
    ?: 'Discover music, videos and booking information for '.$profile->artist_name.'.')

@section('content')
    @php
        $focusAreas = [
            'Music production',
            'DJ sets',
            'Collaborations',
            'Amapiano & beyond',
        ];

        // Keep the two-part heading used by the current design.
        $nameParts = explode(' ', trim($profile->artist_name), 2);

        $hasAbout = filled($profile->biography)
            || filled($profile->short_bio)
            || $aboutPhoto
            || $galleryPhotos->isNotEmpty();
    @endphp

    {{-- Hero --}}
    <section class="hero-wrap">
        <div class="hero-card {{ $heroPhoto ? 'hero-card--photo' : '' }}">
            @if ($heroPhoto)
                <img
                    class="hero-card__img"
                    src="{{ $heroPhoto->image_url }}"
                    alt="{{ $heroPhoto->alt_text }}"
                    width="{{ $heroPhoto->width }}"
                    height="{{ $heroPhoto->height }}"
                    style="object-position: {{ config('site.hero_focus', 'center') }};"
                    fetchpriority="high"
                    decoding="async"
                >
            @endif

            @if ($profile->award)
                <div class="hero-card__top">
                    <span class="award-pill">{{ $profile->award }}</span>
                </div>
            @endif

            <div class="hero-card__main">
                <div>
                    <p class="hero-card__hello">Hey, I'm</p>

                    <h1 class="hero-title">
                        <span>{{ $nameParts[0] }}</span>
                        @if (isset($nameParts[1]))
                            <span>{{ $nameParts[1] }}</span>
                        @endif
                    </h1>

                    <div class="d-flex flex-wrap gap-3 mt-4">
                        @if ($featured || $releases->isNotEmpty())
                            <x-arrow-button href="#music" variant="light">
                                Listen
                            </x-arrow-button>
                        @endif

                        <a class="btn btn-ghost-light" href="#book">
                            Book {{ $profile->artist_name }}
                        </a>
                    </div>
                </div>

                <div class="hero-tagline">
                    <p class="hero-tagline__title">
                        {{ $profile->tagline }}
                        @if ($profile->location)
                            <span> · {{ $profile->location }}</span>
                        @endif
                    </p>

                    @if ($profile->short_bio)
                        <p class="hero-tagline__text">{{ $profile->short_bio }}</p>
                    @endif
                </div>
            </div>

            <ul class="hero-list">
                @foreach ($focusAreas as $i => $area)
                    <li>
                        <span class="hero-list__num" aria-hidden="true">
                            {{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}
                        </span>
                        {{ $area }}
                    </li>
                @endforeach
            </ul>
        </div>
    </section>

    {{-- Social and streaming profiles --}}
    @if ($platforms->isNotEmpty())
        <section class="platform-strip" aria-label="Streaming and social profiles">
            <div class="container">
                <div class="platform-strip__inner">
                    <p class="platform-strip__label">
                        Find {{ $profile->artist_name }} on
                    </p>

                    <ul class="platform-strip__list">
                        @foreach ($platforms as $platform)
                            <li>
                                <a href="{{ $platform['url'] }}"
                                   target="_blank" rel="noopener noreferrer">
                                    {{ $platform['name'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </section>
    @endif

    {{-- Music --}}
    <section class="section" id="music">
        <div class="container">
            @if ($featured || $releases->isNotEmpty())
                <div class="section-head">
                    <div>
                        <p class="eyebrow eyebrow--accent">Music</p>
                        <h2 class="statement">Latest and selected</h2>
                    </div>
                </div>
            @else
                <p class="text-body-secondary">Music will appear here soon.</p>
            @endif

            @if ($featured)
                <div class="featured-card">
                    <div class="row g-4 g-lg-5 align-items-center">
                        <div class="col-md-5 col-lg-4">
                            @if ($featured->cover_url)
                                <img class="live-cover"
                                     src="{{ $featured->cover_url }}"
                                     alt="Cover artwork for {{ $featured->title }}"
                                     loading="lazy" decoding="async">
                            @else
                                <div class="live-cover live-cover--empty"
                                     aria-label="No cover artwork">
                                    {{ $featured->title }}
                                </div>
                            @endif
                        </div>

                        <div class="col-md-7 col-lg-8">
                            <p class="eyebrow">Featured release</p>
                            <h3 class="featured__title">{{ $featured->title }}</h3>

                            <p class="release-type">
                                {{ $featured->type->label() }}
                                @unless ($featured->type->isOfficial())
                                    <span class="badge text-bg-secondary ms-2">
                                        Unofficial
                                    </span>
                                @endunless
                            </p>

                            @if ($featured->audio_url)
                            <div
                                class="js-audio-player"
                                data-audio-player
                            >
                                <audio
                                    data-audio
                                    preload="metadata"
                                    src="{{ $featured->audio_url }}"
                                ></audio>
                        
                                <button
                                    type="button"
                                    class="js-audio-player__play"
                                    data-audio-play
                                    aria-label="Play {{ $featured->title }}"
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
                                                {{ $featured->title }}
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
                            @if ($featured->description)
                                <p class="lead-text live-copy">{{ $featured->description }}</p>
                            @endif
                            @if ($featured->embed_url)
                            <h3 class="featured__title">
                                <a class="text-decoration-none"
                                   href="{{ route('music.show', $featured->slug) }}">
                                    {{ $featured->title }}
                                </a>
                            </h3>
                        @endif
                            @foreach ($featured->links as $link)
                                <x-arrow-button
                                    :href="$link->url"
                                    :external="true"
                                    class="me-2 mb-2">
                                    {{ $link->label }}
                                </x-arrow-button>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            @if ($releases->isNotEmpty())
                <h3 class="h5 mt-5 mb-4">Selected music</h3>
                <a class="d-inline-block mb-4" href="{{ route('music.index') }}">
                    View all music <span aria-hidden="true">↗</span>
                </a>
                <div class="row row-cols-2 row-cols-lg-4 g-3 g-lg-4">
                    @foreach ($releases as $release)
                        <div class="col">
                            <article class="live-release h-100">
                                @if ($release->cover_url)
                                    <img class="live-cover"
                                         src="{{ $release->cover_url }}"
                                         alt="Cover artwork for {{ $release->title }}"
                                         loading="lazy" decoding="async">
                                @else
                                    <div class="live-cover live-cover--empty"
                                         aria-label="No cover artwork">
                                         <h4 class="h6 mt-3 mb-2">
                                            <a href="{{ route('music.show', $release->slug) }}">
                                                {{ $release->title }}
                                            </a>
                                        </h4>
                                    </div>
                                @endif

                                <h4 class="h6 mt-3 mb-2">{{ $release->title }}</h4>

                                <p class="small text-body-secondary mb-2">
                                    {{ $release->type->label() }}
                                    @unless ($release->type->isOfficial())
                                        · Unofficial
                                    @endunless
                                </p>

                                <div class="d-flex flex-wrap gap-2">
                                    @foreach ($release->links as $link)
                                        <a class="small"
                                           href="{{ $link->url }}"
                                           target="_blank"
                                           rel="noopener noreferrer"
                                           aria-label="Listen to {{ $release->title }} on {{ $link->label }}">
                                            {{ $link->label }} ↗
                                        </a>
                                    @endforeach
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- Latest video --}}
    @if ($latestVideo)
        <section class="section pt-0" id="watch">
            <div class="container">
                <div class="section-head">
                    <div>
                        <p class="eyebrow eyebrow--accent">Watch</p>
                        <h2 class="statement">Latest video</h2>
                    </div>
                </div>

                <div class="featured-card">
                    <p class="eyebrow">{{ $latestVideo->type->label() }}</p>
                    <h3 class="h2">{{ $latestVideo->title }}</h3>

                    @if ($latestVideo->description)
                        <p class="lead-text live-copy">{{ $latestVideo->description }}</p>
                    @endif
                    <x-media-player
                    :url="$latestVideo->source_url"
                    :title="$latestVideo->title"
                    class="my-4"
                />
                    <x-arrow-button :href="$latestVideo->source_url" :external="true">
                        Watch video
                    </x-arrow-button>
                </div>
            </div>
        </section>
    @endif

    {{-- About --}}
   {{-- About --}}
@if ($hasAbout)
<section class="section pt-0" id="about">
    <div class="container">

        {{-- About intro --}}
        <div class="row g-4 g-lg-5 align-items-start mb-5">
            <div class="col-lg-5">
                <p class="eyebrow eyebrow--accent">Behind the music</p>

                <h2 class="statement statement--xl">
                    One producer, many rooms.
                </h2>

                @if ($aboutPhoto)
                    <figure class="mt-4 mb-0">
                        <img
                            class="live-about-photo"
                            src="{{ $aboutPhoto->image_url }}"
                            alt="{{ $aboutPhoto->alt_text }}"
                            width="{{ $aboutPhoto->width }}"
                            height="{{ $aboutPhoto->height }}"
                            loading="lazy"
                            decoding="async"
                        >

                        @if ($aboutPhoto->credit)
                            <figcaption class="small text-body-secondary mt-2">
                                Photo: {{ $aboutPhoto->credit }}
                            </figcaption>
                        @endif
                    </figure>
                @endif
            </div>

            {{-- Short introduction only --}}
            <div class="col-lg-6 offset-lg-1">
                @if ($profile->short_bio)
                    <p class="about-intro-copy">
                        {{ $profile->short_bio }}
                    </p>
                @endif

                <x-arrow-button href="#book" class="mt-3">
                    Book {{ $profile->artist_name }}
                </x-arrow-button>
            </div>
        </div>

        {{-- Full biography --}}
        @if ($profile->biography)
            @php
                $bioParagraphs = preg_split(
                    '/\R\s*\R/',
                    trim($profile->biography)
                );
            @endphp

            <div class="about-biography">
                <div class="about-biography__heading">
                    <p class="eyebrow">Biography</p>
                </div>

                <div class="about-biography__copy">
                    @foreach ($bioParagraphs as $paragraph)
                        @if (trim($paragraph))
                            <p>{{ trim($paragraph) }}</p>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Gallery --}}
        @if ($galleryPhotos->isNotEmpty())
            <div
                class="gallery mt-5"
                tabindex="0"
                role="region"
                aria-label="Photo gallery"
            >
                @foreach ($galleryPhotos as $photo)
                    <figure class="gallery__item">
                        <img
                            class="live-gallery-photo"
                            src="{{ $photo->image_url }}"
                            alt="{{ $photo->alt_text }}"
                            width="{{ $photo->width }}"
                            height="{{ $photo->height }}"
                            loading="lazy"
                            decoding="async"
                        >

                        @if ($photo->credit)
                            <figcaption class="small text-body-secondary mt-2">
                                Photo: {{ $photo->credit }}
                            </figcaption>
                        @endif
                    </figure>
                @endforeach
            </div>
        @endif

    </div>
</section>
@endif


    {{-- Booking --}}

    <section class="section booking-section" id="book">
        <div class="container">
            <div class="booking-shell">
    
                <div class="booking-intro">
                    <p class="eyebrow eyebrow--accent">Bookings & collaborations</p>
    
                    <h2 class="booking-title">
                        Let's make something happen.
                    </h2>
    
                    <p class="booking-copy">
                        For production work, collaborations, DJ enquiries and other
                        music-related opportunities, send through the details below.
                    </p>
    
                    <div class="booking-meta">
                        <div class="booking-meta__item">
                            <span class="booking-meta__label">Based in</span>
                            <span class="booking-meta__value">
                                {{ $profile->location ?? 'Lesotho' }}
                            </span>
                        </div>
    
                        <div class="booking-meta__item">
                            <span class="booking-meta__label">Email</span>
                            <a
                                class="booking-meta__value"
                                href="mailto:{{ $profile->booking_email }}"
                            >
                                {{ $profile->booking_email }}
                            </a>
                        </div>
                    </div>
                </div>
    
                <div class="booking-form-wrap">
                    @if (session('success'))
    <div class="alert alert-success mb-4" role="alert">
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger mb-4" role="alert">
        <strong>Please check the form.</strong>

        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
                    <form
                        class="booking-form"
                        method="POST"
                        action="{{ route('booking.store') }}"
                    >
                        @csrf
                        <div class="booking-honeypot" aria-hidden="true" inert>
                            <label for="company_website">Leave this field empty</label>
                            <input type="text"
                                   id="company_website"
                                   name="company_website"
                                   tabindex="-1"
                                   autocomplete="off">
                        </div>
                        <div class="row g-3">
    
                            <div class="col-md-6">
                                <label class="form-label" for="name">
                                    Your name
                                </label>
    
                                <input
                                    type="text"
                                    class="form-control"
                                    id="name"
                                    name="name"
                                    value="{{ old('name') }}"
                                    placeholder="Your full name"
                                    required
                                >
                            </div>
    
                            <div class="col-md-6">
                                <label class="form-label" for="email">
                                    Email address
                                </label>
    
                                <input
                                    type="email"
                                    class="form-control"
                                    id="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    placeholder="you@example.com"
                                    required
                                >
                            </div>
    
                            <div class="col-md-6">
                                <label class="form-label" for="phone">
                                    Phone number
                                </label>
    
                                <input
                                    type="tel"
                                    class="form-control"
                                    id="phone"
                                    name="phone"
                                    value="{{ old('phone') }}"
                                    placeholder="+266 ..."
                                >
                            </div>
    
                            <div class="col-md-6">
                                <label class="form-label" for="booking_type">
                                    Enquiry type
                                </label>
    
                                <select
                                    class="form-select"
                                    id="booking_type"
                                    name="booking_type"
                                    required
                                >
                                <option value="" disabled @selected(! old('booking_type'))>
                                    Select an option
                                </option>
                                
                                @foreach (\App\Models\BookingEnquiry::TYPES as $value => $label)
                                    <option value="{{ $value }}" @selected(old('booking_type') === $value)>
                                        {{ $label }}
                                    </option>
                                @endforeach
                                </select>
                            </div>
    
                            <div class="col-md-6">
                                <label class="form-label" for="event_date">
                                    Preferred date
                                </label>
    
                                <input
                                    type="date"
                                    class="form-control"
                                    id="event_date"
                                    name="event_date"
                                    value="{{ old('event_date') }}"
                                >
                            </div>
    
                            <div class="col-md-6">
                                <label class="form-label" for="budget">
                                    Budget
                                </label>
    
                                <select
                                    class="form-select"
                                    id="budget"
                                    name="budget"
                                >
                                    <option value="">
                                        Select budget range
                                    </option>
    
                                    <option value="under-1000">
                                        Under M1,000
                                    </option>
    
                                    <option value="1000-3000">
                                        M1,000 – M3,000
                                    </option>
    
                                    <option value="3000-5000">
                                        M3,000 – M5,000
                                    </option>
    
                                    <option value="5000-plus">
                                        M5,000+
                                    </option>
    
                                    <option value="discuss">
                                        Let's discuss
                                    </option>
                                </select>
                            </div>
    
                            <div class="col-12">
                                <label class="form-label" for="subject">
                                    Subject
                                </label>
    
                                <input
                                    type="text"
                                    class="form-control"
                                    id="subject"
                                    name="subject"
                                    value="{{ old('subject') }}"
                                    placeholder="Tell me what this is about"
                                >
                            </div>
    
                            <div class="col-12">
                                <label class="form-label" for="message">
                                    Tell me about the project
                                </label>
    
                                <textarea
                                    class="form-control"
                                    id="message"
                                    name="message"
                                    rows="6"
                                    placeholder="Share as much detail as possible — project type, artist, event, timelines, references, etc."
                                    required
                                >{{ old('message') }}</textarea>
                            </div>
    
                            <div class="col-12">
                                <button
                                    type="submit"
                                    class="booking-submit"
                                >
                                    <span>Send enquiry</span>
    
                                    <span class="booking-submit__icon" aria-hidden="true">
                                        ↗
                                    </span>
                                </button>
                            </div>
    
                        </div>
                    </form>
                </div>
    
            </div>
        </div>
    </section>
@endsection