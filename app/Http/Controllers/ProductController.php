<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a paginated listing of products.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProductsPaginated(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $perPage = min($perPage, 100);

        $products = Product::latest()
            ->paginate($perPage);

        return response()->json([
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
        ]);
    }
}
