<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Hotelier;
use Illuminate\Support\Facades\Hash;

class HotelierSeeder extends Seeder
{
    public function run(): void
    {
        Hotelier::create([
            'email' => 'admin@hotel.com',
            'password' => Hash::make('password'), // ou bcrypt('password')
            'company_name' => 'LuxuryStay Inc.',
            'contact_name' => 'John Doe',
            'phone_number' => '+33 1 23 45 67 89',
            'status' => 'active',
        ]);

        Hotelier::create([
            'email' => 'contact@oceanview.com',
            'password' => Hash::make('secure123'),
            'company_name' => 'Ocean View Hotels',
            'contact_name' => 'Alice Durand',
            'phone_number' => '+33 6 12 34 56 78',
            'status' => 'pending',
        ]);
    }
}
