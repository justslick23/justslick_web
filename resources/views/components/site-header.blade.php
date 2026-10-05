@props([
    'profile',
    'showWatch' => false,
    'showAbout' => false,
])

<header class="site-header">
    <nav class="navbar navbar-expand-md" aria-label="Main navigation">
        <div class="container">
            <a class="navbar-brand p-0 me-3"
               href="{{ route('home') }}"
               aria-label="{{ $profile->artist_name }} home">
                <x-logo />
            </a>

            <div class="d-flex align-items-center gap-2 ms-auto ms-md-0 order-md-last">
                <x-theme-toggle />

                <button class="navbar-toggler"
                        type="button"
                        data-nav-toggle
                        aria-controls="primary-nav"
                        aria-expanded="false"
                        aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>

            <div class="collapse navbar-collapse" id="primary-nav">
                <ul class="navbar-nav ms-md-auto me-md-3 gap-md-2">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('music.*') ? 'active' : '' }}"
                            href="{{ route('music.index') }}"
                            @if(request()->routeIs('music.index')) aria-current="page" @endif>
                             Music
                         </a>
                    </li>

                    @if ($showWatch)
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('home') }}#watch">
                                Watch
                            </a>
                        </li>
                    @endif

                    @if ($showAbout)
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('home') }}#about">
                                About
                            </a>
                        </li>
                    @endif

                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('press') }}">
                            Press
                        </a>
                    </li>
                </ul>

                <x-arrow-button
                    :href="route('home') . '#book'"
                    class="btn-arrow--sm mt-3 mt-md-0 mb-3 mb-md-0">
                    Book {{ $profile->artist_name }}
                </x-arrow-button>
            </div>
        </div>
    </nav>
</header>