<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Services\SearchService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::where('status', true)->with('category');

        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        if ($request->filled('brand')) {
            $query->where('brand', 'LIKE', '%' . $request->brand . '%');
        }

        if ($request->filled('search')) {
            $searchResult = SearchService::search($request->search);
            if (!empty($searchResult['products'])) {
                $productIds = array_map(fn($p) => $p->id, $searchResult['products']);
                $products = Product::where('status', true)->with('category')->whereIn('id', $productIds)->paginate(16);
                $categories = Category::whereNull('parent_id')->orderBy('sort_order', 'asc')->orderBy('name', 'asc')->with('children')->get();
                return view('products.index', compact('products', 'categories'));
            }
        }

        $products = $query->orderBy('sort_order', 'asc')->paginate(16);
        $categories = Category::whereNull('parent_id')->orderBy('sort_order', 'asc')->orderBy('name', 'asc')->with('children')->get();

        return view('products.index', compact('products', 'categories'));
    }

    public function category(string $slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        // Get all product IDs in this category and all its descendant subcategories recursively
        $allProducts = $category->getAllProductsRecursive();
        $productIds = $allProducts->pluck('id');

        $products = Product::whereIn('id', $productIds)
            ->where('status', true)
            ->orderBy('sort_order', 'asc')
            ->paginate(16);

        $categories = Category::whereNull('parent_id')->orderBy('sort_order', 'asc')->orderBy('name', 'asc')->with('children')->get();

        return view('products.category', compact('category', 'products', 'categories'));
    }

    /**
     * Dynamic Multi-Level SEO Route Resolver e.g. /c/gacl-products/acid-products/nitric-acid
     */
    public function resolvePath(string $path)
    {
        $segments = array_values(array_filter(explode('/', trim($path, '/'))));
        if (empty($segments)) {
            return redirect()->route('products.index');
        }

        $lastSlug = end($segments);

        // 1. Try matching Product
        $product = Product::where('slug', $lastSlug)->first();
        if ($product) {
            $relatedProducts = $product->relatedProducts();
            return view('products.show', compact('product', 'relatedProducts'));
        }

        // 2. Try matching Category
        $category = Category::where('slug', $lastSlug)->first();
        if ($category) {
            return $this->category($category->slug);
        }

        // 3. Fallback to Search Service
        $searchRes = SearchService::search(str_replace('-', ' ', $lastSlug));
        if ($searchRes['count'] > 0) {
            $product = $searchRes['products'][0];
            $relatedProducts = $product->relatedProducts();
            return view('products.show', compact('product', 'relatedProducts'));
        }

        abort(404, 'Product or Category path not found');
    }

    public function brand(string $brand)
    {
        $cleanBrand = rawurldecode($brand);
        $products = Product::where('status', true)->where('brand', 'LIKE', "%{$cleanBrand}%")->paginate(16);
        $categories = Category::whereNull('parent_id')->orderBy('sort_order', 'asc')->orderBy('name', 'asc')->with('children')->get();
        $category = (object) [
            'name' => strtoupper($cleanBrand) . ' Products',
            'description' => 'Browse high purity ' . strtoupper($cleanBrand) . ' chemicals supplied by SR Chemical Industries Limited.'
        ];

        return view('products.category', compact('category', 'products', 'categories'));
    }

    public function show(string $slug)
    {
        // Strip .php extension if coming from legacy route
        $cleanSlug = str_replace('.php', '', $slug);

        // Check if $cleanSlug is a Category (e.g., sulfur-products, acid-products, etc.)
        $categoryMatch = Category::where('slug', $cleanSlug)->first();
        if ($categoryMatch) {
            return redirect()->route('products.category', $cleanSlug);
        }

        $product = Product::with('category')->where('slug', $cleanSlug)->first();

        if (!$product) {
            // Search service fallback matching
            $searchRes = SearchService::search(str_replace('-', ' ', $cleanSlug));
            if ($searchRes['count'] > 0) {
                $product = $searchRes['products'][0];
            }
        }

        if (!$product) {
            abort(404, 'Product not found');
        }

        $relatedProducts = $product->relatedProducts();

        return view('products.show', compact('product', 'relatedProducts'));
    }
}

