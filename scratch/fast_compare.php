<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$pubDir = public_path('storage/uploads/products');
$appDir = storage_path('app/public/uploads/products');

$pubFiles = file_exists($pubDir) ? array_map('basename', glob($pubDir . '/*')) : [];
$appFiles = file_exists($appDir) ? array_map('basename', glob($appDir . '/*')) : [];

echo "Files in public/storage/uploads/products     : " . count($pubFiles) . "\n";
echo "Files in storage/app/public/uploads/products : " . count($appFiles) . "\n";

$diffPub = array_diff($pubFiles, $appFiles);
$diffApp = array_diff($appFiles, $pubFiles);

echo "Files in public/storage but NOT in storage/app/public: " . count($diffPub) . "\n";
echo "Files in storage/app/public but NOT in public/storage: " . count($diffApp) . "\n";
