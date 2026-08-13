<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BulkPdfUploadRequest;
use App\Models\Product;
use App\Services\BulkPdfMatchingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BulkPdfUploadController extends Controller
{
    /**
     * Display the Bulk PDF Upload dashboard page.
     */
    public function index()
    {
        $stats = $this->getSystemPdfStats();
        $totalProducts = $stats['total_products'];
        $productsWithMsds = $stats['products_with_msds'];
        $productsWithSpec = $stats['products_with_spec'];

        return view('admin.products.bulk-pdf', compact('totalProducts', 'productsWithMsds', 'productsWithSpec', 'stats'));
    }

    /**
     * Calculate dynamic PDF statistics for MSDS and Specification canonical asset directories.
     */
    public function getSystemPdfStats(): array
    {
        $totalProducts = Product::count();

        $msdcDir = public_path('assets/pdf/MSDC');
        $specDir = public_path('assets/pdf/Specification');

        $msdsFiles = file_exists($msdcDir) ? array_values(array_filter(scandir($msdcDir), fn($f) => is_file($msdcDir . '/' . $f) && strtolower(pathinfo($f, PATHINFO_EXTENSION)) === 'pdf')) : [];
        $specFiles = file_exists($specDir) ? array_values(array_filter(scandir($specDir), fn($f) => is_file($specDir . '/' . $f) && strtolower(pathinfo($f, PATHINFO_EXTENSION)) === 'pdf')) : [];

        $totalMsdsFiles = count($msdsFiles);
        $totalSpecFiles = count($specFiles);

        $assignedMsdsFilenames = Product::whereNotNull('msds_url')
            ->where('msds_url', '!=', '#')
            ->where('msds_url', '!=', '')
            ->pluck('msds_url')
            ->map(fn($u) => strtolower(basename($u)))
            ->unique()
            ->toArray();

        $assignedSpecFilenames = Product::where(function($q) {
                $q->whereNotNull('specification_url')->where('specification_url', '!=', '#')->where('specification_url', '!=', '')
                  ->orWhere(function($q2) {
                      $q2->whereNotNull('specification_image')->where('specification_image', '!=', '#')->where('specification_image', '!=', '');
                  });
            })
            ->get()
            ->flatMap(function($p) {
                $urls = [];
                if (!empty($p->specification_url) && $p->specification_url !== '#') $urls[] = strtolower(basename($p->specification_url));
                if (!empty($p->specification_image) && $p->specification_image !== '#') $urls[] = strtolower(basename($p->specification_image));
                return $urls;
            })
            ->unique()
            ->toArray();

        $msdsAssignedCount = 0;
        foreach ($msdsFiles as $f) {
            if (in_array(strtolower($f), $assignedMsdsFilenames)) {
                $msdsAssignedCount++;
            }
        }
        $msdsUnassignedCount = max(0, $totalMsdsFiles - $msdsAssignedCount);

        $specAssignedCount = 0;
        foreach ($specFiles as $f) {
            if (in_array(strtolower($f), $assignedSpecFilenames)) {
                $specAssignedCount++;
            }
        }
        $specUnassignedCount = max(0, $totalSpecFiles - $specAssignedCount);

        $productsWithMsds = Product::whereNotNull('msds_url')->where('msds_url', '!=', '#')->where('msds_url', '!=', '')->count();
        $productsWithSpec = Product::where(function($q) {
            $q->whereNotNull('specification_url')->where('specification_url', '!=', '#')->where('specification_url', '!=', '')
              ->orWhere(function($q2) {
                  $q2->whereNotNull('specification_image')->where('specification_image', '!=', '#')->where('specification_image', '!=', '');
              });
        })->count();

        return [
            'total_products' => $totalProducts,
            'products_with_msds' => $productsWithMsds,
            'products_with_spec' => $productsWithSpec,

            'total_msds_files' => $totalMsdsFiles,
            'msds_assigned_count' => $msdsAssignedCount,
            'msds_unassigned_count' => $msdsUnassignedCount,

            'total_spec_files' => $totalSpecFiles,
            'spec_assigned_count' => $specAssignedCount,
            'spec_unassigned_count' => $specUnassignedCount,
        ];
    }

    /**
     * Preview matching results without writing files or updating database.
     */
    public function preview(BulkPdfUploadRequest $request, BulkPdfMatchingService $matchingService)
    {
        try {
            $pdfType = $request->input('pdf_type', 'msds');
            $mode = $request->input('existing_mode', 'skip');
            $files = $request->file('pdf_files', []);

            $allProducts = Product::select('id', 'name', 'chemical_name', 'slug', 'msds_url', 'specification_url')->get();

            $results = [];
            $summary = [
                'total' => count($files),
                'matched' => 0,
                'already_exists' => 0,
                'not_found' => 0,
                'ambiguous' => 0,
                'failed' => 0,
            ];

            foreach ($files as $file) {
                $filename = $file->getClientOriginalName();

                if (!$file->isValid() || strtolower($file->getClientOriginalExtension()) !== 'pdf') {
                    $summary['failed']++;
                    $results[] = [
                        'filename' => $filename,
                        'pdf_type' => strtoupper($pdfType),
                        'matched_product' => '-',
                        'status' => 'INVALID FILE',
                        'message' => 'Uploaded file is not a valid PDF file.',
                        'badge_class' => 'bg-danger',
                    ];
                    continue;
                }

                $match = $matchingService->matchFilenameToProduct($filename, $allProducts, $pdfType, $mode);

                $status = $match['status'];
                $matchedName = $match['matched_product_name'] ?? '-';
                $badgeClass = $this->getBadgeClass($status);

                if ($status === 'SUCCESS') {
                    $summary['matched']++;
                } elseif ($status === 'ALREADY EXISTS') {
                    $summary['already_exists']++;
                } elseif ($status === 'AMBIGUOUS') {
                    $summary['ambiguous']++;
                } elseif ($status === 'NOT FOUND') {
                    $summary['not_found']++;
                } else {
                    $summary['failed']++;
                }

                $results[] = [
                    'filename' => $filename,
                    'pdf_type' => strtoupper($pdfType),
                    'matched_product' => $matchedName,
                    'status' => $status,
                    'message' => $match['message'],
                    'badge_class' => $badgeClass,
                ];
            }

            return response()->json([
                'success' => true,
                'summary' => $summary,
                'items' => $results,
                'stats' => $this->getSystemPdfStats(),
            ]);

        } catch (\Exception $e) {
            Log::error('Bulk PDF Preview Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate preview: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Process bulk PDF upload and attach PDFs to matched products.
     */
    public function process(BulkPdfUploadRequest $request, BulkPdfMatchingService $matchingService)
    {
        try {
            $pdfType = strtolower($request->input('pdf_type', 'msds'));
            $mode = strtolower($request->input('existing_mode', 'skip'));
            $files = $request->file('pdf_files', []);

            $allProducts = Product::all();
            $productsById = $allProducts->keyBy('id');

            $folder = $pdfType === 'specification' ? 'uploads/specifications' : 'uploads/msds';

            $results = [];
            $summary = [
                'total' => count($files),
                'uploaded' => 0,
                'already_exists' => 0,
                'not_found' => 0,
                'ambiguous' => 0,
                'failed' => 0,
            ];

            foreach ($files as $file) {
                $filename = $file->getClientOriginalName();

                // Validation check per file
                if (!$file->isValid() || strtolower($file->getClientOriginalExtension()) !== 'pdf') {
                    $summary['failed']++;
                    $results[] = [
                        'filename' => $filename,
                        'pdf_type' => strtoupper($pdfType),
                        'matched_product' => '-',
                        'status' => 'INVALID FILE',
                        'message' => 'Uploaded file is corrupted or not a PDF.',
                        'badge_class' => 'bg-danger',
                    ];
                    continue;
                }

                // Matching logic
                $match = $matchingService->matchFilenameToProduct($filename, $allProducts, $pdfType, $mode);

                $status = $match['status'];
                $matchedProductId = $match['matched_product_id'];

                if ($status === 'NOT FOUND') {
                    $summary['not_found']++;
                    $results[] = [
                        'filename' => $filename,
                        'pdf_type' => strtoupper($pdfType),
                        'matched_product' => '-',
                        'status' => 'NOT FOUND',
                        'message' => 'No matching product found in database.',
                        'badge_class' => 'bg-secondary',
                    ];
                    continue;
                }

                if ($status === 'AMBIGUOUS') {
                    $summary['ambiguous']++;
                    $results[] = [
                        'filename' => $filename,
                        'pdf_type' => strtoupper($pdfType),
                        'matched_product' => '-',
                        'status' => 'AMBIGUOUS',
                        'message' => $match['message'],
                        'badge_class' => 'bg-warning text-dark',
                    ];
                    continue;
                }

                if ($status === 'ALREADY EXISTS') {
                    $summary['already_exists']++;
                    $results[] = [
                        'filename' => $filename,
                        'pdf_type' => strtoupper($pdfType),
                        'matched_product' => $match['matched_product_name'],
                        'status' => 'ALREADY EXISTS',
                        'message' => $match['message'],
                        'badge_class' => 'bg-info text-dark',
                    ];
                    continue;
                }

                if ($status === 'SUCCESS' && $matchedProductId && isset($productsById[$matchedProductId])) {
                    $product = $productsById[$matchedProductId];
                    $oldUrl = $pdfType === 'specification' ? $product->specification_url : $product->msds_url;

                    // Store file safely in public/assets/pdf/MSDC/ or public/assets/pdf/Specification/
                    $storedPath = null;
                    try {
                        $subDir = $pdfType === 'specification' ? 'assets/pdf/Specification' : 'assets/pdf/MSDC';
                        $targetDir = public_path($subDir);
                        if (!file_exists($targetDir)) {
                            @mkdir($targetDir, 0755, true);
                        }

                        $ext = $file->getClientOriginalExtension() ?: 'pdf';
                        $cleanBase = \Illuminate\Support\Str::slug(pathinfo($filename, PATHINFO_FILENAME));
                        $newFileName = $cleanBase . '_' . \Illuminate\Support\Str::random(6) . '.' . $ext;

                        $file->move($targetDir, $newFileName);
                        $dbUrl = $subDir . '/' . $newFileName;

                        if ($pdfType === 'specification') {
                            $product->specification_url = $dbUrl;
                        } else {
                            $product->msds_url = $dbUrl;
                        }

                        $product->save();

                        // Clean up old file if replace mode replaces an existing file
                        if ($mode === 'replace' && !empty($oldUrl)) {
                            $oldFileName = basename($oldUrl);
                            $oldPhysicalFile = public_path($subDir . '/' . $oldFileName);
                            if (file_exists($oldPhysicalFile) && is_file($oldPhysicalFile)) {
                                @unlink($oldPhysicalFile);
                            }
                            if (str_starts_with($oldUrl, 'storage/')) {
                                $oldStoragePath = str_replace('storage/', '', $oldUrl);
                                if (Storage::disk('public')->exists($oldStoragePath)) {
                                    Storage::disk('public')->delete($oldStoragePath);
                                }
                            }
                        }

                        $summary['uploaded']++;
                        $results[] = [
                            'filename' => $filename,
                            'pdf_type' => strtoupper($pdfType),
                            'matched_product' => $product->name,
                            'status' => 'SUCCESS',
                            'message' => strtoupper($pdfType) . ' uploaded and attached successfully.',
                            'badge_class' => 'bg-success',
                        ];

                    } catch (\Exception $e) {
                        // Rollback uploaded file if DB save failed
                        if ($storedPath && Storage::disk('public')->exists($storedPath)) {
                            Storage::disk('public')->delete($storedPath);
                        }

                        Log::error("Failed attaching PDF {$filename} to product ID {$matchedProductId}: " . $e->getMessage());

                        $summary['failed']++;
                        $results[] = [
                            'filename' => $filename,
                            'pdf_type' => strtoupper($pdfType),
                            'matched_product' => $product->name,
                            'status' => 'FAILED',
                            'message' => 'File attachment failed during database save.',
                            'badge_class' => 'bg-danger',
                        ];
                    }
                }
            }

            $stats = $this->getSystemPdfStats();

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'summary' => $summary,
                    'items' => $results,
                    'stats' => $stats,
                    'message' => "Processing complete! Total Files: {$summary['total']} | Uploaded: {$summary['uploaded']} | Already Exists: {$summary['already_exists']} | Not Found: {$summary['not_found']} | Ambiguous: {$summary['ambiguous']} | Failed: {$summary['failed']}.",
                ]);
            }

            return redirect()
                ->route('admin.products.bulk-pdf')
                ->with('summary', $summary)
                ->with('results', $results)
                ->with('stats', $stats)
                ->with('success', "Bulk PDF Auto-Matching completed successfully!");

        } catch (\Exception $e) {
            Log::error('Bulk PDF Upload Process Exception: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bulk PDF processing failed: ' . $e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'Bulk PDF Auto-Matching failed: ' . $e->getMessage());
        }
    }

    /**
     * Return Bootstrap badge CSS class for given status string.
     */
    private function getBadgeClass(string $status): string
    {
        return match ($status) {
            'SUCCESS' => 'bg-success',
            'ALREADY EXISTS' => 'bg-info text-dark',
            'NOT FOUND' => 'bg-secondary',
            'AMBIGUOUS' => 'bg-warning text-dark',
            'INVALID FILE', 'FAILED' => 'bg-danger',
            default => 'bg-secondary',
        };
    }
}
