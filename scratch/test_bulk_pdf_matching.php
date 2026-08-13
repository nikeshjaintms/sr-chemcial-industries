<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use App\Services\BulkPdfMatchingService;

$service = new BulkPdfMatchingService();
$allProducts = Product::all();

echo "Loaded " . $allProducts->count() . " products from database.\n\n";

$testCases = [
    "Nitric Acid.pdf",
    "nitric-acid.pdf",
    "NITRIC_ACID.pdf",
    "Caustic Soda Flakes.pdf",
    "Hydrochloric-Acid.pdf",
    "Sodium_Hypochlorite.pdf",
    "Unknown Product.pdf",
    "Acid.pdf",
];

echo "==================================================\n";
echo "1. TESTING FILENAME NORMALIZATION & MATCHING\n";
echo "==================================================\n";

foreach ($testCases as $filename) {
    $norm = $service->normalizeFilename($filename);
    $result = $service->matchFilenameToProduct($filename, $allProducts, 'msds', 'skip');
    
    echo sprintf(
        "File: %-25s | Norm: %-20s | Status: %-15s | Matched: %s\n",
        $filename,
        $norm,
        $result['status'],
        $result['matched_product_name'] ?? '-'
    );
    if (!empty($result['candidates'])) {
        echo "   -> Candidates: " . implode(', ', array_slice($result['candidates'], 0, 5)) . "\n";
    }
}

echo "\n==================================================\n";
echo "2. TESTING EXISTING PDF ATTACHMENT / SKIP / REPLACE HANDLING\n";
echo "==================================================\n";

// Find a test product
$sampleProduct = Product::first();
if ($sampleProduct) {
    $originalMsds = $sampleProduct->msds_url;
    
    // Temporarily attach dummy MSDS
    $sampleProduct->msds_url = 'storage/uploads/msds/dummy_test.pdf';
    $sampleProduct->save();

    // Re-fetch product list
    $refreshedProducts = Product::all();

    $testFile = $sampleProduct->name . ".pdf";
    $resultSkip = $service->matchFilenameToProduct($testFile, $refreshedProducts, 'msds', 'skip');
    $resultReplace = $service->matchFilenameToProduct($testFile, $refreshedProducts, 'msds', 'replace');

    echo "Product Name: " . $sampleProduct->name . "\n";
    echo "SKIP Mode Status    : " . $resultSkip['status'] . " (Msg: " . $resultSkip['message'] . ")\n";
    echo "REPLACE Mode Status : " . $resultReplace['status'] . " (Msg: " . $resultReplace['message'] . ")\n";

    // Restore original
    $sampleProduct->msds_url = $originalMsds;
    $sampleProduct->save();
}

echo "\n==================================================\n";
echo "3. PRODUCT COUNT INTEGRITY CHECK\n";
echo "==================================================\n";
echo "Total Products Count: " . Product::count() . " (Must remain unchanged)\n";
echo "Done!\n";
