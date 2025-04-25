<?php

namespace App\Http\Controllers;

use App\Models\RoomType;
use App\Models\Hotel;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class RoomTypeController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $hotel = Hotel::where('user_id', auth()->id())->first();
        if (!$hotel) {
            return redirect()->route('hotel.create')->with('error', "Vous devez d'abord créer un hôtel.");
        }
        $roomTypes = RoomType::where('hotel_id', $hotel->id)->get();
        return inertia('RoomTypes/Index', [
            'roomTypes' => $roomTypes,
            'hotel' => $hotel
        ]);
    }

    public function create()
    {
        $hotel = Hotel::where('user_id', auth()->id())->first();
        if (!$hotel) {
            return redirect()->route('hotel.create')->with('error', "Vous devez d'abord créer un hôtel.");
        }
        return inertia('RoomTypes/Create', [
            'hotel' => $hotel
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'base_price' => 'required|numeric|min:0',
            'capacity' => 'required|integer|min:1',
            'stock_quantity' => 'required|integer|min:1',
            'amenities' => 'nullable|array',
            'status' => 'required|in:active,inactive',
            'images.*' => 'nullable|image|max:8192',
        ]);
        $hotel = Hotel::where('user_id', auth()->id())->first();
        $pathes = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $pathes[] = $file->store('room_types', 'public');
            }
        }

        $roomType = RoomType::create([
            'hotel_id' => $hotel->id,
            'name' => $validated['name'],
            'description' => $validated['description'],
            'base_price' => $validated['base_price'],
            'capacity' => $validated['capacity'],
            'stock_quantity' => $validated['stock_quantity'],
            'amenities' => $validated['amenities'] ?? [],
            'images' => $pathes,
            'status' => $validated['status']
        ]);

        return Redirect::route('room-types.index')->with('success', 'Type de chambre créé avec succès.');
    }

    public function edit(RoomType $roomType)
    {
        $this->authorize('update', $roomType);

        $hotel = Hotel::where('user_id', auth()->id())->first();

        return inertia('RoomTypes/Create', [
            'roomType' => $roomType,
            'hotel' => $hotel,
            'isEdit' => true
        ]);
    }

    public function update(Request $request, RoomType $roomType)
    {
        $this->authorize('update', $roomType);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'base_price' => 'required|numeric|min:0',
            'capacity' => 'required|integer|min:1',
            'stock_quantity' => 'required|integer|min:1',
            'amenities' => 'nullable|array',
            'status' => 'required|in:active,inactive',
            'images.*' => 'nullable|image|max:8192',
        ]);
        $images = $roomType->images ?? [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $images[] = $file->store('room_types', 'public');
            }
        }

        $roomType->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'base_price' => $validated['base_price'],
            'capacity' => $validated['capacity'],
            'stock_quantity' => $validated['stock_quantity'],
            'amenities' => $validated['amenities'] ?? [],
            'images' => $images,
            'status' => $validated['status'],
        ]);
        return Redirect::route('room-types.index')->with('success', 'Type de chambre mis à jour avec succès.');
    }

    public function destroy(RoomType $roomType)
    {
        $this->authorize('delete', $roomType);
        $roomType->delete();
        return Redirect::route('room-types.index')->with('success', 'Type de chambre supprimé avec succès.');
    }
}
