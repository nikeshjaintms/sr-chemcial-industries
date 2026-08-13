<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Product;
use Illuminate\Support\Facades\Storage;

echo "==================================================\n";
echo "1. DATABASE MSDS & SPECIFICATION PDF AUDIT\n";
echo "==================================================\n";

$products = Product::all();

$msdsCount = 0;
$specCount = 0;

$msdsFound = 0;
$msdsMissing = 0;

$specFound = 0;
$specMissing = 0;

$msdsDetails = [];
$specDetails = [];

foreach ($products as $p) {
    // MSDS Audit
    $msds = $p->msds_url;
    if (!empty($msds) && $msds !== '#') {
        $msdsCount++;
        $cleanMsds = ltrim(trim($msds), '/');
        
        $pubPath = public_path($cleanMsds);
        $appPath = str_starts_with($cleanMsds, 'storage/')
            ? storage_path('app/public/' . str_replace('storage/', '', $cleanMsds))
            : storage_path('app/public/' . $cleanMsds);

        $existsPub = file_exists($pubPath);
        $existsApp = file_exists($appPath);
        $exists = $existsPub || $existsApp;

        if ($exists) {
            $msdsFound++;
        } else {
            $msdsMissing++;
        }

        $msdsDetails[] = [
            'id' => $p->id,
            'name' => $p->name,
            'db_val' => $msds,
            'asset_url' => asset($msds),
            'exists_pub' => $existsPub,
            'exists_app' => $existsApp,
        ];
    }

    // Specification Audit
    $spec = $p->spec_pdf_url ?: $p->specification_url;
    if (!empty($spec) && $spec !== '#' && !str_contains($spec, 'products/')) {
        $specCount++;
        $cleanSpec = ltrim(trim($spec), '/');

        $pubPath = public_path($cleanSpec);
        $appPath = str_starts_with($cleanSpec, 'storage/')
            ? storage_path('app/public/' . str_replace('storage/', '', $cleanSpec))
            : storage_path('app/public/' . $cleanSpec);

        $existsPub = file_exists($pubPath);
        $existsApp = file_exists($appPath);
        $exists = $existsPub || $existsApp;

        if ($exists) {
            $specFound++;
        } else {
            $specMissing++;
        }

        $specDetails[] = [
            'id' => $p->id,
            'name' => $p->name,
            'db_val' => $spec,
            'asset_url' => asset($spec),
            'exists_pub' => $existsPub,
            'exists_app' => $existsApp,
        ];
    }
}

echo "Total Products Audited       : " . $products->count() . "\n";
echo "Products with MSDS PDF       : {$msdsCount} (Found: {$msdsFound}, Missing: {$msdsMissing})\n";
echo "Products with Specification  : {$specCount} (Found: {$specFound}, Missing: {$specMissing})\n\n";

echo "Sample MSDS Records (First 10):\n";
echo "--------------------------------------------------\n";
foreach (array_slice($msdsDetails, 0, 10) as $d) {
    echo sprintf(
        "ID: %-3d | Name: %-25s | DB: %-35s | Pub: %s | App: %s | Asset: %s\n",
        $d['id'],
        substr($d['name'], 0, 25),
        $d['db_val'],
        $d['exists_pub'] ? 'YES' : 'NO ',
        $d['exists_app'] ? 'YES' : 'NO ',
        $d['asset_url']
    );
}

echo "\nSample Specification Records (First 10):\n";
echo "--------------------------------------------------\n";
foreach (array_slice($specDetails, 0, 10) as $d) {
    echo sprintf(
        "ID: %-3d | Name: %-25s | DB: %-35s | Pub: %s | App: %s | Asset: %s\n",
        $d['id'],
        substr($d['name'], 0, 25),
        $d['db_val'],
        $d['exists_pub'] ? 'YES' : 'NO ',
        $d['exists_app'] ? 'YES' : 'NO ',
        $d['asset_url']
    );
}

echo "\n==================================================\n";
echo "2. DIRECTORY PERMISSIONS & FILESYSTEM CHECK\n";
echo "==================================================\n";

$dirsToCheck = [
    public_path('storage'),
    public_path('storage/uploads'),
    public_path('storage/uploads/msds'),
    public_path('storage/uploads/specifications'),
    storage_path('app/public'),
    storage_path('app/public/uploads'),
    storage_path('app/public/uploads/msds'),
    storage_path('app/public/uploads/specifications'),
];

foreach ($dirsToCheck as $dir) {
    $exists = file_exists($dir);
    $isDir = is_dir($dir);
    $isLink = is_link($dir);
    $perms = $exists ? substr(sprintf('%o', fileperms($dir)), -4) : 'N/A';
    
    echo sprintf(
        "%-50s | Exists: %s | Dir: %s | Link: %s | Perms: %s\n",
        str_replace(base_path() . '/', '', str_replace('\\', '/', $dir)),
        $exists ? 'YES' : 'NO ',
        $isDir ? 'YES' : 'NO ',
        $isLink ? 'YES' : 'NO ',
        $perms
    );
}

