<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\ProductFilenameMatcher;
use App\Models\Product;

echo "==================================================\n";
echo "TESTING BI-DIRECTIONAL CORE TOKEN SUBSET MATCHER\n";
echo "==================================================\n\n";

$matcher = new ProductFilenameMatcher();
$allProducts = Product::with('category')->get();

$testCases = [
    [
        'filename' => 'Caustic Soda Prills.jpg',
        'expected_product' => 'Caustic Soda Prills (NaOH)',
        'description' => 'Shorter filename without bracketed chemical formula (NaOH)'
    ],
    [
        'filename' => 'caustic-soda-prills.jpg',
        'expected_product' => 'Caustic Soda Prills (NaOH)',
        'description' => 'Shorter hyphenated filename without (NaOH)'
    ],
    [
        'filename' => 'Methylene Chloride MDC.jpg',
        'expected_product' => 'Methylene Chloride (MDC)',
        'description' => 'Longer filename with MDC abbreviation'
    ],
    [
        'filename' => 'Methylene Chloride.jpg',
        'expected_product' => 'Methylene Chloride (MDC)',
        'description' => 'Shorter filename without MDC abbreviation'
    ],
    [
        'filename' => 'Ortho Di Chloro Benzene.jpg',
        'expected_product' => 'Ortho Di Chloro Benzene (ODCB)',
        'description' => 'Shorter filename without ODCB abbreviation'
    ],
    [
        'filename' => 'Hydrogen Peroxide 50 Percent.jpg',
        'expected_product' => 'Hydrogen Peroxide',
        'description' => 'Longer filename with extra concentration words'
    ],
];

$passed = 0;
$total = count($testCases);

foreach ($testCases as $idx => $test) {
    $num = $idx + 1;
    $filename = $test['filename'];
    $expected = $test['expected_product'];
    $desc = $test['description'];

    $res = $matcher->matchFilenameToProduct($filename, $allProducts, 'image', 'replace');

    $isMatch = ($res['status'] === 'SUCCESS' || $res['status'] === 'MATCHED') && str_contains(strtolower($res['matched_product_name']), strtolower(explode(' (', $expected)[0]));
    
    if ($isMatch) {
        $passed++;
        echo "[TEST {$num}] ✅ PASSED: '{$filename}' → Matched: '{$res['matched_product_name']}' ({$res['matched_category']}) [Method: {$res['match_method']}]\n";
    } else {
        echo "[TEST {$num}] ❌ FAILED: '{$filename}' → Expected: '{$expected}', Status: {$res['status']}, Message: {$res['message']}\n";
    }
}

echo "\n==================================================\n";
echo "SUMMARY: {$passed} / {$total} TEST CASES PASSED!\n";
echo "==================================================\n";

if ($passed !== $total) {
    exit(1);
}
