<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\Product;

return new class extends Migration
{
    /**
     * Run the migration to move physical PDFs and update database URLs to public/assets/pdf/.
     */
    public function up(): void
    {
        $msdcDir = public_path('assets/pdf/MSDC');
        $specDir = public_path('assets/pdf/Specification');

        if (!file_exists($msdcDir)) {
            @mkdir($msdcDir, 0755, true);
        }
        if (!file_exists($specDir)) {
            @mkdir($specDir, 0755, true);
        }

        // 1. Move physical files from storage/app/public/uploads/msds -> public/assets/pdf/MSDC/
        $legacyMsdsDirs = [
            storage_path('app/public/uploads/msds'),
            public_path('storage/uploads/msds'),
            public_path('uploads/msds'),
        ];

        foreach ($legacyMsdsDirs as $dir) {
            if (file_exists($dir) && is_dir($dir)) {
                $files = glob($dir . '/*');
                foreach ($files as $file) {
                    if (is_file($file)) {
                        $dest = $msdcDir . '/' . basename($file);
                        if (!file_exists($dest)) {
                            @copy($file, $dest);
                        }
                    }
                }
            }
        }

        // 2. Move physical files from storage/app/public/uploads/specifications -> public/assets/pdf/Specification/
        $legacySpecDirs = [
            storage_path('app/public/uploads/specifications'),
            public_path('storage/uploads/specifications'),
            public_path('uploads/specifications'),
        ];

        foreach ($legacySpecDirs as $dir) {
            if (file_exists($dir) && is_dir($dir)) {
                $files = glob($dir . '/*');
                foreach ($files as $file) {
                    if (is_file($file)) {
                        $dest = $specDir . '/' . basename($file);
                        if (!file_exists($dest)) {
                            @copy($file, $dest);
                        }
                    }
                }
            }
        }

        // 3. Update database product records for msds_url and specification_url
        $products = Product::all();
        foreach ($products as $p) {
            $updated = false;

            $rawMsds = $p->getRawOriginal('msds_url');
            if (!empty($rawMsds) && $rawMsds !== '#') {
                $fn = basename(trim($rawMsds));
                $newMsds = 'assets/pdf/MSDC/' . $fn;
                if ($rawMsds !== $newMsds) {
                    $p->msds_url = $newMsds;
                    $updated = true;
                }
            }

            $rawSpec = $p->getRawOriginal('specification_url');
            if (!empty($rawSpec) && $rawSpec !== '#') {
                $fn = basename(trim($rawSpec));
                $newSpec = 'assets/pdf/Specification/' . $fn;
                if ($rawSpec !== $newSpec) {
                    $p->specification_url = $newSpec;
                    $updated = true;
                }
            }

            if ($updated) {
                $p->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op for safety
    }
};
