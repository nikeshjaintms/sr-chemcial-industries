<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use App\Services\ProductImageMappingService;
use App\Services\ProductFilenameMatcher;

echo "==================================================\n";
echo "STEP 1: INSPECTING ACTUAL PHYSICAL FILES IN public/assets/products/\n";
echo "==================================================\n";

$targetDir = public_path('assets/products');
$physicalFiles = [];
if (file_exists($targetDir)) {
    $files = glob($targetDir . '/*');
    foreach ($files as $f) {
        if (!is_dir($f)) {
            $physicalFiles[] = basename($f);
        }
    }
}
echo "Total Physical Image Files Found: " . count($physicalFiles) . "\n\n";

echo "--- LIST OF PHYSICAL FILES IN public/assets/products/ ---\n";
foreach ($physicalFiles as $file) {
    echo "  • {$file}\n";
}

echo "\n==================================================\n";
echo "STEP 2: INSPECTING THE 26 PRODUCTS WITHOUT IMAGES IN DB\n";
echo "==================================================\n";

$target26Names = [
    'Caustic Soda Prills (NaOH)',
    'Liquid Chlorine (Cl₂)',
    'Sodium Hypochlorite (NaOCl)',
    'Hydrogen Peroxide',
    'Hydrazine Hydrate',
    'Carbon Tetrachloride (CCl₄)',
    'Anhydrous Aluminium Chloride',
    'Stable Bleaching Powder',
    'Ortho Di Chloro Benzene (ODCB)',
    'Ortho Nitro Chloro Benzene',
    'Meta Nitro Chloro Benzene',
    'Meta Chloro Aniline',
    'Ortho Chloro Aniline',
    'Calcium Chloride Prills/Powder',
    'Calcium Chloride Brine',
    'Sulfuric Acid (Commercial Grade)',
    'Sulfuric Acid (Battery Grade)',
    'Capsol Chemical',
    'Iso Propyl Alcohol (IPA)',
    'Chloroform',
    'Bio-Coal',
    'Indonesian Coal',
    'South African Coal',
    'Screen Coal',
];

$allProducts = Product::with('category')->get();
$matcher = new ProductFilenameMatcher();

foreach ($allProducts as $p) {
    $rawImage = $p->getRawOriginal('image_url');
    $hasImage = !empty($rawImage) && $rawImage !== '#' && trim($rawImage) !== '';
    $physicalPath = $hasImage ? public_path(ltrim($rawImage, '/')) : '';
    $fileExists = $hasImage && file_exists($physicalPath);

    // Filter to inspect why this product is marked without image or what matches it
    $isWithoutValidImage = !$hasImage || !$fileExists;

    if ($isWithoutValidImage) {
        echo "\nProduct ID: {$p->id} | Name: '{$p->name}' | Category: '" . ($p->category->name ?? 'N/A') . "'\n";
        echo "  • DB image_url column value: " . var_export($rawImage, true) . "\n";
        echo "  • Physical file exists on disk? " . ($fileExists ? 'YES' : 'NO') . "\n";

        // Try matching against ALL physical files in public/assets/products/
        $potentialMatches = [];
        foreach ($physicalFiles as $pf) {
            $normP = $matcher->normalizeProductString($p->name);
            $baseP = $matcher->getBaseProductString($p->name);
            $normF = $matcher->normalizeFilename($pf);

            $pTokens = $matcher->tokenize($normP);
            $bTokens = $matcher->tokenize($baseP);
            $fTokens = $matcher->tokenize($normF);

            $targetTokens = !empty($bTokens) ? $bTokens : $pTokens;
            $missingTargetInFile = array_diff($targetTokens, $fTokens);
            $missingFileInTarget = array_diff($fTokens, $targetTokens);

            if (empty($missingTargetInFile) || empty($missingFileInTarget)) {
                $potentialMatches[] = $pf;
            }
        }

        if (!empty($potentialMatches)) {
            echo "  • Candidate physical files matching: " . implode(', ', $potentialMatches) . "\n";
        } else {
            echo "  • ⚠️ NO physical file in public/assets/products/ matches this product name!\n";
        }
    }
}

echo "\n==================================================\n";
echo "DIAGNOSIS COMPLETED!\n";
echo "==================================================\n";
