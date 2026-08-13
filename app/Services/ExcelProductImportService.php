<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ExcelProductImportService
{
    protected string $sourceBasePath = 'C:/xampp/htdocs/SR';
    protected string $laravelBasePath = 'd:/nehal/SR-Laravel';

    /**
     * Validate an Excel file without altering the database.
     */
    public function validateExcelFile(string $filePath): array
    {
        $rows = $this->readSpreadsheetRows($filePath);
        $totalRows = count($rows);

        $report = [
            'total_rows' => $totalRows,
            'valid_rows' => 0,
            'invalid_rows' => 0,
            'new_products' => 0,
            'updated_products' => 0,
            'duplicate_rows' => 0,
            'missing_images' => 0,
            'missing_pdfs' => 0,
            'invalid_category_paths' => 0,
            'row_details' => []
        ];

        $seenCombinations = [];

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // 1-indexed header + 1
            $productName = trim($row['product_name'] ?? '');
            $categoryPath = trim($row['full_category_path'] ?? '');

            if (empty($categoryPath)) {
                $root = trim($row['root_category'] ?? '');
                $cat = trim($row['category'] ?? '');
                $sub = trim($row['subcategory'] ?? '');
                $parts = array_filter([$root, $cat, $sub]);
                $categoryPath = implode(' > ', $parts);
            }

            $imagePathRaw = trim($row['image_path'] ?? $row['image'] ?? '');
            $pdfPathRaw = trim($row['pdf_path'] ?? $row['pdf'] ?? '');

            // Row-level checks
            $issues = [];
            $status = 'VALID';

            if (empty($productName)) {
                $issues[] = 'Missing product_name';
            }

            if (empty($categoryPath)) {
                $issues[] = 'Missing full_category_path';
                $report['invalid_category_paths']++;
            }

            // Check duplicate in Excel
            $comboKey = strtolower($productName) . '||' . strtolower($categoryPath);
            if (!empty($productName) && !empty($categoryPath)) {
                if (isset($seenCombinations[$comboKey])) {
                    $issues[] = 'Duplicate entry in Excel (Row ' . $seenCombinations[$comboKey] . ')';
                    $report['duplicate_rows']++;
                } else {
                    $seenCombinations[$comboKey] = $rowNumber;
                }
            }

            // Verify assets
            $imageCheck = $this->verifyAssetExists($imagePathRaw, 'image');
            if (!$imageCheck['found'] && !empty($imagePathRaw)) {
                $report['missing_images']++;
                $issues[] = 'Image missing on disk (' . basename($imagePathRaw) . ')';
            }

            $pdfCheck = $this->verifyAssetExists($pdfPathRaw, 'pdf');
            if (!$pdfCheck['found'] && !empty($pdfPathRaw)) {
                $report['missing_pdfs']++;
                $issues[] = 'PDF missing on disk (' . basename($pdfPathRaw) . ')';
            }

            // Category & Database resolution preview
            $actionType = 'CREATE';
            if (!empty($productName) && !empty($categoryPath)) {
                try {
                    // Test category resolution
                    $segments = preg_split('/[>\/]+/', $categoryPath);
                    $cleanSegments = array_values(array_filter(array_map('trim', $segments), fn($s) => $s !== '' && strtolower($s) !== 'products'));
                    
                    if (empty($cleanSegments)) {
                        $issues[] = 'Invalid category path structure';
                        $report['invalid_category_paths']++;
                    }

                    // Check if existing product in DB
                    $existing = $this->findExistingProduct($productName, $categoryPath);
                    if ($existing) {
                        $actionType = 'UPDATE';
                        $report['updated_products']++;
                    } else {
                        $report['new_products']++;
                    }
                } catch (\Exception $e) {
                    $issues[] = 'Category error: ' . $e->getMessage();
                    $report['invalid_category_paths']++;
                }
            }

            if (!empty($issues)) {
                if (empty($productName) || empty($categoryPath)) {
                    $status = 'FAILED';
                    $report['invalid_rows']++;
                } else {
                    $status = 'WARNING';
                    $report['valid_rows']++; // Valid for import with warnings
                }
            } else {
                $report['valid_rows']++;
            }

            $report['row_details'][] = [
                'row' => $rowNumber,
                'product_name' => $productName ?: 'N/A',
                'category_path' => $categoryPath ?: 'N/A',
                'image_status' => $imageCheck['found'] ? 'Found' : (empty($imagePathRaw) ? 'Not Provided' : 'Missing'),
                'image_path_resolved' => $imageCheck['path'],
                'pdf_status' => $pdfCheck['found'] ? 'Found' : (empty($pdfPathRaw) ? 'Not Provided' : 'Missing'),
                'pdf_path_resolved' => $pdfCheck['path'],
                'action_type' => $actionType,
                'status' => $status,
                'message' => empty($issues) ? 'Ready for import (' . $actionType . ')' : implode('; ', $issues)
            ];
        }

        return $report;
    }

    /**
     * Import products from an Excel file into the Laravel database.
     */
    public function importExcelFile(string $filePath, bool $replaceMode = false): array
    {
        $rows = $this->readSpreadsheetRows($filePath);
        $totalRows = count($rows);

        $report = [
            'total_rows' => $totalRows,
            'created_count' => 0,
            'updated_count' => 0,
            'skipped_count' => 0,
            'failed_count' => 0,
            'images_copied' => 0,
            'images_missing' => 0,
            'pdfs_copied' => 0,
            'pdfs_missing' => 0,
            'deactivated_count' => 0,
            'row_results' => [],
            'error' => null
        ];

        $processedProductIds = [];

        DB::beginTransaction();
        try {
            foreach ($rows as $index => $row) {
                $rowNumber = $index + 2;
                $productName = trim($row['product_name'] ?? '');
                $categoryPath = trim($row['full_category_path'] ?? '');

                if (empty($productName)) {
                    $report['skipped_count']++;
                    $report['failed_count']++;
                    $report['row_results'][] = [
                        'row' => $rowNumber,
                        'product_name' => 'N/A',
                        'category_path' => $categoryPath ?: 'N/A',
                        'image_status' => 'Skipped',
                        'pdf_status' => 'Skipped',
                        'status' => 'FAILED',
                        'message' => 'Skipped: Required product_name is missing.'
                    ];
                    continue;
                }

                // Try finding existing product by exact name
                $existingProduct = $this->findExistingProduct($productName, $categoryPath);

                if (!$existingProduct && empty($categoryPath)) {
                    $report['skipped_count']++;
                    $report['failed_count']++;
                    $report['row_results'][] = [
                        'row' => $rowNumber,
                        'product_name' => $productName,
                        'category_path' => 'N/A',
                        'image_status' => 'Skipped',
                        'pdf_status' => 'Skipped',
                        'status' => 'FAILED',
                        'message' => "Product '{$productName}' not found in database to update."
                    ];
                    continue;
                }

                // 1. Ensure Category Hierarchy exists if path provided
                $category = null;
                if (!empty($categoryPath)) {
                    $category = Category::findOrCreatePath($categoryPath, $productName);
                }

                // 2. Asset Handling & Safe Copy
                $imagePathRaw = trim($row['image_path'] ?? $row['image'] ?? '');
                $imageCheck = $this->verifyAssetExists($imagePathRaw, 'image');
                $targetImageUrl = null;

                if ($imageCheck['found']) {
                    $copiedRelPath = $this->copyAssetToLaravelPublic($imageCheck['path'], 'image');
                    if ($copiedRelPath) {
                        $targetImageUrl = $copiedRelPath;
                        $report['images_copied']++;
                    }
                } else if (!empty($imagePathRaw)) {
                    $report['images_missing']++;
                }

                $pdfPathRaw = trim($row['pdf_path'] ?? $row['pdf'] ?? '');
                $pdfCheck = $this->verifyAssetExists($pdfPathRaw, 'pdf');
                $targetMsdsUrl = null;

                if ($pdfCheck['found']) {
                    $copiedPdfRelPath = $this->copyAssetToLaravelPublic($pdfCheck['path'], 'pdf');
                    if ($copiedPdfRelPath) {
                        $targetMsdsUrl = $copiedPdfRelPath;
                        $report['pdfs_copied']++;
                    }
                } else if (!empty($pdfPathRaw)) {
                    $report['pdfs_missing']++;
                }

                // Build attribute values
                $slugCandidate = !empty($row['slug']) ? Str::slug($row['slug']) : Str::slug($productName);

                $features = $this->parseArrayOrJsonField($row['features'] ?? null);
                $applications = $this->parseArrayOrJsonField($row['applications'] ?? $row['application'] ?? null);
                $specifications = $this->parseSpecificationsField($row['specifications'] ?? null);

                $isFeatured = isset($row['is_featured']) ? (bool)$row['is_featured'] : false;
                $sortOrder = isset($row['sort_order']) ? (int)$row['sort_order'] : 0;
                $status = isset($row['status']) ? (bool)$row['status'] : true;

                $brand = !empty($row['brand']) ? trim($row['brand']) : 'SRCIL Standard';
                $chemicalName = !empty($row['chemical_name']) ? trim($row['chemical_name']) : $productName;
                $casNumber = !empty($row['cas_number']) ? trim($row['cas_number']) : 'N/A';
                $hsnCode = !empty($row['hsn_code']) ? trim($row['hsn_code']) : 'N/A';
                $purity = !empty($row['purity']) ? trim($row['purity']) : 'Technical Grade';
                $packaging = !empty($row['packaging']) ? trim($row['packaging']) : 'Standard Packaging';
                $description = !empty($row['description']) ? trim($row['description']) : 'High purity ' . $productName . ' supplied by SR Chemical Industries Limited.';
                $storageInfo = !empty($row['storage_info']) ? trim($row['storage_info']) : null;

                if ($existingProduct) {
                    // UPDATE Product
                    $updateData = [
                        'brand' => $brand,
                        'chemical_name' => !empty($row['chemical_name']) ? trim($row['chemical_name']) : $existingProduct->chemical_name,
                        'cas_number' => !empty($row['cas_number']) ? trim($row['cas_number']) : $existingProduct->cas_number,
                        'hsn_code' => !empty($row['hsn_code']) ? trim($row['hsn_code']) : $existingProduct->hsn_code,
                        'purity' => !empty($row['purity']) ? trim($row['purity']) : $existingProduct->purity,
                        'packaging' => !empty($row['packaging']) ? trim($row['packaging']) : $existingProduct->packaging,
                        'description' => !empty($row['description']) ? trim($row['description']) : $existingProduct->description,
                        'features' => !empty($features) ? $features : $existingProduct->features,
                        'applications' => !empty($applications) ? $applications : $existingProduct->applications,
                        'specifications' => !empty($specifications) ? $specifications : $existingProduct->specifications,
                        'storage_info' => !empty($storageInfo) ? $storageInfo : $existingProduct->storage_info,
                        'is_featured' => isset($row['is_featured']) ? (bool)$row['is_featured'] : $existingProduct->is_featured,
                        'sort_order' => isset($row['sort_order']) ? (int)$row['sort_order'] : $existingProduct->sort_order,
                        'status' => isset($row['status']) ? (bool)$row['status'] : $existingProduct->status
                    ];

                    if (!empty($categoryPath) && isset($category) && $category->id) {
                        $updateData['category_id'] = $category->id;
                    }

                    if ($targetImageUrl) {
                        $updateData['image_url'] = $targetImageUrl;
                    }
                    if ($targetMsdsUrl) {
                        $updateData['msds_url'] = $targetMsdsUrl;
                    }

                    $existingProduct->update($updateData);
                    $product = $existingProduct;

                    $report['updated_count']++;
                    $rowStatus = 'UPDATED';
                    $msg = 'Successfully updated product in ' . $categoryPath;
                } else {
                    // CREATE Product (Ensure Unique Slug across Products table)
                    $uniqueSlug = $this->generateUniqueSlug($slugCandidate);

                    $imageUrlToSave = $targetImageUrl ?: 'assets/img/added/product/Caustic-Soda-Flakes-NaOH.jpg';
                    $msdsUrlToSave = $targetMsdsUrl ?: '#';

                    $product = Product::create([
                        'name' => $productName,
                        'slug' => $uniqueSlug,
                        'brand' => $brand,
                        'chemical_name' => $chemicalName,
                        'cas_number' => $casNumber,
                        'hsn_code' => $hsnCode,
                        'purity' => $purity,
                        'packaging' => $packaging,
                        'description' => $description,
                        'features' => $features,
                        'applications' => $applications,
                        'specifications' => $specifications,
                        'storage_info' => $storageInfo,
                        'category_id' => $category->id,
                        'image_url' => $imageUrlToSave,
                        'msds_url' => $msdsUrlToSave,
                        'specification_url' => route('products.show', $uniqueSlug),
                        'product_url' => $uniqueSlug . '.php',
                        'is_featured' => $isFeatured,
                        'sort_order' => $sortOrder,
                        'status' => $status
                    ]);

                    $report['created_count']++;
                    $rowStatus = 'CREATED';
                    $msg = 'Successfully created product in ' . $categoryPath;
                }

                // Sync pivot table for category-product link
                if ($product->category_id) {
                    $product->categories()->sync([$product->category_id]);
                }

                $processedProductIds[] = $product->id;

                $report['row_results'][] = [
                    'row' => $rowNumber,
                    'product_name' => $productName,
                    'category_path' => $categoryPath,
                    'image_status' => $imageCheck['found'] ? 'Found' : (empty($imagePathRaw) ? 'Not Provided' : 'Missing'),
                    'pdf_status' => $pdfCheck['found'] ? 'Found' : (empty($pdfPathRaw) ? 'Not Provided' : 'Missing'),
                    'status' => $rowStatus,
                    'message' => $msg
                ];
            }

            // Handle Replacement Mode
            if ($replaceMode && !empty($processedProductIds)) {
                $deactivated = Product::whereNotIn('id', $processedProductIds)
                    ->where('status', true)
                    ->update(['status' => false]);
                $report['deactivated_count'] = $deactivated;
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $report['error'] = 'Database Transaction Aborted: ' . $e->getMessage();
            throw $e;
        }

        return $report;
    }

    /**
     * Read rows from spreadsheet into associative array keyed by column name
     */
    protected function readSpreadsheetRows(string $filePath): array
    {
        if (!file_exists($filePath)) {
            throw new \InvalidArgumentException("Excel file not found at path: {$filePath}");
        }

        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rawRows = $sheet->toArray(null, true, true, true);

        if (empty($rawRows)) {
            return [];
        }

        // Row 1 contains headers
        $headerRow = array_shift($rawRows);
        $headerMap = [];
        foreach ($headerRow as $colLetter => $headerName) {
            $cleanHeader = strtolower(trim((string)$headerName));
            if (!empty($cleanHeader)) {
                $headerMap[$colLetter] = $cleanHeader;
            }
        }

        $formattedRows = [];
        foreach ($rawRows as $row) {
            $formatted = [];
            $hasData = false;

            foreach ($headerMap as $colLetter => $columnName) {
                $val = $row[$colLetter] ?? null;
                $formatted[$columnName] = $val !== null ? trim((string)$val) : null;
                if (!empty($formatted[$columnName])) {
                    $hasData = true;
                }
            }

            if ($hasData) {
                $formattedRows[] = $formatted;
            }
        }

        return $formattedRows;
    }

    /**
     * Verify if asset (image/pdf) exists physically on disk without modifying source.
     */
    protected function verifyAssetExists(?string $rawPath, string $type = 'image'): array
    {
        if (empty($rawPath)) {
            return ['found' => false, 'path' => null];
        }

        $normalized = str_replace('\\', '/', trim($rawPath));

        // 1. Direct path check
        if (file_exists($normalized) && is_file($normalized)) {
            return ['found' => true, 'path' => $normalized];
        }

        // 2. Source Base Path check (C:/xampp/htdocs/SR/...)
        $sourcePath = rtrim($this->sourceBasePath, '/') . '/' . ltrim($normalized, '/');
        if (file_exists($sourcePath) && is_file($sourcePath)) {
            return ['found' => true, 'path' => $sourcePath];
        }

        // 3. Laravel Public Path check
        $publicPath = public_path(ltrim($normalized, '/'));
        if (file_exists($publicPath) && is_file($publicPath)) {
            return ['found' => true, 'path' => $publicPath];
        }

        // 4. Filename search in standard source directories if base path matches
        $baseName = basename($normalized);
        if (!empty($baseName)) {
            $searchSubdir = ($type === 'image') ? 'assets/img/added/product/' : 'assets/pdf/MSDC/';
            $candidatePath = rtrim($this->sourceBasePath, '/') . '/' . $searchSubdir . $baseName;
            if (file_exists($candidatePath) && is_file($candidatePath)) {
                return ['found' => true, 'path' => $candidatePath];
            }
        }

        return ['found' => false, 'path' => null];
    }

    /**
     * Copy asset into Laravel public folder safely (read-only from source).
     */
    protected function copyAssetToLaravelPublic(string $sourcePath, string $type = 'image'): ?string
    {
        $baseName = basename($sourcePath);
        if (empty($baseName)) {
            return null;
        }

        $subDir = ($type === 'image') ? 'assets/img/added/product' : 'assets/pdf/MSDC';
        $targetDir = public_path($subDir);

        if (!file_exists($targetDir)) {
            @mkdir($targetDir, 0777, true);
        }

        $targetPath = $targetDir . '/' . $baseName;

        // Copy source to target safely
        if (copy($sourcePath, $targetPath)) {
            return $subDir . '/' . $baseName;
        }

        return null;
    }

    /**
     * Find existing product by matching name and category path.
     */
    protected function findExistingProduct(string $name, string $categoryPath): ?Product
    {
        $nameTrim = trim($name);
        if (empty($nameTrim)) {
            return null;
        }

        // 1. Direct exact name match in database
        $product = Product::where('name', $nameTrim)->first();
        if ($product) {
            return $product;
        }

        // 2. Exact case-insensitive normalized match
        $product = Product::whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower($nameTrim)])->first();
        if ($product) {
            return $product;
        }

        if (!empty($categoryPath)) {
            $segments = preg_split('/[>\/]+/', trim($categoryPath));
            $cleanSegments = array_values(array_filter(array_map('trim', $segments), fn($s) => $s !== '' && strtolower($s) !== 'products'));

            if (!empty($cleanSegments)) {
                $leafName = end($cleanSegments);
                $categories = Category::where('name', 'LIKE', $leafName)->get();

                foreach ($categories as $cat) {
                    $catPath = $cat->path;
                    if ($this->compareCategoryPaths($catPath, $categoryPath)) {
                        $product = Product::where('name', $nameTrim)
                            ->where('category_id', $cat->id)
                            ->first();

                        if ($product) {
                            return $product;
                        }
                    }
                }
            }
        }

        return null;
    }

    /**
     * Compare two category path strings for equivalence
     */
    protected function compareCategoryPaths(string $path1, string $path2): bool
    {
        $clean1 = strtolower(preg_replace('/[^a-z0-9]/', '', $path1));
        $clean2 = strtolower(preg_replace('/[^a-z0-9]/', '', $path2));
        return $clean1 === $clean2 || str_contains($clean1, $clean2) || str_contains($clean2, $clean1);
    }

    /**
     * Generate unique product slug
     */
    protected function generateUniqueSlug(string $baseSlug): string
    {
        $slug = $baseSlug;
        $counter = 1;
        while (Product::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        return $slug;
    }

    /**
     * Parse features/applications string or JSON into array
     */
    protected function parseArrayOrJsonField($field): array
    {
        if (empty($field)) {
            return [];
        }

        if (is_array($field)) {
            return array_values(array_filter($field, fn($v) => !empty(trim((string)$v))));
        }

        $decoded = json_decode((string)$field, true);
        if (is_array($decoded)) {
            return array_values(array_filter($decoded, fn($v) => !empty(trim((string)$v))));
        }

        // Split by line or comma
        $parts = preg_split('/[\r\n,]+/', (string)$field);
        return array_values(array_filter(array_map('trim', $parts), fn($v) => !empty($v)));
    }

    /**
     * Parse specifications JSON or string into associative array
     */
    protected function parseSpecificationsField($field): array
    {
        if (empty($field)) {
            return [];
        }

        if (is_array($field)) {
            return $field;
        }

        $decoded = json_decode((string)$field, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        // Try key:value lines
        $specs = [];
        $lines = preg_split('/[\r\n]+/', (string)$field);
        foreach ($lines as $line) {
            if (str_contains($line, ':')) {
                [$k, $v] = explode(':', $line, 2);
                $specs[trim($k)] = trim($v);
            }
        }

        return $specs;
    }
}
