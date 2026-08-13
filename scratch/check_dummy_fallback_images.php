<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;

$products = Product::all();
$fallbackCount = 0;

echo "==================================================\n";
echo "INSPECTING DUMMY FALLBACK IMAGES IN DATABASE\n";
echo "==================================================\n\n";

foreach ($products as $p) {
    $raw = $p->getRawOriginal('image_url');
    if ($raw === 'assets/img/added/Chemical Supply Solutions.jpg' || str_contains($raw, 'assets/img/added/')) {
        $fallbackCount++;
        echo "Product ID: {$p->id} | Name: {$p->name} | Raw DB image_url: {$raw}\n";
    }
}

echo "\nTotal Products with Dummy Fallback Image in DB: {$fallbackCount} / " . $products->count() . "\n";
