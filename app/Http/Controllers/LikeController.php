<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class LikeController extends Controller
{
    protected ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

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

        if (! $userId) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        $likedProducts = $this->productService->getLikedProducts($userId);

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

            if (! $userId) {
                return response()->json(['error' => 'User not authenticated'], 401);
            }

            $result = $this->productService->toggleLike($userId, $productId);

            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'liked' => $result['liked'],
                'likes_count' => $result['likes_count'],
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

        if (! $userId) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        $productIds = $request->input('product_ids', []);

        $likedProductIds = $this->productService->checkUserLikes($userId, $productIds);

        return response()->json([
            'success' => true,
            'liked_product_ids' => $likedProductIds,
        ]);
    }
}
