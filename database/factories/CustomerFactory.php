<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company,
            'contact_c_name' => $this->faker->name,
            'contact_c_email' => $this->faker->unique()->safeEmail,
            'contact_c_phone' => $this->faker->phoneNumber,
            'contact_l_name' => $this->faker->name,
            'contact_l_email' => $this->faker->unique()->safeEmail,
            'contact_l_phone' => $this->faker->phoneNumber,
            'logo' => $this->faker->imageUrl(640, 480, 'business'),
            'is_active' => $this->faker->boolean,
            'user_id' => \App\Models\User::factory(),
        ];
    }
}
