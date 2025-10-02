<?php

namespace App\Services;

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
}
