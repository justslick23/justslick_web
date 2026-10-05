<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookingEnquiry;
use App\Models\MediaItem;
use App\Models\Photo;
use App\Models\PressAsset;
use App\Models\Release;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'releaseCount' => Release::count(),
            'publishedCount' => Release::published()->count(),
            'mediaCount' => MediaItem::count(),
            'photoCount' => Photo::count(),
            'pressAssetCount' => PressAsset::count(),

            'newEnquiryCount' => BookingEnquiry::where('status', 'new')->count(),

            'failedNotificationCount' => BookingEnquiry::where(
                'notification_status',
                'failed'
            )->count(),

            'latestEnquiries' => BookingEnquiry::latest('id')
                ->limit(5)
                ->get(),

            'enquiryTypes' => BookingEnquiry::TYPES,
            'enquiryStatuses' => BookingEnquiry::STATUSES,
        ]);
    }
}