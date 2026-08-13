<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use App\Services\ProductImageMappingService;

echo "==================================================\n";
echo "1. VERIFYING IMAGE PATH ACCESSOR NORMALIZATION\n";
echo "==================================================\n";

$dummy = new Product();

$dummy->image_url = 'uploads/products/test1.jpg';
echo "DB: uploads/products/test1.jpg -> Accessor: " . $dummy->image_url . "\n";

$dummy->image_url = 'storage/uploads/products/test2.jpg';
echo "DB: storage/uploads/products/test2.jpg -> Accessor: " . $dummy->image_url . "\n";

$dummy->image_url = 'public/storage/uploads/products/test3.jpg';
echo "DB: public/storage/uploads/products/test3.jpg -> Accessor: " . $dummy->image_url . "\n";

$dummy->image_url = null;
echo "DB: null -> Accessor: " . $dummy->image_url . "\n";

echo "\n==================================================\n";
echo "2. VERIFYING MSDS & SPECIFICATION FIELD INTEGRITY\n";
echo "==================================================\n";

$initialProducts = Product::all();
$initialCount = $initialProducts->count();
$msdsCountInitial = Product::whereNotNull('msds_url')->where('msds_url', '!=', '#')->count();
$specCountInitial = Product::whereNotNull('specification_url')->where('specification_url', '!=', '#')->count();

echo "Initial Total Products: {$initialCount}\n";
echo "Products with MSDS: {$msdsCountInitial}\n";
echo "Products with Specification: {$specCountInitial}\n\n";

echo "==================================================\n";
echo "3. TESTING BULK AUTO-MATCHING ON ORIGINAL FILENAMES\n";
echo "==================================================\n";

$service = new ProductImageMappingService();
$allProductsArray = $initialProducts->toArray();

$testFiles = [
    "Nitric Acid.jpg" => "Nitric Acid",
    "nitric-acid.jpg" => "Nitric Acid",
    "NITRIC_ACID.jpg" => "Nitric Acid",
    "Caustic Soda Flakes.jpg" => "Caustic Soda Flakes (NaOH)",
    "Caustic-Soda-Flakes.png" => "Caustic Soda Flakes (NaOH)",
    "Sodium_Hypochlorite.webp" => "Sodium Hypochlorite (NaOCl)",
    "Hydrochloric-Acid.jpg" => "Hydrochloric Acid (HCl)",
    "Unknown Chemical.jpg" => null,
    "Acid.jpg" => null
];

foreach ($testFiles as $filename => $expectedName) {
    $res = $service->matchFilenameToProduct($filename, $allProductsArray, 'replace');
    echo sprintf(
        "File: %-25s | Status: %-15s | Matched: %s\n",
        $filename,
        $res['status'],
        $res['product_name'] ?? '—'
    );
}

echo "\n==================================================\n";
echo "4. FINAL INTEGRITY CHECK\n";
echo "==================================================\n";

$finalCount = Product::count();
$msdsCountFinal = Product::whereNotNull('msds_url')->where('msds_url', '!=', '#')->count();
$specCountFinal = Product::whereNotNull('specification_url')->where('specification_url', '!=', '#')->count();

echo "Final Product Count: {$finalCount} (Must equal {$initialCount})\n";
echo "Final MSDS Count: {$msdsCountFinal} (Must equal {$msdsCountInitial})\n";
echo "Final Specification Count: {$specCountFinal} (Must equal {$specCountInitial})\n";

if ($initialCount === $finalCount && $msdsCountInitial === $msdsCountFinal && $specCountInitial === $specCountFinal) {
    echo "\n✅ ALL INTEGRITY CHECKS PASSED PERFECTLY!\n";
} else {
    echo "\n❌ INTEGRITY CHECK FAILURE!\n";
}
