<?php

namespace App\Console\Commands;

use App\Services\BoardGameGeekService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class TestBoardGameGeekApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bgg:test {--game-id= : Test with a specific BGG game ID} {--limit=5 : Number of games to test}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the BoardGameGeek API integration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $bggService = new BoardGameGeekService();
        
        // If specific game ID provided, test that
        if ($gameId = $this->option('game-id')) {
            $this->testSingleGame($bggService, (int) $gameId);
            return;
        }
        
        // Otherwise test with sample games from our data
        $this->testWithSampleGames($bggService);
    }
    
    private function testSingleGame(BoardGameGeekService $bggService, int $gameId): void
    {
        $this->info("Testing BGG API with game ID: {$gameId}");
        $this->info("API URL: https://boardgamegeek.com/xmlapi2/thing?id={$gameId}&type=boardgame");
        
        $gameData = $bggService->fetchGameData($gameId);
        
        if ($gameData) {
            $this->info("✅ Successfully fetched game data:");
            $this->table(
                ['Field', 'Value'],
                [
                    ['Name', $gameData['name'] ?? 'N/A'],
                    ['Image URL', $gameData['image_url'] ?? 'N/A'],
                    ['Thumbnail URL', $gameData['thumbnail_url'] ?? 'N/A'],
                    ['Primary Image', $gameData['primary_image'] ?? 'N/A'],
                    ['Year Published', $gameData['year_published'] ?? 'N/A'],
                    ['Players', ($gameData['min_players'] ?? 'N/A') . '-' . ($gameData['max_players'] ?? 'N/A')],
                    ['Playing Time', $gameData['playing_time'] ?? 'N/A'],
                    ['Min Age', $gameData['min_age'] ?? 'N/A'],
                ]
            );
        } else {
            $this->error("❌ Failed to fetch game data");
        }
    }
    
    private function testWithSampleGames(BoardGameGeekService $bggService): void
    {
        $this->info("Testing BGG API with sample games from boardgames.json...");
        
        // Load sample games from our JSON data
        $jsonPath = database_path('data/boardgames.json');
        
        if (!File::exists($jsonPath)) {
            $this->error("boardgames.json not found");
            return;
        }
        
        $games = json_decode(File::get($jsonPath), true);
        $limit = (int) $this->option('limit');
        $tested = 0;
        
        foreach ($games as $game) {
            if ($tested >= $limit) break;
            
            $linkToGame = $game['link_to_game'] ?? null;
            
            if (!$linkToGame) {
                continue;
            }
            
            $gameId = $bggService->extractGameId($linkToGame);
            
            if (!$gameId) {
                $this->warn("Could not extract game ID from: {$linkToGame}");
                continue;
            }
            
            $this->info("\n🎲 Testing: {$game['boardgame']} (ID: {$gameId})");
            $this->info("BGG URL: {$linkToGame}");
            
            $gameData = $bggService->fetchGameData($gameId);
            
            if ($gameData && $gameData['primary_image']) {
                $this->info("✅ Image found: {$gameData['primary_image']}");
            } else {
                $this->warn("❌ No image found");
            }
            
            $tested++;
        }
        
        $this->info("\n🎉 Tested {$tested} games");
    }
}