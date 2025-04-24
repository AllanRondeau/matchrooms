<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class HotelController extends Controller
{
    use AuthorizesRequests;

    public function create()
    {
        $existing = Hotel::where('user_id', auth()->id())->first();
        if ($existing) {
            return redirect()->route('hotel.edit', $existing->id)
                ->with('error', "Vous avez déjà créé un hôtel.");
        }
        return inertia('Hotel/Information');
    }

    public function store(Request $request)
    {
        $existing = Hotel::where('user_id', auth()->id())->first();
        if ($existing) {
            return redirect()->route('hotel.edit', $existing->id)
                ->with('error', "Vous avez déjà créé un hôtel.");
        }

        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'description'  => 'required|string',
            'address'      => 'required|string|max:255',
            'city'         => 'required|string|max:255',
            'country'      => 'required|string|max:255',
            'postal_code'  => 'required|string|max:20',
            'star_rating'  => 'required|integer|min:1|max:5',
            'amenities'    => 'required|array',
            'photo'        => 'nullable|image|max:4096',
        ]);

        $hotel = new Hotel();
        $hotel->user_id = Auth::id();
        $hotel->name = $validated['name'];
        $hotel->description = $validated['description'];
        $hotel->address = $validated['address'];
        $hotel->city = $validated['city'];
        $hotel->country = $validated['country'];
        $hotel->postal_code = $validated['postal_code'];
        $hotel->star_rating = $validated['star_rating'];
        $hotel->amenities = json_encode($validated['amenities']);
        $hotel->images = json_encode([]);
        $hotel->save();

        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $dir = "hotels/{$hotel->id}/";
            $photoPath = $photo->store($dir, 'public');
            $hotel->images = json_encode([$photoPath]);
            $hotel->save();
        }

        return redirect()->route('hotel.edit', $hotel->id)->with('success', 'Hôtel créé !');
    }

    public function edit($id)
    {
        $hotel = Hotel::findOrFail($id);
        $this->authorize('update', $hotel);
        return inertia('Hotel/Information', ['hotel' => $hotel]);
    }

    public function update(Request $request, $id)
    {
        $hotel = Hotel::findOrFail($id);
        $this->authorize('update', $hotel);
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'description'  => 'required|string',
            'address'      => 'required|string|max:255',
            'city'         => 'required|string|max:255',
            'country'      => 'required|string|max:255',
            'postal_code'  => 'required|string|max:20',
            'star_rating'  => 'required|integer|min:1|max:5',
            'amenities'    => 'required|array',
            'photo'        => 'nullable|image|max:4096',
        ]);

        $hotel->update([
            'name'        => $validated['name'],
            'description' => $validated['description'],
            'address'     => $validated['address'],
            'city'        => $validated['city'],
            'country'     => $validated['country'],
            'postal_code' => $validated['postal_code'],
            'star_rating' => $validated['star_rating'],
            'amenities'   => json_encode($validated['amenities']),
        ]);

        if ($request->hasFile('photo')) {
            $oldImages = json_decode($hotel->images, true) ?? [];
            foreach ($oldImages as $oldPath) {
                Storage::disk('public')->delete($oldPath);
            }

            $photo = $request->file('photo');
            $dir = "hotels/{$hotel->id}/";
            $photoPath = $photo->store($dir, 'public');
            $hotel->images = json_encode([$photoPath]);
            $hotel->save();
        }

        return redirect()->back()->with('success', 'Informations modifiées !');
    }
}
