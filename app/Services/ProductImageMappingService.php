<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Str;

class ProductImageMappingService
{
    /**
     * Normalize filename for product image matching.
     *
     * Rules:
     * - Remove extension (.jpg, .jpeg, .png, .webp, .gif)
     * - Convert to lowercase
     * - Trim leading/trailing spaces
     * - Convert hyphens to spaces
     * - Convert underscores to spaces
     * - Remove unnecessary brackets/special characters
     * - Convert multiple spaces to a single space
     *
     * @param string $filename
     * @return string
     */
    public function normalizeFilename(string $filename): string
    {
        $base = preg_replace('/\.(jpg|jpeg|png|webp|gif)$/i', '', trim($filename));
        if ($base === null) {
            $base = pathinfo($filename, PATHINFO_FILENAME);
        }

        $str = mb_strtolower($base, 'UTF-8');
        $str = str_replace(['-', '_'], ' ', $str);
        $str = preg_replace('~[()\[\]{},.\-\\\\/+&%*#@!$^:;"\']~u', ' ', $str);
        $str = preg_replace('/\s+/', ' ', $str);

        return trim($str);
    }

    /**
     * Clean product string (name, chemical_name, slug).
     *
     * @param string|null $input
     * @return string
     */
    public function normalizeProductString(?string $input): string
    {
        if (empty($input)) {
            return '';
        }

        $str = mb_strtolower(trim($input), 'UTF-8');
        $str = str_replace(['-', '_'], ' ', $str);
        $str = preg_replace('~[()\[\]{},.\-\\\\/+&%*#@!$^:;"\']~u', ' ', $str);
        $str = preg_replace('/\s+/', ' ', $str);

        return trim($str);
    }

    /**
     * Strip parenthetical expressions from product string e.g. "Caustic Soda Flakes (NaOH)" -> "Caustic Soda Flakes"
     *
     * @param string|null $input
     * @return string
     */
    public function getBaseProductString(?string $input): string
    {
        if (empty($input)) {
            return '';
        }

        $base = preg_replace('/\s*\([^)]*\)/u', '', $input);
        return $this->normalizeProductString($base);
    }

    /**
     * Match a given original image filename against products database.
     *
     * @param string $originalFilename
     * @param array|null $allProducts
     * @param string $mode 'skip' or 'replace'
     * @return array
     */
    public function matchFilenameToProduct(string $originalFilename, ?array $allProducts = null, string $mode = 'skip'): array
    {
        if (is_null($allProducts)) {
            $allProducts = Product::with('category')->get()->toArray();
        }

        $normFilename = $this->normalizeFilename($originalFilename);

        if ($normFilename === '') {
            return [
                'filename' => $originalFilename,
                'norm_filename' => '',
                'product_id' => null,
                'product_name' => null,
                'product' => null,
                'status' => 'INVALID FILE',
                'match_type' => 'invalid',
                'confidence' => 0,
                'label' => '❌ Invalid Filename',
                'message' => 'Filename could not be parsed or is empty.',
                'candidates' => [],
            ];
        }

        // Also strip common trailing image keywords like "photo", "image", "pic"
        $keywordsPattern = '/\s+(photo|image|pic|product|main|thumb|thumbnail)$/i';
        $normFilenameStripped = preg_replace($keywordsPattern, '', $normFilename);

        $exactMatches = [];
        $chemicalMatches = [];
        $slugMatches = [];
        $candidateMatches = [];

        foreach ($allProducts as $p) {
            $pName = $p['name'] ?? '';
            $normName = $this->normalizeProductString($pName);
            $baseName = $this->getBaseProductString($pName);

            $pChem = $p['chemical_name'] ?? '';
            $normChem = $this->normalizeProductString($pChem);
            $baseChem = $this->getBaseProductString($pChem);

            $pSlug = $p['slug'] ?? '';
            $normSlug = $this->normalizeProductString($pSlug);

            // Level 1: Exact Name / Base Name Match
            if (
                $normFilename === $normName ||
                $normFilename === $baseName ||
                $normFilenameStripped === $normName ||
                $normFilenameStripped === $baseName
            ) {
                $exactMatches[] = $p;
                continue;
            }

            // Level 2: Exact Chemical Name / Base Chemical Match
            if (
                (!empty($normChem) && ($normFilename === $normChem || $normFilenameStripped === $normChem)) ||
                (!empty($baseChem) && ($normFilename === $baseChem || $normFilenameStripped === $baseChem))
            ) {
                $chemicalMatches[] = $p;
                continue;
            }

            // Level 3: Exact Slug Match
            if (!empty($normSlug) && ($normFilename === $normSlug || $normFilenameStripped === $normSlug)) {
                $slugMatches[] = $p;
                continue;
            }

            // Substring candidate search (for ambiguity detection)
            $targetStr = $baseName !== '' ? $baseName : $normName;
            if ($targetStr !== '' && (str_contains($targetStr, $normFilename) || str_contains($normFilename, $targetStr))) {
                $candidateMatches[] = $p;
            }
        }

        // Priority 1: Exact Name Match
        if (count($exactMatches) === 1) {
            return $this->buildResult($originalFilename, $normFilename, $exactMatches[0], 'exact_name', $mode);
        }

        if (count($exactMatches) > 1) {
            $names = array_values(array_unique(array_map(fn($p) => $p['name'], $exactMatches)));
            if (count($names) === 1) {
                return $this->buildResult($originalFilename, $normFilename, $exactMatches[0], 'exact_name', $mode);
            }
            return [
                'filename' => $originalFilename,
                'norm_filename' => $normFilename,
                'product_id' => null,
                'product_name' => null,
                'product' => null,
                'status' => 'AMBIGUOUS',
                'match_type' => 'ambiguous',
                'confidence' => 0,
                'label' => '⚠️ Ambiguous Match',
                'message' => 'Multiple products match exact name: ' . implode(', ', $names),
                'candidates' => $names,
            ];
        }

        // Priority 2: Exact Chemical Name Match
        if (count($chemicalMatches) === 1) {
            return $this->buildResult($originalFilename, $normFilename, $chemicalMatches[0], 'exact_chemical', $mode);
        }

        if (count($chemicalMatches) > 1) {
            $names = array_values(array_unique(array_map(fn($p) => $p['name'], $chemicalMatches)));
            if (count($names) === 1) {
                return $this->buildResult($originalFilename, $normFilename, $chemicalMatches[0], 'exact_chemical', $mode);
            }
            return [
                'filename' => $originalFilename,
                'norm_filename' => $normFilename,
                'product_id' => null,
                'product_name' => null,
                'product' => null,
                'status' => 'AMBIGUOUS',
                'match_type' => 'ambiguous',
                'confidence' => 0,
                'label' => '⚠️ Ambiguous Match',
                'message' => 'Multiple products match chemical name: ' . implode(', ', $names),
                'candidates' => $names,
            ];
        }

        // Priority 3: Exact Slug Match
        if (count($slugMatches) === 1) {
            return $this->buildResult($originalFilename, $normFilename, $slugMatches[0], 'exact_slug', $mode);
        }

        if (count($slugMatches) > 1) {
            $names = array_values(array_unique(array_map(fn($p) => $p['name'], $slugMatches)));
            if (count($names) === 1) {
                return $this->buildResult($originalFilename, $normFilename, $slugMatches[0], 'exact_slug', $mode);
            }
            return [
                'filename' => $originalFilename,
                'norm_filename' => $normFilename,
                'product_id' => null,
                'product_name' => null,
                'product' => null,
                'status' => 'AMBIGUOUS',
                'match_type' => 'ambiguous',
                'confidence' => 0,
                'label' => '⚠️ Ambiguous Match',
                'message' => 'Multiple products match slug: ' . implode(', ', $names),
                'candidates' => $names,
            ];
        }

        // Candidate / Partial Substring Match Handling (Ambiguous vs Unique Candidate)
        if (count($candidateMatches) > 1) {
            $names = array_values(array_unique(array_map(fn($p) => $p['name'], $candidateMatches)));
            if (count($names) === 1) {
                return $this->buildResult($originalFilename, $normFilename, $candidateMatches[0], 'unique_candidate', $mode);
            }
            return [
                'filename' => $originalFilename,
                'norm_filename' => $normFilename,
                'product_id' => null,
                'product_name' => null,
                'product' => null,
                'status' => 'AMBIGUOUS',
                'match_type' => 'ambiguous',
                'confidence' => 0,
                'label' => '⚠️ Ambiguous Match',
                'message' => 'Multiple candidate products found: ' . implode(', ', array_slice($names, 0, 5)),
                'candidates' => $names,
            ];
        }

        return [
            'filename' => $originalFilename,
            'norm_filename' => $normFilename,
            'product_id' => null,
            'product_name' => null,
            'product' => null,
            'status' => 'NOT FOUND',
            'match_type' => 'not_found',
            'confidence' => 0,
            'label' => '❌ Not Found',
            'message' => 'No matching product found in database.',
            'candidates' => [],
        ];
    }

    /**
     * Build result array for matched product.
     */
    private function buildResult(
        string $filename,
        string $normFilename,
        array $product,
        string $matchType,
        string $mode
    ): array {
        $placeholderPattern = 'Caustic-Soda-Flakes-NaOH.jpg';
        $currentUrl = $product['image_url'] ?? '';

        $hasExisting = !empty($currentUrl) &&
            !str_contains($currentUrl, $placeholderPattern) &&
            $currentUrl !== '#' &&
            trim($currentUrl) !== '';

        if ($hasExisting && $mode === 'skip') {
            return [
                'filename' => $filename,
                'norm_filename' => $normFilename,
                'product_id' => $product['id'],
                'product_name' => $product['name'],
                'product' => $product,
                'status' => 'ALREADY EXISTS',
                'match_type' => $matchType,
                'confidence' => 100,
                'label' => '⏭️ Already Exists',
                'message' => "Product '{$product['name']}' already has an assigned image. Skipped.",
                'candidates' => [],
                'has_existing' => true,
                'existing_url' => $currentUrl,
            ];
        }

        $msg = $hasExisting
            ? "Product '{$product['name']}' matched. Existing image will be replaced."
            : "Product '{$product['name']}' matched successfully.";

        return [
            'filename' => $filename,
            'norm_filename' => $normFilename,
            'product_id' => $product['id'],
            'product_name' => $product['name'],
            'product' => $product,
            'status' => 'MATCHED',
            'match_type' => $matchType,
            'confidence' => 100,
            'label' => '✅ Exact Match',
            'message' => $msg,
            'candidates' => [],
            'has_existing' => $hasExisting,
            'existing_url' => $currentUrl,
        ];
    }

    /**
     * Candidate images helper for Media Library view.
     * Scans ONLY the canonical product image directory: public/assets/products.
     */
    public function getCandidateImages(): array
    {
        $targetDir = public_path('assets/products');
        if (!file_exists($targetDir)) {
            @mkdir($targetDir, 0755, true);
        }

        $images = [];
        $seenPaths = [];

        if (file_exists($targetDir) && is_dir($targetDir)) {
            $files = glob($targetDir . '/*');
            foreach ($files as $file) {
                if (is_dir($file)) continue;

                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) continue;

                $realPath = str_replace('\\', '/', realpath($file) ?: $file);
                if (isset($seenPaths[$realPath])) continue;
                $seenPaths[$realPath] = true;

                $fileName = basename($file);
                $relPath = 'assets/products/' . $fileName;
                $normFilename = $this->normalizeFilename($fileName);
                $rawFilename = pathinfo($fileName, PATHINFO_FILENAME);

                $images[] = [
                    'full_path' => $realPath,
                    'relative_path' => $relPath,
                    'url' => asset($relPath),
                    'filename' => $fileName,
                    'raw_name' => $rawFilename,
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
     * Audit products to check images count and missing images.
     */
    public function auditProducts(): array
    {
        $products = Product::all();

        $total = $products->count();
        $assigned = 0;
        $withoutImages = [];

        foreach ($products as $p) {
            $raw = $p->getRawOriginal('image_url');
            $hasValidImage = !empty($raw) && $raw !== '#' && trim($raw) !== '';

            if ($hasValidImage) {
                $assigned++;
            } else {
                $withoutImages[] = $p;
            }
        }

        return [
            'total' => $total,
            'assigned' => $assigned,
            'without_images_count' => count($withoutImages),
            'without_images' => $withoutImages,
        ];
    }
}
