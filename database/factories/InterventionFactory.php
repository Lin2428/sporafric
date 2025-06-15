<?php

namespace Database\Factories;

use App\Enum\InterventionStatus;
use App\Enum\InterventionType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Intervention>
 */
class InterventionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type_service' => 1,
            'contract_id' => $this->faker->randomElement([1, 2, 4]),
            'date_prise_appel' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'date_planifiee' => $this->faker->dateTimeBetween('now', '+1 month'),
            'type' => $this->faker->randomElement(InterventionType::cases())->value,
            'identifiant' => $this->faker->unique()->word,
            'description_panne' => $this->faker->sentence(10),
            'start_date' => $this->faker->dateTimeBetween('now', '+1 month'),
            'end_date' => $this->faker->dateTimeBetween('now', '+1 month'),
            'status' => $this->faker->randomElement(InterventionStatus::cases())->value,
            'created_at' => $this->faker->dateTimeBetween('-3 month', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-3 month', 'now'),
        ];
    }
}
