<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ProductFilenameMatcher
{
    /**
     * Centralized filename normalization method.
     *
     * Rules:
     * 1. Remove file extension case-insensitively (.jpg, .png, .pdf, etc.)
     * 2. Convert to lowercase UTF-8
     * 3. Replace hyphens (-), underscores (_), slashes, brackets, and special characters with spaces
     * 4. Collapse multiple spaces to a single space
     * 5. Trim leading/trailing whitespace
     *
     * Examples:
     * "Phosphoric-Acid.jpg" -> "phosphoric acid"
     * "PHOSPHORIC_ACID.PNG" -> "phosphoric acid"
     * "Nitric Acid (1).jpg" -> "nitric acid 1"
     */
    public function normalizeFilename(string $filename): string
    {
        // 1. Remove extension case-insensitively
        $base = preg_replace('/\.[a-z0-9]+$/i', '', trim($filename));
        if ($base === null) {
            $base = pathinfo($filename, PATHINFO_FILENAME);
        }

        // 2. Lowercase
        $str = mb_strtolower($base, 'UTF-8');

        // 3. Replace hyphens, underscores, brackets, and special characters with spaces
        $str = str_replace(['-', '_'], ' ', $str);
        $str = preg_replace('~[()\[\]{},.\-\\\\/+&%*#@!$^:;"\']~u', ' ', $str);

        // 4. Collapse multiple spaces to single space and trim
        $str = preg_replace('/\s+/', ' ', $str);

        return trim($str);
    }

    /**
     * Normalize product string (name, chemical_name, or slug) in the exact same manner as filenames.
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
     * Remove parenthetical expressions from product names e.g. "Hydrochloric Acid (HCl)" -> "Hydrochloric Acid"
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
     * Strip common upload copy/version suffixes e.g. "nitric acid 1" -> "nitric acid", "nitric acid copy" -> "nitric acid"
     */
    public function stripVersionSuffix(string $normalized): string
    {
        $stripped = preg_replace('/\s+(copy(\s*\d+)?|\d+)$/i', '', $normalized);
        return trim($stripped ?: $normalized);
    }

    /**
     * Unified product matching engine.
     *
     * Returns:
     * [
     *   'status' => 'SUCCESS' | 'ALREADY EXISTS' | 'AMBIGUOUS' | 'NOT FOUND',
     *   'matched_product_id' => int|null,
     *   'matched_product_name' => string|null,
     *   'message' => string
     * ]
     */
    public function matchFilenameToProduct(
        string $filename,
        Collection $allProducts,
        string $fileType = 'image', // 'image', 'msds', or 'specification'
        string $mode = 'skip'       // 'skip' or 'replace'
    ): array {
        $normFilename = $this->normalizeFilename($filename);
        $strippedFilename = $this->stripVersionSuffix($normFilename);

        if (empty($normFilename)) {
            return [
                'status' => 'NOT FOUND',
                'matched_product_id' => null,
                'matched_product_name' => null,
                'message' => 'Filename normalization resulted in an empty string.'
            ];
        }

        $exactMatches = collect();
        $chemicalMatches = collect();
        $slugMatches = collect();
        $strippedMatches = collect();
        $baseNameMatches = collect();
        $partialMatches = collect();

        foreach ($allProducts as $product) {
            $normName = $this->normalizeProductString($product->name);
            $normChem = $this->normalizeProductString($product->chemical_name ?? '');
            $normSlug = $this->normalizeProductString($product->slug ?? '');
            $baseName = $this->getBaseProductString($product->name);

            // Tier 1: Exact Name
            if ($normFilename === $normName) {
                $exactMatches->push($product);
            }

            // Tier 2: Exact Chemical Name
            if (!empty($normChem) && $normFilename === $normChem) {
                $chemicalMatches->push($product);
            }

            // Tier 3: Exact Slug
            if (!empty($normSlug) && $normFilename === $normSlug) {
                $slugMatches->push($product);
            }

            // Tier 4: Version/Copy Suffix Stripped Match
            if ($strippedFilename !== $normFilename) {
                if ($strippedFilename === $normName || (!empty($normChem) && $strippedFilename === $normChem) || (!empty($normSlug) && $strippedFilename === $normSlug)) {
                    $strippedMatches->push($product);
                }
            }

            // Tier 5: Base Product Name Match (without brackets)
            if ($baseName !== '' && ($normFilename === $baseName || $strippedFilename === $baseName)) {
                $baseNameMatches->push($product);
            }

            // Tier 6: Substring / Partial match candidate detection for ambiguity protection
            $target = $baseName !== '' ? $baseName : $normName;
            if (!empty($target) && strlen($normFilename) >= 3) {
                // If the normalized filename matches a whole word within the product name e.g. "Acid" in "Nitric Acid"
                if (preg_match('/\b' . preg_quote($normFilename, '/') . '\b/i', $target) || preg_match('/\b' . preg_quote($target, '/') . '\b/i', $normFilename)) {
                    $partialMatches->push($product);
                }
            }
        }

        // Determine matching candidates in strict priority order
        $candidates = collect();
        if ($exactMatches->isNotEmpty()) {
            $candidates = $exactMatches->unique('id');
        } elseif ($chemicalMatches->isNotEmpty()) {
            $candidates = $chemicalMatches->unique('id');
        } elseif ($slugMatches->isNotEmpty()) {
            $candidates = $slugMatches->unique('id');
        } elseif ($strippedMatches->isNotEmpty()) {
            $candidates = $strippedMatches->unique('id');
        } elseif ($baseNameMatches->isNotEmpty()) {
            $candidates = $baseNameMatches->unique('id');
        }

        // If high-priority Tiers (1-5) yielded multiple matches -> AMBIGUOUS
        if ($candidates->count() > 1) {
            $matchedNames = $candidates->pluck('name')->implode(', ');
            return [
                'status' => 'AMBIGUOUS',
                'matched_product_id' => null,
                'matched_product_name' => null,
                'message' => "Multiple products matched ({$matchedNames}). Manual assignment required."
            ];
        }

        // If Tiers 1-5 found exactly 1 product match -> SUCCESS
        if ($candidates->count() === 1) {
            /** @var Product $matchedProduct */
            $matchedProduct = $candidates->first();

            $hasExisting = false;
            if ($fileType === 'image') {
                $hasExisting = !empty($matchedProduct->image_url);
            } elseif ($fileType === 'specification') {
                $hasExisting = !empty($matchedProduct->specification_url) || !empty($matchedProduct->specification_image);
            } else { // msds
                $hasExisting = !empty($matchedProduct->msds_url);
            }

            if ($hasExisting && $mode === 'skip') {
                return [
                    'status' => 'ALREADY EXISTS',
                    'matched_product_id' => $matchedProduct->id,
                    'matched_product_name' => $matchedProduct->name,
                    'message' => "Product '{$matchedProduct->name}' already has an attached " . strtoupper($fileType) . " file. Skipped per settings."
                ];
            }

            return [
                'status' => 'SUCCESS',
                'matched_product_id' => $matchedProduct->id,
                'matched_product_name' => $matchedProduct->name,
                'message' => "Successfully matched to product '{$matchedProduct->name}'."
            ];
        }

        // Tier 6: No exact match found, but partial word matches exist
        $uniquePartials = $partialMatches->unique('id');
        if ($uniquePartials->count() > 1) {
            $names = $uniquePartials->pluck('name')->slice(0, 5)->implode(', ');
            return [
                'status' => 'AMBIGUOUS',
                'matched_product_id' => null,
                'matched_product_name' => null,
                'message' => "Multiple partial products matched ({$names}). Manual assignment required."
            ];
        }

        if ($uniquePartials->count() === 1) {
            /** @var Product $matchedProduct */
            $matchedProduct = $uniquePartials->first();
            return [
                'status' => 'SUCCESS',
                'matched_product_id' => $matchedProduct->id,
                'matched_product_name' => $matchedProduct->name,
                'message' => "Successfully matched to candidate product '{$matchedProduct->name}'."
            ];
        }

        return [
            'status' => 'NOT FOUND',
            'matched_product_id' => null,
            'matched_product_name' => null,
            'message' => "No matching product found for '{$filename}'."
        ];
    }
}
