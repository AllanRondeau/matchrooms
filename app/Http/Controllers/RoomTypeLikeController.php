<?php

namespace App\Http\Controllers;

use App\Models\RoomTypeUserLike;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class RoomTypeLikeController extends Controller
{
    public function index()
    {
        $likes = RoomTypeUserLike::with('roomType.hotel')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();
        return inertia('Likes/Index', [
            'likes' => $likes,
        ]);
    }

    public function destroy(RoomTypeUserLike $like)
    {
        $like->delete();
        return back()->with('success', 'Annonce retirée des likes !');
    }
}
