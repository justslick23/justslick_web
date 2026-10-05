<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingEnquiry extends Model
{
    public const TYPES = [
        'production' => 'Music Production',
        'collaboration' => 'Collaboration',
        'dj' => 'DJ Booking',
        'remix' => 'Remix / Rework',
        'other' => 'Other Enquiry',
    ];

    public const BUDGETS = [
        'under-1000' => 'Under M1,000',
        '1000-3000' => 'M1,000 – M3,000',
        '3000-5000' => 'M3,000 – M5,000',
        '5000-plus' => 'M5,000+',
        'discuss' => "Let's discuss",
    ];

    public const STATUSES = [
        'new' => 'New',
        'read' => 'Read',
        'replied' => 'Replied',
    ];

    protected $fillable = [
        'name',
        'email',
        'phone',
        'booking_type',
        'event_date',
        'budget',
        'subject',
        'message',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'notified_at' => 'datetime',
        ];
    }
}