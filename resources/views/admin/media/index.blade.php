@extends('layouts.admin')

@section('title', 'Videos & Mixes')

@section('content')
    <div class="d-flex flex-wrap justify-content-between
                align-items-center gap-3 mb-4">
        <h1 class="h3 mb-0">Videos & Mixes</h1>

        <a class="btn btn-brand" href="{{ route('admin.media.create') }}">
            Add media
        </a>
    </div>

    @if ($items->isEmpty())
        <div class="admin-card">
            <p class="mb-0">No videos or mixes yet. Add your first one.</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th scope="col">Title</th>
                        <th scope="col">Type</th>
                        <th scope="col">Status</th>
                        <th scope="col">Order</th>
                        <th scope="col">Date</th>
                        <th scope="col">
                            <span class="visually-hidden">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $item)
                        <tr>
                            <td>
                                <a href="{{ route('admin.media.edit', $item) }}">
                                    {{ $item->title }}
                                </a>

                                @if ($item->release)
                                    <div class="small text-body-secondary">
                                        Release: {{ $item->release->title }}
                                    </div>
                                @endif
                            </td>

                            <td>{{ $item->type->label() }}</td>

                            <td>
                                <span class="badge {{ $item->is_published
                                    ? 'text-bg-success'
                                    : 'text-bg-secondary' }}">
                                    {{ $item->is_published ? 'Published' : 'Draft' }}
                                </span>
                            </td>

                            <td>{{ $item->sort_order }}</td>

                            <td>
                                {{ $item->published_on?->format('j M Y') ?? 'No date' }}
                            </td>

                            <td class="text-end text-nowrap">
                                <a class="btn btn-sm btn-outline-secondary"
                                   href="{{ route('admin.media.edit', $item) }}">
                                    Edit
                                </a>

                                <form class="d-inline" method="POST"
                                      action="{{ route('admin.media.destroy', $item) }}"
                                      onsubmit="return confirm('Delete this media item? This cannot be undone.');">
                                    @csrf
                                    @method('DELETE')

                                    <button class="btn btn-sm btn-outline-danger"
                                            type="submit"
                                            aria-label="Delete {{ $item->title }}">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $items->links('pagination::bootstrap-5') }}
    @endif
@endsection