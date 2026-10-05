@props(['profile'])

@php
    $socialLinks = collect(\App\Models\ArtistProfile::SOCIAL_PLATFORMS)
        ->map(fn ($label, $key) => [
            'label' => $label,
            'url' => $profile->social_links[$key] ?? null,
        ])
        ->filter(fn ($link) => filled($link['url']));
@endphp

<footer class="site-footer">
    <div class="container">
        <div class="d-flex flex-column flex-lg-row
                    justify-content-between align-items-lg-center gap-4">
            <div>
                <p class="mb-1">
                    &copy; {{ date('Y') }} {{ $profile->artist_name }}.
                    All rights reserved.
                </p>

                <a class="small"
                   href="mailto:{{ $profile->booking_email }}">
                    {{ $profile->booking_email }}
                </a>
            </div>

            @if ($socialLinks->isNotEmpty())
                <nav aria-label="Social and streaming profiles">
                    <ul class="list-unstyled d-flex flex-wrap gap-3 mb-0">
                        @foreach ($socialLinks as $link)
                            <li>
                                <a class="small"
                                   href="{{ $link['url'] }}"
                                   target="_blank"
                                   rel="noopener noreferrer">
                                    {{ $link['label'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </nav>
            @endif
        </div>
    </div>
</footer>