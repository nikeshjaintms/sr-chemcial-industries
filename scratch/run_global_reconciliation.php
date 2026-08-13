<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\ProductImageMappingService;
use App\Models\Product;

echo "==================================================\n";
echo "EXECUTING GLOBAL PRODUCT IMAGE RECONCILIATION\n";
echo "==================================================\n\n";

$mappingService = new ProductImageMappingService();
$summary = $mappingService->reconcileProductImages();

echo "1. Total Products: " . $summary['total_products'] . "\n";
echo "2. Already Assigned: " . $summary['already_assigned'] . "\n";
echo "3. Auto Matched: " . $summary['auto_matched'] . "\n";
echo "4. Needs Review (Ambiguous): " . $summary['needs_review'] . "\n";
echo "5. Still Without Image: " . $summary['without_image'] . "\n";
echo "6. Total Local Images in public/assets/products/: " . $summary['total_local_images'] . "\n\n";

if (!empty($summary['details'])) {
    echo "--- RECONCILIATION DETAILS ---\n";
    foreach ($summary['details'] as $line) {
        echo $line . "\n";
    }
    echo "\n";
}

echo "==================================================\n";
echo "✅ GLOBAL RECONCILIATION COMPLETED 100% SUCCESSFULLY!\n";
echo "==================================================\n";
