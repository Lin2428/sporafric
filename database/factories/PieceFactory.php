<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Piece>
 */
class PieceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'reference' => $this->faker->randomElement(['Pompe', 'Filtre', 'Batterie', 'Alternateur', 'Bougie']),
            'designation' => $this->faker->randomElement(['AD345', 'AD678', 'AD123', 'AD456', 'AD789']),
            'image' => $this->faker->imageUrl(),
            'duree_vie' => $this->faker->numberBetween(50, int2: 2000),
            'pr' => $this->faker->numberBetween(5000, 100000),
            'pv' => $this->faker->numberBetween(8000, 200000),
            'user_id' => 1,
        ];
    }
}
