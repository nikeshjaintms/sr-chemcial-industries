<?php

namespace App\Console\Commands;

use App\Services\ExcelProductImportService;
use Illuminate\Console\Command;

class ImportProductsExcelCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sr:import-products-excel {file : Path to products.xlsx file} {--replace : Mark unlisted products as inactive}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import products from Excel file into Laravel SR Chemicals database';

    /**
     * Execute the console command.
     */
    public function handle(ExcelProductImportService $importService): int
    {
        $file = $this->argument('file');
        $replaceMode = (bool) $this->option('replace');

        if (!file_exists($file)) {
            $this->error("Error: Specified Excel file not found at: {$file}");
            return Command::FAILURE;
        }

        $this->info("==================================================");
        $this->info("SR CHEMICALS - EXCEL PRODUCT IMPORT");
        $this->info("==================================================");
        $this->line("File: <fg=cyan>{$file}</>");
        $this->line("Replace Mode: " . ($replaceMode ? "<fg=yellow>ENABLED</>" : "<fg=green>DISABLED</>"));
        $this->newLine();

        // 1. Validation Run
        $this->info("Step 1: Running validation check on Excel file...");
        $valReport = $importService->validateExcelFile($file);

        $this->table(
            ['Total Rows', 'Valid Rows', 'Invalid Rows', 'New Products', 'Updated Products', 'Missing Images', 'Missing PDFs'],
            [[
                $valReport['total_rows'],
                $valReport['valid_rows'],
                $valReport['invalid_rows'],
                $valReport['new_products'],
                $valReport['updated_products'],
                $valReport['missing_images'],
                $valReport['missing_pdfs']
            ]]
        );

        if ($valReport['invalid_rows'] > 0 && $valReport['valid_rows'] === 0) {
            $this->error("Validation failed! No valid rows found for import.");
            return Command::FAILURE;
        }

        // 2. Import Execution
        $this->info("Step 2: Executing database import & asset copying...");
        try {
            $report = $importService->importExcelFile($file, $replaceMode);

            $this->newLine();
            $this->info("==================================================");
            $this->info("FINAL IMPORT REPORT SUMMARY");
            $this->info("==================================================");
            $this->line("SOURCE EXCEL ROWS      : <fg=cyan>{$report['total_rows']}</>");
            $this->line("CREATED                : <fg=green>{$report['created_count']}</>");
            $this->line("UPDATED                : <fg=blue>{$report['updated_count']}</>");
            $this->line("SKIPPED                : <fg=yellow>{$report['skipped_count']}</>");
            $this->line("FAILED                 : <fg=red>{$report['failed_count']}</>");
            $this->line("IMAGES COPIED          : <fg=green>{$report['images_copied']}</>");
            $this->line("IMAGES MISSING         : <fg=yellow>{$report['images_missing']}</>");
            $this->line("PDFS COPIED            : <fg=green>{$report['pdfs_copied']}</>");
            $this->line("PDFS MISSING           : <fg=yellow>{$report['pdfs_missing']}</>");
            if ($replaceMode) {
                $this->line("DEACTIVATED UNLISTED   : <fg=magenta>{$report['deactivated_count']}</>");
            }
            $this->info("==================================================");

            // Print Row Results
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
                ['Row', 'Product', 'Category Path', 'Image', 'PDF', 'Status', 'Message'],
                $tableRows
            );

            $this->info("Excel import completed successfully! Megamenu, search, and chatbot updated.");
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Fatal Import Error: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
