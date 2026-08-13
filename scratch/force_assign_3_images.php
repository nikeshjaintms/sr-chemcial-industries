<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;

$map = [
    189 => 'assets/products/Liquid-Chlorine.jpg',
    196 => 'assets/products/Methyl-Chloride.jpg',
    204 => 'assets/products/sodium-chlorate.jpg',
];

echo "==================================================\n";
echo "FORCE ASSIGNING THE 3 SPECIFIC PRODUCT IMAGES\n";
echo "==================================================\n\n";

foreach ($map as $id => $relPath) {
    $p = Product::find($id);
    if ($p) {
        $p->image_url = $relPath;
        $p->save();
        echo "✅ Assigned Product ID {$p->id} ('{$p->name}') → image_url: '{$p->image_url}'\n";
    } else {
        echo "❌ Product ID {$id} not found!\n";
    }
}

echo "\n==================================================\n";
echo "AUDIT AFTER FORCE ASSIGNMENT\n";
echo "==================================================\n\n";

$service = new App\Services\ProductImageMappingService();
$audit = $service->auditProducts();

echo "• Total Products in Database: " . $audit['total'] . "\n";
echo "• Products Already Assigned with Valid Physical Files: " . $audit['assigned'] . "\n";
echo "• Still Without Images: " . $audit['without_images_count'] . "\n\n";

echo "Products still without images:\n";
foreach ($audit['without_images'] as $w) {
    echo "  • ID {$w->id}: '{$w->name}' ({$w->category->name})\n";
}
