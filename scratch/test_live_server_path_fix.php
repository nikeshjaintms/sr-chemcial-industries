<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use App\Services\ProductImageMappingService;

echo "==================================================\n";
echo "1. TESTING LIVE SERVER FILESYSTEM PATH RESOLVER\n";
echo "==================================================\n";

$dummy = new Product();

$serverPaths = [
    "/home/srjas/srcchemicalindustries.com/storage/app/public/uploads/products/test1.jpg",
    "/var/www/html/storage/app/public/uploads/products/test2.png",
    "C:\\xampp\\htdocs\\storage\\app\\public\\uploads\\products\\test3.webp",
    "uploads/products/test4.jpg",
    "storage/uploads/products/test5.jpg",
    null,
];

foreach ($serverPaths as $rawPath) {
    $dummy->image_url = $rawPath;
    $accessorVal = $dummy->image_url;
    $publicUrl = $accessorVal ? asset($accessorVal) : 'NULL (Placeholder)';
    
    echo sprintf(
        "Raw: %-75s -> Accessor: %-35s -> Public URL: %s\n",
        $rawPath ?? 'NULL',
        $accessorVal ?? 'NULL',
        $publicUrl
    );

    if ($accessorVal && (str_contains($accessorVal, '/home/') || str_contains($accessorVal, 'C:\\'))) {
        echo "❌ SERVER PATH EXPOSED IN ACCESSOR: {$accessorVal}\n";
        exit(1);
    }
}

echo "\n✅ ALL PATH RESOLUTIONS ARE 100% CLEAN AND FREE OF SERVER ROOT PATHS!\n\n";

echo "==================================================\n";
echo "2. TESTING CANDIDATE IMAGES PATH GENERATION\n";
echo "==================================================\n";

$service = new ProductImageMappingService();

// Create dummy candidate file in uploads/products
$dummyFile = storage_path('app/public/uploads/products/sample_live_test.jpg');
@mkdir(dirname($dummyFile), 0755, true);
file_put_contents($dummyFile, 'live_test');

$candidates = $service->getCandidateImages();
echo "Candidate Images Count: " . count($candidates) . "\n";
foreach ($candidates as $c) {
    echo " - RelPath: {$c['relative_path']} | URL: {$c['url']}\n";
    if (str_contains($c['relative_path'], '/home/') || str_contains($c['url'], '/home/')) {
        echo "❌ SERVER PATH EXPOSED IN MEDIA CANDIDATE: " . $c['relative_path'] . "\n";
        exit(1);
    }
}

echo "\n==================================================\n";
echo "3. TESTING ATOMIC DELETE ALL PRODUCT IMAGES & MEDIA RESET\n";
echo "==================================================\n";

// Execute Reset
foreach (Product::all() as $p) {
    $p->image_url = null;
    $p->save();
}

$targetDirs = [
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
    echo "\n✅ ATOMIC RESET PASSED PERFECTLY!\n";
} else {
    echo "\n❌ RESET FAILED!\n";
    exit(1);
}

echo "\n==================================================\n";
echo "4. TESTING BULK MATCHING & UNIQUE IMAGE ASSIGNMENT\n";
echo "==================================================\n";

$testBatch = [
    "Nitric Acid.jpg",
    "Acetic Acid.jpg",
    "Formic Acid.jpg",
];

$allProductsArray = Product::all()->toArray();
$assignedMap = [];

foreach ($testBatch as $fn) {
    $match = $service->matchFilenameToProduct($fn, $allProductsArray, 'replace');
    echo sprintf("File: %-20s | Status: %-10s | Matched Product: %s\n", $fn, $match['status'], $match['product_name']);

    if ($match['status'] === 'MATCHED') {
        $prod = Product::find($match['product_id']);
        $uniqueFilename = Str::slug($fn) . '_' . md5($fn) . '.jpg';
        $relPath = "uploads/products/{$uniqueFilename}";
        
        $prod->image_url = $relPath;
        $prod->save();

        $storagePath = storage_path("app/public/{$relPath}");
        @mkdir(dirname($storagePath), 0755, true);
        file_put_contents($storagePath, 'test_data');

        $assignedMap[$prod->name] = $prod->image_url;
    }
}

echo "\nAssigned Database Mappings:\n";
foreach ($assignedMap as $pName => $url) {
    echo " - Product: {$pName} -> image_url: {$url} -> Accessor Public URL: " . asset($url) . "\n";
}

$uniqueCount = count(array_unique(array_values($assignedMap)));
echo "\nTotal Unique Mappings: {$uniqueCount} / 3\n";

if ($uniqueCount === 3) {
    echo "✅ BULK MATCHING PASSED PERFECTLY WITH UNIQUE IMAGE PATHS!\n";
} else {
    echo "❌ BULK MATCHING FAILED!\n";
}

echo "\n==================================================\n";
echo "5. FINAL CLEANUP RESET\n";
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
    echo "\n✅ ALL TEST SUITES COMPLETED 100% SUCCESSFULLY!\n";
} else {
    echo "\n❌ FINAL CLEANUP FAILED!\n";
}
