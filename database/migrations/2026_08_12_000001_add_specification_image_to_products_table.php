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
        if (Schema::hasTable('products') && !Schema::hasColumn('products', 'specification_image')) {
            Schema::table('products', function (Blueprint $table) {
                $table->string('specification_image')->nullable()->after('specification_url');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('products') && Schema::hasColumn('products', 'specification_image')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('specification_image');
            });
        }
    }
};
