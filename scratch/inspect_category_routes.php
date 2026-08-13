<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Category;
use Illuminate\Support\Facades\Route;

echo "==================================================\n";
echo "1. AUDITING ALL CATEGORIES & SLUGS IN DATABASE\n";
echo "==================================================\n";

$categories = Category::with('parent', 'children')->get();

echo "Total Categories in DB: " . $categories->count() . "\n\n";

foreach ($categories as $cat) {
    $parentName = $cat->parent ? $cat->parent->name . " (ID: {$cat->parent_id})" : 'ROOT';
    $childCount = $cat->children->count();
    $prodCount = $cat->products()->count();

    echo sprintf(
        "ID: %-3d | Slug: %-40s | Name: %-40s | Parent: %-30s | Prods: %d | Children: %d\n",
        $cat->id,
        $cat->slug,
        $cat->name,
        $parentName,
        $prodCount,
        $childCount
    );
}

echo "\n==================================================\n";
echo "2. CHECKING CATEGORY ROUTES IN WEB.PHP\n";
echo "==================================================\n";

$routes = Route::getRoutes();
$categoryRoutes = [];

foreach ($routes as $route) {
    $uri = $route->uri();
    if (str_contains($uri, 'category') || str_contains($uri, 'product') || str_contains($uri, 'c/')) {
        $categoryRoutes[] = [
            'method' => implode('|', $route->methods()),
            'uri' => $uri,
            'name' => $route->getName(),
            'action' => $route->getActionName(),
        ];
    }
}

foreach ($categoryRoutes as $r) {
    echo sprintf(
        "Method: %-6s | URI: %-45s | Name: %-30s | Action: %s\n",
        $r['method'],
        $r['uri'],
        $r['name'] ?? '—',
        $r['action']
    );
}

