<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\SearchService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Product::with('category');

        if ($request->has('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->get('category'));
            });
        }

        $products = $query->get();

        return response()->json([
            'status' => 'success',
            'count' => $products->count(),
            'products' => $products
        ]);
    }

    public function search(Request $request): JsonResponse
    {
        $q = trim($request->get('q', ''));

        if (empty($q)) {
            return response()->json([
                'status' => 'success',
                'query' => '',
                'match_type' => 'none',
                'priority' => 0,
                'count' => 0,
                'products' => []
            ]);
        }

        $result = SearchService::search($q);

        return response()->json([
            'status' => 'success',
            'query' => $q,
            'match_type' => $result['match_type'],
            'priority' => $result['priority'] ?? 0,
            'count' => $result['count'],
            'products' => $result['products']
        ]);
    }

    public function show(string $slug): JsonResponse
    {
        $product = Product::with('category')->where('slug', $slug)->first();

        if (!$product) {
            $searchRes = SearchService::search(str_replace('-', ' ', $slug));
            if ($searchRes['count'] > 0) {
                $product = $searchRes['products'][0];
            }
        }

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found'
            ], 404);
        }

        $related = $product->relatedProducts();

        return response()->json([
            'status' => 'success',
            'product' => $product,
            'related_products' => $related
        ]);
    }
}
