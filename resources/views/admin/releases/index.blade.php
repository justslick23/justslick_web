@extends('layouts.admin')

@section('title', 'Releases')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <h1 class="h3 mb-0">Releases</h1>
        <a class="btn btn-brand" href="{{ route('admin.releases.create') }}">Add release</a>
    </div>

    @if ($releases->isEmpty())
        <div class="admin-card">
            <p class="mb-0">No releases yet. Add your first one to get started.</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th scope="col"><span class="visually-hidden">Cover</span></th>
                        <th scope="col">Title</th>
                        <th scope="col">Type</th>
                        <th scope="col">Status</th>
                        <th scope="col">Date</th>
                        <th scope="col"><span class="visually-hidden">Actions</span></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($releases as $release)
                        <tr>
                            <td>
                                @if ($release->cover_url)
                                    <img class="cover-thumb" src="{{ $release->cover_url }}" alt="" width="48" height="48" loading="lazy">
                                @else
                                    <div class="cover-thumb"></div>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.releases.edit', $release) }}">{{ $release->title }}</a>
                                <div class="small text-body-secondary">{{ $release->links_count }} {{ Str::plural('link', $release->links_count) }}</div>
                            </td>
                            <td>
                                {{ $release->type->label() }}
                                @unless ($release->type->isOfficial())
                                    <span class="badge text-bg-secondary ms-1">Unofficial</span>
                                @endunless
                            </td>
                            <td>
                                @if ($release->is_published)
                                    <span class="badge text-bg-success">Published</span>
                                @else
                                    <span class="badge text-bg-secondary">Draft</span>
                                @endif
                                @if ($release->is_featured)
                                    <span class="badge badge-brand">Featured</span>
                                @endif
                            </td>
                            <td>{{ $release->release_date?->format('j M Y') ?? 'No date' }}</td>
                            <td class="text-end text-nowrap">
                                <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.releases.edit', $release) }}">Edit</a>
                                <form class="d-inline" method="POST" action="{{ route('admin.releases.destroy', $release) }}" onsubmit="return confirm('Delete this release? This cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $releases->links() }}
    @endif
@endsection