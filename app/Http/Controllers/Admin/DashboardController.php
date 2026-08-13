<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Blog;
use App\Models\ChatHistory;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProducts = Product::count();
        $totalCategories = Category::count();
        $totalBlogs = Blog::count();
        $totalChatQueries = ChatHistory::count();
        $recentProducts = Product::with('category')->latest()->take(5)->get();
        $featuredProductsCount = Product::where('is_featured', true)->count();

        return view('admin.dashboard', compact(
            'totalProducts',
            'totalCategories',
            'totalBlogs',
            'totalChatQueries',
            'recentProducts',
            'featuredProductsCount'
        ));
    }
}
