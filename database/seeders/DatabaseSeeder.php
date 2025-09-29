<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        Product::factory(10)->create();

        // Product test data
        Product::factory()->create([
            'name' => 'Wireless Bluetooth Headphones',
            'description' => 'High-quality wireless headphones with noise cancellation and 20-hour battery life.',
            'price' => 99.99,
        ]);

        Product::factory()->create([
            'name' => 'Smartphone Stand',
            'description' => 'Adjustable aluminum smartphone stand compatible with all devices.',
            'price' => 24.99,
        ]);

        Product::factory()->create([
            'name' => 'USB-C Cable',
            'description' => 'Fast charging USB-C cable, 6 feet long with reinforced connectors.',
            'price' => 15.99,
        ]);
    }
}
