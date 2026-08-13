<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\ProductImageMappingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class MediaAdminController extends Controller
{
    protected ProductImageMappingService $mappingService;

    public function __construct(ProductImageMappingService $mappingService)
    {
        $this->mappingService = $mappingService;
    }

    public function index(Request $request)
    {
        $candidateImages = $this->mappingService->getCandidateImages();
        $products = Product::all();

        // Index product assignments
        $assignedMap = [];
        foreach ($products as $p) {
            if (!empty($p->image_url)) {
                $cleanPath = str_replace('\\', '/', $p->image_url);
                $assignedMap[$cleanPath][] = $p;
            }
        }

        $mediaItems = [];
        $filter = $request->get('filter', 'all'); // all, assigned, unassigned, duplicate
        $search = strtolower(trim($request->get('search', '')));

        foreach ($candidateImages as $img) {
            $relPath = $img['relative_path'];
            $assignedProducts = $assignedMap[$relPath] ?? [];
            $assignedCount = count($assignedProducts);
            $isAssigned = $assignedCount > 0;
            $isDuplicate = $assignedCount > 1;

            if ($filter === 'assigned' && !$isAssigned) continue;
            if ($filter === 'unassigned' && $isAssigned) continue;
            if ($filter === 'duplicate' && !$isDuplicate) continue;

            if (!empty($search)) {
                $matchSearch = str_contains(strtolower($img['filename']), $search) ||
                    str_contains(strtolower($relPath), $search);
                
                if (!$matchSearch && !empty($assignedProducts)) {
                    foreach ($assignedProducts as $ap) {
                        if (str_contains(strtolower($ap->name), $search)) {
                            $matchSearch = true;
                            break;
                        }
                    }
                }

                if (!$matchSearch) continue;
            }

            $mediaItems[] = [
                'full_path' => $img['full_path'],
                'relative_path' => $relPath,
                'url' => $img['url'],
                'filename' => $img['filename'],
                'extension' => $img['extension'],
                'size_formatted' => round($img['size'] / 1024, 1) . ' KB',
                'assigned_count' => $assignedCount,
                'assigned_products' => $assignedProducts,
                'is_assigned' => $isAssigned,
                'is_duplicate' => $isDuplicate,
            ];
        }

        $msdcDir = public_path('assets/pdf/MSDC');
        $specDir = public_path('assets/pdf/Specification');

        $msdsPdfCount = count(file_exists($msdcDir) ? array_filter(scandir($msdcDir), fn($f) => is_file($msdcDir . '/' . $f) && strtolower(pathinfo($f, PATHINFO_EXTENSION)) === 'pdf') : []);
        $specPdfCount = count(file_exists($specDir) ? array_filter(scandir($specDir), fn($f) => is_file($specDir . '/' . $f) && strtolower(pathinfo($f, PATHINFO_EXTENSION)) === 'pdf') : []);
        $totalPdfCount = $msdsPdfCount + $specPdfCount;

        return view('admin.media.index', compact('mediaItems', 'products', 'filter', 'search', 'msdsPdfCount', 'specPdfCount', 'totalPdfCount'));
    }

    public function assign(Request $request)
    {
        $request->validate([
            'image_path' => 'required|string',
            'product_id' => 'required|exists:products,id'
        ]);

        $product = Product::findOrFail($request->product_id);
        $product->image_url = $request->image_path;
        $product->save();

        return back()->with('success', "Image successfully assigned to product '{$product->name}'!");
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'image_path' => 'required|string'
        ]);

        $rawPath = $request->image_path;
        $fileName = basename($rawPath);
        $relPath = 'storage/uploads/products/' . $fileName;

        // Check if assigned to any product
        $inUse = Product::where(function($query) use ($fileName, $relPath, $rawPath) {
            $query->where('image_url', $relPath)
                  ->orWhere('image_url', 'uploads/products/' . $fileName)
                  ->orWhere('image_url', 'LIKE', '%' . $fileName);
        })->get();

        if ($inUse->count() > 0) {
            $productNames = $inUse->pluck('name')->implode(', ');
            return back()->with('error', "Cannot delete image file '{$fileName}'. It is currently assigned to {$inUse->count()} product(s): {$productNames}");
        }

        $deleted = false;
        $storageRelPath = 'uploads/products/' . $fileName;

        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($storageRelPath)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($storageRelPath);
            $deleted = true;
        }

        $possiblePaths = [
            storage_path('app/public/uploads/products/' . $fileName),
            public_path('storage/uploads/products/' . $fileName),
            public_path('uploads/products/' . $fileName),
            public_path(ltrim($rawPath, '/')),
        ];

        foreach ($possiblePaths as $p) {
            if (File::exists($p) && is_file($p)) {
                @File::delete($p);
                $deleted = true;
            }
        }

        if ($deleted) {
            return back()->with('success', "Image file '{$fileName}' deleted successfully!");
        }

        return back()->with('info', "File '{$fileName}' was already removed or is no longer present on storage disk.");
    }
}
