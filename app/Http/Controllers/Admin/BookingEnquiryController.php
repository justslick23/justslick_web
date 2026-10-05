<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookingEnquiry;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BookingEnquiryController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'status' => [
                'nullable',
                Rule::in(array_keys(BookingEnquiry::STATUSES)),
            ],
        ]);

        $status = $validated['status'] ?? null;

        $enquiries = BookingEnquiry::query()
            ->when($status, fn ($query) => $query->where('status', $status))
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.enquiries.index', [
            'enquiries' => $enquiries,
            'status' => $status,
            'statuses' => BookingEnquiry::STATUSES,
            'types' => BookingEnquiry::TYPES,
        ]);
    }

    public function show(BookingEnquiry $enquiry): View
    {
        return view('admin.enquiries.show', [
            'enquiry' => $enquiry,
            'statuses' => BookingEnquiry::STATUSES,
            'types' => BookingEnquiry::TYPES,
            'budgets' => BookingEnquiry::BUDGETS,
        ]);
    }

    public function update(
        Request $request,
        BookingEnquiry $enquiry
    ): RedirectResponse {
        $validated = $request->validate([
            'status' => [
                'required',
                Rule::in(array_keys(BookingEnquiry::STATUSES)),
            ],
        ]);

        $enquiry->status = $validated['status'];
        $enquiry->save();

        return redirect()
            ->route('admin.enquiries.show', $enquiry)
            ->with('status', 'Enquiry status updated.');
    }
}