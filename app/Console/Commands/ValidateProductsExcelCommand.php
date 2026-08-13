<?php

namespace App\Console\Commands;

use App\Services\ExcelProductImportService;
use Illuminate\Console\Command;

class ValidateProductsExcelCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sr:validate-products-excel {file : Path to products.xlsx file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Validate products Excel file structure, category paths, and assets without making database changes';

    /**
     * Execute the console command.
     */
    public function handle(ExcelProductImportService $importService): int
    {
        $file = $this->argument('file');

        if (!file_exists($file)) {
            $this->error("Error: Specified Excel file not found at: {$file}");
            return Command::FAILURE;
        }

        $this->info("==================================================");
        $this->info("SR CHEMICALS - EXCEL PRODUCT VALIDATION");
        $this->info("==================================================");
        $this->line("File: <fg=cyan>{$file}</>");
        $this->newLine();

        $valReport = $importService->validateExcelFile($file);

        $this->info("SUMMARY:");
        $this->table(
            ['Total Rows', 'Valid Rows', 'Invalid Rows', 'New Products', 'Updated Products', 'Missing Images', 'Missing PDFs', 'Duplicate Rows'],
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

        $this->newLine();
        $this->info("DETAILED ROW VALIDATION:");

        $tableRows = [];
        foreach ($valReport['row_details'] as $row) {
            $tableRows[] = [
                $row['row'],
                $row['product_name'],
                $row['category_path'],
                $row['image_status'],
                $row['pdf_status'],
                $row['action_type'],
                $row['status'],
                $row['message']
            ];
        }

        $this->table(
            ['Row', 'Product Name', 'Category Path', 'Image', 'PDF', 'Action', 'Status', 'Message'],
            $tableRows
        );

        if ($valReport['invalid_rows'] > 0) {
            $this->warn("Validation finished with {$valReport['invalid_rows']} invalid rows.");
        } else {
            $this->info("Validation passed successfully! File is 100% ready for import.");
        }

        return Command::SUCCESS;
    }
}
