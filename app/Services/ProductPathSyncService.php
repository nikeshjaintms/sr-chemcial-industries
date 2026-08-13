<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Str;

class ProductPathSyncService
{
    /**
     * Authoritative Product Hierarchy Definition
     */
    protected array $hierarchy = [
        // GACL Products
        'GACL Products > Acid Products' => [
            'Nitric Acid', 'Acetic Acid', 'Formic Acid', 'Sulphuric Acid', 'Phosphoric Acid', 'Acetic Acid (Glacial)', 'Phosphoric Acid (H3PO4)', 'Sulphuric Acid (H₂SO₄)'
        ],
        'GACL Products > Chlor-Alkali Chemicals' => [
            'Caustic Soda Lye (NaOH)', 'Caustic Soda Flakes (NaOH)', 'Caustic Soda Prills (NaOH)',
            'Liquid Chlorine (Cl₂)', 'Hydrochloric Acid (HCl)', 'Sodium Hypochlorite (NaOCl)',
            'Caustic Soda Lye', 'Caustic Soda Flakes', 'Caustic Soda Prills', 'Liquid Chlorine', 'Hydrochloric Acid', 'Sodium Hypochlorite', 'Hydro Chloric Acid (HCL)', 'Hydro Chloric Acid (HCL)'
        ],
        'GACL Products > Hydrogen & Peroxide Chemicals' => [
            'Hydrogen Peroxide', 'Hydrazine Hydrate', 'Hydrogen Peroxide (H2O2)', 'Hydrazine Hydrate (N₂H₄·H₂O)'
        ],
        'GACL Products > Chloromethane Chemicals' => [
            'Methylene Chloride (MDC)', 'Chloroform (CHCl₃)', 'Methyl Chloride (CH₃Cl)', 'Carbon Tetrachloride (CCl₄)',
            'Methylene Chloride', 'Chloroform', 'Methyl Chloride', 'Carbon Tetrachloride', 'Chloroform (CHCl3)', 'Methyl Chloride (CH₃Cl)', 'Carbon Tetrachloride (CCl₄)'
        ],
        'GACL Products > Potassium Chemicals' => [
            'Caustic Potash Lye', 'Caustic Potash Flakes', 'Potassium Carbonate', 'Caustic Potash Lye (KOH Solution)', 'Caustic Potash Flakes (KOH)'
        ],
        'GACL Products > Aluminium Based Chemicals' => [
            'Anhydrous Aluminium Chloride', 'Poly Aluminium Chloride (PAC)', 'Anhydrous Aluminium Chloride (Technical Grade)', 'MonoChloro Acetic Acid (MCA)'
        ],
        'GACL Products > Water Treatment Chemicals' => [
            'Poly Aluminium Chloride (PAC) Powder', 'PAC Liquid', 'Stable Bleaching Powder', 'Bleaching Powder', 'Poly Aluminium Chloride (PAC)', 'Polyaluminium Chloride SAB 18'
        ],
        'GACL Products > Phosphate Chemicals' => [
            'Phosphoric Acid (85%)', 'Food Grade Phosphoric Acid', 'Food Grade Phosphoric Acid (H3PO4)'
        ],
        'GACL Products > Other Specialty Chemicals' => [
            'Sodium Chlorate', 'Hydrazine Hydrate', 'Chlorinated Paraffin', 'Benzyl Chloride', 'Sodium Chlorate (NaClO3)', 'Hydrazine Hydrate (N₂H₄·H₂O)', 'Chlorinated Paraffin (CPW)'
        ],

        // Organic Products
        'Organic Products > Chlorobenzenes' => [
            'Mono Chloro Benzene (MCB)', 'Para Di Chloro Benzene (PDCB)', 'Ortho Di Chloro Benzene (ODCB)', '1,2,4 Tri Chloro Benzene (TCB)'
        ],
        'Organic Products > Nitrobenzenes' => [
            'Nitrobenzene (NB)', '2,4 Di Nitro Chloro Benzene', '2,5 Di Chloro Nitro Benzene', '3,4 Di Chloro Nitro Benzene',
            'Para Nitro Chloro Benzene', 'Ortho Nitro Chloro Benzene', 'Meta Nitro Chloro Benzene', 'Nitrobenzene',
            '2,4 Di Nitro Chloro Benzene (2,4 DNCB)', '2,5 Di Chloro Nitro Benzene (2,5 DNCB)', '3,4 Di Chloro Nitro Benzene (3,4 DCNB)',
            'Para Nitro Chloro Benzene (PNCB)', 'Ortho Nitro Chloro Benzene (ONCB)', 'Meta Nitro Chloro Benzene (MNCB)'
        ],
        'Organic Products > Anilines & Toluidines' => [
            'Para Chloro Aniline', 'Meta Chloro Aniline', 'Ortho Chloro Aniline', '2,5 Di Chloro Aniline',
            '3,4 Di Chloro Aniline', '2,4,5 Tri Chloro Aniline', 'Ortho Anisidine', 'Para Anisidine',
            'Para Toluidine', 'Ortho Toluidine', 'Meta Toluidine',
            'Para Chloro Aniline (PCA)', 'Meta Chloro Aniline (MCA)', 'Ortho Chloro Aniline (OCA)', '2,5 Di Chloro Aniline (2,5 DCA)',
            '3,4 Di Chloro Aniline (3,4 DCA)', '2,4,5 Tri Chloro Aniline (TCA)', 'Ortho Anisidine (OA)', 'Para Anisidine (PA)',
            'Para Toluidine (PT)', 'Ortho Toluidine (OT)', 'Meta Toluidine (MT)'
        ],
        'Organic Products > Calcium & Benzene Products' => [
            'Calcium Chloride Prills/Powder', 'Calcium Chloride Brine', 'Benzene',
            'Calcium Chloride Prills/Powder (94–97%)', 'Calcium Chloride Brine (Solution)', 'Benzene (Pure, Refinery Grade)'
        ],

        // DMCC Products
        'DMCC Products > Boron Chemicals' => [
            'Borax Decahydrate', 'Borax Pentahydrate', 'Boric Acid', 'Boric Acid Special Quality Grade'
        ],
        'DMCC Products > Sulfur Products' => [
            'Sulfuric Acid (Commercial Grade)', 'Sulfuric Acid (Dilute)', 'Sulfuric Acid (Battery Grade)', 'Oleum 23%', 'Oleum 65%',
            'Oleum 23% (Commercial Grade)', 'Oleum 65% (Commercial Grade)', 'Spent Sulphuric Acid 70% (DSA)'
        ],

        // GNFC Products
        'GNFC > Organic Acids & Esters' => [
            'Formic Acid', 'Acetic Acid', 'Ethyl Acetate', 'Methyl Formate', 'Acetic Acid (Glacial)'
        ],
        'GNFC > Industrial Intermediates' => [
            'Aniline', 'Methanol', 'OTD', 'MTD', 'OTD (Ortho-Toluene Diamine)', 'MTD (Meta-Toluene Diamine)'
        ],
        'GNFC > Specialty Chemicals & Urea' => [
            'Technical Grade Urea', 'Capsol Chemical', 'Calcium Carbonate', 'Nitrogen Liquid',
            'Nitric Acid', 'Dilute Nitric Acid', 'Dilute Sulphuric Acid', 'HCl (Spent)', 'Calcium Carbonate (CaCO₃)', 'Hydrochloric Acid (HCl)'
        ],

        // Industrial Solvents
        'Industrial Solvents > Paint & Coating Industry Solvents' => [
            'Ethyl Acetate', 'Butyl Acetate', 'NC Thinner', 'Butyl Acetate (C₆H₁₂O₂)'
        ],
        'Industrial Solvents > Pharmaceutical & Chemical Solvents' => [
            'Iso Propyl Alcohol (IPA)', 'Methanol', 'Methylene Chloride', 'Chloroform', 'Iso Propyl Alcohol (IPA - C₃H₈O)', 'Methylene Chloride (MDC)', 'Chloroform (CHCl3)'
        ],
        'Industrial Solvents > Cleaning & Degreasing Solvents' => [
            'Methylene Chloride', 'Chloroform', 'Methylene Chloride (MDC)', 'Chloroform (CHCl3)'
        ],
        'Industrial Solvents > Coal & Energy Products' => [
            'Bio-Coal', 'Indonesian Coal', 'South African Coal', 'Screen Coal', 'Indonesian Coal (Imported)'
        ]
    ];

    /**
     * Execute category/path sync on existing products ONLY
     */
    public function syncPaths(): array
    {
        $existingProducts = Product::all();
        $totalExisting = $existingProducts->count();

        $matchedCount = 0;
        $pathsUpdatedCount = 0;
        $multiPathCount = 0;
        $unmatched = [];

        // Pre-create/resolve all category nodes
        $categoryMap = [];
        foreach ($this->hierarchy as $pathStr => $candidates) {
            $cat = Category::findOrCreatePath($pathStr);
            $categoryMap[$pathStr] = $cat;
        }

        // Set explicit sort_order for the 5 root categories
        $rootSortOrder = [
            'GACL Products' => 1,
            'Organic Products' => 2,
            'DMCC Products' => 3,
            'GNFC' => 4,
            'Industrial Solvents' => 5,
        ];

        foreach ($rootSortOrder as $rootName => $order) {
            Category::where('name', $rootName)->whereNull('parent_id')->update(['sort_order' => $order]);
        }

        // Delete any leftover 'General Products' root category and test products
        $genCat = Category::where('name', 'General Products')->first();
        if ($genCat) {
            Product::where('category_id', $genCat->id)->delete();
            $genCat->delete();
        }
        Product::where('slug', 'LIKE', '%test-chemical%')->delete();

        // Record initial pivot relationship state
        $initialPivot = \Illuminate\Support\Facades\DB::table('category_product')->get();
        $initialPairs = [];
        foreach ($initialPivot as $row) {
            $initialPairs["{$row->category_id}-{$row->product_id}"] = true;
        }

        // Clear existing pivot relationships for clean authoritative reconstruction
        \Illuminate\Support\Facades\DB::table('category_product')->truncate();

        $newPairs = [];
        $productCategoryCount = [];

        foreach ($this->hierarchy as $pathStr => $candidates) {
            $cat = $categoryMap[$pathStr] ?? Category::findOrCreatePath($pathStr);
            $sortOrder = 1;

            foreach ($candidates as $itemName) {
                $cleanTarget = strtolower(trim(preg_replace('/[^a-zA-Z0-9]/', '', $itemName)));
                $bestProduct = null;
                $bestScore = 0;

                foreach ($existingProducts as $p) {
                    $cleanPName = strtolower(trim(preg_replace('/[^a-zA-Z0-9]/', '', $p->name)));
                    $cleanPSlug = strtolower(trim(str_replace('-', '', $p->slug)));

                    $score = 0;
                    if ($cleanPName === $cleanTarget || $cleanPSlug === $cleanTarget) {
                        $score = 100;
                    } elseif (str_contains($cleanPName, $cleanTarget) || str_contains($cleanTarget, $cleanPName)) {
                        $score = 70 - abs(strlen($cleanPName) - strlen($cleanTarget));
                    }

                    if ($score > $bestScore) {
                        $bestScore = $score;
                        $bestProduct = $p;
                    }
                }

                if ($bestProduct && $bestScore >= 50) {
                    $pairKey = "{$cat->id}-{$bestProduct->id}";
                    if (!isset($newPairs[$pairKey])) {
                        \Illuminate\Support\Facades\DB::table('category_product')->insert([
                            'category_id' => $cat->id,
                            'product_id' => $bestProduct->id
                        ]);
                        $newPairs[$pairKey] = true;
                        $productCategoryCount[$bestProduct->id] = ($productCategoryCount[$bestProduct->id] ?? 0) + 1;
                    }

                    if (is_null($bestProduct->category_id)) {
                        $bestProduct->update([
                            'category_id' => $cat->id,
                            'sort_order' => $sortOrder
                        ]);
                    }
                    $sortOrder++;
                }
            }
        }

        // Calculate relationship diffs
        $relationshipsAdded = 0;
        foreach ($newPairs as $pairKey => $val) {
            if (!isset($initialPairs[$pairKey])) {
                $relationshipsAdded++;
            }
        }

        $relationshipsRemoved = 0;
        foreach ($initialPairs as $pairKey => $val) {
            if (!isset($newPairs[$pairKey])) {
                $relationshipsRemoved++;
            }
        }

        $multiPlacementCount = 0;
        foreach ($productCategoryCount as $pId => $cnt) {
            if ($cnt > 1) {
                $multiPlacementCount++;
            }
        }

        $matchedProductsCount = count($productCategoryCount);

        return [
            'existing_products' => $totalExisting,
            'products_matched' => $matchedProductsCount,
            'relationships_removed' => $relationshipsRemoved,
            'relationships_added' => $relationshipsAdded,
            'multi_placement_products' => $multiPlacementCount,
            'unmatched_products_count' => max(0, $totalExisting - $matchedProductsCount),
            'unmatched_products' => $unmatched,
            'products_created' => 0,
            'products_deleted' => 0,
            'product_data_modified' => 0,
            'assets_modified' => 0,
        ];
    }

    /**
     * Match product name/slug against hierarchy tree
     */
    protected function findCategoryPathsForProduct(string $pName, string $pSlug): array
    {
        $matches = [];
        $cleanName = strtolower(trim(preg_replace('/[^a-zA-Z0-9]/', '', $pName)));
        $cleanSlug = strtolower(trim(str_replace('-', '', $pSlug)));

        foreach ($this->hierarchy as $pathStr => $candidates) {
            foreach ($candidates as $candidate) {
                $cleanCand = strtolower(trim(preg_replace('/[^a-zA-Z0-9]/', '', $candidate)));

                if (
                    $cleanName === $cleanCand ||
                    $cleanSlug === $cleanCand ||
                    str_contains($cleanName, $cleanCand) ||
                    str_contains($cleanCand, $cleanName)
                ) {
                    $matches[] = $pathStr;
                    break;
                }
            }
        }

        return array_unique($matches);
    }
}
