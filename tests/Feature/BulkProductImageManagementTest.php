<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use App\Services\ProductImageMappingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BulkProductImageManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create([
            'email' => 'admin@srchemical.com',
        ]);

        Product::create([
            'name' => 'Nitric Acid',
            'slug' => 'gnfc-nitric-acid',
            'chemical_name' => 'Nitric Acid (HNO3)',
            'description' => 'High purity Nitric Acid.',
            'status' => true,
            'image_url' => 'assets/img/added/product/Caustic-Soda-Flakes-NaOH.jpg',
            'product_url' => '/products/gnfc-nitric-acid',
        ]);

        Product::create([
            'name' => 'Caustic Soda Flakes (NaOH)',
            'slug' => 'gacl-caustic-soda-flakes-naoh',
            'chemical_name' => 'Sodium Hydroxide (NaOH)',
            'description' => 'High purity Caustic Soda Flakes.',
            'status' => true,
            'image_url' => 'assets/img/added/product/Caustic-Soda-Flakes-NaOH.jpg',
            'product_url' => '/products/gacl-caustic-soda-flakes-naoh',
        ]);
    }

    /**
     * Test image audit and filename matching logic.
     */
    public function test_image_mapping_service_matching_logic(): void
    {
        $service = new ProductImageMappingService();
        $products = Product::all()->toArray();

        $match1 = $service->matchFilenameToProduct('nitric-acid.jpg', $products);
        $this->assertEquals('exact', $match1['match_type']);
        $this->assertEquals('Nitric Acid', $match1['product_name']);

        $match2 = $service->matchFilenameToProduct('caustic-soda-flakes-naoh.png', $products);
        $this->assertEquals('exact', $match2['match_type']);
        $this->assertEquals('Caustic Soda Flakes (NaOH)', $match2['product_name']);

        $match3 = $service->matchFilenameToProduct('unknown-chem-12345.jpg', $products);
        $this->assertEquals('none', $match3['match_type']);

        // Test Ambiguity Protection
        Product::create([
            'name' => 'Caustic Soda Lye',
            'slug' => 'gacl-caustic-soda-lye',
            'chemical_name' => 'Sodium Hydroxide Lye',
            'description' => 'Caustic soda lye solution',
            'status' => true,
            'image_url' => 'assets/img/added/product/caustic-potash-lye.jpg',
            'product_url' => '/products/gacl-caustic-soda-lye',
        ]);

        $updatedProducts = Product::all()->toArray();
        $ambiguousMatch = $service->matchFilenameToProduct('caustic-soda.jpg', $updatedProducts);
        $this->assertEquals('ambiguous', $ambiguousMatch['match_type']);
        $this->assertNotEmpty($ambiguousMatch['candidates']);
    }

    /**
     * Test artisan command php artisan products:map-images.
     */
    public function test_artisan_map_images_command(): void
    {
        $this->artisan('products:map-images', ['--apply' => true])
            ->assertExitCode(0);
    }

    /**
     * Test Bulk Image Upload Preview AJAX API.
     */
    public function test_preview_bulk_image_upload_endpoint(): void
    {
        Storage::fake('public');

        $file1 = UploadedFile::fake()->image('nitric-acid.jpg');
        $file2 = UploadedFile::fake()->image('unknown-substance.jpg');

        $response = $this->actingAs($this->adminUser)->postJson(route('admin.products.preview-bulk-images'), [
            'images' => [$file1, $file2]
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'count' => 2,
        ]);
        $response->assertJsonPath('items.0.match_type', 'exact');
        $response->assertJsonPath('items.0.product_name', 'Nitric Acid');
    }

    /**
     * Test Processing Bulk Image Upload and database update.
     */
    public function test_process_bulk_image_upload(): void
    {
        Storage::fake('public');

        $nitricProduct = Product::where('name', 'Nitric Acid')->first();
        $file = UploadedFile::fake()->image('nitric-acid.jpg');

        $response = $this->actingAs($this->adminUser)->post(route('admin.products.process-bulk-images'), [
            'images' => [$file],
            'product_ids' => [$nitricProduct->id]
        ]);

        $response->assertRedirect(route('admin.products.bulk-images'));
        $response->assertSessionHas('success');

        $nitricProduct->refresh();
        $this->assertStringContainsString('storage/uploads/products', $nitricProduct->image_url);
    }

    /**
     * Test Media Library Index and Image Assignment.
     */
    public function test_media_library_assign_image(): void
    {
        $nitricProduct = Product::where('name', 'Nitric Acid')->first();

        $response = $this->actingAs($this->adminUser)->get(route('admin.media.index'));
        $response->assertStatus(200);

        $assignResponse = $this->actingAs($this->adminUser)->post(route('admin.media.assign'), [
            'image_path' => 'assets/img/added/product/nitric-acid.jpg',
            'product_id' => $nitricProduct->id
        ]);

        $assignResponse->assertSessionHas('success');
        $nitricProduct->refresh();
        $this->assertEquals('assets/img/added/product/nitric-acid.jpg', $nitricProduct->image_url);
    }

    /**
     * Test Replace Duplicate Image endpoint.
     */
    public function test_replace_duplicate_image_endpoint(): void
    {
        $nitricProduct = Product::where('name', 'Nitric Acid')->first();

        $response = $this->actingAs($this->adminUser)->post(route('admin.products.replace-duplicate-image'), [
            'product_id' => $nitricProduct->id,
            'image_url' => 'assets/img/added/product/nitric-acid.jpg'
        ]);

        $response->assertSessionHas('success');
        $nitricProduct->refresh();
        $this->assertEquals('assets/img/added/product/nitric-acid.jpg', $nitricProduct->image_url);
    }

    /**
     * Test Chatbot receives the mapped product image URL.
     */
    public function test_chatbot_returns_mapped_product_image_url(): void
    {
        $nitricProduct = Product::where('name', 'Nitric Acid')->first();
        $nitricProduct->update(['image_url' => 'assets/img/added/product/nitric-acid.jpg']);

        $response = $this->postJson('/api/chatbot/chat', [
            'message' => 'Nitric Acid',
            'session_id' => 'img_test_session_1'
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('product.name', 'Nitric Acid');
        $response->assertJsonPath('product.image_url', asset('assets/img/added/product/nitric-acid.jpg'));
    }
}
