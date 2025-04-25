<?php

namespace Database\Seeders;

use App\Models\Hotel;
use App\Models\RoomType;
use Illuminate\Database\Seeder;

class RoomTypeSeeder extends Seeder
{
    public function run()
    {
        $hotels = Hotel::all();

        foreach ($hotels as $hotel) {
            for ($i = 1; $i <= 3; $i++) {
                RoomType::create([
                    'hotel_id' => $hotel->id,
                    'name' => "Type de chambre $i",
                    'description' => "Description de la chambre $i",
                    'base_price' => rand(80, 300),
                    'capacity' => rand(2, 4),
                    'amenities' => json_encode(['WiFi', 'TV', 'Climatisation']),
                    'images' => json_encode([
                        "https://placehold.co/600x400/e2e8f0/1e293b?text=Room+Type+{$i}+A",
                        "https://placehold.co/600x400/e2e8f0/1e293b?text=Room+Type+{$i}+B",
                    ]),
                    'stock_quantity' => rand(2, 10),
                    'status' => 'available',
                ]);
            }
        }
    }
}
