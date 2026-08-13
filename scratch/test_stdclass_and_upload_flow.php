<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\ProductFilenameMatcher;
use App\Services\ProductImageMappingService;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

echo "==================================================\n";
echo "1. TESTING STDCLASS OBJECT COMPATIBILITY\n";
echo "==================================================\n\n";

$matcher = new ProductFilenameMatcher();

// Fetch raw DB stdClass objects
$stdClassProducts = DB::table('products')
    ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
    ->select('products.*', 'categories.name as category_name')
    ->get();

echo "Loaded " . $stdClassProducts->count() . " stdClass product records from database.\n";

try {
    $res1 = $matcher->matchFilenameToProduct('caustic-soda-prills-naoh.jpg', $stdClassProducts, 'image', 'replace');
    echo "✅ stdClass Match Success: " . $res1['matched_product_name'] . " (" . $res1['matched_category'] . ")\n";
} catch (\Throwable $e) {
    echo "❌ stdClass Error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n==================================================\n";
echo "2. TESTING ARRAY DATA COMPATIBILITY\n";
echo "==================================================\n\n";

$arrayProducts = Product::with('category')->get()->toArray();
echo "Loaded " . count($arrayProducts) . " array product records.\n";

try {
    $res2 = $matcher->matchFilenameToProduct('caustic-soda-prills-naoh.jpg', collect($arrayProducts), 'image', 'replace');
    echo "✅ Array Match Success: " . $res2['matched_product_name'] . " (" . $res2['matched_category'] . ")\n";
} catch (\Throwable $e) {
    echo "❌ Array Error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n==================================================\n";
echo "3. TESTING ELOQUENT COLLECTION COMPATIBILITY\n";
echo "==================================================\n\n";

$eloquentProducts = Product::with('category')->get();
echo "Loaded " . $eloquentProducts->count() . " Eloquent product records.\n";

try {
    $res3 = $matcher->matchFilenameToProduct('caustic-soda-prills-naoh.jpg', $eloquentProducts, 'image', 'replace');
    echo "✅ Eloquent Match Success: " . $res3['matched_product_name'] . " (" . $res3['matched_category'] . ")\n";
} catch (\Throwable $e) {
    echo "❌ Eloquent Error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n==================================================\n";
echo "4. TESTING PRODUCT IMAGE MAPPING SERVICE MATCH\n";
echo "==================================================\n\n";

$mappingService = new ProductImageMappingService();
$res4 = $mappingService->matchFilenameToProduct('liquid-chlorine-cl2.jpg');
echo "MappingService Status: " . $res4['status'] . "\n";
echo "MappingService Product: " . $res4['product_name'] . "\n";
echo "MappingService Category: " . $res4['matched_category'] . "\n";

if ($res4['status'] !== 'MATCHED' && $res4['status'] !== 'EXISTING IMAGE') {
    echo "❌ MAPPING SERVICE MATCH FAILED!\n";
    exit(1);
}

echo "\n==================================================\n";
echo "✅ ALL STDCLASS, ARRAY, AND ELOQUENT TESTS PASSED 100%!\n";
echo "==================================================\n";
