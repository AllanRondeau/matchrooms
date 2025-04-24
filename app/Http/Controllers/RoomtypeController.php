<?php

namespace App\Http\Controllers;

use App\Models\RoomType;
use Illuminate\Http\Request;

class RoomtypeController extends Controller
{

    // Liste tous les RoomTypes
    public function index()
    {
        $rooms = RoomType::all();
        if(!$rooms){
            return response()->json(['error'=> 'No room found'], 404);
        }
        return response()->json(RoomType::all());
    }

    // Affiche un RoomType spécifique
    public function getByid($id): \Illuminate\Http\JsonResponse
    {

        $roomType = RoomType::find($id);
        if (!$roomType) {
            return response()->json(['error' => 'RoomType not found'], 404);
        }
        return response()->json($roomType);
    }

    // Crée un nouveau RoomType
    public function store(Request $request)
    {
        $data = $request->validate([
            'hotel_id' => 'required|exists:hotels,id',
            'name' => 'required|string',
            'description' => 'nullable|string',
            'base_price' => 'required|numeric',
            'capacity' => 'required|integer',
            'amenities' => 'nullable|array',
            'images' => 'nullable|array',
            'stock_quantity' => 'required|integer',
            'status' => 'required|string',
        ]);

        $roomType = RoomType::create($data);
        return response()->json($roomType, 201);
    }

    // Met à jour un RoomType
    public function update(Request $request, $id)
    {
        // Cherche le RoomType par ID
        $roomType = RoomType::find($id);

        // Si le RoomType n'existe pas, retourne une erreur 404
        if (!$roomType) {
            return response()->json(['error' => 'RoomType not found'], 404);
        }

        // Validation des données d'entrée, certains champs sont optionnels
        $data = $request->validate([
            'hotel_id' => 'sometimes|exists:hotels,id',
            'name' => 'sometimes|string',
            'description' => 'nullable|string',
            'base_price' => 'sometimes|numeric',
            'capacity' => 'sometimes|integer',
            'amenities' => 'nullable|array',
            'images' => 'nullable|array',
            'stock_quantity' => 'sometimes|integer',
            'status' => 'sometimes|string',
        ]);

        // Mise à jour du RoomType avec les données validées
        $roomType->update($data);

        // Retourne le RoomType mis à jour
        return response()->json($roomType);
    }


    // Supprime un RoomType
    public function destroy($id)
    {
        $rooms = RoomType::all();
        $roomType = $rooms->find($id);
        if (!$roomType) {
            return response()->json(['error' => 'RoomType not found'], 404);
        }

        $roomType->delete();
        return response()->json(['message' => 'RoomType deleted']);
    }


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
            'base_price',
            'capacity',
            'amenities',
            'status',
            'stock_quantity',
        ];

        if (!in_array($filter, $allowedFilters)) {
            return response()->json(['error' => 'Invalid filter'], 400);
        }

        $queries = array_map('trim', explode(',', $query));

        $roomTypes = RoomType::all();

        $filtered = $roomTypes->filter(function ($roomType) use ($filter, $queries) {
            $value = $roomType->$filter;

            if (is_array($value)) {
                return collect($queries)->some(function ($q) use ($value) {
                    return collect($value)->contains(fn($item) => stripos($item, $q) !== false);
                });
            }

            return collect($queries)->contains(function ($q) use ($value) {
                return stripos((string) $value, $q) !== false;
            });
        });

        if(!$filtered->values()){
            return response()->json(['error'=>'No room found'], 404);
        }

        return response()->json($filtered->values());
    }
}
