<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        $hotelRole = Role::where('name', 'hotelier')->first();

        for ($i = 0; $i < 5; $i++) {
            $user = User::create([
                'first_name' => "Hotelier $i",
                'last_name' => "Hotelier $i",
                'email' => "hotelier$i@example.com",
                'password' => Hash::make('password'),
            ]);
            $user->roles()->attach($hotelRole->id);
        }

        for ($i = 0; $i < 10; $i++) {
            User::create([
                'first_name' => "Client $i",
                'last_name' => "Client $i",
                'email' => "client$i@example.com",
                'password' => Hash::make('password'),
            ]);
        }
    }
}
