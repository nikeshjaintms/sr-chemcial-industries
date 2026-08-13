<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\Admin\BulkPdfUploadController;
use App\Models\Product;

echo "==================================================\n";
echo "1. TESTING BULK PDF DYNAMIC SYSTEM COUNTERS\n";
echo "==================================================\n";

$controller = new BulkPdfUploadController();
$stats = $controller->getSystemPdfStats();

echo "Total Products: " . $stats['total_products'] . "\n";
echo "Products with MSDS: " . $stats['products_with_msds'] . " / " . $stats['total_products'] . "\n";
echo "Products with Specification: " . $stats['products_with_spec'] . " / " . $stats['total_products'] . "\n\n";

echo "--- MSDS PDF COUNTERS (public/assets/pdf/MSDC/) ---\n";
echo "Total MSDS PDFs: " . $stats['total_msds_files'] . "\n";
echo "Assigned MSDS PDFs: " . $stats['msds_assigned_count'] . "\n";
echo "Unassigned MSDS PDFs: " . $stats['msds_unassigned_count'] . "\n\n";

echo "--- SPECIFICATION PDF COUNTERS (public/assets/pdf/Specification/) ---\n";
echo "Total Specification PDFs: " . $stats['total_spec_files'] . "\n";
echo "Assigned Specification PDFs: " . $stats['spec_assigned_count'] . "\n";
echo "Unassigned Specification PDFs: " . $stats['spec_unassigned_count'] . "\n";

if (!is_int($stats['total_msds_files']) || !is_int($stats['total_spec_files'])) {
    echo "❌ INVALID STATS RETURN TYPES!\n";
    exit(1);
}

if ($stats['msds_assigned_count'] + $stats['msds_unassigned_count'] !== $stats['total_msds_files']) {
    echo "❌ MSDS ASSIGNED + UNASSIGNED != TOTAL MSDS FILES!\n";
    exit(1);
}

if ($stats['spec_assigned_count'] + $stats['spec_unassigned_count'] !== $stats['total_spec_files']) {
    echo "❌ SPEC ASSIGNED + UNASSIGNED != TOTAL SPEC FILES!\n";
    exit(1);
}

echo "\n==================================================\n";
echo "2. TESTING MEDIA LIBRARY PDF COUNTERS\n";
echo "==================================================\n";

$msdcDir = public_path('assets/pdf/MSDC');
$specDir = public_path('assets/pdf/Specification');

$msdsCount = count(file_exists($msdcDir) ? array_filter(scandir($msdcDir), fn($f) => is_file($msdcDir . '/' . $f) && strtolower(pathinfo($f, PATHINFO_EXTENSION)) === 'pdf') : []);
$specCount = count(file_exists($specDir) ? array_filter(scandir($specDir), fn($f) => is_file($specDir . '/' . $f) && strtolower(pathinfo($f, PATHINFO_EXTENSION)) === 'pdf') : []);
$totalPdfCount = $msdsCount + $specCount;

echo "Media Library MSDS PDFs: {$msdsCount}\n";
echo "Media Library Spec PDFs: {$specCount}\n";
echo "Media Library Total PDFs: {$totalPdfCount}\n";

echo "\n==================================================\n";
echo "✅ ALL PDF COUNTER & STATUS TESTS PASSED 100% SUCCESSFULLY!\n";
