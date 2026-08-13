<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ProductMigrationService;

class ImportProductsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sr:import-products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate pure product data, images, and PDFs from Core PHP source (C:\xampp\htdocs\SR) into Laravel without category creation';

    /**
     * Execute the console command.
     */
    public function handle(ProductMigrationService $migrationService): int
    {
        $this->info("Starting Core PHP product migration process...\n");

        try {
            $report = $migrationService->migrate();

            $this->line("========================================");
            $this->line("SR CHEMICALS PRODUCT IMPORT REPORT");
            $this->line("========================================");
            $this->line("");
            $this->line("Source:");
            $this->line("C:\\xampp\\htdocs\\SR");
            $this->line("");
            $this->line("Total products found:");
            $this->line($report['total_source']);
            $this->line("");
            $this->line("Products imported:");
            $this->line($report['imported']);
            $this->line("");
            $this->line("Products updated:");
            $this->line($report['updated']);
            $this->line("");
            $this->line("Duplicates prevented:");
            $this->line($report['duplicates_prevented']);
            $this->line("");
            $this->line("Images found:");
            $this->line($report['images_found']);
            $this->line("");
            $this->line("Images copied:");
            $this->line($report['images_copied']);
            $this->line("");
            $this->line("Images missing:");
            $this->line($report['images_missing']);
            $this->line("");
            $this->line("PDFs found:");
            $this->line($report['pdfs_found']);
            $this->line("");
            $this->line("PDFs copied:");
            $this->line($report['pdfs_copied']);
            $this->line("");
            $this->line("PDFs missing:");
            $this->line($report['pdfs_missing']);
            $this->line("");
            $this->line("Products with complete details:");
            $this->line($report['complete_details']);
            $this->line("");
            $this->line("Products with missing details:");
            $this->line($report['missing_details']);
            $this->line("");
            $this->line("Failed products:");
            $this->line($report['failed']);
            $this->line("");
            $this->line("========================================");

            if (!empty($report['missing_asset_products'])) {
                $this->warn("\nMISSING ASSETS / DETAILS LOG:");
                foreach (array_slice($report['missing_asset_products'], 0, 20) as $msg) {
                    $this->line(" - {$msg}");
                }
                if (count($report['missing_asset_products']) > 20) {
                    $this->line(" ... and " . (count($report['missing_asset_products']) - 20) . " more.");
                }
            }

            $this->info("\nProduct import completed successfully!");
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Migration Failed: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
