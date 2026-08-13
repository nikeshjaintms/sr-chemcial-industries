<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Str;

class HierarchyParserService
{
    /**
     * Parse raw text containing ASCII tree structure (├──, └──, │, spaces)
     * and return a structured array of category and product nodes.
     */
    public function parseText(string $rawText): array
    {
        $lines = explode("\n", str_replace(["\r\n", "\r"], "\n", $rawText));
        $rawNodes = [];

        foreach ($lines as $lineIndex => $line) {
            $trimmed = trim($line);
            if ($trimmed === '' || $trimmed === '│') {
                continue;
            }

            // Standardize ASCII branch characters to fixed-width spaces for indent counting
            $cleanLine = str_replace(["├──", "└──", "+--", "\\--"], "    ", $line);
            $cleanLine = str_replace("│", " ", $cleanLine);

            // Calculate indentation depth (4 spaces = 1 level)
            $leadingSpaces = strlen($cleanLine) - strlen(ltrim($cleanLine));
            $depth = (int) floor($leadingSpaces / 4);

            // Clean the node label (strip leftover dashes, pipes, ASCII tree characters)
            $label = trim(preg_replace('~^[|\+\-\\\/\s]+~u', '', $trimmed));
            $label = trim(preg_replace('~^[├└│─\s]+~u', '', $label));

            if ($label === '' || strtolower($label) === 'products' || strtolower($label) === 'root') {
                continue;
            }

            $rawNodes[] = [
                'line_index' => $lineIndex,
                'depth' => $depth,
                'name' => $label,
            ];
        }

        if (empty($rawNodes)) {
            return [];
        }

        // Normalize depths so root nodes start at depth 0
        $minDepth = min(array_column($rawNodes, 'depth'));
        foreach ($rawNodes as &$node) {
            $node['depth'] = $node['depth'] - $minDepth;
        }
        unset($node);

        $nodesCount = count($rawNodes);
        $tree = [];
        $stack = []; // depth => node_name

        for ($i = 0; $i < $nodesCount; $i++) {
            $currentNode = $rawNodes[$i];
            $depth = $currentNode['depth'];
            $name = $currentNode['name'];

            $stack[$depth] = $name;
            // Clear any deeper stack items
            foreach (array_keys($stack) as $d) {
                if ($d > $depth) {
                    unset($stack[$d]);
                }
            }

            // Parent name is the item in stack at depth - 1
            $parentName = ($depth > 0 && isset($stack[$depth - 1])) ? $stack[$depth - 1] : null;

            // Check if next node has greater depth (which means this current node is a Category)
            $isCategory = false;
            if ($i < $nodesCount - 1) {
                $nextNode = $rawNodes[$i + 1];
                if ($nextNode['depth'] > $depth) {
                    $isCategory = true;
                }
            }

            // Build full path
            $currentStackPath = array_slice($stack, 0, $depth + 1);

            $tree[] = [
                'name' => $name,
                'depth' => $depth,
                'type' => $isCategory ? 'category' : 'product',
                'parent_name' => $parentName,
                'path' => implode(' > ', $currentStackPath),
            ];
        }

        return $tree;
    }

    /**
     * Process parsed tree array and import into database with duplicate protection.
     */
    public function processImport(array $parsedTree): array
    {
        $categoriesCreated = 0;
        $categoriesReused = 0;
        $productsCreated = 0;
        $productsUpdated = 0;

        // Map path string => Category model instance
        $categoryMap = [];

        foreach ($parsedTree as $item) {
            $name = $item['name'];
            $type = $item['type'];
            $path = $item['path'];

            if ($type === 'category') {
                // Find parent category if path has parent
                $pathSegments = explode(' > ', $path);
                $parentPath = count($pathSegments) > 1 ? implode(' > ', array_slice($pathSegments, 0, -1)) : null;
                $parentCategory = $parentPath && isset($categoryMap[$parentPath]) ? $categoryMap[$parentPath] : null;
                $parentId = $parentCategory ? $parentCategory->id : null;

                // Check existing category by name + parent_id
                $existing = Category::where(function ($q) use ($name) {
                    $q->where('name', 'LIKE', $name)->orWhere('name', $name);
                })
                ->where(function ($q) use ($parentId) {
                    if (is_null($parentId)) {
                        $q->whereNull('parent_id');
                    } else {
                        $q->where('parent_id', $parentId);
                    }
                })
                ->first();

                if ($existing) {
                    $categoryMap[$path] = $existing;
                    $categoriesReused++;
                } else {
                    $baseSlug = Str::slug($name);
                    $slug = $baseSlug;
                    $counter = 1;
                    while (Category::where('slug', $slug)->exists()) {
                        $slug = $baseSlug . '-' . $counter;
                        $counter++;
                    }

                    $newCat = Category::create([
                        'name' => $name,
                        'slug' => $slug,
                        'parent_id' => $parentId,
                        'type' => 'Industrial Chemicals',
                        'description' => $name . ' category',
                        'sort_order' => 0,
                        'status' => true,
                    ]);

                    $categoryMap[$path] = $newCat;
                    $categoriesCreated++;
                }
            } elseif ($type === 'product') {
                // Product belongs to parent category
                $pathSegments = explode(' > ', $path);
                $parentPath = count($pathSegments) > 1 ? implode(' > ', array_slice($pathSegments, 0, -1)) : null;
                $parentCategory = $parentPath && isset($categoryMap[$parentPath]) ? $categoryMap[$parentPath] : null;
                $categoryId = $parentCategory ? $parentCategory->id : null;

                // Check existing product by name + category_id
                $existingProduct = Product::where('name', $name)->first();

                if ($existingProduct) {
                    if ($categoryId && $existingProduct->category_id !== $categoryId) {
                        $existingProduct->update(['category_id' => $categoryId]);
                        if ($categoryId) {
                            $existingProduct->categories()->sync([$categoryId]);
                        }
                    }
                    $productsUpdated++;
                } else {
                    $baseSlug = Str::slug($name);
                    $slug = $baseSlug;
                    $counter = 1;
                    while (Product::where('slug', $slug)->exists()) {
                        $slug = $baseSlug . '-' . $counter;
                        $counter++;
                    }

                    $product = Product::create([
                        'name' => $name,
                        'slug' => $slug,
                        'brand' => 'SRCIL Standard',
                        'chemical_name' => $name,
                        'cas_number' => 'N/A',
                        'hsn_code' => 'N/A',
                        'purity' => 'Technical Grade High Purity',
                        'packaging' => 'Standard Industrial Packaging',
                        'description' => 'High purity ' . $name . ' supplied by SR Chemical Industries Limited.',
                        'features' => [
                            'High Purity Verified Grade',
                            'Reliable Industrial Supply Chain',
                            'Fast Dispatch & Delivery Across India & Export'
                        ],
                        'applications' => [
                            'Industrial Chemical Manufacturing',
                            'Raw Material Processing'
                        ],
                        'specifications' => [
                            'Quality Grade' => 'Verified Pure Grade'
                        ],
                        'category_id' => $categoryId,
                        'image_url' => 'assets/img/added/product/Caustic-Soda-Flakes-NaOH.jpg',
                        'msds_url' => '#',
                        'specification_url' => route('products.show', $slug),
                        'product_url' => $slug . '.php',
                        'is_featured' => false,
                        'sort_order' => 0,
                        'status' => true,
                    ]);

                    if ($categoryId) {
                        $product->categories()->sync([$categoryId]);
                    }

                    $productsCreated++;
                }
            }
        }

        return [
            'categories_created' => $categoriesCreated,
            'categories_reused' => $categoriesReused,
            'products_created' => $productsCreated,
            'products_updated' => $productsUpdated,
        ];
    }
}
