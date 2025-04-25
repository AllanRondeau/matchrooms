<?php

namespace App\Http\Controllers;

use App\Models\RoomType;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $likedRoomTypeIds = $user
            ? $user->roomTypeLikes()->pluck('room_type_id')->toArray()
            : [];

        $roomTypes = RoomType::with('hotel')
            ->when($likedRoomTypeIds, function ($query) use ($likedRoomTypeIds) {
                $query->whereNotIn('id', $likedRoomTypeIds);
            })
            ->inRandomOrder()
            ->get();

        return inertia('Home', [
            'roomTypes' => $roomTypes,
        ]);
    }

    public function toggleLike(Request $request)
    {
        $roomTypeId = $request->input('room_type_id');
        $user = auth()->user();

        $like = $user->roomTypeLikes()->where('room_type_id', $roomTypeId)->first();

        if ($like) {
            $like->delete();
        } else {
            $user->roomTypeLikes()->create(['room_type_id' => $roomTypeId]);
        }

        return back();
    }
}
