<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CardItemController extends Controller
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Add a product to the user's cart
     */
    public function addCardItem(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer',
            'product_id' => 'required|integer',
        ]);

        $this->cartService->addItemToCart($validated['user_id'], $validated['product_id']);

        return redirect()->route('basket')->with('success', 'Added to card');
    }

    /**
     * Get user's cart items
     */
    public function getCartItems(): JsonResponse
    {
        $userId = Auth::id();

        if (! $userId) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        $result = $this->cartService->getCartItems($userId);

        return response()->json([
            'success' => true,
            'cart_items' => $result['cart_items'],
            'cart_count' => $result['cart_count'],
        ]);
    }

    /**
     * Remove item from cart
     */
    public function removeCartItem(Request $request, $id): JsonResponse|RedirectResponse
    {
        try {
            $userId = Auth::id();

            if (! $userId) {
                return response()->json(['error' => 'User not authenticated'], 401);
            }

            $result = $this->cartService->removeCartItem($userId, $id);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'cart_count' => $result['cart_count'],
                ]);
            }

            return redirect()->back()->with('success', 'Item removed from cart');

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while removing item',
                ], 500);
            }

            return redirect()->back()->with('error', 'An error occurred while removing item');
        }
    }

    /**
     * Increment cart item quantity
     */
    public function incrementQuantity(Request $request, $id): JsonResponse
    {
        try {
            $userId = Auth::id();

            if (! $userId) {
                return response()->json(['error' => 'User not authenticated'], 401);
            }

            $result = $this->cartService->incrementQuantity($userId, $id);

            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'item' => $result['item'],
                'cart_count' => $result['cart_count'],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating quantity',
            ], 500);
        }
    }

    /**
     * Decrement cart item quantity
     */
    public function decrementQuantity(Request $request, $id): JsonResponse
    {
        try {
            $userId = Auth::id();

            if (! $userId) {
                return response()->json(['error' => 'User not authenticated'], 401);
            }

            $result = $this->cartService->decrementQuantity($userId, $id);

            if (isset($result['item_removed']) && $result['item_removed']) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'item_removed' => true,
                    'cart_count' => $result['cart_count'],
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'item' => $result['item'],
                'cart_count' => $result['cart_count'],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating quantity',
            ], 500);
        }
    }

    /**
     * Update cart item quantity to specific value
     */
    public function updateQuantity(Request $request, $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'quantity' => 'required|integer|min:1|max:99'
            ]);

            $userId = Auth::id();

            if (! $userId) {
                return response()->json(['error' => 'User not authenticated'], 401);
            }

            $result = $this->cartService->updateQuantity($userId, $id, $validated['quantity']);

            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'item' => $result['item'],
                'cart_count' => $result['cart_count'],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating quantity',
            ], 500);
        }
    }
}
