@extends('layouts.admin')

@section('title', 'Enquiries')

@section('content')
    <h1 class="h3 mb-4">Enquiries</h1>

    <form method="GET" action="{{ route('admin.enquiries.index') }}"
          class="d-flex flex-wrap align-items-end gap-3 mb-4">
        <div>
            <label for="status" class="form-label">Status</label>
            <select id="status" name="status" class="form-select">
                <option value="">All enquiries</option>
                @foreach ($statuses as $value => $label)
                    <option value="{{ $value }}" @selected($status === $value)>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>
        <button class="btn btn-brand" type="submit">Filter</button>
    </form>

    @if ($enquiries->isEmpty())
        <div class="admin-card">No enquiries match this filter.</div>
    @else
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th scope="col">From</th>
                        <th scope="col">Type</th>
                        <th scope="col">Status</th>
                        <th scope="col">Email notification</th>
                        <th scope="col">Received</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($enquiries as $enquiry)
                        <tr>
                            <td>
                                <a href="{{ route('admin.enquiries.show', $enquiry) }}">
                                    {{ $enquiry->name }}
                                </a>
                                <div class="small text-body-secondary">
                                    {{ $enquiry->subject ?: 'No subject' }}
                                </div>
                            </td>
                            <td>{{ $types[$enquiry->booking_type] }}</td>
                            <td>
                                <span class="badge {{ $enquiry->status === 'new'
                                    ? 'text-bg-primary' : 'text-bg-secondary' }}">
                                    {{ $statuses[$enquiry->status] }}
                                </span>
                            </td>
                            <td>
                                <span class="{{ $enquiry->notification_status === 'failed'
                                    ? 'text-danger' : 'text-body-secondary' }}">
                                    {{ ucfirst($enquiry->notification_status) }}
                                </span>
                            </td>
                            <td>{{ $enquiry->created_at->format('j M Y, H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $enquiries->links('pagination::bootstrap-5') }}
    @endif
@endsection