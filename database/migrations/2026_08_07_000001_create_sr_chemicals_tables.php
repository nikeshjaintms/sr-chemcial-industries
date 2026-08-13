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
        if (!Schema::hasTable('companies')) {
            Schema::create('companies', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('short_name');
                $table->string('tagline');
                $table->text('about');
                $table->text('mission');
                $table->text('vision');
                $table->string('address');
                $table->string('phone_primary');
                $table->string('phone_secondary');
                $table->string('email_primary');
                $table->string('email_secondary');
                $table->string('website_url');
                $table->string('logo_url');
                $table->text('services')->nullable();
                $table->text('highlights')->nullable();
                $table->text('logistics_info')->nullable();
                $table->text('compliance_info')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('categories')) {
            Schema::create('categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('type');
                $table->text('description')->nullable();
                $table->string('image_url')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('products')) {
            Schema::create('products', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('brand')->nullable();
                $table->string('chemical_name')->nullable();
                $table->string('cas_number')->nullable();
                $table->string('hsn_code')->nullable();
                $table->string('purity')->nullable();
                $table->string('packaging')->nullable();
                $table->text('description');
                $table->text('features')->nullable();
                $table->text('applications')->nullable();
                $table->text('specifications')->nullable();
                $table->text('storage_info')->nullable();
                $table->unsignedBigInteger('category_id')->nullable();
                $table->string('image_url')->nullable();
                $table->string('msds_url')->nullable();
                $table->string('specification_url')->nullable();
                $table->string('product_url');
                $table->boolean('is_featured')->default(false);
                $table->timestamps();

                $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
            });
        }

        if (!Schema::hasTable('blogs')) {
            Schema::create('blogs', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('slug')->unique();
                $table->text('summary');
                $table->text('content');
                $table->string('category')->nullable();
                $table->string('author')->default('SRCIL Editorial Team');
                $table->string('read_time')->default('5 min read');
                $table->string('published_at')->nullable();
                $table->string('image_url')->nullable();
                $table->string('url')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('faqs')) {
            Schema::create('faqs', function (Blueprint $table) {
                $table->id();
                $table->string('question');
                $table->text('answer');
                $table->string('category')->nullable();
                $table->text('keywords')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('certifications')) {
            Schema::create('certifications', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('issuer')->nullable();
                $table->text('description')->nullable();
                $table->string('document_url')->nullable();
                $table->string('icon')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('contact_details')) {
            Schema::create('contact_details', function (Blueprint $table) {
                $table->id();
                $table->string('office_name');
                $table->text('address');
                $table->string('city');
                $table->string('state');
                $table->string('country');
                $table->string('postal_code');
                $table->string('phone');
                $table->string('email');
                $table->string('whatsapp')->nullable();
                $table->string('working_hours')->nullable();
                $table->text('google_map_url')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('export_countries')) {
            Schema::create('export_countries', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code')->nullable();
                $table->string('region')->nullable();
                $table->string('flag_emoji')->nullable();
                $table->text('details')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('chat_histories')) {
            Schema::create('chat_histories', function (Blueprint $table) {
                $table->id();
                $table->string('session_id');
                $table->text('user_query');
                $table->text('bot_response');
                $table->string('matched_intent')->nullable();
                $table->unsignedBigInteger('context_product_id')->nullable();
                $table->text('metadata')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_histories');
        Schema::dropIfExists('export_countries');
        Schema::dropIfExists('contact_details');
        Schema::dropIfExists('certifications');
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('blogs');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('companies');
    }
};
