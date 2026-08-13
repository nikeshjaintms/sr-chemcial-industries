<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\ProductImageMappingService;
use App\Models\Product;

echo "==================================================\n";
echo "RUNNING FULL IMAGE RESYNC & RECONCILIATION FOR ALL 89 PRODUCTS\n";
echo "==================================================\n\n";

$mappingService = new ProductImageMappingService();
$resync = $mappingService->resyncExistingImages();
$audit = $mappingService->auditProducts();

echo "• Total Products in DB: " . $audit['total'] . "\n";
echo "• Products Already Assigned with Valid Files: " . $audit['assigned'] . "\n";
echo "• Still Without Valid Images: " . $audit['without_images_count'] . "\n\n";

echo "--- PRODUCTS STILL WITHOUT PHYSICAL IMAGES ---\n";
foreach ($audit['without_images'] as $p) {
    echo "  • ID {$p->id}: '{$p->name}' ({$p->category->name})\n";
}
