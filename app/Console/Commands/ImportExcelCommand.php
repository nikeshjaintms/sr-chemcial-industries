<?php

namespace App\Console\Commands;

use App\Services\ExcelProductImportService;
use Illuminate\Console\Command;

class ImportExcelCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sr:import-excel {--file=products.xlsx : Path to the products Excel file} {--replace : Replace dataset mode (marks unlisted products inactive)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import products from Excel file into Laravel SR Chemicals database with complete category hierarchy mapping';

    /**
     * Execute the console command.
     */
    public function handle(ExcelProductImportService $importService): int
    {
        $file = $this->option('file') ?: 'products.xlsx';
        $replaceMode = (bool) $this->option('replace');

        if (!file_exists($file)) {
            $this->error("Error: Specified Excel file not found at path: {$file}");
            $this->line("Please provide a valid file path using: <fg=yellow>php artisan sr:import-excel --file=\"path/to/products.xlsx\"</>");
            return Command::FAILURE;
        }

        $this->info("==================================================");
        $this->info("SR CHEMICALS - EXCEL PRODUCT DATA MIGRATION");
        $this->info("==================================================");
        $this->line("File Path   : <fg=cyan>{$file}</>");
        $this->line("Dataset SyncMode : " . ($replaceMode ? "<fg=yellow>REPLACE (Mark unlisted inactive)</>" : "<fg=green>UPSERT (Preserve existing)</>"));
        $this->newLine();

        // 1. Validation Check
        $this->info("Phase 1: Validating Excel structure, hierarchy paths, and file assets...");
        $valReport = $importService->validateExcelFile($file);

        $this->table(
            ['Total Excel Rows', 'Valid Rows', 'Invalid Rows', 'New Products', 'Updated Products', 'Missing Images', 'Missing PDFs', 'Duplicates'],
            [[
                $valReport['total_rows'],
                $valReport['valid_rows'],
                $valReport['invalid_rows'],
                $valReport['new_products'],
                $valReport['updated_products'],
                $valReport['missing_images'],
                $valReport['missing_pdfs'],
                $valReport['duplicate_rows']
            ]]
        );

        if ($valReport['invalid_rows'] > 0 && $valReport['valid_rows'] === 0) {
            $this->error("Validation failed! No valid rows found in Excel file.");
            return Command::FAILURE;
        }

        // 2. Import Execution
        $this->info("Phase 2: Executing database sync & file asset copy...");
        try {
            $report = $importService->importExcelFile($file, $replaceMode);

            $this->newLine();
            $this->info("==================================================");
            $this->info("MIGRATION EXECUTION SUMMARY");
            $this->info("==================================================");
            $this->line("TOTAL EXCEL ROWS       : <fg=cyan>{$report['total_rows']}</>");
            $this->line("IMPORTED / CREATED     : <fg=green>{$report['created_count']}</>");
            $this->line("UPDATED PRODUCTS       : <fg=blue>{$report['updated_count']}</>");
            $this->line("SKIPPED ROWS           : <fg=yellow>{$report['skipped_count']}</>");
            $this->line("FAILED ROWS            : <fg=red>{$report['failed_count']}</>");
            $this->line("IMAGES FOUND & COPIED  : <fg=green>{$report['images_copied']}</>");
            $this->line("IMAGES MISSING         : <fg=yellow>{$report['images_missing']}</>");
            $this->line("PDFS FOUND & COPIED    : <fg=green>{$report['pdfs_copied']}</>");
            $this->line("PDFS MISSING           : <fg=yellow>{$report['pdfs_missing']}</>");
            if ($replaceMode) {
                $this->line("UNLISTED DEACTIVATED   : <fg=magenta>{$report['deactivated_count']}</>");
            }
            $this->info("==================================================");

            // Row-level details table
            $tableRows = [];
            foreach ($report['row_results'] as $res) {
                $tableRows[] = [
                    $res['row'],
                    $res['product_name'],
                    $res['category_path'],
                    $res['image_status'],
                    $res['pdf_status'],
                    $res['status'],
                    $res['message']
                ];
            }

            $this->table(
                ['Row', 'Product Name', 'Category Path', 'Image', 'PDF', 'Status', 'Details'],
                $tableRows
            );

            $this->info("Excel product migration completed successfully!");
            $this->line("Website Megamenu, Category Pages, Product Detail Pages, Live Search, and Chatbot are automatically synchronized.");
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Fatal Error during Excel Import: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
