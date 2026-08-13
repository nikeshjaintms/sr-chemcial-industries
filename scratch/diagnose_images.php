<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Product;
use Illuminate\Support\Facades\Storage;

echo "==================================================\n";
echo "1. DATABASE PRODUCT IMAGE PATHS AUDIT\n";
echo "==================================================\n";

$products = Product::select('id', 'name', 'image_url', 'specification_image', 'specification_url', 'msds_url')->get();

$formatCounts = [];
$totalProducts = $products->count();
$existingPhysicalFiles = 0;
$missingPhysicalFiles = 0;

$details = [];

foreach ($products as $p) {
    $img = $p->image_url;
    
    // Categorize stored format
    $format = 'UNKNOWN';
    if (empty($img)) {
        $format = 'EMPTY/NULL';
    } elseif (str_starts_with($img, 'storage/')) {
        $format = 'storage/...';
    } elseif (str_starts_with($img, '/storage/')) {
        $format = '/storage/...';
    } elseif (str_starts_with($img, 'uploads/')) {
        $format = 'uploads/...';
    } elseif (str_starts_with($img, 'assets/')) {
        $format = 'assets/...';
    } elseif (str_starts_with($img, 'http://') || str_starts_with($img, 'https://')) {
        $format = 'FULL_URL';
    } else {
        $format = 'OTHER: ' . substr($img, 0, 15);
    }
    
    $formatCounts[$format] = ($formatCounts[$format] ?? 0) + 1;

    // Check physical file existence
    $fullPath = null;
    if ($img) {
        // Remove leading slash if any
        $cleanImg = ltrim($img, '/');
        // If it starts with storage/, it resolves to public/storage/... (via symlink or directly) or storage/app/public/
        if (str_starts_with($cleanImg, 'storage/')) {
            $relStorage = str_replace('storage/', '', $cleanImg);
            $publicPath = public_path($cleanImg);
            $appPublicPath = storage_path('app/public/' . $relStorage);
            
            if (file_exists($publicPath)) {
                $fullPath = $publicPath;
            } elseif (file_exists($appPublicPath)) {
                $fullPath = $appPublicPath;
            }
        } else {
            $publicPath = public_path($cleanImg);
            if (file_exists($publicPath)) {
                $fullPath = $publicPath;
            }
        }
    }

    $exists = !is_null($fullPath) && file_exists($fullPath);
    if ($exists) {
        $existingPhysicalFiles++;
    } else {
        $missingPhysicalFiles++;
    }

    $details[] = [
        'id' => $p->id,
        'name' => $p->name,
        'image_url' => $img,
        'format' => $format,
        'exists' => $exists,
        'resolved_path' => $fullPath,
        'asset_url' => asset($img),
        'storage_url' => $img ? Storage::url($img) : null,
    ];
}

echo "Total Products: {$totalProducts}\n\n";
echo "Image Path Formats in DB:\n";
foreach ($formatCounts as $fmt => $cnt) {
    echo sprintf("  - %-20s : %d\n", $fmt, $cnt);
}

echo "\nPhysical File Existence Check:\n";
echo "  - Physical File Found   : {$existingPhysicalFiles}\n";
echo "  - Physical File Missing : {$missingPhysicalFiles}\n\n";

echo "Sample Product Records (First 15):\n";
echo "--------------------------------------------------\n";
foreach (array_slice($details, 0, 15) as $d) {
    echo sprintf(
        "ID: %-3d | Name: %-30s | DB: %-45s | Exists: %s | Asset URL: %s\n",
        $d['id'],
        substr($d['name'], 0, 30),
        $d['image_url'],
        $d['exists'] ? 'YES' : 'NO ',
        $d['asset_url']
    );
}

echo "\n==================================================\n";
echo "2. STORAGE & SYMLINK CHECK\n";
echo "==================================================\n";

$publicStorageLink = public_path('storage');
echo "public/storage Link Path : " . $publicStorageLink . "\n";
echo "public/storage Exists    : " . (file_exists($publicStorageLink) ? 'YES' : 'NO') . "\n";
echo "public/storage Is Link   : " . (is_link($publicStorageLink) ? 'YES' : 'NO') . "\n";
if (is_link($publicStorageLink)) {
    echo "public/storage Target    : " . readlink($publicStorageLink) . "\n";
}

echo "storage/app/public Path  : " . storage_path('app/public') . "\n";
echo "storage/app/public Exists: " . (file_exists(storage_path('app/public')) ? 'YES' : 'NO') . "\n";

echo "\nAPP_URL in env: " . env('APP_URL') . "\n";
echo "FILESYSTEM_DISK in env: " . env('FILESYSTEM_DISK') . "\n";

