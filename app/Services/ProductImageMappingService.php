<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Str;

class ProductImageMappingService
{
    protected ProductFilenameMatcher $matcher;

    public function __construct(?ProductFilenameMatcher $matcher = null)
    {
        $this->matcher = $matcher ?? new ProductFilenameMatcher();
    }

    public function normalizeFilename(string $filename): string
    {
        return $this->matcher->normalizeFilename($filename);
    }

    public function normalizeProductString(?string $input): string
    {
        return $this->matcher->normalizeProductString($input);
    }

    public function getBaseProductString(?string $input): string
    {
        return $this->matcher->getBaseProductString($input);
    }

    public function matchFilenameToProduct(string $originalFilename, $allProducts = null, string $mode = 'skip'): array
    {
        if (is_null($allProducts)) {
            $allProducts = Product::with('category')->get();
        } elseif (is_array($allProducts)) {
            $allProducts = collect($allProducts)->map(fn($p) => is_array($p) ? (object)$p : $p);
        }

        $res = $this->matcher->matchFilenameToProduct($originalFilename, $allProducts, 'image', $mode);

        $status = $res['status'];
        $productId = $res['matched_product_id'];
        $productName = $res['matched_product_name'];
        $categoryName = $res['matched_category'] ?? null;
        $productObj = $productId ? Product::find($productId) : null;

        return [
            'filename' => $originalFilename,
            'norm_filename' => $this->normalizeFilename($originalFilename),
            'product_id' => $productId,
            'product_name' => $productName,
            'matched_category' => $categoryName,
            'match_method' => $res['match_method'] ?? 'UNKNOWN',
            'confidence' => $res['confidence'] ?? 'HIGH',
            'product' => $productObj,
            'status' => $status === 'SUCCESS' ? 'MATCHED' : $status,
            'message' => $res['message'],
            'badge_class' => match ($status) {
                'SUCCESS', 'MATCHED' => 'bg-success',
                'EXISTING IMAGE', 'ALREADY EXISTS' => 'bg-info text-dark',
                'AMBIGUOUS' => 'bg-warning text-dark',
                default => 'bg-secondary',
            },
        ];
    }

    /**
     * Build result array for matched product.
     */
    private function buildResult(
        string $filename,
        string $normFilename,
        array $product,
        string $matchType,
        string $mode
    ): array {
        $placeholderPattern = 'Caustic-Soda-Flakes-NaOH.jpg';
        $currentUrl = $product['image_url'] ?? '';

        $hasExisting = !empty($currentUrl) &&
            !str_contains($currentUrl, $placeholderPattern) &&
            $currentUrl !== '#' &&
            trim($currentUrl) !== '';

        if ($hasExisting && $mode === 'skip') {
            return [
                'filename' => $filename,
                'norm_filename' => $normFilename,
                'product_id' => $product['id'],
                'product_name' => $product['name'],
                'product' => $product,
                'status' => 'ALREADY EXISTS',
                'match_type' => $matchType,
                'confidence' => 100,
                'label' => '⏭️ Already Exists',
                'message' => "Product '{$product['name']}' already has an assigned image. Skipped.",
                'candidates' => [],
                'has_existing' => true,
                'existing_url' => $currentUrl,
            ];
        }

        $msg = $hasExisting
            ? "Product '{$product['name']}' matched. Existing image will be replaced."
            : "Product '{$product['name']}' matched successfully.";

        return [
            'filename' => $filename,
            'norm_filename' => $normFilename,
            'product_id' => $product['id'],
            'product_name' => $product['name'],
            'product' => $product,
            'status' => 'MATCHED',
            'match_type' => $matchType,
            'confidence' => 100,
            'label' => '✅ Exact Match',
            'message' => $msg,
            'candidates' => [],
            'has_existing' => $hasExisting,
            'existing_url' => $currentUrl,
        ];
    }

    /**
     * Candidate images helper for Media Library view.
     * Scans ONLY the canonical product image directory: public/assets/products.
     */
    public function getCandidateImages(): array
    {
        $targetDir = str_replace('\\', '/', public_path('assets/products'));
        if (!file_exists($targetDir)) {
            @mkdir($targetDir, 0755, true);
        }

        $images = [];
        $seenPaths = [];

        if (file_exists($targetDir) && is_dir($targetDir)) {
            $files = glob(rtrim($targetDir, '/') . '/*');
            if (empty($files)) {
                $scanned = @scandir($targetDir) ?: [];
                $files = [];
                foreach ($scanned as $f) {
                    if ($f !== '.' && $f !== '..') {
                        $files[] = $targetDir . '/' . $f;
                    }
                }
            }

            foreach ($files as $file) {
                if (is_dir($file)) continue;

                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) continue;

                $realPath = str_replace('\\', '/', realpath($file) ?: $file);
                $keyPath = strtolower($realPath);
                if (isset($seenPaths[$keyPath])) continue;
                $seenPaths[$keyPath] = true;

                $fileName = basename($file);
                $relPath = 'assets/products/' . $fileName;
                $normFilename = $this->normalizeFilename($fileName);
                $rawFilename = pathinfo($fileName, PATHINFO_FILENAME);

                $images[] = [
                    'full_path' => $realPath,
                    'relative_path' => $relPath,
                    'url' => asset($relPath),
                    'filename' => $fileName,
                    'raw_name' => $rawFilename,
                    'norm_name' => $normFilename,
                    'extension' => $ext,
                    'size' => file_exists($realPath) ? filesize($realPath) : 0,
                    'hash' => file_exists($realPath) ? md5_file($realPath) : null,
                ];
            }
        }

        return $images;
    }

    /**
     * Audit products to check images count and missing images.
     */
    public function auditProducts(): array
    {
        $products = Product::all();

        $total = $products->count();
        $assigned = 0;
        $withoutImages = [];

        foreach ($products as $p) {
            $raw = $p->getRawOriginal('image_url');
            $hasValidImage = !empty($raw) && $raw !== '#' && trim($raw) !== '';
            $fileExists = $hasValidImage && file_exists(public_path(ltrim($raw, '/')));

            if ($hasValidImage && $fileExists) {
                $assigned++;
            } else {
                $withoutImages[] = $p;
            }
        }

        return [
            'total' => $total,
            'assigned' => $assigned,
            'without_images_count' => count($withoutImages),
            'without_images' => $withoutImages,
        ];
    }

    /**
     * Scan public/assets/products/ and re-sync/auto-match existing physical image files to database products.
     */
    public function resyncExistingImages(): array
    {
        $candidateImages = $this->getCandidateImages();
        $allProducts = Product::with('category')->get();

        $assignedCount = 0;
        $alreadyAssignedCount = 0;
        $unassignedCount = 0;
        $details = [];

        foreach ($candidateImages as $img) {
            $filename = $img['filename'];
            $relPath = 'assets/products/' . $filename;

            // Run matching against all products with replace mode
            $res = $this->matcher->matchFilenameToProduct($filename, $allProducts, 'image', 'replace');

            if ($res['status'] === 'SUCCESS' || $res['status'] === 'MATCHED') {
                $productId = $res['matched_product_id'];
                $product = $allProducts->firstWhere('id', $productId) ?? Product::find($productId);

                if ($product) {
                    $oldPath = $product->image_url;
                    if ($oldPath === $relPath) {
                        $alreadyAssignedCount++;
                    } else {
                        $product->image_url = $relPath;
                        $product->save();
                        $assignedCount++;
                        $details[] = "✅ Assigned '{$filename}' → Product '{$product->name}' ({$res['matched_category']})";
                    }
                }
            } else {
                $unassignedCount++;
            }
        }

        return [
            'total_images_found' => count($candidateImages),
            'assigned_count' => $assignedCount,
            'already_assigned_count' => $alreadyAssignedCount,
            'unassigned_count' => $unassignedCount,
            'details' => $details,
        ];
    }

    /**
     * Reconcile/Auto Match All Product Images globally across all database products and local candidate images in public/assets/products/.
     */
    public function reconcileProductImages(): array
    {
        $candidateImages = $this->getCandidateImages();
        $allProducts = Product::with('category')->get();

        $alreadyAssigned = 0;
        $autoMatched = 0;
        $needsReview = 0;
        $withoutImage = 0;
        $details = [];

        // 1. Audit current physical image assignments
        $unassignedProducts = collect();
        foreach ($allProducts as $p) {
            $rawUrl = $p->getRawOriginal('image_url') ?? $p->image_url;
            $hasValidUrl = !empty($rawUrl) && $rawUrl !== '#' && trim($rawUrl) !== '';
            $physicalFile = $hasValidUrl ? public_path(ltrim($rawUrl, '/')) : '';
            $fileExists = $hasValidUrl && file_exists($physicalFile);

            if ($hasValidUrl && $fileExists) {
                $alreadyAssigned++;
            } else {
                $unassignedProducts->push($p);
            }
        }

        // 2. Match each candidate image against ALL products in database safely
        foreach ($candidateImages as $img) {
            $filename = $img['filename'];
            $relPath = 'assets/products/' . $filename;

            // Match against ALL products
            $res = $this->matcher->matchFilenameToProduct($filename, $allProducts, 'image', 'replace');

            if ($res['status'] === 'SUCCESS' || $res['status'] === 'MATCHED') {
                $productId = $res['matched_product_id'];
                $product = $allProducts->firstWhere('id', $productId);

                if ($product && $unassignedProducts->contains('id', $product->id)) {
                    $product->image_url = $relPath;
                    $product->save();
                    $autoMatched++;
                    $unassignedProducts = $unassignedProducts->reject(fn($p) => $p->id == $product->id);
                    $details[] = "✅ Auto-matched '{$filename}' → Product '{$product->name}' ({$res['matched_category']})";
                }
            } elseif ($res['status'] === 'AMBIGUOUS') {
                $needsReview++;
                $details[] = "⚠️ Needs Review '{$filename}' ({$res['message']})";
            }
        }

        $withoutImage = $unassignedProducts->count();

        return [
            'total_products' => $allProducts->count(),
            'already_assigned' => $alreadyAssigned,
            'auto_matched' => $autoMatched,
            'needs_review' => $needsReview,
            'without_image' => $withoutImage,
            'total_local_images' => count($candidateImages),
            'details' => $details,
        ];
    }
}
