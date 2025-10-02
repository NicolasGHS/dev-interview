<?php

namespace App\Services;

use App\Models\CardItem;

class CartService
{
    public function addItemToCart(int $userId, int $productId): CardItem
    {
        // Check if item already exists in cart
        $existingItem = CardItem::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($existingItem) {
            // If exists, increment quantity
            $existingItem->increment('quantity');

            return $existingItem->load('product');
        }

        // If doesn't exist, create new cart item
        $cartItem = CardItem::create([
            'user_id' => $userId,
            'product_id' => $productId,
            'quantity' => 1,
        ]);

        return $cartItem->load('product');
    }

    public function getCartItems(int $userId): array
    {
        $cartItems = CardItem::where('user_id', $userId)
            ->with('product')
            ->get();

        return [
            'cart_items' => $cartItems,
            'cart_count' => $cartItems->sum('quantity'),
        ];
    }

    public function removeCartItem(int $userId, int $cartItemId): array
    {
        $cartItem = CardItem::where('id', $cartItemId)
            ->where('user_id', $userId)
            ->firstOrFail();

        $cartItem->delete();

        $cartCount = CardItem::where('user_id', $userId)->sum('quantity');

        return [
            'message' => 'Item removed from cart',
            'cart_count' => $cartCount,
        ];
    }

    public function incrementQuantity(int $userId, int $cartItemId): array
    {
        $cartItem = CardItem::where('id', $cartItemId)
            ->where('user_id', $userId)
            ->firstOrFail();

        $cartItem->increment('quantity');

        $cartCount = CardItem::where('user_id', $userId)->sum('quantity');

        return [
            'message' => 'Quantity updated',
            'item' => $cartItem->load('product'),
            'cart_count' => $cartCount,
        ];
    }

    public function decrementQuantity(int $userId, int $cartItemId): array
    {
        $cartItem = CardItem::where('id', $cartItemId)
            ->where('user_id', $userId)
            ->firstOrFail();

        if ($cartItem->quantity <= 1) {
            // Remove item if quantity would go to 0
            $cartItem->delete();

            $cartCount = CardItem::where('user_id', $userId)->sum('quantity');

            return [
                'message' => 'Item removed from cart',
                'item_removed' => true,
                'cart_count' => $cartCount,
            ];
        }

        $cartItem->decrement('quantity');

        $cartCount = CardItem::where('user_id', $userId)->sum('quantity');

        return [
            'message' => 'Quantity updated',
            'item' => $cartItem->load('product'),
            'cart_count' => $cartCount,
        ];
    }

    public function updateQuantity(int $userId, int $cartItemId, int $quantity): array
    {
        $cartItem = CardItem::where('id', $cartItemId)
            ->where('user_id', $userId)
            ->firstOrFail();

        $cartItem->update(['quantity' => $quantity]);

        $cartCount = CardItem::where('user_id', $userId)->sum('quantity');

        return [
            'message' => 'Quantity updated',
            'item' => $cartItem->load('product'),
            'cart_count' => $cartCount,
        ];
    }
}
