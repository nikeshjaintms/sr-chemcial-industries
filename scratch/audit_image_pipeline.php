<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Product;
use Illuminate\Support\Facades\DB;

echo "==================================================\n";
echo "1. DATABASE SCHEMA & IMAGE COLUMN INSPECTION\n";
echo "==================================================\n";

$columns = DB::select("SHOW COLUMNS FROM products");
$imageCols = [];
foreach ($columns as $c) {
    if (str_contains($c->Field, 'image') || str_contains($c->Field, 'photo') || str_contains($c->Field, 'img') || str_contains($c->Field, 'pic')) {
        $imageCols[] = $c->Field . " (" . $c->Type . ")";
    }
}
echo "Image-related columns in 'products' table:\n";
foreach ($imageCols as $col) {
    echo " - " . $col . "\n";
}

echo "\n==================================================\n";
echo "2. ALL 88 PRODUCTS DATABASE IMAGE_URL VALUE AUDIT\n";
echo "==================================================\n";

$products = Product::all();
echo "Total Products in DB: " . $products->count() . "\n\n";

$valueCounts = [];
$rawValues = [];

foreach ($products as $p) {
    $raw = $p->getRawOriginal('image_url');
    $accessor = $p->image_url;
    
    $rawValues[$p->id] = [
        'id' => $p->id,
        'name' => $p->name,
        'raw' => $raw,
        'accessor' => $accessor,
    ];

    $key = $raw ?? 'NULL';
    $valueCounts[$key] = ($valueCounts[$key] ?? 0) + 1;
}

echo "Top 15 Most Frequent Raw image_url Values in Database:\n";
echo "--------------------------------------------------\n";
arsort($valueCounts);
foreach (array_slice($valueCounts, 0, 15, true) as $val => $cnt) {
    echo sprintf("Count: %-3d | Value: %s\n", $cnt, $val);
}

echo "\nFirst 20 Product Database Image Values:\n";
echo "--------------------------------------------------\n";
foreach (array_slice($rawValues, 0, 20) as $item) {
    echo sprintf("ID: %-3d | Name: %-30s | Raw DB: %s\n", $item['id'], substr($item['name'], 0, 30), $item['raw'] ?? 'NULL');
}

echo "\n==================================================\n";
echo "3. PHYSICAL DIRECTORIES & FILE COUNTS\n";
echo "==================================================\n";

$dirs = [
    'storage/app/public/uploads/products' => storage_path('app/public/uploads/products'),
    'public/storage/uploads/products' => public_path('storage/uploads/products'),
    'public/assets/img/added/product' => public_path('assets/img/added/product'),
    'public/assets/img/added/OP' => public_path('assets/img/added/OP'),
];

foreach ($dirs as $label => $path) {
    $exists = file_exists($path);
    $count = 0;
    if ($exists && is_dir($path)) {
        $files = glob($path . '/*');
        $count = count(array_filter($files, 'is_file'));
    }
    echo sprintf("%-45s | Exists: %s | Files Count: %d\n", $label, $exists ? 'YES' : 'NO ', $count);
}
