<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class BulkPdfMatchingService
{
    /**
     * Normalize a PDF filename for comparison.
     *
     * Rules:
     * - Remove .pdf extension
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
        // 1. Remove extension case-insensitively
        $base = preg_replace('/\.pdf$/i', '', trim($filename));
        if ($base === null) {
            $base = pathinfo($filename, PATHINFO_FILENAME);
        }

        // 2. Lowercase
        $str = mb_strtolower($base, 'UTF-8');

        // 3. Convert hyphens and underscores to spaces
        $str = str_replace(['-', '_'], ' ', $str);

        // 4. Remove brackets and special characters
        $str = preg_replace('~[()\[\]{},.\-\\\\/+&%*#@!$^:;"\']~u', ' ', $str);

        // 5. Convert multiple spaces into one space and trim
        $str = preg_replace('/\s+/', ' ', $str);

        return trim($str);
    }

    /**
     * Clean product string (name, chemical_name, or slug) in the same way as filenames.
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
     * Get base product name by stripping parenthetical expressions e.g. "Hydrochloric Acid (HCl)" -> "Hydrochloric Acid"
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
     * Match a given filename against the existing products collection.
     *
     * @param string $filename
     * @param Collection $allProducts
     * @param string $pdfType 'msds' or 'specification'
     * @param string $mode 'skip' or 'replace'
     * @return array
     */
    public function matchFilenameToProduct(
        string $filename,
        Collection $allProducts,
        string $pdfType = 'msds',
        string $mode = 'skip'
    ): array {
        $normFilename = $this->normalizeFilename($filename);

        if ($normFilename === '') {
            return [
                'filename' => $filename,
                'norm_filename' => '',
                'product' => null,
                'matched_product_id' => null,
                'matched_product_name' => null,
                'status' => 'INVALID FILE',
                'match_type' => 'invalid',
                'message' => 'Filename could not be parsed or is empty.',
                'candidates' => []
            ];
        }

        // Create a variant without trailing document type keywords if present (e.g. "nitric acid msds" -> "nitric acid")
        $typeKeywordsPattern = '/\s+(msds|msdc|specification|spec|tds|sdc|certificate|catalog|datasheet)$/i';
        $normFilenameStripped = preg_replace($typeKeywordsPattern, '', $normFilename);

        $exactMatches = [];
        $chemicalMatches = [];
        $slugMatches = [];
        $candidateMatches = [];

        foreach ($allProducts as $product) {
            $normName = $this->normalizeProductString($product->name);
            $baseName = $this->getBaseProductString($product->name);

            $normChem = $this->normalizeProductString($product->chemical_name ?? '');
            $baseChem = $this->getBaseProductString($product->chemical_name ?? '');

            $normSlug = $this->normalizeProductString($product->slug ?? '');

            // 1. Exact Name / Base Name Match
            if (
                $normFilename === $normName ||
                $normFilename === $baseName ||
                $normFilenameStripped === $normName ||
                $normFilenameStripped === $baseName
            ) {
                $exactMatches[] = $product;
                continue;
            }

            // 2. Exact Chemical Name / Base Chemical Match
            if (
                (!empty($normChem) && ($normFilename === $normChem || $normFilenameStripped === $normChem)) ||
                (!empty($baseChem) && ($normFilename === $baseChem || $normFilenameStripped === $baseChem))
            ) {
                $chemicalMatches[] = $product;
                continue;
            }

            // 3. Exact Slug Match
            if (!empty($normSlug) && ($normFilename === $normSlug || $normFilenameStripped === $normSlug)) {
                $slugMatches[] = $product;
                continue;
            }

            // Substring candidate search (for detecting ambiguous partial matches)
            $checkTarget = $baseName !== '' ? $baseName : $normName;
            if ($checkTarget !== '' && (str_contains($checkTarget, $normFilename) || str_contains($normFilename, $checkTarget))) {
                $candidateMatches[] = $product;
            }
        }

        // Priority 1: Exact Name Match
        if (count($exactMatches) === 1) {
            return $this->buildResult($filename, $normFilename, $exactMatches[0], 'exact_name', $pdfType, $mode);
        }

        if (count($exactMatches) > 1) {
            $names = array_values(array_unique(array_map(fn($p) => $p->name, $exactMatches)));
            if (count($names) === 1) {
                return $this->buildResult($filename, $normFilename, $exactMatches[0], 'exact_name', $pdfType, $mode);
            }
            return [
                'filename' => $filename,
                'norm_filename' => $normFilename,
                'product' => null,
                'matched_product_id' => null,
                'matched_product_name' => null,
                'status' => 'AMBIGUOUS',
                'match_type' => 'ambiguous',
                'message' => 'Multiple products match exact name: ' . implode(', ', $names),
                'candidates' => $names
            ];
        }

        // Priority 2: Exact Chemical Name Match
        if (count($chemicalMatches) === 1) {
            return $this->buildResult($filename, $normFilename, $chemicalMatches[0], 'exact_chemical', $pdfType, $mode);
        }

        if (count($chemicalMatches) > 1) {
            $names = array_values(array_unique(array_map(fn($p) => $p->name, $chemicalMatches)));
            if (count($names) === 1) {
                return $this->buildResult($filename, $normFilename, $chemicalMatches[0], 'exact_chemical', $pdfType, $mode);
            }
            return [
                'filename' => $filename,
                'norm_filename' => $normFilename,
                'product' => null,
                'matched_product_id' => null,
                'matched_product_name' => null,
                'status' => 'AMBIGUOUS',
                'match_type' => 'ambiguous',
                'message' => 'Multiple products match chemical name: ' . implode(', ', $names),
                'candidates' => $names
            ];
        }

        // Priority 3: Exact Slug Match
        if (count($slugMatches) === 1) {
            return $this->buildResult($filename, $normFilename, $slugMatches[0], 'exact_slug', $pdfType, $mode);
        }

        if (count($slugMatches) > 1) {
            $names = array_values(array_unique(array_map(fn($p) => $p->name, $slugMatches)));
            if (count($names) === 1) {
                return $this->buildResult($filename, $normFilename, $slugMatches[0], 'exact_slug', $pdfType, $mode);
            }
            return [
                'filename' => $filename,
                'norm_filename' => $normFilename,
                'product' => null,
                'matched_product_id' => null,
                'matched_product_name' => null,
                'status' => 'AMBIGUOUS',
                'match_type' => 'ambiguous',
                'message' => 'Multiple products match slug: ' . implode(', ', $names),
                'candidates' => $names
            ];
        }

        // Handle partial/candidate matches (Ambiguous vs Not Found)
        if (count($candidateMatches) > 1) {
            $names = array_values(array_unique(array_map(fn($p) => $p->name, $candidateMatches)));
            if (count($names) === 1) {
                return $this->buildResult($filename, $normFilename, $candidateMatches[0], 'candidate_unique', $pdfType, $mode);
            }
            return [
                'filename' => $filename,
                'norm_filename' => $normFilename,
                'product' => null,
                'matched_product_id' => null,
                'matched_product_name' => null,
                'status' => 'AMBIGUOUS',
                'match_type' => 'ambiguous',
                'message' => 'Multiple candidate products found: ' . implode(', ', array_slice($names, 0, 5)),
                'candidates' => $names
            ];
        }

        return [
            'filename' => $filename,
            'norm_filename' => $normFilename,
            'product' => null,
            'matched_product_id' => null,
            'matched_product_name' => null,
            'status' => 'NOT FOUND',
            'match_type' => 'not_found',
            'message' => 'No matching product found in database.',
            'candidates' => []
        ];
    }

    /**
     * Build result array for matched product.
     */
    private function buildResult(
        string $filename,
        string $normFilename,
        Product $product,
        string $matchType,
        string $pdfType,
        string $mode
    ): array {
        $typeLabel = strtolower($pdfType) === 'specification' ? 'Specification' : 'MSDS';
        $existingUrl = strtolower($pdfType) === 'specification' ? $product->specification_url : $product->msds_url;

        $hasExisting = !empty($existingUrl) && $existingUrl !== '#' && trim($existingUrl) !== '';

        if ($hasExisting && $mode === 'skip') {
            return [
                'filename' => $filename,
                'norm_filename' => $normFilename,
                'product' => $product,
                'matched_product_id' => $product->id,
                'matched_product_name' => $product->name,
                'status' => 'ALREADY EXISTS',
                'match_type' => $matchType,
                'message' => "Product '{$product->name}' already has a {$typeLabel} attached. Skipped.",
                'candidates' => [],
                'has_existing' => true,
                'existing_url' => $existingUrl,
            ];
        }

        $msg = $hasExisting
            ? "Product '{$product->name}' matched. Existing {$typeLabel} will be replaced."
            : "Product '{$product->name}' matched successfully.";

        return [
            'filename' => $filename,
            'norm_filename' => $normFilename,
            'product' => $product,
            'matched_product_id' => $product->id,
            'matched_product_name' => $product->name,
            'status' => 'SUCCESS',
            'match_type' => $matchType,
            'message' => $msg,
            'candidates' => [],
            'has_existing' => $hasExisting,
            'existing_url' => $existingUrl,
        ];
    }
}
