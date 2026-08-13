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

        return view('admin.media.index', compact('mediaItems', 'products', 'filter', 'search'));
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

        $relPath = $request->image_path;
        $fullPath = public_path($relPath);

        // Check if assigned to any product
        $inUse = Product::where('image_url', $relPath)->get();
        if ($inUse->count() > 0) {
            $productNames = $inUse->pluck('name')->implode(', ');
            return back()->with('error', "Cannot delete image. It is currently assigned to {$inUse->count()} product(s): {$productNames}");
        }

        if (File::exists($fullPath)) {
            File::delete($fullPath);
            return back()->with('success', "Image file '{$relPath}' deleted successfully!");
        }

        return back()->with('error', "File not found: {$relPath}");
    }
}
