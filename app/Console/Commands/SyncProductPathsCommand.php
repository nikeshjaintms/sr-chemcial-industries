<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ProductPathSyncService;

class SyncProductPathsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sr:sync-product-paths';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Replace/update category paths of existing Laravel products ONLY without touching product data or assets';

    /**
     * Execute the console command.
     */
    public function handle(ProductPathSyncService $syncService): int
    {
        $this->info("=================================================");
        $this->info(" SR Chemicals – Replace Product Category Paths ONLY");
        $this->info("=================================================");
        $this->info("Processing existing Laravel products...\n");

        try {
            $report = $syncService->syncPaths();

            $this->info("-------------------------------------------------");
            $this->info(" SUMMARY REPORT");
            $this->info("-------------------------------------------------");
            $this->line("Existing Products       : " . $report['existing_products']);
            $this->line("Matched Products        : " . $report['products_matched']);
            $this->line("Relationships Removed   : " . $report['relationships_removed']);
            $this->line("Relationships Added     : " . $report['relationships_added']);
            $this->line("Multi-Placement Products: " . $report['multi_placement_products']);
            $this->line("Unmatched Products      : " . $report['unmatched_products_count']);
            $this->line("Products Created        : " . $report['products_created']);
            $this->line("Products Deleted        : " . $report['products_deleted']);
            $this->line("Product Data Modified   : " . $report['product_data_modified']);
            $this->line("Assets Modified         : " . $report['assets_modified']);
            $this->info("-------------------------------------------------");

            if (!empty($report['unmatched_products'])) {
                $this->warn("\nUNMATCHED PRODUCTS:");
                foreach ($report['unmatched_products'] as $un) {
                    $this->warn(" - {$un['name']} (Slug: {$un['slug']}, Reason: {$un['reason']})");
                }
            } else {
                $this->info("\nSUCCESS: All existing products successfully mapped to authoritative category hierarchy!");
            }

            $this->info("\nWebsite mega-menu, Search Engine, and AI Chatbot successfully updated!");
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Path Sync Failed: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
