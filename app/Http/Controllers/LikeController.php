<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class LikeController extends Controller
{
    /**
     * Display the user's liked products page
     */
    public function index(): Response
    {
        return Inertia::render('Likes');
    }

    /**
     * Get user's liked products
     */
    public function getLikedProducts(): JsonResponse
    {
        $userId = Auth::id();

        if (!$userId) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        $likedProducts = Product::whereHas('likes', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->get();

        return response()->json([
            'success' => true,
            'liked_products' => $likedProducts,
            'likes_count' => $likedProducts->count(),
        ]);
    }

    /**
     * Toggle like status for a product
     */
    public function toggleLike(Request $request, $productId): JsonResponse
    {
        try {
            $userId = Auth::id();

            if (!$userId) {
                return response()->json(['error' => 'User not authenticated'], 401);
            }

            $product = Product::findOrFail($productId);

            $existingLike = Like::where('user_id', $userId)
                ->where('product_id', $productId)
                ->first();

            if ($existingLike) {
                // Unlike
                $existingLike->delete();
                $liked = false;
                $message = 'Product removed from favorites';
            } else {
                // Like
                Like::create([
                    'user_id' => $userId,
                    'product_id' => $productId,
                ]);
                $liked = true;
                $message = 'Product added to favorites';
            }

            $likesCount = Like::where('user_id', $userId)->count();

            return response()->json([
                'success' => true,
                'message' => $message,
                'liked' => $liked,
                'likes_count' => $likesCount,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating favorites',
            ], 500);
        }
    }

    /**
     * Check if user has liked specific products
     */
    public function checkLikes(Request $request): JsonResponse
    {
        $userId = Auth::id();

        if (!$userId) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        $productIds = $request->input('product_ids', []);
        
        $likedProductIds = Like::where('user_id', $userId)
            ->whereIn('product_id', $productIds)
            ->pluck('product_id')
            ->toArray();

        return response()->json([
            'success' => true,
            'liked_product_ids' => $likedProductIds,
        ]);
    }
}
