<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$mappingService = new App\Services\ProductImageMappingService();
$candidateImages = $mappingService->getCandidateImages();

echo "Candidate images count: " . count($candidateImages) . "\n";
var_dump(array_slice($candidateImages, 0, 5));
