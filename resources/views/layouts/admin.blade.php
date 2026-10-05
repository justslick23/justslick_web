<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">

    <title>@yield('title', 'Admin') | {{ config('app.name') }}</title>

    <script>
        (function () {
            var theme = 'dark';
            try { theme = localStorage.getItem('js-theme') === 'light' ? 'light' : 'dark'; } catch (e) {}
            document.documentElement.setAttribute('data-bs-theme', theme);
        })();
    </script>

    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/site.css') }}?v={{ @filemtime(public_path('css/site.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}?v={{ @filemtime(public_path('css/admin.css')) }}">
    <script src="{{ asset('js/site.js') }}?v={{ @filemtime(public_path('js/site.js')) }}" defer></script>
</head>
<body class="admin">
    <a class="skip-link" href="#main">Skip to content</a>

    <header class="admin-bar">
        <div class="container admin-bar__inner">
            <div class="d-flex align-items-center">
                <a href="{{ auth()->check() ? route('admin.dashboard') : route('home') }}">
                    <x-logo />
                </a>
                <span class="admin-bar__label">Admin</span>
            </div>

            <div class="admin-bar__actions">
                <a class="small" href="{{ route('home') }}" target="_blank" rel="noopener">View site</a>
                <x-theme-toggle />
                @auth
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-secondary">Log out</button>
                    </form>
                @endauth
            </div>
        </div>
    </header>

    @auth
        <nav class="admin-nav" aria-label="Admin sections">
            <div class="container">
                <a href="{{ route('admin.dashboard') }}" @class(['is-active' => request()->routeIs('admin.dashboard')]) @if (request()->routeIs('admin.dashboard')) aria-current="page" @endif>Dashboard</a>
                <a href="{{ route('admin.releases.index') }}" @class(['is-active' => request()->routeIs('admin.releases.*')]) @if (request()->routeIs('admin.releases.*')) aria-current="page" @endif>Releases</a>
                <a href="{{ route('admin.media.index') }}" @class(['is-active' => request()->routeIs('admin.media.*')]) @if (request()->routeIs('admin.media.*')) aria-current="page" @endif>Videos &amp; Mixes</a>
                <a href="{{ route('admin.profile.edit') }}" @class(['is-active' => request()->routeIs('admin.profile.*')]) @if (request()->routeIs('admin.profile.*')) aria-current="page" @endif>Profile &amp; Contact</a>
                <a href="{{ route('admin.photos.index') }}" @class(['is-active' => request()->routeIs('admin.photos.*')]) @if (request()->routeIs('admin.photos.*')) aria-current="page" @endif>Photos</a>
                <a href="{{ route('admin.press.edit') }}" @class(['is-active' => request()->routeIs('admin.press.*')]) @if (request()->routeIs('admin.press.*')) aria-current="page" @endif>Press kit</a>
                <a href="{{ route('admin.enquiries.index') }}"
   @class(['is-active' => request()->routeIs('admin.enquiries.*')])>
    Enquiries
</a>
            </div>
        </nav>
    @endauth

    <main id="main" class="container py-5">
        @if (session('status'))
            <div class="alert alert-success" role="status">{{ session('status') }}</div>
        @endif

        @yield('content')
    </main>
</body>
</html>