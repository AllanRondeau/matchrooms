<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName,
            'last_name'  => $this->faker->lastName,
            'email'      => $this->faker->unique()->safeEmail,
            'password'   => 'password', // Hashed via cast
            'phone_number' => $this->faker->phoneNumber,
            'status'     => 'active',
            'company_name' => $this->faker->company,
            'role'       => $this->faker->randomElement(['admin', 'hotelier', 'client']),
        ];
    }
}
