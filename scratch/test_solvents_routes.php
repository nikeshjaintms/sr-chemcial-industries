<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;

echo "==================================================\n";
echo "TESTING INDUSTRIAL SOLVENTS SUBMENU CATEGORY ROUTES\n";
echo "==================================================\n";

$urlsToTest = [
    '/category/paint-coating-industry-solvents',
    '/category/paint-coating-solvents',
    '/category/pharmaceutical-chemical-solvents',
    '/category/cleaning-degreasing-solvents',
    '/products',
];

$allPassed = true;

foreach ($urlsToTest as $url) {
    $request = Request::create($url, 'GET');
    $response = $app->handle($request);
    $status = $response->getStatusCode();
    
    echo sprintf("URL: %-48s | Status: %d %s\n", $url, $status, ($status === 200 ? '✅ OK' : '❌ FAILED'));
    
    if ($status !== 200) {
        $allPassed = false;
    }
}

echo "==================================================\n";
if ($allPassed) {
    echo "✅ ALL INDUSTRIAL SOLVENTS CATEGORY ROUTES RETURN 200 OK!\n";
} else {
    echo "❌ SOME ROUTES RETURNED NON-200 RESPONSES!\n";
    exit(1);
}
