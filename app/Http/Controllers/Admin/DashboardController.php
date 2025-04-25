<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\RoomType;

class DashboardController extends Controller
{
    // app/Http/Controllers/DashboardController.php
    public function index()
    {
        $hotel = null;
        $roomTypes = null;

        if (auth()->check()) {
            $hotel = Hotel::where('user_id', auth()->id())->first();
            $roomTypes = $hotel ? RoomType::where('hotel_id', $hotel->id)->get() : [];
        }

        return inertia('Pro/Dashboard', [
            'hotel' => $hotel,
            'roomTypes' => $roomTypes,
        ]);
    }

}
