<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use http\Env\Response;
use Illuminate\Http\Request;

class HotelController extends Controller
{
    /**
     * Display a listing of all hotels.
     */
    public function index()
    {
        return response()->json(Hotel::all());
    }

    /**
     * Search hotels by all fillable attributes.
     */
    public function search(Request $request)
    {
        $filter = $request->input('filter');
        $query = $request->input('q');

        if (!$filter || !$query) {
            return response()->json(['error' => 'Missing filter or query'], 400);
        }

        $allowedFilters = [
            'name',
            'description',
            'address',
            'city',
            'country',
            'postal_code',
            'star_rating',
            'status',
            'amenities',
        ];

        if (!in_array($filter, $allowedFilters)) {
            return response()->json(['error' => 'Invalid filter'], 400);
        }

        $queries = array_map('trim', explode(',', $query));

        $hotels = Hotel::all();

        $filtered = $hotels->filter(function ($hotel) use ($filter, $queries) {
            $value = $hotel->$filter;

            if (is_array($value)) {
                // Exemple: filter = amenities
                return collect($queries)->some(function ($q) use ($value) {
                    return collect($value)->contains(fn($item) => stripos($item, $q) !== false);
                });
            }

            return collect($queries)->contains(function ($q) use ($value) {
                return stripos((string) $value, $q) !== false;
            });
        });

        return response()->json($filtered->values());
    }





    /**
     * Show a specific hotel.
     */
    public function show($id)
    {
        $all = Hotel::all();
        $hotel= $all->find($id);
        if(!$hotel){
            return response()->json(['error'=>'hotel not found'],404);
        }
        return response()->json($hotel);
    }

    /**
     * Store a new hotel.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'hotelier_id' => 'required|integer',
            'name' => 'required|string',
            'description' => 'nullable|string',
            'address' => 'required|string',
            'city' => 'required|string',
            'country' => 'required|string',
            'postal_code' => 'required|string',
            'star_rating' => 'required|integer|min:1|max:5',
            'amenities' => 'nullable|array',
            'images' => 'nullable|array',
            'status' => 'nullable|string',
        ]);
        
        $hotel = Hotel::create($validated);

        return response()->json(['Added successfuly !'=>$hotel], 201);
    }

    /**
     * Update a hotel.
     */
    public function update(Request $request, $id)
    {
        $hotel = Hotel::find($id);

        if (!$hotel) {
            return response()->json(['message' => 'Hotel not found'], 404);
        }

        $hotel->update($request->only($hotel->getFillable()));

        return response()->json($hotel);
    }

    /**
     * Delete a hotel.
     */
    public function destroy($id)
    {
        $hotel = Hotel::findOrFail($id);
        $hotel->delete();

        return response()->json(['message' => 'Hotel deleted']);
    }
}
