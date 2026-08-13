<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'name')) {
                $table->index('name', 'products_name_index');
            }
            if (Schema::hasColumn('products', 'chemical_name')) {
                $table->index('chemical_name', 'products_chemical_name_index');
            }
            if (Schema::hasColumn('products', 'cas_number')) {
                $table->index('cas_number', 'products_cas_number_index');
            }
            if (Schema::hasColumn('products', 'hsn_code')) {
                $table->index('hsn_code', 'products_hsn_code_index');
            }
            if (Schema::hasColumn('products', 'status')) {
                $table->index('status', 'products_status_index');
            }
            if (Schema::hasColumn('products', 'is_featured')) {
                $table->index('is_featured', 'products_is_featured_index');
            }
            if (Schema::hasColumn('products', 'sort_order')) {
                $table->index('sort_order', 'products_sort_order_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('products_name_index');
            $table->dropIndex('products_chemical_name_index');
            $table->dropIndex('products_cas_number_index');
            $table->dropIndex('products_hsn_code_index');
            $table->dropIndex('products_status_index');
            $table->dropIndex('products_is_featured_index');
            $table->dropIndex('products_sort_order_index');
        });
    }
};
