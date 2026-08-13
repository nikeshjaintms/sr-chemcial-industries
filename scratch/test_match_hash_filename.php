<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\ProductFilenameMatcher;
use App\Models\Product;

$matcher = new ProductFilenameMatcher();
$allProducts = Product::with('category')->get();

$filesToTest = [
    'Caustic Soda Prills.jpg',
    'caustic-soda-prills.jpg',
    'caustic-soda-prills_NS2ZBntq.jpg',
    'liquid-chlorine_4kFtOB4U.jpg',
    'sodium-hypochlorite_ZmJmYDXP.jpg',
    'hydrogen-peroxide_iWLc20hI.jpg',
    'carbon-tetrachloride_UlF1wNGU.jpg',
    'iso-propyl-alcohol_GLhhf7kL.jpg',
    'ortho-di-chloro-benzene_hHvsipFf.jpg',
    'calcium-chloride-prills-powder_5rRbCBsp.jpg',
];

echo "==================================================\n";
echo "TESTING MATCHING FOR HASHED AND CORE FILENAMES\n";
echo "==================================================\n\n";

foreach ($filesToTest as $file) {
    $res = $matcher->matchFilenameToProduct($file, $allProducts);
    echo "Filename: '{$file}'\n";
    echo "  → Status: {$res['status']}\n";
    echo "  → Product Name: " . ($res['matched_product_name'] ?? 'N/A') . "\n";
    echo "  → Category: " . ($res['matched_category'] ?? 'N/A') . "\n";
    echo "  → Match Method: " . ($res['match_method'] ?? 'N/A') . "\n\n";
}
