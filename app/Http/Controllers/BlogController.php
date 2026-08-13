<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index()
    {
        $blogs = Blog::latest()->paginate(9);
        return view('blogs.index', compact('blogs'));
    }

    public function show(string $slug)
    {
        $cleanSlug = str_replace('.php', '', $slug);
        $blog = Blog::where('slug', $cleanSlug)->firstOrFail();
        $recentBlogs = Blog::where('id', '!=', $blog->id)->latest()->take(3)->get();

        return view('blogs.show', compact('blog', 'recentBlogs'));
    }
}
