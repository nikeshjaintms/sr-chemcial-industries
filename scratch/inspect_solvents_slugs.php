<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Category;

$industrialCat = Category::where('name', 'LIKE', '%Industrial Solvents%')->orWhere('slug', 'LIKE', '%industrial-solvents%')->first();

echo "Industrial Solvents Category:\n";
if ($industrialCat) {
    echo "ID: {$industrialCat->id} | Name: {$industrialCat->name} | Slug: {$industrialCat->slug}\n\n";

    echo "Children of Industrial Solvents:\n";
    echo "--------------------------------------------------\n";
    foreach ($industrialCat->children as $c) {
        echo "ID: {$c->id} | Name: {$c->name} | Slug: {$c->slug} | Prods: " . $c->products()->count() . "\n";
    }
} else {
    echo "Industrial Solvents category not found!\n";
}

echo "\nAll Categories with 'Solvent', 'Paint', 'Coating', 'Pharmaceutical', 'By Product' in Name:\n";
echo "--------------------------------------------------\n";
$matches = Category::where('name', 'LIKE', '%Paint%')
    ->orWhere('name', 'LIKE', '%Coating%')
    ->orWhere('name', 'LIKE', '%Pharmaceutical%')
    ->orWhere('name', 'LIKE', '%Solvent%')
    ->orWhere('name', 'LIKE', '%Product%')
    ->get();

foreach ($matches as $cat) {
    $parentName = $cat->parent ? $cat->parent->name : 'ROOT';
    echo sprintf("ID: %-3d | Slug: %-40s | Name: %-40s | Parent: %s\n", $cat->id, $cat->slug, $cat->name, $parentName);
}
