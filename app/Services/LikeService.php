<?php

namespace App\Services;

use App\Models\Like;
use App\Models\Product;

class LikeService
{
    public function getLikedProducts(int $userId)
    {
        $likedProducts = Product::whereHas('likes', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->get();

        return $likedProducts;
    }

    public function toggleLike(int $userId, int $productId): array
    {
        // Ensure the product exists
        $product = Product::findOrFail($productId);

        $existingLike = Like::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($existingLike) {
            // Unlike - remove the like
            $existingLike->delete();
            $liked = false;
            $message = 'Product removed from favorites';
        } else {
            // Like - create a new like
            Like::create([
                'user_id' => $userId,
                'product_id' => $productId,
            ]);
            $liked = true;
            $message = 'Product added to favorites';
        }

        // Get the updated total likes count for the user
        $likesCount = Like::where('user_id', $userId)->count();

        return [
            'liked' => $liked,
            'message' => $message,
            'likes_count' => $likesCount,
        ];
    }

    public function checkUserLikes(int $userId, array $productIds): array
    {
        $likedProductIds = Like::where('user_id', $userId)
            ->whereIn('product_id', $productIds)
            ->pluck('product_id')
            ->toArray();

        return $likedProductIds;
    }
}
