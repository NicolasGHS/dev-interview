<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with products.
     */
    public function index(Request $request): Response
    {
        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 10);

        $products = Product::latest()->paginate($perPage, ['*'], 'page', $page);

        return Inertia::render('Dashboard', [
            'products' => [
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
            ],
        ]);
    }

    /**
     * Display the dashboard with navbar layout.
     */
    public function indexNavbar(Request $request): Response
    {
        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 10);

        $products = Product::latest()->paginate($perPage, ['*'], 'page', $page);

        return Inertia::render('DashboardNavbar', [
            'products' => [
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
            ],
        ]);
    }
}
