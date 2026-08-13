<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use App\Services\ProductImageMappingService;

$service = new ProductImageMappingService();
$allProducts = Product::all();
$allProductsArray = $allProducts->toArray();

echo "Loaded " . $allProducts->count() . " products from database.\n\n";

$testCases = [
    "Nitric Acid.jpg",
    "nitric-acid.jpg",
    "NITRIC_ACID.jpg",
    "Caustic Soda Flakes.jpg",
    "Caustic-Soda-Flakes.jpg",
    "Sodium_Hypochlorite.jpg",
    "Hydrochloric-Acid.jpg",
    "Unknown Product.jpg",
    "Acid.jpg",
];

echo "==================================================\n";
echo "1. TESTING BULK IMAGE MATCHING ON ORIGINAL FILENAMES\n";
echo "==================================================\n";

foreach ($testCases as $filename) {
    $norm = $service->normalizeFilename($filename);
    $result = $service->matchFilenameToProduct($filename, $allProductsArray, 'skip');
    
    echo sprintf(
        "File: %-25s | Norm: %-20s | Status: %-15s | Matched: %s\n",
        $filename,
        $norm,
        $result['status'],
        $result['product_name'] ?? '-'
    );
    if (!empty($result['candidates'])) {
        echo "   -> Ambiguous Candidates: " . implode(', ', array_slice($result['candidates'], 0, 5)) . "\n";
    }
}

echo "\n==================================================\n";
echo "2. TESTING SKIP VS REPLACE DUPLICATE IMAGE HANDLING\n";
echo "==================================================\n";

$sampleProduct = Product::first();
if ($sampleProduct) {
    $originalUrl = $sampleProduct->image_url;
    
    // Temporarily attach dummy valid image
    $sampleProduct->image_url = 'storage/uploads/products/dummy_test.jpg';
    $sampleProduct->save();

    $refreshedProducts = Product::all()->toArray();
    $testFile = $sampleProduct->name . ".jpg";

    $resultSkip = $service->matchFilenameToProduct($testFile, $refreshedProducts, 'skip');
    $resultReplace = $service->matchFilenameToProduct($testFile, $refreshedProducts, 'replace');

    echo "Product Name: " . $sampleProduct->name . "\n";
    echo "SKIP Mode Status    : " . $resultSkip['status'] . " (Msg: " . $resultSkip['message'] . ")\n";
    echo "REPLACE Mode Status : " . $resultReplace['status'] . " (Msg: " . $resultReplace['message'] . ")\n";

    // Restore original
    $sampleProduct->image_url = $originalUrl;
    $sampleProduct->save();
}

echo "\n==================================================\n";
echo "3. PRODUCT COUNT INTEGRITY CHECK\n";
echo "==================================================\n";
echo "Total Products Count: " . Product::count() . " (Must remain unchanged)\n";
echo "Done!\n";
