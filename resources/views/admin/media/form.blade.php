@extends('layouts.admin')

@section('title', $mediaItem->exists ? 'Edit media' : 'Add media')

@section('content')
    <h1 class="h3 mb-4">
        {{ $mediaItem->exists ? 'Edit media' : 'Add media' }}
    </h1>

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <p class="mb-2">Please correct the following:</p>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST"
          action="{{ $mediaItem->exists
              ? route('admin.media.update', $mediaItem)
              : route('admin.media.store') }}">
        @csrf

        @if ($mediaItem->exists)
            @method('PUT')
        @endif

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="admin-card">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input id="title" name="title" type="text"
                               maxlength="150" required
                               value="{{ old('title', $mediaItem->title) }}"
                               class="form-control @error('title') is-invalid @enderror"
                               aria-describedby="title-error">
                        @error('title')
                            <div id="title-error" class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="type" class="form-label">Type</label>
                        <select id="type" name="type" required
                                class="form-select @error('type') is-invalid @enderror"
                                aria-describedby="type-error">
                            @foreach ($types as $type)
                                <option value="{{ $type->value }}"
                                    @selected(old('type', $mediaItem->type?->value) === $type->value)>
                                    {{ $type->label() }}
                                </option>
                            @endforeach
                        </select>
                        @error('type')
                            <div id="type-error" class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="source_url" class="form-label">Media link</label>
                        <input id="source_url" name="source_url" type="url"
                               maxlength="500" required placeholder="https://"
                               value="{{ old('source_url', $mediaItem->source_url) }}"
                               class="form-control @error('source_url') is-invalid @enderror"
                               aria-describedby="source-help source-error">
                        @error('source_url')
                            <div id="source-error" class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                        <div id="source-help" class="form-text">
                            Paste a full YouTube, SoundCloud, Spotify or Mixcloud
                            track/video link, not iframe HTML. Expand shortened
                            sharing links to the full provider URL first.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="release_id" class="form-label">
                            Linked release (optional)
                        </label>
                        <select id="release_id" name="release_id"
                                class="form-select @error('release_id') is-invalid @enderror"
                                aria-describedby="release-error">
                            <option value="">No linked release</option>
                            @foreach ($releases as $release)
                                <option value="{{ $release->id }}"
                                    @selected((string) old('release_id', $mediaItem->release_id) === (string) $release->id)>
                                    {{ $release->title }}
                                </option>
                            @endforeach
                        </select>
                        @error('release_id')
                            <div id="release-error" class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="form-label">
                            Description (optional)
                        </label>
                        <textarea id="description" name="description"
                                  rows="5" maxlength="2000"
                                  class="form-control @error('description') is-invalid @enderror"
                                  aria-describedby="description-error">{{ old('description', $mediaItem->description) }}</textarea>
                        @error('description')
                            <div id="description-error" class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="admin-card">
                    <div class="mb-3">
                        <label for="published_on" class="form-label">
                            Media date (optional)
                        </label>
                        <input id="published_on" name="published_on" type="date"
                               value="{{ old('published_on', $mediaItem->published_on?->format('Y-m-d')) }}"
                               class="form-control @error('published_on') is-invalid @enderror"
                               aria-describedby="date-help date-error">
                        @error('published_on')
                            <div id="date-error" class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                        <div id="date-help" class="form-text">
                            The original release or performance date.
                            This does not schedule publication.
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="sort_order" class="form-label">
                            Display order
                        </label>
                        <input id="sort_order" name="sort_order" type="number"
                               min="0" max="99999" step="1" required
                               value="{{ old('sort_order', $mediaItem->sort_order ?? 0) }}"
                               class="form-control @error('sort_order') is-invalid @enderror"
                               aria-describedby="order-help order-error">
                        @error('sort_order')
                            <div id="order-error" class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                        <div id="order-help" class="form-text">
                            Lower numbers appear first. Items with the same
                            number are sorted by newest media date.
                        </div>
                    </div>

                    <input type="hidden" name="is_published" value="0">

                    <div class="form-check">
                        <input id="is_published" name="is_published"
                               type="checkbox" value="1"
                               class="form-check-input @error('is_published') is-invalid @enderror"
                               aria-describedby="published-error"
                               @checked(old('is_published', $mediaItem->is_published))>
                        <label for="is_published" class="form-check-label">
                            Published
                        </label>
                        @error('is_published')
                            <div id="published-error" class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex flex-wrap gap-3 mt-4">
            <button type="submit" class="btn btn-brand">Save media</button>
            <a class="btn btn-outline-secondary"
               href="{{ route('admin.media.index') }}">
                Cancel
            </a>
        </div>
    </form>
@endsection