<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;

$targets = [
    189 => 'assets/products/Liquid-Chlorine.jpg',
    196 => 'assets/products/Methyl-Chloride.jpg',
    204 => 'assets/products/sodium-chlorate.jpg',
];

foreach ($targets as $id => $relPath) {
    $p = Product::find($id);
    if ($p) {
        $fullPath = str_replace('\\', '/', public_path($relPath));
        if (file_exists($fullPath)) {
            $p->image_url = $relPath;
            $p->save();
            echo "✅ Successfully assigned Product ID {$p->id} ('{$p->name}') → {$relPath}\n";
        } else {
            echo "❌ File not found for Product ID {$p->id}: {$fullPath}\n";
        }
    }
}

echo "\n--- VERIFYING AUDIT ---\n";
$service = new App\Services\ProductImageMappingService();
$audit = $service->auditProducts();
echo "Total Products: " . $audit['total'] . "\n";
echo "Assigned Products: " . $audit['assigned'] . "\n";
echo "Without Image Count: " . $audit['without_images_count'] . "\n\n";

echo "Products without images:\n";
foreach ($audit['without_images'] as $w) {
    echo "  • ID {$w->id}: {$w->name}\n";
}
