@extends('layouts.admin')

@section('title', 'Photos')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <h1 class="h3 mb-0">Photos</h1>
        <a class="btn btn-brand" href="{{ route('admin.photos.create') }}">
            Add photo
        </a>
    </div>

    <div class="row g-4">
        @forelse ($photos as $photo)
            <div class="col-md-6 col-xl-4">
                <article class="admin-card h-100">
                    <img class="admin-photo-thumb"
                         src="{{ $photo->image_url }}"
                         alt="{{ $photo->alt_text }}"
                         width="{{ $photo->width }}"
                         height="{{ $photo->height }}"
                         loading="lazy">

                    <h2 class="h6 mt-3">{{ $photo->title }}</h2>

                    <p class="small text-body-secondary mb-2">
                        {{ $placements[$photo->placement] }}
                        · Order {{ $photo->sort_order }}
                        · {{ $photo->width }} × {{ $photo->height }}
                    </p>

                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <span class="badge {{ $photo->is_published
                            ? 'text-bg-success' : 'text-bg-secondary' }}">
                            {{ $photo->is_published ? 'Published' : 'Draft' }}
                        </span>

                        @if ($photo->in_press_kit)
                            <span class="badge badge-brand">Press kit</span>
                        @endif
                    </div>

                    <div class="d-flex gap-2">
                        <a class="btn btn-sm btn-outline-secondary"
                           href="{{ route('admin.photos.edit', $photo) }}">
                            Edit
                        </a>

                        <form method="POST"
                              action="{{ route('admin.photos.destroy', $photo) }}"
                              onsubmit="return confirm('Delete this photo? This cannot be undone.');">
                            @csrf
                            @method('DELETE')

                            <button type="submit"
                                    class="btn btn-sm btn-outline-danger"
                                    aria-label="Delete {{ $photo->title }}">
                                Delete
                            </button>
                        </form>
                    </div>
                </article>
            </div>
        @empty
            <div class="col-12">
                <div class="admin-card">No photos yet. Upload your first photo.</div>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $photos->links('pagination::bootstrap-5') }}
    </div>
@endsection