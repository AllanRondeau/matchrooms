<?php

namespace Database\Seeders;

use App\Models\Hotel;
use App\Models\RoomType;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class RoomTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        // Récupérer tous les hôtels ou créer un exemple si aucun n'existe
        $hotels = Hotel::all();
        if ($hotels->isEmpty()) {
            // Créer quelques hôtels fictifs s'il n'y en a pas
            for ($i = 0; $i < 5; $i++) {
                $hotels[] = Hotel::create([
                    'hotelier_id' => 1, // Vous pourriez avoir besoin d'ajuster cela
                    'name' => $faker->company . ' Hotel',
                    'description' => $faker->paragraph(5),
                    'address' => $faker->address,
                    'city' => $faker->city,
                    'country' => $faker->country,
                    'postal_code' => $faker->postcode,
                    'star_rating' => $faker->numberBetween(1, 5),
                    'amenities' => ['wifi', 'pool', 'restaurant', 'parking', 'gym', 'spa'],
                    'images' => [
                        "hotel_" . $faker->numberBetween(1, 10) . ".jpg",
                        "hotel_" . $faker->numberBetween(1, 10) . ".jpg"
                    ],
                    'status' => 'active',
                ]);
            }
        }

        // Exemples de descriptions avec des vues et caractéristiques distinctes
        $roomDescriptions = [
            // Chambres avec vue sur mer
            [
                'description' => 'Magnifique chambre avec une vue imprenable sur la mer. Profitez du son des vagues depuis votre balcon privé.',
                'amenities' => ['wifi', 'air_conditioning', 'balcony', 'mini_bar', 'sea_view'],
                'name' => 'Chambre Vue Mer Deluxe',
            ],
            [
                'description' => 'Suite élégante avec panorama sur l\'océan. Un espace de détente parfait pour admirer le coucher du soleil sur la mer.',
                'amenities' => ['wifi', 'air_conditioning', 'balcony', 'mini_bar', 'room_service', 'sea_view'],
                'name' => 'Suite Océanique',
            ],

            // Chambres avec vue montagne
            [
                'description' => 'Chambre confortable avec vue spectaculaire sur les montagnes environnantes. Un havre de paix pour les amoureux de la nature.',
                'amenities' => ['wifi', 'air_conditioning', 'mountain_view', 'coffee_maker'],
                'name' => 'Chambre Montagne',
            ],
            [
                'description' => 'Suite rustique avec panorama sur les sommets enneigés. Le cadre idéal pour se ressourcer en pleine nature.',
                'amenities' => ['wifi', 'air_conditioning', 'fireplace', 'balcony', 'mountain_view'],
                'name' => 'Suite Alpine',
            ],

            // Chambres avec vue jardin
            [
                'description' => 'Chambre paisible donnant sur nos jardins luxuriants. Éveillez-vous au chant des oiseaux et aux senteurs florales.',
                'amenities' => ['wifi', 'air_conditioning', 'garden_view', 'coffee_maker'],
                'name' => 'Chambre Jardin',
            ],
            [
                'description' => 'Suite spacieuse avec terrasse privée sur le jardin tropical. Un coin de paradis pour les amateurs de nature.',
                'amenities' => ['wifi', 'air_conditioning', 'terrace', 'mini_bar', 'garden_view'],
                'name' => 'Suite Jardin',
            ],

            // Chambres standards
            [
                'description' => 'Chambre standard confortable, parfaite pour un séjour d\'affaires ou de courte durée.',
                'amenities' => ['wifi', 'air_conditioning', 'desk'],
                'name' => 'Chambre Standard',
            ],
            [
                'description' => 'Chambre économique fonctionnelle, idéale pour les voyageurs soucieux de leur budget.',
                'amenities' => ['wifi', 'fan'],
                'name' => 'Chambre Économique',
            ],

            // Chambres familiales
            [
                'description' => 'Grande chambre familiale pouvant accueillir jusqu\'à 5 personnes. Espace et confort pour toute la famille.',
                'amenities' => ['wifi', 'air_conditioning', 'multiple_beds', 'tv', 'refrigerator'],
                'name' => 'Chambre Familiale',
            ],
            [
                'description' => 'Suite familiale avec deux chambres séparées. L\'équilibre parfait entre intimité et convivialité.',
                'amenities' => ['wifi', 'air_conditioning', 'multiple_rooms', 'tv', 'kitchenette'],
                'name' => 'Suite Familiale',
            ],

            // Chambres avec vue ville
            [
                'description' => 'Chambre moderne avec vue panoramique sur la ville. Admirez les lumières urbaines depuis votre fenêtre.',
                'amenities' => ['wifi', 'air_conditioning', 'city_view', 'desk', 'smart_tv'],
                'name' => 'Chambre Vue Ville',
            ],
            [
                'description' => 'Suite urbaine spacieuse offrant une vue imprenable sur la skyline. Idéale pour les voyageurs d\'affaires.',
                'amenities' => ['wifi', 'air_conditioning', 'city_view', 'work_area', 'mini_bar', 'espresso_machine'],
                'name' => 'Suite Executive',
            ],
        ];

        // Créer plusieurs types de chambres pour chaque hôtel
        foreach ($hotels as $hotel) {
            // Choisir aléatoirement 4-6 types de chambres pour cet hôtel
            $selectedDescriptions = $faker->randomElements(
                $roomDescriptions,
                $faker->numberBetween(4, min(6, count($roomDescriptions)))
            );

            foreach ($selectedDescriptions as $roomData) {
                // Créer des images aléatoires
                $imageCount = $faker->numberBetween(3, 6);
                $images = [];
                for ($i = 0; $i < $imageCount; $i++) {
                    $images[] = "room_" . $faker->numberBetween(1, 20) . ".jpg";
                }

                RoomType::create([
                    'hotel_id' => $hotel->id,
                    'name' => $roomData['name'],
                    'description' => $roomData['description'],
                    'base_price' => $faker->randomFloat(2, 50, 500),
                    'capacity' => $faker->numberBetween(1, 6),
                    'amenities' => $roomData['amenities'],
                    'images' => $images,
                    'stock_quantity' => $faker->numberBetween(5, 20),
                    'status' => $faker->randomElement(['active', 'inactive', 'maintenance']),
                ]);
            }
        }
    }
}