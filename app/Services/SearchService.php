<?php

namespace App\Services;

use App\Models\Product;

class SearchService
{
    /**
     * Chemical formula and abbreviation synonym mapping.
     */
    protected static array $synonyms = [
        'hcl' => 'hydrochloric acid',
        'h2so4' => 'sulphuric acid',
        'hno3' => 'nitric acid',
        'naoh' => 'caustic soda',
        'cl2' => 'liquid chlorine',
        'naocl' => 'sodium hypochlorite',
        'ch3cl' => 'methyl chloride',
        'chcl3' => 'chloroform',
        'ccl4' => 'carbon tetrachloride',
        'h3po4' => 'phosphoric acid',
        'h2o2' => 'hydrogen peroxide',
        'ipa' => 'iso propyl alcohol',
        'pac' => 'poly aluminium chloride',
        'mcb' => 'mono chloro benzene',
        'pdcb' => 'para di chloro benzene',
        'odcb' => 'ortho di chloro benzene',
        'tcb' => 'tri chloro benzene',
        'dncb' => 'di nitro chloro benzene',
        '2,4 dncb' => '2,4 di nitro chloro benzene',
        '2,5 dncb' => '2,5 di chloro nitro benzene',
        '3,4 dcnb' => '3,4 di chloro nitro benzene',
        'dcnb' => 'di chloro nitro benzene',
        'pncb' => 'para nitro chloro benzene',
        'oncb' => 'ortho nitro chloro benzene',
        'mncb' => 'meta nitro chloro benzene',
        'pca' => 'para chloro aniline',
        'mca' => 'meta chloro aniline',
        'oca' => 'ortho chloro aniline',
        'dca' => 'di chloro aniline',
        'tca' => 'tri chloro aniline',
        'oa' => 'ortho anisidine',
        'pa' => 'para anisidine',
        'pt' => 'para toluidine',
        'ot' => 'ortho toluidine',
        'mt' => 'meta toluidine',
        'mdc' => 'methylene chloride',
        'methylene dichloride' => 'methylene chloride',
        'dichloromethane' => 'methylene chloride',
        'dcm' => 'methylene chloride',
        'dsa' => 'spent sulphuric acid',
        'nb' => 'nitrobenzene',
        'ccl4' => 'carbon tetrachloride',
        'koh' => 'caustic potash',
        'bcs' => 'butyl cellosolve',
        'dmf' => 'dimethyl formamide',
        'mek' => 'methyl ethyl ketone',
        'mibk' => 'methyl isobutyl ketone',
        'nba' => 'normal butyl alcohol',
    ];

    public static function normalizeSingularPlural(string $input): string
    {
        $words = explode(' ', $input);
        $normalized = [];
        $map = [
            'acids' => 'acid',
            'solvents' => 'solvent',
            'chemicals' => 'chemical',
            'products' => 'product',
            'flakes' => 'flake',
            'prills' => 'prill',
            'chlorides' => 'chloride',
            'alkalis' => 'alkali',
            'oxides' => 'oxide',
            'peroxides' => 'peroxide',
            'nitrates' => 'nitrate',
            'sulfates' => 'sulfate',
            'sulphates' => 'sulphate',
            'carbonates' => 'carbonate',
            'powders' => 'powder',
            'liquids' => 'liquid',
            'gasses' => 'gas',
            'gases' => 'gas',
            'anilines' => 'aniline',
            'toluidines' => 'toluidine',
            'benzenes' => 'benzene',
            'nitrobenzenes' => 'nitrobenzene',
        ];

        foreach ($words as $word) {
            $wLower = strtolower($word);
            $normalized[] = $map[$wLower] ?? $word;
        }

        return implode(' ', $normalized);
    }

    public static function normalize(string $input): string
    {
        $clean = mb_strtolower(trim($input), 'UTF-8');

        // Check if entire input is a known synonym
        if (isset(self::$synonyms[$clean])) {
            $clean = self::$synonyms[$clean];
        } else {
            // Word-by-word replacement
            $words = explode(' ', $clean);
            $replaced = [];
            foreach ($words as $word) {
                $wClean = preg_replace('/[^a-z0-9]/', '', $word);
                if (isset(self::$synonyms[$wClean])) {
                    $replaced[] = self::$synonyms[$wClean];
                } else {
                    $replaced[] = $word;
                }
            }
            $clean = implode(' ', $replaced);
        }

        // Remove special characters
        $clean = preg_replace('~[()\[\]{},.\-_\\\\/+&%*#@!$^:;"\']~u', ' ', $clean);

        // Collapse multiple spaces
        $clean = preg_replace('/\s+/', ' ', $clean);

        $clean = self::normalizeSingularPlural(trim($clean));

        return trim($clean);
    }

    public static function rawClean(string $input): string
    {
        $clean = mb_strtolower(trim($input), 'UTF-8');
        $clean = preg_replace('~[()\[\]{},.\-_\\\\/+&%*#@!$^:;"\']~u', ' ', $clean);
        $clean = preg_replace('/\s+/', ' ', $clean);
        return self::normalizeSingularPlural(trim($clean));
    }

    public static function getBaseName(string $name): string
    {
        $base = preg_replace('/\s*\([^)]*\)/u', '', $name);
        return trim($base);
    }

    /**
     * Search active database products live using 7-level priority order:
     * 1. Exact product name match
     * 2. Exact normalized product name match
     * 3. Product name starts with query
     * 4. Product name contains query
     * 5. Alias / Chemical code / formula / CAS / HSN match
     * 6. Description / Specification match
     * 7. Fuzzy / Typo match ONLY when Levels 1-6 return zero results.
     */
    public static function search(string $rawQuery): array
    {
        $rawQuery = trim($rawQuery);
        if (empty($rawQuery)) {
            return [
                'match_type' => 'none',
                'priority' => 0,
                'count' => 0,
                'products' => [],
            ];
        }

        // ALWAYS fetch LIVE active products directly from Product Eloquent model
        $allProducts = Product::where('status', true)->with('category')->get();

        if ($allProducts->isEmpty()) {
            return [
                'match_type' => 'none',
                'priority' => 0,
                'count' => 0,
                'products' => [],
            ];
        }

        $literalQuery = mb_strtolower($rawQuery, 'UTF-8');
        $rawQueryClean = self::rawClean($rawQuery);
        $normQuery = self::normalize($rawQuery);
        $compactQuery = str_replace(' ', '', $normQuery);

        // Index active products for matching
        $indexedProducts = [];
        foreach ($allProducts as $product) {
            $pNameLower = mb_strtolower($product->name, 'UTF-8');
            $pChemLower = mb_strtolower($product->chemical_name ?? '', 'UTF-8');
            $pSlugClean = str_replace('-', ' ', $product->slug ?? '');

            $baseName = self::getBaseName($product->name);
            $baseChem = self::getBaseName($product->chemical_name ?? '');

            $rawNameClean = self::rawClean($product->name);
            $rawChemClean = self::rawClean($product->chemical_name ?? '');
            $normName = self::normalize($product->name);
            $normBaseName = self::normalize($baseName);
            $normChemName = self::normalize($product->chemical_name ?? '');
            $normBaseChem = self::normalize($baseChem);
            $normSlug = self::normalize($pSlugClean);

            $specsStr = '';
            if (is_array($product->specifications)) {
                $specsStr = implode(' ', array_map(fn($k, $v) => "$k $v", array_keys($product->specifications), $product->specifications));
            } elseif (is_string($product->specifications)) {
                $specsStr = $product->specifications;
            }

            $indexedProducts[] = [
                'model' => $product,
                'id' => $product->id,
                'name' => $product->name,
                'name_lower' => $pNameLower,
                'chem_lower' => $pChemLower,
                'slug_clean' => strtolower($pSlugClean),
                'base_name' => $baseName,
                'raw_name_clean' => $rawNameClean,
                'raw_chem_clean' => $rawChemClean,
                'norm_name' => $normName,
                'norm_base_name' => $normBaseName,
                'norm_chem_name' => $normChemName,
                'norm_base_chem' => $normBaseChem,
                'norm_slug' => $normSlug,
                'compact_name' => str_replace(' ', '', $normName),
                'compact_base' => str_replace(' ', '', $normBaseName),
                'cas_number' => self::rawClean($product->cas_number ?? ''),
                'hsn_code' => self::rawClean($product->hsn_code ?? ''),
                'brand' => self::normalize($product->brand ?? ''),
                'description' => self::normalize($product->description ?? ''),
                'applications' => self::normalize(is_array($product->applications) ? implode(' ', $product->applications) : ($product->applications ?? '')),
                'specifications' => self::normalize($specsStr),
            ];
        }

        // ==========================================
        // PRIORITY 1: LITERAL / EXACT PRODUCT NAME MATCH
        // ==========================================
        $exactLiteral = [];
        $exactBaseName = [];
        foreach ($indexedProducts as $p) {
            if (
                $literalQuery === $p['name_lower'] ||
                $literalQuery === $p['slug_clean'] ||
                (!empty($p['chem_lower']) && $literalQuery === $p['chem_lower'])
            ) {
                $exactLiteral[] = $p;
            } elseif ($literalQuery === mb_strtolower($p['base_name'], 'UTF-8')) {
                $exactBaseName[] = $p;
            }
        }
        if (!empty($exactLiteral)) {
            $dedup = self::deduplicate($exactLiteral);
            return [
                'match_type' => 'exact',
                'priority' => 1,
                'count' => count($dedup),
                'products' => array_map(fn($item) => $item['model'], $dedup),
            ];
        }
        if (!empty($exactBaseName)) {
            $dedup = self::deduplicate($exactBaseName);
            return [
                'match_type' => 'exact_base',
                'priority' => 1,
                'count' => count($dedup),
                'products' => array_map(fn($item) => $item['model'], $dedup),
            ];
        }

        // ==========================================
        // PRIORITY 2: EXACT NORMALIZED PRODUCT NAME MATCH
        // ==========================================
        $exactNormMatches = [];
        foreach ($indexedProducts as $p) {
            if (
                $rawQueryClean === $p['raw_name_clean'] ||
                $normQuery === $p['norm_name'] ||
                $normQuery === $p['norm_base_name'] ||
                (!empty($p['norm_slug']) && $normQuery === $p['norm_slug']) ||
                (!empty($p['norm_chem_name']) && $normQuery === $p['norm_chem_name']) ||
                (!empty($p['norm_base_chem']) && $normQuery === $p['norm_base_chem'])
            ) {
                $exactNormMatches[] = $p;
            }
        }
        if (!empty($exactNormMatches)) {
            $dedup = self::deduplicate($exactNormMatches);
            return [
                'match_type' => 'exact_normalized',
                'priority' => 2,
                'count' => count($dedup),
                'products' => array_map(fn($item) => $item['model'], $dedup),
            ];
        }

        // Compact exact match check (e.g. "nitricacid" -> "nitric acid")
        if (strlen($compactQuery) >= 4) {
            $compactMatches = [];
            foreach ($indexedProducts as $p) {
                if ($compactQuery === $p['compact_name'] || $compactQuery === $p['compact_base']) {
                    $compactMatches[] = $p;
                }
            }
            if (!empty($compactMatches)) {
                $dedup = self::deduplicate($compactMatches);
                return [
                    'match_type' => 'exact_compact',
                    'priority' => 2,
                    'count' => count($dedup),
                    'products' => array_map(fn($item) => $item['model'], $dedup),
                ];
            }
        }

        // ==========================================
        // PRIORITY 3: STARTS WITH MATCH
        // ==========================================
        $startsWithMatches = [];
        foreach ($indexedProducts as $p) {
            if (
                str_starts_with($p['norm_name'], $normQuery) ||
                str_starts_with($p['norm_base_name'], $normQuery) ||
                str_starts_with($normQuery, $p['norm_name']) ||
                str_starts_with($normQuery, $p['norm_base_name']) ||
                (!empty($p['norm_chem_name']) && str_starts_with($p['norm_chem_name'], $normQuery))
            ) {
                $startsWithMatches[] = $p;
            }
        }

        if (!empty($startsWithMatches)) {
            $uniqueStarts = self::deduplicate($startsWithMatches);
            return [
                'match_type' => 'starts_with',
                'priority' => 3,
                'count' => count($uniqueStarts),
                'products' => array_map(fn($item) => $item['model'], $uniqueStarts),
            ];
        }

        // ==========================================
        // PRIORITY 4: CONTAINS MATCH IN PRODUCT NAME
        // ==========================================
        $containsNameMatches = [];
        foreach ($indexedProducts as $p) {
            if (
                str_contains($p['norm_name'], $normQuery) ||
                str_contains($p['norm_base_name'], $normQuery) ||
                (!empty($p['norm_chem_name']) && str_contains($p['norm_chem_name'], $normQuery)) ||
                (!empty($p['norm_slug']) && str_contains($p['norm_slug'], $normQuery))
            ) {
                $containsNameMatches[] = $p;
            }
        }

        if (!empty($containsNameMatches)) {
            $uniqueContains = self::deduplicate($containsNameMatches);
            return [
                'match_type' => 'contains',
                'priority' => 4,
                'count' => count($uniqueContains),
                'products' => array_map(fn($item) => $item['model'], $uniqueContains),
            ];
        }

        // ==========================================
        // PRIORITY 5: ALIAS / CHEMICAL CODE / CAS / HSN MATCH
        // ==========================================
        $codeMatches = [];
        foreach ($indexedProducts as $p) {
            if (
                (!empty($p['cas_number']) && (str_contains($p['cas_number'], $normQuery) || str_contains($compactQuery, str_replace(' ', '', $p['cas_number'])))) ||
                (!empty($p['hsn_code']) && (str_contains($p['hsn_code'], $normQuery) || str_contains($compactQuery, str_replace(' ', '', $p['hsn_code']))))
            ) {
                $codeMatches[] = $p;
            }
        }
        if (!empty($codeMatches)) {
            $uniqueCode = self::deduplicate($codeMatches);
            return [
                'match_type' => 'code_alias',
                'priority' => 5,
                'count' => count($uniqueCode),
                'products' => array_map(fn($item) => $item['model'], $uniqueCode),
            ];
        }

        // ==========================================
        // PRIORITY 6: DESCRIPTION / SPECIFICATION / APPLICATION MATCH
        // ==========================================
        $specDescMatches = [];
        foreach ($indexedProducts as $p) {
            if (
                (!empty($p['specifications']) && str_contains($p['specifications'], $normQuery)) ||
                (!empty($p['applications']) && str_contains($p['applications'], $normQuery)) ||
                (!empty($p['description']) && str_contains($p['description'], $normQuery)) ||
                (!empty($p['brand']) && str_contains($p['brand'], $normQuery))
            ) {
                $specDescMatches[] = $p;
            }
        }

        if (!empty($specDescMatches)) {
            $uniqueSpec = self::deduplicate($specDescMatches);
            return [
                'match_type' => 'description_spec',
                'priority' => 6,
                'count' => count($uniqueSpec),
                'products' => array_map(fn($item) => $item['model'], $uniqueSpec),
            ];
        }

        // ==========================================
        // PRIORITY 7: FUZZY / TYPO MATCHING (ONLY WHEN LEVELS 1-6 RETURN ZERO MATCHES)
        // ==========================================
        $bestProduct = null;
        $bestSimilarity = 0.0;

        foreach ($indexedProducts as $p) {
            $targets = [
                $p['norm_name'],
                $p['norm_base_name'],
                $p['norm_chem_name'],
                $p['norm_base_chem'],
                $p['norm_slug'],
            ];

            foreach ($targets as $target) {
                if (empty($target))
                    continue;

                similar_text($normQuery, $target, $percent);
                if ($percent > $bestSimilarity) {
                    $bestSimilarity = $percent;
                    $bestProduct = $p;
                }

                $len1 = strlen($normQuery);
                $len2 = strlen($target);
                if ($len1 >= 3 && $len2 >= 3) {
                    $lev = levenshtein($normQuery, $target);
                    $maxLen = max($len1, $len2);
                    $levSim = (1 - ($lev / $maxLen)) * 100;
                    if ($levSim > $bestSimilarity) {
                        $bestSimilarity = $levSim;
                        $bestProduct = $p;
                    }
                }
            }
        }

        // If fuzzy similarity threshold >= 75% is reached -> return ONLY the single best match as fallback
        if ($bestProduct && $bestSimilarity >= 75.0) {
            return [
                'match_type' => 'fuzzy_typo',
                'priority' => 7,
                'similarity' => round($bestSimilarity, 2),
                'count' => 1,
                'products' => [$bestProduct['model']],
            ];
        }

        // No match found
        return [
            'match_type' => 'none',
            'priority' => 0,
            'count' => 0,
            'products' => [],
        ];
    }

    /**
     * Deduplicate products array by product ID and product name.
     */
    protected static function deduplicate(array $items): array
    {
        $seenIds = [];
        $seenNames = [];
        $result = [];

        foreach ($items as $item) {
            $id = $item['id'];
            $nameNorm = mb_strtolower(trim($item['name'] ?? ''), 'UTF-8');

            if (!isset($seenIds[$id]) && !isset($seenNames[$nameNorm])) {
                $seenIds[$id] = true;
                $seenNames[$nameNorm] = true;
                $result[] = $item;
            }
        }

        return $result;
    }
}
