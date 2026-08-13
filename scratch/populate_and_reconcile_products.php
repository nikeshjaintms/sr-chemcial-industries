<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\ProductImageMappingService;
use App\Models\Product;

$targetDir = public_path('assets/products');
if (!file_exists($targetDir)) {
    mkdir($targetDir, 0755, true);
}

$specDir = public_path('assets/pdf/Specification');
if (file_exists($specDir)) {
    $files = scandir($specDir);
    foreach ($files as $f) {
        $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
        if (in_array($ext, ['png', 'jpg', 'jpeg', 'webp'])) {
            if (!str_contains($f, 'GNFC')) {
                copy($specDir . '/' . $f, $targetDir . '/' . $f);
                echo "Copied to public/assets/products/: {$f}\n";
            }
        }
    }
}

echo "\n--- RUNNING RECONCILIATION --- \n";
$mappingService = new ProductImageMappingService();
$summary = $mappingService->reconcileProductImages();

echo "Total Products: " . $summary['total_products'] . "\n";
echo "Already Assigned: " . $summary['already_assigned'] . "\n";
echo "Auto Matched: " . $summary['auto_matched'] . "\n";
echo "Needs Review: " . $summary['needs_review'] . "\n";
echo "Still Without Image: " . $summary['without_image'] . "\n";
echo "Total Local Images: " . $summary['total_local_images'] . "\n\n";

foreach ($summary['details'] as $detail) {
    echo $detail . "\n";
}
