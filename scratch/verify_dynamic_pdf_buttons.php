<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;

echo "==================================================\n";
echo "VERIFYING DYNAMIC MSDS & SPECIFICATION PDF BUTTON VISIBILITY\n";
echo "==================================================\n\n";

$products = Product::all();

$bothCount = 0;
$onlyMsdsCount = 0;
$onlySpecCount = 0;
$neitherCount = 0;

$samples = [
    'both' => null,
    'only_msds' => null,
    'only_spec' => null,
    'neither' => null,
];

foreach ($products as $p) {
    $hasMsds = $p->hasValidMsdsPdf();
    $hasSpec = $p->hasValidSpecPdf();

    if ($hasMsds && $hasSpec) {
        $bothCount++;
        if (!$samples['both']) $samples['both'] = $p;
    } elseif ($hasMsds && !$hasSpec) {
        $onlyMsdsCount++;
        if (!$samples['only_msds']) $samples['only_msds'] = $p;
    } elseif (!$hasMsds && $hasSpec) {
        $onlySpecCount++;
        if (!$samples['only_spec']) $samples['only_spec'] = $p;
    } else {
        $neitherCount++;
        if (!$samples['neither']) $samples['neither'] = $p;
    }
}

echo "--- SUMMARY OF PDF AVAILABILITY --- \n";
echo "• Total Products Evaluated: " . $products->count() . "\n";
echo "• Products with BOTH MSDS & Spec PDFs: {$bothCount}\n";
echo "• Products with ONLY MSDS PDF: {$onlyMsdsCount}\n";
echo "• Products with ONLY Specification PDF: {$onlySpecCount}\n";
echo "• Products with NEITHER PDF: {$neitherCount}\n\n";

echo "--- SAMPLE TEST CASES --- \n";

if ($samples['both']) {
    $p = $samples['both'];
    echo "CASE 1: Product with BOTH PDFs\n";
    echo "  Product: '{$p->name}'\n";
    echo "  → MSDS URL: '{$p->msds_pdf_url}' (Exists: YES)\n";
    echo "  → Spec URL: '{$p->spec_pdf_url}' (Exists: YES)\n";
    echo "  → Render Output: SHOW [ MSDS ] and [ Specification ] Buttons\n\n";
}

if ($samples['only_msds']) {
    $p = $samples['only_msds'];
    echo "CASE 2: Product with ONLY MSDS PDF\n";
    echo "  Product: '{$p->name}'\n";
    echo "  → MSDS URL: '{$p->msds_pdf_url}' (Exists: YES)\n";
    echo "  → Spec URL: " . ($p->spec_pdf_url ?: 'NULL') . " (Exists: NO)\n";
    echo "  → Render Output: SHOW [ MSDS ] Button, HIDE Specification Button\n\n";
}

if ($samples['only_spec']) {
    $p = $samples['only_spec'];
    echo "CASE 3: Product with ONLY Specification PDF\n";
    echo "  Product: '{$p->name}'\n";
    echo "  → MSDS URL: " . ($p->msds_pdf_url ?: 'NULL') . " (Exists: NO)\n";
    echo "  → Spec URL: '{$p->spec_pdf_url}' (Exists: YES)\n";
    echo "  → Render Output: HIDE MSDS Button, SHOW [ Specification ] Button\n\n";
}

if ($samples['neither']) {
    $p = $samples['neither'];
    echo "CASE 4: Product with NEITHER PDF\n";
    echo "  Product: '{$p->name}'\n";
    echo "  → MSDS URL: " . ($p->msds_pdf_url ?: 'NULL') . " (Exists: NO)\n";
    echo "  → Spec URL: " . ($p->spec_pdf_url ?: 'NULL') . " (Exists: NO)\n";
    echo "  → Render Output: HIDE BOTH MSDS & Specification Buttons\n\n";
}

echo "==================================================\n";
echo "✅ DYNAMIC BUTTON VISIBILITY VERIFICATION COMPLETED!\n";
echo "==================================================\n";
