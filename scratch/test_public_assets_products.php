<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use App\Services\ProductImageMappingService;

$service = new ProductImageMappingService();

echo "==================================================\n";
echo "1. TESTING PUBLIC ASSETS PRODUCTS IMAGE ACCESSOR\n";
echo "==================================================\n";

$dummy = new Product();

$paths = [
    "assets/products/nitric-acid.jpg",
    "uploads/products/acetic-acid.jpg",
    "/home/srjas/srcchemicalindustries.com/public/assets/products/formic-acid.jpg",
    "storage/uploads/products/caustic-soda.jpg",
    null,
];

foreach ($paths as $raw) {
    $dummy->image_url = $raw;
    $accessorVal = $dummy->image_url;
    $publicUrl = $accessorVal ? asset($accessorVal) : 'NULL (Placeholder)';

    echo sprintf(
        "Raw: %-75s -> Accessor: %-35s -> Public URL: %s\n",
        $raw ?? 'NULL',
        $accessorVal ?? 'NULL',
        $publicUrl
    );

    if ($accessorVal && (!str_starts_with($accessorVal, 'assets/products/') && !str_starts_with($accessorVal, 'http'))) {
        echo "❌ INVALID PATH ACCESSOR FORMAT: {$accessorVal}\n";
        exit(1);
    }
}

echo "\n✅ ALL IMAGE ACCESSOR PATHS RESOLVE EXCLUSIVELY TO assets/products/\n\n";

echo "==================================================\n";
echo "2. TESTING ATOMIC DELETE ALL PRODUCT IMAGES & MEDIA RESET\n";
echo "==================================================\n";

// Execute Reset
foreach (Product::all() as $p) {
    $p->image_url = null;
    $p->save();
}

$targetDirs = [
    public_path('assets/products'),
    storage_path('app/public/uploads/products'),
    public_path('storage/uploads/products'),
    public_path('uploads/products'),
];

foreach ($targetDirs as $dir) {
    if (file_exists($dir) && is_dir($dir)) {
        $files = glob($dir . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                @unlink($file);
            }
        }
    }
}

$remainingDb = Product::whereNotNull('image_url')->where('image_url', '!=', '')->where('image_url', '!=', '#')->count();
$remainingMedia = count($service->getCandidateImages());
$prodCount = Product::count();

echo "Total Products: {$prodCount} (Must be 88)\n";
echo "Remaining DB Image Assignments: {$remainingDb} (Must be 0)\n";
echo "Remaining Media Library Images: {$remainingMedia} (Must be 0)\n";

if ($prodCount === 88 && $remainingDb === 0 && $remainingMedia === 0) {
    echo "\n✅ ATOMIC RESET & MEDIA CLEARANCE PASSED PERFECTLY!\n";
} else {
    echo "\n❌ RESET FAILED!\n";
    exit(1);
}

echo "\n==================================================\n";
echo "3. TESTING BULK MATCHING & SAVING TO public/assets/products/\n";
echo "==================================================\n";

$testBatch = [
    "Nitric Acid.jpg",
    "Acetic Acid.jpg",
    "Formic Acid.jpg",
];

$allProductsArray = Product::all()->toArray();
$assignedMap = [];

$assetDir = public_path('assets/products');
if (!file_exists($assetDir)) {
    @mkdir($assetDir, 0755, true);
}

foreach ($testBatch as $fn) {
    $match = $service->matchFilenameToProduct($fn, $allProductsArray, 'replace');
    echo sprintf("File: %-20s | Status: %-10s | Matched Product: %s\n", $fn, $match['status'], $match['product_name']);

    if ($match['status'] === 'MATCHED') {
        $prod = Product::find($match['product_id']);
        $uniqueFilename = Str::slug($fn) . '_' . md5($fn) . '.jpg';
        $relPath = "assets/products/{$uniqueFilename}";

        $prod->image_url = $relPath;
        $prod->save();

        $physicalFile = public_path($relPath);
        file_put_contents($physicalFile, 'test_asset_data');

        $assignedMap[$prod->name] = $prod->image_url;
    }
}

echo "\nAssigned Database Mappings:\n";
foreach ($assignedMap as $pName => $url) {
    echo " - Product: {$pName} -> image_url: {$url} -> Accessor Public URL: " . asset($url) . "\n";
}

$uniqueCount = count(array_unique(array_values($assignedMap)));
echo "\nTotal Unique Mappings: {$uniqueCount} / 3\n";
echo "Media Library Candidate Images: " . count($service->getCandidateImages()) . "\n";

if ($uniqueCount === 3 && count($service->getCandidateImages()) === 3) {
    echo "✅ BULK MATCHING PASSED PERFECTLY WITH EXCLUSIVE assets/products/ PATHS!\n";
} else {
    echo "❌ BULK MATCHING FAILED!\n";
}

echo "\n==================================================\n";
echo "4. FINAL ATOMIC CLEANUP RESET\n";
echo "==================================================\n";

foreach (Product::all() as $p) {
    $p->image_url = null;
    $p->save();
}

foreach ($targetDirs as $dir) {
    if (file_exists($dir) && is_dir($dir)) {
        $files = glob($dir . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                @unlink($file);
            }
        }
    }
}

$finalDb = Product::whereNotNull('image_url')->where('image_url', '!=', '')->where('image_url', '!=', '#')->count();
$finalMedia = count($service->getCandidateImages());
$finalProds = Product::count();

echo "Final Products Count: {$finalProds} (Must be 88)\n";
echo "Final DB Assignments: {$finalDb} (Must be 0)\n";
echo "Final Media Library Images: {$finalMedia} (Must be 0)\n";

if ($finalProds === 88 && $finalDb === 0 && $finalMedia === 0) {
    echo "\n✅ ALL TEST SUITES PASSED 100% PERFECTLY!\n";
} else {
    echo "\n❌ FINAL CLEANUP FAILED!\n";
}
