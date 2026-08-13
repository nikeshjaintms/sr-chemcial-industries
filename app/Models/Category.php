<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $table = 'categories';

    protected $fillable = [
        'name',
        'slug',
        'parent_id',
        'type',
        'description',
        'image_url',
        'sort_order',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean',
        'sort_order' => 'integer'
    ];

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this
            ->hasMany(Category::class, 'parent_id')
            ->where('status', true)
            ->orderBy('sort_order', 'asc')
            ->orderBy('name', 'asc');
    }

    public function allChildren()
    {
        return $this
            ->hasMany(Category::class, 'parent_id')
            ->orderBy('sort_order', 'asc')
            ->orderBy('name', 'asc');
    }

    public function recursiveChildren()
    {
        return $this->allChildren()->with('recursiveChildren', 'allProducts');
    }

    public function products()
    {
        return $this
            ->hasMany(Product::class, 'category_id')
            ->where('status', true)
            ->orderBy('sort_order', 'asc')
            ->orderBy('name', 'asc');
    }

    public function allProducts()
    {
        return $this
            ->hasMany(Product::class, 'category_id')
            ->orderBy('sort_order', 'asc')
            ->orderBy('name', 'asc');
    }

    public function pivotProducts()
    {
        return $this->belongsToMany(Product::class, 'category_product')->orderByPivot('id', 'asc');
    }

    /**
     * Get all products recursively (this category + subcategories), including pivot relationships
     */
    public function getAllProductsRecursive()
    {
        $direct = $this->allProducts()->where('status', true)->get();
        $pivot = $this->pivotProducts()->where('status', true)->get();
        $products = collect($direct)->merge($pivot);

        foreach ($this->children as $child) {
            $products = $products->merge($child->getAllProductsRecursive());
        }

        return $products->unique('id')->sortBy('sort_order')->values();
    }

    /**
     * Get ancestor category array from root down to this category.
     */
    public function getAncestorsAttribute(): array
    {
        $ancestors = [];
        $current = $this;

        while ($current) {
            array_unshift($ancestors, $current);
            $current = $current->parent;
        }

        return $ancestors;
    }

    /**
     * Get full breadcrumb path string e.g. "Products > GACL Products > Acid Products"
     */
    public function getPathAttribute(): string
    {
        $names = array_map(fn($cat) => $cat->name, $this->ancestors);
        return 'Products > ' . implode(' > ', $names);
    }

    /**
     * Get clean URL for category
     */
    public function getUrlAttribute(): string
    {
        $slugs = array_map(fn($cat) => $cat->slug, $this->ancestors);
        return url('/c/' . implode('/', $slugs));
    }

    /**
     * Find existing category hierarchy path or create missing nodes.
     * Reuses existing categories with matching name + parent_id without creating duplicates.
     */
    public static function findOrCreatePath(string $pathString, ?string $productName = null): Category
    {
        $cleanPath = trim($pathString);
        if (empty($cleanPath)) {
            throw new \InvalidArgumentException('Category path cannot be empty');
        }

        $rawSegments = preg_split('/[>\/]+/', $cleanPath);
        $segments = [];
        foreach ($rawSegments as $seg) {
            $trimmed = trim($seg);
            if ($trimmed !== '' && strtolower($trimmed) !== 'products' && strtolower($trimmed) !== 'root') {
                $segments[] = $trimmed;
            }
        }

        if ($productName && !empty($segments) && strtolower(end($segments)) === strtolower(trim($productName))) {
            array_pop($segments);
        }

        if (empty($segments)) {
            // Fallback to default General Category
            return static::firstOrCreate(
                ['slug' => 'general-products'],
                [
                    'name' => 'General Products',
                    'type' => 'Industrial Chemicals',
                    'description' => 'General Products Category',
                    'sort_order' => 0,
                    'status' => true
                ]
            );
        }

        $currentParentId = null;
        $category = null;

        $rootOrderMap = [
            'gacl products' => 1,
            'organic products' => 2,
            'dmcc products' => 3,
            'gnfc products' => 4,
            'industrial solvents & commodities' => 5,
            'industrial solvents and commodities' => 5
        ];

        foreach ($segments as $segmentName) {
            $existing = static::where(function ($q) use ($segmentName) {
                $q
                    ->where('name', 'LIKE', $segmentName)
                    ->orWhere('name', $segmentName);
            })
                ->where(function ($q) use ($currentParentId) {
                    if (is_null($currentParentId)) {
                        $q->whereNull('parent_id');
                    } else {
                        $q->where('parent_id', $currentParentId);
                    }
                })
                ->first();

            $sortOrder = 0;
            if (is_null($currentParentId)) {
                $sortOrder = $rootOrderMap[strtolower(trim($segmentName))] ?? 0;
            }

            if ($existing) {
                $category = $existing;
                if (is_null($currentParentId) && $sortOrder > 0 && $category->sort_order !== $sortOrder) {
                    $category->update(['sort_order' => $sortOrder]);
                }
            } else {
                $baseSlug = Str::slug($segmentName);
                $slug = $baseSlug;
                $counter = 1;
                while (static::where('slug', $slug)->exists()) {
                    $slug = $baseSlug . '-' . $counter;
                    $counter++;
                }

                $category = static::create([
                    'name' => $segmentName,
                    'slug' => $slug,
                    'parent_id' => $currentParentId,
                    'type' => 'Industrial Chemicals',
                    'description' => $segmentName . ' category',
                    'sort_order' => $sortOrder,
                    'status' => true,
                ]);
            }

            $currentParentId = $category->id;
        }

        return $category;
    }
}
