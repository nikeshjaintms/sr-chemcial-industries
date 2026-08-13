<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Str;

class ProductImageMappingService
{
    /**
     * Get list of candidate product image files from public/assets/img and storage.
     */
    public function getCandidateImages(): array
    {
        $directories = [
            public_path('assets/img/added/product'),
            public_path('assets/img/added/OP'),
            public_path('storage/uploads/products'),
            storage_path('app/public/uploads/products'),
        ];

        $images = [];
        $seenPaths = [];

        foreach ($directories as $dir) {
            if (!file_exists($dir))
                continue;

            $files = glob($dir . '/*');
            foreach ($files as $file) {
                if (is_dir($file))
                    continue;

                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif']))
                    continue;

                $realPath = str_replace('\\', '/', realpath($file) ?: $file);
                if (isset($seenPaths[$realPath]))
                    continue;
                $seenPaths[$realPath] = true;

                $publicRoot = str_replace('\\', '/', public_path());
                $relPath = str_replace($publicRoot . '/', '', $realPath);

                $filename = pathinfo($file, PATHINFO_FILENAME);
                $cleanFilename = str_replace(['-', '_', '.'], ' ', $filename);
                $normFilename = SearchService::normalize($cleanFilename);

                $images[] = [
                    'full_path' => $realPath,
                    'relative_path' => $relPath,
                    'url' => asset($relPath),
                    'filename' => basename($file),
                    'raw_name' => $filename,
                    'norm_name' => $normFilename,
                    'extension' => $ext,
                    'size' => file_exists($realPath) ? filesize($realPath) : 0,
                    'hash' => file_exists($realPath) ? md5_file($realPath) : null,
                ];
            }
        }

        return $images;
    }

    /**
     * Match a single image filename against all live products in database using strict hierarchy.
     */
    public function matchFilenameToProduct(string $filename, ?array $allProducts = null): array
    {
        if (is_null($allProducts)) {
            $allProducts = Product::with('category')->get()->toArray();
        }

        $baseFilename = pathinfo($filename, PATHINFO_FILENAME);
        $cleanFilename = str_replace(['-', '_', '.'], ' ', $baseFilename);
        $normFilename = SearchService::normalize($cleanFilename);
        $rawCleanFilename = SearchService::rawClean($cleanFilename);

        // LEVEL 1 — EXACT MATCH (Name or Base Name)
        foreach ($allProducts as $p) {
            $normName = SearchService::normalize($p['name']);
            $normBaseName = SearchService::normalize(SearchService::getBaseName($p['name']));

            if ($normFilename === $normName || $normFilename === $normBaseName || $rawCleanFilename === SearchService::rawClean($p['name'])) {
                return [
                    'product_id' => $p['id'],
                    'product_name' => $p['name'],
                    'product' => $p,
                    'match_type' => 'exact',
                    'confidence' => 100,
                    'label' => '✅ Exact Match',
                    'candidates' => []
                ];
            }
        }

        // LEVEL 2 — EXACT CHEMICAL NAME MATCH
        foreach ($allProducts as $p) {
            $pChem = $p['chemical_name'] ?? '';
            if (!empty($pChem)) {
                $normChem = SearchService::normalize($pChem);
                if ($normFilename === $normChem) {
                    return [
                        'product_id' => $p['id'],
                        'product_name' => $p['name'],
                        'product' => $p,
                        'match_type' => 'exact_chemical',
                        'confidence' => 95,
                        'label' => '✅ Exact Chemical Match',
                        'candidates' => []
                    ];
                }
            }
        }

        // LEVEL 3 — EXACT SLUG MATCH
        foreach ($allProducts as $p) {
            $pSlug = str_replace('-', ' ', $p['slug'] ?? '');
            if (!empty($pSlug)) {
                $normSlug = SearchService::normalize($pSlug);
                if ($normFilename === $normSlug) {
                    return [
                        'product_id' => $p['id'],
                        'product_name' => $p['name'],
                        'product' => $p,
                        'match_type' => 'exact_slug',
                        'confidence' => 90,
                        'label' => '✅ Exact Slug Match',
                        'candidates' => []
                    ];
                }
            }
        }

        // LEVEL 4 — CAS / HSN NUMBER MATCH
        foreach ($allProducts as $p) {
            $cas = trim($p['cas_number'] ?? '');
            $hsn = trim($p['hsn_code'] ?? '');

            if (!empty($cas) && $cas !== 'N/A' && str_contains($baseFilename, $cas)) {
                return [
                    'product_id' => $p['id'],
                    'product_name' => $p['name'],
                    'product' => $p,
                    'match_type' => 'cas_match',
                    'confidence' => 90,
                    'label' => '✅ CAS Match (' . $cas . ')',
                    'candidates' => []
                ];
            }

            if (!empty($hsn) && $hsn !== 'N/A' && str_contains($baseFilename, $hsn)) {
                return [
                    'product_id' => $p['id'],
                    'product_name' => $p['name'],
                    'product' => $p,
                    'match_type' => 'hsn_match',
                    'confidence' => 90,
                    'label' => '✅ HSN Match (' . $hsn . ')',
                    'candidates' => []
                ];
            }
        }

        // LEVEL 5 — SAFE TOKEN MATCH & AMBIGUITY DETECTION
        $candidateProducts = [];

        if (strlen($normFilename) >= 4) {
            foreach ($allProducts as $p) {
                $normName = SearchService::normalize($p['name']);
                $normBase = SearchService::normalize(SearchService::getBaseName($p['name']));

                if (str_starts_with($normName, $normFilename) || str_starts_with($normBase, $normFilename) || str_starts_with($normFilename, $normBase)) {
                    $candidateProducts[] = $p;
                }
            }
        }

        // AMBIGUITY CHECK: If multiple candidate products match, DO NOT AUTO-ASSIGN!
        if (count($candidateProducts) > 1) {
            return [
                'product_id' => null,
                'product_name' => null,
                'product' => null,
                'match_type' => 'ambiguous',
                'confidence' => 50,
                'label' => '⚠️ Ambiguous (' . count($candidateProducts) . ' possible products)',
                'candidates' => array_map(fn($prod) => ['id' => $prod['id'], 'name' => $prod['name']], $candidateProducts)
            ];
        }

        if (count($candidateProducts) === 1) {
            $singleMatch = $candidateProducts[0];
            return [
                'product_id' => $singleMatch['id'],
                'product_name' => $singleMatch['name'],
                'product' => $singleMatch,
                'match_type' => 'normalized',
                'confidence' => 85,
                'label' => '✓ Normalized Match',
                'candidates' => []
            ];
        }

        return [
            'product_id' => null,
            'product_name' => null,
            'product' => null,
            'match_type' => 'none',
            'confidence' => 0,
            'label' => '❌ No Match',
            'candidates' => []
        ];
    }

    /**
     * Run complete product image audit and return detailed report.
     */
    public function auditProducts(): array
    {
        $products = Product::with('category')->orderBy('name', 'asc')->get();
        $candidateImages = $this->getCandidateImages();

        $placeholderPattern = 'Caustic-Soda-Flakes-NaOH.jpg';

        $report = [
            'total_products' => $products->count(),
            'products_with_images' => 0,
            'products_without_images' => 0,
            'total_candidate_images' => count($candidateImages),
            'matched_products' => 0,
            'placeholder_products' => 0,
            'mapped_items' => [],
            'missing_image_products' => [],
            'unmatched_products' => [],
            'duplicate_images' => [],
        ];

        $assignedImageCount = [];
        $assignedImagesToProducts = [];

        foreach ($products as $p) {
            $hasValidImage = !empty($p->image_url) && !str_contains($p->image_url, $placeholderPattern) && file_exists(public_path($p->image_url));

            if ($hasValidImage) {
                $report['products_with_images']++;
            } else {
                $report['products_without_images']++;
                $report['missing_image_products'][] = [
                    'id' => $p->id,
                    'name' => $p->name,
                    'category' => $p->category ? $p->category->name : 'N/A',
                    'current_url' => $p->image_url,
                ];
            }

            if (!empty($p->image_url)) {
                $assignedImageCount[$p->image_url] = ($assignedImageCount[$p->image_url] ?? 0) + 1;
                $assignedImagesToProducts[$p->image_url][] = $p;
            }

            // Perform image match search for product
            $match = $this->matchFilenameToProduct($p->name, $products->toArray());
            if ($match['match_type'] !== 'none' && !empty($match['product_id'])) {
                $report['matched_products']++;
            }
        }

        // Find duplicate image assignments
        foreach ($assignedImageCount as $url => $count) {
            if ($count > 1 && !str_contains($url, $placeholderPattern)) {
                $report['duplicate_images'][] = [
                    'image_url' => $url,
                    'count' => $count,
                    'products' => array_map(fn($prod) => ['id' => $prod->id, 'name' => $prod->name], $assignedImagesToProducts[$url])
                ];
            }
        }

        return $report;
    }

    /**
     * Auto map and update exact and normalized product images in database.
     */
    public function applyAutoMapping(bool $includeNormalized = true): array
    {
        $products = Product::all();
        $candidateImages = $this->getCandidateImages();

        $updated = 0;
        $skipped = 0;
        $alreadyCorrect = 0;
        $details = [];

        foreach ($products as $p) {
            $pNorm = SearchService::normalize($p->name);
            $pBaseNorm = SearchService::normalize(SearchService::getBaseName($p->name));
            $pSlugNorm = SearchService::normalize(str_replace('-', ' ', $p->slug));

            $matchedImg = null;
            $matchType = 'none';

            foreach ($candidateImages as $img) {
                $iNorm = $img['norm_name'];

                if ($pNorm === $iNorm || $pBaseNorm === $iNorm || $pSlugNorm === $iNorm) {
                    $matchedImg = $img;
                    $matchType = 'exact';
                    break;
                }

                if ($includeNormalized && strlen($iNorm) >= 4) {
                    if (str_contains($pNorm, $iNorm) || str_contains($pBaseNorm, $iNorm) || str_contains($iNorm, $pNorm)) {
                        $matchedImg = $img;
                        $matchType = 'normalized';
                    }
                }
            }

            if ($matchedImg) {
                $newPath = $matchedImg['relative_path'];
                if ($p->image_url === $newPath) {
                    $alreadyCorrect++;
                } else {
                    $p->image_url = $newPath;
                    $p->save();
                    $updated++;
                    $details[] = [
                        'product_id' => $p->id,
                        'name' => $p->name,
                        'new_image' => $newPath,
                        'match_type' => $matchType
                    ];
                }
            } else {
                $skipped++;
            }
        }

        return [
            'updated' => $updated,
            'already_correct' => $alreadyCorrect,
            'skipped' => $skipped,
            'details' => $details
        ];
    }
}
