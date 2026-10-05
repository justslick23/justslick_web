@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <div class="d-flex flex-wrap justify-content-between
                align-items-center gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1">Dashboard</h1>
            <p class="text-body-secondary mb-0">
                Manage your music, website content and enquiries.
            </p>
        </div>

        <a class="btn btn-outline-secondary"
           href="{{ route('home') }}"
           target="_blank"
           rel="noopener">
            View website ↗
        </a>
    </div>

    @if ($failedNotificationCount)
        <div class="alert alert-warning" role="alert">
            {{ $failedNotificationCount }}
            {{ $failedNotificationCount === 1
                ? 'enquiry has a failed email notification.'
                : 'enquiries have failed email notifications.' }}

            The enquiries are saved.
            <a href="{{ route('admin.enquiries.index') }}">
                Check the inbox
            </a>
            and review your mail settings.
        </div>
    @endif

    @php
        $cards = [
            [
                'label' => 'New enquiries',
                'count' => $newEnquiryCount,
                'detail' => 'Awaiting review',
                'url' => route('admin.enquiries.index', ['status' => 'new']),
            ],
            [
                'label' => 'Releases',
                'count' => $releaseCount,
                'detail' => $publishedCount.' published',
                'url' => route('admin.releases.index'),
            ],
            [
                'label' => 'Videos & Mixes',
                'count' => $mediaCount,
                'detail' => 'Manage media',
                'url' => route('admin.media.index'),
            ],
            [
                'label' => 'Photos',
                'count' => $photoCount,
                'detail' => 'Website and press photos',
                'url' => route('admin.photos.index'),
            ],
            [
                'label' => 'Press downloads',
                'count' => $pressAssetCount,
                'detail' => 'Manage your press kit',
                'url' => route('admin.press.edit'),
            ],
        ];
    @endphp

    <div class="row g-3 mb-5">
        @foreach ($cards as $card)
            <div class="col-sm-6 col-xl-4">
                <a class="admin-card d-block h-100 text-decoration-none"
                   href="{{ $card['url'] }}">
                    <h2 class="h6 text-body-secondary mb-3">
                        {{ $card['label'] }}
                    </h2>
                    <p class="display-6 fw-semibold mb-2">
                        {{ $card['count'] }}
                    </p>
                    <p class="small text-body-secondary mb-0">
                        {{ $card['detail'] }}
                    </p>
                </a>
            </div>
        @endforeach
    </div>

    <div class="d-flex flex-wrap justify-content-between
                align-items-center gap-3 mb-3">
        <h2 class="h5 mb-0">Latest enquiries</h2>
        <a href="{{ route('admin.enquiries.index') }}">View all enquiries →</a>
    </div>

    @if ($latestEnquiries->isEmpty())
        <div class="admin-card">
            <p class="mb-0">
                No enquiries yet. Messages submitted through your booking
                form will appear here.
            </p>
        </div>
    @else
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th scope="col">From</th>
                        <th scope="col">Type</th>
                        <th scope="col">Status</th>
                        <th scope="col">Notification</th>
                        <th scope="col">Received</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($latestEnquiries as $enquiry)
                        <tr>
                            <td>
                                <a href="{{ route('admin.enquiries.show', $enquiry) }}">
                                    {{ $enquiry->name }}
                                </a>
                                <div class="small text-body-secondary">
                                    {{ $enquiry->subject ?: 'No subject' }}
                                </div>
                            </td>

                            <td>{{ $enquiryTypes[$enquiry->booking_type] }}</td>

                            <td>
                                <span class="badge {{ $enquiry->status === 'new'
                                    ? 'text-bg-primary' : 'text-bg-secondary' }}">
                                    {{ $enquiryStatuses[$enquiry->status] }}
                                </span>
                            </td>

                            <td class="{{ $enquiry->notification_status === 'failed'
                                ? 'text-danger' : 'text-body-secondary' }}">
                                {{ ucfirst($enquiry->notification_status) }}
                            </td>

                            <td>
                                {{ $enquiry->created_at->format('j M Y, H:i') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <div class="mt-5">
        <h2 class="h5 mb-3">Quick actions</h2>

        <div class="d-flex flex-wrap gap-3">
            <a class="btn btn-brand"
               href="{{ route('admin.releases.create') }}">
                Add release
            </a>

            <a class="btn btn-outline-secondary"
               href="{{ route('admin.media.create') }}">
                Add video or mix
            </a>

            <a class="btn btn-outline-secondary"
               href="{{ route('admin.photos.create') }}">
                Upload photo
            </a>

            <a class="btn btn-outline-secondary"
               href="{{ route('admin.profile.edit') }}">
                Edit profile
            </a>
        </div>
    </div>
@endsection