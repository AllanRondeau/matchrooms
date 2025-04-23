<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'admin',
                'display_name' => 'Administrator',
                'description' => 'Full access to all system features',
            ],
            [
                'name' => 'user',
                'display_name' => 'Regular User',
                'description' => 'Standard user with access to booking features',
            ],
            [
                'name' => 'hotelier',
                'display_name' => 'Hotelier',
                'description' => 'Hotel manager with access to hotel management features',
            ],
            [
                'name' => 'hotel_staff',
                'display_name' => 'Hotel Staff',
                'description' => 'Hotel staff with limited management capabilities',
            ],
            [
                'name' => 'moderator',
                'display_name' => 'Content Moderator',
                'description' => 'Can moderate reviews and user content',
            ],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->insert($role);
        }
    }
}