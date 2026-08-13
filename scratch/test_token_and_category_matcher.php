<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\ProductFilenameMatcher;
use App\Models\Product;

echo "==================================================\n";
echo "TESTING ENHANCED TOKEN & CATEGORY FILENAME MATCHER\n";
echo "==================================================\n\n";

$matcher = new ProductFilenameMatcher();
$allProducts = Product::with('category')->get();

$testCases = [
    ['filename' => 'caustic-soda-prills-naoh.jpg',         'expected_status' => 'SUCCESS',   'expected_product_contains' => 'Caustic Soda Prills'],
    ['filename' => 'caustic_soda_prills_naoh.png',         'expected_status' => 'SUCCESS',   'expected_product_contains' => 'Caustic Soda Prills'],
    ['filename' => 'liquid-chlorine-cl2.jpg',               'expected_status' => 'SUCCESS',   'expected_product_contains' => 'Liquid Chlorine'],
    ['filename' => 'sodium-hypochlorite-naocl.jpg',         'expected_status' => 'SUCCESS',   'expected_product_contains' => 'Sodium Hypochlorite'],
    ['filename' => 'calcium-chloride-prills-powder.jpg',    'expected_status' => 'SUCCESS',   'expected_product_contains' => 'Calcium Chloride Prills/Powder'],
    ['filename' => 'sulfuric-acid-commercial-grade.jpg',    'expected_status' => 'SUCCESS',   'expected_product_contains' => 'Commercial Grade'],
    ['filename' => 'sulfuric-acid-battery-grade.jpg',       'expected_status' => 'SUCCESS',   'expected_product_contains' => 'Battery Grade'],
    ['filename' => 'iso-propyl-alcohol-ipa.jpg',            'expected_status' => 'SUCCESS',   'expected_product_contains' => 'Iso Propyl Alcohol'],
    ['filename' => 'carbon-tetrachloride-ccl4.jpg',         'expected_status' => 'SUCCESS',   'expected_product_contains' => 'Carbon Tetrachloride'],
    ['filename' => 'unknown-chemical.jpg',                 'expected_status' => 'NOT FOUND', 'expected_product_contains' => null],
    ['filename' => 'acid.jpg',                             'expected_status' => 'AMBIGUOUS', 'expected_product_contains' => null],
    ['filename' => 'chloroform.jpg',                       'expected_status' => 'AMBIGUOUS', 'expected_product_contains' => null],
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
    $actualCategory = $result['matched_category'];
    $matchMethod = $result['match_method'];
    $confidence = $result['confidence'];

    $statusMatch = ($actualStatus === $expectedStatus);
    $productMatch = ($expectedSubstr === null) ? ($actualProduct === null) : ($actualProduct !== null && str_contains(strtolower($actualProduct), strtolower($expectedSubstr)));

    if ($statusMatch && $productMatch) {
        $passedCount++;
        echo "[TEST {$num}] ✅ PASSED: '{$filename}' → Status: {$actualStatus}" . ($actualProduct ? " (Product: {$actualProduct} | Category: {$actualCategory} | Method: {$matchMethod} | Confidence: {$confidence})" : "") . "\n";
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
