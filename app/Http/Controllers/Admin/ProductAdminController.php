<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'categories']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q
                    ->where('name', 'LIKE', "%{$s}%")
                    ->orWhere('chemical_name', 'LIKE', "%{$s}%")
                    ->orWhere('cas_number', 'LIKE', "%{$s}%")
                    ->orWhere('hsn_code', 'LIKE', "%{$s}%");
            });
        }

        if ($request->filled('category_id')) {
            $cat = Category::find($request->category_id);
            if ($cat) {
                $catIds = collect([$cat->id]);
                $getChildrenIds = function ($c) use (&$getChildrenIds, &$catIds) {
                    foreach ($c->allChildren as $child) {
                        $catIds->push($child->id);
                        $getChildrenIds($child);
                    }
                };
                $getChildrenIds($cat);

                $query->where(function ($q) use ($catIds) {
                    $q->whereIn('category_id', $catIds)
                        ->orWhereHas('categories', function ($subQ) use ($catIds) {
                            $subQ->whereIn('categories.id', $catIds);
                        });
                });
            }
        }

        $products = $query->orderBy('sort_order', 'asc')->latest('id')->paginate(15);
        $categories = Category::with('parent')->orderBy('name', 'asc')->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::with('parent')->orderBy('name', 'asc')->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug',
            'category_path' => 'nullable|string|max:500',
            'category_id' => 'nullable|exists:categories,id',
            'brand' => 'nullable|string|max:255',
            'chemical_name' => 'nullable|string|max:255',
            'cas_number' => 'nullable|string|max:255',
            'hsn_code' => 'nullable|string|max:255',
            'purity' => 'nullable|string|max:255',
            'packaging' => 'nullable|string|max:255',
            'description' => 'required|string',
            'features' => 'nullable|array',
            'features.*' => 'nullable|string',
            'applications' => 'nullable|array',
            'applications.*' => 'nullable|string',
            'storage_info' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'status' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'specification_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'msds' => 'nullable|mimes:pdf,jpeg,png,jpg,webp|max:10240',
            'is_featured' => 'nullable|boolean',
        ]);

        // Dynamic Category Path creation or selection
        $categoryId = $validated['category_id'] ?? null;
        if (!empty($request->category_path)) {
            $cat = Category::findOrCreatePath($request->category_path, $validated['name']);
            $categoryId = $cat->id;
        }

        $slug = !empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['name']);
        $counter = 1;
        $originalSlug = $slug;
        while (Product::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        // Handle image upload
        $imageUrl = 'assets/img/added/product/Caustic-Soda-Flakes-NaOH.jpg';
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('uploads/products', 'public');
            $imageUrl = 'storage/' . $path;
        }

        // Handle Specification image upload
        $specImage = null;
        if ($request->hasFile('specification_image')) {
            $path = $request->file('specification_image')->store('uploads/specifications', 'public');
            $specImage = 'storage/' . $path;
        }

        // Handle MSDS / Certificate file upload
        $msdsUrl = '#';
        if ($request->hasFile('msds')) {
            $path = $request->file('msds')->store('uploads/msds', 'public');
            $msdsUrl = 'storage/' . $path;
        }

        // Clean features and applications
        $features = array_values(array_filter($request->features ?? [], fn($val) => !empty(trim($val))));
        $applications = array_values(array_filter($request->applications ?? [], fn($val) => !empty(trim($val))));

        $product = Product::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'brand' => $validated['brand'] ?? 'SRCIL Standard',
            'chemical_name' => $validated['chemical_name'] ?? $validated['name'],
            'cas_number' => $validated['cas_number'] ?? 'N/A',
            'hsn_code' => $validated['hsn_code'] ?? 'N/A',
            'purity' => $validated['purity'] ?? 'Technical Grade High Purity',
            'packaging' => $validated['packaging'] ?? 'Standard Packaging',
            'description' => $validated['description'],
            'features' => $features,
            'applications' => $applications,
            'specifications' => [],
            'storage_info' => $validated['storage_info'] ?? null,
            'category_id' => $categoryId,
            'sort_order' => $validated['sort_order'] ?? 0,
            'status' => $request->has('status') ? $request->boolean('status') : true,
            'image_url' => $imageUrl,
            'msds_url' => $msdsUrl,
            'specification_image' => $specImage,
            'specification_url' => $specImage ?: route('products.show', $slug),
            'product_url' => $slug . '.php',
            'is_featured' => $request->boolean('is_featured'),
        ]);

        if ($product->category_id) {
            $product->categories()->sync([$product->category_id]);
        }

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully! It is now instantly searchable in the Chatbot.');
    }

    public function edit(Product $product)
    {
        $categories = Category::with('parent')->orderBy('name', 'asc')->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug,' . $product->id,
            'category_path' => 'nullable|string|max:500',
            'category_id' => 'nullable|exists:categories,id',
            'brand' => 'nullable|string|max:255',
            'chemical_name' => 'nullable|string|max:255',
            'cas_number' => 'nullable|string|max:255',
            'hsn_code' => 'nullable|string|max:255',
            'purity' => 'nullable|string|max:255',
            'packaging' => 'nullable|string|max:255',
            'description' => 'required|string',
            'features' => 'nullable|array',
            'applications' => 'nullable|array',
            'storage_info' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'status' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'specification_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'msds' => 'nullable|mimes:pdf,jpeg,png,jpg,webp|max:10240',
            'is_featured' => 'nullable|boolean',
        ]);

        // Dynamic Category Path creation or selection
        $categoryId = $validated['category_id'] ?? $product->category_id;
        if (!empty($request->category_path)) {
            $cat = Category::findOrCreatePath($request->category_path, $validated['name']);
            $categoryId = $cat->id;
        }

        $imageUrl = $product->image_url;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('uploads/products', 'public');
            $imageUrl = 'storage/' . $path;
        }

        // Handle Specification image upload and removal
        $specImage = $product->specification_image;
        if ($request->boolean('remove_specification_image')) {
            if ($specImage && str_starts_with($specImage, 'storage/')) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete(str_replace('storage/', '', $specImage));
            }
            $specImage = null;
        } elseif ($request->hasFile('specification_image')) {
            $path = $request->file('specification_image')->store('uploads/specifications', 'public');
            $specImage = 'storage/' . $path;
        }

        // Handle MSDS / Certificate file upload and removal
        $msdsUrl = $product->msds_url;
        if ($request->boolean('remove_msds')) {
            if ($msdsUrl && str_starts_with($msdsUrl, 'storage/')) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete(str_replace('storage/', '', $msdsUrl));
            }
            $msdsUrl = '#';
        } elseif ($request->hasFile('msds')) {
            $path = $request->file('msds')->store('uploads/msds', 'public');
            $msdsUrl = 'storage/' . $path;
        }

        $features = array_values(array_filter($request->features ?? [], fn($val) => !empty(trim($val))));
        $applications = array_values(array_filter($request->applications ?? [], fn($val) => !empty(trim($val))));

        $product->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['slug']),
            'brand' => $validated['brand'] ?? $product->brand,
            'chemical_name' => $validated['chemical_name'] ?? $product->chemical_name,
            'cas_number' => $validated['cas_number'] ?? $product->cas_number,
            'hsn_code' => $validated['hsn_code'] ?? $product->hsn_code,
            'purity' => $validated['purity'] ?? $product->purity,
            'packaging' => $validated['packaging'] ?? $product->packaging,
            'description' => $validated['description'],
            'features' => $features,
            'applications' => $applications,
            'specifications' => $product->specifications ?? [],
            'storage_info' => $validated['storage_info'] ?? null,
            'category_id' => $categoryId,
            'sort_order' => $validated['sort_order'] ?? $product->sort_order,
            'status' => $request->has('status') ? $request->boolean('status') : true,
            'image_url' => $imageUrl,
            'msds_url' => $msdsUrl,
            'specification_image' => $specImage,
            'specification_url' => $specImage ?: $product->specification_url,
            'is_featured' => $request->boolean('is_featured'),
        ]);

        if ($product->category_id) {
            $product->categories()->sync([$product->category_id]);
        }

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully!');
    }

    public function destroy(Product $product)
    {
        $this->deleteProductAndAssets($product);
        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully!');
    }

    public function bulkUpdate(Request $request)
    {
        $ids = $request->input('ids', []);
        $action = $request->input('bulk_action');

        if (empty($ids)) {
            return back()->with('error', 'Please select at least one product.');
        }

        if ($action === 'delete') {
            return $this->bulkDestroy($request);
        }

        if ($action === 'activate') {
            Product::whereIn('id', $ids)->update(['status' => true]);
            return back()->with('success', count($ids) . ' products activated successfully!');
        }

        if ($action === 'deactivate') {
            Product::whereIn('id', $ids)->update(['status' => false]);
            return back()->with('success', count($ids) . ' products deactivated successfully!');
        }

        if ($action === 'set_brand' && $request->filled('bulk_brand')) {
            Product::whereIn('id', $ids)->update(['brand' => trim($request->bulk_brand)]);
            return back()->with('success', count($ids) . ' products updated with brand: ' . $request->bulk_brand);
        }

        if ($action === 'set_purity' && $request->filled('bulk_purity')) {
            Product::whereIn('id', $ids)->update(['purity' => trim($request->bulk_purity)]);
            return back()->with('success', count($ids) . ' products updated with purity: ' . $request->bulk_purity);
        }

        if ($action === 'set_packaging' && $request->filled('bulk_packaging')) {
            Product::whereIn('id', $ids)->update(['packaging' => trim($request->bulk_packaging)]);
            return back()->with('success', count($ids) . ' products updated with packaging: ' . $request->bulk_packaging);
        }

        return back()->with('error', 'Invalid bulk action selected.');
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return back()->with('error', 'No products selected for deletion.');
        }

        $products = Product::whereIn('id', $ids)->get();
        $count = 0;
        foreach ($products as $p) {
            $this->deleteProductAndAssets($p);
            $count++;
        }

        return redirect()->route('admin.products.index')->with('success', "{$count} products deleted successfully!");
    }

    private function deleteProductAndAssets(Product $product)
    {
        // Detach pivot relations
        $product->categories()->detach();

        $imgUrl = $product->image_url;
        $msdsUrl = $product->msds_url;

        $product->delete();

        // Delete image file if no remaining product uses it
        if ($imgUrl && str_contains($imgUrl, 'assets/img/added/product')) {
            $isUsed = Product::where('image_url', $imgUrl)->exists();
            if (!$isUsed) {
                $fullImgPath = public_path($imgUrl);
                if (file_exists($fullImgPath)) {
                    @unlink($fullImgPath);
                }
            }
        }

        // Delete PDF file if no remaining product uses it
        if ($msdsUrl && str_contains($msdsUrl, 'assets/pdf/MSDC')) {
            $isUsed = Product::where('msds_url', $msdsUrl)->exists();
            if (!$isUsed) {
                $fullPdfPath = public_path($msdsUrl);
                if (file_exists($fullPdfPath)) {
                    @unlink($fullPdfPath);
                }
            }
        }
    }

    private function cleanOrphanPublicAssets()
    {
        $imgDir = public_path('assets/img/added/product');
        if (file_exists($imgDir)) {
            $files = glob($imgDir . '/*');
            foreach ($files as $f) {
                if (is_file($f))
                    @unlink($f);
            }
        }

        $pdfDir = public_path('assets/pdf/MSDC');
        if (file_exists($pdfDir)) {
            $files = glob($pdfDir . '/*');
            foreach ($files as $f) {
                if (is_file($f))
                    @unlink($f);
            }
        }
    }

    public function toggleFeatured(Product $product)
    {
        $product->is_featured = !$product->is_featured;
        $product->save();

        return back()->with('success', 'Product featured status updated!');
    }

    public function showImportHierarchyForm()
    {
        $defaultTemplate = <<<TREE
            Products
            │
            ├── GACL Products
            │   ├── Acid Products
            │   │   ├── Nitric Acid
            │   │   ├── Acetic Acid
            │   │   ├── Formic Acid
            │   │   ├── Sulphuric Acid
            │   │   └── Phosphoric Acid
            │   ├── Chlor-Alkali Chemicals
            │   │   ├── Caustic Soda Lye (NaOH)
            │   │   ├── Caustic Soda Flakes (NaOH)
            │   │   ├── Caustic Soda Prills (NaOH)
            │   │   ├── Liquid Chlorine (Cl₂)
            │   │   ├── Hydrochloric Acid (HCl)
            │   │   └── Sodium Hypochlorite (NaOCl)
            │   ├── Hydrogen & Peroxide Chemicals
            │   │   ├── Hydrogen Peroxide
            │   │   └── Hydrazine Hydrate
            │   ├── Chloromethane Chemicals
            │   │   ├── Methylene Chloride (MDC)
            │   │   ├── Chloroform (CHCl₃)
            │   │   ├── Methyl Chloride (CH₃Cl)
            │   │   └── Carbon Tetrachloride (CCl₄)
            │   ├── Potassium Chemicals
            │   │   ├── Caustic Potash Lye
            │   │   ├── Caustic Potash Flakes
            │   │   └── Potassium Carbonate
            │   ├── Aluminium Based Chemicals
            │   │   ├── Anhydrous Aluminium Chloride
            │   │   └── Poly Aluminium Chloride (PAC)
            │   ├── Water Treatment Chemicals
            │   │   ├── Poly Aluminium Chloride (PAC) Powder
            │   │   ├── PAC Liquid
            │   │   └── Stable Bleaching Powder
            │   ├── Phosphate Chemicals
            │   │   ├── Phosphoric Acid (85%)
            │   │   └── Food Grade Phosphoric Acid
            │   └── Other Specialty Chemicals
            │       ├── Sodium Chlorate
            │       ├── Hydrazine Hydrate
            │       ├── Chlorinated Paraffin
            │       └── Benzyl Chloride
            │
            ├── Organic Products
            │   ├── Chlorobenzenes
            │   │   ├── Mono Chloro Benzene (MCB)
            │   │   ├── Para Di Chloro Benzene (PDCB)
            │   │   ├── Ortho Di Chloro Benzene (ODCB)
            │   │   └── 1,2,4 Tri Chloro Benzene (TCB)
            │   ├── Nitrobenzenes
            │   │   ├── Nitrobenzene (NB)
            │   │   ├── 2,4 Di Nitro Chloro Benzene
            │   │   ├── 2,5 Di Chloro Nitro Benzene
            │   │   ├── 3,4 Di Chloro Nitro Benzene
            │   │   ├── Para Nitro Chloro Benzene
            │   │   ├── Ortho Nitro Chloro Benzene
            │   │   └── Meta Nitro Chloro Benzene
            │   ├── Anilines & Toluidines
            │   │   ├── Para Chloro Aniline
            │   │   ├── Meta Chloro Aniline
            │   │   ├── Ortho Chloro Aniline
            │   │   ├── 2,5 Di Chloro Aniline
            │   │   ├── 3,4 Di Chloro Aniline
            │   │   ├── 2,4,5 Tri Chloro Aniline
            │   │   ├── Ortho Anisidine
            │   │   ├── Para Anisidine
            │   │   ├── Para Toluidine
            │   │   ├── Ortho Toluidine
            │   │   └── Meta Toluidine
            │   └── Calcium & Benzene Products
            │       ├── Calcium Chloride Prills/Powder
            │       ├── Calcium Chloride Brine
            │       └── Benzene
            │
            ├── DMCC Products
            │   ├── Boron Chemicals
            │   │   ├── Borax Decahydrate
            │   │   ├── Borax Pentahydrate
            │   │   ├── Boric Acid
            │   │   └── Boric Acid Special Quality Grade
            │   └── Sulfur Products
            │       ├── Sulfuric Acid (Commercial Grade)
            │       ├── Sulfuric Acid (Dilute)
            │       ├── Sulfuric Acid (Battery Grade)
            │       ├── Oleum 23%
            │       └── Oleum 65%
            │
            ├── GNFC Products
            │   ├── Organic Acids & Esters
            │   │   ├── Formic Acid
            │   │   ├── Acetic Acid
            │   │   ├── Ethyl Acetate
            │   │   └── Methyl Formate
            │   ├── Industrial Intermediates
            │   │   ├── Aniline
            │   │   ├── Methanol
            │   │   ├── OTD
            │   │   └── MTD
            │   └── Specialty Chemicals & Urea
            │       ├── Technical Grade Urea
            │       ├── Capsol Chemical
            │       ├── Calcium Carbonate
            │       ├── Nitrogen Liquid
            │       ├── Nitric Acid
            │       ├── Dilute Nitric Acid
            │       ├── Dilute Sulphuric Acid
            │       └── HCl (Spent)
            │
            └── Industrial Solvents & Commodities
                ├── Paint & Coating Industry Solvents
                │   ├── Ethyl Acetate
                │   ├── Butyl Acetate
                │   └── NC Thinner
                ├── Pharmaceutical & Chemical Solvents
                │   ├── Iso Propyl Alcohol (IPA)
                │   ├── Methanol
                │   ├── Methylene Chloride
                │   └── Chloroform
                ├── Cleaning & Degreasing Solvents
                │   ├── Methylene Chloride
                │   └── Chloroform
                └── Coal & Energy Products
                    ├── Bio-Coal
                    ├── Indonesian Coal
                    ├── South African Coal
                    └── Screen Coal
            TREE;

        return view('admin.products.import-hierarchy', compact('defaultTemplate'));
    }

    public function previewHierarchy(Request $request)
    {
        $request->validate([
            'hierarchy_text' => 'required|string'
        ]);

        $parser = new \App\Services\HierarchyParserService();
        $parsed = $parser->parseText($request->hierarchy_text);

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'count' => count($parsed),
                'nodes' => $parsed
            ]);
        }

        return back()->with('parsed_nodes', $parsed)->withInput();
    }

    public function importHierarchy(Request $request)
    {
        $request->validate([
            'hierarchy_text' => 'required|string'
        ]);

        $parser = new \App\Services\HierarchyParserService();
        $parsed = $parser->parseText($request->hierarchy_text);

        if (empty($parsed)) {
            return back()->with('error', 'No valid hierarchy nodes found to import. Please check format.');
        }

        $res = $parser->processImport($parsed);

        $msg = 'Bulk Hierarchy Import Completed Successfully! '
            . "Categories Created: {$res['categories_created']}, "
            . "Categories Reused: {$res['categories_reused']}, "
            . "Products Created: {$res['products_created']}, "
            . "Products Updated/Linked: {$res['products_updated']}. "
            . 'Website menu, search, and Chatbot have automatically synchronized.';

        return redirect()->route('admin.categories.index')->with('success', $msg);
    }

    public function importFromCorePhp()
    {
        try {
            $service = new \App\Services\ProductMigrationService();
            $report = $service->migrate();

            $totalSource = $report['source_products_count'] ?? $report['total_source'] ?? 0;
            $imported = $report['imported_products_count'] ?? $report['imported'] ?? 0;
            $mapped = $report['mapped_products_count'] ?? $report['updated'] ?? 0;
            $imagesCopied = $report['imported_images_count'] ?? $report['images_copied'] ?? 0;
            $pdfsCopied = $report['imported_pdfs_count'] ?? $report['pdfs_copied'] ?? 0;

            $catsCreated = $report['categories_created'] ?? 0;
            $catsUpdated = $report['categories_updated'] ?? 0;
            $totalCats = $catsCreated + $catsUpdated;

            $msg = 'Core PHP Product & Category Migration Completed Successfully! '
                . "Categories Synced: {$totalCats}, "
                . "Source Products: {$totalSource}, "
                . "Products Mapped: " . ($imported + $mapped) . ", "
                . "Images Copied: {$imagesCopied}, "
                . "PDFs Copied: {$pdfsCopied}.";

            return redirect()->route('admin.products.index')->with('success', $msg);
        } catch (\Exception $e) {
            return back()->with('error', 'Core PHP Migration Error: ' . $e->getMessage());
        }
    }

    public function showBulkAutoUpdateForm(Request $request)
    {
        $service = new \App\Services\BulkProductAutoUpdateService();
        $previewReport = $service->previewAllProductsUpdate();

        return view('admin.products.bulk-auto-update', compact('previewReport'));
    }

    public function processBulkAutoUpdate(Request $request)
    {
        try {
            $service = new \App\Services\BulkProductAutoUpdateService();
            $report = $service->executeAllProductsUpdate();

            $msg = "Bulk Product Data Update Completed Successfully! "
                . "Total Products Processed: {$report['total_products']}, "
                . "Matched: {$report['matched_count']}, "
                . "Updated: {$report['updated_count']}, "
                . "Failed: {$report['failed_count']}. "
                . "Website details and Chatbot have synchronized automatically!";

            return redirect()->route('admin.products.index')->with('success', $msg);
        } catch (\Exception $e) {
            return back()->with('error', 'Bulk Product Update Failed: ' . $e->getMessage());
        }
    }

    public function showImportExcelForm()
    {
        return view('admin.products.import-excel');
    }

    public function validateExcel(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:20480'
        ]);

        try {
            $file = $request->file('excel_file');
            $tempPath = $file->getRealPath();

            $service = new \App\Services\ExcelProductImportService();
            $valReport = $service->validateExcelFile($tempPath);

            return back()->with('validation_report', $valReport)->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'Excel Validation Error: ' . $e->getMessage())->withInput();
        }
    }

    public function processImportExcel(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:20480',
            'replace_mode' => 'nullable|boolean'
        ]);

        try {
            $file = $request->file('excel_file');
            $tempPath = $file->getRealPath();
            $replaceMode = $request->boolean('replace_mode');

            $service = new \App\Services\ExcelProductImportService();
            $importReport = $service->importExcelFile($tempPath, $replaceMode);

            return back()->with('import_report', $importReport);
        } catch (\Exception $e) {
            return back()->with('error', 'Excel Import Failed: ' . $e->getMessage())->withInput();
        }
    }

    public function downloadExcelTemplate()
    {
        try {
            $service = new \App\Services\ExcelTemplateService();
            $tempFile = tempnam(sys_get_temp_dir(), 'products_template_') . '.xlsx';
            $service->generateFile($tempFile);

            return response()->download($tempFile, 'products.xlsx', [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            ])->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return back()->with('error', 'Template Generation Error: ' . $e->getMessage());
        }
    }

    public function showBulkImageForm(Request $request, \App\Services\ProductImageMappingService $mappingService)
    {
        $audit = $mappingService->auditProducts();
        $products = Product::orderBy('name', 'asc')->get();
        $candidateImages = $mappingService->getCandidateImages();

        return view('admin.products.bulk-images', compact('audit', 'products', 'candidateImages'));
    }

    public function previewBulkImageUpload(Request $request, \App\Services\ProductImageMappingService $mappingService)
    {
        try {
            $files = $request->file('images', []);
            if (empty($files)) {
                return response()->json([
                    'success' => false,
                    'status' => 'error',
                    'message' => 'No image files uploaded in request.',
                    'count' => 0,
                    'items' => []
                ], 422);
            }

            $results = [];
            $allProducts = Product::with('category')->get()->toArray();
            $placeholderPattern = 'Caustic-Soda-Flakes-NaOH.jpg';

            foreach ($files as $file) {
                if (!$file->isValid()) {
                    $results[] = [
                        'original_name' => $file->getClientOriginalName(),
                        'match_type' => 'invalid_file',
                        'confidence' => 0,
                        'label' => '❌ Invalid File / Size Error',
                        'product_id' => null,
                        'product_name' => null,
                        'candidates' => [],
                        'has_existing_image' => false,
                    ];
                    continue;
                }

                $filename = $file->getClientOriginalName();
                $match = $mappingService->matchFilenameToProduct($filename, $allProducts);

                $hasExisting = false;
                if (!empty($match['product_id']) && !empty($match['product'])) {
                    $currentUrl = $match['product']['image_url'] ?? '';
                    $hasExisting = !empty($currentUrl) && !str_contains($currentUrl, $placeholderPattern) && file_exists(public_path($currentUrl));
                }

                $results[] = [
                    'original_name' => $filename,
                    'match_type' => $match['match_type'],
                    'confidence' => $match['confidence'],
                    'label' => $match['label'],
                    'product_id' => $match['product_id'],
                    'product_name' => $match['product_name'],
                    'candidates' => $match['candidates'] ?? [],
                    'has_existing_image' => $hasExisting,
                ];
            }

            return response()->json([
                'success' => true,
                'status' => 'success',
                'count' => count($results),
                'items' => $results
            ]);
        } catch (\Exception $e) {
            \Log::error('Preview Bulk Image Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'status' => 'error',
                'message' => 'Failed to preview image matches: ' . $e->getMessage()
            ], 500);
        }
    }

    public function processBulkImageUpload(Request $request, \App\Services\ProductImageMappingService $mappingService)
    {
        try {
            $request->validate([
                'images' => 'required|array',
                'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
                'mode' => 'nullable|string|in:skip,replace',
                'product_ids' => 'nullable|array',
                'replace_modes' => 'nullable|array',
            ], [
                'images.required' => 'Please select at least one product image file to upload.',
                'images.*.image' => 'Uploaded file must be a valid image format.',
                'images.*.mimes' => 'Only JPG, JPEG, PNG, WEBP, and GIF images are supported.',
                'images.*.max' => 'Image file size must not exceed 10MB per file.',
            ]);

            $files = $request->file('images', []);
            $globalMode = strtolower($request->input('mode', 'skip'));
            $productIds = $request->input('product_ids', []);
            $replaceModes = $request->input('replace_modes', []);

            $allProducts = Product::with('category')->get();
            $allProductsArray = $allProducts->toArray();
            $productsById = $allProducts->keyBy('id');
            $placeholderPattern = 'Caustic-Soda-Flakes-NaOH.jpg';

            $totalFiles = count($files);
            $assignedCount = 0;
            $replacedCount = 0;
            $skippedCount = 0;
            $deletedUnmatchedCount = 0;
            $details = [];
            $resultsTable = [];

            foreach ($files as $idx => $file) {
                $origName = $file ? $file->getClientOriginalName() : 'Unknown';

                if (!$file || !$file->isValid()) {
                    $deletedUnmatchedCount++;
                    $details[] = "🗑️ Deleted invalid or corrupted file upload: '{$origName}'";
                    $resultsTable[] = [
                        'filename' => $origName,
                        'matched_product' => '—',
                        'status' => 'INVALID FILE',
                        'badge_class' => 'bg-danger',
                        'message' => 'File upload was invalid or corrupted. Discarded.',
                    ];
                    continue;
                }

                $userMode = $replaceModes[$idx] ?? $globalMode;
                $targetProductId = $productIds[$idx] ?? null;

                $match = null;
                if (!empty($targetProductId) && $targetProductId !== 'auto' && isset($productsById[$targetProductId])) {
                    $product = $productsById[$targetProductId];
                    $match = [
                        'status' => 'MATCHED',
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'product' => $product->toArray(),
                        'match_type' => 'manual',
                        'message' => "Manually matched to '{$product->name}'.",
                    ];
                } else {
                    $match = $mappingService->matchFilenameToProduct($origName, $allProductsArray, $userMode);
                }

                $status = $match['status'];

                if ($status === 'MATCHED' && !empty($match['product_id']) && isset($productsById[$match['product_id']])) {
                    $product = $productsById[$match['product_id']];
                    $oldUrl = $product->image_url;
                    $hasExisting = !empty($oldUrl) && !str_contains($oldUrl, $placeholderPattern) && $oldUrl !== '#' && trim($oldUrl) !== '';

                    if ($hasExisting && $userMode === 'skip') {
                        $skippedCount++;
                        $details[] = "⏭️ Skipped '{$origName}' (Product '{$product->name}' already has an image)";
                        $resultsTable[] = [
                            'filename' => $origName,
                            'matched_product' => $product->name,
                            'status' => 'ALREADY EXISTS',
                            'badge_class' => 'bg-info text-dark',
                            'message' => "Product '{$product->name}' already has an image. File skipped.",
                        ];
                        continue;
                    }

                    // Store new image file on public disk
                    $path = $file->store('uploads/products', 'public');
                    $relUrl = 'storage/' . $path;

                    if ($hasExisting) {
                        $replacedCount++;

                        // Delete old image file if stored in public storage
                        if (str_starts_with($oldUrl, 'storage/')) {
                            $oldStoragePath = str_replace('storage/', '', $oldUrl);
                            if ($oldStoragePath !== $path && \Illuminate\Support\Facades\Storage::disk('public')->exists($oldStoragePath)) {
                                \Illuminate\Support\Facades\Storage::disk('public')->delete($oldStoragePath);
                            }
                        }
                    } else {
                        $assignedCount++;
                    }

                    $product->image_url = $relUrl;
                    $product->save();

                    $details[] = "✅ Automatically assigned '{$origName}' → Product '{$product->name}'";
                    $resultsTable[] = [
                        'filename' => $origName,
                        'matched_product' => $product->name,
                        'status' => 'SUCCESS',
                        'badge_class' => 'bg-success',
                        'message' => "Image stored & assigned to '{$product->name}'.",
                    ];
                } elseif ($status === 'ALREADY EXISTS') {
                    $skippedCount++;
                    $details[] = "⏭️ Skipped '{$origName}' (Product '{$match['product_name']}' already has an image)";
                    $resultsTable[] = [
                        'filename' => $origName,
                        'matched_product' => $match['product_name'],
                        'status' => 'ALREADY EXISTS',
                        'badge_class' => 'bg-info text-dark',
                        'message' => $match['message'],
                    ];
                } else {
                    // NOT FOUND or AMBIGUOUS -> DO NOT STORE FILE! File is automatically discarded/deleted.
                    $deletedUnmatchedCount++;
                    $details[] = "🗑️ Deleted unmatched file '{$origName}' ({$match['message']})";
                    $resultsTable[] = [
                        'filename' => $origName,
                        'matched_product' => '—',
                        'status' => $status,
                        'badge_class' => $status === 'AMBIGUOUS' ? 'bg-warning text-dark' : 'bg-secondary',
                        'message' => "{$match['message']} File discarded & deleted.",
                    ];
                }
            }

            // Post-process audit: get products without images
            $audit = $mappingService->auditProducts();

            $msg = "Bulk Image Auto-Assignment Completed! Total Files: {$totalFiles} | Assigned: {$assignedCount} | Replaced: {$replacedCount} | Skipped: {$skippedCount} | Deleted Unmatched: {$deletedUnmatchedCount}.";

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'status' => 'success',
                    'total_files' => $totalFiles,
                    'assigned_count' => $assignedCount,
                    'replaced_count' => $replacedCount,
                    'skipped_count' => $skippedCount,
                    'deleted_unmatched_count' => $deletedUnmatchedCount,
                    'without_images_count' => $audit['without_images_count'],
                    'details' => $details,
                    'results_table' => $resultsTable,
                    'message' => $msg
                ]);
            }

            return redirect()
                ->route('admin.products.bulk-images')
                ->with('success', $msg)
                ->with('upload_details', $details)
                ->with('results_table', $resultsTable)
                ->with('audit', $audit);

        } catch (\Illuminate\Validation\ValidationException $ve) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'status' => 'error',
                    'message' => 'Validation error: ' . implode(' ', array_merge(...array_values($ve->errors()))),
                    'errors' => $ve->errors()
                ], 422);
            }
            throw $ve;
        } catch (\Exception $e) {
            \Log::error('Process Bulk Image Upload Exception: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'status' => 'error',
                    'message' => 'Bulk image processing failed: ' . $e->getMessage()
                ], 500);
            }
            return back()->with('error', 'Bulk Image Upload Failed: ' . $e->getMessage());
        }
    }

    public function showDuplicateImages(\App\Services\ProductImageMappingService $mappingService)
    {
        $audit = $mappingService->auditProducts();
        $products = Product::orderBy('name', 'asc')->get();
        $candidateImages = $mappingService->getCandidateImages();

        return view('admin.products.duplicate-images', compact('audit', 'products', 'candidateImages'));
    }

    public function replaceDuplicateImage(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'image_url' => 'required|string'
        ]);

        $product = Product::findOrFail($request->product_id);
        $product->image_url = $request->image_url;
        $product->save();

        return back()->with('success', "Image updated for '{$product->name}'!");
    }
}
