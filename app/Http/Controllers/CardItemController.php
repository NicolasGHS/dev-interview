<?php

namespace App\Http\Controllers;

use App\Models\CardItem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CardItemController extends Controller
{
    /**
     * Add a product to the user's cart
     */
    public function addCardItem(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer',
            'product_id' => 'required|integer',
        ]);

        CardItem::create($validated);

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

        $cartItems = CardItem::where('user_id', $userId)
            ->with('product')
            ->get();

        return response()->json([
            'success' => true,
            'cart_items' => $cartItems,
            'cart_count' => $cartItems->sum('quantity'),
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

            $cartItem = CardItem::where('id', $id)
                ->where('user_id', $userId)
                ->firstOrFail();

            $cartItem->delete();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Item removed from cart',
                    'cart_count' => CardItem::where('user_id', $userId)->sum('quantity'),
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

            $cartItem = CardItem::where('id', $id)
                ->where('user_id', $userId)
                ->firstOrFail();

            $cartItem->increment('quantity');

            return response()->json([
                'success' => true,
                'message' => 'Quantity updated',
                'item' => $cartItem->load('product'),
                'cart_count' => CardItem::where('user_id', $userId)->sum('quantity'),
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

            $cartItem = CardItem::where('id', $id)
                ->where('user_id', $userId)
                ->firstOrFail();

            if ($cartItem->quantity <= 1) {
                // Remove item if quantity would go to 0
                $cartItem->delete();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Item removed from cart',
                    'item_removed' => true,
                    'cart_count' => CardItem::where('user_id', $userId)->sum('quantity'),
                ]);
            }

            $cartItem->decrement('quantity');

            return response()->json([
                'success' => true,
                'message' => 'Quantity updated',
                'item' => $cartItem->load('product'),
                'cart_count' => CardItem::where('user_id', $userId)->sum('quantity'),
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

            $cartItem = CardItem::where('id', $id)
                ->where('user_id', $userId)
                ->firstOrFail();

            $cartItem->update(['quantity' => $validated['quantity']]);

            return response()->json([
                'success' => true,
                'message' => 'Quantity updated',
                'item' => $cartItem->load('product'),
                'cart_count' => CardItem::where('user_id', $userId)->sum('quantity'),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating quantity',
            ], 500);
        }
    }
}
