@extends('layouts.admin')

@section('title', 'Press kit')

@section('content')
    @php
        $uploadErrors = $errors->getBag('upload');
    @endphp

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
        <h1 class="h3 mb-0">Press kit</h1>
        <a class="btn btn-outline-secondary btn-sm" href="{{ route('press') }}" target="_blank" rel="noopener">View public page</a>
    </div>

    <p class="text-body-secondary mb-4">
        The short bio, full biography, booking email and social links come from
        <a href="{{ route('admin.profile.edit') }}">Profile &amp; Contact</a>.
        Press photos are chosen with the press kit option on each photo in
        <a href="{{ route('admin.photos.index') }}">Photos</a>.
        Everything else for the press page is below.
    </p>

    {{-- Press-only facts --}}
    <form method="POST" action="{{ route('admin.press.update') }}" class="admin-card mb-4" novalidate>
        @csrf
        @method('PUT')

        <h2 class="h5 mb-3">Quick facts and credits</h2>

        @if ($errors->any() && ! $errors->hasBag('upload') && $errors->getBags() === [])
            <div class="alert alert-danger" role="alert">Please fix the highlighted fields.</div>
        @endif

        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <label for="real_name" class="form-label">Real name</label>
                <input id="real_name" name="real_name" type="text" maxlength="120"
                    value="{{ old('real_name', $press->real_name) }}"
                    class="form-control @error('real_name') is-invalid @enderror"
                    @error('real_name') aria-describedby="real_name-error" @enderror>
                @error('real_name') <div id="real_name-error" class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4">
                <label for="nickname" class="form-label">Also known as</label>
                <input id="nickname" name="nickname" type="text" maxlength="120"
                    value="{{ old('nickname', $press->nickname) }}"
                    class="form-control @error('nickname') is-invalid @enderror"
                    @error('nickname') aria-describedby="nickname-error" @enderror>
                @error('nickname') <div id="nickname-error" class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4">
                <label for="active_since" class="form-label">Producing since (year)</label>
                <input id="active_since" name="active_since" type="number" min="1990" max="{{ now()->year }}"
                    value="{{ old('active_since', $press->active_since) }}"
                    class="form-control @error('active_since') is-invalid @enderror"
                    @error('active_since') aria-describedby="active_since-error" @enderror>
                @error('active_since') <div id="active_since-error" class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="mb-3">
            <label for="genres" class="form-label">Genres</label>
            <input id="genres" name="genres" type="text" maxlength="255"
                value="{{ old('genres', $press->genres) }}"
                class="form-control @error('genres') is-invalid @enderror"
                aria-describedby="genres-help @error('genres') genres-error @enderror">
            @error('genres') <div id="genres-error" class="invalid-feedback">{{ $message }}</div> @enderror
            <div id="genres-help" class="form-text">Comma separated, shown as one line.</div>
        </div>

        <div class="row g-3 mb-4">
            @foreach ([
                'awards' => ['Awards', 'One award per line, with the year.'],
                'influences' => ['Producer influences', 'One name per line.'],
                'collaborators' => ['Worked with', 'One name per line.'],
            ] as $field => [$label, $help])
                <div class="col-lg-4">
                    <label for="{{ $field }}" class="form-label">{{ $label }}</label>
                    <textarea id="{{ $field }}" name="{{ $field }}" rows="6" maxlength="2000"
                        class="form-control @error($field) is-invalid @enderror"
                        aria-describedby="{{ $field }}-help @error($field) {{ $field }}-error @enderror">{{ old($field, $press->{$field}) }}</textarea>
                    @error($field) <div id="{{ $field }}-error" class="invalid-feedback">{{ $message }}</div> @enderror
                    <div id="{{ $field }}-help" class="form-text">{{ $help }} Leave empty to hide the section.</div>
                </div>
            @endforeach
        </div>

        <button type="submit" class="btn btn-brand">Save details</button>
    </form>

    {{-- Upload --}}
    <form method="POST" action="{{ route('admin.press.assets.store') }}" enctype="multipart/form-data" class="admin-card mb-4" novalidate>
        @csrf

        <h2 class="h5 mb-3">Add a download</h2>

        <div class="row g-3 align-items-start">
            <div class="col-md-4">
                <label for="upload-title" class="form-label">Title</label>
                <input id="upload-title" name="title" type="text" maxlength="120" required
                    value="{{ $uploadErrors->any() ? old('title') : '' }}"
                    class="form-control {{ $uploadErrors->has('title') ? 'is-invalid' : '' }}"
                    @if ($uploadErrors->has('title')) aria-describedby="upload-title-error" @endif>
                @if ($uploadErrors->has('title'))
                    <div id="upload-title-error" class="invalid-feedback">{{ $uploadErrors->first('title') }}</div>
                @endif
            </div>
            <div class="col-md-3">
                <label for="upload-kind" class="form-label">Type</label>
                <select id="upload-kind" name="kind" required class="form-select {{ $uploadErrors->has('kind') ? 'is-invalid' : '' }}">
                    @foreach ($kinds as $value => $label)
                        <option value="{{ $value }}" @selected(($uploadErrors->any() ? old('kind') : 'one_sheet') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @if ($uploadErrors->has('kind'))
                    <div class="invalid-feedback">{{ $uploadErrors->first('kind') }}</div>
                @endif
            </div>
            <div class="col-md-5">
                <label for="upload-file" class="form-label">File</label>
                <input id="upload-file" name="file" type="file" required
                    accept=".pdf,.zip,.jpg,.jpeg,.png,.webp"
                    class="form-control {{ $uploadErrors->has('file') ? 'is-invalid' : '' }}"
                    aria-describedby="upload-file-help {{ $uploadErrors->has('file') ? 'upload-file-error' : '' }}">
                @if ($uploadErrors->has('file'))
                    <div id="upload-file-error" class="invalid-feedback">{{ $uploadErrors->first('file') }}</div>
                @endif
                <div id="upload-file-help" class="form-text">PDF, ZIP, JPG, PNG or WebP, up to 50 MB.</div>
            </div>
        </div>

        <button type="submit" class="btn btn-brand mt-3">Upload</button>
    </form>

    {{-- Existing downloads --}}
    <h2 class="h5 mb-3">Downloads</h2>

    @forelse ($assets as $asset)
        @php
            $bagName = 'asset-'.$asset->id;
            $bag = $errors->getBag($bagName);
            $failed = $bag->any();
        @endphp

        <div class="admin-card mb-3">
            <form method="POST" action="{{ route('admin.press.assets.update', $asset) }}" novalidate>
                @csrf
                @method('PUT')

                <div class="row g-3 align-items-start">
                    <div class="col-md-4">
                        <label for="asset-title-{{ $asset->id }}" class="form-label">Title</label>
                        <input id="asset-title-{{ $asset->id }}" name="title" type="text" maxlength="120" required
                            value="{{ $failed ? old('title', $asset->title) : $asset->title }}"
                            class="form-control {{ $bag->has('title') ? 'is-invalid' : '' }}"
                            @if ($bag->has('title')) aria-describedby="asset-title-error-{{ $asset->id }}" @endif>
                        @if ($bag->has('title'))
                            <div id="asset-title-error-{{ $asset->id }}" class="invalid-feedback">{{ $bag->first('title') }}</div>
                        @endif
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <label for="asset-kind-{{ $asset->id }}" class="form-label">Type</label>
                        <select id="asset-kind-{{ $asset->id }}" name="kind" class="form-select">
                            @foreach ($kinds as $value => $label)
                                <option value="{{ $value }}" @selected(($failed ? old('kind', $asset->kind) : $asset->kind) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-6 col-md-2">
                        <label for="asset-order-{{ $asset->id }}" class="form-label">Order</label>
                        <input id="asset-order-{{ $asset->id }}" name="sort_order" type="number" min="0" max="9999"
                            value="{{ $failed ? old('sort_order', $asset->sort_order) : $asset->sort_order }}"
                            class="form-control {{ $bag->has('sort_order') ? 'is-invalid' : '' }}">
                        @if ($bag->has('sort_order'))
                            <div class="invalid-feedback">{{ $bag->first('sort_order') }}</div>
                        @endif
                    </div>
                    <div class="col-md-3 pt-md-4">
                        <div class="form-check mt-md-2">
                            <input id="asset-published-{{ $asset->id }}" name="is_published" type="checkbox" value="1" class="form-check-input"
                                @checked($failed ? (bool) old('is_published') : $asset->is_published)>
                            <label for="asset-published-{{ $asset->id }}" class="form-check-label">Published</label>
                        </div>
                    </div>
                </div>

                <p class="small text-body-secondary mt-3 mb-3">
                    <a href="{{ $asset->file_url }}" target="_blank" rel="noopener">{{ $asset->original_name }}</a>
                    @if ($asset->human_size) &middot; {{ $asset->human_size }} @endif
                    &middot; Lower numbers appear first.
                </p>

                <button type="submit" class="btn btn-sm btn-brand">Save</button>
            </form>

            <form method="POST" action="{{ route('admin.press.assets.destroy', $asset) }}" class="mt-2"
                onsubmit="return confirm('Delete this download and its file? This cannot be undone.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
            </form>
        </div>
    @empty
        <div class="admin-card">
            <p class="mb-0">No downloads yet. Upload a one-sheet, photo pack or logo files above.</p>
        </div>
    @endforelse
@endsection