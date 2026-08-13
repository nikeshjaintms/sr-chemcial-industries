<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\ProductFilenameMatcher;
use App\Models\Product;

echo "==================================================\n";
echo "TESTING UNIFIED PRODUCT FILENAME MATCHER SERVICE\n";
echo "==================================================\n\n";

$matcher = new ProductFilenameMatcher();
$allProducts = Product::all();

$testCases = [
    ['filename' => 'Nitric Acid.jpg',          'expected_status' => 'SUCCESS',   'expected_product_contains' => 'Nitric Acid'],
    ['filename' => 'nitric-acid.jpg',          'expected_status' => 'SUCCESS',   'expected_product_contains' => 'Nitric Acid'],
    ['filename' => 'NITRIC_ACID.PNG',          'expected_status' => 'SUCCESS',   'expected_product_contains' => 'Nitric Acid'],
    ['filename' => 'Phosphoric-Acid.jpg',      'expected_status' => 'SUCCESS',   'expected_product_contains' => 'Phosphoric Acid'],
    ['filename' => 'Phosphoric_Acid.pdf',      'expected_status' => 'SUCCESS',   'expected_product_contains' => 'Phosphoric Acid'],
    ['filename' => 'Hydrochloric-Acid.pdf',    'expected_status' => 'SUCCESS',   'expected_product_contains' => 'Hydrochloric Acid'],
    ['filename' => 'Sodium_Hypochlorite.jpg',  'expected_status' => 'SUCCESS',   'expected_product_contains' => 'Sodium Hypochlorite'],
    ['filename' => 'Unknown-Product.jpg',      'expected_status' => 'NOT FOUND', 'expected_product_contains' => null],
    ['filename' => 'Acid.jpg',                 'expected_status' => 'AMBIGUOUS', 'expected_product_contains' => null],
    ['filename' => 'Nitric Acid (1).jpg',      'expected_status' => 'SUCCESS',   'expected_product_contains' => 'Nitric Acid'],
];

$passedCount = 0;
$totalCount = count($testCases);

foreach ($testCases as $idx => $test) {
    $num = $idx + 1;
    $filename = $test['filename'];
    $expectedStatus = $test['expected_status'];
    $expectedSubstr = $test['expected_product_contains'];

    $result = $matcher->matchFilenameToProduct($filename, $allProducts, 'image', 'replace');

    $actualStatus = $result['status'];
    $actualProduct = $result['matched_product_name'];

    $statusMatch = ($actualStatus === $expectedStatus);
    $productMatch = ($expectedSubstr === null) ? ($actualProduct === null) : ($actualProduct !== null && str_contains(strtolower($actualProduct), strtolower($expectedSubstr)));

    if ($statusMatch && $productMatch) {
        $passedCount++;
        echo "[TEST {$num}] ✅ PASSED: '{$filename}' → Status: {$actualStatus}" . ($actualProduct ? " (Product: {$actualProduct})" : "") . "\n";
    } else {
        echo "[TEST {$num}] ❌ FAILED: '{$filename}'\n";
        echo "   Expected Status: {$expectedStatus}, Got: {$actualStatus}\n";
        echo "   Expected Product Substr: " . ($expectedSubstr ?? 'NONE') . ", Got: " . ($actualProduct ?? 'NONE') . "\n";
        echo "   Message: {$result['message']}\n";
    }
}

echo "\n==================================================\n";
echo "SUMMARY: {$passedCount} / {$totalCount} TEST CASES PASSED!\n";
echo "==================================================\n";

if ($passedCount !== $totalCount) {
    exit(1);
}
