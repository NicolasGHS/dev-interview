<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BoardGameGeekService
{
    private const BGG_API_BASE = 'https://boardgamegeek.com/xmlapi2';
    private const REQUEST_DELAY = 1; // BGG requires rate limiting

    /**
     * Extract BoardGameGeek ID from a BGG URL
     * 
     * @param string $url BGG URL like "https://boardgamegeek.com/boardgame/224517/brass-birmingham"
     * @return int|null The game ID or null if not found
     */
    public function extractGameId(string $url): ?int
    {
        // Match pattern: /boardgame/NUMBER/
        if (preg_match('/\/boardgame\/(\d+)\//', $url, $matches)) {
            return (int) $matches[1];
        }
        
        return null;
    }

    /**
     * Fetch game data from BoardGameGeek API
     * 
     * @param int $gameId The BGG game ID
     * @return array|null Game data with image URL, name, etc.
     */
    public function fetchGameData(int $gameId): ?array
    {
        try {
            // BGG API endpoint
            $url = self::BGG_API_BASE . "/thing?id={$gameId}&type=boardgame";
            
            // Add delay to respect BGG rate limits
            sleep(self::REQUEST_DELAY);
            
            $response = Http::timeout(30)->get($url);
            
            if (!$response->successful()) {
                Log::warning("BGG API request failed for game ID {$gameId}: " . $response->status());
                return null;
            }
            
            return $this->parseXmlResponse($response->body());
            
        } catch (\Exception $e) {
            Log::error("Error fetching BGG data for game ID {$gameId}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Parse XML response from BGG API
     * 
     * @param string $xmlContent The XML response body
     * @return array|null Parsed game data
     */
    private function parseXmlResponse(string $xmlContent): ?array
    {
        try {
            $xml = new \SimpleXMLElement($xmlContent);
            
            // Check if we have items
            if (!isset($xml->item) || count($xml->item) === 0) {
                return null;
            }
            
            $item = $xml->item[0];
            
            // Extract the data we need
            $gameData = [
                'name' => (string) $item->name['value'] ?? null,
                'image_url' => (string) $item->image ?? null,
                'thumbnail_url' => (string) $item->thumbnail ?? null,
                'description' => (string) $item->description ?? null,
                'year_published' => (int) $item->yearpublished['value'] ?? null,
                'min_players' => (int) $item->minplayers['value'] ?? null,
                'max_players' => (int) $item->maxplayers['value'] ?? null,
                'playing_time' => (int) $item->playingtime['value'] ?? null,
                'min_age' => (int) $item->minage['value'] ?? null,
            ];
            
            // Prefer the full image over thumbnail
            $gameData['primary_image'] = $gameData['image_url'] ?: $gameData['thumbnail_url'];
            
            return $gameData;
            
        } catch (\Exception $e) {
            Log::error("Error parsing BGG XML response: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Fetch image URL from a BGG game URL
     * 
     * @param string $bggUrl The full BGG URL
     * @return string|null The image URL or null if not found
     */
    public function fetchImageFromUrl(string $bggUrl): ?string
    {
        $gameId = $this->extractGameId($bggUrl);
        
        if (!$gameId) {
            return null;
        }
        
        $gameData = $this->fetchGameData($gameId);
        
        return $gameData['primary_image'] ?? null;
    }

    /**
     * Batch fetch multiple games with rate limiting
     * 
     * @param array $gameIds Array of BGG game IDs
     * @return array Array of game data indexed by game ID
     */
    public function batchFetchGames(array $gameIds): array
    {
        $results = [];
        
        foreach ($gameIds as $gameId) {
            $gameData = $this->fetchGameData($gameId);
            
            if ($gameData) {
                $results[$gameId] = $gameData;
            }
            
            // Progress indicator for large batches
            if (count($results) % 10 === 0) {
                Log::info("Fetched " . count($results) . " games from BGG API");
            }
        }
        
        return $results;
    }
}