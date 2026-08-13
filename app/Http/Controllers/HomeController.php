<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Company;
use App\Models\Blog;
use App\Models\ExportCountry;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $company = Company::first();
        $categories = Category::withCount('products')->get();
        $featuredProducts = Product::where('is_featured', true)->take(8)->get();
        if ($featuredProducts->isEmpty()) {
            $featuredProducts = Product::take(8)->get();
        }
        $blogs = Blog::latest()->take(3)->get();
        $exportCountries = ExportCountry::take(8)->get();

        return view('pages.home', compact('company', 'categories', 'featuredProducts', 'blogs', 'exportCountries'));
    }
}
