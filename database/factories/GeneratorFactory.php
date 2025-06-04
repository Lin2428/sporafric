<?php

namespace Database\Factories;

use App\Enum\GeneratorStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Generator>
 */
class GeneratorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'reference' => $this->faker->randomElement(['SDMO', 'Kohler', 'Perkins', 'Caterpillar', 'Cummins']),
            //'reference' => $this->faker->randomElement(['SDMO 20KVA', 'Kohler 30KVA', 'Perkins 50KVA', 'Caterpillar 100KVA', 'Cummins 150KVA']),
            'voltage' => $this->faker->numberBetween(220, 600),
            'frequency' => $this->faker->numberBetween(50, 60),
            'serial_number' => $this->faker->unique()->numberBetween(100000, 999999),
            'start-up' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'status' => $this->faker->randomElement(GeneratorStatus::cases()),
            'houres' => $this->faker->numberBetween(0, 10000),
            'power' => $this->faker->numberBetween(1, 100),
            'next_vidange' => $this->faker->numberBetween(24, 1000),
            'fuel_type' => $this->faker->randomElement(['Diesel', 'Essence']),
        ];
    }
}
