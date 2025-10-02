<?php

namespace App\Services;

use App\Models\Like;
use App\Models\Product;

class ProductService
{
    public function getPaginatedProducts(?string $search, int $perPage, int $page)
    {
        if ($search) {
            $products = Product::where('name', 'LIKE', '%'.$search.'%')
                ->orWhere('description', 'LIKE', '%'.$search.'%')
                ->latest()
                ->paginate($perPage, ['*'], 'page', $page);
        } else {
            $products = Product::latest()->paginate($perPage, ['*'], 'page', $page);
        }

        return [
            'data' => $products->items(),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
                'from' => $products->firstItem(),
                'to' => $products->lastItem(),
                'has_more_pages' => $products->hasMorePages(),
            ],
        ];
    }

    public function getProductById(int $id)
    {
        $product = Product::findOrFail($id);

        return $product;
    }

    public function searchProducts(string $query, int $perPage)
    {
        return Product::where('name', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->latest()
            ->paginate($perPage);
    }

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
