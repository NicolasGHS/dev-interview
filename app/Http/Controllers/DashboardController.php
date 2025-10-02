<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    protected ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    // TODO: check if functions can be merged

    /**
     * Display the dashboard with products.
     */
    public function index(Request $request): Response
    {
        $products = $this->productService->getPaginatedProducts(
            $request->get('search'),
            $request->get('per_page', 10),
            $request->get('page', 1),
        );

        return Inertia::render('Dashboard', [
            'products' => $products,
            'search' => $request->get('search', ''),
        ]);
    }

    /**
     * Display the dashboard with navbar layout.
     */
    public function indexNavbar(Request $request): Response
    {

        $products = $this->productService->getPaginatedProducts(
            $request->get('search'),
            $request->get('per_page', 10),
            $request->get('page', 1),
        );

        return Inertia::render('DashboardNavbar', [
            'products' => $products,
            'search' => $request->get('search', ''),

        ]);
    }
}
