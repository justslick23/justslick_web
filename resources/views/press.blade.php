@extends('layouts.app')

@section('title', 'Press kit')
@section('meta_description', 'Press kit for '.$profile->artist_name.': biography, awards, press photos and booking contact.')

@section('content')
    @php
        $facts = array_filter([
            'Artist name' => $profile->artist_name,
            'Real name' => $press->real_name,
            'Also known as' => $press->nickname,
            'Based in' => $profile->location,
            'Producing since' => $press->active_since,
            'Genres' => $press->genres,
        ]);

        $awards = $press->lines('awards');
        $influences = $press->lines('influences');
        $collaborators = $press->lines('collaborators');
    @endphp

    {{-- Header --}}
    <section class="press-hero">
        <div class="container">
            <p class="eyebrow eyebrow--accent">Press kit</p>
            <h1 class="press-hero__title">{{ $profile->artist_name }}</h1>

            @if ($profile->tagline)
                <p class="lead-text">{{ $profile->tagline }}</p>
            @endif

            <div class="d-flex flex-wrap gap-3">
                @if ($profile->booking_email)
                    <a class="press-btn press-btn--primary" href="mailto:{{ $profile->booking_email }}">Booking contact</a>
                @endif
                @if ($photos->isNotEmpty())
                    <a class="press-btn press-btn--ghost" href="#press-photos">Press photos</a>
                @endif
                @if ($assets->isNotEmpty())
                    <a class="press-btn press-btn--ghost" href="#press-downloads">Downloads</a>
                @endif
            </div>
        </div>
    </section>

    {{-- Quick facts --}}
    @if (count($facts))
        <section class="press-section" aria-labelledby="press-facts-title">
            <div class="container">
                <h2 id="press-facts-title" class="press-section__title">Quick facts</h2>
                <dl class="press-facts">
                    @foreach ($facts as $label => $value)
                        <div class="press-facts__item">
                            <dt>{{ $label }}</dt>
                            <dd>{{ $value }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>
        </section>
    @endif

    {{-- Biography --}}
    @if ($profile->short_bio || count($biography))
        <section class="press-section" id="press-bio" aria-labelledby="press-bio-title">
            <div class="container">
                <h2 id="press-bio-title" class="press-section__title">Biography</h2>

                @if ($profile->short_bio)
                    <div class="press-block">
                        <div class="press-block__head">
                            <h3 class="press-block__label">Short bio</h3>
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" data-copy-target="#bio-short" aria-label="Copy short bio">Copy</button>
                        </div>
                        <div id="bio-short" class="press-prose">
                            <p>{{ $profile->short_bio }}</p>
                        </div>
                    </div>
                @endif

                @if (count($biography))
                    <div class="press-block">
                        <div class="press-block__head">
                            <h3 class="press-block__label">Full biography</h3>
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" data-copy-target="#bio-long" aria-label="Copy full biography">Copy</button>
                        </div>
                        <div id="bio-long" class="press-prose press-prose--columns">
                            @foreach ($biography as $paragraph)
                                <p>{{ $paragraph }}</p>
                            @endforeach
                        </div>
                    </div>
                @endif

                <span class="visually-hidden" role="status" data-copy-status></span>
            </div>
        </section>
    @endif

    {{-- Awards --}}
    @if (count($awards))
        <section class="press-section" aria-labelledby="press-awards-title">
            <div class="container">
                <h2 id="press-awards-title" class="press-section__title">Awards</h2>
                <ul class="press-list">
                    @foreach ($awards as $award)
                        <li>{{ $award }}</li>
                    @endforeach
                </ul>
            </div>
        </section>
    @endif

    {{-- Influences and collaborators --}}
    @if (count($influences) || count($collaborators))
        <section class="press-section" aria-labelledby="press-credits-title">
            <div class="container">
                <h2 id="press-credits-title" class="press-section__title">Influences and collaborators</h2>
                <div class="row g-4">
                    @if (count($influences))
                        <div class="col-lg-6">
                            <h3 class="press-block__label">Producer influences</h3>
                            <ul class="press-pills">
                                @foreach ($influences as $name)
                                    <li>{{ $name }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @if (count($collaborators))
                        <div class="col-lg-6">
                            <h3 class="press-block__label">Worked with</h3>
                            <ul class="press-pills">
                                @foreach ($collaborators as $name)
                                    <li>{{ $name }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    @endif

    {{-- Selected music --}}
    @if ($releases->isNotEmpty())
        <section class="press-section" aria-labelledby="press-music-title">
            <div class="container">
                <h2 id="press-music-title" class="press-section__title">Selected music</h2>
                <ul class="press-music">
                    @foreach ($releases as $release)
                        @php
                            $releaseUrl = Route::has('music.show') ? route('music.show', $release) : null;
                        @endphp
                        <li class="press-music__item">
                            @if ($release->cover_url)
                                <img class="press-music__cover" src="{{ $release->cover_url }}" alt="" width="56" height="56" loading="lazy">
                            @else
                                <span class="press-music__cover" aria-hidden="true"></span>
                            @endif
                            <div>
                                @if ($releaseUrl)
                                    <a class="press-music__title" href="{{ $releaseUrl }}">{{ $release->title }}</a>
                                @else
                                    <span class="press-music__title">{{ $release->title }}</span>
                                @endif
                                <div class="press-music__meta">
                                    {{ $release->type->label() }}@if ($release->release_date), {{ $release->release_date->format('Y') }}@endif
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
    @endif

    {{-- Press photos --}}
    @if ($photos->isNotEmpty())
        <section class="press-section" id="press-photos" aria-labelledby="press-photos-title">
            <div class="container">
                <h2 id="press-photos-title" class="press-section__title">Press photos</h2>
                <div class="press-photos">
                    @foreach ($photos as $photo)
                        @php
                            $extension = pathinfo($photo->image_path, PATHINFO_EXTENSION) ?: 'jpg';
                            $downloadName = Str::slug($profile->artist_name).'-press-photo-'.$loop->iteration.'.'.$extension;
                        @endphp
                        <figure class="press-photo">
                            <img
                                src="{{ $photo->image_url }}"
                                alt="{{ $photo->alt_text ?: ($photo->title ?: $profile->artist_name) }}"
                                @if ($photo->width && $photo->height) width="{{ $photo->width }}" height="{{ $photo->height }}" @endif
                                loading="lazy"
                                decoding="async"
                            >
                            <figcaption>
                                <span class="press-photo__credit">
                                    @if ($photo->credit)Photo: {{ $photo->credit }}@else{{ $photo->title }}@endif
                                </span>
                                <a href="{{ $photo->image_url }}" download="{{ $downloadName }}" aria-label="Download press photo {{ $loop->iteration }}">Download</a>
                            </figcaption>
                        </figure>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Downloads --}}
    @if ($assets->isNotEmpty())
        <section class="press-section" id="press-downloads" aria-labelledby="press-downloads-title">
            <div class="container">
                <h2 id="press-downloads-title" class="press-section__title">Downloads</h2>
                <ul class="press-downloads">
                    @foreach ($assets as $asset)
                        <li>
                            <a class="press-download" href="{{ $asset->file_url }}" download="{{ $asset->original_name }}">
                                <span class="press-download__title">{{ $asset->title }}</span>
                                <span class="press-download__meta">
                                    {{ $asset->kind_label }}@if ($asset->human_size) &middot; {{ $asset->human_size }}@endif
                                </span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
    @endif

    {{-- Contact and online --}}
    @if ($profile->booking_email || count($socials))
        <section class="press-section" id="press-contact" aria-labelledby="press-contact-title">
            <div class="container">
                <h2 id="press-contact-title" class="press-section__title">Contact and online</h2>
                <div class="row g-4">
                    @if ($profile->booking_email)
                        <div class="col-md-6">
                            <h3 class="press-block__label">Bookings and press</h3>
                            <p class="mb-1"><a href="mailto:{{ $profile->booking_email }}">{{ $profile->booking_email }}</a></p>
                            @if ($profile->location)
                                <p class="text-body-secondary mb-0">Based in {{ $profile->location }}</p>
                            @endif
                        </div>
                    @endif
                    @if (count($socials))
                        <div class="col-md-6">
                            <h3 class="press-block__label">Find {{ $profile->artist_name }} online</h3>
                            <ul class="press-pills">
                                @foreach ($socials as $key => $url)
                                    <li>
                                        <a href="{{ $url }}" target="_blank" rel="noopener">{{ \App\Models\ArtistProfile::SOCIAL_PLATFORMS[$key] ?? ucfirst($key) }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    @endif

    <script>
        document.querySelectorAll('[data-copy-target]').forEach(function (button) {
            button.addEventListener('click', function () {
                var target = document.querySelector(button.getAttribute('data-copy-target'));
                var status = document.querySelector('[data-copy-status]');

                if (!target) {
                    return;
                }

                var text = Array.prototype.map.call(target.querySelectorAll('p'), function (p) {
                    return p.textContent.trim();
                }).join('\n\n');

                function done(message) {
                    var original = button.textContent;
                    button.textContent = 'Copied';
                    if (status) { status.textContent = message; }
                    setTimeout(function () {
                        button.textContent = original;
                        if (status) { status.textContent = ''; }
                    }, 2000);
                }

                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(text).then(function () { done('Copied to clipboard'); });
                    return;
                }

                // Fallback for non-secure contexts.
                var area = document.createElement('textarea');
                area.value = text;
                area.setAttribute('readonly', '');
                area.style.position = 'fixed';
                area.style.opacity = '0';
                document.body.appendChild(area);
                area.select();
                try {
                    document.execCommand('copy');
                    done('Copied to clipboard');
                } catch (e) {}
                document.body.removeChild(area);
            });
        });
    </script>
@endsection