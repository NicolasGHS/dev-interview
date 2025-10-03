<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SeedBoardGames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'boardgames:seed 
                            {--limit=50 : Number of games to seed}
                            {--no-images : Skip fetching images from BGG}
                            {--fresh : Fresh migration before seeding}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed board games with configurable options';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = (int) $this->option('limit');
        $noImages = $this->option('no-images');
        $fresh = $this->option('fresh');
        
        if ($fresh) {
            if ($this->confirm('This will delete all existing data. Are you sure?')) {
                $this->info('🔄 Running fresh migrations...');
                $this->call('migrate:fresh');
            } else {
                $this->info('❌ Cancelled');
                return;
            }
        }
        
        // Set environment variables for this seeding session
        putenv("SEED_GAMES_LIMIT={$limit}");
        putenv("SEED_FETCH_IMAGES=" . ($noImages ? 'false' : 'true'));
        
        $this->info("🎲 Seeding {$limit} board games" . ($noImages ? ' (without images)' : ' (with BGG images)'));
        
        if (!$noImages) {
            $estimatedTime = ceil($limit / 10) * 10; // Rough estimate: ~10 seconds per 10 games
            $this->info("⏱️  Estimated time: ~{$estimatedTime} seconds");
        }
        
        $startTime = microtime(true);
        
        $this->call('db:seed', ['--class' => 'BoardgamesSeeder']);
        
        $endTime = microtime(true);
        $duration = round($endTime - $startTime, 2);
        
        $this->info("⚡ Completed in {$duration} seconds");
        
        // Show some stats
        $this->call('tinker', [
            '--execute' => 'echo "📊 Database stats:\nTotal products: " . App\Models\Product::count() . "\nWith images: " . App\Models\Product::whereNotNull("image_url")->count() . "\n";'
        ]);
    }
}