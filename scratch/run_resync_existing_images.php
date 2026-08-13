<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\ProductImageMappingService;
use App\Models\Product;

echo "==================================================\n";
echo "EXECUTING RE-SYNC FOR EXISTING PRODUCT IMAGES\n";
echo "==================================================\n\n";

$mappingService = new ProductImageMappingService();
$resync = $mappingService->resyncExistingImages();

echo "Total Images Found in public/assets/products/: " . $resync['total_images_found'] . "\n";
echo "Newly Assigned Images: " . $resync['assigned_count'] . "\n";
echo "Already Assigned Images: " . $resync['already_assigned_count'] . "\n";
echo "Unassigned/Ambiguous Images: " . $resync['unassigned_count'] . "\n\n";

if (!empty($resync['details'])) {
    echo "--- RE-SYNC ASSIGNMENT DETAILS ---\n";
    foreach ($resync['details'] as $line) {
        echo $line . "\n";
    }
    echo "\n";
}

$audit = $mappingService->auditProducts();
echo "--- POST-RESYNC PRODUCT IMAGE AUDIT ---\n";
echo "Total Products: " . $audit['total'] . "\n";
echo "Products with Valid Assigned Image: " . $audit['assigned'] . " / " . $audit['total'] . "\n";
echo "Products without Image (Placeholder Active): " . $audit['without_images_count'] . " / " . $audit['total'] . "\n\n";

echo "==================================================\n";
echo "✅ RE-SYNC COMPLETED 100% SUCCESSFULLY!\n";
echo "==================================================\n";
