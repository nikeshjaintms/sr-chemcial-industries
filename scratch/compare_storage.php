<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$pubStorage = public_path('storage');
$appPublic = storage_path('app/public');

echo "Checking files in public/storage vs storage/app/public...\n";

function getFilesRecursively($dir) {
    $results = [];
    if (!file_exists($dir)) return $results;
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS));
    foreach ($iterator as $file) {
        if ($file->isFile()) {
            $path = str_replace('\\', '/', $file->getPathname());
            $rel = str_replace(str_replace('\\', '/', $dir) . '/', '', $path);
            $results[$rel] = $file->getSize();
        }
    }
    return $results;
}

$pubFiles = getFilesRecursively($pubStorage);
$appFiles = getFilesRecursively($appPublic);

echo "Total files in public/storage      : " . count($pubFiles) . "\n";
echo "Total files in storage/app/public  : " . count($appFiles) . "\n\n";

$onlyInPub = array_diff_key($pubFiles, $appFiles);
$onlyInApp = array_diff_key($appFiles, $pubFiles);

echo "Files ONLY in public/storage (" . count($onlyInPub) . "):\n";
foreach (array_slice(array_keys($onlyInPub), 0, 10) as $f) {
    echo " - " . $f . "\n";
}

echo "\nFiles ONLY in storage/app/public (" . count($onlyInApp) . "):\n";
foreach (array_slice(array_keys($onlyInApp), 0, 10) as $f) {
    echo " - " . $f . "\n";
}
