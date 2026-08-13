<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ProductFilenameMatcher
{
    /**
     * Map unicode subscript characters to standard ASCII digits.
     */
    protected array $subscriptMap = [
        '₀' => '0', '₁' => '1', '₂' => '2', '₃' => '3', '₄' => '4',
        '₅' => '5', '₆' => '6', '₇' => '7', '₈' => '8', '₉' => '9',
    ];

    /**
     * Non-essential stop words to ignore during token comparison.
     */
    protected array $stopWords = [
        'photo', 'image', 'pic', 'product', 'file', 'main', 'thumb', 'thumbnail',
        'catalog', 'datasheet', 'msds', 'msdc', 'specification', 'spec', 'tds', 'sdc',
        'certificate', 'chemical', 'grade', 'pct', 'percent', '%'
    ];

    /**
     * Centralized filename normalization method.
     */
    public function normalizeFilename(string $filename): string
    {
        // 1. Remove extension case-insensitively
        $base = preg_replace('/\.[a-z0-9]+$/i', '', trim($filename));
        if ($base === null) {
            $base = pathinfo($filename, PATHINFO_FILENAME);
        }

        // 2. Lowercase UTF-8
        $str = mb_strtolower($base, 'UTF-8');

        // 3. Convert subscript characters
        $str = strtr($str, $this->subscriptMap);

        // 4. Replace hyphens, underscores, brackets, slashes, and punctuation with spaces
        $str = str_replace(['-', '_'], ' ', $str);
        $str = preg_replace('~[()\[\]{},.\-\\\\/+&%*#@!$^:;"\']~u', ' ', $str);

        // 5. Collapse multiple spaces and trim
        $str = preg_replace('/\s+/', ' ', $str);

        return trim($str);
    }

    /**
     * Normalize product string (name, chemical_name, or slug) identically.
     */
    public function normalizeProductString(?string $input): string
    {
        if (empty($input)) {
            return '';
        }

        $str = mb_strtolower(trim($input), 'UTF-8');
        $str = strtr($str, $this->subscriptMap);
        $str = str_replace(['-', '_'], ' ', $str);
        $str = preg_replace('~[()\[\]{},.\-\\\\/+&%*#@!$^:;"\']~u', ' ', $str);
        $str = preg_replace('/\s+/', ' ', $str);

        return trim($str);
    }

    /**
     * Get base product string by removing parenthetical expressions e.g. "Caustic Soda Prills (NaOH)" -> "caustic soda prills"
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
     * Extract clean tokens from a normalized string, ignoring stop words.
     */
    public function tokenize(string $normalized): array
    {
        $words = explode(' ', $normalized);
        $tokens = [];

        foreach ($words as $w) {
            $w = trim($w);
            if ($w !== '' && !in_array($w, $this->stopWords, true)) {
                $tokens[] = $w;
            }
        }

        return array_values(array_unique($tokens));
    }

    /**
     * Strip common upload version/copy suffixes e.g. "nitric acid 1" -> "nitric acid"
     */
    public function stripVersionSuffix(string $normalized): string
    {
        $stripped = preg_replace('/\s+(copy(\s*\d+)?|\d+)$/i', '', $normalized);
        return trim($stripped ?: $normalized);
    }

    /**
     * Enhanced Category-Aware & Token-Based Product Filename Matching Engine.
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
                'matched_category' => null,
                'match_method' => 'NONE',
                'confidence' => 'LOW',
                'message' => 'Filename normalization resulted in an empty string.'
            ];
        }

        $fileTokens = $this->tokenize($normFilename);

        $exactNameMatches = collect();
        $exactChemMatches = collect();
        $exactSlugMatches = collect();
        $versionMatches  = collect();
        $baseNameMatches = collect();
        $tokenMatches    = collect();

        foreach ($allProducts as $product) {
            $prodName = $product->name;
            $normName = $this->normalizeProductString($prodName);
            $baseName = $this->getBaseProductString($prodName);

            $normChem = $this->normalizeProductString($product->chemical_name ?? '');
            $normSlug = $this->normalizeProductString($product->slug ?? '');

            $catName  = $product->category->name ?? ($product['category']['name'] ?? '');
            $normCat  = $this->normalizeProductString($catName);

            // Tier 1: Exact Name
            if ($normFilename === $normName) {
                $exactNameMatches->push(['product' => $product, 'method' => 'EXACT_NAME', 'confidence' => 'HIGH']);
            }

            // Tier 2: Exact Chemical Name
            if (!empty($normChem) && $normFilename === $normChem) {
                $exactChemMatches->push(['product' => $product, 'method' => 'EXACT_CHEMICAL', 'confidence' => 'HIGH']);
            }

            // Tier 3: Exact Slug
            if (!empty($normSlug) && $normFilename === $normSlug) {
                $exactSlugMatches->push(['product' => $product, 'method' => 'EXACT_SLUG', 'confidence' => 'HIGH']);
            }

            // Tier 4: Version Suffix Match
            if ($strippedFilename !== $normFilename) {
                if ($strippedFilename === $normName || (!empty($normChem) && $strippedFilename === $normChem) || (!empty($normSlug) && $strippedFilename === $normSlug)) {
                    $versionMatches->push(['product' => $product, 'method' => 'VERSION_STRIPPED', 'confidence' => 'HIGH']);
                }
            }

            // Tier 5: Base Product Name Match (without parenthetical formula)
            if ($baseName !== '' && ($normFilename === $baseName || $strippedFilename === $baseName)) {
                $baseNameMatches->push(['product' => $product, 'method' => 'BASE_NAME', 'confidence' => 'HIGH']);
            }

            // Tier 6: Token-Set Match & Category Disambiguation
            $productNameTokens = $this->tokenize($normName);
            $baseNameTokens = $this->tokenize($baseName);
            $categoryTokens = $this->tokenize($normCat);

            // Check if all essential base name tokens (e.g., ["caustic", "soda", "prills"]) are present in fileTokens
            $targetTokens = !empty($baseNameTokens) ? $baseNameTokens : $productNameTokens;
            if (!empty($targetTokens)) {
                $missingTokens = array_diff($targetTokens, $fileTokens);
                if (empty($missingTokens)) {
                    // Check if category tokens are also matched in filename (for disambiguation)
                    $catMatchCount = count(array_intersect($categoryTokens, $fileTokens));
                    $tokenMatches->push([
                        'product' => $product,
                        'method' => $catMatchCount > 0 ? 'CATEGORY_TOKEN_MATCH' : 'TOKEN_MATCH',
                        'confidence' => 'HIGH',
                        'cat_matches' => $catMatchCount,
                        'total_tokens' => count($targetTokens) + $catMatchCount,
                    ]);
                }
            }
        }

        // Determine candidates in strict tier priority order
        $tierCandidates = collect();

        if ($exactNameMatches->isNotEmpty()) {
            $tierCandidates = $exactNameMatches;
        } elseif ($exactChemMatches->isNotEmpty()) {
            $tierCandidates = $exactChemMatches;
        } elseif ($exactSlugMatches->isNotEmpty()) {
            $tierCandidates = $exactSlugMatches;
        } elseif ($versionMatches->isNotEmpty()) {
            $tierCandidates = $versionMatches;
        } elseif ($baseNameMatches->isNotEmpty()) {
            $tierCandidates = $baseNameMatches;
        } elseif ($tokenMatches->isNotEmpty()) {
            // Sort token matches by highest category match and total tokens matched
            $sorted = $tokenMatches->sortByDesc('cat_matches')->sortByDesc('total_tokens');
            $maxCatMatches = $sorted->first()['cat_matches'];
            $maxTotalTokens = $sorted->first()['total_tokens'];

            // Keep candidates that tied for top token match score
            $tierCandidates = $sorted->filter(fn($m) => $m['cat_matches'] === $maxCatMatches && $m['total_tokens'] === $maxTotalTokens);
        }

        // Tier 7: Partial Overlap Search for Ambiguity Protection (e.g. acid.jpg matching multiple products)
        if ($tierCandidates->isEmpty()) {
            $partialMatches = collect();
            foreach ($allProducts as $product) {
                $normName = $this->normalizeProductString($product->name);
                $baseName = $this->getBaseProductString($product->name);
                $targetTokens = array_merge($this->tokenize($normName), $this->tokenize($baseName));

                $intersection = array_intersect($fileTokens, $targetTokens);
                if (!empty($intersection)) {
                    $partialMatches->push($product);
                }
            }

            $uniquePartials = $partialMatches->unique('id');
            if ($uniquePartials->count() > 1) {
                $names = $uniquePartials->map(function($p) {
                    $c = $p->category->name ?? '';
                    return $c ? "{$p->name} ({$c})" : $p->name;
                })->slice(0, 5)->implode(', ');

                return [
                    'status' => 'AMBIGUOUS',
                    'matched_product_id' => null,
                    'matched_product_name' => null,
                    'matched_category' => null,
                    'match_method' => 'AMBIGUOUS',
                    'confidence' => 'LOW',
                    'message' => "Multiple candidate products matched ({$names}). Manual assignment required."
                ];
            } elseif ($uniquePartials->count() === 1) {
                $tierCandidates->push(['product' => $uniquePartials->first(), 'method' => 'TOKEN_MATCH', 'confidence' => 'HIGH']);
            }
        }

        // Distinct product IDs matching
        $uniqueProducts = $tierCandidates->pluck('product')->unique('id');

        // AMBIGUITY PROTECTION: If multiple products match at top tier -> AMBIGUOUS
        if ($uniqueProducts->count() > 1) {
            $names = $uniqueProducts->map(function($p) {
                $c = $p->category->name ?? '';
                return $c ? "{$p->name} ({$c})" : $p->name;
            })->implode(', ');

            return [
                'status' => 'AMBIGUOUS',
                'matched_product_id' => null,
                'matched_product_name' => null,
                'matched_category' => null,
                'match_method' => 'AMBIGUOUS',
                'confidence' => 'LOW',
                'message' => "Multiple products matched ({$names}). Manual assignment required."
            ];
        }

        if ($uniqueProducts->isEmpty()) {
            return [
                'status' => 'NOT FOUND',
                'matched_product_id' => null,
                'matched_product_name' => null,
                'matched_category' => null,
                'match_method' => 'NONE',
                'confidence' => 'LOW',
                'message' => "No matching product found for '{$filename}'."
            ];
        }

        // Exactly ONE product matched!
        $match = $tierCandidates->first();
        /** @var Product $product */
        $product = $match['product'];
        $catName = $product->category->name ?? ($product['category']['name'] ?? 'General');

        $hasExisting = false;
        if ($fileType === 'image') {
            $hasExisting = !empty($product->image_url);
        } elseif ($fileType === 'specification') {
            $hasExisting = !empty($product->specification_url) || !empty($product->specification_image);
        } else { // msds
            $hasExisting = !empty($product->msds_url);
        }

        if ($hasExisting && $mode === 'skip') {
            return [
                'status' => 'EXISTING IMAGE',
                'matched_product_id' => $product->id,
                'matched_product_name' => $product->name,
                'matched_category' => $catName,
                'match_method' => $match['method'],
                'confidence' => $match['confidence'],
                'message' => "Product '{$product->name}' already has an assigned " . strtoupper($fileType) . ". Skipped per settings."
            ];
        }

        return [
            'status' => 'SUCCESS',
            'matched_product_id' => $product->id,
            'matched_product_name' => $product->name,
            'matched_category' => $catName,
            'match_method' => $match['method'],
            'confidence' => $match['confidence'],
            'message' => "Successfully matched to product '{$product->name}' ({$catName}) via {$match['method']}."
        ];
    }
}
