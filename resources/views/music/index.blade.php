@extends('layouts.app')

@section('title', 'Music')
@section('meta_description', 'Explore releases, remixes and mixes by '.$profile->artist_name.'.')

@section('content')
    <section class="section">
        <div class="container">
            <div class="section-head">
                <div>
                    <p class="eyebrow eyebrow--accent">
                        {{ $profile->artist_name }}
                    </p>
                    <h1 class="statement">Music</h1>
                </div>
            </div>

            @if ($releases->isEmpty())
                <p class="text-body-secondary">Music will appear here soon.</p>
            @else
                <div class="row row-cols-2 row-cols-lg-4 g-3 g-lg-4">
                    @foreach ($releases as $release)
                        <div class="col">
                            <article class="live-release h-100">
                                <a class="d-block text-decoration-none"
                                   href="{{ route('music.show', $release->slug) }}">
                                    @if ($release->cover_url)
                                        <img class="live-cover"
                                             src="{{ $release->cover_url }}"
                                             alt=""
                                             loading="lazy"
                                             decoding="async">
                                    @else
                                        <div class="live-cover live-cover--empty"
                                             aria-hidden="true">
                                            {{ $release->title }}
                                        </div>
                                    @endif

                                    <h2 class="h5 mt-3 mb-2">
                                        {{ $release->title }}
                                    </h2>
                                </a>

                                <p class="small text-body-secondary mb-2">
                                    {{ $release->type->label() }}

                                    @unless ($release->type->isOfficial())
                                        · Unofficial
                                    @endunless
                                </p>

                                @if ($release->release_date)
                                    <time class="small text-body-secondary"
                                          datetime="{{ $release->release_date->format('Y-m-d') }}">
                                        {{ $release->release_date->format('j M Y') }}
                                    </time>
                                @endif
                            </article>
                        </div>
                    @endforeach
                </div>

                <div class="mt-5">
                    {{ $releases->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </section>
@endsection