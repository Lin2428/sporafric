<?php

namespace Database\Seeders;

use App\Models\Generator;
use Illuminate\Database\Console\Seeds\Withoutreferencevents;
use Illuminate\Database\Seeder;

class GeneratorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Generator::factory(10)->create();
        // $this->call(CustomerSeeder::class);
        // \App\Models\Generator::factory(10)->create();
        // \App\Models\Generator::factory(10)->create();
    }
}
