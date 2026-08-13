<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Product;

$products = Product::select('id', 'name', 'chemical_name', 'slug')->get();
foreach ($products as $p) {
    echo "ID: {$p->id} | Name: {$p->name} | Chem: {$p->chemical_name} | Slug: {$p->slug}\n";
}
