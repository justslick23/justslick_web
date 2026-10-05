<?php

namespace App\Http\Controllers;

use App\Models\ArtistProfile;
use App\Models\BookingEnquiry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use RuntimeException;
use Throwable;

class BookingController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $successMessage = 'Thanks for reaching out. Your enquiry has been received.';

        // Honeypot: real visitors leave this field empty.
        if ($request->filled('company_website')) {
            return redirect()->to(route('home').'#book')
                ->with('success', $successMessage);
        }

        $validated = $request->validate([
            'name' => [
                'required', 'string', 'max:120', 'not_regex:/[\r\n]/',
            ],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:50'],
            'booking_type' => [
                'required',
                Rule::in(array_keys(BookingEnquiry::TYPES)),
            ],
            'event_date' => ['nullable', 'date_format:Y-m-d'],
            'budget' => [
                'nullable',
                Rule::in(array_keys(BookingEnquiry::BUDGETS)),
            ],
            'subject' => [
                'nullable', 'string', 'max:200', 'not_regex:/[\r\n]/',
            ],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        // Save first. A mail failure must not erase the enquiry.
        $enquiry = BookingEnquiry::create($validated);

        $bookingType = BookingEnquiry::TYPES[$enquiry->booking_type];
        $budget = BookingEnquiry::BUDGETS[$enquiry->budget] ?? 'Not specified';

        $body = implode("\n", [
            "New booking enquiry #{$enquiry->id}",
            '',
            "Name: {$enquiry->name}",
            "Email: {$enquiry->email}",
            'Phone: '.($enquiry->phone ?: 'Not provided'),
            "Enquiry type: {$bookingType}",
            'Preferred date: '.($enquiry->event_date?->format('j F Y') ?? 'Not specified'),
            "Budget: {$budget}",
            'Subject: '.($enquiry->subject ?: 'Not provided'),
            '',
            'Message:',
            $enquiry->message,
            '',
            'Admin inbox: '.route('admin.enquiries.show', $enquiry),
        ]);

        $notificationStatus = 'failed';

        try {
            $recipient = config('mail.booking_address')
                ?: ArtistProfile::current()->booking_email;

            $sent = Mail::raw(
                $body,
                function ($mail) use ($recipient, $enquiry, $bookingType) {
                    $mail->to($recipient)
                        ->replyTo($enquiry->email, $enquiry->name)
                        ->subject(
                            $enquiry->subject
                                ?: "New {$bookingType} Enquiry"
                        );
                }
            );

            if ($sent === null) {
                throw new RuntimeException('Booking notification was not sent.');
            }

            $notificationStatus = 'sent';
        } catch (Throwable $exception) {
            report($exception);
        }

        // Notification tracking failure must not encourage a duplicate submission.
        try {
            $enquiry->notification_status = $notificationStatus;
            $enquiry->notified_at = $notificationStatus === 'sent' ? now() : null;
            $enquiry->save();
        } catch (Throwable $exception) {
            report($exception);
        }

        return redirect()->to(route('home').'#book')
            ->with('success', $successMessage);
    }
}