@php
    $profile = $profile ?? \App\Models\ArtistProfile::current();

    $siteName = $profile->artist_name ?: 'Just Slick';

    $pageTitle = trim(strip_tags($__env->yieldContent('title')));

    $seoTitle = $pageTitle !== '' && $pageTitle !== $siteName
        ? $pageTitle . ' | ' . $siteName
        : $siteName . ' | Music Producer & DJ from Lesotho';

    $description = trim(strip_tags(
        $__env->yieldContent('meta_description')
    ));

    if ($description === '') {
        $description = config('seo.description');
    }

    // Exclude tracking parameters from the canonical URL.
    $canonical = request()->url();

    // Paginated music pages should retain their own page URL.
    if (
        request()->routeIs('music.index')
        && isset($releases)
        && $releases instanceof \Illuminate\Contracts\Pagination\Paginator
        && $releases->currentPage() > 1
    ) {
        $canonical .= '?page=' . $releases->currentPage();
    }

    $canonicalOverride = trim($__env->yieldContent('canonical_url'));

    if ($canonicalOverride !== '') {
        $canonical = $canonicalOverride;
    }

    $socialImage = trim($__env->yieldContent('og_image'));
    $defaultImage = config('seo.image');

    if (
        $socialImage === ''
        && $defaultImage
        && is_file(public_path($defaultImage))
    ) {
        $socialImage = asset($defaultImage);
    }

    $socialImageAlt = trim($__env->yieldContent('og_image_alt'))
        ?: $siteName . ' — music producer and DJ from Lesotho';

    $indexable = app()->environment('production')
        && config('seo.indexable');

    $robots = $indexable
        ? 'index, follow, max-image-preview:large'
        : 'noindex, nofollow';

    $schema = null;

    if (request()->routeIs('home')) {
        $homeUrl = route('home');

        $artistSchema = [
            '@type' => 'Person',
            '@id' => $homeUrl . '#artist',
            'name' => $siteName,
            'url' => $homeUrl,
            'jobTitle' => 'Music Producer and DJ',
        ];

        $socialLinks = collect($profile->social_links ?? [])
            ->filter(fn ($url) => is_string($url)
                && filter_var($url, FILTER_VALIDATE_URL)
                && in_array(
                    strtolower(parse_url($url, PHP_URL_SCHEME) ?? ''),
                    ['http', 'https'],
                    true
                )
            )
            ->values()
            ->all();

        if ($socialLinks !== []) {
            $artistSchema['sameAs'] = $socialLinks;
        }

        if ($socialImage !== '') {
            $artistSchema['image'] = $socialImage;
        }

        $schema = [
            '@context' => 'https://schema.org',
            '@graph' => [
                $artistSchema,
                [
                    '@type' => 'WebSite',
                    '@id' => $homeUrl . '#website',
                    'url' => $homeUrl,
                    'name' => $siteName,
                    'publisher' => [
                        '@id' => $homeUrl . '#artist',
                    ],
                ],
            ],
        ];
    }
@endphp

<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    data-bs-theme="dark"
>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-T4XKDH5T');</script>
    <!-- End Google Tag Manager -->

    <title>{{ $seoTitle }}</title>
    <meta name="description" content="{{ $description }}">
    <meta name="robots" content="{{ $robots }}">
    <meta name="theme-color" content="#141312">

    <link rel="canonical" href="{{ $canonical }}">

    {{-- Social sharing --}}
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:title" content="{{ $seoTitle }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:url" content="{{ $canonical }}">

    <meta
        name="twitter:card"
        content="{{ $socialImage !== '' ? 'summary_large_image' : 'summary' }}"
    >
    <meta name="twitter:title" content="{{ $seoTitle }}">
    <meta name="twitter:description" content="{{ $description }}">

    @if ($socialImage !== '')
        <meta property="og:image" content="{{ $socialImage }}">
        <meta property="og:image:alt" content="{{ $socialImageAlt }}">
        <meta name="twitter:image" content="{{ $socialImage }}">
        <meta name="twitter:image:alt" content="{{ $socialImageAlt }}">
    @endif

    {{-- Artist and website structured data --}}
    @if ($schema !== null)
        <script type="application/ld+json">{!!
            json_encode(
                $schema,
                JSON_UNESCAPED_SLASHES
                | JSON_UNESCAPED_UNICODE
                | JSON_HEX_TAG
                | JSON_HEX_AMP
                | JSON_HEX_APOS
                | JSON_HEX_QUOT
            )
        !!}</script>
    @endif

    {{-- Apply the saved theme before first paint. --}}
    <script>
        (function () {
            var theme = 'dark';

            try {
                theme = localStorage.getItem('js-theme') === 'light'
                    ? 'light'
                    : 'dark';
            } catch (e) {}

            document.documentElement.setAttribute('data-bs-theme', theme);
        })();
    </script>

    <link
        rel="stylesheet"
        href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}"
    >
    <link
        rel="stylesheet"
        href="{{ asset('css/site.css') }}?v={{ @filemtime(public_path('css/site.css')) }}"
    >

    <script
        src="{{ asset('js/site.js') }}?v={{ @filemtime(public_path('js/site.js')) }}"
        defer
    ></script>

    @stack('head')
</head>
<body>
    <!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-T4XKDH5T"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    <a class="skip-link" href="#main">Skip to content</a>

    <x-site-header
        :profile="$profile"
        :show-watch="$showWatch ?? isset($latestVideo)"
        :show-about="$showAbout ?? (
            filled($profile->biography)
            || filled($profile->short_bio)
            || isset($aboutPhoto)
            || (isset($galleryPhotos) && $galleryPhotos->isNotEmpty())
        )"
    />

    <main id="main">
        @yield('content')
    </main>

    <x-site-footer :profile="$profile" />

    @stack('scripts')

    <script
        src="{{ asset('js/site-animations.js') }}"
        defer
    ></script>
</body>
</html>