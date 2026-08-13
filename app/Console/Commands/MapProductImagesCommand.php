<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ProductImageMappingService;
use App\Models\Product;

class MapProductImagesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:map-images {--apply : Apply exact and normalized image updates to database} {--preview : Preview mapping results without saving} {--all : Display full report for all products}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan candidate product images, match filenames against product names/slugs, and update product images';

    /**
     * Execute the console command.
     */
    public function handle(ProductImageMappingService $service)
    {
        $this->info("=========================================");
        $this->info("  SR CHEMICALS PRODUCT IMAGE MAPPING REPORT ");
        $this->info("=========================================");

        $audit = $service->auditProducts();

        $this->line("Total Products: " . $audit['total_products']);
        $this->line("Candidate Images Found: " . $audit['total_candidate_images']);
        $this->line("Products Using Placeholder Images: " . $audit['placeholder_products']);
        $this->line("Matched Products: " . $audit['matched_products']);
        $this->line("Unmatched Products: " . count($audit['unmatched_products']));
        $this->newLine();

        $headers = ['ID', 'Product Name', 'Image Filename', 'Match Status', 'Image URL', 'Missing?', 'Duplicate?'];
        $rows = [];

        // Track image URL frequencies for duplicate check
        $urlCounts = [];
        $products = Product::all();
        foreach ($products as $p) {
            if (!empty($p->image_url)) {
                $urlCounts[$p->image_url] = ($urlCounts[$p->image_url] ?? 0) + 1;
            }
        }

        foreach ($audit['mapped_items'] as $item) {
            $isPlaceholder = $item['is_placeholder'];
            $url = $item['current_image_url'];
            $isDup = isset($urlCounts[$url]) && $urlCounts[$url] > 1 && !$isPlaceholder;
            $missing = $isPlaceholder || !$item['current_image_exists'];

            $statusLabel = strtoupper($item['match_type']);
            if ($item['match_type'] === 'none') {
                $statusLabel = 'UNMATCHED';
            } elseif ($isPlaceholder) {
                $statusLabel = 'PLACEHOLDER';
            } elseif ($isDup) {
                $statusLabel = 'DUPLICATE_WARNING';
            }

            $rows[] = [
                $item['product_id'],
                $item['product_name'],
                $isPlaceholder ? '[NONE/PLACEHOLDER]' : basename($url),
                $statusLabel,
                $url ? asset($url) : 'N/A',
                $missing ? 'YES' : 'NO',
                $isDup ? 'YES' : 'NO'
            ];
        }

        $limit = $this->option('all') ? count($rows) : 35;
        $this->table($headers, array_slice($rows, 0, $limit));

        if (count($rows) > $limit) {
            $this->info("... showing first {$limit} of " . count($rows) . " items. Pass --all to view all products.");
        }

        if ($this->option('apply')) {
            $this->info("\nApplying image mapping to database...");
            $res = $service->applyAutoMapping(true);
            $this->info("✓ Successfully updated {$res['updated']} product images!");
            $this->info("✓ {$res['already_correct']} products already had correct images.");
            $this->info("⚠ {$res['skipped']} products were skipped (no confident match found).");
        } else {
            $this->warn("\nRun with --apply flag to update database records e.g. php artisan products:map-images --apply");
        }

        return 0;
    }
}
