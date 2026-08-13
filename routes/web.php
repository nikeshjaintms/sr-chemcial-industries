<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Api\ChatbotController;
use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ProductAdminController;
use App\Http\Controllers\Admin\CategoryAdminController;
use App\Http\Controllers\Admin\MediaAdminController;
use App\Http\Controllers\Admin\BulkPdfUploadController;

/*
|--------------------------------------------------------------------------
| Public Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/certificate', [PageController::class, 'certificate'])->name('certificate');
Route::get('/clients', [PageController::class, 'clients'])->name('clients');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact', [ContactController::class, 'send'])->name('contact.send');
Route::get('/thank-you', [PageController::class, 'thankYou'])->name('thank-you');

// Products Routes
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/category/{slug}', [ProductController::class, 'category'])->name('products.category');
Route::get('/brand/{brand}', [ProductController::class, 'brand'])->name('products.brand');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/c/{path}', [ProductController::class, 'resolvePath'])->where('path', '.*')->name('products.path');

// Blog Routes
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

// Industry Guide Route
Route::get('/guide/{slug}', [PageController::class, 'guide'])->name('guide.show');

/*
|--------------------------------------------------------------------------
| Legacy Core PHP URL Fallback & Backward Compatibility Routes (.php URLs)
|--------------------------------------------------------------------------
*/

Route::get('/about.php', function() { return redirect()->route('about'); });
Route::get('/certificate.php', function() { return redirect()->route('certificate'); });
Route::get('/clients.php', function() { return redirect()->route('clients'); });
Route::get('/contact.php', function() { return redirect()->route('contact'); });
Route::get('/thank-you.php', function() { return redirect()->route('thank-you'); });
Route::get('/blog.php', function() { return redirect()->route('blog.index'); });
Route::get('/blog-details.php', function() { return redirect()->route('blog.index'); });
Route::get('/products.php', function() { return redirect()->route('products.index'); });
Route::get('/acid-products.php', function() { return redirect()->route('products.category', 'acid-products'); });
Route::get('/chlor-alkali-chemicals.php', function() { return redirect()->route('products.category', 'chlor-alkali-chemicals'); });
Route::get('/water-treatment-chemicals.php', function() { return redirect()->route('products.category', 'water-treatment-chemicals'); });
Route::get('/pharmaceutical-chemical-solvents.php', function() { return redirect()->route('products.category', 'pharmaceutical-chemical-solvents'); });
Route::get('/boron-chemicals.php', function() { return redirect()->route('products.category', 'boron-chemicals'); });
Route::get('/sulfur-products.php', function() { return redirect()->route('products.category', 'sulfur-products'); });

// Dynamic Legacy .php Route Handler
Route::get('/{slug}.php', function (string $slug) {
    $category = \App\Models\Category::where('slug', $slug)->first();
    if ($category) {
        return redirect()->route('products.category', $slug);
    }
    $product = \App\Models\Product::where('slug', $slug)->first();
    if ($product) {
        return redirect()->route('products.show', $slug);
    }
    return redirect()->route('products.index');
});

/*
|--------------------------------------------------------------------------
| API Endpoints for Chatbot & Live Product Search
|--------------------------------------------------------------------------
*/

Route::prefix('api')->group(function () {
    Route::post('/chatbot/chat', [ChatbotController::class, 'chat']);
    Route::get('/chatbot/history', [ChatbotController::class, 'history']);
    Route::post('/chatbot/clear', [ChatbotController::class, 'clear']);
    
    Route::get('/products/search', [ProductApiController::class, 'search']);
    Route::get('/products', [ProductApiController::class, 'index']);
    Route::get('/products/{slug}', [ProductApiController::class, 'show']);
});

/*
|--------------------------------------------------------------------------
| Named Login Route for Guest Middleware Redirects
|--------------------------------------------------------------------------
*/

Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');

/*
|--------------------------------------------------------------------------
| Product Admin Panel Routes (/admin)
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    Route::middleware('auth')->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
        
        // Excel Product Import & Template Routes
        Route::get('/products/import-excel', [ProductAdminController::class, 'showImportExcelForm'])->name('products.import-excel');
        Route::post('/products/validate-excel', [ProductAdminController::class, 'validateExcel'])->name('products.validate-excel');
        Route::post('/products/import-excel', [ProductAdminController::class, 'processImportExcel'])->name('products.process-import-excel');
        Route::get('/products/download-template', [ProductAdminController::class, 'downloadExcelTemplate'])->name('products.download-template');

        // Bulk Auto-Update All Products Route
        Route::get('/products/bulk-auto-update', [ProductAdminController::class, 'showBulkAutoUpdateForm'])->name('products.bulk-auto-update');
        Route::post('/products/process-bulk-auto-update', [ProductAdminController::class, 'processBulkAutoUpdate'])->name('products.process-bulk-auto-update');

        // Bulk Hierarchy Import & Core PHP Import
        Route::get('/products/import-hierarchy', [ProductAdminController::class, 'showImportHierarchyForm'])->name('products.import-hierarchy');
        Route::post('/products/preview-hierarchy', [ProductAdminController::class, 'previewHierarchy'])->name('products.preview-hierarchy');
        Route::post('/products/import-hierarchy', [ProductAdminController::class, 'importHierarchy'])->name('products.process-import-hierarchy');
        Route::post('/products/import-core-php', [ProductAdminController::class, 'importFromCorePhp'])->name('products.import-core-php');

        // Bulk Product Image Manager & Duplicate Image Management Routes
        Route::get('/products/bulk-images', [ProductAdminController::class, 'showBulkImageForm'])->name('products.bulk-images');
        Route::post('/products/preview-bulk-images', [ProductAdminController::class, 'previewBulkImageUpload'])->name('products.preview-bulk-images');
        Route::post('/products/process-bulk-images', [ProductAdminController::class, 'processBulkImageUpload'])->name('products.process-bulk-images');
        Route::post('/products/delete-all-images', [ProductAdminController::class, 'deleteAllProductImages'])->name('products.delete-all-images');
        Route::get('/products/duplicate-images', [ProductAdminController::class, 'showDuplicateImages'])->name('products.duplicate-images');
        Route::post('/products/replace-duplicate-image', [ProductAdminController::class, 'replaceDuplicateImage'])->name('products.replace-duplicate-image');

        // Bulk PDF Auto-Matching Routes
        Route::get('/products/bulk-pdf', [BulkPdfUploadController::class, 'index'])->name('products.bulk-pdf');
        Route::post('/products/preview-bulk-pdf', [BulkPdfUploadController::class, 'preview'])->name('products.preview-bulk-pdf');
        Route::post('/products/process-bulk-pdf', [BulkPdfUploadController::class, 'process'])->name('products.process-bulk-pdf');

        // Media Library Routes
        Route::get('/media', [MediaAdminController::class, 'index'])->name('media.index');
        Route::post('/media/assign', [MediaAdminController::class, 'assign'])->name('media.assign');
        Route::post('/media/delete', [MediaAdminController::class, 'destroy'])->name('media.destroy');

        // Products CRUD, Bulk Actions & Featured Toggle
        Route::post('/products/bulk-update', [ProductAdminController::class, 'bulkUpdate'])->name('products.bulk-update');
        Route::post('/products/bulk-delete', [ProductAdminController::class, 'bulkDestroy'])->name('products.bulk-delete');
        Route::post('/products/{product}/toggle-featured', [ProductAdminController::class, 'toggleFeatured'])->name('products.toggle-featured');
        Route::resource('products', ProductAdminController::class);
        
        // Categories CRUD
        Route::resource('categories', CategoryAdminController::class);
    });
});

/*
|--------------------------------------------------------------------------
| Catch-all Legacy Product .php Links (e.g. /formic-acid.php -> /products/formic-acid)
|--------------------------------------------------------------------------
*/

Route::get('/{slug}.php', function($slug) {
    return redirect()->route('products.show', $slug);
});
