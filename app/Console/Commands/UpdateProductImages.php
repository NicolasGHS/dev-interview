<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Services\BoardGameGeekService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class UpdateProductImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:update-images {--limit=20 : Limit number of products to update} {--force : Update all products, even those with existing images}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update product images from BoardGameGeek API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = (int) $this->option('limit');
        $force = $this->option('force');
        
        $this->info('🔄 Updating product images from BoardGameGeek API...');
        
        // Load the original boardgames data to get BGG URLs
        $jsonPath = database_path('data/boardgames.json');
        
        if (!File::exists($jsonPath)) {
            $this->error('❌ boardgames.json not found');
            return 1;
        }
        
        $boardgamesData = json_decode(File::get($jsonPath), true);
        
        // Create a map of game names to BGG URLs
        $bggUrlMap = [];
        foreach ($boardgamesData as $game) {
            if (isset($game['boardgame']) && isset($game['link_to_game'])) {
                $bggUrlMap[$game['boardgame']] = $game['link_to_game'];
            }
        }
        
        $this->info("📊 Found BGG URLs for " . count($bggUrlMap) . " games in JSON data");
        
        // Get products that need image updates
        $query = Product::query();
        
        if (!$force) {
            $query->whereNull('image_url');
        }
        
        $products = $query->limit($limit)->get();
        
        if ($products->isEmpty()) {
            $this->info('✅ No products need image updates');
            return 0;
        }
        
        $this->info("🎯 Found {$products->count()} products to update");
        
        $bggService = new BoardGameGeekService();
        $updated = 0;
        $skipped = 0;
        
        $progressBar = $this->output->createProgressBar($products->count());
        $progressBar->start();
        
        foreach ($products as $product) {
            $progressBar->advance();
            
            // Find the BGG URL for this product
            $bggUrl = $bggUrlMap[$product->name] ?? null;
            
            if (!$bggUrl) {
                $skipped++;
                continue;
            }
            
            // Fetch image from BGG
            $imageUrl = $bggService->fetchImageFromUrl($bggUrl);
            
            if ($imageUrl) {
                $product->update(['image_url' => $imageUrl]);
                $updated++;
            } else {
                $skipped++;
            }
        }
        
        $progressBar->finish();
        $this->newLine();
        
        $this->info("✅ Update complete!");
        $this->info("📈 Updated: {$updated} products");
        $this->info("⏭️  Skipped: {$skipped} products");
        
        if ($updated > 0) {
            $this->info("🎉 Products now have images from BoardGameGeek!");
        }
        
        return 0;
    }
}