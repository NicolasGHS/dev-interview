<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    /**
     * Display the specified product.
     */
    public function show(int $id): Response
    {
        $product = Product::findOrFail($id);

        return Inertia::render('Product', [
            'product' => $product,
        ]);
    }

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

    /**
     * Search products by name or description.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $perPage = $request->get('per_page', 15);
        $perPage = min($perPage, 100);

        $products = Product::where('name', 'LIKE', '%' . $query . '%')
            ->orWhere('description', 'LIKE', '%' . $query . '%')
            ->latest()
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

    public function getProductById(int $id)
    {
        $product = Product::findOrFail($id);

        return response()->json(
            [
                'data' => $product,
            ],
        );
    }
}
