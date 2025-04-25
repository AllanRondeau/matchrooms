<?php

namespace Database\Seeders;

use App\Models\Hotel;
use App\Models\User;
use Illuminate\Database\Seeder;

class HotelSeeder extends Seeder
{
    public function run()
    {
        $hoteliers = User::whereHas('roles', function($q) {
            $q->where('name', 'hotelier');
        })->get();

        foreach ($hoteliers as $index => $hotelier) {
            Hotel::create([
                'user_id' => $hotelier->id,
                'name'    => "Hôtel d'exemple $index",
                'description' => "Description de l'hôtel $index",
                'address' => "Adresse $index",
                'city' => 'Paris',
                'country' => 'France',
                'postal_code' => '60270',
                'star_rating' => rand(1, 5),
                'images' => json_encode([
                    "hotels/{$hotelier->id}/hotel{$index}_1.jpg",
                    "hotels/{$hotelier->id}/hotel{$index}_2.jpg",
                ]),
                'amenities' => json_encode(['WiFi', 'Piscine', 'Parking']),
            ]);
        }
    }
}
