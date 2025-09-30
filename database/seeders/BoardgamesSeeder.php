<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class BoardgamesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = File::get(database_path('data/boardgames.json'));
        $boardgames = json_decode($json, true);

        foreach ($boardgames as $game) {
            DB::table('products')->insert([
                'name' => $game['boardgame'] ?? 'Unknown',
                'description' => $game['description'],
                'price' => $game['amazon_price'] ?? 30,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
