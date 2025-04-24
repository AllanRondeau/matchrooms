<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Hotel;
use App\Models\Hotelier;

class HotelSeeder extends Seeder
{
    public function run(): void
    {
        // On récupère un hôtelier existant (ou crée un par défaut si besoin)
        $hotelier = Hotelier::first() ?? Hotelier::factory()->create();

        Hotel::create([
            'hotelier_id' => $hotelier->id,
            'name' => 'Hotel Paradisio',
            'description' => 'Un hôtel de rêve avec toutes les commodités modernes.',
            'address' => '123 Rue du Paradis',
            'city' => 'Paris',
            'country' => 'France',
            'postal_code' => '75001',
            'star_rating' => 5,
            'amenities' => ['wifi', 'spa', 'parking'],
            'images' => ['paradisio1.jpg', 'paradisio2.jpg'],
            'status' => 'active',
        ]);

        Hotel::create([
            'hotelier_id' => $hotelier->id,
            'name' => 'Hotel e',
            'description' => 'Un hôtel de rêve avec toutes les commodités modernes.',
            'address' => '123 Rue du Paradis',
            'city' => 'nice',
            'country' => 'France',
            'postal_code' => '75001',
            'star_rating' => 5,
            'amenities' => ['wifi', 'spa', 'parking'],
            'images' => ['paradisio1.jpg', 'paradisio2.jpg'],
            'status' => 'active',
        ]);
    }
}
