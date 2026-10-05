@php
    $hasOld = session()->hasOldInput();
    $published = $hasOld
        ? (bool) old('is_published')
        : $release->is_published;

    $featured = $hasOld
        ? (bool) old('is_featured')
        : $release->is_featured;
@endphp

@if ($errors->any())
    <div class="alert alert-danger" role="alert">
        Please fix the highlighted fields and save again.
    </div>
@endif

<div class="row g-4">

    {{-- Main content --}}
    <div class="col-lg-8">
        <div class="admin-card">

            {{-- Title --}}
            <div class="mb-3">
                <label for="title" class="form-label">
                    Title
                </label>

                <input
                    id="title"
                    name="title"
                    type="text"
                    maxlength="150"
                    required
                    value="{{ old('title', $release->title) }}"
                    class="form-control @error('title') is-invalid @enderror"
                    @error('title')
                        aria-describedby="title-error"
                    @enderror
                >

                @error('title')
                    <div
                        id="title-error"
                        class="invalid-feedback"
                    >
                        {{ $message }}
                    </div>
                @enderror
            </div>


            {{-- Type / Release Date --}}
            <div class="row g-3 mb-3">

                <div class="col-sm-6">
                    <label for="type" class="form-label">
                        Type
                    </label>

                    <select
                        id="type"
                        name="type"
                        required
                        class="form-select @error('type') is-invalid @enderror"
                        @error('type')
                            aria-describedby="type-error"
                        @enderror
                    >
                        @foreach ($types as $type)
                            <option
                                value="{{ $type->value }}"
                                @selected(
                                    old(
                                        'type',
                                        $release->type?->value
                                    ) === $type->value
                                )
                            >
                                {{ $type->label() }}
                            </option>
                        @endforeach
                    </select>

                    @error('type')
                        <div
                            id="type-error"
                            class="invalid-feedback"
                        >
                            {{ $message }}
                        </div>
                    @enderror

                    <div class="form-text">
                        Bootlegs are shown publicly as unofficial.
                    </div>
                </div>


                <div class="col-sm-6">
                    <label for="release_date" class="form-label">
                        Release date
                    </label>

                    <input
                        id="release_date"
                        name="release_date"
                        type="date"
                        value="{{ old(
                            'release_date',
                            $release->release_date?->format('Y-m-d')
                        ) }}"
                        class="form-control @error('release_date') is-invalid @enderror"
                        @error('release_date')
                            aria-describedby="release_date-error"
                        @enderror
                    >

                    @error('release_date')
                        <div
                            id="release_date-error"
                            class="invalid-feedback"
                        >
                            {{ $message }}
                        </div>
                    @enderror
                </div>

            </div>


            {{-- Description --}}
            <div class="mb-3">
                <label for="description" class="form-label">
                    Description
                </label>

                <textarea
                    id="description"
                    name="description"
                    rows="4"
                    maxlength="2000"
                    class="form-control @error('description') is-invalid @enderror"
                    @error('description')
                        aria-describedby="description-error"
                    @enderror
                >{{ old('description', $release->description) }}</textarea>

                @error('description')
                    <div
                        id="description-error"
                        class="invalid-feedback"
                    >
                        {{ $message }}
                    </div>
                @enderror
            </div>


            {{-- Credits --}}
            <div class="mb-3">
                <label for="credits" class="form-label">
                    Credits
                </label>

                <textarea
                    id="credits"
                    name="credits"
                    rows="3"
                    maxlength="2000"
                    class="form-control @error('credits') is-invalid @enderror"
                    aria-describedby="credits-help @error('credits') credits-error @enderror"
                >{{ old('credits', $release->credits) }}</textarea>

                @error('credits')
                    <div
                        id="credits-error"
                        class="invalid-feedback"
                    >
                        {{ $message }}
                    </div>
                @enderror

                <div
                    id="credits-help"
                    class="form-text"
                >
                    One credit per line, for example "Produced by …".
                </div>
            </div>


            {{-- Embed --}}
            <div class="mb-4">
                <label for="embed_url" class="form-label">
                    Audio or video embed link (optional)
                </label>

                <input
                    id="embed_url"
                    name="embed_url"
                    type="url"
                    maxlength="500"
                    placeholder="https://"
                    value="{{ old(
                        'embed_url',
                        $release->embed_url
                    ) }}"
                    class="form-control @error('embed_url') is-invalid @enderror"
                    aria-describedby="embed-help @error('embed_url') embed-error @enderror"
                >

                @error('embed_url')
                    <div
                        id="embed-error"
                        class="invalid-feedback"
                    >
                        {{ $message }}
                    </div>
                @enderror

                <div
                    id="embed-help"
                    class="form-text"
                >
                    Optional if you upload audio directly below.
                    Supports YouTube, SoundCloud, Spotify or Mixcloud.
                </div>
            </div>


            {{-- OR Divider --}}
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="flex-grow-1 border-top"></div>

                <span class="small text-body-secondary">
                    OR
                </span>

                <div class="flex-grow-1 border-top"></div>
            </div>


            {{-- Direct Audio Upload --}}
            <div class="mb-4">
                <label for="audio" class="form-label">
                    Upload audio file (optional)
                </label>

                @if ($release->audio_url)
                    <div class="mb-3">

                        <div class="mb-2">
                            <audio
                                controls
                                preload="metadata"
                                class="w-100"
                            >
                                <source src="{{ $release->audio_url }}">

                                Your browser does not support audio playback.
                            </audio>
                        </div>

                        <div class="form-check">
                            <input
                                id="remove_audio"
                                name="remove_audio"
                                type="checkbox"
                                value="1"
                                class="form-check-input"
                                @checked(old('remove_audio'))
                            >

                            <label
                                for="remove_audio"
                                class="form-check-label"
                            >
                                Remove current audio
                            </label>
                        </div>

                    </div>
                @endif

                <input
                    id="audio"
                    name="audio"
                    type="file"
                    accept=".mp3,.wav,.m4a,.aac,.ogg,audio/*"
                    class="form-control @error('audio') is-invalid @enderror"
                    aria-describedby="audio-help @error('audio') audio-error @enderror"
                >

                @error('audio')
                    <div
                        id="audio-error"
                        class="invalid-feedback"
                    >
                        {{ $message }}
                    </div>
                @enderror

                <div
                    id="audio-help"
                    class="form-text"
                >
                    MP3, WAV, M4A, AAC or OGG, up to 50 MB.
                    Uploading a new file will replace the current audio.
                </div>
            </div>


            {{-- Streaming Links --}}
            <fieldset>
                <legend class="form-label">
                    Streaming links
                </legend>

                <p class="form-text mt-0">
                    Platform name and full link.
                    Leave unused rows empty.
                </p>

                <datalist id="platform-options">
                    @foreach ([
                        'Spotify',
                        'Apple Music',
                        'YouTube',
                        'SoundCloud',
                        'Audiomack',
                        'Boomplay',
                        'Deezer',
                        'Tidal',
                        'Bandcamp'
                    ] as $platform)
                        <option value="{{ $platform }}"></option>
                    @endforeach
                </datalist>

                @foreach ($linkRows as $i => $row)

                    <div class="row g-2 mb-2">

                        <div class="col-sm-4">
                            <label
                                for="link-label-{{ $i }}"
                                class="visually-hidden"
                            >
                                Platform name {{ $i + 1 }}
                            </label>

                            <input
                                id="link-label-{{ $i }}"
                                name="links[{{ $i }}][label]"
                                type="text"
                                list="platform-options"
                                maxlength="40"
                                placeholder="Platform"
                                value="{{ $row['label'] ?? '' }}"
                                class="form-control @error("links.$i.label") is-invalid @enderror"
                                @error("links.$i.label")
                                    aria-describedby="link-label-error-{{ $i }}"
                                @enderror
                            >

                            @error("links.$i.label")
                                <div
                                    id="link-label-error-{{ $i }}"
                                    class="invalid-feedback"
                                >
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>


                        <div class="col-sm-8">
                            <label
                                for="link-url-{{ $i }}"
                                class="visually-hidden"
                            >
                                Link {{ $i + 1 }}
                            </label>

                            <input
                                id="link-url-{{ $i }}"
                                name="links[{{ $i }}][url]"
                                type="url"
                                maxlength="500"
                                placeholder="https://"
                                value="{{ $row['url'] ?? '' }}"
                                class="form-control @error("links.$i.url") is-invalid @enderror"
                                @error("links.$i.url")
                                    aria-describedby="link-url-error-{{ $i }}"
                                @enderror
                            >

                            @error("links.$i.url")
                                <div
                                    id="link-url-error-{{ $i }}"
                                    class="invalid-feedback"
                                >
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                    </div>

                @endforeach

            </fieldset>

        </div>
    </div>


    {{-- Sidebar --}}
    <div class="col-lg-4">

        {{-- Visibility --}}
        <div class="admin-card mb-4">
            <p class="form-label">
                Visibility
            </p>

            <div class="form-check mb-2">
                <input
                    id="is_published"
                    name="is_published"
                    type="checkbox"
                    value="1"
                    class="form-check-input"
                    @checked($published)
                >

                <label
                    for="is_published"
                    class="form-check-label"
                >
                    Published
                </label>
            </div>


            <div class="form-check">
                <input
                    id="is_featured"
                    name="is_featured"
                    type="checkbox"
                    value="1"
                    class="form-check-input"
                    aria-describedby="featured-help"
                    @checked($featured)
                >

                <label
                    for="is_featured"
                    class="form-check-label"
                >
                    Featured on the homepage
                </label>

                <div
                    id="featured-help"
                    class="form-text"
                >
                    Only one release is featured at a time,
                    and it must be published.
                </div>
            </div>
        </div>


        {{-- Cover Artwork --}}
        <div class="admin-card">

            <label for="cover" class="form-label">
                Cover artwork
            </label>

            @if ($release->cover_url)
                <img
                    class="cover-preview"
                    src="{{ $release->cover_url }}"
                    alt="Current cover artwork"
                >
            @endif

            <input
                id="cover"
                name="cover"
                type="file"
                accept="image/jpeg,image/png,image/webp"
                class="form-control @error('cover') is-invalid @enderror"
                aria-describedby="cover-help @error('cover') cover-error @enderror"
            >

            @error('cover')
                <div
                    id="cover-error"
                    class="invalid-feedback"
                >
                    {{ $message }}
                </div>
            @enderror

            <div
                id="cover-help"
                class="form-text"
            >
                Square JPG, PNG or WebP, at least 600 px
                and up to 5 MB. It is resized automatically.
            </div>


            @if ($release->cover_url)
                <div class="form-check mt-3">

                    <input
                        id="remove_cover"
                        name="remove_cover"
                        type="checkbox"
                        value="1"
                        class="form-check-input"
                        @checked(old('remove_cover'))
                    >

                    <label
                        for="remove_cover"
                        class="form-check-label"
                    >
                        Remove current cover
                    </label>

                </div>
            @endif

        </div>

    </div>
</div>


{{-- Actions --}}
<div class="d-flex flex-wrap gap-3 mt-4">

    <button
        type="submit"
        class="btn btn-brand"
    >
        Save release
    </button>

    <a
        class="btn btn-outline-secondary"
        href="{{ route('admin.releases.index') }}"
    >
        Cancel
    </a>

</div>