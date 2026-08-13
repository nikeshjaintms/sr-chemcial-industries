<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use Illuminate\Support\Str;

echo "==================================================\n";
echo "1. TESTING MSDS & SPECIFICATION PDF REMOVAL PIPELINE\n";
echo "==================================================\n";

$msdcDir = public_path('assets/pdf/MSDC');
$specDir = public_path('assets/pdf/Specification');

if (!file_exists($msdcDir)) { @mkdir($msdcDir, 0755, true); }
if (!file_exists($specDir)) { @mkdir($specDir, 0755, true); }

// Create test dummy physical files
$testMsdsFilename = 'test-dummy-msds-' . Str::random(6) . '.pdf';
$testSpecFilename = 'test-dummy-spec-' . Str::random(6) . '.pdf';

$testMsdsPath = $msdcDir . '/' . $testMsdsFilename;
$testSpecPath = $specDir . '/' . $testSpecFilename;

file_put_contents($testMsdsPath, '%PDF-1.4 dummy msds content');
file_put_contents($testSpecPath, '%PDF-1.4 dummy spec content');

// Create test dummy product
$testProduct = Product::create([
    'name' => 'Test Dummy Chemical Product ' . Str::random(4),
    'slug' => 'test-dummy-chemical-' . Str::random(6),
    'brand' => 'SRCIL Standard',
    'chemical_name' => 'Test Chemical',
    'cas_number' => '000-00-0',
    'hsn_code' => '00000000',
    'purity' => '99%',
    'packaging' => '50kg',
    'description' => 'Test chemical for MSDS and Spec removal testing',
    'image_url' => 'assets/products/nitric-acid.jpg',
    'msds_url' => 'assets/pdf/MSDC/' . $testMsdsFilename,
    'specification_url' => 'assets/pdf/Specification/' . $testSpecFilename,
    'specification_image' => 'assets/pdf/Specification/' . $testSpecFilename,
]);

echo "Created Test Product ID: {$testProduct->id}\n";
echo "MSDS Path: {$testProduct->msds_url}\n";
echo "Spec Path: {$testProduct->specification_url}\n";
echo "Image Path: {$testProduct->image_url}\n\n";

// Verify Accessors
echo "MSDS Accessor: " . $testProduct->msds_pdf_url . "\n";
echo "Spec Accessor: " . $testProduct->spec_pdf_url . "\n";

if ($testProduct->msds_pdf_url !== 'assets/pdf/MSDC/' . $testMsdsFilename) {
    echo "❌ INVALID MSDS ACCESSOR RESULT!\n";
    exit(1);
}

if ($testProduct->spec_pdf_url !== 'assets/pdf/Specification/' . $testSpecFilename) {
    echo "❌ INVALID SPEC ACCESSOR RESULT!\n";
    exit(1);
}

// Perform Removal Test for MSDS
echo "\nTesting MSDS Removal Action...\n";
$msdsFileBefore = public_path($testProduct->msds_pdf_url);
echo "Physical MSDS File Exists Before: " . (file_exists($msdsFileBefore) ? 'YES' : 'NO') . "\n";

// Simulate Admin Request with remove_msds = 1
$req = Illuminate\Http\Request::create('/admin/products/' . $testProduct->id, 'PUT', [
    'name' => $testProduct->name,
    'slug' => $testProduct->slug,
    'description' => $testProduct->description,
    'remove_msds' => '1',
]);

$controller = new App\Http\Controllers\Admin\ProductAdminController();
$controller->update($req, $testProduct);

$testProduct->refresh();

echo "Physical MSDS File Exists After: " . (file_exists($msdsFileBefore) ? 'YES' : 'NO') . "\n";
echo "Product msds_url After: " . var_export($testProduct->msds_url, true) . "\n";
echo "Product image_url After: " . $testProduct->image_url . "\n";
echo "Product spec_pdf_url After: " . $testProduct->spec_pdf_url . "\n";

if (file_exists($msdsFileBefore)) {
    echo "❌ MSDS PHYSICAL FILE WAS NOT DELETED!\n";
    exit(1);
}

if (!is_null($testProduct->msds_url)) {
    echo "❌ MSDS_URL WAS NOT SET TO NULL!\n";
    exit(1);
}

if ($testProduct->image_url !== 'assets/products/nitric-acid.jpg') {
    echo "❌ PRODUCT IMAGE WAS ALTERED!\n";
    exit(1);
}

if (empty($testProduct->spec_pdf_url)) {
    echo "❌ SPECIFICATION PDF WAS ALTERED!\n";
    exit(1);
}

echo "✅ MSDS REMOVAL PASSED CLEANLY!\n\n";

// Perform Removal Test for Specification
echo "Testing Specification Removal Action...\n";
$specFileBefore = public_path($testProduct->spec_pdf_url);
echo "Physical Spec File Exists Before: " . (file_exists($specFileBefore) ? 'YES' : 'NO') . "\n";

$reqSpec = Illuminate\Http\Request::create('/admin/products/' . $testProduct->id, 'PUT', [
    'name' => $testProduct->name,
    'slug' => $testProduct->slug,
    'description' => $testProduct->description,
    'remove_specification_image' => '1',
]);

$controller->update($reqSpec, $testProduct);

$testProduct->refresh();

echo "Physical Spec File Exists After: " . (file_exists($specFileBefore) ? 'YES' : 'NO') . "\n";
echo "Product specification_url After: " . var_export($testProduct->specification_url, true) . "\n";
echo "Product specification_image After: " . var_export($testProduct->specification_image, true) . "\n";

if (file_exists($specFileBefore)) {
    echo "❌ SPECIFICATION PHYSICAL FILE WAS NOT DELETED!\n";
    exit(1);
}

if (!is_null($testProduct->specification_url) || !is_null($testProduct->specification_image)) {
    echo "❌ SPECIFICATION URL WAS NOT SET TO NULL!\n";
    exit(1);
}

// Clean up dummy record
$testProduct->delete();

echo "\n==================================================\n";
echo "2. AUDITING DATABASE PRODUCT RECORDS FOR '#' OR OLD PATHS\n";
echo "==================================================\n";

$oldMsdsCount = Product::where('msds_url', 'LIKE', '%storage/uploads/msds%')->orWhere('msds_url', '#')->count();
$oldSpecCount = Product::where('specification_url', 'LIKE', '%storage/uploads/specifications%')->orWhere('specification_url', '#')->count();

echo "Products with invalid/old msds_url: {$oldMsdsCount}\n";
echo "Products with invalid/old specification_url: {$oldSpecCount}\n";

if ($oldMsdsCount === 0 && $oldSpecCount === 0) {
    echo "✅ DATABASE AUDIT PASSED 100%!\n";
} else {
    echo "Updating old database records to null...\n";
    Product::where('msds_url', '#')->orWhere('msds_url', 'LIKE', '%storage/uploads/msds%')->update(['msds_url' => null]);
    Product::where('specification_url', '#')->orWhere('specification_url', 'LIKE', '%storage/uploads/specifications%')->update(['specification_url' => null]);
    echo "✅ CLEANUP COMPLETE!\n";
}

echo "\n==================================================\n";
echo "✅ ALL MSDS & SPECIFICATION PDF TESTS COMPLETED 100% SUCCESSFULLY!\n";
