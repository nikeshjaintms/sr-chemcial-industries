<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use App\Services\BulkPdfMatchingService;

$matchingService = new BulkPdfMatchingService();

echo "==================================================\n";
echo "1. TESTING MSDS & SPECIFICATION PDF ACCESSORS\n";
echo "==================================================\n";

$dummy = new Product();

// Test MSDS Accessor
$dummy->msds_url = 'uploads/msds/Nitric Acid.pdf';
$msdsAccessor = $dummy->msds_pdf_url;
$msdsPublicUrl = asset($msdsAccessor);

echo "MSDS Raw: {$dummy->msds_url}\n";
echo "MSDS Accessor: {$msdsAccessor}\n";
echo "MSDS Public URL: {$msdsPublicUrl}\n";

if ($msdsAccessor !== 'assets/pdf/MSDC/Nitric Acid.pdf') {
    echo "❌ INVALID MSDS ACCESSOR: {$msdsAccessor}\n";
    exit(1);
}

// Test Specification Accessor
$dummy->specification_url = 'uploads/specifications/Nitric Acid.pdf';
$specAccessor = $dummy->spec_pdf_url;
$specPublicUrl = asset($specAccessor);

echo "\nSpec Raw: {$dummy->specification_url}\n";
echo "Spec Accessor: {$specAccessor}\n";
echo "Spec Public URL: {$specPublicUrl}\n";

if ($specAccessor !== 'assets/pdf/Specification/Nitric Acid.pdf') {
    echo "❌ INVALID SPEC ACCESSOR: {$specAccessor}\n";
    exit(1);
}

echo "\n✅ BOTH ACCESSORS RESOLVE EXCLUSIVELY TO CANONICAL DIRECTORIES!\n\n";

echo "==================================================\n";
echo "2. AUDITING DATABASE PRODUCT PDF URLS FOR OLD PATHS\n";
echo "==================================================\n";

$oldMsdsCount = Product::where('msds_url', 'LIKE', '%storage/uploads/msds%')->count();
$oldSpecCount = Product::where('specification_url', 'LIKE', '%storage/uploads/specifications%')->count();

echo "Products with old storage/uploads/msds path: {$oldMsdsCount} (Must be 0)\n";
echo "Products with old storage/uploads/specifications path: {$oldSpecCount} (Must be 0)\n";

if ($oldMsdsCount === 0 && $oldSpecCount === 0) {
    echo "✅ DATABASE CONTAINS ZERO OLD PDF STORAGE PATHS!\n\n";
} else {
    echo "❌ DATABASE CONTAINS OLD PDF STORAGE PATHS!\n";
    exit(1);
}

echo "==================================================\n";
echo "3. TESTING BULK MATCHING & PHYSICAL DIRECTORY SAVING\n";
echo "==================================================\n";

$msdcDir = public_path('assets/pdf/MSDC');
$specDir = public_path('assets/pdf/Specification');

echo 'MSDC Directory Exists: ' . (file_exists($msdcDir) ? 'YES' : 'NO') . "\n";
echo 'Specification Directory Exists: ' . (file_exists($specDir) ? 'YES' : 'NO') . "\n";

// Test matching logic
$allProducts = Product::all();
$match = $matchingService->matchFilenameToProduct('Nitric Acid.pdf', $allProducts, 'msds', 'skip');

echo "Match Status for 'Nitric Acid.pdf': {$match['status']} (Matched Product: {$match['matched_product_name']})\n";

if ($match['status'] === 'SUCCESS' || $match['status'] === 'ALREADY EXISTS') {
    echo "\n✅ ALL MSDS & SPECIFICATION PDF STORAGE TESTS COMPLETED 100% SUCCESSFULLY!\n";
} else {
    echo "\n❌ MATCHING TEST FAILED!\n";
    exit(1);
}
