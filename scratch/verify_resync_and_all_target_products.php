<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use App\Services\ProductImageMappingService;

echo "==================================================\n";
echo "VERIFYING ALL 20 TARGET PRODUCTS AFTER RE-SYNC\n";
echo "==================================================\n\n";

$targetProductNames = [
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
];

$mappingService = new ProductImageMappingService();
$mappingService->resyncExistingImages();

$products = Product::with('category')->get();
$passed = 0;
$total = count($targetProductNames);

foreach ($targetProductNames as $idx => $targetName) {
    $num = $idx + 1;
    $matchedProducts = $products->filter(fn($p) => str_contains(strtolower($p->name), strtolower($targetName)) || str_contains(strtolower($targetName), strtolower($p->name)));

    if ($matchedProducts->isEmpty()) {
        echo "[PRODUCT {$num}] ⚠️ NOT IN DB: '{$targetName}'\n";
        continue;
    }

    foreach ($matchedProducts as $p) {
        $rawUrl = $p->getRawOriginal('image_url') ?? $p->image_url;
        $hasUrl = !empty($rawUrl) && $rawUrl !== '#' && trim($rawUrl) !== '';
        $physicalPath = public_path(ltrim($rawUrl ?? '', '/'));
        $fileExists = $hasUrl && file_exists($physicalPath);

        $statusStr = ($hasUrl && $fileExists) ? "✅ ASSIGNED (Image: " . basename($rawUrl) . ")" : "⚠️ NO PHYSICAL IMAGE ASSIGNED";
        echo "[PRODUCT {$num}] {$p->name} ({$p->category->name}) → Status: {$statusStr}\n";
        if ($hasUrl && $fileExists) {
            $passed++;
        }
    }
}

echo "\n==================================================\n";
echo "TARGET PRODUCTS VERIFICATION COMPLETED!\n";
echo "==================================================\n";
