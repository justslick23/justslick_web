@extends('layouts.admin')

@section('title', $photo->exists ? 'Edit photo' : 'Add photo')

@section('content')
    <h1 class="h3 mb-4">{{ $photo->exists ? 'Edit photo' : 'Add photo' }}</h1>

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

    <form method="POST" enctype="multipart/form-data"
          action="{{ $photo->exists
              ? route('admin.photos.update', $photo)
              : route('admin.photos.store') }}">
        @csrf

        @if ($photo->exists)
            @method('PUT')
        @endif

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="admin-card">
                    @php
                        $fields = [
                            'title' => ['Title', 150],
                            'alt_text' => ['Image description', 255],
                            'credit' => ['Photographer credit (optional)', 150],
                        ];
                    @endphp

                    @foreach ($fields as $name => [$label, $limit])
                        <div class="mb-3">
                            <label for="{{ $name }}" class="form-label">
                                {{ $label }}
                            </label>
                            <input id="{{ $name }}" name="{{ $name }}"
                                   type="text" maxlength="{{ $limit }}"
                                   value="{{ old($name, $photo->{$name}) }}"
                                   class="form-control @error($name) is-invalid @enderror"
                                   @required($name !== 'credit')
                                   @error($name)
                                       aria-describedby="{{ $name }}-error"
                                       aria-invalid="true"
                                   @enderror>
                            @error($name)
                                <div id="{{ $name }}-error" class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    @endforeach

                    <p class="small text-body-secondary">
                        Describe what is visible, for example:
                        “Just Slick in a tan shirt against a warm brown background.”
                    </p>

                    <div class="mb-3">
                        <label for="placement" class="form-label">Placement</label>
                        <select id="placement" name="placement" required
                                class="form-select @error('placement') is-invalid @enderror">
                            @foreach ($placements as $value => $label)
                                <option value="{{ $value }}"
                                    @selected(old('placement', $photo->placement) === $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('placement')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="sort_order" class="form-label">Display order</label>
                        <input id="sort_order" name="sort_order" type="number"
                               min="0" max="99999" required
                               value="{{ old('sort_order', $photo->sort_order ?? 0) }}"
                               class="form-control @error('sort_order') is-invalid @enderror">
                        @error('sort_order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            Lower numbers appear first. For Hero and About,
                            the first published photo is used.
                        </div>
                    </div>

                    @foreach ([
                        'is_published' => 'Published',
                        'in_press_kit' => 'Include in press kit',
                    ] as $name => $label)
                        <input type="hidden" name="{{ $name }}" value="0">
                        <div class="form-check mb-2">
                            <input id="{{ $name }}" name="{{ $name }}"
                                   type="checkbox" value="1"
                                   class="form-check-input"
                                   @checked(old($name, $photo->{$name}))>
                            <label for="{{ $name }}" class="form-check-label">
                                {{ $label }}
                            </label>
                        </div>
                    @endforeach

                    <p class="form-text mt-3 mb-0">
                        Published controls whether the photo appears on public pages.
                        Uploaded image URLs are public even for draft photos.
                    </p>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="admin-card">
                    @if ($photo->exists)
                        <img class="admin-photo-preview mb-3"
                             src="{{ $photo->image_url }}"
                             alt="{{ $photo->alt_text }}"
                             width="{{ $photo->width }}"
                             height="{{ $photo->height }}">
                    @endif

                    <label for="image" class="form-label">
                        {{ $photo->exists ? 'Replace photo (optional)' : 'Photo' }}
                    </label>

                    <input id="image" name="image" type="file"
                           accept="image/jpeg,image/png,image/webp"
                           class="form-control @error('image') is-invalid @enderror"
                           aria-describedby="image-help @error('image') image-error @enderror"
                           @required(! $photo->exists)>

                    @error('image')
                        <div id="image-error" class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                    <div id="image-help" class="form-text">
                        JPG, PNG or WebP, up to 8 MB. Both dimensions must be
                        between 600 and 4000 pixels. A JPEG copy is saved at
                        up to 2400 pixels on its longest side.
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex gap-3 mt-4">
            <button type="submit" class="btn btn-brand">Save photo</button>
            <a class="btn btn-outline-secondary"
               href="{{ route('admin.photos.index') }}">Cancel</a>
        </div>
    </form>
@endsection