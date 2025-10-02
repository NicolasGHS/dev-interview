<?php

namespace App\Http\Controllers;

use App\Helpers\PaginationHelper;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    protected ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * Display the specified product.
     */
    public function show(int $id): Response
    {
        $product = $this->productService->getProductById($id);

        return Inertia::render('Product', [
            'product' => $product,
        ]);
    }

    /**
     * Search products by name or description.
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q', '');
        $perPage = min((int) $request->get('per_page', 15), 100);

        $paginator = $this->productService->searchProducts($query, $perPage);

        return response()->json([
            'data' => $paginator->items(),
            'pagination' => PaginationHelper::format($paginator),
        ]);
    }

    public function getProductById(int $id)
    {
        $product = $this->productService->getProductById($id);

        return response()->json(
            [
                'data' => $product,
            ],
        );
    }
}
