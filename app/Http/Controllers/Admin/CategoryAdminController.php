<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryAdminController extends Controller
{
    public function index()
    {
        $rootCategories = Category::whereNull('parent_id')
            ->with(['allChildren.allChildren.allChildren', 'allProducts'])
            ->orderBy('sort_order', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        $allCategories = Category::with('parent')
            ->withCount('allProducts')
            ->orderBy('sort_order', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        return view('admin.categories.index', compact('rootCategories', 'allCategories'));
    }

    public function create(Request $request)
    {
        $parentId = $request->query('parent_id');
        $parentCategory = $parentId ? Category::find($parentId) : null;
        $categories = Category::orderBy('name', 'asc')->get();

        return view('admin.categories.create', compact('categories', 'parentId', 'parentCategory'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug',
            'parent_id' => 'nullable|exists:categories,id',
            'type' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'status' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
        ]);

        $imageUrl = 'assets/img/added/product/Caustic-Soda-Flakes-NaOH.jpg';
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('uploads/categories', 'public');
            $imageUrl = 'storage/' . $path;
        }

        $slug = !empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['name']);
        $counter = 1;
        $originalSlug = $slug;
        while (Category::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        Category::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'parent_id' => $validated['parent_id'] ?? null,
            'type' => $validated['type'] ?? 'Industrial Chemicals',
            'description' => $validated['description'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'status' => $request->has('status') ? $request->boolean('status') : true,
            'image_url' => $imageUrl,
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Category created successfully!');
    }

    public function edit(Category $category)
    {
        // Exclude self and descendants to prevent circular reference
        $allCategories = Category::where('id', '!=', $category->id)->orderBy('name', 'asc')->get();
        $categories = $allCategories->reject(function ($c) use ($category) {
            $ancestors = $c->ancestors;
            return collect($ancestors)->pluck('id')->contains($category->id);
        });

        return view('admin.categories.edit', compact('category', 'categories'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories,slug,' . $category->id,
            'parent_id' => 'nullable|exists:categories,id|different:id',
            'type' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'status' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
        ]);

        $imageUrl = $category->image_url;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('uploads/categories', 'public');
            $imageUrl = 'storage/' . $path;
        }

        $category->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['slug']),
            'parent_id' => $validated['parent_id'] ?? null,
            'type' => $validated['type'] ?? 'Industrial Chemicals',
            'description' => $validated['description'],
            'sort_order' => $validated['sort_order'] ?? 0,
            'status' => $request->has('status') ? $request->boolean('status') : true,
            'image_url' => $imageUrl,
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Category updated successfully!');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('admin.categories.index')->with('success', 'Category deleted successfully!');
    }
}

