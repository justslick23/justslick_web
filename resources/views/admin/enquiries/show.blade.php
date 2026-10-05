@extends('layouts.admin')

@section('title', 'Enquiry #'.$enquiry->id)

@section('content')
    <a href="{{ route('admin.enquiries.index') }}"
       class="d-inline-block mb-4">← All enquiries</a>

    <h1 class="h3 mb-4">
        {{ $enquiry->subject ?: $types[$enquiry->booking_type] }}
    </h1>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="admin-card">
                <h2 class="h5">{{ $enquiry->name }}</h2>
                <p class="text-body-secondary">
                    Received {{ $enquiry->created_at->format('j F Y, H:i') }}
                </p>

                <dl class="row">
                    <dt class="col-sm-4">Email</dt>
                    <dd class="col-sm-8">
                        <a href="mailto:{{ $enquiry->email }}">
                            {{ $enquiry->email }}
                        </a>
                    </dd>

                    <dt class="col-sm-4">Phone</dt>
                    <dd class="col-sm-8">{{ $enquiry->phone ?: 'Not provided' }}</dd>

                    <dt class="col-sm-4">Enquiry type</dt>
                    <dd class="col-sm-8">{{ $types[$enquiry->booking_type] }}</dd>

                    <dt class="col-sm-4">Preferred date</dt>
                    <dd class="col-sm-8">
                        {{ $enquiry->event_date?->format('j F Y') ?? 'Not specified' }}
                    </dd>

                    <dt class="col-sm-4">Budget</dt>
                    <dd class="col-sm-8">
                        {{ $budgets[$enquiry->budget] ?? 'Not specified' }}
                    </dd>
                </dl>

                <h2 class="h5 mt-4">Message</h2>
                <div class="enquiry-message">{{ $enquiry->message }}</div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="admin-card">
                <h2 class="h5 mb-3">Manage enquiry</h2>

                <form method="POST"
                      action="{{ route('admin.enquiries.update', $enquiry) }}">
                    @csrf
                    @method('PATCH')

                    <label for="status" class="form-label">Status</label>
                    <select id="status" name="status"
                            class="form-select @error('status') is-invalid @enderror">
                        @foreach ($statuses as $value => $label)
                            <option value="{{ $value }}"
                                @selected(old('status', $enquiry->status) === $value)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>

                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    <button type="submit" class="btn btn-brand mt-3">
                        Save status
                    </button>
                </form>

                <hr class="my-4">

                <a class="btn btn-outline-secondary"
                   href="mailto:{{ $enquiry->email }}">
                    Reply by email
                </a>

                <p class="small text-body-secondary mt-3">
                    Reply using your email app, then mark this enquiry as Replied.
                </p>

                <p class="small mb-0">
                    Notification: {{ ucfirst($enquiry->notification_status) }}
                    @if ($enquiry->notified_at)
                        <br>{{ $enquiry->notified_at->format('j M Y, H:i') }}
                    @endif
                </p>
            </div>
        </div>
    </div>
@endsection