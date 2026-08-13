<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;

$products = Product::all();

echo "==================================================\n";
echo "PRINTING ALL 89 DB PRODUCT IMAGE_URL VALUES\n";
echo "==================================================\n\n";

foreach ($products as $p) {
    $raw = $p->getRawOriginal('image_url');
    $physicalExists = !empty($raw) && file_exists(public_path(ltrim($raw, '/')));
    echo "ID: " . str_pad($p->id, 3) . " | Name: " . str_pad("'" . $p->name . "'", 40) . " | Raw DB: " . var_export($raw, true) . " | Exists: " . ($physicalExists ? "YES" : "NO") . "\n";
}
