<?php

namespace Database\Seeders;

use App\Models\Generator;
use App\Models\Piece;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Database\Factories\PieceFactory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Piece::factory(5)->create();
        // User::factory(10)->create();
        //Generator::factory(10)->create();
        //$this->call(CustomerSeeder::class); 
        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
