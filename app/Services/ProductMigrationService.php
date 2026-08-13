<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Str;
use PDO;

class ProductMigrationService
{
    protected string $sourceBasePath = 'C:/xampp/htdocs/SR';
    protected string $targetBasePath = 'd:/nehal/SR-Laravel';

    public function migrate(): array
    {
        $dbPath = $this->sourceBasePath . '/database/database.sqlite';
        if (!file_exists($dbPath)) {
            throw new \RuntimeException("Core PHP source database not found at {$dbPath}");
        }

        $pdo = new PDO("sqlite:" . $dbPath);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // 1. Fetch Categories from Core PHP DB
        $sourceCategories = $pdo->query("SELECT * FROM categories ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);

        $categoryMap = []; // core_id => laravel_cat_id
        $categoriesCreated = 0;
        $categoriesUpdated = 0;

        // Process categories hierarchically (roots first, then subcategories)
        $pendingCategories = $sourceCategories;
        $maxPasses = 5;

        for ($pass = 0; $pass < $maxPasses && !empty($pendingCategories); $pass++) {
            $nextPending = [];

            foreach ($pendingCategories as $sc) {
                $sourceId = $sc['id'];
                $sourceParentId = $sc['parent_id'] ?? null;

                $targetParentId = null;
                if (!empty($sourceParentId)) {
                    if (isset($categoryMap[$sourceParentId])) {
                        $targetParentId = $categoryMap[$sourceParentId];
                    } else {
                        // Parent not processed yet in this pass
                        $nextPending[] = $sc;
                        continue;
                    }
                }

                $name = trim($sc['name']);
                $slug = !empty($sc['slug']) ? Str::slug($sc['slug']) : Str::slug($name);

                // Find existing category by slug or name+parent_id
                $category = Category::where('slug', $slug)
                    ->orWhere(function ($q) use ($name, $targetParentId) {
                        $q->where('name', $name);
                        if ($targetParentId) {
                            $q->where('parent_id', $targetParentId);
                        } else {
                            $q->whereNull('parent_id');
                        }
                    })->first();

                $categoryData = [
                    'name' => $name,
                    'slug' => $slug,
                    'parent_id' => $targetParentId,
                    'type' => !empty($sc['type']) ? trim($sc['type']) : 'Industrial Chemicals',
                    'description' => !empty($sc['description']) ? trim($sc['description']) : "{$name} category",
                    'status' => true,
                ];

                if ($category) {
                    $category->update($categoryData);
                    $categoriesUpdated++;
                } else {
                    $category = Category::create($categoryData);
                    $categoriesCreated++;
                }

                $categoryMap[$sourceId] = $category->id;
            }

            if (count($nextPending) === count($pendingCategories)) {
                // Handle remaining orphaned categories without parent
                foreach ($nextPending as $orphan) {
                    $name = trim($orphan['name']);
                    $slug = !empty($orphan['slug']) ? Str::slug($orphan['slug']) : Str::slug($name);
                    $category = Category::firstOrCreate(
                        ['slug' => $slug],
                        [
                            'name' => $name,
                            'parent_id' => null,
                            'type' => 'Industrial Chemicals',
                            'description' => "{$name} category",
                            'status' => true
                        ]
                    );
                    $categoryMap[$orphan['id']] = $category->id;
                }
                break;
            }

            $pendingCategories = $nextPending;
        }

        // 2. Fetch Products from Core PHP DB
        $sourceProducts = $pdo->query("SELECT * FROM products ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);

        $report = [
            'categories_created' => $categoriesCreated,
            'categories_updated' => $categoriesUpdated,
            'total_source' => count($sourceProducts),
            'imported' => 0,
            'updated' => 0,
            'duplicates_prevented' => 0,
            'images_found' => 0,
            'images_copied' => 0,
            'images_missing' => 0,
            'pdfs_found' => 0,
            'pdfs_copied' => 0,
            'pdfs_missing' => 0,
            'complete_details' => 0,
            'missing_details' => 0,
            'failed' => 0,
            'missing_asset_products' => [],
            'missing_detail_products' => [],
        ];

        // Gather existing asset files for fallback matching
        $sourceImgFiles = glob($this->sourceBasePath . '/assets/img/added/product/*.*') ?: [];
        $sourceOpImgFiles = glob($this->sourceBasePath . '/assets/img/added/OP/*.*') ?: [];
        $allSourceImgFiles = array_merge($sourceImgFiles, $sourceOpImgFiles);

        $sourcePdfFiles = glob($this->sourceBasePath . '/assets/pdf/MSDC/*.*') ?: [];

        // Prepare Laravel public destination directories
        $targetImgDir = public_path('assets/img/added/product');
        $targetOpImgDir = public_path('assets/img/added/OP');
        $targetPdfDir = public_path('assets/pdf/MSDC');

        if (!file_exists($targetImgDir)) {
            mkdir($targetImgDir, 0755, true);
        }
        if (!file_exists($targetOpImgDir)) {
            mkdir($targetOpImgDir, 0755, true);
        }
        if (!file_exists($targetPdfDir)) {
            mkdir($targetPdfDir, 0755, true);
        }

        $processedSlugs = [];

        foreach ($sourceProducts as $sp) {
            try {
                $name = trim($sp['name']);
                $slug = !empty($sp['slug']) ? Str::slug($sp['slug']) : Str::slug($name);

                if (isset($processedSlugs[$slug])) {
                    $report['duplicates_prevented']++;
                    $slug = $slug . '-' . $sp['id'];
                }
                $processedSlugs[$slug] = true;

                // Image migration logic
                $finalImageUrl = null;
                $hasImage = false;

                if (!empty($sp['image_url'])) {
                    $report['images_found']++;
                    $relImgPath = str_replace('\\', '/', $sp['image_url']);
                    $srcImgFullPath = $this->sourceBasePath . '/' . ltrim($relImgPath, '/');

                    $matchedSrcImg = null;
                    if (file_exists($srcImgFullPath)) {
                        $matchedSrcImg = $srcImgFullPath;
                    } else {
                        // Fallback fuzzy search by basename
                        $baseName = basename($relImgPath);
                        foreach ($allSourceImgFiles as $f) {
                            if (strcasecmp(basename($f), $baseName) === 0 || strcasecmp(pathinfo($f, PATHINFO_FILENAME), pathinfo($baseName, PATHINFO_FILENAME)) === 0) {
                                $matchedSrcImg = $f;
                                break;
                            }
                        }
                    }

                    if ($matchedSrcImg && file_exists($matchedSrcImg)) {
                        $imgBase = basename($matchedSrcImg);
                        $subFolder = str_contains($matchedSrcImg, '/OP/') || str_contains($matchedSrcImg, '\\OP\\') ? 'OP' : 'product';
                        $destImgPath = public_path("assets/img/added/{$subFolder}/" . $imgBase);

                        copy($matchedSrcImg, $destImgPath);
                        $finalImageUrl = "assets/img/added/{$subFolder}/" . $imgBase;
                        $report['images_copied']++;
                        $hasImage = true;
                    } else {
                        $report['images_missing']++;
                        $report['missing_asset_products'][] = "{$name} (Missing Image: {$sp['image_url']})";
                    }
                } else {
                    $report['images_missing']++;
                }

                // PDF / MSDS migration logic
                $finalMsdsUrl = null;
                $hasPdf = false;

                if (!empty($sp['msds_url']) && $sp['msds_url'] !== '#' && $sp['msds_url'] !== 'contact.php') {
                    $report['pdfs_found']++;
                    $relPdfPath = str_replace('\\', '/', $sp['msds_url']);
                    $srcPdfFullPath = $this->sourceBasePath . '/' . ltrim($relPdfPath, '/');

                    $matchedSrcPdf = null;
                    if (file_exists($srcPdfFullPath)) {
                        $matchedSrcPdf = $srcPdfFullPath;
                    } else {
                        // Fallback fuzzy search by basename
                        $basePdfName = basename($relPdfPath);
                        foreach ($sourcePdfFiles as $f) {
                            if (strcasecmp(basename($f), $basePdfName) === 0 || strcasecmp(pathinfo($f, PATHINFO_FILENAME), pathinfo($basePdfName, PATHINFO_FILENAME)) === 0) {
                                $matchedSrcPdf = $f;
                                break;
                            }
                        }
                    }

                    if ($matchedSrcPdf && file_exists($matchedSrcPdf)) {
                        $pdfBase = basename($matchedSrcPdf);
                        $destPdfPath = public_path("assets/pdf/MSDC/" . $pdfBase);

                        copy($matchedSrcPdf, $destPdfPath);
                        $finalMsdsUrl = "assets/pdf/MSDC/" . $pdfBase;
                        $report['pdfs_copied']++;
                        $hasPdf = true;
                    } else {
                        $report['pdfs_missing']++;
                        $report['missing_asset_products'][] = "{$name} (Missing PDF: {$sp['msds_url']})";
                    }
                } else {
                    $report['pdfs_missing']++;
                }

                // Check completeness of core product fields
                $isComplete = !empty($sp['description']) && !empty($sp['cas_number']) && !empty($sp['hsn_code']) && !empty($sp['purity']);
                if ($isComplete) {
                    $report['complete_details']++;
                } else {
                    $report['missing_details']++;
                    $report['missing_detail_products'][] = $name;
                }

                // Category linking
                $sourceCatId = $sp['category_id'] ?? null;
                $targetCatId = ($sourceCatId && isset($categoryMap[$sourceCatId])) ? $categoryMap[$sourceCatId] : null;

                $productData = [
                    'name' => $name,
                    'slug' => $slug,
                    'brand' => !empty($sp['brand']) ? trim($sp['brand']) : null,
                    'chemical_name' => !empty($sp['chemical_name']) ? trim($sp['chemical_name']) : null,
                    'cas_number' => !empty($sp['cas_number']) ? trim($sp['cas_number']) : null,
                    'hsn_code' => !empty($sp['hsn_code']) ? trim($sp['hsn_code']) : null,
                    'purity' => !empty($sp['purity']) ? trim($sp['purity']) : null,
                    'packaging' => !empty($sp['packaging']) ? trim($sp['packaging']) : null,
                    'description' => !empty($sp['description']) ? trim($sp['description']) : "High purity {$name} supplied by SR Chemical Industries Limited.",
                    'features' => !empty($sp['features']) ? trim($sp['features']) : null,
                    'applications' => !empty($sp['applications']) ? trim($sp['applications']) : null,
                    'specifications' => !empty($sp['specifications']) ? trim($sp['specifications']) : null,
                    'storage_info' => !empty($sp['storage_info']) ? trim($sp['storage_info']) : null,
                    'category_id' => $targetCatId,
                    'image_url' => $finalImageUrl,
                    'msds_url' => $finalMsdsUrl,
                    'specification_url' => !empty($sp['specification_url']) ? trim($sp['specification_url']) : null,
                    'product_url' => !empty($sp['product_url']) ? trim($sp['product_url']) : route('products.show', $slug, false),
                    'is_featured' => !empty($sp['is_featured']) ? (bool)$sp['is_featured'] : false,
                    'status' => true,
                ];

                $existing = Product::where('slug', $slug)->first();
                if ($existing) {
                    $existing->update($productData);
                    $productObj = $existing;
                    $report['updated']++;
                } else {
                    $productObj = Product::create($productData);
                    $report['imported']++;
                }

                if ($targetCatId && $productObj) {
                    $productObj->categories()->sync([$targetCatId]);
                }

            } catch (\Exception $e) {
                $report['failed']++;
                $report['missing_asset_products'][] = "FAILED: {$sp['name']} ({$e->getMessage()})";
            }
        }

        // Trigger automatic hierarchy path sync
        try {
            $pathSync = new ProductPathSyncService();
            $pathSync->sync();
        } catch (\Throwable $e) {
            // Path sync fallback warning
        }

        $report['source_products_count'] = $report['total_source'];
        $report['imported_products_count'] = $report['imported'];
        $report['mapped_products_count'] = $report['updated'];
        $report['imported_images_count'] = $report['images_copied'];
        $report['imported_pdfs_count'] = $report['pdfs_copied'];

        return $report;
    }
}
