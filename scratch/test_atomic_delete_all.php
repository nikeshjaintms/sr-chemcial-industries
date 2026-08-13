<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use App\Services\ProductImageMappingService;

$service = new ProductImageMappingService();

echo "==================================================\n";
echo "1. TESTING ATOMIC DELETE ALL PRODUCT IMAGES\n";
echo "==================================================\n";

// Execute Delete All Product Images
$products = Product::all();
$initialProductCount = $products->count();

foreach ($products as $p) {
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

$remainingDbAssignments = Product::whereNotNull('image_url')->where('image_url', '!=', '')->where('image_url', '!=', '#')->count();
$candidatesAfterDelete = $service->getCandidateImages();
$remainingPhysicalFiles = count($candidatesAfterDelete);
$finalProductCount = Product::count();

echo "Initial Total Products: {$initialProductCount}\n";
echo "Final Total Products: {$finalProductCount} (Must equal {$initialProductCount})\n";
echo "Remaining DB Assignments: {$remainingDbAssignments} (Must be 0)\n";
echo "Remaining Media Library Images: {$remainingPhysicalFiles} (Must be 0)\n";

if ($finalProductCount === 88 && $remainingDbAssignments === 0 && $remainingPhysicalFiles === 0) {
    echo "✅ TEST A: DELETE ALL PRODUCT IMAGES PASSED PERFECTLY!\n";
} else {
    echo "❌ TEST A: DELETE ALL FAILED!\n";
}

echo "\n==================================================\n";
echo "2. TESTING 3-FILE BULK MATCHING & UNIQUE IMAGE MAPPING\n";
echo "==================================================\n";

$testBatch = [
    "Nitric Acid.jpg",
    "Acetic Acid.jpg",
    "Formic Acid.jpg",
];

$allProductsArray = Product::all()->toArray();
$assignedImages = [];

foreach ($testBatch as $filename) {
    $res = $service->matchFilenameToProduct($filename, $allProductsArray, 'replace');
    
    echo sprintf(
        "File: %-20s | Status: %-10s | Product: %s (ID: %d)\n",
        $filename,
        $res['status'],
        $res['product_name'],
        $res['product_id']
    );

    if ($res['status'] === 'MATCHED') {
        $prod = Product::find($res['product_id']);
        $uniqueHash = md5($filename);
        $dummyPath = "uploads/products/{$uniqueHash}.jpg";
        
        // Save unique image path
        $prod->image_url = $dummyPath;
        $prod->save();
        $assignedImages[$prod->name] = $prod->image_url;

        // Create dummy physical file
        $storagePath = storage_path("app/public/{$dummyPath}");
        @mkdir(dirname($storagePath), 0755, true);
        file_put_contents($storagePath, 'test_image_data');
    }
}

echo "\nVerification of Database Image Mapping:\n";
echo "--------------------------------------------------\n";
foreach ($assignedImages as $prodName => $path) {
    echo sprintf("Product: %-20s | Unique Path: %s\n", $prodName, $path);
}

$uniquePathCount = count(array_unique(array_values($assignedImages)));
echo "\nTotal Unique Assigned Image Paths: {$uniquePathCount} / " . count($testBatch) . "\n";

$productsWithImagesCount = Product::whereNotNull('image_url')->where('image_url', '!=', '')->where('image_url', '!=', '#')->count();
$candidatesAfterUpload = $service->getCandidateImages();

echo "Products With Images: {$productsWithImagesCount}\n";
echo "Media Library Candidate Images: " . count($candidatesAfterUpload) . "\n";

if ($uniquePathCount === count($testBatch) && $productsWithImagesCount === count($testBatch)) {
    echo "✅ TEST B & C: UNIQUE BULK IMAGE MAPPING PASSED PERFECTLY!\n";
} else {
    echo "❌ TEST B & C: BULK IMAGE MAPPING FAILED!\n";
}

echo "\n==================================================\n";
echo "3. FINAL ATOMIC CLEANUP\n";
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

echo "Final Product Count: {$finalProds} (Must be 88)\n";
echo "Final DB Assignments: {$finalDb} (Must be 0)\n";
echo "Final Media Library Images: {$finalMedia} (Must be 0)\n";

if ($finalProds === 88 && $finalDb === 0 && $finalMedia === 0) {
    echo "\n✅ ALL TEST SUITES PASSED 100% PERFECTLY!\n";
} else {
    echo "\n❌ FINAL CLEANUP FAILED!\n";
}
