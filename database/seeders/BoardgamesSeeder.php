<?php

namespace Database\Seeders;

use App\Services\BoardGameGeekService;
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
        
        // Limit the number of games to seed (configurable via environment or parameter)
        $limit = (int) env('SEED_GAMES_LIMIT', 50);
        $fetchImages = env('SEED_FETCH_IMAGES', true);
        
        $this->command->info("Seeding {$limit} board games" . ($fetchImages ? ' with images from BGG...' : '...'));
        
        // Take only the first N games for faster seeding
        $boardgames = array_slice($boardgames, 0, $limit);
        
        $bggService = $fetchImages ? new BoardGameGeekService() : null;
        $processed = 0;
        $withImages = 0;

        foreach ($boardgames as $game) {
            $processed++;
            
            // Extract image URL from BGG if service is available
            $imageUrl = null;
            if ($bggService && isset($game['link_to_game'])) {
                $imageUrl = $bggService->fetchImageFromUrl($game['link_to_game']);
                if ($imageUrl) {
                    $withImages++;
                }
            }

            DB::table('products')->insert([
                'name' => $game['boardgame'] ?? 'Unknown',
                'description' => $game['description'],
                'price' => $game['amazon_price'] ?? 30,
                'image_url' => $imageUrl,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // Progress indicator every 10 games
            if ($processed % 10 === 0) {
                $this->command->info("Processed {$processed}/{$limit} games" . ($fetchImages ? " ({$withImages} with images)" : ""));
            }
        }
        
        $this->command->info("✅ Completed! Seeded {$processed} games" . ($fetchImages ? ", {$withImages} with images from BGG API" : ""));
        
        if ($fetchImages && $withImages < $processed) {
            $this->command->info("💡 Tip: You can update remaining images later with: php artisan products:update-images");
        }
    }
}
